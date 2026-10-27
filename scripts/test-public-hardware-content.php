<?php
/** Standalone content regression checks; no WordPress database or network writes. */
define('ABSPATH', __DIR__);
$theme = dirname(__DIR__) . '/theme/svicloudtvbox-lumen';
function add_action(...$args) {}
function add_filter(...$args) {}
function apply_filters($hook, $value, ...$args) { return $value; }
function wp_cache_get(...$args) { return false; }
function wp_cache_set(...$args) {}
function trailingslashit($value) { return rtrim($value, '/') . '/'; }
function get_template_directory() { global $theme; return $theme; }
function get_stylesheet_directory() { return get_template_directory(); }
function get_locale() { return $GLOBALS['test_locale'] ?? 'en_US'; }
function svic_current_locale() { return get_locale(); }
function home_url($path = '') { return 'https://svicloudtvbox.us' . $path; }
function svic_15p_price_tokens_recursive($value) { return $value; }
function svic_15p_price_tokens($value, ...$args) { return $value; }
function sanitize_title($value) { return strtolower($value); }
function esc_html($value) { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
function esc_url($value) { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
require $theme . '/inc/hardware-disclaimer.php';
require $theme . '/inc/public-hardware-content.php';
require $theme . '/inc/class-svic-translator.php';
require $theme . '/inc/guides-data.php';
require $theme . '/inc/guide-routes.php';
require $theme . '/inc/15p-promo-page.php';
require $theme . '/inc/agent-resources.php';
$checks = 0; $errors = [];
function check($ok, $label) { global $checks, $errors; ++$checks; if (!$ok) { $errors[] = $label; } }
function scan($data, $where) {
    foreach ($data as $key => $value) {
        if (is_array($value)) { scan($value, "$where.$key"); continue; }
        if (!is_string($value)) { continue; }
        check(!preg_match('/channels?|頻道|频道|live\\s*tv|free\\s*channels|\\bBBC\\b|\\bNHK\\b|\\bbeIN\\b|直播|Yogurt|WeFun|8989c|\\.apk|sideload|App[- ]download|app installation|install (?:the |an? )?(?:app|software)|軟體安裝|软件安装|應用安裝|应用安装/iu', $value), "$where.$key: $value");
    }
}
$app_disclaimer_phrases = [
    'en_US' => ['do not provide or endorse third-party streaming software', 'do not guarantee its availability or performance', 'uptime of external servers'],
    'zh_TW' => ['不提供或背書任何第三方串流軟體', '不保證其可用性、效能', '外部伺服器的持續運作'],
    'zh_CN' => ['不提供或认可任何第三方流媒体软件', '不保证其可用性、性能', '外部服务器的持续运行'],
];
foreach (['en_US', 'zh_TW', 'zh_CN'] as $locale) {
    $GLOBALS['test_locale'] = $locale;
    $registry = SVIC_Translator::instance()->registry($locale);
    scan($registry, $locale);
    check(count($registry) > 10, "$locale retains registry structure");
    scan(svic_15p_promo_content(), "promo.$locale");
    $notice = svic_hardware_notice_copy();
    scan($notice, "notice.$locale");
    ob_start(); svic_render_hardware_notice(); $rendered = ob_get_clean();
    check(str_contains($rendered, esc_html($notice['body'])), "$locale notice renders buyer responsibility");
    foreach ($app_disclaimer_phrases[$locale] as $phrase) {
        check(str_contains($notice['body'], $phrase), "$locale notice includes $phrase");
        check(str_contains($rendered, esc_html($phrase)), "$locale notice visibly renders $phrase");
    }
    check(str_contains($rendered, esc_html($notice['rights'])), "$locale notice preserves consumer rights");
    check(str_contains($rendered, '/legal-disclaimer/'), "$locale notice links terms");
}
scan(svic_agent_resource_map(), 'agent');
$guides = svic_guides_get_content();
check($guides['app_cards'] === [], 'Retired cards absent');
check($guides['yogurt_mo_cards'] === [], 'Retired followup cards absent');
check(!isset($guides['resource_articles']['channel-guide']), 'Retired resource article absent');
foreach ($guides['anchor_items'] as $item) { check(!in_array($item['key'], ['apps', 'yogurt_mo'], true), 'Guide navigation excludes retired topics'); }
foreach (['/apps/', '/zh/apps/', '/zh-cn/guides-apps/', '/guides-yogurt-mo/', '/agent/apps.md', '/yogurt-tv-not-working-upgrade-guide/'] as $path) { check(svic_retired_public_path($path), "Retires $path"); }
foreach (['/', '/shop/', '/checkout/', '/cart/', '/guides-setup/'] as $path) { check(!svic_retired_public_path($path), "Preserves $path"); }
check(!isset(svic_guide_route_map()['guides-apps']), 'Fallback route removed');
$functions = file_get_contents($theme . '/functions.php');
check(str_contains($functions, "require_once __DIR__ . '/inc/public-hardware-content.php'"), 'Retirement hook loaded');
check(str_contains($functions, 'svic_public_hardware_static_meta($registry)'), 'Metadata override applied');
preg_match('/function svic_static_page_meta_registry\(\): array\s*\{.*?\n    \}/s', $functions, $match);
check(isset($match[0]), 'Metadata registry extractable');
if (isset($match[0])) { eval($match[0]); scan(svic_static_page_meta_registry(), 'static-meta'); }
foreach (['llms.txt', 'llms-full.txt'] as $file) { scan([file_get_contents(dirname(__DIR__) . '/agent-root/' . $file)], $file); }
if ($errors) { fwrite(STDERR, implode("\n", $errors) . "\nFAIL " . count($errors) . "/$checks\n"); exit(1); }
echo "PASS $checks hardware-content assertions\n";
