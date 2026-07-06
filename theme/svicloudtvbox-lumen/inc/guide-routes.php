<?php
/** Fallback routes for guide section templates when local DB pages are absent. */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('svic_guide_route_map')) {
    function svic_guide_route_map(): array {
        return [
            'guides-setup' => 'setup',
            'guides-apps' => 'apps',
            'guides-troubleshooting' => 'troubleshooting',
            'guides-after-setup' => 'post_setup',
            'guides-resources' => 'resources',
            'guides-support' => 'support',
            'svicloud遙控器配對失敗-故障碼排查一次搞定' => 'troubleshooting',
        ];
    }
}

if (!function_exists('svic_current_guide_route_key')) {
    function svic_current_guide_route_key(): string {
        $path = parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
        $path = is_string($path) ? trim(rawurldecode($path), '/') : '';
        if (preg_match('#^(zh|zh-cn|zh-tw)/(.+)$#i', $path, $matches)) {
            $path = $matches[2];
        }
        return $path;
    }
}

if (!function_exists('svic_render_guide_route')) {
    function svic_render_guide_route(): void {
        if (is_admin()) { return; }
        $map = svic_guide_route_map();
        $path = svic_current_guide_route_key();
        if (!isset($map[$path])) { return; }

        $existing_page = get_page_by_path($path, OBJECT, 'page');
        if ($existing_page instanceof WP_Post && $existing_page->post_status === 'publish') {
            return;
        }

        global $svic_guides_detail_key;
        $svic_guides_detail_key = $map[$path];
        $title_key = 'SVICLOUD Guide';
        $bundle = function_exists('svic_guides_get_section_content') ? svic_guides_get_section_content($svic_guides_detail_key) : null;
        if (is_array($bundle) && !empty($bundle['section']['translation_root'])) {
            $candidate = svic_translate($bundle['section']['translation_root'] . '.title');
            if (is_string($candidate) && $candidate !== '') {
                $title_key = $candidate;
            }
        }
        if (function_exists('svic_mark_virtual_page_request')) {
            svic_mark_virtual_page_request($title_key, 'guides');
        } else {
            status_header(200);
        }
        include get_template_directory() . '/page-guide-section.php';
        exit;
    }
}
add_action('template_redirect', 'svic_render_guide_route', -80);
