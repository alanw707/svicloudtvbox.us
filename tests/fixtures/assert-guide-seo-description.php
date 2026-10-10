<?php
/**
 * Isolated regression for guide-only localized Rank Math descriptions.
 * Run: php tests/fixtures/assert-guide-seo-description.php
 */
$root = dirname(__DIR__, 2);
$GLOBALS['ctx'] = ['locale' => 'en_US', 'type' => 'post', 'slug' => 'best-chinese-tv-box-north-america', 'id' => 325, 'admin' => false, 'front' => false];
$GLOBALS['meta'] = [
    '_svic_description_zh_tw' => '選購美國中文電視盒前，核對第三方應用程式、電視與網路配置、總費用、配送、退貨及賣家支援。比較目前機型並確認目的地條款。',
    '_svic_description_zh_cn' => '选购美国中文电视盒前，核对第三方应用程序、电视与网络配置、总费用、配送、退货及卖家支持。比较当前机型并确认目的地条款。',
];
$GLOBALS['hooks'] = [];
function is_admin(): bool { return $GLOBALS['ctx']['admin']; }
function is_front_page(): bool { return $GLOBALS['ctx']['front']; }
function is_singular($type = null): bool { return $type === null ? in_array($GLOBALS['ctx']['type'], ['post', 'product'], true) : $GLOBALS['ctx']['type'] === $type; }
function get_queried_object_id(): int { return $GLOBALS['ctx']['id']; }
function get_post_field(string $field, int $id): string { return $field === 'post_name' && $id === 325 ? $GLOBALS['ctx']['slug'] : ''; }
function svic_current_locale(): string { return $GLOBALS['ctx']['locale']; }
function get_locale(): string { return 'en_US'; }
function get_post_meta(int $id, string $key, bool $single = true): string { return $id === 325 ? ($GLOBALS['meta'][$key] ?? '') : ''; }
function wp_strip_all_tags(string $text): string { return strip_tags($text); }
function strip_shortcodes(string $text): string { return $text; }
function add_filter(string $hook, string $callback, int $priority): void { $GLOBALS['hooks'][] = [$hook, $callback, $priority]; }
function svic_build_singular_seo_description(int $id): string { return 'generated fallback'; }
function load_block(string $file, string $start, string $end): void {
    $src = file_get_contents($file);
    $a = strpos($src, $start);
    $b = $a === false ? false : strpos($src, $end, $a + strlen($start));
    if ($a === false || $b === false || $b <= $a) { throw new RuntimeException("Missing function block: $start"); }
    eval(substr($src, $a, $b - $a));
}
$helper = $root . '/theme/svicloudtvbox-lumen/inc/helpers-svic.php';
$theme = $root . '/theme/svicloudtvbox-lumen/functions.php';
load_block($helper, "if (!function_exists('svic_post_locale_meta'))", "if (!function_exists('svic_post_localized_content'))");
load_block($theme, "if (!function_exists('svic_clean_seo_description_text'))", "if (!function_exists('svic_is_seo_description_useful'))");
load_block($theme, "if (!function_exists('svic_is_seo_description_useful'))", "if (!function_exists('svic_build_singular_seo_description'))");
load_block($theme, "if (!function_exists('svic_filter_rank_math_singular_description'))", "if (!function_exists('svic_filter_singular_post_document_title'))");
$english = 'Compare Chinese TV boxes for a US home: third-party app compatibility, TV setup, total cost, shipping, returns and seller support. Check current models.';
$tested = 0;
function check_case(string $name, string $want, string $english): void {
    global $tested;
    $got = svic_filter_rank_math_singular_description($english);
    if ($got !== $want) { fwrite(STDERR, "$name failed: " . json_encode($got, JSON_UNESCAPED_UNICODE) . "\n"); exit(1); }
    $tested++;
}
check_case('English guide unchanged', $english, $english);
$GLOBALS['ctx']['locale'] = 'zh_TW';
check_case('Traditional guide', $GLOBALS['meta']['_svic_description_zh_tw'], $english);
$GLOBALS['ctx']['locale'] = 'zh_CN';
check_case('Simplified guide', $GLOBALS['meta']['_svic_description_zh_cn'], $english);
$GLOBALS['ctx']['locale'] = 'zh-cn';
check_case('hyphenated locale', $GLOBALS['meta']['_svic_description_zh_cn'], $english);
$GLOBALS['meta']['_svic_description_zh_cn'] = '';
check_case('missing Simplified uses Traditional fallback', $GLOBALS['meta']['_svic_description_zh_tw'], $english);
$GLOBALS['meta']['_svic_description_zh_tw'] = '';
check_case('missing both translations leaves useful English', $english, $english);
$GLOBALS['meta']['_svic_description_zh_tw'] = '選購美國中文電視盒前，核對第三方應用程式、電視與網路配置、總費用、配送、退貨及賣家支援。比較目前機型並確認目的地條款。';
$GLOBALS['meta']['_svic_description_zh_cn'] = '选购美国中文电视盒前，核对第三方应用程序、电视与网络配置、总费用、配送、退货及卖家支持。比较当前机型并确认目的地条款。';
$GLOBALS['ctx']['slug'] = 'unrelated-post';
check_case('unrelated post unchanged', $english, $english);
$GLOBALS['ctx']['slug'] = 'best-chinese-tv-box-north-america';
$GLOBALS['ctx']['type'] = 'product';
check_case('product unchanged', $english, $english);
$GLOBALS['ctx']['type'] = 'post';
$GLOBALS['ctx']['admin'] = true;
check_case('admin unchanged', $english, $english);
$expectedHooks = ['rank_math/frontend/description', 'rank_math/frontend/snippet_description', 'rank_math/opengraph/facebook/og_description', 'rank_math/opengraph/twitter/twitter_description'];
foreach ($expectedHooks as $hook) {
    if (!in_array([$hook, 'svic_filter_rank_math_singular_description', 40], $GLOBALS['hooks'], true)) { fwrite(STDERR, "Missing hook: $hook\n"); exit(1); }
}
echo "PASS $tested cases and " . count($expectedHooks) . " description hooks\n";
