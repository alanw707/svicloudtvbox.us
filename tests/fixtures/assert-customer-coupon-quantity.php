<?php
/** Isolated coupon validator tests: no WordPress writes or orders. */
declare(strict_types=1);
define('ABSPATH', __DIR__);
function absint($value): int { return abs((int) $value); }
function __($text, $domain): string { return $text; }
function add_filter($hook, $callback, $priority, $args): void {
    if ([$hook, $callback, $priority, $args] !== ['woocommerce_coupon_is_valid', 'svic_validate_coupon_product_quantity', 20, 2]) {
        throw new RuntimeException('Unexpected hook registration');
    }
}
class WC_Coupon {
    public function __construct(public $minimum = '', public array $ids = [12]) {}
    public function get_meta($key, $single) { return $key === '_svic_min_product_quantity' ? $this->minimum : ''; }
    public function get_product_ids(): array { return $this->ids; }
}
class WC_Cart {
    public function __construct(public array $items = []) {}
    public function get_cart(): array { return $this->items; }
}
$wc = (object) ['cart' => new WC_Cart()];
function WC() { return $GLOBALS['wc']; }
require __DIR__ . '/../../theme/svicloudtvbox-lumen/inc/customer-coupons.php';
function item(int $id, int $quantity, int $variation = 0): array {
    return ['product_id' => $id, 'quantity' => $quantity, 'variation_id' => $variation];
}
$cases = [
    ['ordinary coupon untouched', new WC_Coupon(), [], true],
    ['zero disables opt-in', new WC_Coupon(0), [], true],
    ['one rejected', new WC_Coupon(3), [item(12, 1)], false],
    ['two rejected', new WC_Coupon(3), [item(12, 2)], false],
    ['three accepted', new WC_Coupon(3), [item(12, 3)], true],
    ['four accepted', new WC_Coupon(3), [item(12, 4)], true],
    ['other model rejected', new WC_Coupon(3), [item(1204, 3)], false],
    ['mixed-cart loophole rejected', new WC_Coupon(3), [item(12, 2), item(1204, 3), item(840, 8)], false],
    ['mixed eligible cart accepted', new WC_Coupon(3), [item(12, 3), item(1204, 1)], true],
    ['empty cart rejected', new WC_Coupon(3), [], false],
    ['missing product scope fails closed', new WC_Coupon(3, []), [item(12, 3)], false],
    ['split lines aggregate', new WC_Coupon(3), [item(12, 1), item(12, 2)], true],
    ['negative quantities do not qualify', new WC_Coupon(3), [item(12, -3)], false],
    ['missing quantity does not qualify', new WC_Coupon(3), [['product_id' => 12]], false],
    ['parent includes variations', new WC_Coupon(3), [item(12, 3, 99)], true],
    ['explicit variation scope', new WC_Coupon(3, [99]), [item(12, 3, 99)], true],
    ['other variation excluded', new WC_Coupon(3, [99]), [item(12, 3, 98)], false],
];
$count = 0;
foreach ($cases as [$label, $coupon, $items, $expected]) {
    $wc->cart = new WC_Cart($items);
    try { $actual = svic_validate_coupon_product_quantity(true, $coupon); }
    catch (Exception $e) { $actual = false; }
    if ($actual !== $expected) { throw new RuntimeException('FAIL: ' . $label); }
    ++$count;
    echo 'PASS: ' . $label . PHP_EOL;
}
if (svic_validate_coupon_product_quantity(false, new WC_Coupon(3)) !== false) {
    throw new RuntimeException('Prior invalid result was overwritten');
}
++$count;
$wc->cart = null;
try { svic_validate_coupon_product_quantity(true, new WC_Coupon(3)); throw new RuntimeException('Missing cart unexpectedly accepted'); }
catch (Exception $e) {
    if ($e->getMessage() !== 'This coupon requires a cart containing its eligible products.') { throw $e; }
}
++$count;
if (svic_validate_coupon_product_quantity(true, new WC_Coupon()) !== true) {
    throw new RuntimeException('Ordinary coupon requires cart');
}
++$count;
echo "All {$count} assertions passed.\n";
