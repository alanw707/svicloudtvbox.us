<?php
/** Virtual promotional route for the SVICLOUD 15P launch page. */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('svic_15p_promo_slug')) {
    function svic_15p_promo_slug(): string {
        return 'svicloud-15p-features';
    }
}

if (!function_exists('svic_is_15p_promo_request')) {
    function svic_is_15p_promo_request(): bool {
        return function_exists('svic_current_virtual_route_slug')
            && svic_current_virtual_route_slug() === svic_15p_promo_slug();
    }
}

if (!function_exists('svic_15p_promo_url')) {
    function svic_15p_promo_url(): string {
        return function_exists('svic_url_with_lang')
            ? svic_url_with_lang(home_url('/' . svic_15p_promo_slug() . '/'))
            : home_url('/' . svic_15p_promo_slug() . '/');
    }
}

if (!function_exists('svic_15p_promo_locale_key')) {
    function svic_15p_promo_locale_key(): string {
        $locale = function_exists('svic_current_locale') ? strtolower((string) svic_current_locale()) : strtolower((string) get_locale());
        if ($locale === 'zh_cn' || $locale === 'zh-cn') {
            return 'zh_cn';
        }
        if (strpos($locale, 'zh') === 0) {
            return 'zh_tw';
        }
        return 'en';
    }
}

if (!function_exists('svic_15p_promo_content')) {
    function svic_15p_promo_content(): array {
        $content = [
            'en' => [
                'meta_title' => 'SVICLOUD 15P Features, Specs & Yogurt TV Go | Android 14 TV Box',
                'meta_description' => 'Explore SVICLOUD 15P features, specs, Android 14, Wi-Fi 6, Bluetooth 5.4, Filmax local playback, app downloads, Yogurt TV Go guidance, and 10P+ comparison.',
                'badge' => 'SVICLOUD 15P guide',
                'title' => 'SVICLOUD 15P features, specs, and app-download upgrade',
                'lead' => 'A focused launch page for buyers comparing 15P against 10P+: Android 14, Wi-Fi 6, Bluetooth 5.4, air-mouse control, Filmax local playback, and support for downloading mobile apps including Yogurt TV Go guidance.',
                'primary_cta' => 'Pre-order 15P',
                'secondary_cta' => 'Compare models',
                'tertiary_cta' => 'Yogurt TV app guide',
                'shop_cta' => 'Shop lineup',
                'features_link' => 'Explore 15P features, specs, and Yogurt TV Go guidance',
                'hero_notes' => ['Available for pre-order now', 'Release window: 1 to 2 weeks', '$288 pre-order price', '$379 regular price', '10P+ remains the in-stock premium model'],
                'feature_title' => 'What 15P adds',
                'feature_lead' => 'The useful upgrade story is not one giant spec. It is a newer platform, stronger wireless baseline, and a better app/local-playback path for buyers who want the newest model.',
                'features' => [
                    ['Android 14 platform', 'Newer Android base than the current 10P+ generation, useful for buyers asking for the newest SVICLOUD software platform.'],
                    ['Amlogic S905Y5 Cortex-A55', 'Quad-core Cortex-A55 processor with 4 GB DDR3 memory and 64 GB eMMC storage.'],
                    ['Wi-Fi 6 and Bluetooth 5.4', 'Dual-band Wi-Fi 6, 2T2R wireless, and Bluetooth 5.4 for newer network and accessory support.'],
                    ['App-download focus', '15P is the model to highlight when shoppers ask about downloading mobile apps, Yogurt TV Go, or app flexibility. App availability can change, so setup guidance stays on the app guide.'],
                    ['Filmax local player', 'Public 15P materials describe built-in Filmax local playback for owned files, including NAS-style sources and subtitle/audio controls.'],
                    ['Air-mouse remote', 'Public 15P materials describe pointer-style remote control, a practical difference for app navigation and search.'],
                ],
                'compare_title' => '15P vs 10P+: the clean buying angle',
                'compare_copy' => 'Choose 10P+ if you want the proven in-stock premium model today. Choose 15P if you want the newest SVICLOUD box with Android 14, Wi-Fi 6, Bluetooth 5.4, and a stronger app-download path.',
                'compare_rows' => [
                    ['Platform', 'Android 14', 'Established 10P+ platform'],
                    ['Wireless', 'Wi-Fi 6, Bluetooth 5.4', 'In-stock premium family setup'],
                    ['Storage', '4 GB DDR3 / 64 GB eMMC', 'Strong current premium model'],
                    ['App story', 'Mobile app downloads and Yogurt TV Go guidance', 'Yogurt TV, Kids, karaoke, and voice remote use'],
                ],
                'spec_title' => 'Core 15P specs',
                'specs' => [
                    ['OS', 'Android 14'],
                    ['CPU', 'Amlogic S905Y5 quad-core ARM Cortex-A55'],
                    ['Memory / storage', '4 GB DDR3 / 64 GB eMMC'],
                    ['Wireless', 'Dual-band Wi-Fi 6, Bluetooth 5.4'],
                    ['Video', 'AV1, VP9, H.265/HEVC, H.264, HDR10+, HDR10, HLG'],
                    ['Ports', 'HDMI 2.1, RJ45 Ethernet, optical audio, USB, Type-C power'],
                    ['In the box', 'Gift box, AC adapter, HDMI cable, Bluetooth voice remote, user manual'],
                ],
                'app_title' => 'Yogurt TV Go and app-download guidance',
                'app_copy' => 'The safe claim is app flexibility: 15P is the model to promote when buyers ask about downloading mobile apps or Yogurt TV Go. For setup, app search terms, Cherry TV password questions, or regional content sections, route shoppers to the app guide and keep the answer support-first.',
                'faq_title' => '15P launch questions',
                'faqs' => [
                    ['Is SVICLOUD 15P available now?', 'It is available for pre-order at $288, with a public release window of 1 to 2 weeks.'],
                    ['What is the biggest practical difference?', '15P is the newer model for shoppers who care about mobile app downloads, Yogurt TV Go guidance, Android 14, Wi-Fi 6, and Bluetooth 5.4.'],
                    ['Should I choose 15P or 10P+?', 'Choose 15P if you want the newest release and can pre-order. Choose 10P+ if you want the proven premium model that is already in stock.'],
                    ['Can you promise specific Yogurt TV content?', 'No. App menus and availability can change. The page should route setup and content-section questions to the Yogurt TV app guide and support.'],
                ],
            ],
            'zh_tw' => [
                'meta_title' => '小雲 15P 功能、規格與 Yogurt TV Go｜Android 14 電視盒',
                'meta_description' => '查看小雲 15P 功能、規格、Android 14、Wi-Fi 6、藍牙 5.4、Filmax 本機播放、App 下載、Yogurt TV Go 指引與 10P+ 比較。',
                'badge' => '小雲 15P 指南',
                'title' => '小雲 15P 功能、規格與 App 下載升級',
                'lead' => '這是 15P 上市期的重點頁：Android 14、Wi-Fi 6、藍牙 5.4、飛鼠操作、Filmax 本機播放，以及支援下載手機 App，包括 Yogurt TV Go 相關指引。',
                'primary_cta' => '預購 15P',
                'secondary_cta' => '比較機型',
                'tertiary_cta' => 'Yogurt TV App 指南',
                'shop_cta' => '查看產品系列',
                'features_link' => '查看 15P 功能、規格與 Yogurt TV Go 指引',
                'hero_notes' => ['接受預購', '上市時程：1 至 2 週', '預購價 US$288', '原價 US$379', '10P+ 仍是現貨高階主力'],
                'feature_title' => '15P 新增什麼',
                'feature_lead' => '15P 的銷售重點不是單一規格，而是更新的平台、更新的無線規格，以及更適合 App 下載與本機播放的使用情境。',
                'features' => [
                    ['Android 14 平台', '比目前 10P+ 世代更新的 Android 平台，適合詢問最新小雲盒子的買家。'],
                    ['Amlogic S905Y5 Cortex-A55', '四核心 Cortex-A55 處理器，搭配 4 GB DDR3 記憶體與 64 GB eMMC 儲存空間。'],
                    ['Wi-Fi 6 與藍牙 5.4', '雙頻 Wi-Fi 6、2T2R 無線與 Bluetooth 5.4。'],
                    ['App 下載重點', '當買家詢問手機 App 下載、Yogurt TV Go 或 App 彈性時，15P 是應該被突出的機型。App 可用性可能變動，安裝說明仍以 App 指南為準。'],
                    ['Filmax 本機播放器', '公開 15P 資料提到內建 Filmax 本機播放，可用於自有影片檔案、NAS 類來源、字幕與音軌控制。'],
                    ['飛鼠遙控器', '公開 15P 資料提到指標式飛鼠操作，對 App 導航與搜尋更直覺。'],
                ],
                'compare_title' => '15P vs 10P+：最清楚的購買角度',
                'compare_copy' => '如果想要現在就能出貨、成熟穩定的高階機型，選 10P+。如果想要最新小雲盒子、Android 14、Wi-Fi 6、藍牙 5.4 與更強的 App 下載使用情境，選 15P。',
                'compare_rows' => [
                    ['平台', 'Android 14', '成熟的 10P+ 平台'],
                    ['無線', 'Wi-Fi 6、藍牙 5.4', '現貨高階家庭使用'],
                    ['記憶體 / 儲存', '4 GB DDR3 / 64 GB eMMC', '目前高階現貨機型'],
                    ['App 角度', '手機 App 下載與 Yogurt TV Go 指引', 'Yogurt TV、Kids、K 歌與語音遙控'],
                ],
                'spec_title' => '15P 核心規格',
                'specs' => [
                    ['系統', 'Android 14'],
                    ['處理器', 'Amlogic S905Y5 四核心 ARM Cortex-A55'],
                    ['記憶體 / 儲存', '4 GB DDR3 / 64 GB eMMC'],
                    ['無線', '雙頻 Wi-Fi 6、Bluetooth 5.4'],
                    ['影像', 'AV1、VP9、H.265/HEVC、H.264、HDR10+、HDR10、HLG'],
                    ['連接埠', 'HDMI 2.1、RJ45、光纖音訊、USB、Type-C 電源'],
                    ['盒內配件', '禮盒、AC 變壓器、HDMI 線、藍牙語音遙控器、使用手冊'],
                ],
                'app_title' => 'Yogurt TV Go 與 App 下載指引',
                'app_copy' => '安全的說法是 App 彈性：當買家問手機 App 下載或 Yogurt TV Go 時，15P 是要主推的機型。安裝、搜尋詞、Cherry TV 密碼或內容分類問題，仍導向 App 指南與客服。',
                'faq_title' => '15P 上市常見問題',
                'faqs' => [
                    ['小雲 15P 現在能買嗎？', '可以預購，預購價 US$288，上市時程為 1 至 2 週。'],
                    ['最大的實用差異是什麼？', '15P 是較新的機型，適合重視手機 App 下載、Yogurt TV Go 指引、Android 14、Wi-Fi 6 與藍牙 5.4 的買家。'],
                    ['應該選 15P 還是 10P+？', '想要最新上市機型、可以接受預購，選 15P。想要已經現貨供應、成熟穩定的高階機型，選 10P+。'],
                    ['可以保證 Yogurt TV 內容嗎？', '不可以。App 選單與可用性可能變動，內容分類與安裝問題應導向 Yogurt TV App 指南與客服。'],
                ],
            ],
            'zh_cn' => [
                'meta_title' => '小云 15P 功能、规格与 Yogurt TV Go｜Android 14 电视盒',
                'meta_description' => '查看小云 15P 功能、规格、Android 14、Wi-Fi 6、蓝牙 5.4、Filmax 本地播放、App 下载、Yogurt TV Go 指引与 10P+ 比较。',
                'badge' => '小云 15P 指南',
                'title' => '小云 15P 功能、规格与 App 下载升级',
                'lead' => '这是 15P 上市期的重点页：Android 14、Wi-Fi 6、蓝牙 5.4、飞鼠操作、Filmax 本地播放，以及支持下载手机 App，包括 Yogurt TV Go 相关指引。',
                'primary_cta' => '预订 15P',
                'secondary_cta' => '比较机型',
                'tertiary_cta' => 'Yogurt TV App 指南',
                'shop_cta' => '查看产品系列',
                'features_link' => '查看 15P 功能、规格与 Yogurt TV Go 指引',
                'hero_notes' => ['接受预订', '上市时间：1 至 2 周', '预订价 US$288', '原价 US$379', '10P+ 仍是现货高端主力'],
                'feature_title' => '15P 新增什么',
                'feature_lead' => '15P 的销售重点不是单一规格，而是更新的平台、更新的无线规格，以及更适合 App 下载与本地播放的使用场景。',
                'features' => [
                    ['Android 14 平台', '比目前 10P+ 世代更新的 Android 平台，适合询问最新小云盒子的买家。'],
                    ['Amlogic S905Y5 Cortex-A55', '四核心 Cortex-A55 处理器，搭配 4 GB DDR3 内存与 64 GB eMMC 存储空间。'],
                    ['Wi-Fi 6 与蓝牙 5.4', '双频 Wi-Fi 6、2T2R 无线与 Bluetooth 5.4。'],
                    ['App 下载重点', '当买家询问手机 App 下载、Yogurt TV Go 或 App 灵活性时，15P 是应该被突出的机型。App 可用性可能变动，安装说明仍以 App 指南为准。'],
                    ['Filmax 本地播放器', '公开 15P 资料提到内置 Filmax 本地播放，可用于自有视频文件、NAS 类来源、字幕与音轨控制。'],
                    ['飞鼠遥控器', '公开 15P 资料提到指针式飞鼠操作，对 App 导航与搜索更直观。'],
                ],
                'compare_title' => '15P vs 10P+：最清楚的购买角度',
                'compare_copy' => '如果想要现在就能发货、成熟稳定的高端机型，选 10P+。如果想要最新小云盒子、Android 14、Wi-Fi 6、蓝牙 5.4 与更强的 App 下载使用场景，选 15P。',
                'compare_rows' => [
                    ['平台', 'Android 14', '成熟的 10P+ 平台'],
                    ['无线', 'Wi-Fi 6、蓝牙 5.4', '现货高端家庭使用'],
                    ['内存 / 存储', '4 GB DDR3 / 64 GB eMMC', '目前高端现货机型'],
                    ['App 角度', '手机 App 下载与 Yogurt TV Go 指引', 'Yogurt TV、Kids、K 歌与语音遥控'],
                ],
                'spec_title' => '15P 核心规格',
                'specs' => [
                    ['系统', 'Android 14'],
                    ['处理器', 'Amlogic S905Y5 四核心 ARM Cortex-A55'],
                    ['内存 / 存储', '4 GB DDR3 / 64 GB eMMC'],
                    ['无线', '双频 Wi-Fi 6、Bluetooth 5.4'],
                    ['视频', 'AV1、VP9、H.265/HEVC、H.264、HDR10+、HDR10、HLG'],
                    ['接口', 'HDMI 2.1、RJ45、光纤音频、USB、Type-C 电源'],
                    ['盒内配件', '礼盒、AC 适配器、HDMI 线、蓝牙语音遥控器、用户手册'],
                ],
                'app_title' => 'Yogurt TV Go 与 App 下载指引',
                'app_copy' => '安全的说法是 App 灵活性：当买家问手机 App 下载或 Yogurt TV Go 时，15P 是要主推的机型。安装、搜索词、Cherry TV 密码或内容分类问题，仍导向 App 指南与客服。',
                'faq_title' => '15P 上市常见问题',
                'faqs' => [
                    ['小云 15P 现在能买吗？', '可以预订，预订价 US$288，上市时间为 1 至 2 周。'],
                    ['最大的实用差异是什么？', '15P 是更新的机型，适合重视手机 App 下载、Yogurt TV Go 指引、Android 14、Wi-Fi 6 与蓝牙 5.4 的买家。'],
                    ['应该选 15P 还是 10P+？', '想要最新上市机型、可以接受预订，选 15P。想要已经现货供应、成熟稳定的高端机型，选 10P+。'],
                    ['可以保证 Yogurt TV 内容吗？', '不可以。App 菜单与可用性可能变动，内容分类与安装问题应导向 Yogurt TV App 指南与客服。'],
                ],
            ],
        ];

        $key = svic_15p_promo_locale_key();
        return $content[$key] ?? $content['en'];
    }
}

if (!function_exists('svic_15p_promo_meta')) {
    function svic_15p_promo_meta(): array {
        $content = svic_15p_promo_content();
        $image = function_exists('svic_get_theme_image_meta')
            ? svic_get_theme_image_meta('/assets/images/products/svicloud-15p-marketing-v4-watermarked.webp')
            : [];
        return [
            'title' => $content['meta_title'],
            'description' => $content['meta_description'],
            'image' => $image,
        ];
    }
}

if (!function_exists('svic_render_15p_promo_route')) {
    function svic_render_15p_promo_route(): void {
        if (is_admin() || !svic_is_15p_promo_request()) {
            return;
        }

        $existing_page = get_page_by_path(svic_15p_promo_slug(), OBJECT, 'page');
        if ($existing_page instanceof WP_Post && $existing_page->post_status === 'publish') {
            return;
        }

        $meta = svic_15p_promo_meta();
        if (function_exists('svic_mark_virtual_page_request')) {
            svic_mark_virtual_page_request($meta['title'], 'guides');
        } else {
            status_header(200);
        }

        include get_template_directory() . '/page-svicloud-15p-features.php';
        exit;
    }
}
add_action('parse_request', 'svic_render_15p_promo_route', 0);
add_action('template_redirect', 'svic_render_15p_promo_route', -1000000);

add_filter('document_title_parts', function (array $parts): array {
    if (svic_is_15p_promo_request()) {
        $parts['title'] = svic_15p_promo_meta()['title'];
    }
    return $parts;
}, 60);

add_filter('body_class', function (array $classes): array {
    if (svic_is_15p_promo_request()) {
        $classes[] = 'svic-15p-promo-page';
    }
    return $classes;
}, 60);

if (defined('RANK_MATH_VERSION')) {
    add_filter('rank_math/frontend/title', function ($title) {
        return svic_is_15p_promo_request() ? svic_15p_promo_meta()['title'] : $title;
    }, 40);
    add_filter('rank_math/frontend/description', function ($description) {
        return svic_is_15p_promo_request() ? svic_15p_promo_meta()['description'] : $description;
    }, 40);
    add_filter('rank_math/frontend/snippet_description', function ($description) {
        return svic_is_15p_promo_request() ? svic_15p_promo_meta()['description'] : $description;
    }, 40);
}
