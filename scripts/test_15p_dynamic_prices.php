<?php
declare(strict_types=1);

// Isolated theme test: never loads WordPress or changes a real WooCommerce product.
define('ABSPATH', __DIR__);
define('OBJECT', 'OBJECT');
$locale = 'en_US';
$lookups = 0;
$missing = in_array('--missing', $argv, true);
class WooCommerce {}
class WP_Post { public int $ID = 1204; }
class Test15pProduct {
    public string $price = '312.49';
    public string $regular = '411.90';
    public bool $sale = true;
    public function get_price(): string { return $this->price; }
    public function get_regular_price(): string { return $this->regular; }
    public function is_on_sale(): bool { return $this->sale; }
}
$product = new Test15pProduct();
function add_action(...$args): void {}
function add_filter(...$args): void {}
function apply_filters($hook, $value, ...$rest) { return $value; }
function get_locale(): string { global $locale; return $locale; }
function get_template_directory(): string { return __DIR__ . '/../theme/svicloudtvbox-lumen'; }
function trailingslashit(string $path): string { return rtrim($path, '/') . '/'; }
function wp_cache_get($key, $group) { return false; }
function wp_cache_set($key, $value, $group): void {}
function home_url(string $path = ''): string { return 'https://svicloudtvbox.us' . $path; }
function get_page_by_path($slug, $output, $post_type) {
    global $lookups, $missing;
    if ($slug !== 'svicloud-15p' || $post_type !== 'product') { throw new RuntimeException('Wrong product lookup'); }
    ++$lookups;
    return $missing ? null : new WP_Post();
}
function wc_get_product(int $id): Test15pProduct { global $product; return $product; }
function check_price(string $label, string $value, string $current, string $regular): void {
    if (!str_contains($value, $current) || !str_contains($value, $regular) || str_contains($value, '{15p_')) {
        throw new RuntimeException($label . ': price tokens unresolved or wrong: ' . substr($value, 0, 250));
    }
}
function check_no_stale(string $label, string $value): void {
    if (str_contains($value, '{15p_') || str_contains($value, '$287.99') || str_contains($value, '$379') || str_contains($value, 'USsee')) {
        throw new RuntimeException($label . ': unresolved/stale price: ' . substr($value, 0, 250));
    }
}

require __DIR__ . '/../theme/svicloudtvbox-lumen/inc/class-svic-translator.php';
require __DIR__ . '/../theme/svicloudtvbox-lumen/inc/helpers-svic.php';
require __DIR__ . '/../theme/svicloudtvbox-lumen/inc/15p-promo-page.php';
require __DIR__ . '/../theme/svicloudtvbox-lumen/inc/agent-resources.php';

if (in_array('--regular-only', $argv, true)) {
    $product->price = '430.00';
    $product->regular = '430.00';
    $product->sale = false;
    foreach (['en_US', 'zh_TW', 'zh_CN'] as $code) {
        $locale = $code;
        $copy = svic_translate('products.svicloud-15p.prelaunch.faq.availability.a', [], $code);
        if (!str_contains($copy, '$430.00') || str_contains($copy, '{15p_')) { throw new RuntimeException("$code regular-only price failed: $copy"); }
        $promo = json_encode(svic_15p_promo_content(), JSON_UNESCAPED_UNICODE);
        if (str_contains($promo, ' sale price') || str_contains($promo, '特價') || str_contains($promo, '特价')) {
            throw new RuntimeException("$code promo still advertises a sale");
        }
    }
    if ($lookups !== 1) { throw new RuntimeException('Catalog lookup not memoized'); }
    echo "PASS: regular-only 15P no false sale labels in three locales\n";
    exit;
}

foreach (['en_US', 'zh_TW', 'zh_CN'] as $code) {
    $locale = $code;
    $prefix = $code === 'en_US' ? '' : 'US';
    $current = $missing ? ($code === 'zh_CN' ? '请查看商品页当前价格' : ($code === 'zh_TW' ? '請見商品頁目前價格' : 'see current price on product page')) : $prefix . '$312.49';
    $regular = $missing ? ($code === 'zh_CN' ? '请查看商品页原价' : ($code === 'zh_TW' ? '請見商品頁原價' : 'see regular price on product page')) : $prefix . '$411.90';
    check_price("$code product SEO", svic_translate('products.svicloud-15p.meta.description', [], $code), $current, $regular);
    check_price("$code compare", svic_translate('compare.meta.description', [], $code), $current, $regular);
    check_price("$code homepage", svic_translate('frontpage.hero.copy', [], $code), $current, $regular);
    foreach (['products', 'frontpage', 'compare', 'shop'] as $section) {
        check_no_stale("$code $section translations", json_encode(svic_translate_array($section, $code), JSON_UNESCAPED_UNICODE));
    }
    $promo = svic_15p_promo_content();
    if (!str_contains($promo['meta_description'], $current)) { throw new RuntimeException("$code promo SEO price missing"); }
    $promo_text = json_encode($promo, JSON_UNESCAPED_UNICODE);
    check_price("$code promo", $promo_text, $current, $regular);
    check_no_stale("$code promo", $promo_text);
}
$locale = 'en_US';
$resources = svic_agent_resource_map();
foreach (['llms.txt', 'llms-full.txt', 'agent/svicloud-15p.md', 'agent/products.md'] as $key) {
    check_price($key, $resources[$key], $missing ? 'see current price on product page' : '$312.49', $missing ? 'see regular price on product page' : '$411.90');
    check_no_stale($key, $resources[$key]);
}
if ($lookups !== 1) { throw new RuntimeException('Expected one catalog lookup per request; got ' . $lookups); }
if ($missing && !str_contains(svic_translate('products.svicloud-15p.meta.description', [], 'zh-cn'), '请查看商品页当前价格')) {
    throw new RuntimeException('Explicit simplified-Chinese fallback ignored');
}
if (!$missing) {
    $product->price = '318.99';
    $product->regular = '430.00';
    check_price('price mutation in request', svic_15p_price_tokens('{15p_price} / {15p_regular} / {15p_regular_2dp}'), '$318.99', '$430');
    if (svic_15p_price_tokens('{15p_regular_2dp}') !== '$430.00') { throw new RuntimeException('Regular feed precision failed'); }
    $product->price = '';
    check_no_stale('empty Woo price', svic_15p_price_tokens('{15p_price}'));
    if (svic_15p_price_tokens('{15p_price}') !== 'see current price on product page') { throw new RuntimeException('Empty price was not suppressed'); }
}
echo 'PASS: ' . ($missing ? 'missing catalog' : 'changed catalog') . ", EN/Traditional/Simplified, SEO/promo/agent/regular precision\n";
