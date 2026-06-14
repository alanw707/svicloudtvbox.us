<?php
/** XML sitemap for virtual agent, guide fallback, and decision routes. */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('svic_agent_friendly_sitemap_paths')) {
    function svic_agent_friendly_sitemap_paths(): array {
        $paths = [
            '/llms.txt',
            '/llms-full.txt',
            '/agent/products.md',
            '/agent/compare-10p-vs-10s.md',
            '/agent/apps.md',
            '/agent/troubleshooting.md',
            '/agent/setup.md',
            '/agent/shipping-returns.md',
            '/agent/contact.md',
            '/guides-apps/',
            '/zh/guides-apps/',
            '/guides-troubleshooting/',
            '/zh/guides-troubleshooting/',
            '/guides-setup/',
            '/zh/guides-setup/',
            '/zh/svicloud遙控器配對失敗-故障碼排查一次搞定/',
            '/svicloud-10p-vs-10s/',
            '/best-svicloud-box-for-chinese-tv-usa/',
            '/yogurt-tv-not-working-upgrade-guide/',
            '/svicloud-box-authenticity-guide/',
        ];
        return apply_filters('svic_agent_friendly_sitemap_paths', $paths);
    }
}

if (!function_exists('svic_agent_friendly_sitemap_lastmod')) {
    function svic_agent_friendly_sitemap_lastmod(): string {
        $marker = function_exists('svic_get_theme_deploy_marker') ? svic_get_theme_deploy_marker() : '';
        $timestamp = is_numeric($marker) ? (int) $marker : 0;
        if ($timestamp <= 0) {
            $theme_dir = function_exists('get_template_directory') ? get_template_directory() : __DIR__;
            $mtime = is_string($theme_dir) && $theme_dir !== '' ? @filemtime($theme_dir) : false;
            $timestamp = is_int($mtime) && $mtime > 0 ? $mtime : time();
        }
        return gmdate('c', $timestamp);
    }
}

if (!function_exists('svic_output_agent_friendly_sitemap')) {
    function svic_output_agent_friendly_sitemap(): void {
        $path = parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
        $path = is_string($path) ? $path : '';
        if ($path !== '/agent-friendly-sitemap.xml') {
            return;
        }

        status_header(200);
        header('Content-Type: application/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        $lastmod = svic_agent_friendly_sitemap_lastmod();
        foreach (svic_agent_friendly_sitemap_paths() as $url_path) {
            echo "  <url>\n";
            echo '    <loc>' . esc_url(home_url($url_path)) . "</loc>\n";
            echo '    <lastmod>' . esc_html($lastmod) . "</lastmod>\n";
            echo "  </url>\n";
        }
        echo '</urlset>' . "\n";
        exit;
    }
}
add_action('template_redirect', 'svic_output_agent_friendly_sitemap', -110);

add_filter('robots_txt', function ($output, $public) {
    $line = 'Sitemap: ' . esc_url_raw(home_url('/agent-friendly-sitemap.xml'));
    if (strpos((string) $output, $line) === false) {
        $output = rtrim((string) $output) . "\n" . $line . "\n";
    }
    return $output;
}, 30, 2);

add_filter('rank_math/sitemap/index', function ($xml) {
    $entry = '<sitemap><loc>' . esc_url(home_url('/agent-friendly-sitemap.xml')) . '</loc><lastmod>' . esc_html(svic_agent_friendly_sitemap_lastmod()) . '</lastmod></sitemap>';
    if (is_string($xml) && strpos($xml, 'agent-friendly-sitemap.xml') === false) {
        return str_replace('</sitemapindex>', $entry . '</sitemapindex>', $xml);
    }
    return $xml;
});
