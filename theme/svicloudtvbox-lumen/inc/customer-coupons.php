<?php
declare(strict_types=1);
/**
 * Opt-in product quantity requirements for private customer coupons.
 *
 * Set _svic_min_product_quantity on a coupon with explicit product_ids.
 * Coupons without this metadata retain WooCommerce's normal validation.
 * Customer identities and offer amounts belong in WooCommerce, not source.
 */

if (!defined('ABSPATH')) { exit; }

if (!function_exists('svic_validate_coupon_product_quantity')) {
    function svic_validate_coupon_product_quantity($valid, $coupon) {
        if (!$valid || !($coupon instanceof WC_Coupon)) {
            return $valid;
        }

        $minimum = (int) $coupon->get_meta('_svic_min_product_quantity', true);
        if ($minimum <= 0) {
            return $valid;
        }

        $product_ids = array_map('absint', $coupon->get_product_ids());
        $cart = function_exists('WC') ? WC()->cart : null;
        if (!$product_ids || !($cart instanceof WC_Cart)) {
            throw new Exception(__('This coupon requires a cart containing its eligible products.', 'svicloudtvbox-lumen'));
        }

        $quantity = 0;
        foreach ($cart->get_cart() as $item) {
            $product_id = absint($item['product_id'] ?? 0);
            $variation_id = absint($item['variation_id'] ?? 0);
            if (in_array($product_id, $product_ids, true) || ($variation_id && in_array($variation_id, $product_ids, true))) {
                $quantity += max(0, (int) ($item['quantity'] ?? 0));
            }
        }

        if ($quantity < $minimum) {
            throw new Exception(sprintf(
                __('This coupon requires at least %d eligible products in your cart. Other products do not count toward this minimum.', 'svicloudtvbox-lumen'),
                $minimum
            ));
        }

        return $valid;
    }
}

add_filter('woocommerce_coupon_is_valid', 'svic_validate_coupon_product_quantity', 20, 2);
