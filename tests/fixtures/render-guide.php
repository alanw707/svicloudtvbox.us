<?php
// Render the real guide template without a WordPress database for browser previews.
$locale = $argv[1] ?? 'en_US';
if (!in_array($locale, ['en_US', 'zh_TW', 'zh_CN'], true)) { exit(1); }
$theme = dirname(__DIR__, 2) . '/theme/svicloudtvbox-lumen';
$translations = include $theme . '/lang/' . $locale . '.php';
function get_header() {}
function get_footer() {}
function get_template_directory() { global $theme; return $theme; }
function home_url($path = '') { return 'https://svicloudtvbox.us' . $path; }
function svic_current_locale() { global $locale; return $locale; }
function svic_url_with_lang($url) {
    $prefix = ['en_US' => '', 'zh_TW' => '/zh', 'zh_CN' => '/zh-cn'][svic_current_locale()];
    return str_replace('https://svicloudtvbox.us/', 'https://svicloudtvbox.us' . $prefix . '/', $url);
}
function svic_translate($key) {
    global $translations;
    $value = $translations;
    foreach (explode('.', $key) as $part) { $value = $value[$part] ?? $key; }
    return is_string($value) ? $value : $key;
}
function esc_html($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function esc_attr($s) { return esc_html($s); }
function esc_url($s) { return esc_html($s); }
function svic_translate_html($key) { return esc_html(svic_translate($key)); }
function svic_translate_rich($key) { return svic_translate($key); }
function wp_kses_post($s) { return strip_tags($s, '<ol><li><strong><p><a><kbd><br>'); }
function get_page_by_path($s) { return null; }
require $theme . '/inc/guides-data.php';
require $theme . '/page-guides-troubleshooting.php';
