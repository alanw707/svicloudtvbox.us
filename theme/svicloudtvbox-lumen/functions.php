<?php
/**
 * SVICLOUD TV Box Classic Theme Functions
 *
 * @package SVICloudTVBoxClassic
 */

if (!defined('ABSPATH')) { exit; }

if (!defined('SVIC_THEME_TEXT_DOMAIN')) {
    define('SVIC_THEME_TEXT_DOMAIN', 'svicloudtvbox-lumen');
}

$svic_shared_helpers = dirname(get_template_directory()) . '/shared/helpers-svic.php';
if (file_exists($svic_shared_helpers)) {
    require_once $svic_shared_helpers;
}

// Theme setup
add_action('after_setup_theme', function () {
    load_theme_textdomain('svicloudtvbox-lumen', get_template_directory() . '/languages');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('woocommerce');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
    // Allow logo management from the WordPress Customizer
    add_theme_support('custom-logo', [
        'height'      => 75,
        'width'       => 220,
        'flex-height' => true,
        'flex-width'  => true,
        'unlink-homepage-logo' => false,
    ]);
    register_nav_menus([
        'primary' => __('Primary Menu', 'svicloudtvbox-lumen'),
        'footer'  => __('Footer Menu', 'svicloudtvbox-lumen'),
    ]);
});

// Enqueue assets
add_action('wp_enqueue_scripts', function () {
    $theme_version = wp_get_theme()->get('Version');

    // Cache-busting strategy:
    // - Prefer a numeric version from .deploy-version (written by deploy script)
    // - Fallback to file modification time
    // - Finally fallback to theme version string
    $deploy_ver_file = get_template_directory() . '/.deploy-version';
    $deploy_version = 0;
    if (file_exists($deploy_ver_file)) {
        $raw = trim((string) @file_get_contents($deploy_ver_file));
        if (ctype_digit($raw)) {
            $deploy_version = (int) $raw;
        }
    }

    $css_file = get_template_directory() . '/assets/css/style.css';
    $css_mtime = file_exists($css_file) ? (int) filemtime($css_file) : 0;
    $css_version = $deploy_version ? max($deploy_version, $css_mtime) : ($css_mtime ?: $theme_version);

    $js_file = get_template_directory() . '/assets/js/theme.js';
    $js_mtime = file_exists($js_file) ? (int) filemtime($js_file) : 0;
    $js_version = $deploy_version ? max($deploy_version, $js_mtime) : ($js_mtime ?: $theme_version);

    // Fonts
    wp_enqueue_style(
        'svicloudtvbox-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Sans+SC:wght@400;500;600;700&family=Noto+Sans+TC:wght@400;500;600;700&display=swap',
        [],
        null
    );

    // Theme styles
    wp_enqueue_style(
        'svicloudtvbox-style',
        get_template_directory_uri() . '/assets/css/style.css',
        ['svicloudtvbox-fonts'],
        $css_version
    );

    // Theme script
    wp_enqueue_script(
        'svicloudtvbox-script',
        get_template_directory_uri() . '/assets/js/theme.js',
        ['jquery'],
        $js_version,
        true
    );

    wp_localize_script('svicloudtvbox-script', 'svicTheme', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'homeUrl' => home_url('/'),
        'isWoo'   => class_exists('WooCommerce'),
        'themeUrl' => get_template_directory_uri(),
        'i18n'    => [
            'addingToCart' => esc_html__('Adding…', 'svicloudtvbox-lumen'),
        ],
    ]);
});

// Basic WooCommerce tweaks (optional minimal)
add_filter('woocommerce_enqueue_styles', '__return_empty_array');

// Performance cleanups
add_action('init', function () {
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
});

// Remove legacy theme body class to prevent confusion with classic skin
add_filter('body_class', function ($classes) {
    $disallowed = ['theme-svicloudtvbox', 'theme-svicloudtvbox-lumen'];
    foreach ($disallowed as $class) {
        $index = array_search($class, $classes, true);
        if ($index !== false) {
            unset($classes[$index]);
        }
    }

    return array_values($classes);
});

