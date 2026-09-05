<?php
declare(strict_types=1);
define('ABSPATH', __DIR__);
$hooks = [];
function add_action($hook, $callback, $priority = 10, $args = 1) { global $hooks; $hooks[$hook] = [$callback, $args]; }
function add_filter($hook, $callback) { add_action($hook, $callback); }
function svic_url_with_lang($url) { return '/zh' . $url; }
function wc_get_cart_url() { return '/cart/'; }
function wc_get_checkout_url() { return '/checkout/'; }
class TestCart {
    public array $items = ['same' => ['quantity' => 2], 'other' => ['quantity' => 1]];
    function set_quantity($key, $qty) { $this->items[$key]['quantity'] = $qty; }
    function get_cart() { return $this->items; }
    function remove_cart_item($key) { unset($this->items[$key]); }
}
$woo = (object) ['cart' => new TestCart()];
function WC() { global $woo; return $woo; }
require __DIR__ . '/../theme/svicloudtvbox-lumen/inc/cart-request-safety.php';
function check($condition, $message) { if (!$condition) { throw new RuntimeException($message); } }
$_GET = [];
[$callback, $args] = $hooks['woocommerce_add_to_cart'];
check($args === 3, 'Must receive Woo validated quantity');
$callback('same', 123, 1);
check(WC()->cart->items['same']['quantity'] === 2, 'Ordinary Add to cart remains additive');
check($hooks['woocommerce_add_to_cart_redirect'][0]('') === '/zh/cart/', 'Successful addition needs clean localized redirect');
check(svic_cart_success_redirect('/custom/') === '/custom/', 'Preserve existing explicit redirect');
$_GET['svic_buynow'] = '1';
$callback('same', 123, 1);
check(WC()->cart->items === ['same' => ['quantity' => 1]], 'Buy Now must not accumulate same-model quantity');
$callback('same', 123, 2);
check(WC()->cart->items['same']['quantity'] === 2, 'Allow intentionally requested two units');
check(svic_cart_success_redirect('') === '/zh/checkout/', 'Buy Now localized checkout redirect');
echo "PASS: request hooks, replay-safe redirect, Buy Now quantity and ordinary additions\n";
