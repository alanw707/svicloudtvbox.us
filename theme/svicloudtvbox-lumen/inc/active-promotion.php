<?php
/**
 * Config-driven site promotion automation.
 *
 * To disable the current promo immediately, define SVIC_PROMOTION_ENABLED as false.
 * To launch the next promo, update the SVIC_PROMOTION_* constants below, add or
 * reuse matching translation copy, then bump SVIC_PROMOTION_SYNC_MARK.
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('SVIC_PROMOTION_ENABLED')) {
    define('SVIC_PROMOTION_ENABLED', true);
}

if (!defined('SVIC_PROMOTION_KEY')) {
    define('SVIC_PROMOTION_KEY', 'july_4');
}

if (!defined('SVIC_PROMOTION_CODE')) {
    define('SVIC_PROMOTION_CODE', 'JULY4');
}

if (!defined('SVIC_PROMOTION_START')) {
    define('SVIC_PROMOTION_START', '2026-07-04 00:00:00');
}

if (!defined('SVIC_PROMOTION_END')) {
    define('SVIC_PROMOTION_END', '2026-07-04 23:59:59');
}

if (!defined('SVIC_PROMOTION_RATE')) {
    define('SVIC_PROMOTION_RATE', 5.0);
}

if (!defined('SVIC_PROMOTION_PRODUCT_IDS')) {
    define('SVIC_PROMOTION_PRODUCT_IDS', [12, 14, 840]);
}

if (!defined('SVIC_PROMOTION_DESCRIPTION')) {
    define('SVIC_PROMOTION_DESCRIPTION', 'July 4th 2026: 5% off all SVICLOUD products.');
}

if (!defined('SVIC_PROMOTION_SYNC_MARK')) {
    define('SVIC_PROMOTION_SYNC_MARK', '20260704-01');
}

if (!function_exists('svic_active_promotion_config')) {
    function svic_active_promotion_config(): array {
        $config = [
            'enabled'         => (bool) SVIC_PROMOTION_ENABLED,
            'key'             => (string) SVIC_PROMOTION_KEY,
            'translation_key' => (string) SVIC_PROMOTION_KEY,
            'code'            => (string) SVIC_PROMOTION_CODE,
            'start'           => (string) SVIC_PROMOTION_START,
            'end'             => (string) SVIC_PROMOTION_END,
            'rate'            => (float) SVIC_PROMOTION_RATE,
            'description'     => (string) SVIC_PROMOTION_DESCRIPTION,
            'product_ids'     => SVIC_PROMOTION_PRODUCT_IDS,
            'sync_mark'       => (string) SVIC_PROMOTION_SYNC_MARK,
        ];

        return apply_filters('svic_active_promotion_config', $config);
    }
}

if (!function_exists('svic_promotion_config_value')) {
    function svic_promotion_config_value(string $key, $default = null) {
        $config = svic_active_promotion_config();
        return array_key_exists($key, $config) ? $config[$key] : $default;
    }
}

if (!function_exists('svic_promotion_code')) {
    function svic_promotion_code(): string {
        return (string) svic_promotion_config_value('code', '');
    }
}

if (!function_exists('svic_promotion_products')) {
    function svic_promotion_products(): array {
        return [
            'all' => [
                'model' => 'SVICLOUD',
                'rate'  => (float) svic_promotion_config_value('rate', 0.0),
            ],
        ];
    }
}

if (!function_exists('svic_promotion_timezone')) {
    function svic_promotion_timezone(): DateTimeZone {
        if (function_exists('wp_timezone')) {
            return wp_timezone();
        }

        $timezone_string = get_option('timezone_string');
        if (is_string($timezone_string) && $timezone_string !== '') {
            return new DateTimeZone($timezone_string);
        }

        return new DateTimeZone('UTC');
    }
}

if (!function_exists('svic_promotion_window')) {
    function svic_promotion_window(): array {
        $timezone = svic_promotion_timezone();

        return [
            'start' => new DateTimeImmutable((string) svic_promotion_config_value('start', 'now'), $timezone),
            'end'   => new DateTimeImmutable((string) svic_promotion_config_value('end', 'now'), $timezone),
        ];
    }
}

if (!function_exists('svic_is_promotion_active')) {
    function svic_is_promotion_active(?DateTimeImmutable $now = null): bool {
        if (!(bool) svic_promotion_config_value('enabled', false)) {
            return false;
        }

        $window = svic_promotion_window();
        $now    = $now ?: new DateTimeImmutable('now', svic_promotion_timezone());

        return $now >= $window['start'] && $now <= $window['end'];
    }
}

if (!function_exists('svic_is_promotion_preview')) {
    function svic_is_promotion_preview(): bool {
        if (!is_user_logged_in() || !current_user_can('manage_options')) {
            return false;
        }

        $preview = isset($_GET['svic_preview_promo']) ? sanitize_text_field(wp_unslash($_GET['svic_preview_promo'])) : '';
        $preview = strtolower($preview);

        return $preview !== ''
            && in_array($preview, [
                strtolower(svic_promotion_code()),
                strtolower((string) svic_promotion_config_value('key', '')),
            ], true);
    }
}

if (!function_exists('svic_is_promotion_visible')) {
    function svic_is_promotion_visible(): bool {
        return svic_is_promotion_active() || svic_is_promotion_preview();
    }
}

if (!function_exists('svic_promotion_product_ids')) {
    function svic_promotion_product_ids(): array {
        $product_ids = svic_promotion_config_value('product_ids', []);
        if (!is_array($product_ids)) {
            return [];
        }

        return array_values(array_filter(array_map('absint', $product_ids)));
    }
}

if (!function_exists('svic_promotion_rate_for_product')) {
    function svic_promotion_rate_for_product($product): float {
        if (!$product instanceof WC_Product) {
            return 0.0;
        }

        $eligible_ids = svic_promotion_product_ids();
        if ($eligible_ids !== []) {
            $product_id = (int) $product->get_id();
            $parent_id  = (int) $product->get_parent_id();

            if (!in_array($product_id, $eligible_ids, true) && !in_array($parent_id, $eligible_ids, true)) {
                return 0.0;
            }
        }

        return max(0.0, (float) svic_promotion_config_value('rate', 0.0));
    }
}

if (!function_exists('svic_promotion_cart_has_eligible_item')) {
    function svic_promotion_cart_has_eligible_item(): bool {
        if (!function_exists('WC') || !WC()->cart) {
            return false;
        }

        foreach (WC()->cart->get_cart() as $cart_item) {
            $product = $cart_item['data'] ?? null;
            if (svic_promotion_rate_for_product($product) > 0) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('svic_sync_active_promotion_coupon')) {
    function svic_sync_active_promotion_coupon(): void {
        if (!class_exists('WC_Coupon') || !function_exists('wc_get_coupon_id_by_code') || wp_installing()) {
            return;
        }

        $code = svic_promotion_code();
        if ($code === '') {
            return;
        }

        $coupon_id = wc_get_coupon_id_by_code($code);
        $mark      = get_option('svic_active_promotion_coupon_sync_mark');
        $sync_mark = (string) svic_promotion_config_value('sync_mark', '');

        if ($mark === $sync_mark && $coupon_id) {
            return;
        }

        $coupon = $coupon_id ? new WC_Coupon($coupon_id) : new WC_Coupon();
        $window = svic_promotion_window();

        $coupon->set_code($code);
        $coupon->set_description((string) svic_promotion_config_value('description', 'SVICLOUD promotion.'));
        $coupon->set_discount_type('percent');
        $coupon->set_amount((float) svic_promotion_config_value('rate', 0.0));
        $coupon->set_individual_use(true);
        $coupon->set_usage_limit(0);
        $coupon->set_usage_limit_per_user(1);
        $coupon->set_minimum_amount('');
        $coupon->set_product_ids(svic_promotion_product_ids());
        $coupon->set_exclude_sale_items(false);
        $coupon->set_free_shipping(false);

        if (class_exists('WC_DateTime')) {
            $coupon->set_date_expires(new WC_DateTime($window['end']->format('Y-m-d H:i:s'), svic_promotion_timezone()));
        }

        $coupon->save();
        update_option('svic_active_promotion_coupon_sync_mark', $sync_mark, false);
    }
}

add_action('init', 'svic_sync_active_promotion_coupon', 40);

add_filter('woocommerce_coupon_is_valid', function ($valid, $coupon) {
    if (!$coupon instanceof WC_Coupon || strtolower((string) $coupon->get_code()) !== strtolower(svic_promotion_code())) {
        return $valid;
    }

    if (!svic_is_promotion_active()) {
        throw new Exception(svic_promotion_translate('coupon_not_active'));
    }

    if (!svic_promotion_cart_has_eligible_item()) {
        throw new Exception(svic_promotion_translate('coupon_not_eligible'));
    }

    return $valid;
}, 10, 2);

add_filter('woocommerce_coupon_is_valid_for_product', function ($valid, $product, $coupon, $values) {
    if (!$coupon instanceof WC_Coupon || strtolower((string) $coupon->get_code()) !== strtolower(svic_promotion_code())) {
        return $valid;
    }

    return svic_promotion_rate_for_product($product) > 0;
}, 10, 4);

add_filter('woocommerce_coupon_get_discount_amount', function ($discount, $discounting_amount, $cart_item, $single, $coupon) {
    if (!$coupon instanceof WC_Coupon || strtolower((string) $coupon->get_code()) !== strtolower(svic_promotion_code())) {
        return $discount;
    }

    $product = is_array($cart_item) ? ($cart_item['data'] ?? null) : null;
    $rate    = svic_promotion_rate_for_product($product);
    if ($rate <= 0) {
        return 0;
    }

    $amount = (float) $discounting_amount * ($rate / 100);
    if (function_exists('wc_round_discount') && function_exists('wc_get_rounding_precision')) {
        return wc_round_discount($amount, wc_get_rounding_precision());
    }

    return round($amount, 2);
}, 10, 5);

if (!function_exists('svic_promotion_translate')) {
    function svic_promotion_translate(string $leaf, array $replacements = []): string {
        $translation_key = (string) svic_promotion_config_value('translation_key', svic_promotion_config_value('key', ''));
        return svic_translate('promotion.' . $translation_key . '.' . $leaf, $replacements);
    }
}

if (!function_exists('svic_promotion_translate_html')) {
    function svic_promotion_translate_html(string $leaf, array $replacements = []): string {
        return esc_html(svic_promotion_translate($leaf, $replacements));
    }
}

if (!function_exists('svic_promotion_translate_attr')) {
    function svic_promotion_translate_attr(string $leaf, array $replacements = []): string {
        return esc_attr(svic_promotion_translate($leaf, $replacements));
    }
}

if (!function_exists('svic_render_promotion_bar')) {
    function svic_render_promotion_bar(): void {
        if (!svic_is_promotion_visible()) {
            return;
        }

        $code      = svic_promotion_code();
        $promo_key = sanitize_html_class((string) svic_promotion_config_value('key', 'active'));
        $shopUrl   = function_exists('svic_url_with_lang') ? svic_url_with_lang(home_url('/shop/')) : home_url('/shop/');
        ?>
        <div class="svic-promo-bar svic-promo-bar--<?php echo esc_attr($promo_key); ?>" role="region" aria-label="<?php echo svic_promotion_translate_attr('aria_label'); ?>">
            <div class="svic-promo-bar__inner">
                <span class="svic-promo-bar__eyebrow"><?php echo svic_promotion_translate_html('eyebrow'); ?></span>
                <span class="svic-promo-bar__message">
                    <span class="svic-promo-bar__offer"><?php echo svic_promotion_translate_html('offer_10p'); ?></span>
                    <span class="svic-promo-bar__code"><?php echo svic_promotion_translate_html('code_label', ['code' => $code]); ?></span>
                </span>
                <a class="svic-promo-bar__cta" href="<?php echo esc_url($shopUrl); ?>"><?php echo svic_promotion_translate_html('cta'); ?></a>
            </div>
        </div>
        <?php
    }
}

if (!function_exists('svic_fathers_day_promotion_code')) {
    function svic_fathers_day_promotion_code(): string {
        return svic_promotion_code();
    }
}

if (!function_exists('svic_fathers_day_promotion_products')) {
    function svic_fathers_day_promotion_products(): array {
        return svic_promotion_products();
    }
}

if (!function_exists('svic_fathers_day_promotion_timezone')) {
    function svic_fathers_day_promotion_timezone(): DateTimeZone {
        return svic_promotion_timezone();
    }
}

if (!function_exists('svic_fathers_day_promotion_window')) {
    function svic_fathers_day_promotion_window(): array {
        return svic_promotion_window();
    }
}

if (!function_exists('svic_is_fathers_day_promotion_active')) {
    function svic_is_fathers_day_promotion_active(?DateTimeImmutable $now = null): bool {
        return svic_is_promotion_active($now);
    }
}

if (!function_exists('svic_is_fathers_day_promotion_preview')) {
    function svic_is_fathers_day_promotion_preview(): bool {
        return svic_is_promotion_preview();
    }
}

if (!function_exists('svic_is_fathers_day_promotion_visible')) {
    function svic_is_fathers_day_promotion_visible(): bool {
        return svic_is_promotion_visible();
    }
}

if (!function_exists('svic_fathers_day_promotion_product_ids')) {
    function svic_fathers_day_promotion_product_ids(): array {
        return svic_promotion_product_ids();
    }
}

if (!function_exists('svic_fathers_day_promotion_rate_for_product')) {
    function svic_fathers_day_promotion_rate_for_product($product): float {
        return svic_promotion_rate_for_product($product);
    }
}

if (!function_exists('svic_fathers_day_promotion_cart_has_eligible_item')) {
    function svic_fathers_day_promotion_cart_has_eligible_item(): bool {
        return svic_promotion_cart_has_eligible_item();
    }
}

if (!function_exists('svic_sync_fathers_day_promotion_coupon')) {
    function svic_sync_fathers_day_promotion_coupon(): void {
        svic_sync_active_promotion_coupon();
    }
}

if (!function_exists('svic_render_fathers_day_promotion_bar')) {
    function svic_render_fathers_day_promotion_bar(): void {
        svic_render_promotion_bar();
    }
}
