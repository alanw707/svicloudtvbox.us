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

if (!defined('SVIC_GOOGLE_CUSTOMER_REVIEWS_MERCHANT_ID')) {
    define('SVIC_GOOGLE_CUSTOMER_REVIEWS_MERCHANT_ID', 5317978135);
}

const SVIC_LITESPEED_PURGE_MARK = 'svic-litespeed-sitemap-20251113a';

add_action('init', function () {
    if (get_option('svic_litespeed_last_purge') === SVIC_LITESPEED_PURGE_MARK) {
        return;
    }

    if (!class_exists('\LiteSpeed\Purge')) {
        return;
    }

    update_option('svic_litespeed_last_purge', SVIC_LITESPEED_PURGE_MARK, false);
    \LiteSpeed\Purge::purge_all('SVIC hero chunk hotfix');
}, 1);

require_once get_template_directory() . '/inc/class-svic-translator.php';
require_once get_template_directory() . '/inc/class-svic-markdown.php';

require_once get_template_directory() . '/inc/class-svic-locale-resolver.php';
require_once get_template_directory() . '/inc/guides-data.php';
require_once get_template_directory() . '/inc/theme-maintenance.php';
require_once get_template_directory() . '/inc/helpers-svic.php';
require_once get_template_directory() . '/inc/class-svic-zh-sitemap.php';

SVIC_Locale_Resolver::bootstrap();

if (!function_exists('svic_get_localized_canonical_url')) {
    function svic_get_localized_canonical_url(): ?string
    {
        if (!function_exists('svic_current_base_url') || !function_exists('svic_url_with_lang')) {
            return null;
        }

        $base = svic_current_base_url();
        if (!is_string($base) || $base === '') {
            return null;
        }

        $localized = svic_url_with_lang($base);
        $parts     = wp_parse_url($localized);
        if (!is_array($parts)) {
            return null;
        }

        $path = isset($parts['path']) && is_string($parts['path']) ? $parts['path'] : '/';
        $canonical = home_url($path);

        if ($canonical !== home_url('/') && substr($canonical, -1) !== '/') {
            $canonical .= '/';
        }

        return $canonical;
    }
}

if (!function_exists('svic_static_page_meta_registry')) {
    function svic_static_page_meta_registry(): array
    {
        static $registry = null;
        if ($registry !== null) {
            return $registry;
        }

        $default_image = '/assets/images/svicloud-hero-product.webp';

        $registry = [
            'support' => [
                'title'       => [
                    'en' => 'SVICLOUD Support & Concierge | Bilingual Help in the USA',
                    'zh' => '小雲電視盒客服｜中英雙語禮賓支援',
                ],
                'description' => [
                    'en' => 'Chat with the Las Vegas concierge team for setup help, warranty claims, returns, and upgrades in English or 繁體中文.',
                    'zh' => '拉斯維加斯禮賓客服提供中文與英文安裝協助、保固申請、退換貨與升級諮詢。',
                ],
                'image'       => '/assets/images/hero-voice-assistant.webp',
                'image_alt'   => [
                    'en' => 'SVICLOUD concierge helping customers in Las Vegas',
                    'zh' => '小雲電視盒禮賓客服提供拉斯維加斯支援',
                ],
            ],
            'contact' => [
                'title'       => [
                    'en' => 'Talk to SVICLOUD Concierge | SMS, WhatsApp, Email Support',
                    'zh' => '聯絡小雲禮賓客服｜SMS、WhatsApp、Email 即時支援',
                ],
                'description' => [
                    'en' => 'Reach our Nevada-based concierge for orders, setup walkthroughs, Wi-Fi tuning, and app updates with a one-business-day SLA.',
                    'zh' => '美國內華達客服一個工作天回覆訂單、安裝、網路調校與應用更新需求。',
                ],
                'image'       => '/assets/images/hero-voice-assistant.webp',
                'image_alt'   => [
                    'en' => 'Concierge specialist answering SVICLOUD questions',
                    'zh' => '小雲禮賓客服回覆客戶問題',
                ],
            ],
            'faq' => [
                'title'       => [
                    'en' => 'SVICLOUD FAQ | Warranty, Setup, Shipping & Payments',
                    'zh' => '小雲電視盒常見問題｜保固、安裝、物流與付款',
                ],
                'description' => [
                    'en' => 'Browse bilingual answers covering SVICLOUD warranty rules, activation tips, streaming apps, and delivery timelines.',
                    'zh' => '查詢小雲電視盒保固規範、安裝秘訣、串流應用與出貨時程的中英解答。',
                ],
                'image'       => $default_image,
                'image_alt'   => [
                    'en' => 'SVICLOUD FAQ illustration',
                    'zh' => '小雲電視盒常見問題示意圖',
                ],
            ],
            'guides' => [
                'title'       => [
                    'en' => 'SVICLOUD Guides Hub | Setup, Apps, Troubleshooting',
                    'zh' => '小雲使用指南總覽｜安裝、應用、疑難排解',
                ],
                'description' => [
                    'en' => 'Find curated SVICLOUD setup checklists, app tutorials, after-setup tips, and bilingual troubleshooting workflows.',
                    'zh' => '收錄小雲安裝檢查表、應用教學、安裝後建議與雙語故障排除流程。',
                ],
                'image'       => '/assets/images/svicloud-10p-plus-lifestyle-1.webp',
                'image_alt'   => [
                    'en' => 'SVICLOUD 10P+ setup guide illustration',
                    'zh' => '小雲 10P+ 安裝指南示意圖',
                ],
            ],
            'guides-setup' => [
                'title'       => [
                    'en' => 'SVICLOUD Setup Guide | Wi-Fi, Apps, Language Tips',
                    'zh' => '小雲安裝指南｜網路、應用與語言設定',
                ],
                'description' => [
                    'en' => 'Follow step-by-step onboarding for SVICLOUD 10P+/10S covering network placement, language, and core app installs.',
                    'zh' => '逐步完成小雲 10P+/10S 安裝：網路擺位、語言設定與核心應用安裝一次到位。',
                ],
                'image'       => '/assets/images/svicloud-10p-plus.webp',
                'image_alt'   => [
                    'en' => 'SVICLOUD 10P+ setup hero image',
                    'zh' => '小雲 10P+ 安裝示意圖',
                ],
            ],
            'guides-after-setup' => [
                'title'       => [
                    'en' => 'After Setup Guide | Profiles, Parental Controls & Tips',
                    'zh' => '安裝後指南｜多用戶、家長監護與調校技巧',
                ],
                'description' => [
                    'en' => 'Optimize SVICLOUD after day one with parental controls, profile tips, smart home integrations, and bilingual shortcuts.',
                    'zh' => '完成安裝後，利用家長監護、個人化設定與智慧家庭整合讓小雲體驗更順。',
                ],
                'image'       => $default_image,
                'image_alt'   => [
                    'en' => 'Family using SVICLOUD after setup',
                    'zh' => '家庭使用小雲的場景',
                ],
            ],
            'guides-support' => [
                'title'       => [
                    'en' => 'Concierge Support Guide | Warranty & Escalation Paths',
                    'zh' => '禮賓客服指南｜保固與升級流程',
                ],
                'description' => [
                    'en' => 'Understand concierge tiers, warranty checkpoints, and how to escalate SVICLOUD requests in English or Chinese.',
                    'zh' => '了解禮賓等級、保固檢核與中英升級流程，確保小雲支援快速到位。',
                ],
                'image'       => '/assets/images/certification-authorized-dealer.webp',
                'image_alt'   => [
                    'en' => 'Authorized SVICLOUD concierge badge',
                    'zh' => '小雲禮賓客服認證徽章',
                ],
            ],
            'guides-resources' => [
                'title'       => [
                    'en' => 'Resources Hub | Warranty Docs, Compliance & Playbooks',
                    'zh' => '資源中心｜保固文件、合規與行銷手冊',
                ],
                'description' => [
                    'en' => 'Download SVICLOUD warranty forms, compliance checklists, and marketing playbooks to support US households.',
                    'zh' => '下載小雲保固文件、合規清單與行銷手冊，掌握美國市場需求。',
                ],
                'image'       => $default_image,
                'image_alt'   => [
                    'en' => 'SVICLOUD resource library illustration',
                    'zh' => '小雲資源中心示意圖',
                ],
            ],
            'guides-troubleshooting' => [
                'title'       => [
                    'en' => 'Troubleshooting Guide | Remote, Streaming & Network Fixes',
                    'zh' => '疑難排解指南｜遙控、串流與網路修復',
                ],
                'description' => [
                    'en' => 'Resolve SVICLOUD buffering, remote pairing, and streaming glitches with concierge-approved checklists.',
                    'zh' => '依照禮賓核可清單排除小雲緩衝、遙控配對與串流異常問題。',
                ],
                'image'       => '/assets/images/svicloud-10s-lifestyle-1.webp',
                'image_alt'   => [
                    'en' => 'SVICLOUD troubleshooting illustration',
                    'zh' => '小雲疑難排解示意圖',
                ],
            ],
            'guides-apps' => [
                'title'       => [
                    'en' => 'Apps & Channels Guide | Yogurt TV, Cherry TV, Kids Mode',
                    'zh' => '應用與頻道指南｜Yogurt TV、Cherry TV 與兒童模式',
                ],
                'description' => [
                    'en' => 'Install and organize SVICLOUD live TV, on-demand, karaoke, and kid-friendly apps with bilingual tips.',
                    'zh' => '以雙語教學安裝與整理小雲直播、隨選、卡拉OK與兒童應用。',
                ],
                'image'       => '/assets/images/svicloud-10s-lifestyle-3.webp',
                'image_alt'   => [
                    'en' => 'SVICLOUD apps and channels collage',
                    'zh' => '小雲應用與頻道拼貼圖',
                ],
            ],
            'return-policy' => [
                'title'       => [
                    'en' => 'SVICLOUD Return Policy | 30-Day U.S. Exchanges & Refunds',
                    'zh' => '小雲退貨政策｜30 天美國換貨與退款',
                ],
                'description' => [
                    'en' => 'Review eligibility, timelines, and shipping steps for SVICLOUD returns handled from the Nevada warehouse.',
                    'zh' => '了解小雲美國倉庫退貨資格、時程與物流步驟。',
                ],
                'image'       => '/assets/images/certification-authorized-dealer.webp',
                'image_alt'   => [
                    'en' => 'Return and exchange timeline graphic',
                    'zh' => '退換貨流程圖示',
                ],
            ],
            'legal-disclaimer' => [
                'title'       => [
                    'en' => 'Legal Disclaimer | SVICLOUD TV BOX US Policies',
                    'zh' => '法律聲明｜SVICLOUD TV BOX US 政策',
                ],
                'description' => [
                    'en' => 'Review SVICLOUD TV BOX US legal terms covering service boundaries, intellectual property, and compliance notes.',
                    'zh' => '檢視 SVICLOUD TV BOX US 服務範圍、智慧財產與合規聲明。',
                ],
                'image'       => $default_image,
                'image_alt'   => [
                    'en' => 'SVICLOUD legal policy illustration',
                    'zh' => '小雲法律政策示意圖',
                ],
            ],
            'order-tracking' => [
                'title'       => [
                    'en' => 'SVICLOUD Order Tracking | Live Status & Support',
                    'zh' => '小雲訂單查詢｜即時狀態與客服支援',
                ],
                'description' => [
                    'en' => 'Enter your order number to see SVICLOUD shipping progress, signatures, and concierge follow-up options.',
                    'zh' => '輸入訂單編號即可查看小雲出貨進度、簽收資訊與禮賓客服選項。',
                ],
                'image'       => '/assets/images/svicloud-10p-plus-lifestyle-2.webp',
                'image_alt'   => [
                    'en' => 'Order tracking illustration',
                    'zh' => '訂單追蹤示意圖',
                ],
            ],
            'blog' => [
                'title'       => [
                    'en' => 'SVICLOUD Blog | 小雲電視盒 美國 Buying Guides & Updates',
                    'zh' => '小雲部落格｜美國購買指南與最新更新',
                ],
                'description' => [
                    'en' => 'Read bilingual buying checklists, launch news, and troubleshooting tips for SVICLOUD customers in North America.',
                    'zh' => '閱讀面向北美小雲客戶的雙語購買清單、產品更新與疑難排解建議。',
                ],
                'image'       => '/assets/images/hero-voice-assistant.webp',
                'image_alt'   => [
                    'en' => 'SVICLOUD blog hero image',
                    'zh' => '小雲部落格主圖',
                ],
            ],
            'about' => [
                'title'       => [
                    'en' => 'About SVICLOUD TV BOX US | Authorized 小雲電視盒 Partner',
                    'zh' => '關於 SVICLOUD TV BOX US｜小雲電視盒美國授權夥伴',
                ],
                'description' => [
                    'en' => 'Discover our Las Vegas fulfillment center, bilingual concierge, and mission to support 小雲電視盒 households in the US.',
                    'zh' => '了解我們的拉斯維加斯倉庫、中英禮賓客服與服務美國小雲家庭的使命。',
                ],
                'image'       => '/assets/images/svicloud-10s-lifestyle-2.webp',
                'image_alt'   => [
                    'en' => 'SVICLOUD team in Las Vegas warehouse',
                    'zh' => '小雲團隊於拉斯維加斯倉庫',
                ],
            ],
            'compare' => [
                'title'       => [
                    'en' => 'SVICLOUD 10P+ vs 10S Comparison | Specs, Warranty, Concierge',
                    'zh' => '小雲 10P+ vs 10S 比較｜規格、保固與禮賓服務',
                ],
                'description' => [
                    'en' => 'Break down CPUs, storage, voice control, and support perks to pick the right SVICLOUD box for your household.',
                    'zh' => '比較處理器、儲存、語音控制與支援服務，挑選最合適的小雲機型。',
                ],
                'image'       => '/assets/images/svicloud-10p-plus-lifestyle-3.webp',
                'image_alt'   => [
                    'en' => 'SVICLOUD 10P+ and 10S side-by-side',
                    'zh' => '小雲 10P+ 與 10S 並排圖',
                ],
            ],
            'shop' => [
                'title'       => [
                    'en' => 'Shop SVICLOUD 10P+ & 10S | Authorized U.S. Inventory',
                    'zh' => '選購小雲 10P+／10S｜美國授權現貨',
                ],
                'description' => [
                    'en' => 'Browse in-stock SVICLOUD bundles with 48-hour Nevada shipping, 1-year warranty, and bilingual concierge setup.',
                    'zh' => '瀏覽小雲現貨組合，內華達 48 小時出貨、附一年保固與中英禮賓安裝。',
                ],
                'image'       => '/assets/images/svicloud-10p-plus.webp',
                'image_alt'   => [
                    'en' => 'SVICLOUD 10P+ product photo',
                    'zh' => '小雲 10P+ 產品照',
                ],
            ],
            'cart' => [
                'title'       => [
                    'en' => 'SVICLOUD Cart | Review Items Before Checkout',
                    'zh' => '小雲購物車｜結帳前確認商品',
                ],
                'description' => [
                    'en' => 'Confirm SVICLOUD models, shipping protection, and concierge add-ons before entering checkout.',
                    'zh' => '結帳前確認小雲機型、出貨保障與禮賓加值項目。',
                ],
                'image'       => $default_image,
                'image_alt'   => [
                    'en' => 'Shopping cart illustration',
                    'zh' => '購物車示意圖',
                ],
            ],
            'checkout' => [
                'title'       => [
                    'en' => 'Secure Checkout | SVICLOUD 10P+ & 10S in the USA',
                    'zh' => '安全結帳｜小雲 10P+／10S 美國訂單',
                ],
                'description' => [
                    'en' => 'Complete your SVICLOUD order with encrypted payment, Nevada fulfillment, and bilingual concierge confirmation.',
                    'zh' => '使用加密付款完成小雲訂單，內華達出貨並附中英禮賓確認。',
                ],
                'image'       => $default_image,
                'image_alt'   => [
                    'en' => 'Secure checkout illustration',
                    'zh' => '安全結帳示意圖',
                ],
            ],
            'my-account' => [
                'title'       => [
                    'en' => 'My Account | Track SVICLOUD Orders & Concierge Tickets',
                    'zh' => '會員中心｜查詢小雲訂單與客服案件',
                ],
                'description' => [
                    'en' => 'Log in to download invoices, track warranty submissions, and manage concierge conversations.',
                    'zh' => '登入下載發票、追蹤保固案件並管理禮賓對話。',
                ],
                'image'       => $default_image,
                'image_alt'   => [
                    'en' => 'Account dashboard illustration',
                    'zh' => '會員中心儀表板示意',
                ],
            ],
        ];

        return $registry;
    }
}

if (!function_exists('svic_normalize_meta_slug')) {
    function svic_normalize_meta_slug(?string $slug): ?string
    {
        if (!is_string($slug) || $slug === '') {
            return null;
        }

        $slug = strtolower($slug);
        $decoded = rawurldecode($slug);

        $aliases = [
            '%e4%bd%bf%e7%94%a8%e6%8c%87%e5%8d%97' => 'guides',
            '%e6%a9%9f%e5%9e%8b%e6%af%94%e8%bc%83' => 'compare',
            '使用指南'                         => 'guides',
            '機型比較'                         => 'compare',
        ];

        if (isset($aliases[$slug])) {
            return $aliases[$slug];
        }

        if ($decoded !== $slug && isset($aliases[$decoded])) {
            return $aliases[$decoded];
        }

        return $slug;
    }
}

if (!function_exists('svic_resolve_static_page_meta')) {
    function svic_resolve_static_page_meta(?string $slug): ?array
    {
        $normalized = svic_normalize_meta_slug($slug);
        if (!$normalized) {
            return null;
        }

        $registry = svic_static_page_meta_registry();
        if (!isset($registry[$normalized])) {
            return null;
        }

        $entry = $registry[$normalized];
        $lang  = function_exists('svic_language_query_value') ? svic_language_query_value() : 'en';
        $lang  = $lang === 'zh' ? 'zh' : 'en';

        $title = $entry['title'][$lang] ?? $entry['title']['en'] ?? '';
        $description = $entry['description'][$lang] ?? $entry['description']['en'] ?? '';
        $image_alt = $entry['image_alt'][$lang] ?? ($entry['image_alt']['en'] ?? '');

        return [
            'title'       => $title,
            'description' => $description,
            'image'       => $entry['image'] ?? null,
            'image_alt'   => $image_alt,
        ];
    }
}

if (!function_exists('svic_get_static_page_meta_slug')) {
    function svic_get_static_page_meta_slug(): ?string
    {
        $post = get_queried_object();
        if ($post instanceof WP_Post && !empty($post->post_name)) {
            return $post->post_name;
        }

        if (function_exists('is_shop') && is_shop()) {
            return 'shop';
        }

        if (function_exists('is_cart') && is_cart()) {
            return 'cart';
        }
        if (function_exists('is_checkout') && is_checkout()) {
            return 'checkout';
        }
        if (function_exists('is_account_page') && is_account_page()) {
            return 'my-account';
        }

        return null;
    }
}

if (!function_exists('svic_should_output_static_page_meta')) {
    function svic_should_output_static_page_meta(): bool
    {
        if (is_admin() || (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION'))) {
            return false;
        }

        if (is_front_page()) {
            return false;
        }

        $is_static_page = is_page() || is_home();
        $is_shop_archive = function_exists('is_shop') && is_shop();

        if (!$is_static_page && !$is_shop_archive) {
            return false;
        }

        if (function_exists('is_page_template') && is_page_template('page-compare.php')) {
            return false;
        }

        return true;
    }
}

add_filter('document_title_parts', function ($parts) {
    if (!svic_should_output_static_page_meta()) {
        return $parts;
    }

    $slug = svic_get_static_page_meta_slug();
    $meta = svic_resolve_static_page_meta($slug);

    if ($meta && !empty($meta['title'])) {
        $parts['title'] = $meta['title'];
    }

    return $parts;
}, 40);

if (!function_exists('svic_output_static_page_meta')) {
    function svic_output_static_page_meta(): void
    {
        if (!svic_should_output_static_page_meta()) {
            return;
        }

        $slug = svic_get_static_page_meta_slug();
        $meta = svic_resolve_static_page_meta($slug);

        if (!$meta) {
            return;
        }

        $post_id = get_queried_object_id();
        $title = $meta['title'] ?: ($post_id ? wp_strip_all_tags(get_the_title($post_id)) : '');

        $description = $meta['description'];
        if ($description === '') {
            if ($post_id) {
                $description = wp_strip_all_tags(get_post_field('post_excerpt', $post_id));
            }
            if ($description === '' && $post_id) {
                $description = wp_trim_words(wp_strip_all_tags(get_post_field('post_content', $post_id)), 32, '…');
            }
        }

        if ($title === '' && $description === '') {
            return;
        }

        $canonical = svic_get_localized_canonical_url();
        if (!is_string($canonical) || $canonical === '') {
            $canonical = $post_id ? get_permalink($post_id) : home_url('/');
        }

        $image_meta = [];
        if (!empty($meta['image']) && function_exists('svic_get_theme_image_meta')) {
            $image_meta = svic_get_theme_image_meta($meta['image']);
            if (!empty($meta['image_alt'])) {
                $image_meta['alt'] = $meta['image_alt'];
            }
        }

        echo '<meta name="description" content="' . esc_attr($description) . "\" />\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

        $og_tags = [
            ['property' => 'og:type', 'content' => 'website'],
            ['property' => 'og:site_name', 'content' => get_bloginfo('name')],
            ['property' => 'og:title', 'content' => $title],
            ['property' => 'og:description', 'content' => $description],
            ['property' => 'og:url', 'content' => esc_url_raw($canonical)],
        ];

        if (!empty($image_meta['url'])) {
            $og_tags[] = ['property' => 'og:image', 'content' => $image_meta['url']];
            if (!empty($image_meta['width'])) {
                $og_tags[] = ['property' => 'og:image:width', 'content' => (string) $image_meta['width']];
            }
            if (!empty($image_meta['height'])) {
                $og_tags[] = ['property' => 'og:image:height', 'content' => (string) $image_meta['height']];
            }
            if (!empty($image_meta['alt'])) {
                $og_tags[] = ['property' => 'og:image:alt', 'content' => $image_meta['alt']];
            }
        }

        foreach ($og_tags as $tag) {
            echo '<meta property="' . esc_attr($tag['property']) . '" content="' . esc_attr($tag['content']) . "\" />\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        $twitter_tags = [
            ['name' => 'twitter:card', 'content' => 'summary_large_image'],
            ['name' => 'twitter:title', 'content' => $title],
            ['name' => 'twitter:description', 'content' => $description],
        ];

        if (!empty($image_meta['url'])) {
            $twitter_tags[] = ['name' => 'twitter:image', 'content' => $image_meta['url']];
        }

        foreach ($twitter_tags as $tag) {
            echo '<meta name="' . esc_attr($tag['name']) . '" content="' . esc_attr($tag['content']) . "\" />\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
    }
}

add_action('wp_head', 'svic_output_static_page_meta', 7);

if (!function_exists('svic_should_output_navigation_schema')) {
    function svic_should_output_navigation_schema(): bool
    {
        if (is_admin()) {
            return false;
        }

        if (!function_exists('is_front_page') || !function_exists('is_home')) {
            return false;
        }

        return is_front_page() || is_home();
    }
}

if (!function_exists('svic_fetch_primary_nav_menu_items')) {
    function svic_fetch_primary_nav_menu_items(): array
    {
        if (!function_exists('wp_get_nav_menu_items')) {
            return [];
        }

        $menu_items = [];
        $locations  = function_exists('get_nav_menu_locations') ? get_nav_menu_locations() : [];

        if (isset($locations['primary'])) {
            $menu_id = (int) $locations['primary'];
            if ($menu_id > 0) {
                $menu_items = wp_get_nav_menu_items($menu_id);
            }
        }

        if (!$menu_items && function_exists('has_nav_menu') && has_nav_menu('primary')) {
            $menus = wp_get_nav_menus();
            foreach ($menus as $menu) {
                if (!($menu instanceof WP_Term)) {
                    continue;
                }

                if (isset($locations['primary']) && (int) $menu->term_id === (int) $locations['primary']) {
                    continue;
                }

                if (stripos($menu->slug, 'primary') !== false || stripos($menu->name, 'primary') !== false) {
                    $menu_items = wp_get_nav_menu_items($menu->term_id);
                    break;
                }
            }
        }

        if (!$menu_items) {
            $menu_object = wp_get_nav_menu_object('primary');
            if ($menu_object instanceof WP_Term) {
                $menu_items = wp_get_nav_menu_items($menu_object->term_id);
            }
        }

        return is_array($menu_items) ? $menu_items : [];
    }
}

if (!function_exists('svic_fallback_navigation_schema_items')) {
    function svic_fallback_navigation_schema_items(): array
    {
        $defaults = [
            [
                'label_key' => 'header.nav.home',
                'fallback'  => 'Home',
                'url'       => home_url('/'),
            ],
            [
                'label_key' => 'header.nav.compare',
                'fallback'  => 'Compare',
                'url'       => home_url('/compare/'),
            ],
            [
                'label_key' => 'header.nav.faq',
                'fallback'  => 'FAQ',
                'url'       => home_url('/faq/'),
            ],
            [
                'label_key' => 'header.nav.ten_p',
                'fallback'  => 'SViCloud 10P+',
                'url'       => home_url('/product/svicloud-10p-plus/'),
            ],
            [
                'label_key' => 'header.nav.ten_s',
                'fallback'  => 'SViCloud 10S',
                'url'       => home_url('/product/svicloud-10s/'),
            ],
            [
                'label_key' => 'header.nav.concierge',
                'fallback'  => 'Contact',
                'url'       => home_url('/contact/'),
            ],
        ];

        $items = [];
        foreach ($defaults as $entry) {
            $label = function_exists('svic_translate') ? (string) svic_translate($entry['label_key']) : '';
            if ($label === '') {
                $label = $entry['fallback'];
            }

            $url = svic_url_with_lang($entry['url']);

            $items[] = [
                'name'        => wp_strip_all_tags($label),
                'url'         => esc_url_raw($url),
                'description' => '',
            ];
        }

        return $items;
    }
}

if (!function_exists('svic_resolve_navigation_schema_items')) {
    function svic_resolve_navigation_schema_items(): array
    {
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }

        $resolved   = [];
        $menu_items = svic_fetch_primary_nav_menu_items();

        if ($menu_items) {
            foreach ($menu_items as $item) {
                if (!($item instanceof WP_Post)) {
                    continue;
                }

                if ((int) ($item->menu_item_parent ?? 0) !== 0) {
                    continue;
                }

                $title = isset($item->title) ? wp_strip_all_tags((string) $item->title) : '';
                $url   = isset($item->url) ? (string) $item->url : '';

                if ($url !== '') {
                    $url = svic_url_with_lang($url);
                }

                $url = esc_url_raw($url);

                if ($title === '' || $url === '') {
                    continue;
                }

                $description = isset($item->description) && $item->description !== ''
                    ? wp_strip_all_tags((string) $item->description)
                    : '';

                if (!isset($resolved[$url])) {
                    $resolved[$url] = [
                        'name'        => $title,
                        'url'         => $url,
                        'description' => $description,
                    ];
                }
            }
        }

        if (!$resolved) {
            $resolved = [];
            foreach (svic_fallback_navigation_schema_items() as $entry) {
                if ($entry['url'] === '') {
                    continue;
                }
                $resolved[$entry['url']] = $entry;
            }
        }

        $cache = array_slice(array_values($resolved), 0, 8);
        return $cache;
    }
}

if (!function_exists('svic_build_site_navigation_elements')) {
    function svic_build_site_navigation_elements(?string $website_id = null): array
    {
        $items = svic_resolve_navigation_schema_items();
        if (!$items) {
            return [];
        }

        $website_id = $website_id ?: untrailingslashit(home_url('/')) . '#website';

        $elements = [];
        $position = 1;
        foreach ($items as $item) {
            $element = [
                '@type'    => 'SiteNavigationElement',
                '@id'      => untrailingslashit($item['url']) . '#nav-' . $position,
                'position' => $position,
                'name'     => $item['name'],
                'url'      => $item['url'],
                'isPartOf' => [
                    '@id' => $website_id,
                ],
            ];

            if (!empty($item['description'])) {
                $element['description'] = $item['description'];
            }

            $elements[] = $element;
            $position++;
        }

        return $elements;
    }
}

/**
 * Output a standalone SiteNavigationElement graph when no SEO plugin handles schema.
 */
if (!function_exists('svic_output_site_navigation_schema')) {
    function svic_output_site_navigation_schema(): void
    {
        if (defined('RANK_MATH_VERSION') || !svic_should_output_navigation_schema()) {
            return;
        }

        $navigation_elements = svic_build_site_navigation_elements();
        if (!$navigation_elements) {
            return;
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@graph'   => [
                [
                    '@type'   => 'WebSite',
                    '@id'     => untrailingslashit(home_url('/')) . '#website',
                    'url'     => home_url('/'),
                    'name'    => get_bloginfo('name'),
                    'hasPart' => $navigation_elements,
                ],
            ],
        ];

        echo "\n" . '<!-- SVICLOUD Navigation Schema -->' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '<script type="application/ld+json">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}

if (!function_exists('svic_rank_math_inject_site_navigation_schema')) {
    function svic_rank_math_inject_site_navigation_schema($schema_graph, $jsonld = null)
    {
        if (!svic_should_output_navigation_schema()) {
            return $schema_graph;
        }

        $website_index = null;
        $website_id    = null;

        foreach ($schema_graph as $index => $node) {
            if (!is_array($node) || empty($node['@type'])) {
                continue;
            }

            $types = is_array($node['@type']) ? $node['@type'] : [$node['@type']];
            $types = array_map('strtolower', array_map('strval', $types));

            if (in_array('website', $types, true)) {
                $website_index = $index;
                $website_id    = isset($node['@id']) ? (string) $node['@id'] : null;
                break;
            }
        }

        $navigation_elements = svic_build_site_navigation_elements($website_id ?: null);
        if (!$navigation_elements) {
            return $schema_graph;
        }

        if ($website_index !== null) {
            $existing = $schema_graph[$website_index]['hasPart'] ?? [];
            if ($existing && !is_array($existing)) {
                $existing = [$existing];
            }

            if (!is_array($existing)) {
                $existing = [];
            }

            $schema_graph[$website_index]['hasPart'] = array_merge($existing, $navigation_elements);
            return $schema_graph;
        }

        $schema_graph[] = [
            '@type'   => 'WebSite',
            '@id'     => untrailingslashit(home_url('/')) . '#website',
            'url'     => home_url('/'),
            'name'    => get_bloginfo('name'),
            'hasPart' => $navigation_elements,
        ];

        return $schema_graph;
    }
}

if (defined('RANK_MATH_VERSION')) {
    add_filter('rank_math/json_ld', 'svic_rank_math_inject_site_navigation_schema', 85, 2);
} else {
    add_action('wp_head', 'svic_output_site_navigation_schema', 99);
}

if (!function_exists('svic_is_order_tracking_request')) {
    function svic_is_order_tracking_request(): bool
    {
        if (function_exists('is_wc_endpoint_url') && is_wc_endpoint_url('order-tracking')) {
            return true;
        }

        if (function_exists('is_page') && is_page('order-tracking')) {
            return true;
        }

        if (!function_exists('is_singular') || !is_singular('page')) {
            return false;
        }

        $order_tracking_post = get_post();
        if (!($order_tracking_post instanceof WP_Post)) {
            return false;
        }

        if (function_exists('has_shortcode') && has_shortcode($order_tracking_post->post_content, 'woocommerce_order_tracking')) {
            return true;
        }

        if (function_exists('has_block') && has_block('woocommerce/order-tracking', $order_tracking_post)) {
            return true;
        }

        return false;
    }
}

if (!function_exists('svic_output_hreflang_links')) {
    function svic_output_hreflang_links(): void
    {
        if (is_admin()) {
            return;
        }

        $supportedLocales = SVIC_Translator::supportedLocales();
        if (!is_array($supportedLocales) || count($supportedLocales) < 2) {
            return;
        }

        if (!function_exists('svic_current_base_url') || !function_exists('svic_url_with_lang')) {
            return;
        }

        $baseUrl = svic_current_base_url();
        if (!is_string($baseUrl) || $baseUrl === '') {
            return;
        }

        $links = [];
        foreach ($supportedLocales as $locale) {
            if (!is_string($locale) || $locale === '') {
                continue;
            }

            $hreflang = svic_locale_to_hreflang($locale);
            if ($hreflang === '') {
                continue;
            }

            $langValue = svic_language_query_value($locale);
            $href      = svic_url_with_lang($baseUrl, $langValue);

            if (!$href || isset($links[$hreflang])) {
                continue;
            }

            $links[$hreflang] = $href;
        }

        if (count($links) < 2) {
            return;
        }

        $defaultLocale = SVIC_Translator::normalizeLocaleCode(null);
        $defaultHref   = svic_url_with_lang($baseUrl, svic_language_query_value($defaultLocale));

        foreach ($links as $hreflang => $href) {
            echo '<link rel="alternate" hreflang="' . esc_attr($hreflang) . '" href="' . esc_url($href) . "\" />\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        if ($defaultHref) {
            echo '<link rel="alternate" hreflang="x-default" href="' . esc_url($defaultHref) . "\" />\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
    }
}

add_action('wp_head', 'svic_output_hreflang_links', 5);

// Output a language-aware canonical URL for each page.
// This ensures the Chinese (/zh) pages self-canonicalize instead of pointing
// to the English variant, preventing cross-language canonical conflicts.
if (!function_exists('svic_output_canonical_link')) {
    function svic_output_canonical_link(): void
    {
        if (is_admin()) {
            return;
        }

        // If an SEO plugin is active (Yoast or RankMath), defer canonical
        // rendering to the plugin to avoid duplicate tags.
        if (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION')) {
            return;
        }

        $canonical = svic_get_localized_canonical_url();
        if (!is_string($canonical) || $canonical === '') {
            return;
        }

        echo '<link rel="canonical" href="' . esc_url($canonical) . "\" />\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}

// Remove WordPress' default rel_canonical to avoid conflicts with our
// language-aware canonical.
remove_action('wp_head', 'rel_canonical');
add_action('wp_head', 'svic_output_canonical_link', 6);

// Adjust canonical via popular SEO plugins if active so zh pages canonicalize
// to their /zh/... variants instead of the English version.
// Yoast SEO
add_filter('wpseo_canonical', function ($url) {
    if (is_admin()) {
        return $url;
    }
    $canonical = svic_get_localized_canonical_url();
    return $canonical ?: $url;
});

// RankMath SEO
add_filter('rank_math/frontend/canonical', function ($url) {
    if (is_admin()) {
        return $url;
    }
    $canonical = svic_get_localized_canonical_url();
    return $canonical ?: $url;
}, 10, 1);

if (!function_exists('svic_homepage_meta_definitions')) {
    function svic_homepage_meta_definitions(): array
    {
        $locale = function_exists('svic_current_locale') ? svic_current_locale() : get_locale();
        $locale = is_string($locale) ? strtolower($locale) : 'en_us';

        $definitions = [
            'zh_tw' => [
                'title'       => '小雲電視盒 美國代理｜10P+、10S 官方現貨與保固',
                'description' => '小雲電視盒 美國代理提供 10P+、10S 官方現貨，內華達倉 48 小時出貨，附一年美國保固與中英雙語禮賓安裝服務，無月費。',
                'image_alt'   => '小雲電視盒 10P+ 與遙控器，強調中英雙語禮賓服務',
            ],
            'zh_cn' => [
                'title'       => '小云电视盒 美国代理｜10P+、10S 官方现货与保固',
                'description' => '小云电视盒美国代理提供 10P+、10S 官方现货，内华达仓 48 小时发货，含一年美保与中英双语礼宾安装，无月费。',
                'image_alt'   => '小云电视盒 10P+ 与遥控器，突显中英双语礼宾服务',
            ],
            'en_us' => [
                'title'       => 'SVICLOUD TV Box US | Authorized 小雲電視盒 美國代理',
                'description' => 'Shop SVICLOUD 10P+ & 10S with 48-hour Nevada shipping, bilingual concierge setup, and 1-year U.S. warranty from the official 小雲電視盒 美國代理—no monthly fees.',
                'image_alt'   => 'SVICLOUD 10P+ streaming box with bilingual concierge support badge',
            ],
        ];

        if (strpos($locale, 'zh') === 0) {
            return $definitions['zh_tw'];
        }

        return $definitions[$locale] ?? $definitions['en_us'];
    }
}

if (!function_exists('svic_get_theme_image_meta')) {
    function svic_get_theme_image_meta(string $relative_path): array
    {
        $relative_path = '/' . ltrim($relative_path, '/');
        $original_path = $relative_path;
        $file_path     = get_template_directory() . $relative_path;
        $url           = get_template_directory_uri() . $relative_path;

        if (!file_exists($file_path)) {
            $webp_candidate = preg_replace('/\\.(png|jpe?g)$/i', '.webp', $relative_path);
            if ($webp_candidate && $webp_candidate !== $relative_path) {
                $candidate_path = get_template_directory() . $webp_candidate;
                if (file_exists($candidate_path)) {
                    $relative_path = $webp_candidate;
                    $file_path     = $candidate_path;
                    $url           = get_template_directory_uri() . $relative_path;
                }
            }
        }

        $width         = null;
        $height        = null;

        if (file_exists($file_path)) {
            $dimensions = @getimagesize($file_path);
            if (is_array($dimensions) && isset($dimensions[0], $dimensions[1])) {
                $width  = (int) $dimensions[0];
                $height = (int) $dimensions[1];
            }
        }

        $meta = [
            'url'    => esc_url_raw($url),
            'width'  => $width,
            'height' => $height,
        ];

        if ($relative_path !== $original_path) {
            $meta['fallback'] = esc_url_raw(get_template_directory_uri() . $original_path);
        }

        return $meta;
    }
}

if (!function_exists('svic_get_homepage_hero_image_meta')) {
    function svic_get_homepage_hero_image_meta(): array
    {
        return svic_get_theme_image_meta('/assets/images/hero-voice-assistant.webp');
    }
}

if (!function_exists('svic_get_homepage_meta_for_output')) {
    /**
     * Helper that returns sanitized homepage meta strings for SEO integrations.
     *
     * @return array{title:string,description:string,image_alt:string}
     */
    function svic_get_homepage_meta_for_output(): array
    {
        $meta = svic_homepage_meta_definitions();

        $title       = isset($meta['title']) ? trim(wp_strip_all_tags((string) $meta['title'])) : '';
        $description = isset($meta['description']) ? trim(wp_strip_all_tags((string) $meta['description'])) : '';
        $image_alt   = isset($meta['image_alt']) ? trim(wp_strip_all_tags((string) $meta['image_alt'])) : '';

        return [
            'title'       => $title,
            'description' => $description,
            'image_alt'   => $image_alt,
        ];
    }
}

if (!function_exists('svic_filter_rank_math_front_page_title')) {
    function svic_filter_rank_math_front_page_title($title)
    {
        if (!function_exists('is_front_page') || !is_front_page()) {
            return $title;
        }

        $meta_title = svic_get_homepage_meta_for_output()['title'];
        return $meta_title !== '' ? $meta_title : $title;
    }

    add_filter('rank_math/frontend/title', 'svic_filter_rank_math_front_page_title', 20);
}

if (!function_exists('svic_filter_rank_math_front_page_description')) {
    function svic_filter_rank_math_front_page_description($description)
    {
        if (!function_exists('is_front_page') || !is_front_page()) {
            return $description;
        }

        $meta_description = svic_get_homepage_meta_for_output()['description'];
        return $meta_description !== '' ? $meta_description : $description;
    }

    add_filter('rank_math/frontend/description', 'svic_filter_rank_math_front_page_description', 20);
    add_filter('rank_math/frontend/snippet_description', 'svic_filter_rank_math_front_page_description', 20);
}

if (!function_exists('svic_filter_rank_math_front_page_og_title')) {
    function svic_filter_rank_math_front_page_og_title($title)
    {
        if (!function_exists('is_front_page') || !is_front_page()) {
            return $title;
        }

        $meta_title = svic_get_homepage_meta_for_output()['title'];
        return $meta_title !== '' ? $meta_title : $title;
    }

    add_filter('rank_math/opengraph/facebook_title', 'svic_filter_rank_math_front_page_og_title', 20);
    add_filter('rank_math/opengraph/twitter_title', 'svic_filter_rank_math_front_page_og_title', 20);
}

if (!function_exists('svic_filter_rank_math_front_page_og_description')) {
    function svic_filter_rank_math_front_page_og_description($description)
    {
        if (!function_exists('is_front_page') || !is_front_page()) {
            return $description;
        }

        $meta_description = svic_get_homepage_meta_for_output()['description'];
        return $meta_description !== '' ? $meta_description : $description;
    }

    add_filter('rank_math/opengraph/facebook_description', 'svic_filter_rank_math_front_page_og_description', 20);
    add_filter('rank_math/opengraph/twitter_description', 'svic_filter_rank_math_front_page_og_description', 20);
}

if (!function_exists('svic_filter_rank_math_front_page_og_image')) {
    function svic_filter_rank_math_front_page_og_image($image)
    {
        if (!function_exists('is_front_page') || !is_front_page()) {
            return $image;
        }

        $hero_meta = svic_get_homepage_hero_image_meta();
        if (empty($hero_meta['url'])) {
            return $image;
        }

        return $hero_meta['url'];
    }

    add_filter('rank_math/opengraph/facebook_image', 'svic_filter_rank_math_front_page_og_image', 20);
    add_filter('rank_math/opengraph/twitter_image', 'svic_filter_rank_math_front_page_og_image', 20);
}

if (!function_exists('svic_filter_rank_math_front_page_og_image_alt')) {
    function svic_filter_rank_math_front_page_og_image_alt($alt)
    {
        if (!function_exists('is_front_page') || !is_front_page()) {
            return $alt;
        }

        $image_alt = svic_get_homepage_meta_for_output()['image_alt'];
        return $image_alt !== '' ? $image_alt : $alt;
    }

    add_filter('rank_math/opengraph/facebook_image_alt', 'svic_filter_rank_math_front_page_og_image_alt', 20);
    add_filter('rank_math/opengraph/twitter_image_alt', 'svic_filter_rank_math_front_page_og_image_alt', 20);
}

if (!function_exists('svic_disable_rank_math_front_page_schema')) {
    /**
     * Force Rank Math to skip its default schema on the static homepage.
     */
    function svic_disable_rank_math_front_page_schema($schema, $type, $object_id)
    {
        if (function_exists('is_front_page') && is_front_page()) {
            return 'none';
        }

        return $schema;
    }

    add_filter('rank_math/frontend/schema/post_type', 'svic_disable_rank_math_front_page_schema', 20, 3);
}

if (!function_exists('svic_get_compare_page_meta_definitions')) {
    /**
     * Retrieve localized metadata for the compare page experience.
     *
     * @return array{title:string,description:string,image_alt:string,image:array}
     */
    function svic_get_compare_page_meta_definitions(): array
    {
        $post_id = get_queried_object_id();

        $title = trim(wp_strip_all_tags(svic_translate('compare.meta.title')));
        if ($title === '' || $title === 'title') {
            if ($post_id) {
                $title = wp_strip_all_tags(get_the_title($post_id));
            }
        }

        $description = trim(wp_strip_all_tags(svic_translate('compare.meta.description')));
        if ($description === '' || $description === 'description') {
            $description = trim(wp_strip_all_tags(svic_translate('compare.hero.subtitle')));
        }

        $image_alt = trim(wp_strip_all_tags(svic_translate('compare.meta.image_alt')));
        if ($image_alt === '' || $image_alt === 'image_alt') {
            $image_alt = 'SVICLOUD streaming boxes';
        }

        $image_meta = svic_get_theme_image_meta('/assets/images/svicloud-hero-product.webp');
        if (empty($image_meta['url'])) {
            $image_meta = svic_get_theme_image_meta('/assets/images/svicloud-10p-plus.webp');
        }

        return [
            'title'       => $title,
            'description' => $description,
            'image_alt'   => $image_alt,
            'image'       => $image_meta,
        ];
    }
}

if (!function_exists('svic_get_store_postal_address_schema')) {
    /**
     * Build a PostalAddress node using the WooCommerce store address fields.
     *
     * Falls back to the Nevada fulfillment center if store options are blank
     * so Google still receives a location signal.
     */
    function svic_get_store_postal_address_schema(): ?array
    {
        $street1   = trim((string) get_option('woocommerce_store_address', ''));
        $street2   = trim((string) get_option('woocommerce_store_address_2', ''));
        $city      = trim((string) get_option('woocommerce_store_city', ''));
        $postcode  = trim((string) get_option('woocommerce_store_postcode', ''));

        $country   = '';
        $region    = '';
        if (function_exists('wc_get_base_location')) {
            $location  = wc_get_base_location();
            if (is_array($location)) {
                $country = isset($location['country']) ? strtoupper((string) $location['country']) : '';
                $region  = isset($location['state']) ? strtoupper((string) $location['state']) : '';
            }
        }

        $address = [
            '@type' => 'PostalAddress',
        ];

        $street = trim($street1 . ($street2 !== '' ? ' ' . $street2 : ''));
        if ($street !== '') {
            $address['streetAddress'] = $street;
        }
        if ($city !== '') {
            $address['addressLocality'] = $city;
        }
        if ($region !== '') {
            $address['addressRegion'] = $region;
        }
        if ($postcode !== '') {
            $address['postalCode'] = $postcode;
        }
        if ($country !== '') {
            $address['addressCountry'] = $country;
        }

        if (count($address) === 1) {
            // Provide a sensible fallback tied to the Nevada warehouse.
            $address['addressLocality'] = 'Las Vegas';
            $address['addressRegion']   = 'NV';
            $address['addressCountry']  = 'US';
        }

        return $address;
    }
}

if (!function_exists('svic_get_primary_contact_phone_number')) {
    /**
     * Returns the concierge phone number formatted for schema (E.164).
     */
    function svic_get_primary_contact_phone_number(): ?string
    {
        $raw = svic_translate('contact.channels.items.phone.value');
        if (!is_string($raw) || $raw === '') {
            return null;
        }

        $digits = preg_replace('/[^0-9+]/', '', $raw);
        if ($digits === '') {
            return null;
        }

        if ($digits[0] !== '+') {
            // Assume United States country code when a leading + is missing.
            $digits = '+1' . ltrim($digits, '+');
        }

        return $digits;
    }
}

if (!function_exists('svic_get_price_range_label')) {
    function svic_get_price_range_label(): string
    {
        /**
         * Pricing currently spans $180–$360 USD across the 10-series lineup.
         * Use a readable string for the Organization schema.
         */
        return 'USD $180-$360';
    }
}

if (!function_exists('svic_get_organization_schema_id')) {
    function svic_get_organization_schema_id(): string
    {
        $site_url = trailingslashit(home_url());
        return untrailingslashit($site_url) . '#organization';
    }
}

if (!function_exists('svic_generate_product_image_object')) {
    /**
     * Returns an ImageObject array for the given product (or fallback URL).
     *
     * @param WC_Product|null $product
     */
    function svic_generate_product_image_object($product, ?string $fallback_url = null): ?array
    {
        $image_id = null;
        $caption  = '';

        if ($product instanceof WC_Product) {
            $caption  = $product->get_name();
            $image_id = $product->get_image_id();

            if (!$image_id && method_exists($product, 'get_gallery_image_ids')) {
                $gallery_ids = $product->get_gallery_image_ids();
                if (is_array($gallery_ids) && !empty($gallery_ids)) {
                    $image_id = (int) reset($gallery_ids);
                }
            }
        }

        if ($image_id) {
            $image_src = wp_get_attachment_image_src($image_id, 'full');
            if (is_array($image_src) && isset($image_src[0])) {
                $image_object = [
                    '@type'  => 'ImageObject',
                    'url'    => esc_url_raw($image_src[0]),
                ];
                if (isset($image_src[1])) {
                    $image_object['width'] = (int) $image_src[1];
                }
                if (isset($image_src[2])) {
                    $image_object['height'] = (int) $image_src[2];
                }
                if ($caption !== '') {
                    $image_object['caption'] = $caption;
                }
                return $image_object;
            }
        }

        if ($fallback_url) {
            $fallback = [
                '@type' => 'ImageObject',
                'url'   => esc_url_raw($fallback_url),
            ];
            if ($caption !== '') {
                $fallback['caption'] = $caption;
            }
            return $fallback;
        }

        return null;
    }
}

if (!function_exists('svic_build_product_schema_from_wc_product')) {
    /**
     * Builds a Product schema node from a WC_Product plus optional overrides.
     *
     * @param WC_Product $product
     * @param array<string,mixed> $overrides
     */
    function svic_build_product_schema_from_wc_product($product, array $overrides = []): ?array
    {
        if (!$product instanceof WC_Product) {
            return null;
        }

        $product_id = $product->get_id();
        $permalink  = $overrides['url'] ?? get_permalink($product_id);
        if (!is_string($permalink) || $permalink === '') {
            return null;
        }

        $localized_url = function_exists('svic_url_with_lang') ? svic_url_with_lang($permalink) : $permalink;
        if (!is_string($localized_url) || $localized_url === '') {
            return null;
        }

        $product_url = esc_url_raw($localized_url);
        $slug        = $product->get_slug();

        $name = $overrides['name'] ?? $product->get_name();
        if (!is_string($name) || $name === '') {
            $name = get_the_title($product_id);
        }

        if (!is_string($name) || $name === '') {
            return null;
        }

        $description = $overrides['description'] ?? '';
        if ($description === '') {
            $translation_key = 'products.' . $slug . '.description';
            $translated      = svic_translate_rich($translation_key);
            if (is_string($translated) && $translated !== '' && $translated !== 'description') {
                $description = wp_strip_all_tags($translated);
            }
        }
        if ($description === '') {
            $description = wp_strip_all_tags($product->get_description());
        }
        if ($description === '') {
            $description = wp_strip_all_tags($product->get_short_description());
        }

        $image_object = $overrides['image'] ?? svic_generate_product_image_object($product);
        if (!is_array($image_object) || empty($image_object['url'])) {
            $image_object = null;
        }

        $raw_price = $overrides['price'] ?? $product->get_price();
        if ($raw_price === '' || $raw_price === null) {
            $raw_price = $product->get_regular_price();
        }
        $price_value = null;
        if ($raw_price !== '' && $raw_price !== null) {
            $price_value = number_format((float) $raw_price, 2, '.', '');
        }

        $availability = $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock';

        $sku = $product->get_sku();
        if (!is_string($sku) || $sku === '') {
            $sku = strtoupper(str_replace('-', '', $slug));
        }

        $product_node = [
            '@type'         => 'Product',
            '@id'           => untrailingslashit($product_url) . '#product',
            'url'           => $product_url,
            'name'          => $name,
            'itemCondition' => 'https://schema.org/NewCondition',
            'brand'         => [
                '@type' => 'Brand',
                'name'  => 'SVICLOUD',
            ],
            'category'      => 'Electronics > Streaming Players',
        ];

        if ($description !== '') {
            $product_node['description'] = $description;
        }

        if ($sku !== '') {
            $product_node['sku'] = $sku;
        }

        if ($image_object) {
            $product_node['image'] = $image_object;
        }

        if ($price_value !== null) {
            $product_node['offers'] = [
                '@type'         => 'Offer',
                'priceCurrency' => strtoupper(get_option('woocommerce_currency', 'USD')),
                'price'         => $price_value,
                'availability'  => $availability,
                'url'           => $product_url,
                'seller'        => [
                    '@id' => svic_get_organization_schema_id(),
                ],
            ];
        }

        return $product_node;
    }
}

if (!function_exists('svic_get_organization_schema_enhancements')) {
    /**
     * Shared Organization fields reused between our custom schema and Rank Math's output.
     */
    function svic_get_organization_schema_enhancements(): array
    {
        $enhancements = [];

        $legal_name = apply_filters('svic_organization_legal_name', '168 Media Group LLC');
        if (is_string($legal_name)) {
            $legal_name = trim($legal_name);
        }
        if (!empty($legal_name)) {
            $enhancements['legalName'] = $legal_name;
        }

        $contact_email = svic_support_form_recipient();
        if (is_string($contact_email) && $contact_email !== '') {
            $enhancements['contactPoint'] = [
                [
                    '@type'             => 'ContactPoint',
                    'contactType'       => 'customer support',
                    'email'             => $contact_email,
                    'areaServed'        => ['US', 'CA'],
                    'availableLanguage' => ['English', 'Chinese'],
                ],
            ];
        }

        $postal_address = svic_get_store_postal_address_schema();
        if (is_array($postal_address) && $postal_address) {
            $enhancements['address'] = $postal_address;
        }

        $primary_phone = svic_get_primary_contact_phone_number();
        if (is_string($primary_phone) && $primary_phone !== '') {
            $enhancements['telephone'] = $primary_phone;
        }

        $price_range = svic_get_price_range_label();
        if ($price_range !== '') {
            $enhancements['priceRange'] = $price_range;
        }

        return $enhancements;
    }
}

if (!function_exists('svic_apply_organization_schema_enhancements')) {
    function svic_apply_organization_schema_enhancements(array $schema): array
    {
        $enhancements = svic_get_organization_schema_enhancements();
        if (empty($enhancements)) {
            return $schema;
        }

        foreach ($enhancements as $key => $value) {
            if ($key === 'contactPoint') {
                $existing = [];
                if (isset($schema['contactPoint']) && is_array($schema['contactPoint'])) {
                    $existing = array_values(array_filter($schema['contactPoint'], 'is_array'));
                }
                $schema['contactPoint'] = array_values(array_merge($existing, $value));
                continue;
            }

            if ($key === 'legalName') {
                $schema[$key] = $value;
                continue;
            }

            if (!isset($schema[$key]) || $schema[$key] === '' || $schema[$key] === null) {
                $schema[$key] = $value;
            }
        }

        return $schema;
    }
}

if (!function_exists('svic_adjust_homepage_schema_node')) {
    function svic_adjust_homepage_schema_node(array $node): array
    {
        $meta       = svic_get_homepage_meta_for_output();
        $image_meta = svic_get_homepage_hero_image_meta();

        $types = [];
        if (isset($node['@type'])) {
            $types = is_array($node['@type']) ? array_values($node['@type']) : [$node['@type']];
        }

        $filtered_types = [];
        foreach ($types as $type) {
            $type_string = is_string($type) ? trim($type) : '';
            if ($type_string === '') {
                continue;
            }
            if (strcasecmp($type_string, 'Article') === 0) {
                continue;
            }
            if (!in_array($type_string, $filtered_types, true)) {
                $filtered_types[] = $type_string;
            }
        }

        $has_webpage_type = false;
        foreach ($filtered_types as $type) {
            if (strcasecmp($type, 'WebPage') === 0) {
                $has_webpage_type = true;
                break;
            }
        }
        if (!$has_webpage_type) {
            $filtered_types[] = 'WebPage';
        }

        $node['@type'] = count($filtered_types) === 1 ? $filtered_types[0] : $filtered_types;

        if ($meta['title'] !== '') {
            $node['name'] = $meta['title'];
            $node['headline'] = $meta['title'];
        }

        if ($meta['description'] !== '') {
            $node['description'] = $meta['description'];
        }

        if (function_exists('svic_current_locale') && function_exists('svic_locale_to_hreflang')) {
            $lang = svic_locale_to_hreflang(svic_current_locale());
            if ($lang !== '') {
                $node['inLanguage'] = strtolower(str_replace('_', '-', $lang));
            }
        }

        if (!empty($image_meta['url'])) {
            $image_object = [
                '@type' => 'ImageObject',
                'url'   => $image_meta['url'],
            ];

            if (!empty($image_meta['width'])) {
                $image_object['width'] = (int) $image_meta['width'];
            }

            if (!empty($image_meta['height'])) {
                $image_object['height'] = (int) $image_meta['height'];
            }

            if ($meta['image_alt'] !== '') {
                $image_object['caption'] = $meta['image_alt'];
            }

            $node['image'] = [$image_object];
            $node['primaryImageOfPage'] = $image_object;
            $node['thumbnailUrl'] = $image_meta['url'];
        }

        return $node;
    }
}

if (!function_exists('svic_filter_rank_math_schema_graph')) {
    /**
     * Inject address/phone/priceRange details into Rank Math's Organization node so Google
     * no longer flags missing required fields when the plugin outputs the schema.
     *
     * @param array<mixed> $schema_graph Rank Math schema data.
     * @return array<mixed>
     */
    function svic_filter_rank_math_schema_graph($schema_graph, $jsonld = null)
    {
        if (is_admin() || empty($schema_graph) || !is_array($schema_graph)) {
            return $schema_graph;
        }

        $is_front_page = function_exists('is_front_page') && is_front_page();
        $homepage_ids  = [];
        if ($is_front_page) {
            $canonical = svic_get_localized_canonical_url();
            if (!is_string($canonical) || $canonical === '') {
                $canonical = home_url('/');
            }

            $site_home = home_url('/');

            $homepage_ids = array_unique([
                trailingslashit($canonical) . '#webpage',
                untrailingslashit($canonical) . '#webpage',
                $canonical,
                untrailingslashit($canonical),
                trailingslashit($site_home) . '#webpage',
                untrailingslashit($site_home) . '#webpage',
                $site_home,
                untrailingslashit($site_home),
            ]);
        }

        foreach ($schema_graph as $key => $node) {
            if (!is_array($node) || empty($node['@type'])) {
                continue;
            }

            $types = is_array($node['@type']) ? $node['@type'] : [$node['@type']];
            $is_organization = false;

            foreach ($types as $type) {
                if (!is_string($type)) {
                    continue;
                }

                if (stripos($type, 'Organization') !== false) {
                    $is_organization = true;
                    break;
                }
            }

            if ($is_organization) {
                $schema_graph[$key] = svic_apply_organization_schema_enhancements($node);
                continue;
            }

            if ($is_front_page && !empty($homepage_ids)) {
                $node_id = isset($node['@id']) && is_string($node['@id']) ? $node['@id'] : '';
                $node_url = isset($node['url']) && is_string($node['url']) ? $node['url'] : '';

                $matches_homepage = ($node_id !== '' && in_array($node_id, $homepage_ids, true))
                    || ($node_url !== '' && in_array($node_url, $homepage_ids, true));

                if ($matches_homepage) {
                    $schema_graph[$key] = svic_adjust_homepage_schema_node($node);
                }
            }
        }

        return $schema_graph;
    }

    add_filter('rank_math/json_ld', 'svic_filter_rank_math_schema_graph', 80, 2);
}

if (!function_exists('svic_output_max_image_preview_meta')) {
    function svic_output_max_image_preview_meta(): void
    {
        if (is_admin() || (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION'))) {
            return;
        }

        echo "<meta name=\"robots\" content=\"max-image-preview:large\" />\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}

add_action('wp_head', 'svic_output_max_image_preview_meta', 3);

if (!function_exists('svic_output_homepage_social_meta')) {
    function svic_output_homepage_social_meta(): void
    {
        if (is_admin() || !is_front_page() || (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION'))) {
            return;
        }

        $meta     = svic_homepage_meta_definitions();
        $image    = svic_get_homepage_hero_image_meta();
        $siteName = get_bloginfo('name');
        $url      = svic_get_localized_canonical_url() ?: home_url('/');

        $tags = [
            ['property' => 'og:type', 'content' => 'website'],
            ['property' => 'og:site_name', 'content' => $siteName],
            ['property' => 'og:title', 'content' => $meta['title']],
            ['property' => 'og:description', 'content' => $meta['description']],
            ['property' => 'og:url', 'content' => $url],
        ];

        if (!empty($image['url'])) {
            $tags[] = ['property' => 'og:image', 'content' => $image['url']];
            if (!empty($meta['image_alt'])) {
                $tags[] = ['property' => 'og:image:alt', 'content' => $meta['image_alt']];
            }
            if (!empty($image['width'])) {
                $tags[] = ['property' => 'og:image:width', 'content' => (string) $image['width']];
            }
            if (!empty($image['height'])) {
                $tags[] = ['property' => 'og:image:height', 'content' => (string) $image['height']];
            }
        }

        $twitter_tags = [
            ['name' => 'twitter:card', 'content' => 'summary_large_image'],
            ['name' => 'twitter:title', 'content' => $meta['title']],
            ['name' => 'twitter:description', 'content' => $meta['description']],
        ];

        if (!empty($image['url'])) {
            $twitter_tags[] = ['name' => 'twitter:image', 'content' => $image['url']];
        }

        foreach ($tags as $tag) {
            echo '<meta property="' . esc_attr($tag['property']) . '" content="' . esc_attr($tag['content']) . '" />' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        foreach ($twitter_tags as $tag) {
            echo '<meta name="' . esc_attr($tag['name']) . '" content="' . esc_attr($tag['content']) . '" />' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
    }
}

add_action('wp_head', 'svic_output_homepage_social_meta', 7);
add_action('wp_head', 'svic_output_compare_page_meta', 7);

if (!function_exists('svic_get_featured_image_meta')) {
    function svic_get_featured_image_meta(int $post_id): ?array
    {
        $thumbnail_id = get_post_thumbnail_id($post_id);
        if (!$thumbnail_id) {
            return null;
        }

        $image_data = wp_get_attachment_image_src($thumbnail_id, 'full');
        if (!is_array($image_data) || empty($image_data[0])) {
            return null;
        }

        return [
            'url'    => esc_url_raw($image_data[0]),
            'width'  => isset($image_data[1]) ? (int) $image_data[1] : null,
            'height' => isset($image_data[2]) ? (int) $image_data[2] : null,
        ];
    }
}

if (!function_exists('svic_output_singular_social_meta')) {
    function svic_output_singular_social_meta(): void
    {
        if (is_admin() || is_front_page()) {
            return;
        }

        if (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION')) {
            return;
        }

        $post_id = get_queried_object_id();
        if (!$post_id) {
            return;
        }

        $is_product = function_exists('is_singular') && is_singular('product');
        $is_post    = function_exists('is_single') && is_single();

        if (!$is_product && !$is_post) {
            return;
        }

        $title = wp_strip_all_tags(get_the_title($post_id));
        $description = '';

        if ($is_product && function_exists('wc_get_product')) {
            $product = wc_get_product($post_id);
            if ($product instanceof WC_Product) {
                $description = wp_strip_all_tags($product->get_short_description() ?: $product->get_description());
            }
        } else {
            $description = wp_strip_all_tags(get_the_excerpt($post_id));
        }

        if ($description === '') {
            $description = wp_trim_words(strip_shortcodes(wp_strip_all_tags(get_post_field('post_content', $post_id))), 40, '…');
        }

        $image_meta = svic_get_featured_image_meta($post_id);
        if (!$image_meta) {
            if ($is_product) {
                $slug = get_post_field('post_name', $post_id);
                if ($slug === 'svicloud-10p-plus') {
                    $image_meta = svic_get_theme_image_meta('/assets/images/svicloud-10p-plus.webp');
                } elseif ($slug === 'svicloud-10s') {
                    $image_meta = svic_get_theme_image_meta('/assets/images/svicloud-tvbox-10s.webp');
                }
            }
            if (!$image_meta) {
                $image_meta = svic_get_theme_image_meta('/assets/images/hero-voice-assistant.webp');
            }
        }

        $canonical = svic_get_localized_canonical_url() ?: get_permalink($post_id);

        $meta_tags = [
            ['property' => 'og:type', 'content' => $is_product ? 'product' : 'article'],
            ['property' => 'og:site_name', 'content' => get_bloginfo('name')],
            ['property' => 'og:title', 'content' => $title],
            ['property' => 'og:description', 'content' => $description],
            ['property' => 'og:url', 'content' => $canonical],
        ];

        if (!empty($image_meta['url'])) {
            $meta_tags[] = ['property' => 'og:image', 'content' => $image_meta['url']];
            if (!empty($image_meta['width'])) {
                $meta_tags[] = ['property' => 'og:image:width', 'content' => (string) $image_meta['width']];
            }
            if (!empty($image_meta['height'])) {
                $meta_tags[] = ['property' => 'og:image:height', 'content' => (string) $image_meta['height']];
            }
        }

        $twitter_tags = [
            ['name' => 'twitter:card', 'content' => 'summary_large_image'],
            ['name' => 'twitter:title', 'content' => $title],
            ['name' => 'twitter:description', 'content' => $description],
        ];

        if (!empty($image_meta['url'])) {
            $twitter_tags[] = ['name' => 'twitter:image', 'content' => $image_meta['url']];
        }

        foreach ($meta_tags as $tag) {
            echo '<meta property="' . esc_attr($tag['property']) . '" content="' . esc_attr($tag['content']) . "\" />\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        foreach ($twitter_tags as $tag) {
            echo '<meta name="' . esc_attr($tag['name']) . '" content="' . esc_attr($tag['content']) . "\" />\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
    }
}

if (!function_exists('svic_output_compare_page_meta')) {
    function svic_output_compare_page_meta(): void
    {
        if (is_admin() || (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION'))) {
            return;
        }

        if (!function_exists('is_page_template') || !is_page_template('page-compare.php')) {
            return;
        }

        $meta         = svic_get_compare_page_meta_definitions();
        $title        = isset($meta['title']) ? trim((string) $meta['title']) : '';
        $description  = isset($meta['description']) ? trim((string) $meta['description']) : '';
        $image_meta   = isset($meta['image']) && is_array($meta['image']) ? $meta['image'] : [];
        $image_url    = isset($image_meta['url']) ? (string) $image_meta['url'] : '';
        $image_width  = isset($image_meta['width']) && $image_meta['width'] ? (int) $image_meta['width'] : null;
        $image_height = isset($image_meta['height']) && $image_meta['height'] ? (int) $image_meta['height'] : null;
        $image_alt    = isset($meta['image_alt']) ? trim((string) $meta['image_alt']) : '';

        $post_id = get_queried_object_id();

        if ($title === '' && $post_id) {
            $title = wp_strip_all_tags(get_the_title($post_id));
        }

        if ($description === '') {
            $description = $title;
        }

        if ($title === '' && $description === '') {
            return;
        }

        $canonical = svic_get_localized_canonical_url();
        if (!is_string($canonical) || $canonical === '') {
            if ($post_id) {
                $canonical = get_permalink($post_id);
            }
        }

        if (!is_string($canonical) || $canonical === '') {
            return;
        }

        $canonical = esc_url_raw($canonical);
        $site_name = get_bloginfo('name') ?: 'SVICLOUD TV BOX US';

        echo '<meta name="description" content="' . esc_attr($description) . "\" />\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

        $meta_tags = [
            ['property' => 'og:type', 'content' => 'website'],
            ['property' => 'og:site_name', 'content' => $site_name],
            ['property' => 'og:title', 'content' => $title],
            ['property' => 'og:description', 'content' => $description],
            ['property' => 'og:url', 'content' => $canonical],
        ];

        if ($image_url !== '') {
            $meta_tags[] = ['property' => 'og:image', 'content' => $image_url];
            if ($image_alt !== '') {
                $meta_tags[] = ['property' => 'og:image:alt', 'content' => $image_alt];
            }
            if ($image_width) {
                $meta_tags[] = ['property' => 'og:image:width', 'content' => (string) $image_width];
            }
            if ($image_height) {
                $meta_tags[] = ['property' => 'og:image:height', 'content' => (string) $image_height];
            }
        }

        foreach ($meta_tags as $tag) {
            echo '<meta property="' . esc_attr($tag['property']) . '" content="' . esc_attr($tag['content']) . "\" />\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        $twitter_tags = [
            ['name' => 'twitter:card', 'content' => 'summary_large_image'],
            ['name' => 'twitter:title', 'content' => $title],
            ['name' => 'twitter:description', 'content' => $description],
        ];

        if ($image_url !== '') {
            $twitter_tags[] = ['name' => 'twitter:image', 'content' => $image_url];
            if ($image_alt !== '') {
                $twitter_tags[] = ['name' => 'twitter:image:alt', 'content' => $image_alt];
            }
        }

        foreach ($twitter_tags as $tag) {
            echo '<meta name="' . esc_attr($tag['name']) . '" content="' . esc_attr($tag['content']) . "\" />\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        $locale   = function_exists('svic_current_locale') ? svic_current_locale() : get_locale();
        $hreflang = '';
        if (is_string($locale) && $locale !== '' && function_exists('svic_locale_to_hreflang')) {
            $hreflang = svic_locale_to_hreflang($locale);
        }
        $language = $hreflang ? strtolower(str_replace('_', '-', $hreflang)) : 'en-us';

        $site_url = home_url('/');
        $schema   = [
            '@context'   => 'https://schema.org',
            '@type'      => 'CollectionPage',
            '@id'        => trailingslashit($canonical) . '#webpage',
            'url'        => $canonical,
            'name'       => $title,
            'description' => $description,
            'inLanguage' => $language,
            'isPartOf'   => [
                '@type' => 'WebSite',
                '@id'   => trailingslashit($site_url) . '#website',
                'name'  => $site_name,
                'url'   => $site_url,
            ],
        ];

        if ($image_url !== '') {
            $image_object = [
                '@type' => 'ImageObject',
                'url'   => $image_url,
            ];

            if ($image_width) {
                $image_object['width'] = $image_width;
            }

            if ($image_height) {
                $image_object['height'] = $image_height;
            }

            if ($image_alt !== '') {
                $image_object['caption'] = $image_alt;
            }

            $schema['image']              = [$image_object];
            $schema['primaryImageOfPage'] = $image_object;
            $schema['thumbnailUrl']       = $image_url;
        }

        if (class_exists('WooCommerce')) {
            $schema['mainEntity'] = [
                '@id' => untrailingslashit($canonical) . '#compare-itemlist',
            ];
        }

        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}

add_action('wp_head', 'svic_output_singular_social_meta', 8);

if (!function_exists('svic_output_homepage_webpage_schema')) {
    function svic_output_homepage_webpage_schema(): void
    {
        if (is_admin() || !is_front_page() || (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION'))) {
            return;
        }

        $meta        = svic_homepage_meta_definitions();
        $image       = svic_get_homepage_hero_image_meta();
        $canonical   = svic_get_localized_canonical_url() ?: home_url('/');
        $site_name   = get_bloginfo('name');
        $site_url    = home_url('/');
        $language    = function_exists('svic_locale_to_hreflang') ? svic_locale_to_hreflang(svic_current_locale()) : get_locale();
        $language    = $language ? strtolower(str_replace('_', '-', $language)) : 'en-us';

        $webpage_schema = [
            '@context'   => 'https://schema.org',
            '@type'      => 'WebPage',
            '@id'        => trailingslashit($canonical) . '#webpage',
            'url'        => $canonical,
            'name'       => $meta['title'],
            'description'=> $meta['description'],
            'inLanguage' => $language,
            'isPartOf'   => [
                '@type' => 'WebSite',
                '@id'   => trailingslashit($site_url) . '#website',
                'name'  => $site_name,
                'url'   => $site_url,
            ],
        ];

        if (!empty($image['url'])) {
            $image_object = [
                '@type'  => 'ImageObject',
                'url'    => $image['url'],
            ];

            if (!empty($image['width'])) {
                $image_object['width'] = $image['width'];
            }

            if (!empty($image['height'])) {
                $image_object['height'] = $image['height'];
            }

            if (!empty($meta['image_alt'])) {
                $image_object['caption'] = $meta['image_alt'];
            }

            $webpage_schema['primaryImageOfPage'] = $image_object;
        }

        echo '<script type="application/ld+json">' . wp_json_encode($webpage_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}

add_action('wp_head', 'svic_output_homepage_webpage_schema', 8);

if (!function_exists('svic_output_global_site_schema')) {
    function svic_output_global_site_schema(): void
    {
        if (is_admin() || defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION')) {
            return;
        }

        $site_url    = home_url('/');
        $site_name   = get_bloginfo('name') ?: 'SVICLOUD TV BOX US';
        $organization_id = trailingslashit($site_url) . '#organization';

        $logo_meta = svic_get_theme_image_meta('/assets/images/site-logo.png');
        if (empty($logo_meta['url'])) {
            $site_icon = get_site_icon_url();
            if ($site_icon) {
                $logo_meta = ['url' => esc_url_raw($site_icon)];
            }
        }

        $schemas    = [];
        $organization = [
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            '@id'      => $organization_id,
            'name'     => $site_name,
            'url'      => $site_url,
        ];

        if (!empty($logo_meta['url'])) {
            $logo_object = [
                '@type' => 'ImageObject',
                'url'   => $logo_meta['url'],
            ];

            if (!empty($logo_meta['width'])) {
                $logo_object['width'] = $logo_meta['width'];
            }

            if (!empty($logo_meta['height'])) {
                $logo_object['height'] = $logo_meta['height'];
            }

            $organization['logo'] = $logo_object;
        }

        $schemas[] = svic_apply_organization_schema_enhancements($organization);

        $website = [
            '@context'        => 'https://schema.org',
            '@type'           => 'WebSite',
            '@id'             => trailingslashit($site_url) . '#website',
            'url'             => $site_url,
            'name'            => $site_name,
            'publisher'       => ['@id' => $organization_id],
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => home_url('/?s={search_term_string}'),
                'query-input' => 'required name=search_term_string',
            ],
        ];

        $schemas[] = $website;

        echo '<script type="application/ld+json">' . wp_json_encode($schemas, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}

add_action('wp_head', 'svic_output_global_site_schema', 6);

if (!function_exists('svic_output_single_product_schema')) {
    function svic_output_single_product_schema(): void
    {
        if (is_admin() || !function_exists('is_product') || !is_product() || !class_exists('WooCommerce')) {
            return;
        }

        global $product;
        if (!$product instanceof WC_Product) {
            $product = function_exists('wc_get_product') ? wc_get_product(get_the_ID()) : null;
        }

        if (!$product instanceof WC_Product) {
            return;
        }

        $product_node = svic_build_product_schema_from_wc_product($product);
        if (empty($product_node)) {
            return;
        }

        echo '<script type="application/ld+json">' . wp_json_encode($product_node, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}

add_action('wp_head', 'svic_output_single_product_schema', 8);

if (!function_exists('svic_should_render_breadcrumbs')) {
    function svic_should_render_breadcrumbs(): bool
    {
        if (is_admin() || is_front_page() || is_404()) {
            return false;
        }

        if (function_exists('is_cart') && is_cart()) {
            return false;
        }

        if (function_exists('is_checkout') && is_checkout()) {
            return false;
        }

        return true;
    }
}

if (!function_exists('svic_get_breadcrumb_items')) {
    function svic_get_breadcrumb_items(): array
    {
        $filter_label = static function ($label, $slug = '', $context = '') {
            if (!function_exists('svic_translate_html')) {
                return $label;
            }

            if ($context === 'home') {
                $translated = svic_translate_html('header.nav.home');
                if ($translated) {
                    return wp_strip_all_tags($translated);
                }
            }

            $normalized_slug = $slug ? sanitize_title($slug) : '';
            if ($normalized_slug === 'shop') {
                $nav_label = svic_translate_html('header.nav.shop');
                if ($nav_label) {
                    return wp_strip_all_tags($nav_label);
                }
            }
            if ($normalized_slug && function_exists('svic_guides_resolve_section_key') && function_exists('svic_guides_get_section_by_key')) {
                $section_key = svic_guides_resolve_section_key($normalized_slug);
                if ($section_key) {
                    $section = svic_guides_get_section_by_key($section_key);
                    $label_key = is_array($section) ? ($section['label_key'] ?? '') : '';
                    if ($label_key) {
                        $section_label = svic_translate_html($label_key);
                        if ($section_label) {
                            return wp_strip_all_tags($section_label);
                        }
                    }
                }
            }

            return $label;
        };

        $items = [];
        $home_url = function_exists('svic_url_with_lang') ? svic_url_with_lang(home_url('/')) : home_url('/');
        $items[] = [
            'label' => $filter_label(esc_html__('Home', 'svicloudtvbox-lumen'), '', 'home'),
            'url'   => $home_url,
        ];

        if (function_exists('is_shop') && is_shop()) {
            $shop_id = function_exists('wc_get_page_id') ? wc_get_page_id('shop') : 0;
            $shop_label = $shop_id && $shop_id > 0 ? get_the_title($shop_id) : esc_html__('Shop', 'svicloudtvbox-lumen');
            $shop_slug  = $shop_id ? get_post_field('post_name', $shop_id) : 'shop';

            $items[] = [
                'label' => $filter_label($shop_label, $shop_slug, 'shop'),
                'url'   => null,
            ];

            return $items;
        }

        if (is_home() && !is_front_page()) {
            $blog_id = (int) get_option('page_for_posts');
            $items[] = [
                'label' => $blog_id ? get_the_title($blog_id) : esc_html__('Blog', 'svicloudtvbox-lumen'),
                'url'   => null,
            ];

            return $items;
        }

        if (is_page()) {
            $ancestors = array_reverse(get_post_ancestors(get_queried_object_id()));
            foreach ($ancestors as $ancestor_id) {
                $ancestor_slug = get_post_field('post_name', $ancestor_id);
                $items[] = [
                    'label' => $filter_label(get_the_title($ancestor_id), $ancestor_slug, 'page'),
                    'url'   => function_exists('svic_url_with_lang') ? svic_url_with_lang(get_permalink($ancestor_id)) : get_permalink($ancestor_id),
                ];
            }

            $current_slug = get_post_field('post_name', get_the_ID());
            $items[] = [
                'label' => $filter_label(get_the_title(), $current_slug, 'page'),
                'url'   => null,
            ];

            return $items;
        }

        if (function_exists('is_product') && is_product()) {
            if (function_exists('wc_get_page_id')) {
                $shop_id = wc_get_page_id('shop');
                if ($shop_id && $shop_id > 0) {
                    $items[] = [
                        'label' => get_the_title($shop_id),
                        'url'   => function_exists('svic_url_with_lang') ? svic_url_with_lang(get_permalink($shop_id)) : get_permalink($shop_id),
                    ];
                }
            }

            $category_ids = wp_get_post_terms(get_the_ID(), 'product_cat', ['fields' => 'ids']);
            if (is_array($category_ids) && $category_ids !== []) {
                $primary_cat_id = (int) $category_ids[0];
                $term = get_term($primary_cat_id, 'product_cat');
                if ($term && !is_wp_error($term)) {
                    $items[] = [
                        'label' => $term->name,
                        'url'   => function_exists('svic_url_with_lang') ? svic_url_with_lang(get_term_link($term)) : get_term_link($term),
                    ];
                }
            }

            $items[] = [
                'label' => get_the_title(),
                'url'   => null,
            ];

            return $items;
        }

        if (is_singular('post')) {
            $blog_id = (int) get_option('page_for_posts');
            if ($blog_id) {
                $items[] = [
                    'label' => get_the_title($blog_id),
                    'url'   => function_exists('svic_url_with_lang') ? svic_url_with_lang(get_permalink($blog_id)) : get_permalink($blog_id),
                ];
            }
            $items[] = [
                'label' => get_the_title(),
                'url'   => null,
            ];

            return $items;
        }

        if (is_search()) {
            $items[] = [
                'label' => sprintf(esc_html__('Search results for “%s”', 'svicloudtvbox-lumen'), get_search_query()),
                'url'   => null,
            ];

            return $items;
        }

        if (is_archive()) {
            $items[] = [
                'label' => get_the_archive_title(),
                'url'   => null,
            ];

            return $items;
        }

        $items[] = [
            'label' => get_the_title(),
            'url'   => null,
        ];

        return $items;
    }
}

if (!function_exists('svic_render_breadcrumbs')) {
    function svic_render_breadcrumbs(): void
    {
        if (!svic_should_render_breadcrumbs()) {
            return;
        }

        $items = svic_get_breadcrumb_items();
        if (count($items) < 2) {
            return;
        }

        echo '<nav class="svic-breadcrumbs" aria-label="' . esc_attr__('Breadcrumb', 'svicloudtvbox-lumen') . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '<ol class="svic-breadcrumbs__list" itemscope itemtype="https://schema.org/BreadcrumbList">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

        foreach ($items as $index => $item) {
            $position = $index + 1;
            $label    = isset($item['label']) ? $item['label'] : '';
            $url      = isset($item['url']) ? $item['url'] : null;

            echo '<li class="svic-breadcrumbs__item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            if ($url) {
                echo '<a class="svic-breadcrumbs__link" href="' . esc_url($url) . '" itemprop="item"><span itemprop="name">' . esc_html($label) . '</span></a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            } else {
                echo '<span class="svic-breadcrumbs__current" itemprop="name" aria-current="page">' . esc_html($label) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }
            echo '<meta itemprop="position" content="' . esc_attr((string) $position) . '" />'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        echo '</ol></nav>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}

if (!function_exists('svic_filter_body_class')) {
    function svic_filter_body_class($classes)
    {
        if (!is_array($classes)) {
            $classes = [];
        }

        if (function_exists('svic_current_locale')) {
            $locale = svic_current_locale();
            if (is_string($locale) && stripos($locale, 'zh') === 0 && !in_array('lang-zh', $classes, true)) {
                $classes[] = 'lang-zh';
            }
        }

        $disallowed = ['theme-svicloudtvbox', 'theme-svicloudtvbox-lumen'];
        $classes = array_values(array_filter($classes, static function ($class) use ($disallowed) {
            return !in_array($class, $disallowed, true);
        }));

        return $classes;
    }
}

add_filter('body_class', 'svic_filter_body_class');

function svic_support_form_recipient(): string
{
    return apply_filters('svic_support_form_recipient', 'support@svicloudtvbox.us');
}

function svic_handle_support_form(): void
{
    $locale      = isset($_POST['svic_locale']) ? sanitize_text_field(wp_unslash($_POST['svic_locale'])) : '';
    $lang_query  = svic_language_query_value($locale ?: null);
    $redirect    = svic_url_with_lang(home_url('/support/'), $lang_query);

    if (!isset($_POST['svic_support_nonce']) || !wp_verify_nonce(wp_unslash($_POST['svic_support_nonce']), 'svic_support_form')) {
        wp_safe_redirect(add_query_arg('support_status', 'error', $redirect));
        exit;
    }

    $name    = isset($_POST['support_name']) ? sanitize_text_field(wp_unslash($_POST['support_name'])) : '';
    $email   = isset($_POST['support_email']) ? sanitize_email(wp_unslash($_POST['support_email'])) : '';
    $phone   = isset($_POST['support_phone']) ? sanitize_text_field(wp_unslash($_POST['support_phone'])) : '';
    $order   = isset($_POST['support_order']) ? sanitize_text_field(wp_unslash($_POST['support_order'])) : '';
    $device  = isset($_POST['support_device']) ? sanitize_text_field(wp_unslash($_POST['support_device'])) : '';
    $issue   = isset($_POST['support_issue']) ? sanitize_text_field(wp_unslash($_POST['support_issue'])) : '';
    $message = isset($_POST['support_message']) ? trim(wp_kses_post(wp_unslash($_POST['support_message']))) : '';

    if ($name === '' || $email === '' || $message === '' || !is_email($email)) {
        wp_safe_redirect(add_query_arg('support_status', 'error', $redirect));
        exit;
    }

    $translator         = SVIC_Translator::instance();
    $registry           = $translator->registry('en_US');
    $device_options_en  = $registry['support']['form']['device_options'] ?? [];
    $issue_options_en   = $registry['support']['form']['issue_options'] ?? [];

    $device_label = $device !== '' && isset($device_options_en[$device]) ? $device_options_en[$device] : ($device ?: 'n/a');
    $issue_label  = $issue !== '' && isset($issue_options_en[$issue]) ? $issue_options_en[$issue] : ($issue ?: 'n/a');

    $subject = sprintf('[SVICLOUD Support] %s', $issue_label);
    $body_lines = [
        'Support request submitted via svicloudtvbox.us',
        '--------------------------------------------------',
        'Name: ' . $name,
        'Email: ' . $email,
        'Phone / WhatsApp: ' . ($phone !== '' ? $phone : 'n/a'),
        'Order number: ' . ($order !== '' ? $order : 'n/a'),
        'Device: ' . $device_label,
        'Issue category: ' . $issue_label,
        'Locale: ' . ($locale !== '' ? $locale : 'n/a'),
        '--------------------------------------------------',
        'Message:',
        $message,
    ];

    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        'From: SVICLOUD Concierge <' . svic_support_form_recipient() . '>',
        'Reply-To: ' . $name . ' <' . $email . '>',
    ];

    $sent = wp_mail(svic_support_form_recipient(), $subject, implode("\n", $body_lines), $headers);

    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('[SVIC Support] Mail send result: ' . ($sent ? 'success' : 'error'));
    }

    $status = $sent ? 'success' : 'error';
    wp_safe_redirect(add_query_arg('support_status', $status, $redirect));
    exit;
}

add_action('admin_post_svic_support_form', 'svic_handle_support_form');
add_action('admin_post_nopriv_svic_support_form', 'svic_handle_support_form');

if (!function_exists('svic_log_wp_mail_failure')) {
    function svic_log_wp_mail_failure($wp_error)
    {
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            return;
        }

        if ($wp_error instanceof WP_Error) {
            error_log('[SVIC Support] wp_mail failed: ' . $wp_error->get_error_message());
        }
    }
}

add_action('wp_mail_failed', 'svic_log_wp_mail_failure');

/**
 * Hide legacy bind-mounted theme directories from the admin theme list.
 */
function svic_filter_theme_roots($value) {
    if (!is_array($value) || !$value) {
        return $value;
    }

    $blocked_slugs = ['shared', 'svicloudtvbox'];

    foreach ($blocked_slugs as $slug) {
        if (isset($value[$slug])) {
            unset($value[$slug]);
        }
    }

    return $value;
}

add_filter('pre_set_site_transient_theme_roots', 'svic_filter_theme_roots');
add_filter('pre_site_transient_theme_roots', 'svic_filter_theme_roots');
add_filter('site_transient_theme_roots', 'svic_filter_theme_roots');

add_filter('wp_prepare_themes_for_js', function ($themes) {
    if (!is_array($themes) || !$themes) {
        return $themes;
    }

    $blocked_slugs = ['shared', 'svicloudtvbox'];

    foreach ($themes as $index => $data) {
        $slug = $data['id'] ?? ($data['stylesheet'] ?? null);
        if ($slug && in_array($slug, $blocked_slugs, true)) {
            unset($themes[$index]);
        }
    }

    return $themes;
}, 5);

add_action('admin_footer-themes.php', function () {
    $blocked_slugs = wp_json_encode(['shared', 'svicloudtvbox']);
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const slugs = <?php echo $blocked_slugs; ?>;
        const themeCards = document.querySelectorAll('.theme');
        themeCards.forEach(function (card) {
            const slug = card.getAttribute('data-slug');
            if (slug && slugs.includes(slug)) {
                card.remove();
            }
        });

        const brokenSection = document.querySelector('.broken-themes table');
        if (brokenSection) {
            slugs.forEach(function (slug) {
                brokenSection.querySelectorAll('tr').forEach(function (row) {
                    const cell = row.querySelector('td');
                    if (!cell) { return; }
                    if (cell.textContent.trim() === slug) {
                        row.remove();
                    }
                });
            });

            const rows = brokenSection.querySelectorAll('tr');
            if (rows.length <= 1) {
                brokenSection.closest('.broken-themes')?.remove();
            }
        }
    });
    </script>
    <?php
});

add_filter('wp_nav_menu_objects', function ($items, $args) {
    if (!is_array($items) || !$items) {
        return $items;
    }

    $location = $args->theme_location ?? null;
    $supported_locations = apply_filters('svic_translated_menu_locations', ['primary']);

    if (!$location || !in_array($location, (array) $supported_locations, true)) {
        return $items;
    }

    $menu_key_map = [
        'home'                 => 'header.nav.home',
        'compare'              => 'header.nav.compare',
        'blog'                 => 'header.nav.blog',
        'shop'                 => 'header.nav.shop',
        'store'                => 'header.nav.shop',
        'faq'                  => 'header.nav.faq',
        'svicloud-10p-plus'    => 'header.nav.ten_p',
        'svicloud-10p'         => 'header.nav.ten_p',
        'svicloud-10s'         => 'header.nav.ten_s',
        'svicloud-10p-plus-vs-evpad-10-pro'      => 'header.nav.compare_evpad',
        'svicloud-10p-plus-vs-unblocktech-ubox-12' => 'header.nav.compare_ubox12',
        'contact'              => 'header.nav.concierge',
        'concierge'            => 'header.nav.concierge',
        'guides'               => 'header.nav.guides',
        'guide'                => 'header.nav.guides',
        'setup-guide'          => 'guides.nav.setup',
        'guides-setup'         => 'guides.nav.setup',
        'after-setup'          => 'guides.nav.post_setup',
        'guides-after-setup'   => 'guides.nav.post_setup',
        'apps-streaming'       => 'guides.nav.apps',
        'guides-apps'          => 'guides.nav.apps',
        'support-concierge'    => 'guides.nav.support',
        'guides-support'       => 'guides.nav.support',
        'resources'            => 'guides.nav.resources',
        'guides-resources'     => 'guides.nav.resources',
        'troubleshooting'      => 'guides.nav.troubleshooting',
        'guides-troubleshooting' => 'guides.nav.troubleshooting',
        'about'                => 'header.nav.about',
        'return-policy'        => 'header.nav.return_policy',
        'returns'              => 'header.nav.return_policy',
        'legal-disclaimer'     => 'header.nav.legal',
        'order-tracking'       => 'header.nav.order_tracking',
        'account'              => 'header.nav.account',
        'my-account'           => 'header.nav.account',
    ];

    foreach ($items as $item) {
        if (!($item instanceof WP_Post)) {
            continue;
        }

        $slug_candidates = [];

        if (!empty($item->post_name)) {
            $slug_candidates[] = sanitize_title($item->post_name);
        }

        if (!empty($item->title)) {
            $slug_candidates[] = sanitize_title($item->title);
        }

        if (!empty($item->url)) {
            $path = preg_replace('#/+#', '/', trim((string) parse_url($item->url, PHP_URL_PATH), '/'));
            if ($path !== '') {
                $segments = explode('/', $path);
                $slug_candidates[] = sanitize_title(end($segments));
            }
        }

        $slug_candidates = array_filter(array_unique($slug_candidates));

        foreach ($slug_candidates as $slug) {
            if (!isset($menu_key_map[$slug])) {
                continue;
            }

            $translated = svic_translate($menu_key_map[$slug]);
            if (is_string($translated) && $translated !== '') {
                $item->title = $translated;
                break;
            }
        }
    }

    return $items;
}, 20, 2);

/**
 * Automatically append the latest blog posts as children under the Blog nav item.
 *
 * This keeps the "Blog" submenu fresh without requiring manual menu edits every time
 * a new article is published. It only affects the primary menu and respects the
 * current locale when choosing link URLs and titles.
 */
add_filter('wp_nav_menu_objects', function ($items, $args) {
    if (!is_array($items) || !$items) {
        return $items;
    }

    if (($args->theme_location ?? '') !== 'primary') {
        return $items;
    }

    $blog_page_id = (int) get_option('page_for_posts');
    if ($blog_page_id <= 0) {
        return $items;
    }

    $blog_menu_item_id = 0;
    $blog_menu_item    = null;
    foreach ($items as $item) {
        if (!($item instanceof WP_Post)) {
            continue;
        }
        if ((int) ($item->object_id ?? 0) === $blog_page_id && ($item->object ?? '') === 'page') {
            $blog_menu_item_id = (int) $item->ID;
            $blog_menu_item    = $item;
            break;
        }
    }

    if ($blog_menu_item_id <= 0) {
        return $items;
    }

    // Remove any dynamic entries we appended previously to avoid duplication.
    $items = array_values(array_filter($items, static function ($item) {
        return empty($item->svic_dynamic_blog_item);
    }));

    $recent_posts = get_posts([
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'posts_per_page' => 5,
        'no_found_rows'  => true,
        'suppress_filters' => false,
    ]);

    if (!$recent_posts) {
        return $items;
    }

    $locale = function_exists('svic_current_locale')
        ? strtolower((string) svic_current_locale())
        : '';

    $max_menu_order = 0;
    foreach ($items as $item) {
        if (!empty($item->menu_order) && (int) $item->menu_order > $max_menu_order) {
            $max_menu_order = (int) $item->menu_order;
        }
    }

    if ($blog_menu_item instanceof WP_Post) {
        if (!is_array($blog_menu_item->classes)) {
            $blog_menu_item->classes = [];
        }
        if (!in_array('menu-item-has-children', $blog_menu_item->classes, true)) {
            $blog_menu_item->classes[] = 'menu-item-has-children';
        }
    }

    foreach ($recent_posts as $index => $post) {
        $title = get_the_title($post);
        if (strpos($locale, 'zh') === 0) {
            $translated = get_post_meta($post->ID, '_svic_title_zh_tw', true);
            if (is_string($translated) && $translated !== '') {
                $title = $translated;
            }
        }

        $url = get_permalink($post);
        if (function_exists('svic_url_with_lang')) {
            $url = svic_url_with_lang($url);
        }

        $nav_object = (object) [
            'ID'               => 0,
            'db_id'            => 0,
            'post_type'        => 'nav_menu_item',
            'menu_item_parent' => $blog_menu_item_id,
            'object_id'        => (string) $post->ID,
            'object'           => 'post',
            'type'             => 'post_type',
            'type_label'       => __('Post'),
            'title'            => $title,
            'post_title'       => $title,
            'post_name'        => sanitize_title($title),
            'url'              => $url,
            'target'           => '',
            'attr_title'       => '',
            'description'      => '',
            'xfn'              => '',
            'status'           => 'publish',
            'classes'          => [
                'menu-item',
                'menu-item-type-post',
                'menu-item-object-post',
                'menu-item-svic-dynamic',
            ],
        ];

        $nav_item = wp_setup_nav_menu_item($nav_object);
        $nav_item->menu_item_parent = $blog_menu_item_id;
        $nav_item->menu_order = $max_menu_order + $index + 1;
        $nav_item->svic_dynamic_blog_item = true;

        $items[] = $nav_item;
    }

    return $items;
}, 30, 2);

add_filter('nav_menu_link_attributes', function ($atts, $item, $args, $depth) {
    if (is_admin()) {
        return $atts;
    }

    if (!isset($atts['href']) || !is_string($atts['href']) || $atts['href'] === '') {
        return $atts;
    }

    if (stripos($atts['href'], 'lang=') !== false) {
        return $atts;
    }

    $atts['href'] = svic_url_with_lang($atts['href']);

    return $atts;
}, 10, 4);

// Ensure robots.txt advertises the WordPress sitemap
add_filter('robots_txt', function ($output, $public) {
    // Respect site visibility setting; only append when public
    if ((int) get_option('blog_public', 1) !== 1) {
        return $output;
    }

    // Prefer plugin sitemaps when available to avoid redirects.
    if (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION')) {
        $sitemapUrl = home_url('/sitemap_index.xml');
    } else {
        $sitemapUrl = home_url('/wp-sitemap.xml');
    }
    $line = 'Sitemap: ' . esc_url_raw($sitemapUrl);

    // Avoid duplicate lines if a plugin already added it
    if (stripos($output, 'Sitemap:') === false) {
        $output = rtrim((string) $output) . "\n" . $line . "\n";
    }

    return $output;
}, 10, 2);

// Exclude sensitive or utility pages from the core sitemap
add_filter('wp_sitemaps_posts_query_args', function ($args, $postType) {
    if ($postType !== 'page') {
        return $args;
    }

    $slugsToExclude = ['my-account'];
    $idsToExclude   = [];

    foreach ($slugsToExclude as $slug) {
        $page = get_page_by_path($slug, OBJECT, 'page');
        if ($page instanceof WP_Post) {
            $idsToExclude[] = (int) $page->ID;
        }
    }

    if ($idsToExclude === []) {
        return $args;
    }

    $notIn                 = isset($args['post__not_in']) ? (array) $args['post__not_in'] : [];
    $args['post__not_in']  = array_unique(array_merge($notIn, $idsToExclude));

    return $args;
}, 10, 2);

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

add_filter('woocommerce_has_cart_block', '__return_false', 5);
add_filter('woocommerce_has_checkout_block', '__return_false', 5);

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

    $css_files = [
        'style'            => 'assets/css/style.css',
        'front-page'       => 'assets/css/front-page.css',
        'about'            => 'assets/css/about.css',
        'guides'           => 'assets/css/guides.css',
        'contact'          => 'assets/css/contact.css',
        'return-policy'    => 'assets/css/return-policy.css',
        'faq'              => 'assets/css/faq.css',
        'support'          => 'assets/css/support.css',
        'compare'          => 'assets/css/compare.css',
        'blog'             => 'assets/css/blog.css',
        'woocommerce'      => 'assets/css/woocommerce.css',
    ];

    $css_versions = [];
    foreach ($css_files as $key => $relative_path) {
        $full_path = get_template_directory() . '/' . $relative_path;
        if (! file_exists($full_path)) {
            continue;
        }

        $mtime = (int) filemtime($full_path);
        $css_versions[$key] = $deploy_version ? max($deploy_version, $mtime) : ($mtime ?: $theme_version);
    }

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

    // Determine which contextual bundles should load for this request.
    $is_front_page = is_front_page() || is_page_template('front-page.php');
    $is_about_page = is_page_template('page-about.php') || is_page('about');
    $is_guides_page = is_page_template('page-guides.php') || is_page('guides');
    if (! $is_guides_page) {
        $guide_section_slugs = array_map(static function ($item) {
            return isset($item['slug']) ? sanitize_title($item['slug']) : null;
        }, (array) svic_guides_get_anchor_items());

        $guide_section_slugs = array_values(array_filter(array_unique($guide_section_slugs)));

        if ($guide_section_slugs) {
            $is_guides_page = is_page($guide_section_slugs) || is_page_template('page-guide-section.php');
        }
    }
    $is_contact_page = is_page_template('page-contact.php') || is_page('contact');
    $is_return_policy_page = is_page_template('page-return-policy.php')
        || is_page('return-policy')
        || is_page('returns');
    $is_legal_disclaimer_page = is_page_template('page-legal-disclaimer.php')
        || is_page('legal-disclaimer');
    $is_policy_page = $is_return_policy_page || $is_legal_disclaimer_page;
    $is_support_page = is_page_template('page-support.php') || is_page('support');
    $is_faq_page = is_page_template('page-faq.php') || is_page('faq');
    $is_compare_page = is_page_template('page-compare.php') || is_page('compare');
    $is_blog_post    = is_singular('post');
    $is_blog_listing = is_home() || is_post_type_archive('post') || is_category() || is_tag() || is_author() || is_date();
    $is_blog_context = $is_blog_post || $is_blog_listing;

    // WooCommerce bundle
    $is_woo_request = false;
    if (class_exists('WooCommerce')) {
        foreach (['is_woocommerce', 'is_cart', 'is_checkout', 'is_account_page'] as $fn) {
            if (function_exists($fn) && $fn()) {
                $is_woo_request = true;
                break;
            }
        }
    }

    $style_conditions = [
        'svicloudtvbox-style' => [
            'key'       => 'style',
            'condition' => true,
            'deps'      => ['svicloudtvbox-fonts'],
        ],
        'svicloudtvbox-front-page' => [
            'key'       => 'front-page',
            'condition' => $is_front_page,
            'deps'      => ['svicloudtvbox-style'],
        ],
        'svicloudtvbox-about' => [
            'key'       => 'about',
            'condition' => $is_about_page,
            'deps'      => ['svicloudtvbox-style'],
        ],
        'svicloudtvbox-guides' => [
            'key'       => 'guides',
            'condition' => $is_guides_page,
            'deps'      => ['svicloudtvbox-style'],
        ],
        'svicloudtvbox-contact' => [
            'key'       => 'contact',
            'condition' => $is_contact_page,
            'deps'      => ['svicloudtvbox-style'],
        ],
        'svicloudtvbox-return-policy' => [
            'key'       => 'return-policy',
            'condition' => $is_policy_page,
            'deps'      => ['svicloudtvbox-style'],
        ],
        'svicloudtvbox-support' => [
            'key'       => 'support',
            'condition' => $is_support_page,
            'deps'      => ['svicloudtvbox-style'],
        ],
        'svicloudtvbox-blog' => [
            'key'       => 'blog',
            'condition' => $is_blog_context,
            'deps'      => ['svicloudtvbox-style'],
        ],
        'svicloudtvbox-faq' => [
            'key'       => 'faq',
            'condition' => $is_faq_page,
            'deps'      => ['svicloudtvbox-style'],
        ],
        'svicloudtvbox-compare' => [
            'key'       => 'compare',
            'condition' => $is_compare_page,
            'deps'      => ['svicloudtvbox-style'],
        ],
        'svicloudtvbox-woocommerce' => [
            'key'       => 'woocommerce',
            'condition' => $is_woo_request || svic_is_order_tracking_request(),
            'deps'      => ['svicloudtvbox-style'],
        ],
    ];

    foreach ($style_conditions as $handle => $config) {
        $key = $config['key'];
        if (!$config['condition'] || !isset($css_versions[$key]) || !isset($css_files[$key])) {
            continue;
        }

        wp_enqueue_style(
            $handle,
            get_template_directory_uri() . '/' . $css_files[$key],
            $config['deps'],
            $css_versions[$key]
        );
    }

    // Theme script
    wp_enqueue_script(
        'svicloudtvbox-script',
        get_template_directory_uri() . '/assets/js/theme.js',
        ['jquery'],
        $js_version,
        true
    );

    $currentLocale = svic_current_locale();

    wp_localize_script('svicloudtvbox-script', 'svicTheme', [
        'ajaxUrl'  => admin_url('admin-ajax.php'),
        'homeUrl'  => svic_url_with_lang(home_url('/')),
        'isWoo'    => class_exists('WooCommerce'),
        'themeUrl' => get_template_directory_uri(),
        'locale'   => $currentLocale,
        'translations' => SVIC_Translator::instance()->registry($currentLocale),
        'i18n'     => [
            'addingToCart'      => svic_translate_html('core.cart.adding'),
            'addedToCart'       => svic_translate_html('core.cart.added'),
            'cartError'         => svic_translate_html('core.cart.error'),
            'cartCountEmpty'    => svic_translate_html('core.cart.count_label_empty'),
            'cartCountSingle'   => svic_translate_html('core.cart.count_label_single'),
            'cartCountPlural'   => svic_translate_html('core.cart.count_label_plural'),
        ],
    ]);
});

add_action('wp_enqueue_scripts', function () {
    if (!is_front_page()) {
        return;
    }

    $styles_to_remove = [
        'wp-block-library',
        'wp-block-library-theme',
        'wc-block-style',
        'wc-address-autocomplete',
        'hostinger-reach-subscription-block',
        'brands-styles',
        'mediaelement',
        'wp-mediaelement',
        'global-styles',
        'classic-theme-styles',
    ];

    foreach ($styles_to_remove as $handle) {
        if (wp_style_is($handle, 'enqueued')) {
            wp_dequeue_style($handle);
        }
    }
}, 100);

add_action('wp_enqueue_scripts', function () {
    if (!is_front_page()) {
        return;
    }

    $script_handles = [
        'jquery',
        'jquery-core',
        'jquery-migrate',
    ];

    foreach ($script_handles as $handle) {
        if (wp_script_is($handle, 'enqueued')) {
            wp_script_add_data($handle, 'group', 1);
            wp_script_add_data($handle, 'strategy', 'defer');
        }
    }

    if (wp_script_is('svicloudtvbox-script', 'enqueued')) {
        wp_script_add_data('svicloudtvbox-script', 'strategy', 'defer');
    }
}, 20);

// Provide accessible text for checkout payment method icons.
add_filter('woocommerce_gateway_icon', function ($icon_html, $gateway_id) {
    if (strpos($icon_html, 'wc-stripe-card-icons-container') === false) {
        return $icon_html;
    }

    if (! preg_match_all('/alt="([^"]+)"/i', $icon_html, $matches)) {
        return $icon_html;
    }

    $labels = array_unique(array_filter(array_map(static function ($label) {
        $decoded = html_entity_decode($label, ENT_QUOTES, 'UTF-8');
        $clean = wp_strip_all_tags($decoded);
        return $clean !== '' ? $clean : null;
    }, $matches[1])));

    if (! $labels) {
        return $icon_html;
    }

    $icon_html = preg_replace(
        '/<img([^>]*?)alt="([^"]*)"([^>]*?)>/i',
        '<img$1alt="$2"$3 aria-hidden="true" />',
        $icon_html
    );

    $sr_text = sprintf(
        /* translators: %s: comma-separated list of accepted payment card brands. */
        __('Accepted cards: %s', 'svicloudtvbox-lumen'),
        implode(', ', $labels)
    );

    return $icon_html . '<span class="screen-reader-text">' . esc_html($sr_text) . '</span>';
}, 10, 2);

// Ensure cart totals are calculated before rendering our custom checkout
// template (especially when we replace the Checkout block content via
// render_block below). Some environments defer calculation until late in the
// request; calculating here guarantees the order review shows correct items
// and totals on first paint.
add_action('template_redirect', function () {
    if (function_exists('is_checkout') && is_checkout() && function_exists('WC')) {
        $wc = WC();
        if ($wc && isset($wc->cart) && $wc->cart) {
            try {
                $wc->cart->calculate_totals();
            } catch (Throwable $e) {
                // Avoid breaking the page if a gateway hooks into totals.
            }
        }
    }
}, 20);

/**
 * Legacy URL guardrails for blog posts that referenced older slugs/files.
 *
 * The crawler still hits /support/setup-guide/ (and the zh version) plus an
 * outdated marketing .md path. Redirect them permanently to the current
 * destinations so future internal/external links inherit the fix.
 */
add_action('template_redirect', function () {
    if (!is_404()) {
        return;
    }

    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $path = $uri ? wp_parse_url($uri, PHP_URL_PATH) : '';

    if (class_exists('SVIC_Locale_Resolver')) {
        $original = SVIC_Locale_Resolver::originalRequestPath();
        if (is_string($original) && $original !== '') {
            $path = $original;
        }
    }
    if (!is_string($path) || $path === '') {
        return;
    }

    $path = rtrim($path, '/');
    $redirects = [
        '/support/setup-guide' => [
            'path' => '/guides-setup/',
            'lang' => 'en',
        ],
        '/zh/support/setup-guide' => [
            'path' => '/guides-setup/',
            'lang' => 'zh',
        ],
        '/zh/marketing/google-review-stars-playbook.md' => [
            'path' => '/svicloud-tv-box-us-guide/',
            'lang' => 'zh',
        ],
    ];

    if (!isset($redirects[$path])) {
        return;
    }

    $target = $redirects[$path];
    $lang = is_array($target) ? ($target['lang'] ?? null) : null;
    $target_path = is_array($target) ? ($target['path'] ?? '') : (string) $target;

    if ($target_path === '') {
        return;
    }

    if (function_exists('svic_url_with_lang')) {
        $destination = svic_url_with_lang($target_path, $lang);
    }

    if (empty($destination)) {
        $destination = home_url($target_path);
    }

    wp_safe_redirect($destination, 301);
    exit;
}, 30);

// Redirect to a stable order summary page after purchase.
// This ensures users always land on the order-received view, even if a gateway
// or plugin tries to keep them on checkout (which can be confusing when totals
// were recalculated).
add_filter('woocommerce_get_checkout_order_received_url', function ($url, $order) {
    if (! $order || ! is_object($order)) {
        return $url;
    }

    // Prefer the canonical checkout "order-received" endpoint with the key.
    $base = wc_get_page_permalink('checkout');
    if ($base) {
        $canonical = wc_get_endpoint_url('order-received', (string) $order->get_id(), $base);
        $canonical = add_query_arg('key', $order->get_order_key(), $canonical);

        // Preserve language param for our site locales.
        if (function_exists('svic_url_with_lang')) {
            $canonical = svic_url_with_lang($canonical);
        }

        return $canonical;
    }

    return $url;
}, 10, 2);

add_filter('render_block', function ($blockContent, $block) {
    if (is_admin()) {
        return $blockContent;
    }

    if (! function_exists('is_checkout') || ! is_checkout()) {
        return $blockContent;
    }

    if (function_exists('is_order_received_page') && is_order_received_page()) {
        return $blockContent;
    }

    if (function_exists('is_checkout_pay_page') && is_checkout_pay_page()) {
        return $blockContent;
    }

    if (!is_array($block) || ($block['blockName'] ?? '') !== 'woocommerce/checkout') {
        return $blockContent;
    }

    $checkout = function_exists('WC') ? WC()->checkout() : null;

    ob_start();
    wc_get_template('checkout/form-checkout.php', [
        'checkout' => $checkout,
    ]);

    return ob_get_clean();
}, 10, 2);

if (!function_exists('svic_theme_favicon_path')) {
    function svic_theme_favicon_path(): string
    {
        return get_template_directory() . '/assets/images/favicon.png';
    }
}

if (!function_exists('svic_theme_favicon_url')) {
    function svic_theme_favicon_url(): string
    {
        return get_template_directory_uri() . '/assets/images/favicon.png';
    }
}

if (!function_exists('svic_theme_output_favicons')) {
    function svic_theme_output_favicons(): void
    {
        if (function_exists('has_site_icon') && has_site_icon()) {
            return;
        }

        $path = svic_theme_favicon_path();
        if (!file_exists($path)) {
            return;
        }

        $url = esc_url(svic_theme_favicon_url());
        echo "
    <link rel=\"icon\" href=\"{$url}\" sizes=\"32x32\" />
    <link rel=\"apple-touch-icon\" href=\"{$url}\" />
";
    }

    add_action('wp_head', 'svic_theme_output_favicons', 5);
    add_action('admin_head', 'svic_theme_output_favicons', 5);
    add_action('login_head', 'svic_theme_output_favicons', 5);
}

if (!function_exists('svic_theme_serve_favicon')) {
    function svic_theme_serve_favicon(): void
    {
        if (function_exists('has_site_icon') && has_site_icon()) {
            return;
        }

        $path = svic_theme_favicon_path();
        if (!file_exists($path)) {
            return;
        }

        if (!headers_sent()) {
            header('Content-Type: image/png');
            header('Content-Length: ' . (string) filesize($path));
        }

        readfile($path);
        exit;
    }

    add_action('do_favicon', 'svic_theme_serve_favicon');
    add_action('template_redirect', function () {
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        if ($request_uri === '/favicon.ico' || $request_uri === 'favicon.ico') {
            svic_theme_serve_favicon();
        }
    }, 0);
}


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
}, 30);

add_filter('woocommerce_countries_allowed_countries', function ($countries) {
    if (!is_array($countries)) {
        return $countries;
    }

    $label = $countries['US'] ?? null;

    if ($label === null && function_exists('WC')) {
        $wc = WC();
        if ($wc && isset($wc->countries, $wc->countries->countries['US'])) {
            $label = $wc->countries->countries['US'];
        }
    }

    if ($label === null) {
        $label = __('United States (US)', 'woocommerce');
    }

    return ['US' => $label];
});

add_filter('woocommerce_countries_shipping_countries', function ($countries) {
    if (!is_array($countries)) {
        return $countries;
    }

    $label = $countries['US'] ?? null;

    if ($label === null && function_exists('WC')) {
        $wc = WC();
        if ($wc && isset($wc->countries, $wc->countries->countries['US'])) {
            $label = $wc->countries->countries['US'];
        }
    }

    if ($label === null) {
        $label = __('United States (US)', 'woocommerce');
    }

    return ['US' => $label];
});

add_filter('default_checkout_billing_country', function ($country) {
    return $country ?: 'US';
});

add_filter('default_checkout_shipping_country', function ($country) {
    return $country ?: 'US';
});



add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {
    if (!function_exists('svic_header_cart_link') || !function_exists('svic_header_cart_count_markup')) {
        return $fragments;
    }

    if (!is_array($fragments)) {
        $fragments = [];
    }

    $fragments['a[data-cart-link]'] = svic_header_cart_link();
    $fragments['span[data-cart-count]'] = svic_header_cart_count_markup();

    return $fragments;
});

// Remove default WooCommerce notices from order tracking pages
// Our custom template handles notice display with better positioning
add_action('template_redirect', function () {
    if (!svic_is_order_tracking_request()) {
        return;
    }

    // Remove the default WooCommerce notice output
    remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
    remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
}, 10);

add_action('init', function () {
    $meta_keys = [
        '_svic_content_zh_tw',
        '_svic_title_zh_tw',
        '_svic_description_zh_tw',
        '_svic_keywords_zh_tw',
        '_svic_keywords_en_us',
    ];

    foreach ($meta_keys as $meta_key) {
        register_post_meta('post', $meta_key, [
            'type'           => 'string',
            'single'         => true,
            'show_in_rest'   => true,
            'auth_callback'  => '__return_true',
        ]);
    }
});

add_filter('the_content', function ($content) {
    if (!is_singular('post')) {
        return $content;
    }

    if (!function_exists('svic_current_locale')) {
        return $content;
    }

    $locale = strtolower(svic_current_locale());
    if (strpos($locale, 'zh') !== 0) {
        return $content;
    }

    $translated = get_post_meta(get_the_ID(), '_svic_content_zh_tw', true);
    if (!is_string($translated) || $translated === '') {
        return $content;
    }

    if (class_exists('SVIC_Markdown') && !SVIC_Markdown::looks_like_html($translated)) {
        $translated = SVIC_Markdown::to_html($translated);
    }

    $translated = svic_replace_inline_code_placeholders($translated, get_the_ID());

    $safe_html = wp_kses_post($translated);
    return $safe_html !== '' ? $safe_html : $content;
}, 20);

add_filter('the_title', function ($title, $post_id) {
    if (!is_singular('post') || get_the_ID() !== $post_id) {
        return $title;
    }

    if (!function_exists('svic_current_locale')) {
        return $title;
    }

    $locale = strtolower(svic_current_locale());
    if (strpos($locale, 'zh') !== 0) {
        return $title;
    }

    $translated = get_post_meta($post_id, '_svic_title_zh_tw', true);
    return is_string($translated) && $translated !== '' ? $translated : $title;
}, 10, 2);

add_filter('get_the_excerpt', function ($excerpt, $post) {
    if (!($post instanceof WP_Post) || $post->post_type !== 'post') {
        return $excerpt;
    }

    if (!function_exists('svic_current_locale')) {
        return $excerpt;
    }

    $locale = strtolower(svic_current_locale());
    if (strpos($locale, 'zh') !== 0) {
        return $excerpt;
    }

    $translated = get_post_meta($post->ID, '_svic_description_zh_tw', true);
    return is_string($translated) && $translated !== '' ? $translated : $excerpt;
}, 10, 2);

add_filter('woocommerce_short_description', function ($excerpt) {
    if (!function_exists('svic_current_locale')) {
        return $excerpt;
    }

    global $product;
    if (!$product instanceof WC_Product) {
        $product = function_exists('wc_get_product') ? wc_get_product(get_the_ID()) : null;
    }

    if (!$product instanceof WC_Product) {
        return $excerpt;
    }

    $slug = $product->get_slug();
    $translation_key = 'products.' . $slug . '.short_description';
    $translated = svic_translate_rich($translation_key);

    if (!is_string($translated) || $translated === '' || $translated === 'short_description') {
        return $excerpt;
    }

    return wp_kses_post($translated);
}, 10);

add_filter('the_content', function ($content) {
    if (!is_singular('post')) {
        return $content;
    }

    if (!function_exists('svic_url_with_lang')) {
        return $content;
    }

    $install_url = esc_url(svic_url_with_lang(home_url('/guides-setup/')));
    $compare_url = esc_url(svic_url_with_lang(home_url('/svicloud-10p-plus-vs-unblocktech-ubox-12/')));

    $search = [
        '../how-to-set-up-svicloud-tv-box.md',
        'http://../how-to-set-up-svicloud-tv-box-zh.md',
        '../svicloud-10p-plus-vs-unblocktech-ubox-12.md',
        '$219–$239 USD (official U.S. store)',
        '$219–$239 美元（官方美國網站）',
    ];

    $replace = [
        $install_url,
        $install_url,
        $compare_url,
        '$249–$359 USD (official U.S. store)',
        '$249–$359 美元（官方美國網站）',
    ];

    return str_replace($search, $replace, $content);
}, 30);

add_filter('wp_nav_menu_objects', function ($items) {
    if (!function_exists('svic_current_locale')) {
        return $items;
    }

    $locale = strtolower(svic_current_locale());
    if (strpos($locale, 'zh') !== 0) {
        return $items;
    }

    $overrides = [
        665 => '小雲電視盒 10P+ vs 安博電視盒 12 代',
        664 => '小雲電視盒 10P+ vs EVPAD 10 系列',
    ];

    foreach ($items as $item) {
        $id = isset($item->object_id) ? (int) $item->object_id : 0;
        if ($id && isset($overrides[$id])) {
            $item->title = esc_html($overrides[$id]);
        }
    }

    return $items;
}, 20);

function svic_replace_inline_code_placeholders(string $html, int $post_id): string
{
    if (strpos($html, '{{SVIC') === false) {
        return $html;
    }

    $source_content = get_post_field('post_content', $post_id);
    $code_texts     = [];
    if (is_string($source_content) && $source_content !== '') {
        if (preg_match_all('/<code>(.*?)<\/code>/s', $source_content, $code_matches)) {
            foreach ($code_matches[1] as $code) {
                $code_texts[] = html_entity_decode(wp_strip_all_tags($code), ENT_QUOTES, get_bloginfo('charset') ?: 'UTF-8');
            }
        }
    }

    $normalized = preg_replace_callback('/\{\{([^}]*)\}\}/', function ($match) {
        $inner = strip_tags($match[1]);
        $inner = preg_replace('/\s+/', '', $inner);
        return '{{' . $inner . '}}';
    }, $html);

    $cursor = 0;
    $html = preg_replace_callback('/\{\{SVICCODE(\d+)\}\}/i', function ($match) use (&$cursor, $code_texts) {
        $explicit_index = (int) $match[1];
        $value = $code_texts[$cursor] ?? ($code_texts[$explicit_index] ?? '');
        if ($value === '') {
            $value = trim($match[0], '{}');
        }
        $cursor++;
        return '<code>' . esc_html($value) . '</code>';
    }, $normalized);

    return $html;
}

if (!function_exists('svic_get_theme_deploy_marker')) {
    /**
     * Returns a lightweight marker that updates whenever we deploy the theme.
     */
    function svic_get_theme_deploy_marker(): string
    {
        $theme_dir = get_template_directory();
        $deploy_file = $theme_dir . '/.deploy-version';
        if (file_exists($deploy_file)) {
            $raw = trim((string) @file_get_contents($deploy_file));
            if ($raw !== '') {
                return $raw;
            }
        }

        if (function_exists('wp_get_theme')) {
            $theme = wp_get_theme();
            if ($theme && $theme->exists()) {
                $version = (string) $theme->get('Version');
                if ($version !== '') {
                    return $version;
                }
            }
        }

        $stylesheet_dir = function_exists('get_stylesheet_directory') ? get_stylesheet_directory() : $theme_dir;
        $mtime = is_string($stylesheet_dir) && $stylesheet_dir !== '' ? @filemtime($stylesheet_dir) : false;
        if (is_int($mtime) && $mtime > 0) {
            return (string) $mtime;
        }

        return (string) time();
    }
}

if (!function_exists('svic_rank_math_sitemap_rules_missing')) {
    function svic_rank_math_sitemap_rules_missing(): bool
    {
        if (!defined('RANK_MATH_VERSION')) {
            return false;
        }

        $rules = get_option('rewrite_rules');
        if (!is_array($rules) || $rules === []) {
            return true;
        }

        foreach ($rules as $regex => $query) {
            if (!is_string($regex) || stripos($regex, 'sitemap') === false) {
                continue;
            }

            if (is_string($query) && strpos($query, 'rank-math-sitemap') !== false) {
                return false;
            }
        }

        return true;
    }
}

if (!function_exists('svic_is_legacy_sitemap_request')) {
    function svic_is_legacy_sitemap_request(): bool
    {
        $candidates = [];

        if (class_exists('SVIC_Locale_Resolver')) {
            if (method_exists('SVIC_Locale_Resolver', 'originalRequestPath')) {
                $candidates[] = SVIC_Locale_Resolver::originalRequestPath();
            }
            if (method_exists('SVIC_Locale_Resolver', 'currentRequestPath')) {
                $candidates[] = SVIC_Locale_Resolver::currentRequestPath();
            }
        }

        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        if (is_string($request_uri) && $request_uri !== '') {
            $parsed = wp_parse_url($request_uri);
            if (isset($parsed['path']) && is_string($parsed['path'])) {
                $candidates[] = $parsed['path'];
            }
        }

        $redirect_url = $_SERVER['REDIRECT_URL'] ?? '';
        if (is_string($redirect_url) && $redirect_url !== '') {
            $candidates[] = $redirect_url;
        }

        foreach ($candidates as $path) {
            if (!is_string($path) || $path === '') {
                continue;
            }

            $normalized = strtolower($path);
            $normalized = $normalized === '/' ? '/' : rtrim($normalized, '/');

            if ($normalized === '/sitemap_index.xml' || $normalized === '/zh/sitemap_index.xml') {
                return true;
            }
        }

        return false;
    }
}

add_action('template_redirect', function () {
    if (!svic_is_legacy_sitemap_request()) {
        return;
    }

    if (!function_exists('wp_sitemaps_get_server')) {
        return;
    }

    $server = wp_sitemaps_get_server();
    if (!is_object($server) || !method_exists($server, 'render_index')) {
        return;
    }

    $server->render_index();
    exit;
}, 0);

if (!function_exists('svic_flush_rewrites_once_for_sitemaps')) {
    /**
     * Flush rewrite rules one time per deployment so sitemap routes stay intact.
     */
    function svic_flush_rewrites_once_for_sitemaps(): void
    {
        if (is_admin() || defined('REST_REQUEST') || (defined('WP_CLI') && WP_CLI)) {
            return;
        }

        $marker = svic_get_theme_deploy_marker();

        $stored = get_option('svic_rank_math_sitemap_rewrite_flushed');
        $stored_marker = '';

        if (is_array($stored)) {
            $stored_marker = isset($stored['marker']) ? (string) $stored['marker'] : '';
        } elseif (is_string($stored) && $stored !== '') {
            $stored_marker = $stored;
        } elseif (is_numeric($stored)) {
            $stored_marker = (string) $stored;
        }

        $force_flush = svic_rank_math_sitemap_rules_missing();

        if (!$force_flush && $stored_marker === $marker) {
            return;
        }

        flush_rewrite_rules(false);

        $payload = [
            'marker'     => $marker,
            'flushed_at' => time(),
            'reason'     => $force_flush ? 'auto-missing-rules' : 'deploy',
        ];

        update_option('svic_rank_math_sitemap_rewrite_flushed', $payload, false);
    }

    add_action('init', 'svic_flush_rewrites_once_for_sitemaps', 21);
}
