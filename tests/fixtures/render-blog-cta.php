<?php
/** Minimal WordPress fixture for the single-post CTA (not a live-site test). */
declare(strict_types=1);

$locale = $argv[1] ?? 'en_US';
$GLOBALS['post_slug'] = $argv[2] ?? 'best-chinese-tv-box-north-america';
$GLOBALS['rendered_posts'] = 0;
$GLOBALS['locale_registry'] = require __DIR__ . '/../../theme/svicloudtvbox-lumen/lang/' . $locale . '.php';
$GLOBALS['english_registry'] = require __DIR__ . '/../../theme/svicloudtvbox-lumen/lang/en_US.php';

class WooCommerce {}
class Fixture_Product {
    private string $slug;
    public function __construct(string $slug) { $this->slug = $slug; }
    public function get_id(): string { return $this->slug; }
}
function get_header(): void {}
function get_footer(): void {}
function have_posts(): bool { return $GLOBALS['rendered_posts'] === 0; }
function the_post(): void { $GLOBALS['rendered_posts']++; }
function get_the_ID(): int { return 325; }
function the_ID(): void { echo '325'; }
function get_post_field(string $field, int $id): string {
    return $field === 'post_name' ? $GLOBALS['post_slug'] : '<p>Original article body.</p>';
}
function get_the_category(int $id): array { return []; }
function get_the_date($format = '', $id = 0): string { return 'October 9, 2026'; }
function get_the_modified_date($format = '', $id = 0): string { return 'October 9, 2026'; }
function svic_estimated_read_time(int $id): int { return 1; }
function esc_html__(string $text, string $domain): string { return $text; }
function esc_attr_e(string $text, string $domain): void { echo htmlspecialchars($text, ENT_QUOTES); }
function svic_post_title(int $id): string { return 'Test article'; }
function svic_post_locale_meta(int $id, string $field): string { return ''; }
function has_excerpt(): bool { return false; }
function svic_post_localized_content(int $id): string { return ''; }
function apply_filters(string $hook, string $value): string { return $value; }
function svic_extract_intro_media_blocks(string $body, $image): array {
    return ['content' => $body, 'blocks' => []];
}
function has_post_thumbnail($id = null): bool { return false; }
function post_class(string $class): void { echo 'class="' . $class . '"'; }
function svic_get_product_by_slug(string $slug): Fixture_Product { return new Fixture_Product($slug); }
function get_permalink(string $id): string { return 'https://svicloudtvbox.us/product/' . $id . '/'; }
function home_url(string $path): string { return 'https://svicloudtvbox.us' . $path; }
function svic_url_with_lang(string $url): string {
    $prefix = match ($GLOBALS['fixture_locale']) {
        'zh_TW' => '/zh',
        'zh_CN' => '/zh-cn',
        default => '',
    };
    return str_replace('https://svicloudtvbox.us/', 'https://svicloudtvbox.us' . $prefix . '/', $url);
}
function esc_url(string $url): string { return htmlspecialchars($url, ENT_QUOTES); }
function esc_html(string $text): string { return htmlspecialchars($text, ENT_QUOTES); }
function esc_attr(string $text): string { return htmlspecialchars($text, ENT_QUOTES); }
function fixture_lookup(array $registry, string $key): ?string {
    $value = $registry;
    foreach (explode('.', $key) as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) { return null; }
        $value = $value[$part];
    }
    return is_string($value) ? $value : null;
}
function svic_translate_html(string $key): string {
    $text = fixture_lookup($GLOBALS['locale_registry'], $key)
        ?? fixture_lookup($GLOBALS['english_registry'], $key);
    return htmlspecialchars($text ?? $key, ENT_QUOTES);
}
$GLOBALS['fixture_locale'] = $locale;
require __DIR__ . '/../../theme/svicloudtvbox-lumen/single.php';
