<?php
declare(strict_types=1);
define('ABSPATH', __DIR__);
function add_action(...$args) {}
function add_filter(...$args) {}
function esc_attr($s) { return htmlspecialchars($s, ENT_QUOTES); }
function esc_html($s) { return htmlspecialchars($s, ENT_QUOTES); }
function esc_url($s) { return $s; }
function wp_strip_all_tags($s) { return strip_tags($s); }
function wc_price($n) { return '<span>&#36;' . number_format($n, 2) . '</span>'; }
function get_permalink($id) { return '/product/svicloud-10p-plus/'; }
function svic_url_with_lang($url) { global $prefix; return $prefix . $url; }
function svic_translate($key, $replace = []) {
    global $translations;
    $value = $translations;
    foreach (explode('.', $key) as $part) { $value = $value[$part]; }
    foreach ($replace as $k => $v) { $value = str_replace('{{' . $k . '}}', $v, $value); }
    return $value;
}
class WC_Product {
    public bool $stock = true;
    public bool $sale = true;
    public float $price = 234.99;
    public int $expires;
    function __construct() { $this->expires = time() + 1209600; }
    function is_in_stock() { return $this->stock; }
    function is_on_sale() { return $this->sale; }
    function get_price() { return $this->price; }
    function get_regular_price() { return 269.0; }
    function get_id() { return 12; }
    function get_date_on_sale_to() { return new DateTimeImmutable('@' . $this->expires); }
}
require __DIR__ . '/../theme/svicloudtvbox-lumen/inc/active-promotion.php';
function render($product) { ob_start(); svic_render_home_sale_banner($product); return ob_get_clean(); }
$fixtures = [];
foreach (['en_US' => '', 'zh_TW' => '/zh', 'zh_CN' => '/zh-cn'] as $locale => $prefix) {
    $translations = require __DIR__ . '/../theme/svicloudtvbox-lumen/lang/' . $locale . '.php';
    $p = new WC_Product();
    $html = render($p);
    if (!str_contains($html, '$234.99') || !str_contains($html, '$34.01') || !str_contains($html, 'href="' . $prefix . '/product/')) { throw new RuntimeException('Localized price/link failed'); }
    $fixtures[$prefix] = $html;
    foreach (['stock' => false, 'sale' => false, 'price' => 255.55, 'expires' => time() - 1] as $key => $value) {
        $invalid = clone $p; $invalid->$key = $value;
        if (render($invalid) !== '') { throw new RuntimeException('Invalid sale banner shown: ' . $key); }
    }
}
if (in_array('--fixtures', $argv, true)) { echo json_encode($fixtures, JSON_UNESCAPED_UNICODE); }
else { echo "PASS: localized price/savings/link and expired/out-of-stock/changed-price guards\n"; }
