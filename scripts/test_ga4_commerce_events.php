<?php
// Exercise the WooCommerce hook boundary without real orders or analytics traffic.
define('ABSPATH', __DIR__);
$hooks = []; $page = 'product'; $emitted = [];
function add_action($name, $fn, $priority = 10, $args = 1) { global $hooks; $hooks[$name] = $fn; }
function svic_is_tracking_enabled() { return true; }
function is_admin() { return false; }
function is_product() { return $GLOBALS['page'] === 'product'; }
function is_cart() { return $GLOBALS['page'] === 'cart'; }
function is_checkout() { return $GLOBALS['page'] === 'checkout'; }
function is_order_received_page() { return $GLOBALS['page'] === 'thankyou'; }
function get_queried_object_id() { return 15; }
function get_woocommerce_currency() { return 'USD'; }
function wc_get_price_excluding_tax($p) { return $p->get_price(); }
function wp_json_encode($v, $flags = 0) { return json_encode($v, $flags); }
class WC_Product {
 function get_id() { return 15; } function get_sku() { return 'SVI-15P'; }
 function get_name() { return 'SVICLOUD 15P'; } function get_price() { return 288; }
}
function wc_get_product($id) { return new WC_Product(); }
class SessionStub { public array $data=[]; function get($k,$d=null){return $this->data[$k]??$d;} function set($k,$v){$this->data[$k]=$v;} }
class CartStub { function get_cart(){return [['data'=>new WC_Product(),'quantity'=>2,'line_total'=>550]];} }
$wc=(object)['session'=>new SessionStub(),'cart'=>new CartStub()]; function WC(){return $GLOBALS['wc'];}
function check($ok,$why){if(!$ok){throw new RuntimeException($why);}}
$file=__DIR__.'/../theme/svicloudtvbox-lumen/inc/ga4-commerce.php';
check(is_file($file),'Missing standard commerce hook implementation'); require $file;
function render_events(){ob_start();svic_render_ga4_commerce_events();return ob_get_clean();}
check(isset($hooks['woocommerce_add_to_cart']), 'Must use confirmed Woo add hook');
$s=render_events();check(str_contains($s,'view_item'),'Product view missing');check(str_contains($s,'SVI-15P'),'Item SKU missing');
svic_queue_ga4_cart_add('cart-key',15,2,0,[],[]);
$GLOBALS['page']='cart';$s=render_events();check(str_contains($s,'add_to_cart'),'Successful cart addition missing');check(str_contains($s,'576'),'Added value must reflect quantity');check(str_contains($s,'view_cart'),'Cart view missing');
check(!str_contains(render_events(),'add_to_cart'),'Cart addition must drain once');
$GLOBALS['page']='checkout';$s=render_events();check(str_contains($s,'begin_checkout'),'Checkout entry missing');check(str_contains($s,'550'),'Checkout uses discounted line totals');
$GLOBALS['page']='thankyou';check(render_events()==='','Do not emit checkout on thankyou');
echo "PASS: product, confirmed add, one-time queue, discounted cart, checkout and thankyou exclusion\n";
