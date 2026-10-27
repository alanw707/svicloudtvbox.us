<?php
/** Public resource retirement. Originals remain in private CMS/revision backups. */
if (!defined('ABSPATH')) { exit; }

function svic_retired_public_path(string $uri): bool {
    $path = trim(rawurldecode((string) parse_url($uri, PHP_URL_PATH)), '/');
    $path = preg_replace('#^(?:en|zh|zh-cn|zh-tw)/#i', '', $path);
    $retired = include __DIR__ . '/retired-public-resources.php';
    return in_array($path, array_map('rawurldecode', $retired), true);
}

function svic_render_retired_public_resource(): void {
    if (is_admin()) { return; }
    $uri = (string) ($_SERVER['REQUEST_URI'] ?? '');
    $retired_ids = [1307, 1181, 1154, 1153, 879, 864, 830, 294, 274, 1338, 109];
    $query_id = absint($_GET['p'] ?? $_GET['page_id'] ?? 0);
    $topic_path = trim(rawurldecode((string) parse_url($uri, PHP_URL_PATH)), '/');
    $topic_path = preg_replace('#^(?:en|zh|zh-cn|zh-tw)/#i', '', $topic_path);
    $retired_topic = $topic_path === 'guides-resources' && isset($_GET['topic']) && $_GET['topic'] === 'channel-guide';
    if (!svic_retired_public_path($uri) && !in_array($query_id, $retired_ids, true) && !$retired_topic) { return; }
    status_header(410);
    nocache_headers();
    header('X-Robots-Tag: noindex, nofollow');
    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Resource unavailable</title><main><h1>This resource is no longer available.</h1><p><a href="' . esc_url(home_url('/guides-setup/')) . '">Hardware connection guide</a> · <a href="' . esc_url(home_url('/support/')) . '">Contact support</a></p></main></html>';
    exit;
}
// Earlier than virtual routes, canonical redirects and the theme's soft-404 handler.
add_action('parse_request', 'svic_render_retired_public_resource', -1000010);
add_action('template_redirect', 'svic_render_retired_public_resource', -1000010);
add_filter('svic_agent_friendly_sitemap_paths', static function ($paths) {
    return array_values(array_filter($paths, static function ($path) { return !svic_retired_public_path($path); }));
});
add_filter('wp_nav_menu_objects', static function ($items) {
    return array_values(array_filter($items, static function ($item) { return !svic_retired_public_path((string) $item->url); }));
});

function svic_public_hardware_static_meta(array $registry): array {
    unset($registry['guides-apps']);
    $descriptions = [
        'en' => 'SVICLOUD hardware connections, HDMI, Wi-Fi, remote pairing and support resources.',
        'zh' => 'SVICLOUD 硬體連接、HDMI、Wi-Fi、遙控器配對與客服資源。',
        'zh-cn' => 'SVICLOUD 硬件连接、HDMI、Wi-Fi、遥控器配对与客服资源。',
    ];
    foreach (['faq', 'guides', 'guides-setup', 'guides-resources', 'guides-after-setup', 'guides-troubleshooting', 'blog'] as $slug) {
        if (isset($registry[$slug])) { $registry[$slug]['description'] = $descriptions; }
    }
    return $registry;
}

function svic_public_hardware_promo(array $content, string $locale): array {
    $copy = [
        'en' => ['SVICLOUD 15P hardware, specs and price', 'Compare Android 14, Wi-Fi 6, Bluetooth 5.4, 4GB RAM, 64GB storage and device connections.', 'Compare the published hardware specifications and choose the model that fits your TV, network and accessory needs.'],
        'zh_tw' => ['小雲盒子 15P 硬體、規格與價格', '比較 Android 14、Wi-Fi 6、藍牙 5.4、4GB 記憶體、64GB 儲存空間與設備介面。', '請依已公布的硬體規格，以及電視、網路與配件需求選擇機型。'],
        'zh_cn' => ['小云盒子 15P 硬件、规格与价格', '比较 Android 14、Wi-Fi 6、蓝牙 5.4、4GB 内存、64GB 存储空间与设备接口。', '请按已公布的硬件规格，以及电视、网络与配件需求选择型号。'],
    ][$locale] ?? null;
    if ($copy === null) { return $content; }
    $content['title'] = $copy[0];
    $content['features_link'] = $copy[0];
    $content['lead'] = $copy[1];
    $content['meta_description'] = $copy[1];
    $content['feature_lead'] = $copy[2];
    $content['compare_copy'] = $copy[2];
    unset($content['tertiary_cta'], $content['app_title'], $content['app_copy']);
    unset($content['features'][3], $content['compare_rows'][3], $content['faqs'][1], $content['faqs'][3]);
    foreach (['features', 'compare_rows', 'faqs'] as $key) { $content[$key] = array_values($content[$key]); }
    return $content;
}
