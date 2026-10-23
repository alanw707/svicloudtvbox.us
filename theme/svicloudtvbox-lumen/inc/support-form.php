<?php
/** Purchase-gated support and bounded, credential-free spam protection. */
if (!defined('ABSPATH')) {
    exit;
}

function svic_support_key(string $scope, string $value): string
{
    return 'svic_support_' . $scope . '_' . hash_hmac('sha256', $value, wp_salt('nonce'));
}

/** Fixed windows: rejected attempts do not indefinitely extend the window. */
function svic_support_rate_allowed(string $key, int $limit, int $window): bool
{
    $now = time();
    $state = get_transient($key);
    if (!is_array($state) || ($state['until'] ?? 0) <= $now) {
        $state = ['count' => 0, 'until' => $now + $window];
    }
    if ($state['count'] >= $limit) {
        return false;
    }
    ++$state['count'];
    set_transient($key, $state, max(1, $state['until'] - $now));
    return true;
}

/** Returns a public status, never an order record or customer identity. */
function svic_process_support_request(array $post): string
{
    $limits = [
        'svic_support_nonce' => 64, 'support_kind' => 20, 'svic_locale' => 20,
        'support_name' => 200, 'support_email' => 254, 'support_phone' => 60,
        'support_order' => 40, 'support_device' => 30, 'support_issue' => 30,
        'support_message' => 12000, 'support_website' => 200,
    ];
    $values = [];
    foreach ($limits as $field => $limit) {
        if (isset($post[$field]) && !is_string($post[$field])) {
            return 'error';
        }
        $value = wp_unslash($post[$field] ?? '');
        if (strlen($value) > $limit) {
            return 'error';
        }
        $values[$field] = $value;
    }
    if (!wp_verify_nonce($values['svic_support_nonce'], 'svic_support_form')) {
        return 'error';
    }
    // A bot-filled trap is acknowledged without sending mail or disclosing detection.
    if (trim($values['support_website']) !== '') {
        return 'success';
    }
    $kind = $values['support_kind'];
    if (in_array($kind, ['order-help', 'other-seller'], true)) {
        return $kind;
    }
    if (!in_array($kind, ['aftersales', 'presales'], true)) {
        return 'error';
    }

    // Do not trust arbitrary X-Forwarded-For/CF headers. The trusted hosting
    // reverse proxy must restore REMOTE_ADDR before PHP (same as the old limit).
    $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        return 'error';
    }
    if (!svic_support_rate_allowed(svic_support_key('ip', $ip), 5, HOUR_IN_SECONDS)) {
        return 'limited';
    }
    $name = sanitize_text_field($values['support_name']);
    $email = strtolower(trim($values['support_email']));
    $message = trim(sanitize_textarea_field($values['support_message']));
    $issue = $values['support_issue'];
    $device = $values['support_device'];
    $phone = sanitize_text_field($values['support_phone']);
    $locale = sanitize_text_field($values['svic_locale']);
    $order_ref = trim($values['support_order']);
    $registry = SVIC_Translator::instance()->registry('en_US');
    $issues = $registry['support']['form']['issue_options'] ?? [];
    $devices = $registry['support']['form']['device_options'] ?? [];
    $presales = $registry['support']['guard']['presales_issues'] ?? [];
    $allowed_issues = $kind === 'presales' ? $presales : $issues;
    if ($name === '' || !is_email($email) || $message === '' ||
        !isset($allowed_issues[$issue]) || ($device !== '' && !isset($devices[$device]))) {
        return 'error';
    }

    $verified_order = null;
    if ($kind === 'aftersales') {
        // One generic failure for missing/wrong email, nonexistent or unpaid orders.
        // No guest login requirement; both guest and registered purchases work.
        if (!preg_match('/^(?:#|WC-)?([1-9][0-9]{0,11})$/i', $order_ref, $match) || !function_exists('wc_get_order')) {
            return 'verification';
        }
        $order = wc_get_order((int) $match[1]);
        if (!$order || $order->get_type() !== 'shop_order' ||
            (! $order->is_paid() && ! $order->get_date_paid()) ||
            !hash_equals(strtolower(trim($order->get_billing_email())), $email)) {
            return 'verification';
        }
        $verified_order = $order->get_id();
    }
    // Only validated requests consume the email budget, not failed order guesses.
    $email_key = svic_support_key('email', $email);
    $duplicate_key = svic_support_key('duplicate', implode('|', [
        $email, $kind, (string) $verified_order, $issue, $device,
        preg_replace('/\s+/u', ' ', $message),
    ]));
    if (get_transient($duplicate_key)) {
        return 'duplicate';
    }
    if (!svic_support_rate_allowed($email_key, 5, HOUR_IN_SECONDS)) {
        return 'limited';
    }
    // Serialize sends per email (atomic DB insert), including double clicks from
    // different IPs. Expiring lock recovers from an interrupted PHP request.
    $lock_key = svic_support_key('lock', $email);
    $lock_until = (int) get_option($lock_key, 0);
    if ($lock_until > 0 && $lock_until <= time()) {
        delete_option($lock_key);
    }
    if (!add_option($lock_key, time() + 120, '', false)) {
        return 'limited';
    }
    try {
        // Repeat after acquiring the lock: a concurrent send may just have finished.
        if (get_transient($duplicate_key)) {
            return 'duplicate';
        }
        $subject = sprintf('[SVICLOUD %s] %s', $kind === 'aftersales' ? 'Support' : 'Presales', $allowed_issues[$issue]);
        $lines = [
            'Support request submitted via svicloudtvbox.us',
            '--------------------------------------------------',
            'Request type: ' . ($kind === 'aftersales' ? 'Verified customer after-sales' : 'Presales (purchase not verified)'),
            'Name: ' . $name, 'Email: ' . $email, 'Phone: ' . ($phone ?: 'n/a'),
            'Verified order number: ' . ($verified_order ?? 'n/a'),
            'Device: ' . ($devices[$device] ?? 'n/a'),
            'Issue category: ' . $allowed_issues[$issue], 'Locale: ' . ($locale ?: 'n/a'),
            '--------------------------------------------------', 'Message:', $message,
        ];
        $sent = wp_mail(svic_support_form_recipient(), $subject, implode("\n", $lines), [
            'Content-Type: text/plain; charset=UTF-8',
            'From: SVICLOUD Concierge <' . svic_support_form_recipient() . '>',
            'Reply-To: ' . $email,
        ]);
        if ($sent) {
            set_transient($duplicate_key, 1, 10 * MINUTE_IN_SECONDS);
        }
        return $sent ? 'success' : 'error';
    } catch (Throwable $error) {
        // Do not expose transport exceptions or customer data to the browser.
        return 'error';
    } finally {
        delete_option($lock_key);
    }
}

function svic_handle_support_form(): void
{
    $locale = isset($_POST['svic_locale']) && is_string($_POST['svic_locale'])
        ? sanitize_text_field(wp_unslash($_POST['svic_locale'])) : '';
    $redirect = svic_url_with_lang(home_url('/support/'), svic_language_query_value($locale ?: null));
    $status = ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' ? svic_process_support_request($_POST) : 'error';
    wp_safe_redirect(add_query_arg('support_status', $status, $redirect) . '#support-response');
    exit;
}
add_action('admin_post_svic_support_form', 'svic_handle_support_form');
add_action('admin_post_nopriv_svic_support_form', 'svic_handle_support_form');
