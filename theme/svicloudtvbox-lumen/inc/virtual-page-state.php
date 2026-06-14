<?php
/** Helpers to make virtual theme routes render as indexable pages, not internal 404s. */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('svic_mark_virtual_page_request')) {
    function svic_mark_virtual_page_request(string $title = ''): void {
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
    }
}

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
