<?php
/**
 * Empty cart template wrapper that reuses the Lumen cart layout.
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

do_action('woocommerce_cart_is_empty');

require __DIR__ . '/cart.php';
