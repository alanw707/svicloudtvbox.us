<?php
/**
 * Theme setup and assets for SVICLOUDTVBOX Neon Tech block theme.
 */

if (!function_exists('svicloudtvbox_setup')) {
    function svicloudtvbox_setup() {
        add_theme_support('wp-block-styles');
        add_theme_support('responsive-embeds');
        add_theme_support('editor-styles');
        add_theme_support('html5', ['style', 'script']);
        add_theme_support('automatic-feed-links');
        add_editor_style('style.css');
    }
}
add_action('after_setup_theme', 'svicloudtvbox_setup');

/** Enqueue fonts (Inter + Noto Sans SC) */
function svicloudtvbox_enqueue_assets() {
    // Google Fonts — adjust if self-hosting later
    wp_enqueue_style(
        'svicloudtvbox-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Noto+Sans+SC:wght@400;700&display=swap',
        [],
        null
    );
}
add_action('wp_enqueue_scripts', 'svicloudtvbox_enqueue_assets');

/** Optional: Register a custom block pattern category for theme patterns */
add_action('init', function () {
    register_block_pattern_category('svicloud', [
        'label' => __('SVICLOUDTVBOX', 'svicloudtvbox')
    ]);
});

