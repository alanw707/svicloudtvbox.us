<?php
/**
 * Administrative maintenance helpers for the Lumen theme.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('svic_theme_cleanup_duplicate_installs')) {
    /**
     * Removes duplicate copies of the theme that share the same display name.
     *
     * WordPress sometimes leaves behind directories such as `svicloudtvbox`
     * or `svicloudtvbox-lumen-1` when the theme is uploaded repeatedly.
     */
    function svic_theme_cleanup_duplicate_installs() {
        if (!is_admin() || !current_user_can('delete_themes')) {
            return;
        }

        $primary_stylesheet = get_template();
        $primary_theme      = wp_get_theme($primary_stylesheet);

        if (!$primary_theme instanceof WP_Theme || !$primary_theme->exists()) {
            return;
        }

        $primary_name = $primary_theme->get('Name');
        if (!$primary_name) {
            return;
        }

        if (!function_exists('wp_get_themes')) {
            return;
        }

        $themes = wp_get_themes();
        if (!$themes) {
            return;
        }

        if (!function_exists('delete_theme')) {
            require_once ABSPATH . 'wp-admin/includes/theme.php';
        }

        foreach ($themes as $stylesheet => $theme) {
            if (!$theme instanceof WP_Theme || !$theme->exists()) {
                continue;
            }

            if ($stylesheet === $primary_stylesheet) {
                continue;
            }

            if ($theme->get('Name') !== $primary_name) {
                continue;
            }

            delete_theme($stylesheet);
        }
    }
}

if (!function_exists('svic_theme_filter_theme_lists')) {
    /**
     * Hides legacy mounts from the wp-admin theme browser.
     *
     * @param array $prepared Themes payload prepared for JS.
     * @return array
     */
    function svic_theme_filter_theme_lists($prepared) {
        if (!is_array($prepared)) {
            return $prepared;
        }

        $skip_ids = [
            'shared',
            'svicloudtvbox',
        ];

        $skip_patterns = [
            '/^svicloudtvbox-lumen-[0-9]+$/',
        ];

        $should_skip = static function ($candidate) use ($skip_ids, $skip_patterns) {
            if (!is_array($candidate)) {
                return false;
            }

            $stylesheet = $candidate['stylesheet'] ?? '';
            $slug = $candidate['slug'] ?? '';
            $template = $candidate['template'] ?? '';
            $id = $candidate['id'] ?? $stylesheet;

            foreach ([$id, $stylesheet, $slug, $template] as $value) {
                if ($value && in_array($value, $skip_ids, true)) {
                    return true;
                }
            }

            $haystack = array_filter([$id, $stylesheet, $slug, $template]);
            if (!$haystack) {
                return false;
            }

            $haystack = array_unique($haystack);

            $matches_pattern = static function ($value, $patterns) {
                foreach ($patterns as $pattern) {
                    if (preg_match($pattern, $value)) {
                        return true;
                    }
                }
                return false;
            };

            foreach ($haystack as $value) {
                if ($matches_pattern($value, $skip_patterns)) {
                    return true;
                }
            }

            return false;
        };

        if (isset($prepared['brokenThemes']) && is_array($prepared['brokenThemes'])) {
            $prepared['brokenThemes'] = array_values(array_filter(
                $prepared['brokenThemes'],
                static function ($item) use ($should_skip) {
                    return !$should_skip($item);
                }
            ));
        }

        $lists = ['themes', 'themesFiltered', 'allThemes'];
        foreach ($lists as $key) {
            if (!isset($prepared[$key]) || !is_array($prepared[$key])) {
                continue;
            }

            $prepared[$key] = array_values(array_filter(
                $prepared[$key],
                static function ($item) use ($should_skip) {
                    return !$should_skip($item);
                }
            ));
        }

        return $prepared;
    }
}

add_action('admin_init', 'svic_theme_cleanup_duplicate_installs', 5);
add_filter('wp_prepare_themes_for_js', 'svic_theme_filter_theme_lists', 10);
