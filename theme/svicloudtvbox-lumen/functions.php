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

require_once get_template_directory() . '/inc/class-svic-translator.php';

require_once get_template_directory() . '/inc/class-svic-locale-resolver.php';
require_once get_template_directory() . '/inc/guides-data.php';
require_once get_template_directory() . '/inc/theme-maintenance.php';
require_once get_template_directory() . '/inc/helpers-svic.php';

SVIC_Locale_Resolver::bootstrap();

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

        if (!function_exists('svic_current_base_url') || !function_exists('svic_url_with_lang')) {
            return;
        }

        $base = svic_current_base_url();
        if (!is_string($base) || $base === '') {
            return;
        }

        // Localize to current language; strip query/fragment for canonical.
        $localized = svic_url_with_lang($base);
        $parts     = wp_parse_url($localized);
        if (!is_array($parts)) {
            return;
        }

        $path = isset($parts['path']) && is_string($parts['path']) ? $parts['path'] : '/';
        $canonical = home_url($path);

        // Normalize trailing slash to match WordPress behavior.
        if ($canonical !== home_url('/') && substr($canonical, -1) !== '/') {
            $canonical .= '/';
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
    if (!function_exists('svic_current_base_url') || !function_exists('svic_url_with_lang')) {
        return $url;
    }
    $base = svic_current_base_url();
    if (!is_string($base) || $base === '') {
        return $url;
    }
    $localized = svic_url_with_lang($base);
    $parts     = wp_parse_url($localized);
    if (!is_array($parts)) {
        return $url;
    }
    $path = isset($parts['path']) && is_string($parts['path']) ? $parts['path'] : '/';
    $canonical = home_url($path);
    if ($canonical !== home_url('/') && substr($canonical, -1) !== '/') {
        $canonical .= '/';
    }
    return $canonical;
});

// RankMath SEO
add_filter('rank_math/frontend/canonical', function ($url) {
    if (is_admin()) {
        return $url;
    }
    if (!function_exists('svic_current_base_url') || !function_exists('svic_url_with_lang')) {
        return $url;
    }
    $base = svic_current_base_url();
    if (!is_string($base) || $base === '') {
        return $url;
    }
    $localized = svic_url_with_lang($base);
    $parts     = wp_parse_url($localized);
    if (!is_array($parts)) {
        return $url;
    }
    $path = isset($parts['path']) && is_string($parts['path']) ? $parts['path'] : '/';
    $canonical = home_url($path);
    if ($canonical !== home_url('/') && substr($canonical, -1) !== '/') {
        $canonical .= '/';
    }
    return $canonical;
}, 10, 1);

add_filter('body_class', function ($classes) {
    if (!is_array($classes)) {
        $classes = [];
    }

    if (function_exists('svic_current_locale')) {
        $locale = svic_current_locale();
        if (is_string($locale) && stripos($locale, 'zh') === 0) {
            if (!in_array('lang-zh', $classes, true)) {
                $classes[] = 'lang-zh';
            }
        }
    }

    return $classes;
});

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
}, 20);

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
        'faq'                  => 'header.nav.faq',
        'svicloud-10p-plus'    => 'header.nav.ten_p',
        'svicloud-10p'         => 'header.nav.ten_p',
        'svicloud-10s'         => 'header.nav.ten_s',
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
        'style'       => 'assets/css/style.css',
        'front-page'  => 'assets/css/front-page.css',
        'about'       => 'assets/css/about.css',
        'guides'      => 'assets/css/guides.css',
        'contact'     => 'assets/css/contact.css',
        'faq'         => 'assets/css/faq.css',
        'support'     => 'assets/css/support.css',
        'compare'     => 'assets/css/compare.css',
        'woocommerce' => 'assets/css/woocommerce.css',
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

    // Base theme styles
    if (isset($css_versions['style'])) {
        wp_enqueue_style(
            'svicloudtvbox-style',
            get_template_directory_uri() . '/assets/css/style.css',
            ['svicloudtvbox-fonts'],
            $css_versions['style']
        );
    }

    // Homepage / marketing bundle
    $is_front_page = is_front_page() || is_page_template('front-page.php');
    if ($is_front_page && isset($css_versions['front-page'])) {
        wp_enqueue_style(
            'svicloudtvbox-front-page',
            get_template_directory_uri() . '/assets/css/front-page.css',
            ['svicloudtvbox-style'],
            $css_versions['front-page']
        );
    }

    // About page bundle
    $is_about_page = is_page_template('page-about.php') || is_page('about');
    if ($is_about_page && isset($css_versions['about'])) {
        wp_enqueue_style(
            'svicloudtvbox-about',
            get_template_directory_uri() . '/assets/css/about.css',
            ['svicloudtvbox-style'],
            $css_versions['about']
        );
    }

    // Guides page bundle (overview + detailed sections)
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

    if ($is_guides_page && isset($css_versions['guides'])) {
        wp_enqueue_style(
            'svicloudtvbox-guides',
            get_template_directory_uri() . '/assets/css/guides.css',
            ['svicloudtvbox-style'],
            $css_versions['guides']
        );
    }

    // Contact page bundle
    $is_contact_page = is_page_template('page-contact.php') || is_page('contact');
    if ($is_contact_page && isset($css_versions['contact'])) {
        wp_enqueue_style(
            'svicloudtvbox-contact',
            get_template_directory_uri() . '/assets/css/contact.css',
            ['svicloudtvbox-style'],
            $css_versions['contact']
        );
    }

    // Support form bundle
    $is_support_page = is_page_template('page-support.php') || is_page('support');
    if ($is_support_page && isset($css_versions['support'])) {
        wp_enqueue_style(
            'svicloudtvbox-support',
            get_template_directory_uri() . '/assets/css/support.css',
            ['svicloudtvbox-style'],
            $css_versions['support']
        );
    }

    // FAQ page bundle
    $is_faq_page = is_page_template('page-faq.php') || is_page('faq');
    if ($is_faq_page && isset($css_versions['faq'])) {
        wp_enqueue_style(
            'svicloudtvbox-faq',
            get_template_directory_uri() . '/assets/css/faq.css',
            ['svicloudtvbox-style'],
            $css_versions['faq']
        );
    }

    // Compare table bundle
    $is_compare_page = is_page_template('page-compare.php') || is_page('compare');
    if ($is_compare_page && isset($css_versions['compare'])) {
        wp_enqueue_style(
            'svicloudtvbox-compare',
            get_template_directory_uri() . '/assets/css/compare.css',
            ['svicloudtvbox-style'],
            $css_versions['compare']
        );
    }

    // WooCommerce bundle
    $is_woo_request = false;
    $is_order_tracking_page = false;
    if (class_exists('WooCommerce')) {
        foreach (['is_woocommerce', 'is_cart', 'is_checkout', 'is_account_page'] as $fn) {
            if (function_exists($fn) && $fn()) {
                $is_woo_request = true;
                break;
            }
        }
    }

    if (! $is_woo_request) {
        if (function_exists('is_wc_endpoint_url') && is_wc_endpoint_url('order-tracking')) {
            $is_order_tracking_page = true;
        } elseif (is_page('order-tracking')) {
            $is_order_tracking_page = true;
        } elseif (is_singular('page')) {
            $order_tracking_post = get_post();
            if ($order_tracking_post instanceof WP_Post) {
                if (function_exists('has_shortcode') && has_shortcode($order_tracking_post->post_content, 'woocommerce_order_tracking')) {
                    $is_order_tracking_page = true;
                } elseif (function_exists('has_block') && has_block('woocommerce/order-tracking', $order_tracking_post)) {
                    $is_order_tracking_page = true;
                }
            }
        }
    }

    if (($is_woo_request || $is_order_tracking_page) && isset($css_versions['woocommerce'])) {
        wp_enqueue_style(
            'svicloudtvbox-woocommerce',
            get_template_directory_uri() . '/assets/css/woocommerce.css',
            ['svicloudtvbox-style'],
            $css_versions['woocommerce']
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
    if (!function_exists('is_wc_endpoint_url')) {
        return;
    }

    $is_order_tracking = false;

    if (is_wc_endpoint_url('order-tracking')) {
        $is_order_tracking = true;
    } elseif (is_page('order-tracking')) {
        $is_order_tracking = true;
    } elseif (is_singular('page')) {
        $order_tracking_post = get_post();
        if ($order_tracking_post instanceof WP_Post) {
            if (function_exists('has_shortcode') && has_shortcode($order_tracking_post->post_content, 'woocommerce_order_tracking')) {
                $is_order_tracking = true;
            } elseif (function_exists('has_block') && has_block('woocommerce/order-tracking', $order_tracking_post)) {
                $is_order_tracking = true;
            }
        }
    }

    if ($is_order_tracking) {
        // Remove the default WooCommerce notice output
        remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
        remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
    }
}, 10);
