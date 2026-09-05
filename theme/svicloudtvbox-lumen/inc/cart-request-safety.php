<?php
/** Prevent replayed additions and make Buy Now use the requested quantity. */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

function svic_is_buy_now_request(): bool {
    return !empty($_GET['svic_buynow']); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
}

function svic_buy_now_cart_quantity(string $cart_item_key, int $product_id, $quantity): void {
    if (!svic_is_buy_now_request()) {
        return;
    }
    $cart = WC()->cart;
    // Woo has already validated this addition. Do not accumulate a prior unit
    // of the same model when the customer explicitly selects Buy Now × 1.
    $cart->set_quantity($cart_item_key, $quantity);
    foreach (array_keys($cart->get_cart()) as $key) {
        if ($key !== $cart_item_key) {
            $cart->remove_cart_item($key);
        }
    }
}
add_action('woocommerce_add_to_cart', 'svic_buy_now_cart_quantity', 10, 3);

function svic_cart_success_redirect(string $url): string {
    if (svic_is_buy_now_request()) {
        return svic_url_with_lang(wc_get_checkout_url());
    }
    // Woo invokes this only after a successful non-AJAX addition. A clean GET
    // destination avoids a second POST/add-to-cart when the shopper refreshes.
    return $url !== '' ? $url : svic_url_with_lang(wc_get_cart_url());
}
add_filter('woocommerce_add_to_cart_redirect', 'svic_cart_success_redirect');
