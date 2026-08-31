<?php
/** Helpers to make virtual theme routes render as indexable pages, not internal 404s. */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('svic_mark_virtual_page_request')) {
    function svic_mark_virtual_page_request(string $title = '', string $style_key = ''): void {
        global $wp_query;

        status_header(200);
        if (is_object($wp_query)) {
            $wp_query->is_404      = false;
            $wp_query->is_page     = true;
            $wp_query->is_singular = true;
            $wp_query->is_home     = false;
            $wp_query->is_archive  = false;
            $wp_query->is_search   = false;
        }

        $GLOBALS['svic_virtual_page_title'] = $title;
        if ($style_key !== '') {
            $GLOBALS['svic_virtual_page_style_key'] = sanitize_key($style_key);
        }
    }
}

if (!function_exists('svic_virtual_route_slugs')) {
    function svic_virtual_route_slugs(): array {
        return [
            'contact',
            'guides-setup',
            'guides-apps',
            'guides-yogurt-mo',
            'guides-troubleshooting',
            'guides-after-setup',
            'guides-resources',
            'guides-support',
            'svicloud遙控器配對失敗-故障碼排查一次搞定',
            'svicloud-10p-vs-10s',
            'best-svicloud-box-for-chinese-tv-usa',
            'yogurt-tv-not-working-upgrade-guide',
            'svicloud-box-authenticity-guide',
            'svicloud-15p-features',
        ];
    }
}

if (!function_exists('svic_current_virtual_route_slug')) {
    function svic_current_virtual_route_slug(): string {
        $path = parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
        $path = is_string($path) ? trim(rawurldecode($path), '/') : '';
        if (preg_match('#^(zh|zh-cn|zh-tw)/(.+)$#i', $path, $matches)) {
            $path = trim($matches[2], '/');
        }
        return $path;
    }
}

if (!function_exists('svic_is_virtual_route_request')) {
    function svic_is_virtual_route_request(): bool {
        return in_array(svic_current_virtual_route_slug(), svic_virtual_route_slugs(), true);
    }
}

if (!function_exists('svic_prepare_virtual_route_request')) {
    function svic_prepare_virtual_route_request(): void {
        if (is_admin() || !svic_is_virtual_route_request()) {
            return;
        }
        svic_mark_virtual_page_request('', 'guides');
    }
}

add_action('parse_request', 'svic_prepare_virtual_route_request', 0);
add_action('wp', 'svic_prepare_virtual_route_request', 0);
add_action('template_redirect', 'svic_prepare_virtual_route_request', -1000000);

add_filter('redirect_canonical', function ($redirect_url) {
    return svic_is_virtual_route_request() ? false : $redirect_url;
}, 5);

add_filter('rank_math/redirection/fallback_exclude_locations', function (array $locations): array {
    foreach (svic_virtual_route_slugs() as $slug) {
        $locations[] = $slug;
        $locations[] = 'zh/' . $slug;
        $locations[] = 'zh-cn/' . $slug;
        $locations[] = 'zh-tw/' . $slug;
    }
    return array_values(array_unique($locations));
});

add_filter('document_title_parts', function (array $parts): array {
    $title = $GLOBALS['svic_virtual_page_title'] ?? '';
    if (is_string($title) && $title !== '') {
        $parts['title'] = $title;
    }
    return $parts;
}, 20);

add_filter('body_class', function (array $classes): array {
    if (empty($GLOBALS['svic_virtual_page_title'])) {
        return $classes;
    }

    $classes = array_values(array_filter($classes, static function ($class): bool {
        return $class !== 'error404';
    }));

    $classes[] = 'page';
    $classes[] = 'svic-virtual-page';
    return array_values(array_unique($classes));
}, 20);

add_action('wp_enqueue_scripts', function (): void {
    $style_key = isset($GLOBALS['svic_virtual_page_style_key'])
        ? sanitize_key((string) $GLOBALS['svic_virtual_page_style_key'])
        : '';

    if ($style_key !== 'guides') {
        return;
    }

    $relative_path = 'assets/css/guides.css';
    $full_path = get_template_directory() . '/' . $relative_path;
    if (!file_exists($full_path)) {
        return;
    }

    wp_enqueue_style(
        'svicloudtvbox-guides',
        get_template_directory_uri() . '/' . $relative_path,
        ['svicloudtvbox-style'],
        (string) filemtime($full_path)
    );
}, 20);
