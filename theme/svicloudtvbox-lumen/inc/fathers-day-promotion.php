<?php
/**
 * Father's Day 2026 coupon promotion automation.
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('SVIC_FATHERS_DAY_PROMO_CODE')) {
    define('SVIC_FATHERS_DAY_PROMO_CODE', 'DAD2026');
}

if (!defined('SVIC_FATHERS_DAY_PROMO_START')) {
    define('SVIC_FATHERS_DAY_PROMO_START', '2026-06-19 00:00:00');
}

if (!defined('SVIC_FATHERS_DAY_PROMO_END')) {
    define('SVIC_FATHERS_DAY_PROMO_END', '2026-06-22 23:59:59');
}

if (!defined('SVIC_FATHERS_DAY_PROMO_SYNC_MARK')) {
    define('SVIC_FATHERS_DAY_PROMO_SYNC_MARK', '20260618-01');
}

if (!function_exists('svic_fathers_day_promotion_code')) {
    function svic_fathers_day_promotion_code(): string
    {
        return (string) SVIC_FATHERS_DAY_PROMO_CODE;
    }
}

if (!function_exists('svic_fathers_day_promotion_products')) {
    function svic_fathers_day_promotion_products(): array
    {
        return [
            'svicloud-10p-plus' => [
                'model' => 'SVICLOUD 10P+',
                'rate'  => 5.0,
            ],
            'svicloud-10s' => [
                'model' => 'SVICLOUD 10S',
                'rate'  => 10.0,
            ],
        ];
    }
}

if (!function_exists('svic_fathers_day_promotion_timezone')) {
    function svic_fathers_day_promotion_timezone(): DateTimeZone
    {
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

if (!function_exists('svic_fathers_day_promotion_window')) {
    function svic_fathers_day_promotion_window(): array
    {
        $timezone = svic_fathers_day_promotion_timezone();

        return [
            'start' => new DateTimeImmutable((string) SVIC_FATHERS_DAY_PROMO_START, $timezone),
            'end'   => new DateTimeImmutable((string) SVIC_FATHERS_DAY_PROMO_END, $timezone),
        ];
    }
}

if (!function_exists('svic_is_fathers_day_promotion_active')) {
    function svic_is_fathers_day_promotion_active(?DateTimeImmutable $now = null): bool
    {
        $window = svic_fathers_day_promotion_window();
        $now    = $now ?: new DateTimeImmutable('now', svic_fathers_day_promotion_timezone());

        return $now >= $window['start'] && $now <= $window['end'];
    }
}

if (!function_exists('svic_is_fathers_day_promotion_preview')) {
    function svic_is_fathers_day_promotion_preview(): bool
    {
        if (!is_user_logged_in() || !current_user_can('manage_options')) {
            return false;
        }

        $preview = isset($_GET['svic_preview_promo']) ? sanitize_text_field(wp_unslash($_GET['svic_preview_promo'])) : '';
        return strtolower($preview) === strtolower(svic_fathers_day_promotion_code());
    }
}

if (!function_exists('svic_is_fathers_day_promotion_visible')) {
    function svic_is_fathers_day_promotion_visible(): bool
    {
        return svic_is_fathers_day_promotion_active() || svic_is_fathers_day_promotion_preview();
    }
}

if (!function_exists('svic_fathers_day_promotion_product_ids')) {
    function svic_fathers_day_promotion_product_ids(): array
    {
        $ids = [];

        if (!class_exists('WooCommerce')) {
            return $ids;
        }

        foreach (array_keys(svic_fathers_day_promotion_products()) as $slug) {
            $post = get_page_by_path($slug, OBJECT, 'product');
            if ($post instanceof WP_Post) {
                $ids[$slug] = (int) $post->ID;
            }
        }

        return $ids;
    }
}

if (!function_exists('svic_fathers_day_promotion_rate_for_product')) {
    function svic_fathers_day_promotion_rate_for_product($product): float
    {
        if (!$product instanceof WC_Product) {
            return 0.0;
        }

        $product_ids = svic_fathers_day_promotion_product_ids();
        $product_id  = (int) $product->get_id();
        $parent_id   = method_exists($product, 'get_parent_id') ? (int) $product->get_parent_id() : 0;

        foreach (svic_fathers_day_promotion_products() as $slug => $config) {
            $eligible_id = (int) ($product_ids[$slug] ?? 0);
            if ($eligible_id > 0 && ($product_id === $eligible_id || $parent_id === $eligible_id)) {
                return (float) ($config['rate'] ?? 0.0);
            }
        }

        return 0.0;
    }
}

if (!function_exists('svic_fathers_day_promotion_cart_has_eligible_item')) {
    function svic_fathers_day_promotion_cart_has_eligible_item(): bool
    {
        if (!function_exists('WC') || !WC()->cart) {
            return false;
        }

        foreach (WC()->cart->get_cart() as $cart_item) {
            $product = $cart_item['data'] ?? null;
            if (svic_fathers_day_promotion_rate_for_product($product) > 0) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('svic_sync_fathers_day_promotion_coupon')) {
    function svic_sync_fathers_day_promotion_coupon(): void
    {
        if (!class_exists('WC_Coupon') || !function_exists('wc_get_coupon_id_by_code') || wp_installing()) {
            return;
        }

        $code      = svic_fathers_day_promotion_code();
        $coupon_id = wc_get_coupon_id_by_code($code);
        $mark      = get_option('svic_fathers_day_promotion_coupon_sync_mark');

        if ($mark === SVIC_FATHERS_DAY_PROMO_SYNC_MARK && $coupon_id) {
            return;
        }

        $coupon = $coupon_id ? new WC_Coupon($coupon_id) : new WC_Coupon();
        $window = svic_fathers_day_promotion_window();

        $coupon->set_code($code);
        $coupon->set_description('Father\'s Day 2026: 5% off SVICLOUD 10P+ and 10% off SVICLOUD 10S.');
        $coupon->set_discount_type('percent');
        $coupon->set_amount(10);
        $coupon->set_individual_use(true);
        $coupon->set_usage_limit(0);
        $coupon->set_usage_limit_per_user(1);
        $coupon->set_minimum_amount('');
        $coupon->set_product_ids(array_values(svic_fathers_day_promotion_product_ids()));
        $coupon->set_exclude_sale_items(false);
        $coupon->set_free_shipping(false);

        if (class_exists('WC_DateTime')) {
            $coupon->set_date_expires(new WC_DateTime($window['end']->format('Y-m-d H:i:s'), svic_fathers_day_promotion_timezone()));
        }

        $coupon->save();
        update_option('svic_fathers_day_promotion_coupon_sync_mark', SVIC_FATHERS_DAY_PROMO_SYNC_MARK, false);
    }
}

add_action('init', 'svic_sync_fathers_day_promotion_coupon', 40);

add_filter('woocommerce_coupon_is_valid', function ($valid, $coupon) {
    if (!$coupon instanceof WC_Coupon || strtolower((string) $coupon->get_code()) !== strtolower(svic_fathers_day_promotion_code())) {
        return $valid;
    }

    if (!svic_is_fathers_day_promotion_active()) {
        throw new Exception(svic_translate('promotion.fathers_day.coupon_not_active'));
    }

    if (!svic_fathers_day_promotion_cart_has_eligible_item()) {
        throw new Exception(svic_translate('promotion.fathers_day.coupon_not_eligible'));
    }

    return $valid;
}, 10, 2);

add_filter('woocommerce_coupon_is_valid_for_product', function ($valid, $product, $coupon, $values) {
    if (!$coupon instanceof WC_Coupon || strtolower((string) $coupon->get_code()) !== strtolower(svic_fathers_day_promotion_code())) {
        return $valid;
    }

    return svic_fathers_day_promotion_rate_for_product($product) > 0;
}, 10, 4);

add_filter('woocommerce_coupon_get_discount_amount', function ($discount, $discounting_amount, $cart_item, $single, $coupon) {
    if (!$coupon instanceof WC_Coupon || strtolower((string) $coupon->get_code()) !== strtolower(svic_fathers_day_promotion_code())) {
        return $discount;
    }

    $product = is_array($cart_item) ? ($cart_item['data'] ?? null) : null;
    $rate    = svic_fathers_day_promotion_rate_for_product($product);
    if ($rate <= 0) {
        return 0;
    }

    $amount = (float) $discounting_amount * ($rate / 100);
    if (function_exists('wc_round_discount') && function_exists('wc_get_rounding_precision')) {
        return wc_round_discount($amount, wc_get_rounding_precision());
    }

    return round($amount, 2);
}, 10, 5);

if (!function_exists('svic_render_fathers_day_promotion_bar')) {
    function svic_render_fathers_day_promotion_bar(): void
    {
        if (!svic_is_fathers_day_promotion_visible()) {
            return;
        }

        $code    = svic_fathers_day_promotion_code();
        $shopUrl = function_exists('svic_url_with_lang') ? svic_url_with_lang(home_url('/shop/')) : home_url('/shop/');
        ?>
        <div class="svic-promo-bar svic-promo-bar--fathers-day" role="region" aria-label="<?php echo svic_translate_attr('promotion.fathers_day.aria_label'); ?>">
            <div class="svic-promo-bar__inner">
                <span class="svic-promo-bar__eyebrow"><?php echo svic_translate_html('promotion.fathers_day.eyebrow'); ?></span>
                <span class="svic-promo-bar__message"><?php echo svic_translate_html('promotion.fathers_day.message'); ?></span>
                <span class="svic-promo-bar__offers" aria-label="<?php echo svic_translate_attr('promotion.fathers_day.offers_label'); ?>">
                    <span class="svic-promo-bar__chip"><?php echo svic_translate_html('promotion.fathers_day.offer_10p'); ?></span>
                    <span class="svic-promo-bar__chip svic-promo-bar__chip--strong"><?php echo svic_translate_html('promotion.fathers_day.offer_10s'); ?></span>
                </span>
                <span class="svic-promo-bar__code"><?php echo svic_translate_html('promotion.fathers_day.code_label', ['code' => $code]); ?></span>
                <a class="svic-promo-bar__cta" href="<?php echo esc_url($shopUrl); ?>"><?php echo svic_translate_html('promotion.fathers_day.cta'); ?></a>
            </div>
        </div>
        <?php
    }
}
