#!/usr/bin/env php
<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    echo "CLI only.\n";
    exit(1);
}

if ($argc < 2 || !is_string($argv[1]) || $argv[1] === '') {
    fwrite(STDERR, "Usage: php scripts/aftership_production_audit.php /absolute/path/to/wp-load.php\n");
    exit(1);
}

$wp_load = $argv[1];

if (!is_file($wp_load)) {
    fwrite(STDERR, "wp-load.php not found: {$wp_load}\n");
    exit(1);
}

require_once $wp_load;

if (!function_exists('get_option')) {
    fwrite(STDERR, "WordPress bootstrap failed.\n");
    exit(1);
}

if (!class_exists('WooCommerce') || !function_exists('wc_get_order')) {
    fwrite(STDERR, "WooCommerce does not appear to be active.\n");
    exit(1);
}

require_once ABSPATH . 'wp-admin/includes/plugin.php';

/** @var wpdb $wpdb */
global $wpdb;

function audit_heading(string $label): void {
    echo "\n## {$label}\n";
}

function audit_line(string $label, string $value): void {
    echo "- {$label}: {$value}\n";
}

function audit_table_exists(wpdb $wpdb, string $table_name): bool {
    $result = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table_name));
    return is_string($result) && $result !== '';
}

function audit_redact_text(string $text): string {
    $text = preg_replace('/https?:\/\/\S+/i', '[redacted-url]', $text) ?? $text;
    $text = preg_replace('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', '[redacted-email]', $text) ?? $text;
    $text = preg_replace('/\b(?:\d[ -]?){10,}\b/', '[redacted-number]', $text) ?? $text;
    $text = preg_replace('/\b[A-Z]{2}\d{9}[A-Z]{2}\b/i', '[redacted-tracking]', $text) ?? $text;
    $text = preg_replace('/\b[A-Z0-9]{10,}\b/', '[redacted-token]', $text) ?? $text;
    $text = trim($text);

    if (mb_strlen($text) > 180) {
        $text = mb_substr($text, 0, 177) . '...';
    }

    return $text;
}

function audit_looks_like_datetime(string $value): bool {
    $value = trim($value);

    if ($value === '') {
        return false;
    }

    if (preg_match('/^\d{4}-\d{2}-\d{2}(?:[ T]\d{2}:\d{2}:\d{2})?$/', $value) === 1) {
        return true;
    }

    if (preg_match('/^\d{10}$/', $value) === 1) {
        $timestamp = (int) $value;
        return $timestamp >= 946684800 && $timestamp <= 2147483647;
    }

    return false;
}

function audit_format_value(string $meta_key, $raw_value): string {
    if (is_null($raw_value)) {
        return '(null)';
    }

    if (is_bool($raw_value)) {
        return $raw_value ? 'true' : 'false';
    }

    if (is_array($raw_value)) {
        return '[array count=' . count($raw_value) . ']';
    }

    if (is_object($raw_value)) {
        return '[object ' . get_class($raw_value) . ']';
    }

    $value = trim((string) $raw_value);

    if ($value === '') {
        return '(empty)';
    }

    if (is_serialized($value)) {
        return '[serialized len=' . strlen($value) . ']';
    }

    if (audit_looks_like_datetime($value)) {
        if (preg_match('/^\d{10}$/', $value) === 1) {
            return gmdate('Y-m-d H:i:s', (int) $value) . ' UTC (from unix timestamp)';
        }

        return $value;
    }

    if (filter_var($value, FILTER_VALIDATE_URL)) {
        $host = (string) wp_parse_url($value, PHP_URL_HOST);
        return $host !== '' ? '[url host=' . $host . ']' : '[url]';
    }

    $safe_value_pattern = '/^(?:aftership|after_ship|track123|trackingmore|shippo|pirateship|pirate_ship|usps|ups|fedex|dhl|ontrac|lasership|in_transit|delivered|out_for_delivery|exception|pending|unknown|shipment|tracking|fulfilled|complete|completed|yes|no|true|false|manual|api|webhook|test|live|sandbox)$/i';

    if (preg_match($safe_value_pattern, $value) === 1) {
        return strtolower($value);
    }

    if (preg_match('/^[A-Z]{2,6}$/', $value) === 1) {
        return strtoupper($value);
    }

    if (preg_match('/^[a-z0-9 _:\-]{1,40}$/i', $value) === 1 && preg_match('/\d{5,}/', $value) !== 1) {
        return $value;
    }

    if (stripos($meta_key, 'carrier') !== false || stripos($meta_key, 'provider') !== false || stripos($meta_key, 'status') !== false) {
        return audit_redact_text($value);
    }

    return '[redacted len=' . strlen($value) . ']';
}

function audit_get_active_candidate_plugins(): array {
    $plugins         = get_plugins();
    $active_plugins  = (array) get_option('active_plugins', array());
    $matches         = array();
    $keyword_pattern = '/(after|track|shipment|deliver|fulfill|ship)/i';

    foreach ($active_plugins as $plugin_file) {
        $plugin_data = $plugins[$plugin_file] ?? array();
        $haystack    = $plugin_file . ' ' . implode(' ', array_map('strval', $plugin_data));

        if (preg_match($keyword_pattern, $haystack) !== 1) {
            continue;
        }

        $matches[] = array(
            'file'    => $plugin_file,
            'name'    => (string) ($plugin_data['Name'] ?? $plugin_file),
            'version' => (string) ($plugin_data['Version'] ?? 'unknown'),
        );
    }

    return $matches;
}

function audit_get_candidate_tables(wpdb $wpdb): array {
    $all_tables = $wpdb->get_col('SHOW TABLES');
    $matches    = array();

    foreach ($all_tables as $table_name) {
        if (!is_string($table_name)) {
            continue;
        }

        if (preg_match('/(after|track|shipment|deliver|fulfill)/i', $table_name) !== 1) {
            continue;
        }

        $matches[] = $table_name;
    }

    return $matches;
}

function audit_get_candidate_options(wpdb $wpdb, int $limit = 80): array {
    $sql = $wpdb->prepare(
        "SELECT option_name FROM {$wpdb->options} WHERE option_name REGEXP %s ORDER BY option_name ASC LIMIT %d",
        'after|track|shipment|deliver|fulfill',
        $limit
    );

    $rows = $wpdb->get_col($sql);

    return array_values(array_filter(array_map('strval', is_array($rows) ? $rows : array())));
}

function audit_get_meta_key_counts(wpdb $wpdb, string $table_name, int $limit = 80): array {
    if (!audit_table_exists($wpdb, $table_name)) {
        return array();
    }

    $sql = $wpdb->prepare(
        "SELECT meta_key, COUNT(*) AS count FROM {$table_name} WHERE meta_key REGEXP %s GROUP BY meta_key ORDER BY count DESC, meta_key ASC LIMIT %d",
        'after|track|shipment|deliver|fulfill',
        $limit
    );

    $rows = $wpdb->get_results($sql, ARRAY_A);

    return is_array($rows) ? $rows : array();
}

function audit_get_candidate_order_ids(wpdb $wpdb, int $limit = 15): array {
    $candidate_ids = array();
    $patterns      = 'after|track|shipment|deliver|fulfill';
    $hpos_table    = $wpdb->prefix . 'wc_orders_meta';

    if (audit_table_exists($wpdb, $hpos_table)) {
        $sql  = $wpdb->prepare(
            "SELECT DISTINCT order_id FROM {$hpos_table} WHERE meta_key REGEXP %s ORDER BY order_id DESC LIMIT %d",
            $patterns,
            $limit * 3
        );
        $rows = $wpdb->get_col($sql);

        foreach ((array) $rows as $row) {
            $candidate_ids[] = (int) $row;
        }
    }

    if (audit_table_exists($wpdb, $wpdb->postmeta)) {
        $sql  = $wpdb->prepare(
            "SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key REGEXP %s ORDER BY post_id DESC LIMIT %d",
            $patterns,
            $limit * 3
        );
        $rows = $wpdb->get_col($sql);

        foreach ((array) $rows as $row) {
            $candidate_ids[] = (int) $row;
        }
    }

    $candidate_ids = array_values(array_unique(array_filter($candidate_ids)));
    rsort($candidate_ids, SORT_NUMERIC);

    if (!empty($candidate_ids)) {
        return array_slice($candidate_ids, 0, $limit);
    }

    $orders = wc_get_orders(
        array(
            'type'    => 'shop_order',
            'status'  => array_keys(wc_get_order_statuses()),
            'limit'   => $limit,
            'orderby' => 'date',
            'order'   => 'DESC',
            'return'  => 'ids',
        )
    );

    return array_map('intval', is_array($orders) ? $orders : array());
}

function audit_get_candidate_meta_rows(wpdb $wpdb, int $order_id): array {
    $rows        = array();
    $patterns    = 'after|track|shipment|deliver|fulfill';
    $hpos_table  = $wpdb->prefix . 'wc_orders_meta';

    if (audit_table_exists($wpdb, $hpos_table)) {
        $sql      = $wpdb->prepare(
            "SELECT meta_key, meta_value FROM {$hpos_table} WHERE order_id = %d AND meta_key REGEXP %s ORDER BY meta_key ASC",
            $order_id,
            $patterns
        );
        $hpos_rows = $wpdb->get_results($sql, ARRAY_A);
        if (is_array($hpos_rows)) {
            foreach ($hpos_rows as $row) {
                $rows[] = array(
                    'source'    => 'wc_orders_meta',
                    'meta_key'  => (string) ($row['meta_key'] ?? ''),
                    'meta_value'=> $row['meta_value'] ?? '',
                );
            }
        }
    }

    if (audit_table_exists($wpdb, $wpdb->postmeta)) {
        $sql       = $wpdb->prepare(
            "SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key REGEXP %s ORDER BY meta_key ASC",
            $order_id,
            $patterns
        );
        $post_rows = $wpdb->get_results($sql, ARRAY_A);
        if (is_array($post_rows)) {
            foreach ($post_rows as $row) {
                $rows[] = array(
                    'source'    => 'postmeta',
                    'meta_key'  => (string) ($row['meta_key'] ?? ''),
                    'meta_value'=> $row['meta_value'] ?? '',
                );
            }
        }
    }

    return $rows;
}

function audit_get_candidate_order_notes(wpdb $wpdb, int $order_id, int $limit = 8): array {
    $sql = $wpdb->prepare(
        "SELECT comment_date_gmt, comment_content FROM {$wpdb->comments} WHERE comment_post_ID = %d AND comment_type = 'order_note' AND comment_content REGEXP %s ORDER BY comment_ID DESC LIMIT %d",
        $order_id,
        'aftership|track|deliver|shipment|carrier|in transit|out for delivery',
        $limit
    );

    $rows = $wpdb->get_results($sql, ARRAY_A);

    return is_array($rows) ? $rows : array();
}

function audit_print_key_counts(string $label, array $rows): void {
    audit_heading($label);

    if (empty($rows)) {
        echo "- none\n";
        return;
    }

    foreach ($rows as $row) {
        $meta_key = (string) ($row['meta_key'] ?? '');
        $count    = (int) ($row['count'] ?? 0);
        echo '- ' . $meta_key . ': ' . $count . "\n";
    }
}

function audit_print_order_sample(wpdb $wpdb, int $order_id): void {
    $order = wc_get_order($order_id);

    if (!$order) {
        echo "\n### Order {$order_id}\n";
        echo "- missing order object\n";
        return;
    }

    $completed = $order->get_date_completed();
    $shipping_city_present  = $order->get_shipping_city() !== '' ? 'yes' : 'no';
    $shipping_state_present = $order->get_shipping_state() !== '' ? 'yes' : 'no';
    $billing_city_present   = $order->get_billing_city() !== '' ? 'yes' : 'no';
    $billing_state_present  = $order->get_billing_state() !== '' ? 'yes' : 'no';

    echo "\n### Order {$order_id}\n";
    audit_line('status', (string) $order->get_status());
    audit_line('created_gmt', (string) $order->get_date_created()->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s'));
    audit_line('completed_gmt', $completed ? $completed->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s') : '(none)');
    audit_line('shipping_city_present', $shipping_city_present);
    audit_line('shipping_state_present', $shipping_state_present);
    audit_line('billing_city_present', $billing_city_present);
    audit_line('billing_state_present', $billing_state_present);

    $meta_rows = audit_get_candidate_meta_rows($wpdb, $order_id);
    if (empty($meta_rows)) {
        audit_line('candidate_meta', 'none');
    } else {
        echo "- candidate_meta:\n";
        foreach ($meta_rows as $row) {
            $meta_key = (string) ($row['meta_key'] ?? '');
            $source   = (string) ($row['source'] ?? 'meta');
            $value    = audit_format_value($meta_key, $row['meta_value'] ?? '');
            echo "  - {$source} :: {$meta_key} = {$value}\n";
        }
    }

    $notes = audit_get_candidate_order_notes($wpdb, $order_id);
    if (empty($notes)) {
        audit_line('candidate_order_notes', 'none');
        return;
    }

    echo "- candidate_order_notes:\n";
    foreach ($notes as $note) {
        $date = (string) ($note['comment_date_gmt'] ?? '');
        $text = audit_redact_text((string) ($note['comment_content'] ?? ''));
        echo "  - {$date} UTC :: {$text}\n";
    }
}

audit_heading('Environment');
audit_line('site_url', (string) get_option('siteurl'));
audit_line('home_url', (string) get_option('home'));
audit_line('woocommerce_version', defined('WC_VERSION') ? WC_VERSION : 'unknown');
audit_line('hpos_enabled', get_option('woocommerce_custom_orders_table_enabled', 'no') === 'yes' ? 'yes' : 'no');
audit_line('php_version', PHP_VERSION);

$plugins = audit_get_active_candidate_plugins();
audit_heading('Active shipping / tracking candidate plugins');
if (empty($plugins)) {
    echo "- none matched after|track|shipment|deliver|fulfill|ship\n";
} else {
    foreach ($plugins as $plugin) {
        echo '- ' . $plugin['name'] . ' (' . $plugin['version'] . ') :: ' . $plugin['file'] . "\n";
    }
}

$tables = audit_get_candidate_tables($wpdb);
audit_heading('Candidate custom tables');
if (empty($tables)) {
    echo "- none\n";
} else {
    foreach ($tables as $table_name) {
        echo '- ' . $table_name . "\n";
    }
}

$options = audit_get_candidate_options($wpdb);
audit_heading('Candidate option names');
if (empty($options)) {
    echo "- none\n";
} else {
    foreach ($options as $option_name) {
        echo '- ' . $option_name . "\n";
    }
}

audit_print_key_counts('HPOS candidate meta keys', audit_get_meta_key_counts($wpdb, $wpdb->prefix . 'wc_orders_meta'));
audit_print_key_counts('Legacy postmeta candidate keys', audit_get_meta_key_counts($wpdb, $wpdb->postmeta));

$note_counts = array(
    'tracking'         => 0,
    'delivered'        => 0,
    'shipment'         => 0,
    'in transit'       => 0,
    'out for delivery' => 0,
    'aftership'        => 0,
);

audit_heading('Candidate order note counts');
foreach (array_keys($note_counts) as $needle) {
    $sql              = $wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_type = 'order_note' AND comment_content LIKE %s",
        '%' . $wpdb->esc_like($needle) . '%'
    );
    $note_counts[$needle] = (int) $wpdb->get_var($sql);
    echo '- ' . $needle . ': ' . $note_counts[$needle] . "\n";
}

$order_ids = audit_get_candidate_order_ids($wpdb, 15);
audit_heading('Sample candidate orders');
if (empty($order_ids)) {
    echo "- none\n";
} else {
    foreach ($order_ids as $order_id) {
        audit_print_order_sample($wpdb, $order_id);
    }
}

echo "\n## Audit complete\n";
echo "Review this output for: active AfterShip plugin, candidate tables/meta, delivered timestamps, provider/carrier fields, and recent orders with usable shipment data.\n";
