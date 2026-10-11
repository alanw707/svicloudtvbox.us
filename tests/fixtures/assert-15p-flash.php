<?php
/** Isolated promo test: no WordPress or WooCommerce DB; never places an order. */
declare(strict_types=1);
define('ABSPATH', __DIR__ . '/');
$GLOBALS['hooks'] = [];
$GLOBALS['options'] = [];
$GLOBALS['coupon_id'] = 0;
$GLOBALS['coupon_save_count'] = 0;
$GLOBALS['test_locale'] = 'en_US';
$GLOBALS['expired_override'] = false;
class WC_Product {
    public function __construct(private int $id, private float $price = 287.99, private bool $stock = true) {}
    public function get_id(): int { return $this->id; }
    public function get_parent_id(): int { return 0; }
    public function is_in_stock(): bool { return $this->stock; }
    public function is_purchasable(): bool { return $this->stock; }
    public function is_on_sale(): bool { return true; }
    public function get_date_on_sale_to() { return null; }
    public function get_price(): float { return $this->price; }
}
class WC_Coupon {
    public array $values = [];
    public function __construct(public int $id = 0) {}
    public function __call(string $name, array $args) {
        if (str_starts_with($name, 'set_')) { $this->values[substr($name, 4)] = $args[0]; return; }
        throw new RuntimeException($name);
    }
    public function get_code(): string { return (string) ($this->values['code'] ?? ''); }
    public function save(): void { $GLOBALS['coupon_id'] = 997; $GLOBALS['saved_coupon'] = $this; $GLOBALS['coupon_save_count']++; }
}
class WC_DateTime extends DateTime {}
class TestCart {
    public array $items = [];
    public function get_cart(): array { return $this->items; }
}
function WC(): object { return $GLOBALS['woocommerce']; }
function add_action(string $hook, $callback, int $priority = 10): void { $GLOBALS['hooks'][$hook][] = $callback; }
function add_filter(string $hook, $callback, int $priority = 10, int $args = 1): void { $GLOBALS['hooks'][$hook][] = $callback; }
function apply_filters(string $hook, $value) {
    if ($hook === 'svic_active_promotion_config' && $GLOBALS['expired_override']) { $value['end'] = '2026-10-10 23:59:59'; }
    return $value;
}
function wp_installing(): bool { return false; }
function absint($value): int { return abs((int) $value); }
function get_option(string $key) { return $GLOBALS['options'][$key] ?? null; }
function update_option(string $key, $value, $autoload = null): void { $GLOBALS['options'][$key] = $value; }
function wc_get_coupon_id_by_code(string $code): int { return $GLOBALS['coupon_id']; }
function wc_get_product(int $id): WC_Product { return $id === 1204 ? new WC_Product(1204) : new WC_Product(12, 239.00); }
function wp_timezone(): DateTimeZone { return new DateTimeZone('UTC'); }
function is_user_logged_in(): bool { return false; }
function current_user_can(string $cap): bool { return false; }
function svic_translate(string $key, array $replacements = []): string {
    $lang = require dirname(__DIR__, 2) . '/theme/svicloudtvbox-lumen/lang/' . $GLOBALS['test_locale'] . '.php';
    $value = $lang;
    foreach (explode('.', $key) as $piece) { $value = $value[$piece] ?? null; }
    if (!is_string($value)) { throw new RuntimeException('Missing translation ' . $key); }
    foreach ($replacements as $key => $replacement) { $value = str_replace('{{' . $key . '}}', (string) $replacement, $value); }
    return $value;
}
function esc_html(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
function esc_attr(string $value): string { return esc_html($value); }
function esc_url(string $value): string { return esc_attr($value); }
function home_url(string $path): string { return 'https://svicloudtvbox.us' . $path; }
function svic_url_with_lang(string $url): string { return $url; }
function sanitize_html_class(string $value): string { return preg_replace('/[^a-z0-9_-]/i', '', $value); }
function get_permalink(int $id): string { return home_url($id === 12 ? '/product/svicloud-10p-plus/' : '/product/svicloud-15p/'); }
function wc_price(float $price): string { return '$' . number_format($price, 2); }
function wp_strip_all_tags(string $value): string { return strip_tags($value); }
function wc_round_discount(float $amount, int $places): float { return round($amount, $places); }
function wc_get_rounding_precision(): int { return 2; }
function expect(bool $condition, string $message): void { if (!$condition) { throw new RuntimeException($message); } }
$GLOBALS['woocommerce'] = (object) ['cart' => new TestCart()];
require dirname(__DIR__, 2) . '/theme/svicloudtvbox-lumen/inc/active-promotion.php';
expect(svic_promotion_code() === '15PFLASH5', 'coupon code');
expect(svic_promotion_product_ids() === [1204], 'product restriction');
expect(svic_promotion_timezone()->getName() === 'America/Los_Angeles', 'Pacific timezone');
$tz = svic_promotion_timezone();
expect(!svic_is_promotion_active(new DateTimeImmutable('2026-10-10 23:59:59', $tz)), 'before start');
expect(svic_is_promotion_active(new DateTimeImmutable('2026-10-11 00:00:00', $tz)), 'start');
expect(svic_is_promotion_active(new DateTimeImmutable('2026-10-18 23:59:59', $tz)), 'end');
expect(!svic_is_promotion_active(new DateTimeImmutable('2026-10-19 00:00:00', $tz)), 'after end');
expect(svic_is_promotion_visible(), '15P offer visible while in stock');
svic_sync_active_promotion_coupon();
$coupon = $GLOBALS['saved_coupon'] ?? null;
expect($coupon instanceof WC_Coupon, 'coupon created');
expect($coupon->values['discount_type'] === 'percent' && $coupon->values['amount'] === 5.0, '5% discount');
expect($coupon->values['product_ids'] === [1204] && $coupon->values['individual_use'] === true, '15P only, no stacking');
expect($coupon->values['exclude_sale_items'] === false, 'applies to existing sale price');
expect($coupon->values['usage_limit_per_user'] === 1, 'per-user limit');
expect($coupon->values['date_expires']->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s') === '2026-10-19 06:59:59', 'coupon expires at Pacific midnight');
svic_sync_active_promotion_coupon();
expect($GLOBALS['coupon_save_count'] === 1, 'idempotent sync');
foreach (['en_US' => 'Extra 5% off', 'zh_TW' => '現價再享 95 折', 'zh_CN' => '现价再享 95 折'] as $locale => $needle) {
    $GLOBALS['test_locale'] = $locale;
    ob_start(); svic_render_home_sale_banner(new WC_Product(12, 239.00)); $html = ob_get_clean();
    expect(str_contains($html, $needle), 'localized 15P banner ' . $locale);
    expect(str_contains($html, '15PFLASH5') && str_contains($html, '/product/svicloud-15p/'), 'coupon code and 15P CTA ' . $locale);
    expect(!str_contains($html, '10P+'), 'no simultaneous 10P banner ' . $locale);
    expect(str_contains($html, 'data-sale-expires='), 'cached banner hides at expiry');
}
$GLOBALS['test_locale'] = 'en_US';
$GLOBALS['woocommerce']->cart->items = [['data' => new WC_Product(1204)]];
expect(($GLOBALS['hooks']['woocommerce_coupon_is_valid'][0])(true, $coupon) === true, 'coupon valid on 15P cart');
expect(($GLOBALS['hooks']['woocommerce_coupon_is_valid_for_product'][0])(true, new WC_Product(12, 239.00), $coupon, []) === false, '10P excluded');
expect(($GLOBALS['hooks']['woocommerce_coupon_is_valid_for_product'][0])(true, new WC_Product(1204), $coupon, []) === true, '15P eligible');
expect(($GLOBALS['hooks']['woocommerce_coupon_get_discount_amount'][0])(0, 287.99, ['data' => new WC_Product(1204)], true, $coupon) === 14.4, 'discount on existing $287.99 sale price');
$GLOBALS['woocommerce']->cart->items = [['data' => new WC_Product(12, 239.00)]];
try { ($GLOBALS['hooks']['woocommerce_coupon_is_valid'][0])(true, $coupon); throw new RuntimeException('10P-only cart accepted'); }
catch (Exception $e) { expect(str_contains($e->getMessage(), '15P'), '10P cart rejection'); }
$GLOBALS['expired_override'] = true;
expect(!svic_is_promotion_visible(), 'expired banner hidden');
ob_start(); svic_render_home_sale_banner(new WC_Product(12, 239.00)); $html = ob_get_clean();
expect(str_contains($html, 'Shop 10P+ sale') && !str_contains($html, '15PFLASH5'), '10P banner restored after expiry');
try { ($GLOBALS['hooks']['woocommerce_coupon_is_valid'][0])(true, $coupon); throw new RuntimeException('expired coupon accepted'); }
catch (Exception $e) { expect(str_contains($e->getMessage(), 'October 11'), 'expired coupon rejection'); }
echo "15P flash coupon, locale, expiry, cart-scope checks passed\n";
