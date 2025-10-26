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
                'title'       => '小雲電視盒 美國｜SVICLOUD 10P Plus / 10S 授權經銷店',
                'description' => '美國授權小雲電視盒經銷，10P Plus 與 10S 現貨 48 小時內從加州出貨。繁體中文介面、無月費裝置、美國保固與中英雙語客服。',
                'image_alt'   => '小雲電視盒與語音遙控器',
            ],
            'zh_cn' => [
                'title'       => '小云电视盒 美国｜SVICLOUD 10P Plus / 10S 授权经销店',
                'description' => '美国授权小云电视盒经销，10P Plus 与 10S 现货 48 小时内从加州发货。繁体/简体界面，无月费设备，美国保固与双语客服。',
                'image_alt'   => '小云电视盒与语音遥控器',
            ],
            'en_us' => [
                'title'       => 'SVICLOUD TV Box US – Chinese TV Box with U.S. Warranty',
                'description' => 'Authorized U.S. dealer for SVICLOUD 10P Plus & 10S. Ships from California within 48 hours, bilingual support, no monthly device fees.',
                'image_alt'   => 'SVICLOUD streaming device with voice remote',
            ],
        ];

        if (strpos($locale, 'zh') === 0) {
            return $definitions['zh_tw'];
        }

        return $definitions[$locale] ?? $definitions['en_us'];
    }
}

if (!function_exists('svic_get_homepage_hero_image_meta')) {
    function svic_get_homepage_hero_image_meta(): array
    {
        $relative_path = '/assets/images/svicloud-hero-product.png';
        $file_path     = get_template_directory() . $relative_path;
        $url           = get_template_directory_uri() . $relative_path;
        $width         = null;
        $height        = null;

        if (file_exists($file_path)) {
            $dimensions = @getimagesize($file_path);
            if (is_array($dimensions) && isset($dimensions[0], $dimensions[1])) {
                $width  = (int) $dimensions[0];
                $height = (int) $dimensions[1];
            }
        }

        return [
            'url'    => esc_url_raw($url),
            'width'  => $width,
            'height' => $height,
        ];
    }
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
    $is_blog_post = is_singular('post');

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
            'condition' => $is_blog_post,
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
