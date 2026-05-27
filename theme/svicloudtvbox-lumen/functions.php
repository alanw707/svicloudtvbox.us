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

/**
 * Announcement bar toggle.
 * Set to true to show the out-of-stock (or any) banner across all pages.
 * Set to false to hide it when back in stock.
 */
if (!defined('SVIC_ANNOUNCEMENT_ENABLED')) {
    define('SVIC_ANNOUNCEMENT_ENABLED', false);
}

/**
 * Recent shipments strip toggle.
 * Uses WooCommerce Shipping metadata to display recent shipment estimates below the header.
 */
if (!defined('SVIC_RECENT_SHIPMENTS_ENABLED')) {
    define('SVIC_RECENT_SHIPMENTS_ENABLED', true);
}

/**
 * Render the site-wide announcement bar.
 * Called from header.php just before <header>.
 */
function svic_render_announcement_bar(): void {
    if (!SVIC_ANNOUNCEMENT_ENABLED) {
        return;
    }

    $message = svic_translate_html('announcement.message');
    $cta     = svic_translate_html('announcement.cta');
    $cta_url = svic_translate_html('announcement.cta_url');

    if (empty($message)) {
        return;
    }

    $full_cta_url = svic_url_with_lang(home_url($cta_url));
    ?>
    <div class="svic-announcement-bar" role="status" aria-live="polite">
        <div class="svic-announcement-bar__inner">
            <span class="svic-announcement-bar__icon" aria-hidden="true">⚠️</span>
            <span class="svic-announcement-bar__text"><?php echo $message; // phpcs:ignore ?></span>
            <?php if ($cta && $cta_url) : ?>
                <a class="svic-announcement-bar__cta" href="<?php echo esc_url($full_cta_url); ?>"><?php echo $cta; // phpcs:ignore ?></a>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

if (!defined('SVIC_GOOGLE_CUSTOMER_REVIEWS_MERCHANT_ID')) {
    define('SVIC_GOOGLE_CUSTOMER_REVIEWS_MERCHANT_ID', 5317978135);
}

// Official Google Customer Reviews rating badge. Google only shows a rating
// once Merchant Center has enough eligible survey responses.
if (!defined('SVIC_GOOGLE_CUSTOMER_REVIEWS_BADGE_ENABLED')) {
    define('SVIC_GOOGLE_CUSTOMER_REVIEWS_BADGE_ENABLED', true);
}

// Direct Google Business Profile "write a review" link. Set in wp-config.php
// or via `svic_google_business_review_url` filter once the GBP Place ID link is known.
if (!defined('SVIC_GOOGLE_BUSINESS_REVIEW_URL')) {
    define('SVIC_GOOGLE_BUSINESS_REVIEW_URL', '');
}

if (!defined('SVIC_GA4_MEASUREMENT_ID')) {
    define('SVIC_GA4_MEASUREMENT_ID', '');
}

if (!defined('SVIC_GOOGLE_ADS_CONVERSION_ID')) {
    define('SVIC_GOOGLE_ADS_CONVERSION_ID', 'AW-17655850932/8WyxCM_gpLQbELTP--JB');
}

if (!defined('SVIC_META_PIXEL_ID')) {
    define('SVIC_META_PIXEL_ID', '');
}

// Toggle homepage testimonials section. Flip to true once real customer quotes
// have been added to the `frontpage.testimonials.quotes` translation block.
if (!defined('SVIC_TESTIMONIALS_ENABLED')) {
    define('SVIC_TESTIMONIALS_ENABLED', false);
}

// Floating WhatsApp support chat entrypoint.
// Use international format digits only, e.g. 15551234567.
if (!defined('SVIC_SUPPORT_CHAT_ENABLED')) {
    define('SVIC_SUPPORT_CHAT_ENABLED', true);
}

if (!defined('SVIC_SUPPORT_CHAT_WHATSAPP_NUMBER')) {
    define('SVIC_SUPPORT_CHAT_WHATSAPP_NUMBER', '');
}

// Force US state names to English regardless of UI locale.
// - zh_CN WooCommerce pack translates states fully; zh_TW only translates DC — inconsistent.
// - Customers shipping within the US recognise their state in English.
// - USPS/UPS/FedEx use English state names; translated names add no practical value.
add_filter('woocommerce_states', function (array $states): array {
    $states['US'] = [
        'AL' => 'Alabama',        'AK' => 'Alaska',          'AZ' => 'Arizona',
        'AR' => 'Arkansas',       'CA' => 'California',       'CO' => 'Colorado',
        'CT' => 'Connecticut',    'DE' => 'Delaware',         'DC' => 'District of Columbia',
        'FL' => 'Florida',        'GA' => 'Georgia',          'HI' => 'Hawaii',
        'ID' => 'Idaho',          'IL' => 'Illinois',         'IN' => 'Indiana',
        'IA' => 'Iowa',           'KS' => 'Kansas',           'KY' => 'Kentucky',
        'LA' => 'Louisiana',      'ME' => 'Maine',            'MD' => 'Maryland',
        'MA' => 'Massachusetts',  'MI' => 'Michigan',         'MN' => 'Minnesota',
        'MS' => 'Mississippi',    'MO' => 'Missouri',         'MT' => 'Montana',
        'NE' => 'Nebraska',       'NV' => 'Nevada',           'NH' => 'New Hampshire',
        'NJ' => 'New Jersey',     'NM' => 'New Mexico',       'NY' => 'New York',
        'NC' => 'North Carolina', 'ND' => 'North Dakota',     'OH' => 'Ohio',
        'OK' => 'Oklahoma',       'OR' => 'Oregon',           'PA' => 'Pennsylvania',
        'RI' => 'Rhode Island',   'SC' => 'South Carolina',   'SD' => 'South Dakota',
        'TN' => 'Tennessee',      'TX' => 'Texas',            'UT' => 'Utah',
        'VT' => 'Vermont',        'VA' => 'Virginia',         'WA' => 'Washington',
        'WV' => 'West Virginia',  'WI' => 'Wisconsin',        'WY' => 'Wyoming',
        'AA' => 'Armed Forces (AA)', 'AE' => 'Armed Forces (AE)', 'AP' => 'Armed Forces (AP)',
    ];
    return $states;
});

// Fix fulfillment tracking URLs that point to AfterShip without the tracking number embedded.
// Root cause: AfterShip integration may store `_tracking_url` as `https://track.aftership.com/`
// without appending the tracking number. This filter corrects it at write time.
function svic_fix_fulfillment_tracking_url( $fulfillment ) {
    if ( ! is_object( $fulfillment ) || ! method_exists( $fulfillment, 'get_meta' ) ) {
        return $fulfillment;
    }

    $tracking_url    = $fulfillment->get_meta( '_tracking_url', true );
    $tracking_number = $fulfillment->get_meta( '_tracking_number', true );

    if ( empty( $tracking_url ) || empty( $tracking_number ) ) {
        return $fulfillment;
    }

    // Only intervene when the URL goes to AfterShip but the tracking number is absent from it.
    if ( strpos( $tracking_url, 'aftership.com' ) === false ) {
        return $fulfillment;
    }

    if ( strpos( $tracking_url, $tracking_number ) !== false ) {
        return $fulfillment; // Already correct — tracking number is already in the URL.
    }

    // USPS GS1-128 / IMpb tracking numbers start with 94/93/92/95/96.
    $provider = $fulfillment->get_meta( '_shipment_provider', true );
    if ( $provider === 'usps' || preg_match( '/^(94|93|92|95|96)\d{18,22}$/', (string) $tracking_number ) ) {
        $fixed_url = 'https://tools.usps.com/go/TrackConfirmAction?tLabels=' . rawurlencode( $tracking_number );
    } else {
        // Non-USPS: keep AfterShip but append the tracking number to the path.
        $fixed_url = rtrim( $tracking_url, '/' ) . '/' . rawurlencode( $tracking_number );
    }

    $fulfillment->update_meta_data( '_tracking_url', $fixed_url );

    return $fulfillment;
}
add_filter( 'woocommerce_fulfillment_before_create', 'svic_fix_fulfillment_tracking_url' );
add_filter( 'woocommerce_fulfillment_before_update', 'svic_fix_fulfillment_tracking_url' );

// Product reviews are visible to everyone, but submission is limited to logged-in verified buyers.
add_filter('woocommerce_review_rating_verification_required', '__return_true');
add_filter('option_woocommerce_review_rating_verification_required', static function () {
    return 'yes';
});

if (!function_exists('svic_user_can_review_product')) {
    function svic_user_can_review_product(int $product_id): bool
    {
        if ($product_id <= 0 || !is_user_logged_in() || !function_exists('wc_customer_bought_product')) {
            return false;
        }

        $user = wp_get_current_user();
        if (!$user instanceof WP_User || $user->ID <= 0) {
            return false;
        }

        return wc_customer_bought_product((string) $user->user_email, $user->ID, $product_id);
    }
}

if (!function_exists('svic_product_review_restriction_message')) {
    function svic_product_review_restriction_message(int $product_id = 0): string
    {
        if (!is_user_logged_in()) {
            $login_url = $product_id > 0 ? wp_login_url(get_permalink($product_id)) : wp_login_url();
            return sprintf(
                /* translators: %s: login URL. */
                wp_kses_post(__('Only logged-in customers who purchased this product can write a review. <a href="%s">Log in to your account</a> to continue.', 'svicloudtvbox-lumen')),
                esc_url($login_url)
            );
        }

        return esc_html__('Only customers with a verified purchase of this product can write a review.', 'svicloudtvbox-lumen');
    }
}

if (!function_exists('svic_restrict_product_review_form_to_verified_buyers')) {
    function svic_restrict_product_review_form_to_verified_buyers(array $comment_form): array
    {
        $product_id = get_the_ID();
        if ($product_id > 0 && !svic_user_can_review_product((int) $product_id)) {
            $message = '<p class="svic-review-restriction-notice">' . svic_product_review_restriction_message((int) $product_id) . '</p>';

            $comment_form['fields'] = [];
            $comment_form['comment_field'] = $message;
            $comment_form['submit_button'] = '';
            $comment_form['submit_field'] = '';
            $comment_form['title_reply'] = esc_html__('Reviews are limited to verified buyers', 'svicloudtvbox-lumen');
            $comment_form['label_submit'] = esc_html__('Submit review', 'svicloudtvbox-lumen');

            return $comment_form;
        }

        if (!empty($comment_form['comment_field'])) {
            $comment_form['comment_field'] = str_replace('rows="8"', 'rows="5"', $comment_form['comment_field']);
        }

        $comment_form['label_submit'] = esc_html__('Submit review', 'svicloudtvbox-lumen');

        return $comment_form;
    }
}
add_filter('woocommerce_product_review_comment_form_args', 'svic_restrict_product_review_form_to_verified_buyers', 20);

if (!function_exists('svic_block_unverified_product_review_submission')) {
    function svic_block_unverified_product_review_submission(array $commentdata): array
    {
        $post_id = isset($commentdata['comment_post_ID']) ? (int) $commentdata['comment_post_ID'] : 0;
        if ($post_id <= 0 || get_post_type($post_id) !== 'product') {
            return $commentdata;
        }

        if (!svic_user_can_review_product($post_id)) {
            wp_die(
                wp_kses_post(svic_product_review_restriction_message($post_id)),
                esc_html__('Review not accepted', 'svicloudtvbox-lumen'),
                ['response' => is_user_logged_in() ? 403 : 401]
            );
        }

        return $commentdata;
    }
}
add_filter('preprocess_comment', 'svic_block_unverified_product_review_submission', 10, 1);

// Force currency display as "$269.00" (no space) regardless of WC currency-position setting.
add_filter('woocommerce_price_format', function ($format, $currency_pos) {
    if ($currency_pos === 'left_space') {
        return '%1$s%2$s';
    }
    if ($currency_pos === 'right_space') {
        return '%2$s%1$s';
    }
    return $format;
}, 10, 2);

// Buy-Now flow: when ?svic_buynow=1 is present, isolate the cart to just the
// newly added item and redirect straight to checkout, bypassing the cart page.
add_action('woocommerce_add_to_cart', function (string $cart_item_key): void {
    if (empty($_GET['svic_buynow'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        return;
    }
    foreach (array_keys(WC()->cart->get_cart()) as $key) {
        if ($key !== $cart_item_key) {
            WC()->cart->remove_cart_item($key);
        }
    }
}, 10, 1);

add_filter('woocommerce_add_to_cart_redirect', function (string $url): string {
    if (empty($_GET['svic_buynow'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        return $url;
    }
    return wc_get_checkout_url();
});

const SVIC_LITESPEED_PURGE_MARK = 'svic-hero-rating-pill-20260510-11';
const SVIC_REWRITE_FLUSH_MARK   = 'svic-rewrite-flush-20260407';

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

// One-time rewrite flush to ensure alias rewrites (e.g., /compare/) take effect.
// Run after our custom rewrites have been registered.
add_action('init', function () {
    if (get_option('svic_last_rewrite_flush') === SVIC_REWRITE_FLUSH_MARK) {
        return;
    }

    if (function_exists('svic_register_route_alias_rewrites')) {
        svic_register_route_alias_rewrites();
    }

    flush_rewrite_rules(false);
    update_option('svic_last_rewrite_flush', SVIC_REWRITE_FLUSH_MARK, false);
}, 30);

require_once get_template_directory() . '/inc/class-svic-translator.php';
require_once get_template_directory() . '/inc/class-svic-markdown.php';

require_once get_template_directory() . '/inc/class-svic-locale-resolver.php';
require_once get_template_directory() . '/inc/guides-data.php';
require_once get_template_directory() . '/inc/theme-maintenance.php';
require_once get_template_directory() . '/inc/helpers-svic.php';
require_once get_template_directory() . '/inc/class-svic-recent-shipments.php';
require_once get_template_directory() . '/inc/class-svic-zh-sitemap.php';

SVIC_Locale_Resolver::bootstrap();
SVIC_Recent_Shipments::bootstrap();

if (!function_exists('svic_render_recent_shipments_strip')) {
    function svic_render_recent_shipments_strip(): void
    {
        if (!class_exists('SVIC_Recent_Shipments')) {
            return;
        }

        SVIC_Recent_Shipments::render();
    }
}

if (!function_exists('svic_get_localized_canonical_url')) {
    function svic_get_localized_canonical_url(): ?string
    {
        if (!function_exists('svic_current_base_url') || !function_exists('svic_url_with_lang')) {
            return null;
        }

        $basePath = method_exists('SVIC_Locale_Resolver', 'originalRequestPath')
            ? SVIC_Locale_Resolver::originalRequestPath()
            : null;

        if (!is_string($basePath) || $basePath === '') {
            $basePath = SVIC_Locale_Resolver::currentRequestPath();
        }

        $pathForLang = is_string($basePath) ? $basePath : '/';
        $isZhCnPath = (
            $pathForLang === '/zh-cn'
            || $pathForLang === '/zh-cn/'
            || strpos($pathForLang, '/zh-cn/') === 0
        );
        $isZhPath = (
            $pathForLang === '/zh'
            || $pathForLang === '/zh/'
            || strpos($pathForLang, '/zh/') === 0
        );
        $langValue = $isZhCnPath ? 'zh-cn' : ($isZhPath ? 'zh' : 'en');

        $base = home_url($basePath);
        if (!is_string($base) || $base === '') {
            return null;
        }

        $localized = svic_url_with_lang($base, $langValue);
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

add_filter('wp_get_attachment_image_attributes', 'svic_apply_meaningful_image_alt', 10, 3);

if (!function_exists('svic_apply_meaningful_image_alt')) {
    /**
     * Ensure every attachment image tag ships with a meaningful alt attribute.
     *
     * @param array        $attr
     * @param WP_Post|null $attachment
     * @param string|array $size
     *
     * @return array
     */
    function svic_apply_meaningful_image_alt(array $attr, $attachment, $size): array
    {
        $current_alt = isset($attr['alt']) ? trim((string) $attr['alt']) : '';
        if ($current_alt !== '') {
            return $attr;
        }

        $attr['alt'] = svic_generate_default_image_alt($attachment);

        return $attr;
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
                    'en' => 'SVICLOUD Support | 小雲盒子 客服',
                    'zh' => 'SVICLOUD Support | 小雲盒子 客服',
                    'zh-cn' => 'SVICLOUD Support | 小雲盒子 客服',
                ],
                'description' => [
                    'en' => 'Get help with setup, warranty, and troubleshooting from the SVICLOUD concierge team. 小雲盒子 客服與技術支援。',
                    'zh' => 'Get help with setup, warranty, and troubleshooting from the SVICLOUD concierge team. 小雲盒子 客服與技術支援。',
                    'zh-cn' => 'Get help with setup, warranty, and troubleshooting from the SVICLOUD concierge team. 小雲盒子 客服與技術支援。',
                ],
                'image'       => '/assets/images/hero-voice-assistant.webp',
                'image_alt'   => [
                    'en' => 'SVICLOUD concierge helping customers in Las Vegas',
                    'zh' => '小雲電視盒禮賓客服提供拉斯維加斯支援',
                    'zh-cn' => '小云电视盒礼宾客服提供拉斯维加斯支持',
                ],
            ],
            'contact' => [
                'title'       => [
                    'en' => 'Contact SVICLOUD TV Box US | 小雲盒子 聯絡我們',
                    'zh' => 'Contact SVICLOUD TV Box US | 小雲盒子 聯絡我們',
                    'zh-cn' => 'Contact SVICLOUD TV Box US | 小雲盒子 聯絡我們',
                ],
                'description' => [
                    'en' => 'Reach the SVICLOUD U.S. team for orders, setup, and warranty support. 小雲盒子 聯絡方式與服務時間。',
                    'zh' => 'Reach the SVICLOUD U.S. team for orders, setup, and warranty support. 小雲盒子 聯絡方式與服務時間。',
                    'zh-cn' => 'Reach the SVICLOUD U.S. team for orders, setup, and warranty support. 小雲盒子 聯絡方式與服務時間。',
                ],
                'image'       => '/assets/images/hero-voice-assistant.webp',
                'image_alt'   => [
                    'en' => 'Concierge specialist answering SVICLOUD questions',
                    'zh' => '小雲禮賓客服回覆客戶問題',
                    'zh-cn' => '小云礼宾客服回复客户问题',
                ],
            ],
            'faq' => [
                'title'       => [
                    'en' => 'SVICLOUD FAQ | 小雲盒子 常見問題',
                    'zh' => 'SVICLOUD FAQ | 小雲盒子 常見問題',
                    'zh-cn' => 'SVICLOUD FAQ | 小雲盒子 常見問題',
                ],
                'description' => [
                    'en' => 'Answers on setup, warranty, shipping, and apps. 小雲盒子 常見問題與解答。',
                    'zh' => 'Answers on setup, warranty, shipping, and apps. 小雲盒子 常見問題與解答。',
                    'zh-cn' => 'Answers on setup, warranty, shipping, and apps. 小雲盒子 常見問題與解答。',
                ],
                'image'       => $default_image,
                'image_alt'   => [
                    'en' => 'SVICLOUD FAQ illustration',
                    'zh' => '小雲電視盒常見問題示意圖',
                    'zh-cn' => '小云电视盒常见问题示意图',
                ],
            ],
            'guides' => [
                'title'       => [
                    'en' => 'SVICLOUD Setup Guides | 小雲盒子 使用指南',
                    'zh' => 'SVICLOUD Setup Guides | 小雲盒子 使用指南',
                    'zh-cn' => 'SVICLOUD Setup Guides | 小雲盒子 使用指南',
                ],
                'description' => [
                    'en' => 'Setup, apps, and troubleshooting resources for SVICLOUD boxes. 小雲盒子 使用教學與支援資源。',
                    'zh' => 'Setup, apps, and troubleshooting resources for SVICLOUD boxes. 小雲盒子 使用教學與支援資源。',
                    'zh-cn' => 'Setup, apps, and troubleshooting resources for SVICLOUD boxes. 小雲盒子 使用教學與支援資源。',
                ],
                'image'       => '/assets/images/svicloud-hero-product.webp',
                'image_alt'   => [
                    'en' => 'SVICLOUD 10P+ setup guide illustration',
                    'zh' => '小雲 10P+ 安裝指南示意圖',
                    'zh-cn' => '小云 10P+ 安装指南示意图',
                ],
            ],
            'guides-setup' => [
                'title'       => [
                    'en' => 'SVICLOUD Setup Guide | 小雲盒子 安裝指南',
                    'zh' => 'SVICLOUD Setup Guide | 小雲盒子 安裝指南',
                    'zh-cn' => 'SVICLOUD Setup Guide | 小雲盒子 安裝指南',
                ],
                'description' => [
                    'en' => 'Step-by-step setup for SVICLOUD boxes. 小雲盒子 安裝步驟教學。',
                    'zh' => 'Step-by-step setup for SVICLOUD boxes. 小雲盒子 安裝步驟教學。',
                    'zh-cn' => 'Step-by-step setup for SVICLOUD boxes. 小雲盒子 安裝步驟教學。',
                ],
                'image'       => '/assets/images/svicloud-hero-product.webp',
                'image_alt'   => [
                    'en' => 'SVICLOUD 10P+ setup hero image',
                    'zh' => '小雲 10P+ 安裝示意圖',
                    'zh-cn' => '小云 10P+ 安装示意图',
                ],
            ],
            'guides-after-setup' => [
                'title'       => [
                    'en' => 'After Setup Guide | SVICLOUD | 小雲盒子 完成設定',
                    'zh' => 'After Setup Guide | SVICLOUD | 小雲盒子 完成設定',
                    'zh-cn' => 'After Setup Guide | SVICLOUD | 小雲盒子 完成設定',
                ],
                'description' => [
                    'en' => 'What to do after setup with tips, apps, and settings. 小雲盒子 設定後建議。',
                    'zh' => 'What to do after setup with tips, apps, and settings. 小雲盒子 設定後建議。',
                    'zh-cn' => 'What to do after setup with tips, apps, and settings. 小雲盒子 設定後建議。',
                ],
                'image'       => $default_image,
                'image_alt'   => [
                    'en' => 'Family using SVICLOUD after setup',
                    'zh' => '家庭使用小雲的場景',
                    'zh-cn' => '家庭使用小云的场景',
                ],
            ],
            'guides-support' => [
                'title'       => [
                    'en' => 'Concierge Support | SVICLOUD | 小雲盒子 專人支援',
                    'zh' => 'Concierge Support | SVICLOUD | 小雲盒子 專人支援',
                    'zh-cn' => 'Concierge Support | SVICLOUD | 小雲盒子 專人支援',
                ],
                'description' => [
                    'en' => 'Premium concierge help for setup, warranty, and upgrades. 小雲盒子 專人支援服務。',
                    'zh' => 'Premium concierge help for setup, warranty, and upgrades. 小雲盒子 專人支援服務。',
                    'zh-cn' => 'Premium concierge help for setup, warranty, and upgrades. 小雲盒子 專人支援服務。',
                ],
                'image'       => '/assets/images/certification-authorized-dealer.webp',
                'image_alt'   => [
                    'en' => 'Authorized SVICLOUD concierge badge',
                    'zh' => '小雲禮賓客服認證徽章',
                    'zh-cn' => '小云礼宾客服认证徽章',
                ],
            ],
            'guides-resources' => [
                'title'       => [
                    'en' => 'Resource Library | SVICLOUD | 小雲盒子 資源庫',
                    'zh' => 'Resource Library | SVICLOUD | 小雲盒子 資源庫',
                    'zh-cn' => 'Resource Library | SVICLOUD | 小雲盒子 資源庫',
                ],
                'description' => [
                    'en' => 'Downloads, manuals, and warranty resources. 小雲盒子 資源與下載。',
                    'zh' => 'Downloads, manuals, and warranty resources. 小雲盒子 資源與下載。',
                    'zh-cn' => 'Downloads, manuals, and warranty resources. 小雲盒子 資源與下載。',
                ],
                'image'       => $default_image,
                'image_alt'   => [
                    'en' => 'SVICLOUD resource library illustration',
                    'zh' => '小雲資源中心示意圖',
                    'zh-cn' => '小云资源中心示意图',
                ],
            ],
            'guides-troubleshooting' => [
                'title'       => [
                    'en' => 'Troubleshooting Guide | SVICLOUD | 小雲盒子 故障排除',
                    'zh' => 'Troubleshooting Guide | SVICLOUD | 小雲盒子 故障排除',
                    'zh-cn' => 'Troubleshooting Guide | SVICLOUD | 小雲盒子 故障排除',
                ],
                'description' => [
                    'en' => 'Fix SVICLOUD TV Box remote pairing, buffering, Wi-Fi, app, and setup issues with bilingual troubleshooting help for 10P+ and 10S owners.',
                    'zh' => 'Fix SVICLOUD TV Box remote pairing, buffering, Wi-Fi, app, and setup issues with bilingual troubleshooting help for 10P+ and 10S owners.',
                    'zh-cn' => 'Fix SVICLOUD TV Box remote pairing, buffering, Wi-Fi, app, and setup issues with bilingual troubleshooting help for 10P+ and 10S owners.',
                ],
                'image'       => '/assets/images/svicloud-hero-product.webp',
                'image_alt'   => [
                    'en' => 'SVICLOUD troubleshooting illustration',
                    'zh' => '小雲疑難排解示意圖',
                    'zh-cn' => '小云疑难排解示意图',
                ],
            ],
            'guides-apps' => [
                'title'       => [
                    'en' => 'SVICLOUD App Installation Guide | 小雲盒子 App 安裝教學',
                    'zh' => 'SVICLOUD App Installation Guide | 小雲盒子 App 安裝教學',
                    'zh-cn' => 'SVICLOUD App Installation Guide | 小雲盒子 App 安装教学',
                ],
                'description' => [
                    'en' => 'Step-by-step SVICLOUD TV Box app installation and update help for 10P+ and 10S, with bilingual tips for U.S. customers and support links.',
                    'zh' => 'Step-by-step SVICLOUD TV Box app installation and update help for 10P+ and 10S, with bilingual tips for U.S. customers and support links.',
                    'zh-cn' => 'Step-by-step SVICLOUD TV Box app installation and update help for 10P+ and 10S, with bilingual tips for U.S. customers and support links.',
                ],
                'image'       => '/assets/images/svicloud-hero-product.webp',
                'image_alt'   => [
                    'en' => 'SVICLOUD app installation guide illustration',
                    'zh' => '小雲 App 安裝教學示意圖',
                    'zh-cn' => '小云 App 安装教学示意图',
                ],
            ],
            'return-policy' => [
                'title'       => [
                    'en' => 'Return Policy | SVICLOUD | 小雲盒子 退貨政策',
                    'zh' => 'Return Policy | SVICLOUD | 小雲盒子 退貨政策',
                    'zh-cn' => 'Return Policy | SVICLOUD | 小雲盒子 退貨政策',
                ],
                'description' => [
                    'en' => 'Return and exchange policy for SVICLOUD TV Box US. 小雲盒子 退換貨說明。',
                    'zh' => 'Return and exchange policy for SVICLOUD TV Box US. 小雲盒子 退換貨說明。',
                    'zh-cn' => 'Return and exchange policy for SVICLOUD TV Box US. 小雲盒子 退換貨說明。',
                ],
                'image'       => '/assets/images/certification-authorized-dealer.webp',
                'image_alt'   => [
                    'en' => 'Return and exchange timeline graphic',
                    'zh' => '退換貨流程圖示',
                    'zh-cn' => '退换货流程图示',
                ],
            ],
            'legal-disclaimer' => [
                'title'       => [
                    'en' => 'Legal Disclaimer | SVICLOUD | 小雲盒子 法律聲明',
                    'zh' => 'Legal Disclaimer | SVICLOUD | 小雲盒子 法律聲明',
                    'zh-cn' => 'Legal Disclaimer | SVICLOUD | 小雲盒子 法律聲明',
                ],
                'description' => [
                    'en' => 'Legal disclaimer for SVICLOUD TV Box US. 小雲盒子 法律聲明。',
                    'zh' => 'Legal disclaimer for SVICLOUD TV Box US. 小雲盒子 法律聲明。',
                    'zh-cn' => 'Legal disclaimer for SVICLOUD TV Box US. 小雲盒子 法律聲明。',
                ],
                'image'       => $default_image,
                'image_alt'   => [
                    'en' => 'SVICLOUD legal policy illustration',
                    'zh' => '小雲法律政策示意圖',
                    'zh-cn' => '小云法律政策示意图',
                ],
            ],
            'privacy-policy' => [
                'title'       => [
                    'en' => 'Privacy Policy | SVICLOUD | 小雲盒子 隱私權政策',
                    'zh' => 'Privacy Policy | SVICLOUD | 小雲盒子 隱私權政策',
                    'zh-cn' => 'Privacy Policy | SVICLOUD | 小雲盒子 隐私权政策',
                ],
                'description' => [
                    'en' => 'Privacy policy for SVICLOUD TV Box US. Learn how we handle your data. 小雲盒子 隱私權說明。',
                    'zh' => 'Privacy policy for SVICLOUD TV Box US. Learn how we handle your data. 小雲盒子 隱私權說明。',
                    'zh-cn' => 'Privacy policy for SVICLOUD TV Box US. Learn how we handle your data. 小雲盒子 隐私权说明。',
                ],
                'image'       => $default_image,
                'image_alt'   => [
                    'en' => 'SVICLOUD privacy policy illustration',
                    'zh' => '小雲隱私政策示意圖',
                    'zh-cn' => '小云隐私政策示意图',
                ],
            ],
            'shipping-policy' => [
                'title'       => [
                    'en' => 'Shipping Policy | SVICLOUD | 小雲盒子 運送政策',
                    'zh' => 'Shipping Policy | SVICLOUD | 小雲盒子 運送政策',
                    'zh-cn' => 'Shipping Policy | SVICLOUD | 小雲盒子 运送政策',
                ],
                'description' => [
                    'en' => 'Shipping policy for SVICLOUD TV Box US. Delivery times, coverage, and tracking. 小雲盒子 運送說明。',
                    'zh' => 'Shipping policy for SVICLOUD TV Box US. Delivery times, coverage, and tracking. 小雲盒子 運送說明。',
                    'zh-cn' => 'Shipping policy for SVICLOUD TV Box US. Delivery times, coverage, and tracking. 小雲盒子 运送说明。',
                ],
                'image'       => $default_image,
                'image_alt'   => [
                    'en' => 'SVICLOUD shipping policy illustration',
                    'zh' => '小雲運送政策示意圖',
                    'zh-cn' => '小云运送政策示意图',
                ],
            ],
            'order-tracking' => [
                'title'       => [
                    'en' => 'Order Tracking | SVICLOUD | 小雲盒子 訂單查詢',
                    'zh' => 'Order Tracking | SVICLOUD | 小雲盒子 訂單查詢',
                    'zh-cn' => 'Order Tracking | SVICLOUD | 小雲盒子 訂單查詢',
                ],
                'description' => [
                    'en' => 'Track your SVICLOUD order status and delivery. 小雲盒子 訂單查詢。',
                    'zh' => 'Track your SVICLOUD order status and delivery. 小雲盒子 訂單查詢。',
                    'zh-cn' => 'Track your SVICLOUD order status and delivery. 小雲盒子 訂單查詢。',
                ],
                'image'       => '/assets/images/svicloud-hero-product.webp',
                'image_alt'   => [
                    'en' => 'Order tracking illustration',
                    'zh' => '訂單追蹤示意圖',
                    'zh-cn' => '订单追踪示意图',
                ],
            ],
            'blog' => [
                'title'       => [
                    'en' => 'SVICLOUD Blog | 小雲盒子 最新消息',
                    'zh' => 'SVICLOUD Blog | 小雲盒子 最新消息',
                    'zh-cn' => 'SVICLOUD Blog | 小雲盒子 最新消息',
                ],
                'description' => [
                    'en' => 'Updates, tips, and guides for SVICLOUD owners. 小雲盒子 最新消息與教學。',
                    'zh' => 'Updates, tips, and guides for SVICLOUD owners. 小雲盒子 最新消息與教學。',
                    'zh-cn' => 'Updates, tips, and guides for SVICLOUD owners. 小雲盒子 最新消息與教學。',
                ],
                'image'       => '/assets/images/hero-voice-assistant.webp',
                'image_alt'   => [
                    'en' => 'SVICLOUD blog hero image',
                    'zh' => '小雲部落格主圖',
                    'zh-cn' => '小云博客主图',
                ],
            ],
            'about' => [
                'title'       => [
                    'en' => 'About SVICLOUD TV Box US | 小雲盒子 美國代理',
                    'zh' => 'About SVICLOUD TV Box US | 小雲盒子 美國代理',
                    'zh-cn' => 'About SVICLOUD TV Box US | 小雲盒子 美國代理',
                ],
                'description' => [
                    'en' => 'Learn about our authorized dealer status and U.S. fulfillment. 小雲盒子 美國代理資訊。',
                    'zh' => 'Learn about our authorized dealer status and U.S. fulfillment. 小雲盒子 美國代理資訊。',
                    'zh-cn' => 'Learn about our authorized dealer status and U.S. fulfillment. 小雲盒子 美國代理資訊。',
                ],
                'image'       => '/assets/images/svicloud-hero-product.webp',
                'image_alt'   => [
                    'en' => 'SVICLOUD team in Las Vegas warehouse',
                    'zh' => '小雲團隊於拉斯維加斯倉庫',
                    'zh-cn' => '小云团队于拉斯维加斯仓库',
                ],
            ],
            'compare' => [
                'title'       => [
                    'en' => 'SVICLOUD 10P+ vs 10S | 小雲盒子 型號比較',
                    'zh' => 'SVICLOUD 10P+ vs 10S | 小雲盒子 型號比較',
                    'zh-cn' => 'SVICLOUD 10P+ vs 10S | 小雲盒子 型號比較',
                ],
                'description' => [
                    'en' => 'Compare SVICLOUD 10P+ and 10S specs, storage, and features. 小雲盒子 型號規格比較。',
                    'zh' => 'Compare SVICLOUD 10P+ and 10S specs, storage, and features. 小雲盒子 型號規格比較。',
                    'zh-cn' => 'Compare SVICLOUD 10P+ and 10S specs, storage, and features. 小雲盒子 型號規格比較。',
                ],
                'image'       => '/assets/images/svicloud-hero-product.webp',
                'image_alt'   => [
                    'en' => 'SVICLOUD 10P+ and 10S side-by-side',
                    'zh' => '小雲 10P+ 與 10S 並排圖',
                    'zh-cn' => '小云 10P+ 与 10S 并排图',
                ],
            ],
            'shop' => [
                'title'       => [
                    'en' => 'Shop SVICLOUD TV Boxes | 小雲盒子 美國現貨',
                    'zh' => 'Shop SVICLOUD TV Boxes | 小雲盒子 美國現貨',
                    'zh-cn' => 'Shop SVICLOUD TV Boxes | 小雲盒子 美國現貨',
                ],
                'description' => [
                    'en' => 'SVICLOUD 10P+ and 10S with U.S. warranty, fast shipping, and concierge support. 小雲盒子 美國現貨保固。',
                    'zh' => 'SVICLOUD 10P+ and 10S with U.S. warranty, fast shipping, and concierge support. 小雲盒子 美國現貨保固。',
                    'zh-cn' => 'SVICLOUD 10P+ and 10S with U.S. warranty, fast shipping, and concierge support. 小雲盒子 美國現貨保固。',
                ],
                'image'       => '/assets/images/svicloud-hero-product.webp',
                'image_alt'   => [
                    'en' => 'SVICLOUD 10P+ product photo',
                    'zh' => '小雲 10P+ 產品照',
                    'zh-cn' => '小云 10P+ 产品照',
                ],
            ],
            'cart' => [
                'title'       => [
                    'en' => 'SVICLOUD Cart | Review Items Before Checkout',
                    'zh' => '小雲購物車｜結帳前確認商品',
                    'zh-cn' => '小云购物车｜结账前确认商品',
                ],
                'description' => [
                    'en' => 'Confirm SVICLOUD models, extended warranty, and concierge add-ons before entering checkout.',
                    'zh' => '結帳前確認小雲機型、延長保固與禮賓加值項目。',
                    'zh-cn' => '结账前确认小云机型、延长保固与礼宾加值项目。',
                ],
                'image'       => $default_image,
                'image_alt'   => [
                    'en' => 'Shopping cart illustration',
                    'zh' => '購物車示意圖',
                    'zh-cn' => '购物车示意图',
                ],
            ],
            'checkout' => [
                'title'       => [
                    'en' => 'Secure Checkout | SVICLOUD 10P+ & 10S in the USA',
                    'zh' => '安全結帳｜小雲 10P+／10S 美國訂單',
                    'zh-cn' => '安全结账｜小云 10P+／10S 美国订单',
                ],
                'description' => [
                    'en' => 'Complete your SVICLOUD order with encrypted payment, Nevada fulfillment, and bilingual concierge confirmation.',
                    'zh' => '使用加密付款完成小雲訂單，內華達出貨並附中英禮賓確認。',
                    'zh-cn' => '使用加密付款完成小云订单，内华达出货并附中英礼宾确认。',
                ],
                'image'       => $default_image,
                'image_alt'   => [
                    'en' => 'Secure checkout illustration',
                    'zh' => '安全結帳示意圖',
                    'zh-cn' => '安全结账示意图',
                ],
            ],
            'my-account' => [
                'title'       => [
                    'en' => 'My Account | Track SVICLOUD Orders & Concierge Tickets',
                    'zh' => '會員中心｜查詢小雲訂單與客服案件',
                    'zh-cn' => '会员中心｜查询小云订单与客服案件',
                ],
                'description' => [
                    'en' => 'Log in to download invoices, track warranty submissions, and manage concierge conversations.',
                    'zh' => '登入下載發票、追蹤保固案件並管理禮賓對話。',
                    'zh-cn' => '登录下载发票、追踪保固案件并管理礼宾对话。',
                ],
                'image'       => $default_image,
                'image_alt'   => [
                    'en' => 'Account dashboard illustration',
                    'zh' => '會員中心儀表板示意',
                    'zh-cn' => '会员中心仪表板示意',
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
        $lang_raw = function_exists('svic_language_query_value') ? svic_language_query_value() : 'en';
        $lang_raw = is_string($lang_raw) ? strtolower(trim($lang_raw)) : 'en';
        $lang_raw = str_replace('_', '-', $lang_raw);

        $lang = 'en';
        if ($lang_raw === 'zh-cn') {
            $lang = 'zh-cn';
        } elseif ($lang_raw === 'zh') {
            $lang = 'zh';
        }

        $title = $entry['title'][$lang] ?? ($entry['title']['zh'] ?? ($entry['title']['en'] ?? ''));
        $description = $entry['description'][$lang] ?? ($entry['description']['zh'] ?? ($entry['description']['en'] ?? ''));
        $image_alt = $entry['image_alt'][$lang] ?? ($entry['image_alt']['zh'] ?? ($entry['image_alt']['en'] ?? ''));

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
    add_filter('rank_math/json_ld', 'svic_rank_math_inject_site_navigation_schema', 60, 2);
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

if (!function_exists('svic_preserve_language_prefix_canonical')) {
    /**
     * Prevent WordPress canonical redirects from stripping the /zh/ prefix when the
     * original request explicitly targeted the zh route.
     */
    function svic_preserve_language_prefix_canonical($redirect_url, $requested_url)
    {
        // Avoid redirect loops between /compare/ and legacy Chinese slugs.
        $path = is_string($requested_url) ? (string) wp_parse_url($requested_url, PHP_URL_PATH) : '';
        if (is_string($path) && $path !== '') {
            $path = svic_normalize_route_path($path);
            $compare_aliases = [
                '/compare/',
                '/%e6%a9%9f%e5%9e%8b%e6%af%94%e8%bc%83/',
                '/機型比較/',
                '/zh/compare/',
                '/zh/%e6%a9%9f%e5%9e%8b%e6%af%94%e8%bc%83/',
                '/zh/機型比較/',
            ];
            if (in_array($path, $compare_aliases, true)) {
                return false;
            }
        }

        if (!is_string($redirect_url) || $redirect_url === '' || !is_string($requested_url) || $requested_url === '') {
            return $redirect_url;
        }

        $requested_parts = wp_parse_url($requested_url);
        $redirect_parts  = wp_parse_url($redirect_url);

        if (!is_array($requested_parts) || empty($requested_parts['path'])) {
            return $redirect_url;
        }

        $requested_path = svic_normalize_route_path($requested_parts['path']);
        if (strpos($requested_path, '/zh/') !== 0 && $requested_path !== '/zh/') {
            return $redirect_url;
        }

        if (!is_array($redirect_parts) || empty($redirect_parts['path'])) {
            return $redirect_url;
        }

        $redirect_path = svic_normalize_route_path($redirect_parts['path']);
        $requested_base = svic_strip_route_locale_prefix($requested_path);

        if ($requested_base === $redirect_path) {
            return false;
        }

        return $redirect_url;
    }

    add_filter('redirect_canonical', 'svic_preserve_language_prefix_canonical', 99, 2);
}

add_action('template_redirect', function () {
    if (is_admin()) {
        return;
    }

    if (!class_exists('SVIC_Locale_Resolver')) {
        return;
    }

    $original_path = SVIC_Locale_Resolver::originalRequestPath();
    if (!is_string($original_path) || $original_path === '') {
        return;
    }

    if (strpos($original_path, '/zh/') === 0 || $original_path === '/zh/') {
        remove_action('template_redirect', 'redirect_canonical');
    }
}, 0);

// Serve a minimal zh-v1.1.json translation payload to prevent front-end 404s.
add_action('template_redirect', function () {
    if (is_admin()) {
        return;
    }

    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $path        = is_string($request_uri) ? wp_parse_url($request_uri, PHP_URL_PATH) : '';
    if (!is_string($path) || $path === '') {
        return;
    }

    if (rtrim(strtolower($path), '/') !== '/zh-v1.1.json') {
        return;
    }

    if (!headers_sent()) {
        header('Content-Type: application/json; charset=UTF-8');
        header('Cache-Control: public, max-age=86400, immutable');
    }

    $payload = [
        'translation-revision-date' => gmdate('Y-m-d H:i:s+0000'),
        'generator'                 => 'svicloudtvbox-lumen',
        'domain'                    => 'default',
        'locale_data'               => [
            'messages' => [
                '' => [
                    'domain'        => 'messages',
                    'lang'          => 'zh',
                    'plural-forms'  => 'nplurals=1; plural=0;',
                ],
            ],
        ],
    ];

    echo wp_json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}, 1);

// Normalize legacy ?lang= querystring requests to the canonical language-prefixed URL so
// Search Console stops surfacing /contact?lang=zh style 404s.
add_action('template_redirect', function () {
    if (is_admin() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }

    if (!isset($_GET['lang'])) {
        return;
    }

    $lang_raw = wp_unslash((string) $_GET['lang']);
    $lang     = sanitize_text_field($lang_raw);
    if ($lang === '') {
        return;
    }

    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $current     = home_url(is_string($request_uri) && $request_uri !== '' ? $request_uri : '/');
    $target      = svic_url_with_lang(remove_query_arg('lang', $current), $lang);

    if (!is_string($target) || $target === '' || $target === $current) {
        return;
    }

    wp_safe_redirect($target, 301);
    exit;
}, 4);

// Harden thin/error-prone archives and placeholder search URLs so they 404 cleanly
// instead of returning 5xx or being indexed.
add_action('template_redirect', function () {
    if (is_admin() || is_feed()) {
        return;
    }

    // Hard 404 for author archives (thin content) to avoid crawler 5xx noise.
    if (is_author()) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        nocache_headers();
        include get_query_template('404');
        exit;
    }

    // Block placeholder/invalid searches like ?s={search_term_string} or encoded variants.
    if (is_search()) {
        $term   = get_query_var('s');
        $raw_qs = $_SERVER['QUERY_STRING'] ?? '';
        $match_placeholder = false;

        if (is_string($term) && preg_match('/\{\s*search_term_string\s*\}/i', $term)) {
            $match_placeholder = true;
        }

        if (!$match_placeholder && is_string($raw_qs)) {
            // Also catch URL-encoded placeholder from bots
            $decoded_qs = rawurldecode($raw_qs);
            if (stripos($decoded_qs, 'search_term_string') !== false) {
                $match_placeholder = true;
            }
        }

        if ($match_placeholder) {
            global $wp_query;
            $wp_query->set_404();
            status_header(404);
            nocache_headers();
            include get_query_template('404');
            exit;
        }
    }
}, 6);

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
// When Rank Math is active, force localized canonicals so /zh/ pages self-canonicalize.
if (defined('RANK_MATH_VERSION')) {
    if (!function_exists('svic_mark_rank_math_meta_seen')) {
        function svic_mark_rank_math_meta_seen($value)
        {
            if (!is_admin()) {
                $GLOBALS['svic_rank_math_meta_seen'] = true;
            }

            return $value;
        }
    }

    add_filter('rank_math/frontend/title', 'svic_mark_rank_math_meta_seen', 999, 1);
    add_filter('rank_math/frontend/description', 'svic_mark_rank_math_meta_seen', 999, 1);
    add_filter('rank_math/frontend/canonical', function ($url) {
        if (is_admin()) {
            return $url;
        }

        $GLOBALS['svic_rank_math_meta_seen'] = true;

        $canonical = svic_get_localized_canonical_url();
        return $canonical ?: $url;
    }, 99, 1);
}

if (!function_exists('svic_homepage_meta_definitions')) {
    function svic_homepage_meta_definitions(): array
    {
        $locale = function_exists('svic_current_locale') ? svic_current_locale() : get_locale();
        $locale = is_string($locale) ? strtolower($locale) : 'en_us';

        $definitions = [
            'zh_tw' => [
                'title'       => '小雲盒子美國授權經銷｜SVICLOUD TV Box US 美國購買',
                'description' => '小雲盒子美國授權經銷，提供 SVICLOUD 10P+、10S 美國現貨、美國購買、快速出貨、1 年保固與 English/中文 禮賓支援。',
                'image_alt'   => 'SVICLOUD 10P+ streaming box with concierge support badge, 小雲盒子',
            ],
            'zh_cn' => [
                'title'       => '小云盒子美国授权经销｜SVICLOUD TV Box US 美国购买',
                'description' => '小云盒子美国授权经销，提供 SVICLOUD 10P+、10S 美国现货、美国购买、快速发货、1 年保修与 English/中文 礼宾支持。',
                'image_alt'   => 'SVICLOUD 10P+ streaming box with concierge support badge, 小雲盒子',
            ],
            'en_us' => [
                'title'       => '小雲盒子 美國授權經銷 | SVICLOUD TV Box US Dealer',
                'description' => 'Buy 小雲盒子 in the USA from an authorized SVICLOUD dealer. Shop SVICLOUD 10P+ and 10S with U.S. shipping, warranty, and bilingual English/中文 support.',
                'image_alt'   => 'SVICLOUD 10P+ streaming box with concierge support badge, 小雲盒子',
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

    if (defined('RANK_MATH_VERSION')) {
        add_filter('rank_math/frontend/title', 'svic_filter_rank_math_front_page_title', 20);
    }
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

    if (defined('RANK_MATH_VERSION')) {
        add_filter('rank_math/frontend/description', 'svic_filter_rank_math_front_page_description', 20);
        add_filter('rank_math/frontend/snippet_description', 'svic_filter_rank_math_front_page_description', 20);
    }
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

    if (defined('RANK_MATH_VERSION')) {
        add_filter('rank_math/opengraph/facebook/og_title', 'svic_filter_rank_math_front_page_og_title', 20);
        add_filter('rank_math/opengraph/twitter/twitter_title', 'svic_filter_rank_math_front_page_og_title', 20);
    }
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

    if (defined('RANK_MATH_VERSION')) {
        add_filter('rank_math/opengraph/facebook/og_description', 'svic_filter_rank_math_front_page_og_description', 20);
        add_filter('rank_math/opengraph/twitter/twitter_description', 'svic_filter_rank_math_front_page_og_description', 20);
    }
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

        $attachment = [
            'url' => $hero_meta['url'],
        ];

        if (!empty($hero_meta['width'])) {
            $attachment['width'] = (int) $hero_meta['width'];
        }

        if (!empty($hero_meta['height'])) {
            $attachment['height'] = (int) $hero_meta['height'];
        }

        $image_alt = svic_get_homepage_meta_for_output()['image_alt'];
        if ($image_alt !== '') {
            $attachment['alt'] = $image_alt;
        }

        return $attachment;
    }

    if (defined('RANK_MATH_VERSION')) {
        add_filter('rank_math/opengraph/facebook/image_array', 'svic_filter_rank_math_front_page_og_image', 20);
        add_filter('rank_math/opengraph/twitter/image_array', 'svic_filter_rank_math_front_page_og_image', 20);
    }
}

if (!function_exists('svic_rank_math_should_override_static_page_meta')) {
    function svic_rank_math_should_override_static_page_meta(): bool
    {
        if (!defined('RANK_MATH_VERSION') || is_admin() || !function_exists('svic_language_query_value')) {
            return false;
        }

        $lang = svic_language_query_value();
        return $lang === 'zh' || $lang === 'zh-cn';
    }
}

if (!function_exists('svic_filter_rank_math_static_page_title')) {
    function svic_filter_rank_math_static_page_title($title)
    {
        if (!svic_rank_math_should_override_static_page_meta()) {
            return $title;
        }

        if (function_exists('is_front_page') && is_front_page()) {
            return $title;
        }

        $slug = function_exists('svic_get_static_page_meta_slug') ? svic_get_static_page_meta_slug() : null;
        $meta = function_exists('svic_resolve_static_page_meta') ? svic_resolve_static_page_meta($slug) : null;
        if (!is_array($meta) || empty($meta['title'])) {
            return $title;
        }

        return trim(wp_strip_all_tags((string) $meta['title']));
    }

    if (defined('RANK_MATH_VERSION')) {
        add_filter('rank_math/frontend/title', 'svic_filter_rank_math_static_page_title', 30);
    }
}

if (!function_exists('svic_filter_rank_math_static_page_description')) {
    function svic_filter_rank_math_static_page_description($description)
    {
        if (function_exists('is_front_page') && is_front_page()) {
            return $description;
        }

        $current_description = trim(wp_strip_all_tags((string) $description));
        $should_override     = svic_rank_math_should_override_static_page_meta();

        // Keep Rank Math-authored English descriptions when present. Use the
        // theme registry as a fallback for static pages with empty Rank Math
        // descriptions, and as the source of truth for localized zh pages.
        if (!$should_override && $current_description !== '') {
            return $description;
        }

        $slug = function_exists('svic_get_static_page_meta_slug') ? svic_get_static_page_meta_slug() : null;
        $meta = function_exists('svic_resolve_static_page_meta') ? svic_resolve_static_page_meta($slug) : null;
        if (!is_array($meta) || empty($meta['description'])) {
            return $description;
        }

        return trim(wp_strip_all_tags((string) $meta['description']));
    }

    if (defined('RANK_MATH_VERSION')) {
        add_filter('rank_math/frontend/description', 'svic_filter_rank_math_static_page_description', 30);
        add_filter('rank_math/frontend/snippet_description', 'svic_filter_rank_math_static_page_description', 30);
    }
}

if (!function_exists('svic_filter_rank_math_static_page_og_title')) {
    function svic_filter_rank_math_static_page_og_title($title)
    {
        return svic_filter_rank_math_static_page_title($title);
    }

    if (defined('RANK_MATH_VERSION')) {
        add_filter('rank_math/opengraph/facebook/og_title', 'svic_filter_rank_math_static_page_og_title', 30);
        add_filter('rank_math/opengraph/twitter/twitter_title', 'svic_filter_rank_math_static_page_og_title', 30);
    }
}

if (!function_exists('svic_filter_rank_math_static_page_og_description')) {
    function svic_filter_rank_math_static_page_og_description($description)
    {
        return svic_filter_rank_math_static_page_description($description);
    }

    if (defined('RANK_MATH_VERSION')) {
        add_filter('rank_math/opengraph/facebook/og_description', 'svic_filter_rank_math_static_page_og_description', 30);
        add_filter('rank_math/opengraph/twitter/twitter_description', 'svic_filter_rank_math_static_page_og_description', 30);
    }
}

if (!function_exists('svic_disable_rank_math_front_page_schema')) {
    /**
     * Force Rank Math to skip its default schema on the static homepage.
     *
     * Only relevant when Rank Math is not controlling schema output. If Rank Math
     * is active, we leave its WebPage node intact so structured data stays complete.
     */
    function svic_disable_rank_math_front_page_schema($schema, $type, $object_id)
    {
        if (function_exists('is_front_page') && is_front_page()) {
            return 'none';
        }

        return $schema;
    }

    if (!defined('RANK_MATH_VERSION')) {
        add_filter('rank_math/frontend/schema/post_type', 'svic_disable_rank_math_front_page_schema', 20, 3);
    }
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
            $image_meta = svic_get_theme_image_meta('/assets/images/svicloud-hero-product.webp');
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
        // Street address intentionally omitted — home-based business.
        // Only city / state / country are emitted for Google location signals.
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
            $shipping_policy_url = function_exists('svic_url_with_lang') ? svic_url_with_lang(home_url('/shipping-policy/')) : home_url('/shipping-policy/');
            $return_policy_url   = function_exists('svic_url_with_lang') ? svic_url_with_lang(home_url('/return-policy/')) : home_url('/return-policy/');

            $product_node['offers'] = [
                '@type'         => 'Offer',
                'priceCurrency' => strtoupper(get_option('woocommerce_currency', 'USD')),
                'price'         => $price_value,
                'availability'  => $availability,
                'url'           => $product_url,
                'seller'        => [
                    '@id' => svic_get_organization_schema_id(),
                ],
                'shippingDetails' => [
                    '@type' => 'OfferShippingDetails',
                    'shippingDestination' => [
                        '@type' => 'DefinedRegion',
                        'addressCountry' => 'US',
                    ],
                    'shippingRate' => [
                        '@type' => 'MonetaryAmount',
                        'value' => '0',
                        'currency' => strtoupper(get_option('woocommerce_currency', 'USD')),
                    ],
                    'deliveryTime' => [
                        '@type' => 'ShippingDeliveryTime',
                        'handlingTime' => [
                            '@type' => 'QuantitativeValue',
                            'minValue' => 0,
                            'maxValue' => 2,
                            'unitCode' => 'd',
                        ],
                        'transitTime' => [
                            '@type' => 'QuantitativeValue',
                            'minValue' => 2,
                            'maxValue' => 5,
                            'unitCode' => 'd',
                        ],
                    ],
                    'url' => esc_url_raw($shipping_policy_url),
                ],
                'hasMerchantReturnPolicy' => [
                    '@type' => 'MerchantReturnPolicy',
                    'applicableCountry' => 'US',
                    'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
                    'merchantReturnDays' => 30,
                    'returnMethod' => 'https://schema.org/ReturnByMail',
                    'returnFees' => 'https://schema.org/ReturnShippingFees',
                    'url' => esc_url_raw($return_policy_url),
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

        // AggregateRating from Google Business Profile reviews
        // Enable via filter once Google Business Profile has real reviews:
        // add_filter('svic_organization_aggregate_rating', fn($r) => array_merge($r, ['enabled' => true, 'reviewCount' => YOUR_COUNT]));
        $aggregate_rating = apply_filters('svic_organization_aggregate_rating', [
            'enabled'     => false, // Disabled until Google Business Profile is set up with real reviews
            'ratingValue' => 4.9,
            'reviewCount' => 0,
            'bestRating'  => 5,
            'worstRating' => 1,
        ]);

        if (!empty($aggregate_rating['enabled']) && !empty($aggregate_rating['ratingValue']) && !empty($aggregate_rating['reviewCount'])) {
            $enhancements['aggregateRating'] = [
                '@type'       => 'AggregateRating',
                'ratingValue' => (float) $aggregate_rating['ratingValue'],
                'reviewCount' => (int) $aggregate_rating['reviewCount'],
                'bestRating'  => (int) ($aggregate_rating['bestRating'] ?? 5),
                'worstRating' => (int) ($aggregate_rating['worstRating'] ?? 1),
            ];
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

    if (defined('RANK_MATH_VERSION')) {
        add_filter('rank_math/json_ld', 'svic_filter_rank_math_schema_graph', 80, 2);
    }
}

/**
 * Minimal per-request schema registry for adding page-specific nodes to Rank Math's JSON-LD graph.
 *
 * IMPORTANT: Rank Math outputs JSON-LD in wp_head (before templates render). Therefore, page-level schema
 * must be registered during `wp` (or earlier) rather than inside templates.
 */
if (!function_exists('svic_schema_register_once')) {
    function svic_schema_register_once(string $key, array $node): void
    {
        if ($key === '' || empty($node)) {
            return;
        }

        if (!isset($GLOBALS['svic_schema_registry_keys']) || !is_array($GLOBALS['svic_schema_registry_keys'])) {
            $GLOBALS['svic_schema_registry_keys'] = [];
        }
        if (!isset($GLOBALS['svic_schema_registry_nodes']) || !is_array($GLOBALS['svic_schema_registry_nodes'])) {
            $GLOBALS['svic_schema_registry_nodes'] = [];
        }

        if (isset($GLOBALS['svic_schema_registry_keys'][$key])) {
            return;
        }

        $GLOBALS['svic_schema_registry_keys'][$key] = true;
        $GLOBALS['svic_schema_registry_nodes'][]    = $node;
    }
}

if (!function_exists('svic_schema_registered_nodes')) {
    /**
     * @return array<int, array<mixed>>
     */
    function svic_schema_registered_nodes(): array
    {
        $nodes = $GLOBALS['svic_schema_registry_nodes'] ?? [];
        if (!is_array($nodes)) {
            return [];
        }

        return array_values(array_filter($nodes, 'is_array'));
    }
}

if (!function_exists('svic_schema_graph_has_type')) {
    /**
     * @param array<mixed> $schema_graph
     */
    function svic_schema_graph_has_type(array $schema_graph, string $type): bool
    {
        if ($type === '') {
            return false;
        }

        foreach ($schema_graph as $node) {
            if (!is_array($node) || empty($node['@type'])) {
                continue;
            }

            $types = is_array($node['@type']) ? $node['@type'] : [$node['@type']];
            foreach ($types as $candidate) {
                if (!is_string($candidate)) {
                    continue;
                }
                if (strcasecmp($candidate, $type) === 0) {
                    return true;
                }
            }
        }

        return false;
    }
}

if (!function_exists('svic_build_faq_entities_from_items')) {
    /**
     * @param array<int, array{question_key:string,answer_key:string,replacements?:array<string,string>}> $items
     * @return array<int, array{ '@type':string, name:string, acceptedAnswer:array{ '@type':string, text:string } }>
     */
    function svic_build_faq_entities_from_items(array $items): array
    {
        $entities = [];

        foreach ($items as $item) {
            $question_key = isset($item['question_key']) ? (string) $item['question_key'] : '';
            $answer_key   = isset($item['answer_key']) ? (string) $item['answer_key'] : '';

            if ($question_key === '' || $answer_key === '') {
                continue;
            }

            $question = trim(wp_strip_all_tags((string) svic_translate($question_key)));

            $replacements = isset($item['replacements']) && is_array($item['replacements']) ? $item['replacements'] : [];
            $answer_raw   = svic_translate_rich($answer_key, $replacements);
            $answer_text  = trim(wp_strip_all_tags((string) $answer_raw));

            if ($question === '' || $answer_text === '') {
                continue;
            }

            $entities[] = [
                '@type' => 'Question',
                'name'  => $question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => $answer_text,
                ],
            ];
        }

        return $entities;
    }
}


if (!function_exists('svic_product_faq_schema_items')) {
    /**
     * Keep keys in sync with `theme/svicloudtvbox-lumen/woocommerce/single-product.php`.
     *
     * @return array<int, array{question_key:string,answer_key:string}>
     */
    function svic_product_faq_schema_items(): array
    {
        return [
            [
                'question_key' => 'product.faq.items.shipping.q',
                'answer_key'   => 'product.faq.items.shipping.a',
            ],
            [
                'question_key' => 'product.faq.items.warranty.q',
                'answer_key'   => 'product.faq.items.warranty.a',
            ],
            [
                'question_key' => 'product.faq.items.concierge.q',
                'answer_key'   => 'product.faq.items.concierge.a',
            ],
        ];
    }
}

if (!function_exists('svic_faq_page_schema_items')) {
    /**
     * Keep keys in sync with `theme/svicloudtvbox-lumen/page-faq.php`.
     *
     * @return array<int, array{question_key:string,answer_key:string,replacements?:array<string,string>}>
     */
    function svic_faq_page_schema_items(): array
    {
        $setup_guide_url = svic_url_with_lang(home_url('/guides-setup/'));
        $compare_url     = svic_url_with_lang(home_url('/compare/'));
        $support_url     = svic_url_with_lang(home_url('/contact/'));

        return [
            [
                'question_key' => 'faq.sections.device_models.items.model_choice.question',
                'answer_key'   => 'faq.sections.device_models.items.model_choice.answer',
                'replacements' => [
                    'setup_guide_url' => esc_url($setup_guide_url),
                    'compare_url'     => esc_url($compare_url),
                ],
            ],
            [
                'question_key' => 'faq.sections.device_models.items.international_use.question',
                'answer_key'   => 'faq.sections.device_models.items.international_use.answer',
            ],
            [
                'question_key' => 'faq.sections.device_models.items.box_contents.question',
                'answer_key'   => 'faq.sections.device_models.items.box_contents.answer',
            ],
            [
                'question_key' => 'faq.sections.setup_activation.items.power_on.question',
                'answer_key'   => 'faq.sections.setup_activation.items.power_on.answer',
                'replacements' => [
                    'setup_guide_url' => esc_url($setup_guide_url),
                ],
            ],
            [
                'question_key' => 'faq.sections.setup_activation.items.change_language.question',
                'answer_key'   => 'faq.sections.setup_activation.items.change_language.answer',
            ],
            [
                'question_key' => 'faq.sections.setup_activation.items.remote_pairing.question',
                'answer_key'   => 'faq.sections.setup_activation.items.remote_pairing.answer',
            ],
            [
                'question_key' => 'faq.sections.apps_content.items.preinstalled.question',
                'answer_key'   => 'faq.sections.apps_content.items.preinstalled.answer',
            ],
            [
                'question_key' => 'faq.sections.apps_content.items.third_party.question',
                'answer_key'   => 'faq.sections.apps_content.items.third_party.answer',
            ],
            [
                'question_key' => 'faq.sections.apps_content.items.family_content.question',
                'answer_key'   => 'faq.sections.apps_content.items.family_content.answer',
            ],
            [
                'question_key' => 'faq.sections.apps_content.items.adult_content.question',
                'answer_key'   => 'faq.sections.apps_content.items.adult_content.answer',
            ],
            [
                'question_key' => 'faq.sections.features_limitations.items.karaoke_support.question',
                'answer_key'   => 'faq.sections.features_limitations.items.karaoke_support.answer',
            ],
            [
                'question_key' => 'faq.sections.features_limitations.items.voice_control.question',
                'answer_key'   => 'faq.sections.features_limitations.items.voice_control.answer',
            ],
            [
                'question_key' => 'faq.sections.features_limitations.items.subtitle_speed.question',
                'answer_key'   => 'faq.sections.features_limitations.items.subtitle_speed.answer',
            ],
            [
                'question_key' => 'faq.sections.troubleshooting_support.items.buffering.question',
                'answer_key'   => 'faq.sections.troubleshooting_support.items.buffering.answer',
                'replacements' => [
                    'setup_guide_url' => esc_url($setup_guide_url),
                ],
            ],
            [
                'question_key' => 'faq.sections.troubleshooting_support.items.orz_installer.question',
                'answer_key'   => 'faq.sections.troubleshooting_support.items.orz_installer.answer',
            ],
            [
                'question_key' => 'faq.sections.troubleshooting_support.items.contact_support.question',
                'answer_key'   => 'faq.sections.troubleshooting_support.items.contact_support.answer',
                'replacements' => [
                    'support_url' => esc_url($support_url),
                ],
            ],
        ];
    }
}

if (!function_exists('svic_register_faq_schema_for_request')) {
    function svic_register_faq_schema_for_request(): void
    {
        if (is_admin()) {
            return;
        }

        $canonical = function_exists('svic_get_localized_canonical_url') ? svic_get_localized_canonical_url() : '';
        if (!is_string($canonical) || $canonical === '') {
            $canonical = function_exists('svic_current_base_url') ? svic_current_base_url() : home_url('/');
        }
        $canonical = esc_url_raw($canonical);

        if (function_exists('is_front_page') && is_front_page()) {
            // front-page.php emits its own @graph block with FAQPage — skip registration here
            // to avoid the "Duplicate field FAQPage" error in Google Search Console.
            return;
        }

        if (function_exists('is_page') && (is_page('faq') || is_page_template('page-faq.php'))) {
            $entities = svic_build_faq_entities_from_items(svic_faq_page_schema_items());
            if ($entities) {
                svic_schema_register_once('faqpage_faq', [
                    '@type'      => 'FAQPage',
                    '@id'        => untrailingslashit($canonical) . '#faqpage',
                    'url'        => $canonical,
                    'mainEntity' => $entities,
                ]);
            }
            return;
        }

        if (function_exists('is_product') && is_product()) {
            $entities = svic_build_faq_entities_from_items(svic_product_faq_schema_items());
            if ($entities) {
                svic_schema_register_once('faqpage_product', [
                    '@type'      => 'FAQPage',
                    '@id'        => untrailingslashit($canonical) . '#faqpage',
                    'url'        => $canonical,
                    'mainEntity' => $entities,
                ]);
            }
        }
    }
}

add_action('wp', 'svic_register_faq_schema_for_request', 5);

if (!function_exists('svic_rank_math_inject_registered_schema')) {
    /**
     * Inject nodes registered during `wp` into Rank Math's JSON-LD graph.
     *
     * @param array<mixed> $schema_graph
     * @return array<mixed>
     */
    function svic_rank_math_inject_registered_schema($schema_graph, $jsonld = null)
    {
        if (is_admin() || empty($schema_graph) || !is_array($schema_graph)) {
            return $schema_graph;
        }

        $GLOBALS['svic_rank_math_jsonld_seen'] = true;
        if (svic_schema_graph_has_type($schema_graph, 'Product')) {
            $GLOBALS['svic_rank_math_product_seen'] = true;
        }

        $nodes = svic_schema_registered_nodes();
        if (!$nodes) {
            return $schema_graph;
        }

        // Avoid duplicates if Rank Math (or another integration) already emitted an FAQPage.
        if (svic_schema_graph_has_type($schema_graph, 'FAQPage')) {
            $nodes = array_values(array_filter($nodes, function ($node) {
                return !(is_array($node) && isset($node['@type']) && is_string($node['@type']) && strcasecmp($node['@type'], 'FAQPage') === 0);
            }));
        }

        if (!$nodes) {
            return $schema_graph;
        }

        return array_merge($schema_graph, $nodes);
    }

    if (defined('RANK_MATH_VERSION')) {
        add_filter('rank_math/json_ld', 'svic_rank_math_inject_registered_schema', 90, 2);
    }
}

if (!function_exists('svic_output_registered_schema')) {
    function svic_output_registered_schema(): void
    {
        if (is_admin() || defined('WPSEO_VERSION')) {
            return;
        }

        // If Rank Math is active AND has already rendered its JSON-LD graph for this request,
        // our schema nodes should have been injected via `rank_math/json_ld`.
        if (defined('RANK_MATH_VERSION') && !empty($GLOBALS['svic_rank_math_jsonld_seen'])) {
            return;
        }

        $nodes = svic_schema_registered_nodes();
        if (!$nodes) {
            return;
        }

        echo '<script type="application/ld+json">' . wp_json_encode([
            '@context' => 'https://schema.org',
            '@graph'   => $nodes,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

    // Run late so Rank Math (when present) has a chance to render JSON-LD and set the seen flag.
    add_action('wp_head', 'svic_output_registered_schema', 999);
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
                    $image_meta = svic_get_theme_image_meta('/assets/images/svicloud-hero-product.webp');
                } elseif ($slug === 'svicloud-10s') {
                    $image_meta = svic_get_theme_image_meta('/assets/images/svicloud-hero-product.webp');
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

if (!function_exists('svic_build_homepage_webpage_schema_node')) {
    function svic_build_homepage_webpage_schema_node(): ?array
    {
        $meta      = svic_homepage_meta_definitions();
        $image     = svic_get_homepage_hero_image_meta();
        $canonical = svic_get_localized_canonical_url() ?: home_url('/');
        $site_name = get_bloginfo('name');
        $site_url  = home_url('/');
        $language  = function_exists('svic_locale_to_hreflang') ? svic_locale_to_hreflang(svic_current_locale()) : get_locale();
        $language  = $language ? strtolower(str_replace('_', '-', $language)) : 'en-us';

        $webpage_schema = [
            '@context'    => 'https://schema.org',
            '@type'       => 'WebPage',
            '@id'         => trailingslashit($canonical) . '#webpage',
            'url'         => $canonical,
            'name'        => $meta['title'],
            'description' => $meta['description'],
            'inLanguage'  => $language,
            'isPartOf'    => [
                '@type' => 'WebSite',
                '@id'   => trailingslashit($site_url) . '#website',
                'name'  => $site_name,
                'url'   => $site_url,
            ],
        ];

        if (!empty($image['url'])) {
            $image_object = [
                '@type' => 'ImageObject',
                'url'   => $image['url'],
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

        return $webpage_schema;
    }
}

if (!function_exists('svic_output_global_site_schema')) {
    function svic_output_global_site_schema(): void
    {
        if (is_admin() || defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION')) {
            return;
        }

        $schemas = svic_build_global_site_schema_nodes();
        if (!$schemas) {
            return;
        }

        echo '<script type="application/ld+json">' . wp_json_encode($schemas, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}

if (!function_exists('svic_build_global_site_schema_nodes')) {
    function svic_build_global_site_schema_nodes(): array
    {
        $site_url        = home_url('/');
        $site_name       = get_bloginfo('name') ?: 'SVICLOUD TV BOX US';
        $organization_id = trailingslashit($site_url) . '#organization';

        $logo_meta = svic_get_theme_image_meta('/assets/images/site-logo.png');
        if (empty($logo_meta['url'])) {
            $site_icon = get_site_icon_url();
            if ($site_icon) {
                $logo_meta = ['url' => esc_url_raw($site_icon)];
            }
        }

        $schemas      = [];
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

        $schemas[] = [
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

        return $schemas;
    }
}

add_action('wp_head', 'svic_output_global_site_schema', 6);

if (!function_exists('svic_output_rank_math_schema_fallbacks')) {
    function svic_output_rank_math_schema_fallbacks(): void
    {
        if (is_admin() || !defined('RANK_MATH_VERSION') || !empty($GLOBALS['svic_rank_math_jsonld_seen'])) {
            return;
        }

        if (is_front_page()) {
            $schemas = svic_build_global_site_schema_nodes();
            $webpage = function_exists('svic_build_homepage_webpage_schema_node') ? svic_build_homepage_webpage_schema_node() : null;
            if ($webpage) {
                $schemas[] = $webpage;
            }

            if ($schemas) {
                echo '<script type="application/ld+json">' . wp_json_encode($schemas, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }

            return;
        }

        if (function_exists('is_page_template') && is_page_template('page-compare.php')) {
            $meta       = svic_get_compare_page_meta_definitions();
            $canonical  = svic_get_localized_canonical_url() ?: home_url('/compare/');
            $site_name  = get_bloginfo('name');
            $site_url   = home_url('/');
            $language   = function_exists('svic_locale_to_hreflang') ? svic_locale_to_hreflang(svic_current_locale()) : get_locale();
            $language   = $language ? strtolower(str_replace('_', '-', $language)) : 'en-us';
            $image_meta = isset($meta['image']) && is_array($meta['image']) ? $meta['image'] : [];

            $schema = [
                '@context'    => 'https://schema.org',
                '@type'       => 'WebPage',
                '@id'         => trailingslashit($canonical) . '#webpage',
                'url'         => $canonical,
                'name'        => isset($meta['title']) ? (string) $meta['title'] : wp_get_document_title(),
                'description' => isset($meta['description']) ? (string) $meta['description'] : '',
                'inLanguage'  => $language,
                'isPartOf'    => [
                    '@type' => 'WebSite',
                    '@id'   => trailingslashit($site_url) . '#website',
                    'name'  => $site_name,
                    'url'   => $site_url,
                ],
                'mainEntity'  => [
                    '@id' => untrailingslashit($canonical) . '#compare-itemlist',
                ],
            ];

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
                $schema['primaryImageOfPage'] = $image_object;
            }

            echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
    }
}

add_action('wp_head', 'svic_output_rank_math_schema_fallbacks', 998);

if (!function_exists('svic_output_single_product_schema')) {
    function svic_output_single_product_schema(): void
    {
        // When Rank Math is active, let its Product schema be the single source of truth.
        if (defined('RANK_MATH_VERSION')) {
            return;
        }

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

if (!function_exists('svic_output_single_product_schema_fallback')) {
    function svic_output_single_product_schema_fallback(): void
    {
        if (is_admin() || !function_exists('is_product') || !is_product() || !class_exists('WooCommerce')) {
            return;
        }

        if (defined('RANK_MATH_VERSION') && !empty($GLOBALS['svic_rank_math_product_seen'])) {
            return;
        }

        $product_id = get_queried_object_id();
        $product    = $product_id && function_exists('wc_get_product') ? wc_get_product($product_id) : null;
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

add_action('wp_head', 'svic_output_single_product_schema_fallback', 999);

if (!function_exists('svic_output_rank_math_meta_fallback')) {
    function svic_output_rank_math_meta_fallback(): void
    {
        if (is_admin() || !defined('RANK_MATH_VERSION')) {
            return;
        }

        if (!empty($GLOBALS['svic_rank_math_meta_seen'])) {
            return;
        }

        $canonical = svic_get_localized_canonical_url();
        if (!is_string($canonical) || $canonical === '') {
            $canonical = function_exists('svic_current_base_url') ? svic_current_base_url() : home_url(add_query_arg([]));
        }

        if (!is_string($canonical) || $canonical === '') {
            return;
        }

        $title       = wp_get_document_title();
        $description = '';
        $image_url   = '';

        if (is_front_page() && function_exists('svic_homepage_meta_definitions')) {
            $meta        = svic_homepage_meta_definitions();
            $description = isset($meta['description']) ? trim((string) $meta['description']) : '';
            $image_meta  = svic_get_homepage_hero_image_meta();
            $image_url   = is_array($image_meta) && !empty($image_meta['url']) ? (string) $image_meta['url'] : '';
        } elseif (function_exists('is_page_template') && is_page_template('page-compare.php') && function_exists('svic_get_compare_page_meta_definitions')) {
            $meta        = svic_get_compare_page_meta_definitions();
            $description = isset($meta['description']) ? trim((string) $meta['description']) : '';
            $image_meta  = isset($meta['image']) && is_array($meta['image']) ? $meta['image'] : [];
            $image_url   = isset($image_meta['url']) ? (string) $image_meta['url'] : '';
        } else {
            $post_id = get_queried_object_id();
            if ($post_id) {
                if (function_exists('is_singular') && is_singular('product') && function_exists('wc_get_product')) {
                    $product = wc_get_product($post_id);
                    if ($product instanceof WC_Product) {
                        $description = wp_strip_all_tags($product->get_short_description() ?: $product->get_description());
                    }
                }

                if ($description === '') {
                    $description = wp_strip_all_tags(get_the_excerpt($post_id));
                }
                if ($description === '') {
                    $description = wp_trim_words(strip_shortcodes(wp_strip_all_tags(get_post_field('post_content', $post_id))), 32, '…');
                }

                $image_meta = svic_get_featured_image_meta($post_id);
                $image_url  = is_array($image_meta) && !empty($image_meta['url']) ? (string) $image_meta['url'] : '';
            }
        }

        echo '<link rel="canonical" href="' . esc_url($canonical) . "\" />\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

        if ($description !== '') {
            echo '<meta name="description" content="' . esc_attr($description) . "\" />\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo '<meta property="og:title" content="' . esc_attr($title) . "\" />\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo '<meta property="og:description" content="' . esc_attr($description) . "\" />\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo '<meta property="og:url" content="' . esc_attr($canonical) . "\" />\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . "\" />\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo '<meta name="twitter:card" content="summary_large_image" />' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo '<meta name="twitter:title" content="' . esc_attr($title) . "\" />\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo '<meta name="twitter:description" content="' . esc_attr($description) . "\" />\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            if ($image_url !== '') {
                echo '<meta property="og:image" content="' . esc_url($image_url) . "\" />\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo '<meta name="twitter:image" content="' . esc_url($image_url) . "\" />\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }
        }
    }
}

add_action('wp_head', 'svic_output_rank_math_meta_fallback', 999);

add_filter('wp_robots', function (array $robots): array {
    $is_order_tracking = is_page('order-tracking') || is_page_template('page-order-tracking.php');

    if ((function_exists('is_cart') && is_cart())
        || (function_exists('is_checkout') && is_checkout())
        || (function_exists('is_account_page') && is_account_page())
        || $is_order_tracking) {
        $robots['noindex'] = true;
        $robots['noarchive'] = true;
    }

    return $robots;
}, 20);

// Prevent WooCommerce from outputting its own Product structured data when Rank Math (or our custom schema) is present.
add_filter('woocommerce_structured_data_enabled', function ($enabled) {
    if (is_product()) {
        return false;
    }

    return $enabled;
}, 10, 1);

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

if (!function_exists('svic_output_breadcrumb_schema')) {
    /**
     * Emits a JSON-LD BreadcrumbList block in wp_head for Google rich results.
     * Complements the microdata breadcrumbs already rendered in the HTML.
     */
    function svic_output_breadcrumb_schema(): void
    {
        if (!svic_should_render_breadcrumbs()) {
            return;
        }

        $items = svic_get_breadcrumb_items();
        if (count($items) < 2) {
            return;
        }

        $list_elements = [];
        foreach ($items as $index => $item) {
            $element = [
                '@type'    => 'ListItem',
                'position' => $index + 1,
                'name'     => $item['label'] ?? '',
            ];
            if (!empty($item['url'])) {
                $element['item'] = esc_url_raw($item['url']);
            }
            $list_elements[] = $element;
        }

        $schema = [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $list_elements,
        ];

        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}
add_action('wp_head', 'svic_output_breadcrumb_schema', 10);

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
        'Phone: ' . ($phone !== '' ? $phone : 'n/a'),
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

/**
 * Rate-limit support form submissions per IP: max 5 per hour.
 * Runs before svic_handle_support_form (priority 1).
 */
add_action('admin_post_nopriv_svic_support_form', function () {
    $ip  = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'] ?? ''));
    $key = 'svic_rate_' . md5($ip);
    $count = (int) get_transient($key);
    if ($count >= 5) {
        wp_die(
            'Too many support requests. Please wait before submitting again.',
            'Rate Limit Exceeded',
            [ 'response' => 429 ]
        );
    }
    set_transient($key, $count + 1, HOUR_IN_SECONDS);
}, 1);

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

if (!function_exists('svic_preferred_sitemap_url')) {
    /**
     * Return the canonical sitemap endpoint for the active sitemap adapter.
     */
    function svic_preferred_sitemap_url(): string
    {
        if (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION')) {
            return home_url('/sitemap_index.xml');
        }

        return home_url('/wp-sitemap.xml');
    }
}

// Ensure robots.txt advertises the canonical sitemap endpoint.
add_filter('robots_txt', function ($output, $public) {
    // Respect site visibility setting; only append when public
    if ((int) get_option('blog_public', 1) !== 1) {
        return $output;
    }

    $line = 'Sitemap: ' . esc_url_raw(svic_preferred_sitemap_url());

    // Avoid duplicate lines if a plugin already added it
    if (stripos($output, 'Sitemap:') === false) {
        $output = rtrim((string) $output) . "\n" . $line . "\n";
    }

    return $output;
}, 10, 2);

if (!function_exists('svic_current_request_path')) {
    /**
     * Return the raw request path without query parameters.
     */
    function svic_current_request_path(): string
    {
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        if (!is_string($request_uri) || $request_uri === '') {
            return '';
        }

        $parsed = wp_parse_url($request_uri);
        if (!is_array($parsed) || empty($parsed['path']) || !is_string($parsed['path'])) {
            return '';
        }

        $path = '/' . ltrim($parsed['path'], '/');
        return $path === '/' ? '/' : rtrim($path, '/');
    }
}

if (!function_exists('svic_redirect_legacy_sitemap_request')) {
    /**
     * Redirect legacy sitemap URLs before Rank Math's redirection module can
     * canonicalize them to the homepage.
     */
    function svic_redirect_legacy_sitemap_request(): void
    {
        if (is_admin()) {
            return;
        }

        $path = strtolower(svic_current_request_path());
        if (!in_array($path, ['/sitemap.xml', '/zh/sitemap.xml', '/zh-cn/sitemap.xml'], true)) {
            return;
        }

        wp_safe_redirect(svic_preferred_sitemap_url(), 301);
        exit;
    }
}

add_action('parse_request', 'svic_redirect_legacy_sitemap_request', 0);
add_action('template_redirect', 'svic_redirect_legacy_sitemap_request', -20);

if (!function_exists('svic_rank_math_sitemap_excluded_page_slugs')) {
    function svic_rank_math_sitemap_excluded_page_slugs(): array
    {
        return ['my-account', 'cart', 'checkout', 'order-tracking'];
    }
}

if (!function_exists('svic_should_exclude_from_sitemap')) {
    function svic_should_exclude_from_sitemap(int $post_id): bool
    {
        if ($post_id <= 0 || get_post_type($post_id) !== 'page') {
            return false;
        }

        $slug = get_post_field('post_name', $post_id);
        return is_string($slug) && in_array($slug, svic_rank_math_sitemap_excluded_page_slugs(), true);
    }
}

// Keep utility / transactional pages out of Rank Math XML sitemaps.
add_filter('rank_math/sitemap/exclude_post', function ($exclude, $post_id) {
    return $exclude || svic_should_exclude_from_sitemap((int) $post_id);
}, 10, 2);

// Refresh visible sitemap lastmod values after theme deployments so Search Console sees meaningful content updates.
add_filter('rank_math/sitemap/entry', function ($url, $type, $object) {
    if (!is_array($url) || !isset($object->ID)) {
        return $url;
    }

    $post_id = (int) $object->ID;
    if ($post_id <= 0 || !in_array(get_post_type($post_id), ['page', 'post', 'product'], true)) {
        return $url;
    }

    if (svic_should_exclude_from_sitemap($post_id)) {
        return false;
    }

    $post_modified = get_post_modified_time('c', true, $post_id);
    $deploy_marker = svic_get_theme_deploy_marker();
    $deploy_time   = is_numeric($deploy_marker) ? gmdate('c', (int) $deploy_marker) : '';

    if ($post_modified && $deploy_time) {
        $url['mod'] = strtotime($deploy_time) > strtotime($post_modified) ? $deploy_time : $post_modified;
    } elseif ($post_modified) {
        $url['mod'] = $post_modified;
    }

    return $url;
}, 20, 3);

// Exclude sensitive or utility pages from the core sitemap
add_filter('wp_sitemaps_posts_query_args', function ($args, $postType) {
    if ($postType !== 'page') {
        return $args;
    }

    // Keep utility/transactional pages out of sitemaps to avoid 3xx/robots conflicts.
    $slugsToExclude = svic_rank_math_sitemap_excluded_page_slugs();
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

    $notIn                = isset($args['post__not_in']) ? (array) $args['post__not_in'] : [];
    $args['post__not_in'] = array_unique(array_merge($notIn, $idsToExclude));

    return $args;
}, 10, 2);

// Remove the default user (author) sitemap to avoid thin archive URLs and 5xxs on bad author slugs.
add_filter('wp_sitemaps_add_provider', function ($provider, $name) {
    if ($name === 'users') {
        return false;
    }

    return $provider;
}, 10, 2);

if (!function_exists('svic_seo_trim_text')) {
    /**
     * Keep generated SEO strings inside practical SERP limits without breaking
     * multibyte Chinese copy.
     */
    function svic_seo_trim_text($value, int $max_length): string
    {
        $text = trim(wp_strip_all_tags((string) $value));
        $text = preg_replace('/\s+/u', ' ', $text) ?: $text;

        $length = function_exists('mb_strlen') ? mb_strlen($text, 'UTF-8') : strlen($text);
        if ($length <= $max_length) {
            return $text;
        }

        $slice_length = max(1, $max_length - 1);
        $slice = function_exists('mb_substr') ? mb_substr($text, 0, $slice_length, 'UTF-8') : substr($text, 0, $slice_length);
        $slice = rtrim((string) $slice, " \t\n\r\0\x0B,.;:，。；：、-");

        return $slice . '…';
    }
}

if (!function_exists('svic_filter_rank_math_serp_title_length')) {
    function svic_filter_rank_math_serp_title_length($title)
    {
        $clean = trim(wp_strip_all_tags((string) $title));
        if ($clean === '') {
            return $title;
        }

        $length = function_exists('mb_strlen') ? mb_strlen($clean, 'UTF-8') : strlen($clean);
        if ($length <= 70) {
            return $clean;
        }

        // Rank Math commonly appends the site name with a pipe. Drop that first
        // before truncating the actual post/page title.
        $parts = preg_split('/\s+[|｜]\s+/u', $clean);
        if (is_array($parts) && isset($parts[0]) && trim($parts[0]) !== '') {
            $clean = trim($parts[0]);
        }

        return svic_seo_trim_text($clean, 70);
    }

    add_filter('rank_math/frontend/title', 'svic_filter_rank_math_serp_title_length', 95);
    add_filter('rank_math/opengraph/facebook/og_title', 'svic_filter_rank_math_serp_title_length', 95);
    add_filter('rank_math/opengraph/twitter/twitter_title', 'svic_filter_rank_math_serp_title_length', 95);
}

if (!function_exists('svic_filter_rank_math_serp_description_length')) {
    function svic_filter_rank_math_serp_description_length($description)
    {
        if (trim(wp_strip_all_tags((string) $description)) === '') {
            return $description;
        }

        return svic_seo_trim_text($description, 160);
    }

    add_filter('rank_math/frontend/description', 'svic_filter_rank_math_serp_description_length', 95);
    add_filter('rank_math/frontend/snippet_description', 'svic_filter_rank_math_serp_description_length', 95);
    add_filter('rank_math/opengraph/facebook/og_description', 'svic_filter_rank_math_serp_description_length', 95);
    add_filter('rank_math/opengraph/twitter/twitter_description', 'svic_filter_rank_math_serp_description_length', 95);
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
    // Slimmer font weights to reduce render-blocking transfers.
    $locale      = svic_current_locale();
    $is_chinese  = is_string($locale) && stripos($locale, 'zh') === 0;
    $font_url    = $is_chinese
        ? 'https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Noto+Sans+SC:wght@400;600&display=swap'
        : 'https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap';

    wp_enqueue_style('svicloudtvbox-fonts', $font_url, [], null);

    // Make Google Fonts non-render-blocking using media="print" swap trick.
    // The font loads async and swaps to "all" once loaded, avoiding FCP delay.
    add_filter('style_loader_tag', function ($tag, $handle) {
        if ($handle === 'svicloudtvbox-fonts') {
            // Change media="all" to media="print" onload="this.media='all'"
            $tag = str_replace(
                "media='all'",
                "media='print' onload=\"this.media='all'\"",
                $tag
            );
        }
        return $tag;
    }, 10, 2);

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
    $is_privacy_policy_page = is_page_template('page-privacy-policy.php')
        || is_page('privacy-policy');
    $is_shipping_policy_page = is_page_template('page-shipping-policy.php')
        || is_page('shipping-policy');
    $is_policy_page = $is_return_policy_page || $is_legal_disclaimer_page || $is_privacy_policy_page || $is_shipping_policy_page;
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

    // Theme script — use minified version in production (built with terser)
    $js_file_name = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? 'theme.js' : 'theme.min.js';
    $js_file_path = get_template_directory() . '/assets/js/' . $js_file_name;
    
    // Fall back to unminified if minified doesn't exist
    if (!file_exists($js_file_path)) {
        $js_file_name = 'theme.js';
    }
    
    wp_enqueue_script(
        'svicloudtvbox-script',
        get_template_directory_uri() . '/assets/js/' . $js_file_name,
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

    // Aggressively remove unused CSS on the homepage to improve FCP/LCP.
    // These styles are needed on cart/checkout but not on the marketing homepage.
    $styles_to_remove = [
        // WordPress block editor styles (not using blocks on homepage)
        'wp-block-library',
        'wp-block-library-theme',
        'global-styles',
        'classic-theme-styles',

        // WooCommerce block styles (classic checkout, no blocks)
        'wc-block-style',
        'wc-blocks-style',
        'wc-address-autocomplete',

        // Advanced Coupons for WooCommerce (cart/checkout only)
        'acfwf-wc-cart-block-integration',
        'acfwf-wc-checkout-block-integration',

        // WooCommerce Conditional Product Fees (checkout only)
        'woocommerce-conditional-product-fees-for-checkout',

        // Misc plugin styles not needed on homepage
        'hostinger-reach-subscription-block',
        'brands-styles',
        'mediaelement',
        'wp-mediaelement',
    ];

    foreach ($styles_to_remove as $handle) {
        wp_dequeue_style($handle);
        wp_deregister_style($handle);
    }
}, 999);

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

if (!function_exists('svic_should_preserve_lang_for_woocommerce')) {
    function svic_should_preserve_lang_for_woocommerce(): bool
    {
        if (is_admin() || !function_exists('svic_language_query_value') || !function_exists('svic_url_with_lang')) {
            return false;
        }

        $lang = svic_language_query_value();
        return $lang === 'zh' || $lang === 'zh-cn';
    }
}

if (!function_exists('svic_filter_woocommerce_url_with_lang')) {
    function svic_filter_woocommerce_url_with_lang($url)
    {
        if (!svic_should_preserve_lang_for_woocommerce() || !is_string($url) || $url === '') {
            return $url;
        }

        return svic_url_with_lang($url, svic_language_query_value());
    }

    add_filter('woocommerce_get_cart_url', 'svic_filter_woocommerce_url_with_lang', 20);
    add_filter('woocommerce_get_checkout_url', 'svic_filter_woocommerce_url_with_lang', 20);
}

// Keep the zh/zh-cn prefix when WooCommerce redirects away from checkout (e.g. empty cart).
add_action('template_redirect', function () {
    if (!svic_should_preserve_lang_for_woocommerce()) {
        return;
    }

    if (!function_exists('is_checkout') || !is_checkout() || !function_exists('WC')) {
        return;
    }

    if (function_exists('is_order_received_page') && is_order_received_page()) {
        return;
    }

    if (function_exists('is_checkout_pay_page') && is_checkout_pay_page()) {
        return;
    }

    $wc = WC();
    if (!$wc || !isset($wc->cart) || !$wc->cart) {
        return;
    }

    if (!$wc->cart->is_empty()) {
        return;
    }

    $cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : '';
    if (!is_string($cart_url) || $cart_url === '') {
        return;
    }

    $destination = svic_url_with_lang($cart_url, svic_language_query_value());
    if (!is_string($destination) || $destination === '') {
        return;
    }

    wp_safe_redirect($destination, 302);
    exit;
}, 1);

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
        '/zh/reviews' => [
            'path' => '/blog/',
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

if (!function_exists('svic_theme_favicon_assets')) {
    function svic_theme_favicon_assets(): array
    {
        $base_dir = get_template_directory() . '/assets/images';
        $base_uri = get_template_directory_uri() . '/assets/images';

        return [
            'ico'   => ['path' => $base_dir . '/favicon.ico', 'url' => $base_uri . '/favicon.ico'],
            '32'    => ['path' => $base_dir . '/favicon-32.png', 'url' => $base_uri . '/favicon-32.png', 'size' => '32x32'],
            '96'    => ['path' => $base_dir . '/favicon-96.png', 'url' => $base_uri . '/favicon-96.png', 'size' => '96x96'],
            '192'   => ['path' => $base_dir . '/favicon-192.png', 'url' => $base_uri . '/favicon-192.png', 'size' => '192x192'],
            'apple' => ['path' => $base_dir . '/apple-touch-icon.png', 'url' => $base_uri . '/apple-touch-icon.png', 'size' => '180x180'],
        ];
    }
}

if (!function_exists('svic_theme_favicon_path')) {
    function svic_theme_favicon_path(): string
    {
        $assets = svic_theme_favicon_assets();
        return $assets['32']['path'];
    }
}

if (!function_exists('svic_theme_favicon_url')) {
    function svic_theme_favicon_url(): string
    {
        $assets = svic_theme_favicon_assets();
        return $assets['32']['url'];
    }
}

if (!function_exists('svic_theme_output_favicons')) {
    function svic_theme_output_favicons(): void
    {
        if (function_exists('has_site_icon') && has_site_icon()) {
            return;
        }

        $assets = svic_theme_favicon_assets();

        $links = [];
        foreach (['32', '96', '192'] as $key) {
            if (isset($assets[$key]) && file_exists($assets[$key]['path'])) {
                $links[] = sprintf(
                    '<link rel="icon" type="image/png" sizes="%1$s" href="%2$s" />',
                    esc_attr($assets[$key]['size']),
                    esc_url($assets[$key]['url'])
                );
            }
        }

        if (isset($assets['apple']) && file_exists($assets['apple']['path'])) {
            $links[] = sprintf(
                '<link rel="apple-touch-icon" sizes="%1$s" href="%2$s" />',
                esc_attr($assets['apple']['size']),
                esc_url($assets['apple']['url'])
            );
        }

        if (isset($assets['ico']) && file_exists($assets['ico']['path'])) {
            $links[] = sprintf(
                '<link rel="shortcut icon" href="%s" />',
                esc_url($assets['ico']['url'])
            );
        }

        if (empty($links)) {
            return;
        }

        echo "\n    " . implode("\n    ", $links) . "\n";
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

        $assets = svic_theme_favicon_assets();
        $serve = null;
        $content_type = 'image/png';

        if (isset($assets['ico']) && file_exists($assets['ico']['path'])) {
            $serve = $assets['ico']['path'];
            $content_type = 'image/x-icon';
        } elseif (isset($assets['32']) && file_exists($assets['32']['path'])) {
            $serve = $assets['32']['path'];
        }

        if ($serve === null) {
            return;
        }

        if (!headers_sent()) {
            header('Content-Type: ' . $content_type);
            header('Content-Length: ' . (string) filesize($serve));
        }

        readfile($serve);
        exit;
    }

    add_action('do_favicon', 'svic_theme_serve_favicon');
add_action('template_redirect', function () {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    if ($request_uri === '/favicon.ico' || $request_uri === 'favicon.ico') {
        svic_theme_serve_favicon();
    }
}, 0);

// Seed missing Rank Math focus keywords for published content (posts/pages/products)
// to satisfy SEO analyzer checks. Runs once per deploy and only in admin for users
// who can manage options.
add_action('admin_init', function () {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Avoid re-running after initial seed
    $already_seeded = get_option('svic_rankmath_focus_seeded');
    if ($already_seeded) {
        return;
    }

    $types = ['post', 'page'];
    if (post_type_exists('product')) {
        $types[] = 'product';
    }

    $query = new WP_Query([
        'post_type'      => $types,
        'post_status'    => 'publish',
        'posts_per_page' => 200,
        'fields'         => 'ids',
        'meta_query'     => [
            'relation' => 'OR',
            [
                'key'     => 'rank_math_focus_keyword',
                'compare' => 'NOT EXISTS',
            ],
            [
                'key'     => 'rank_math_focus_keyword',
                'value'   => '',
                'compare' => '=',
            ],
        ],
    ]);

    if (!($query instanceof WP_Query) || empty($query->posts)) {
        update_option('svic_rankmath_focus_seeded', ['count' => 0, 'ts' => time()], false);
        return;
    }

    $count = 0;
    foreach ($query->posts as $post_id) {
        $title = get_the_title($post_id);
        if (!is_string($title) || $title === '') {
            continue;
        }

        $keyword = sanitize_text_field(wp_trim_words($title, 12, ''));
        if ($keyword === '') {
            continue;
        }

        update_post_meta($post_id, 'rank_math_focus_keyword', $keyword);
        $count++;
    }

    update_option('svic_rankmath_focus_seeded', ['count' => $count, 'ts' => time()], false);
}, 99);
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
        '_svic_content_zh_cn',
        '_svic_title_zh_cn',
        '_svic_description_zh_cn',
        '_svic_content_en_us',
        '_svic_title_en_us',
        '_svic_description_en_us',
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

    $locale = svic_current_locale();
    if (!is_string($locale) || stripos($locale, 'zh') !== 0) {
        // Try English override if present
        if (stripos($locale, 'en') === 0) {
            $translated_en = get_post_meta(get_the_ID(), '_svic_content_en_us', true);
            if (is_string($translated_en) && $translated_en !== '') {
                $content = $translated_en;
                if (class_exists('SVIC_Markdown') && !SVIC_Markdown::looks_like_html($content)) {
                    $content = SVIC_Markdown::to_html($content);
                }
                $content = svic_replace_inline_code_placeholders($content, get_the_ID());
                return wp_kses_post($content) ?: $content;
            }
        }
        return $content;
    }

    $base = $content;

    $meta_key = stripos($locale, 'zh_cn') === 0 ? '_svic_content_zh_cn' : '_svic_content_zh_tw';
    $translated = get_post_meta(get_the_ID(), $meta_key, true);
    if (is_string($translated) && $translated !== '') {
        $base = $translated;

        if (class_exists('SVIC_Markdown') && !SVIC_Markdown::looks_like_html($base)) {
            $base = SVIC_Markdown::to_html($base);
        }

        $base = svic_replace_inline_code_placeholders($base, get_the_ID());
    }

    if (function_exists('svic_localize_brand_in_text')) {
        $base = svic_localize_brand_in_text($base, $locale);
    }

    $safe_html = wp_kses_post($base);
    return $safe_html !== '' ? $safe_html : $content;
}, 20);

add_filter('the_title', function ($title, $post_id) {
    if (!is_singular('post') || get_the_ID() !== $post_id) {
        return $title;
    }

    if (!function_exists('svic_current_locale')) {
        return $title;
    }

    $locale = svic_current_locale();
    if (!is_string($locale)) {
        return $title;
    }

    if (stripos($locale, 'zh') !== 0 && stripos($locale, 'en') !== 0) {
        return $title;
    }

    $meta_key = '';
    if (stripos($locale, 'zh') === 0) {
        $meta_key = stripos($locale, 'zh_cn') === 0 ? '_svic_title_zh_cn' : '_svic_title_zh_tw';
    } elseif (stripos($locale, 'en') === 0) {
        $meta_key = '_svic_title_en_us';
    }

    $translated = $meta_key ? get_post_meta($post_id, $meta_key, true) : '';
    $chosen_title = is_string($translated) && $translated !== '' ? $translated : $title;

    if (function_exists('svic_localize_brand_in_text')) {
        $chosen_title = svic_localize_brand_in_text($chosen_title, $locale);
    }

    return $chosen_title;
}, 10, 2);

add_filter('get_the_excerpt', function ($excerpt, $post) {
    if (!($post instanceof WP_Post) || $post->post_type !== 'post') {
        return $excerpt;
    }

    if (!function_exists('svic_current_locale')) {
        return $excerpt;
    }

    $locale = svic_current_locale();
    if (!is_string($locale)) {
        return $excerpt;
    }

    if (stripos($locale, 'zh') !== 0 && stripos($locale, 'en') !== 0) {
        return $excerpt;
    }

    $meta_key = '';
    if (stripos($locale, 'zh') === 0) {
        $meta_key = stripos($locale, 'zh_cn') === 0 ? '_svic_description_zh_cn' : '_svic_description_zh_tw';
    } elseif (stripos($locale, 'en') === 0) {
        $meta_key = '_svic_description_en_us';
    }

    $translated = $meta_key ? get_post_meta($post->ID, $meta_key, true) : '';
    $chosen_excerpt = is_string($translated) && $translated !== '' ? $translated : $excerpt;

    if (function_exists('svic_localize_brand_in_text')) {
        $chosen_excerpt = svic_localize_brand_in_text((string) $chosen_excerpt, $locale);
    }

    return $chosen_excerpt;
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
        if (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION')) {
            return false;
        }

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

            if (in_array($normalized, ['/sitemap_index.xml', '/zh/sitemap_index.xml', '/zh-cn/sitemap_index.xml'], true)) {
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

if (!function_exists('svic_is_tracking_enabled')) {
    /**
     * Central gate for frontend analytics scripts.
     */
    function svic_is_tracking_enabled(): bool
    {
        if (is_admin()) {
            return false;
        }

        if (defined('WP_INSTALLING') && WP_INSTALLING) {
            return false;
        }

        /**
         * Allow integrations or environments to disable tracking.
         *
         * @param bool $enabled
         */
        return (bool) apply_filters('svic_tracking_enabled', true);
    }
}

if (!function_exists('svic_get_tracking_ids')) {
    /**
     * Collects configured tracking IDs in one place.
     */
    function svic_get_tracking_ids(): array
    {
        $ga4_id = defined('SVIC_GA4_MEASUREMENT_ID') ? trim((string) SVIC_GA4_MEASUREMENT_ID) : '';
        $ga4_id = (string) apply_filters('svic_ga4_measurement_id', $ga4_id);

        $meta_pixel_id = defined('SVIC_META_PIXEL_ID') ? trim((string) SVIC_META_PIXEL_ID) : '';
        $meta_pixel_id = (string) apply_filters('svic_meta_pixel_id', $meta_pixel_id);

        return [
            'ga4'       => $ga4_id,
            'metaPixel' => $meta_pixel_id,
        ];
    }
}

if (!function_exists('svic_render_tracking_config')) {
    /**
     * Outputs lightweight config for deferred tracking loaders.
     */
    function svic_render_tracking_config(): void
    {
        if (defined('GOOGLESITEKIT_VERSION')) {
            // Let Site Kit handle GA if present.
            return;
        }

        if (!svic_is_tracking_enabled()) {
            return;
        }

        $ids = svic_get_tracking_ids();
        if ($ids['ga4'] === '' && $ids['metaPixel'] === '') {
            return;
        }
        ?>
        <script>
        window.svicTrackingConfig = Object.assign(window.svicTrackingConfig || {}, {
            ga4Id: <?php echo $ids['ga4'] !== '' ? "'" . esc_js($ids['ga4']) . "'" : 'null'; ?>,
            metaPixelId: <?php echo $ids['metaPixel'] !== '' ? "'" . esc_js($ids['metaPixel']) . "'" : 'null'; ?>
        });
        </script>
        <?php
    }
}
add_action('wp_head', 'svic_render_tracking_config', 19);

if (!function_exists('svic_render_deferred_tracking_loader')) {
    /**
     * Defers loading GA4 / Meta Pixel until after first interaction or idle.
     */
    function svic_render_deferred_tracking_loader(): void
    {
        if (defined('GOOGLESITEKIT_VERSION')) {
            return;
        }

        if (!svic_is_tracking_enabled()) {
            return;
        }
        ?>
        <script>
        (function() {
            var cfg = window.svicTrackingConfig || {};
            if (!cfg.ga4Id && !cfg.metaPixelId) {
                return;
            }

            var loaded = false;
            function loadScripts() {
                if (loaded) return;
                loaded = true;

                if (cfg.ga4Id) {
                    var gtagScript = document.createElement('script');
                    gtagScript.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(cfg.ga4Id);
                    gtagScript.async = true;
                    document.head.appendChild(gtagScript);

                    window.dataLayer = window.dataLayer || [];
                    function gtag(){ dataLayer.push(arguments); }
                    window.gtag = window.gtag || gtag;
                    gtag('js', new Date());
                    gtag('config', cfg.ga4Id, {
                        send_page_view: true,
                        page_location: window.location.href,
                        page_path: window.location.pathname + window.location.search,
                        page_title: document.title
                    });
                }

                if (cfg.metaPixelId) {
                    !function(f,b,e,v,n,t,s){
                        if(f.fbq) return;
                        n=f.fbq=function(){ n.callMethod ? n.callMethod.apply(n,arguments) : n.queue.push(arguments); };
                        if(!f._fbq) f._fbq=n;
                        n.push=n; n.loaded=!0; n.version='2.0'; n.queue=[];
                        t=b.createElement(e); t.async=!0; t.src=v;
                        s=b.getElementsByTagName(e)[0]; s.parentNode.insertBefore(t,s);
                    }(window, document,'script','https://connect.facebook.net/en_US/fbevents.js');
                    window.fbq('init', cfg.metaPixelId);
                    window.fbq('track', 'PageView');
                }
            }

            var triggered = false;
            var triggerLoad = function() {
                if (triggered) return;
                triggered = true;
                loadScripts();
                removeListeners();
            };

            function removeListeners() {
                ['pointerdown','keydown','scroll','touchstart'].forEach(function(evt) {
                    window.removeEventListener(evt, triggerLoad, passiveOpts);
                });
            }

            var passiveOpts = { once: true, passive: true };
            ['pointerdown','keydown','scroll','touchstart'].forEach(function(evt) {
                window.addEventListener(evt, triggerLoad, passiveOpts);
            });
        })();
        </script>
        <?php
    }
}
add_action('wp_footer', 'svic_render_deferred_tracking_loader', 5);

/**
 * Disable tracking on home/landing to reduce lab-performance noise.
 */
add_filter('svic_tracking_enabled', function (bool $enabled): bool {
    if (is_front_page() || is_home() || is_page_template('front-page.php')) {
        return false;
    }
    return $enabled;
});

/**
 * Drop jQuery Migrate to reduce legacy JS on modern browsers.
 */
add_action('wp_default_scripts', function ($scripts) {
    if (!isset($scripts->registered['jquery'])) {
        return;
    }
    $jquery = $scripts->registered['jquery'];
    if (is_array($jquery->deps)) {
        $jquery->deps = array_values(array_diff($jquery->deps, ['jquery-migrate']));
    }
});

/**
 * Preconnect to Google Fonts domains for faster font fetch.
 */
add_filter('wp_resource_hints', function ($urls, $relation_type) {
    if ($relation_type !== 'preconnect') {
        return $urls;
    }
    $fonts = [
        'https://fonts.googleapis.com',
        'https://fonts.gstatic.com',
    ];
    foreach ($fonts as $font_origin) {
        if (!in_array($font_origin, $urls, true)) {
            $urls[] = $font_origin;
        }
    }
    return $urls;
}, 10, 2);

/**
 * Dequeue heavy Woo scripts on non-Woo/non-cart pages.
 *
 * This improves PageSpeed scores by removing WooCommerce attribution tracking,
 * cart fragments, and plugin scripts that are only needed on shop/cart/checkout.
 */
add_action('wp_enqueue_scripts', function () {
    if (is_admin()) {
        return;
    }

    $is_woo = function_exists('is_woocommerce') && is_woocommerce();
    $is_cart = function_exists('is_cart') && is_cart();
    $is_checkout = function_exists('is_checkout') && is_checkout();
    $is_account = function_exists('is_account_page') && is_account_page();

    if ($is_woo || $is_cart || $is_checkout || $is_account) {
        return;
    }

    // Scripts to remove on non-WooCommerce pages
    $handles = [
        'woocommerce',
        'wc-cart-fragments',
        'wc-add-to-cart',
        'jquery-blockui',
        'js-cookie',

        // WooCommerce order attribution / sourcebuster (adds ~8KB + network requests)
        'sourcebuster-js',
        'wc-order-attribution',

        // WooCommerce Conditional Product Fees plugin
        'woocommerce-conditional-product-fees-for-checkout-public',
        'woocommerce-conditional-product-fees-for-checkout-public-js',

        // Extra plugin scripts not needed outside checkout
        'jquery-bind-first',
        'tld-js',
        'public-js',
    ];
    foreach ($handles as $handle) {
        wp_dequeue_script($handle);
        wp_deregister_script($handle);
    }

    // Styles to remove on non-WooCommerce pages
    $style_handles = [
        'woocommerce-conditional-product-fees-for-checkout-public-css',
    ];
    foreach ($style_handles as $handle) {
        wp_dequeue_style($handle);
        wp_deregister_style($handle);
    }
}, 99);

/**
 * Trim heavy/unused scripts on the homepage/front page by URL pattern.
 *
 * NOTE: We intentionally keep Google Analytics (gtag) scripts for tracking.
 * Only remove scripts that add no value to the homepage user experience.
 */
add_action('wp_print_scripts', function () {
    if (!is_front_page() && !is_page_template('front-page.php')) {
        return;
    }

    // URL patterns for scripts to remove on homepage.
    // These are tracking/utility scripts that don't affect homepage functionality.
    $patterns = [
        'tld.min.js',
        'public.js',
        'js.cookie-2.1.3.min.js',
        'jquery.tipTip.min.js',
        'sourcebuster.min.js',         // WooCommerce order attribution
        'order-attribution.min.js',    // WooCommerce order attribution
    ];

    $wp_scripts = wp_scripts();
    if (!$wp_scripts instanceof WP_Scripts) {
        return;
    }

    foreach ((array) $wp_scripts->queue as $handle) {
        $src = $wp_scripts->registered[$handle]->src ?? '';
        if ($src === '') {
            continue;
        }
        $full_src = wp_normalize_path($wp_scripts->base_url . $src);
        foreach ($patterns as $pattern) {
            if (strpos($full_src, $pattern) !== false) {
                wp_dequeue_script($handle);
                wp_deregister_script($handle);
                break;
            }
        }
    }
}, 100);

/**
 * Remove Google Sign-In SDK (GSI) on non-checkout pages.
 *
 * The Google for WooCommerce plugin loads the GSI SDK (/gsi/client, ~91KB)
 * globally for "Sign in with Google" functionality. However, this is only
 * useful on checkout/account pages. On homepage and other marketing pages,
 * it adds significant weight to the critical path without benefit.
 *
 * We filter out GSI-related script loader tags on non-essential pages.
 */
add_filter('script_loader_tag', function ($tag, $handle, $src) {
    // Only filter on frontend, non-checkout pages
    if (is_admin()) {
        return $tag;
    }

    // Keep GSI on pages where sign-in is useful
    $is_checkout = function_exists('is_checkout') && is_checkout();
    $is_account = function_exists('is_account_page') && is_account_page();
    $is_cart = function_exists('is_cart') && is_cart();

    if ($is_checkout || $is_account || $is_cart) {
        return $tag;
    }

    // Remove GSI client script (accounts.google.com/gsi/client)
    if (strpos($src, 'accounts.google.com/gsi/client') !== false) {
        return '<!-- GSI SDK deferred: not needed on this page -->';
    }

    return $tag;
}, 10, 3);

/**
 * Prevent Google for WooCommerce from loading GSI on non-checkout pages.
 *
 * The plugin uses the 'woocommerce_gla_handle_gsi_script' filter. We return
 * false on homepage to skip GSI initialization entirely.
 */
add_filter('woocommerce_gla_handle_gsi_script', function ($should_load) {
    if (is_admin()) {
        return $should_load;
    }

    // Only load GSI on checkout, cart, and account pages
    $is_checkout = function_exists('is_checkout') && is_checkout();
    $is_account = function_exists('is_account_page') && is_account_page();
    $is_cart = function_exists('is_cart') && is_cart();

    if ($is_checkout || $is_account || $is_cart) {
        return $should_load;
    }

    return false;
}, 10);

/**
 * Disable Site Kit's "Sign in with Google" on homepage.
 *
 * The GSI SDK (~91KB) is heavy and not needed on the marketing homepage.
 * Use Site Kit's filter to conditionally disable the feature.
 */
add_filter('googlesitekit_sign_in_with_google_button_enabled', function ($enabled) {
    // Disable on homepage to reduce JS payload
    if (!is_admin() && is_front_page()) {
        return false;
    }
    return $enabled;
}, 10);

// Also filter at module level
add_filter('googlesitekit_is_module_connected', function ($connected, $slug) {
    if ($slug === 'sign-in-with-google' && !is_admin() && is_front_page()) {
        return false;
    }
    return $connected;
}, 10, 2);

if (!function_exists('svic_render_ga4_purchase_event')) {
    /**
     * Fires a GA4 purchase event on the WooCommerce thank-you page.
     *
     * @param int $order_id
     */
    function svic_render_ga4_purchase_event(int $order_id): void
    {
        if (!svic_is_tracking_enabled()) {
            return;
        }

        if (!function_exists('wc_get_order')) {
            return;
        }

        $measurement_id = defined('SVIC_GA4_MEASUREMENT_ID') ? trim((string) SVIC_GA4_MEASUREMENT_ID) : '';
        $measurement_id = (string) apply_filters('svic_ga4_measurement_id', $measurement_id);
        if ($measurement_id === '' && !defined('GOOGLESITEKIT_VERSION')) {
            return;
        }

        $order = wc_get_order($order_id);
        if (!$order instanceof WC_Order) {
            return;
        }

        if (!$order->is_paid()) {
            return;
        }

        $currency = $order->get_currency() ?: 'USD';
        $items    = [];

        foreach ($order->get_items() as $item) {
            if (!$item instanceof WC_Order_Item_Product) {
                continue;
            }

            $product = $item->get_product();
            if (!$product) {
                continue;
            }

            $items[] = [
                'item_id'   => $product->get_sku() ?: $product->get_id(),
                'item_name' => $item->get_name(),
                'quantity'  => (float) $item->get_quantity(),
                'price'     => (float) $order->get_item_total($item, true, true),
            ];
        }

        $payload = [
            'transaction_id' => (string) $order->get_order_number(),
            'value'          => (float) $order->get_total(),
            'currency'       => $currency,
            'tax'            => (float) $order->get_total_tax(),
            'shipping'       => (float) $order->get_shipping_total(),
            'items'          => $items,
        ];

        /**
         * Filter the GA4 purchase payload before it is rendered.
         *
         * @param array    $payload
         * @param WC_Order $order
         */
        $payload = (array) apply_filters('svic_ga4_purchase_payload', $payload, $order);

        $json = wp_json_encode($payload);
        if (!is_string($json) || $json === '') {
            return;
        }
        ?>
        <script>
        if (typeof gtag === 'function') {
            gtag('event', 'purchase', <?php echo $json; ?>);
        }
        </script>
        <?php
    }
}

add_action('woocommerce_thankyou', 'svic_render_ga4_purchase_event', 20);

if (!function_exists('svic_render_google_ads_purchase_event')) {
    /**
     * Fires a Google Ads conversion event on the WooCommerce thank-you page.
     *
     * @param int $order_id
     */
    function svic_render_google_ads_purchase_event(int $order_id): void
    {
        $debug = isset($_GET['svic_gtag_debug']) && $_GET['svic_gtag_debug'] === '1';

        if (!svic_is_tracking_enabled()) {
            if ($debug) {
                ?>
                <script>
                console.warn('[SVIC] Google Ads conversion skipped: tracking disabled.');
                </script>
                <?php
            }
            return;
        }

        if (!function_exists('wc_get_order')) {
            if ($debug) {
                ?>
                <script>
                console.warn('[SVIC] Google Ads conversion skipped: wc_get_order missing.');
                </script>
                <?php
            }
            return;
        }

        $conversion_id = defined('SVIC_GOOGLE_ADS_CONVERSION_ID') ? trim((string) SVIC_GOOGLE_ADS_CONVERSION_ID) : '';
        $conversion_id = (string) apply_filters('svic_google_ads_conversion_id', $conversion_id);
        if ($conversion_id === '') {
            if ($debug) {
                ?>
                <script>
                console.warn('[SVIC] Google Ads conversion skipped: conversion ID missing.');
                </script>
                <?php
            }
            return;
        }

        $order = wc_get_order($order_id);
        if (!$order instanceof WC_Order) {
            if ($debug) {
                ?>
                <script>
                console.warn('[SVIC] Google Ads conversion skipped: order not found.');
                </script>
                <?php
            }
            return;
        }

        if (!$order->is_paid()) {
            if ($debug) {
                ?>
                <script>
                console.warn('[SVIC] Google Ads conversion skipped: order not paid.');
                </script>
                <?php
            }
            return;
        }

        $payload = [
            'send_to'        => $conversion_id,
            'value'          => (float) $order->get_total(),
            'currency'       => $order->get_currency() ?: 'USD',
            'transaction_id' => (string) $order->get_order_number(),
        ];

        /**
         * Filter the Google Ads purchase payload before it is rendered.
         *
         * @param array    $payload
         * @param WC_Order $order
         */
        $payload = (array) apply_filters('svic_google_ads_purchase_payload', $payload, $order);

        $json = wp_json_encode($payload);
        if (!is_string($json) || $json === '') {
            if ($debug) {
                ?>
                <script>
                console.warn('[SVIC] Google Ads conversion skipped: payload encoding failed.');
                </script>
                <?php
            }
            return;
        }
        ?>
        <script>
        if (typeof gtag === 'function') {
            gtag('event', 'conversion', <?php echo $json; ?>);
            <?php if ($debug) : ?>
            console.info('[SVIC] Google Ads conversion fired.', <?php echo $json; ?>);
            <?php endif; ?>
        }
        </script>
        <?php
    }
}

add_action('woocommerce_thankyou', 'svic_render_google_ads_purchase_event', 21);

if (!function_exists('svic_render_meta_pixel_purchase_event')) {
    /**
     * Fires a Meta Pixel Purchase event on the WooCommerce thank-you page.
     *
     * @param int $order_id
     */
    function svic_render_meta_pixel_purchase_event(int $order_id): void
    {
        if (!svic_is_tracking_enabled()) {
            return;
        }

        if (!function_exists('wc_get_order')) {
            return;
        }

        $pixel_id = defined('SVIC_META_PIXEL_ID') ? trim((string) SVIC_META_PIXEL_ID) : '';
        $pixel_id = (string) apply_filters('svic_meta_pixel_id', $pixel_id);
        if ($pixel_id === '') {
            return;
        }

        $order = wc_get_order($order_id);
        if (!$order instanceof WC_Order) {
            return;
        }

        if (!$order->is_paid()) {
            return;
        }

        $contents = [];

        foreach ($order->get_items() as $item) {
            if (!$item instanceof WC_Order_Item_Product) {
                continue;
            }

            $product = $item->get_product();
            if (!$product) {
                continue;
            }

            $contents[] = [
                'id'         => $product->get_sku() ?: $product->get_id(),
                'quantity'   => (int) $item->get_quantity(),
                'item_price' => (float) $order->get_item_total($item, true, true),
            ];
        }

        $payload = [
            'value'        => (float) $order->get_total(),
            'currency'     => $order->get_currency() ?: 'USD',
            'contents'     => $contents,
            'content_type' => 'product',
        ];

        /**
         * Filter the Meta Pixel Purchase payload before it is rendered.
         *
         * @param array    $payload
         * @param WC_Order $order
         */
        $payload = (array) apply_filters('svic_meta_pixel_purchase_payload', $payload, $order);

        $json = wp_json_encode($payload);
        if (!is_string($json) || $json === '') {
            return;
        }
        ?>
        <script>
        if (typeof fbq === 'function') {
            fbq('track', 'Purchase', <?php echo $json; ?>);
        }
        </script>
        <?php
    }
}

add_action('woocommerce_thankyou', 'svic_render_meta_pixel_purchase_event', 21);

if (!function_exists('svic_render_admin_thankyou_url')) {
    /**
     * Shows the thank-you URL on the WooCommerce order admin screen.
     *
     * @param WC_Order $order
     */
    function svic_render_admin_thankyou_url($order): void
    {
        if (!is_admin() || !$order instanceof WC_Order) {
            return;
        }

        $url = method_exists($order, 'get_checkout_order_received_url')
            ? $order->get_checkout_order_received_url()
            : '';

        if (!is_string($url) || $url === '') {
            return;
        }
        ?>
        <p class="form-field form-field-wide">
            <strong><?php echo esc_html__('Thank-you URL', SVIC_THEME_TEXT_DOMAIN); ?>:</strong>
            <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer">
                <?php echo esc_html($url); ?>
            </a>
        </p>
        <?php
    }
}

add_action('woocommerce_admin_order_data_after_order_details', 'svic_render_admin_thankyou_url', 20);

add_action('pre_get_posts', function ($query) {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($query->is_home()) {
        $query->set('posts_per_page', 12);
    }
});

// =============================================================================
// Epic B — Security baseline: HTTP security headers
// =============================================================================

add_action('send_headers', function () {
    // Prevent the site from being embedded in iframes on other origins.
    header('X-Frame-Options: SAMEORIGIN');

    // Stop browsers from MIME-sniffing the content type.
    header('X-Content-Type-Options: nosniff');

    // Send full URL for same-origin requests; only origin for cross-origin.
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // Disable browser features not used by this site.
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
});

// =============================================================================
// Epic D — WooCommerce: email branding
// =============================================================================

// NOTE (Epic D1 — Currency/locale): USD currency and store address are set in
// WooCommerce → Settings → General (database-driven). No code override is
// needed unless you want to hard-lock it — set "Currency" to "United States
// dollar ($ USD)" and "Selling location(s)" to "Sell to specific countries →
// United States" in the WooCommerce admin.

add_filter('woocommerce_email_from_name', function ($name) {
    return 'SVICLOUD TV Box';
});

add_filter('woocommerce_email_from_address', function ($email) {
    return 'orders@svicloudtvbox.us';
});

if (!function_exists('svic_customer_setup_email_copy')) {
    /**
     * Private customer-only setup copy. Do not store this in the public theme
     * translation registry because that registry is serialized into page HTML.
     */
    function svic_customer_setup_email_copy(?string $locale = null): array
    {
        $locale = $locale ?: (function_exists('get_locale') ? get_locale() : 'en_US');
        $is_simplified = stripos($locale, 'zh_CN') === 0 || stripos($locale, 'zh_Hans') === 0 || stripos($locale, 'zh-CN') === 0;
        $is_chinese = $is_simplified || stripos($locale, 'zh') === 0;

        if ($is_simplified) {
            return [
                'title' => '客户设定教学：安装 App',
                'intro' => '请保留这份客户专属步骤，第一次设定或日后重新安装 TV App 时都可使用。',
                'steps' => [
                    '请先将小云电视盒连上 Wi-Fi 或网线。',
                    '从主画面打开 Orz Browser。',
                    '在网址列输入 8989c.cc 并打开页面。',
                    '在 App 页面找到 Yogurt TV，然后下载并安装。',
                    '如果 Android 要求权限，请允许从此可信任来源安装，然后继续。',
                    '安装完成后打开 Yogurt TV。若任何步骤失败，请直接回覆此 Email，附上电视画面照片与订单编号。',
                ],
                'note' => '请勿公开分享此客户专属安装连结。App 名称与可用性可能会变动；如果页面看起来不同，请先联系我们，不要随便下载网路上的 APK。',
                'public_guide' => '一般公开 App 安装指南',
                'support' => '需要协助？请直接回覆此 Email，附上清楚的电视画面照片、订单编号，以及卡在哪一个步骤。',
            ];
        }

        if ($is_chinese) {
            return [
                'title' => '客戶設定教學：安裝 App',
                'intro' => '請保留這份客戶專屬步驟，第一次設定或日後重新安裝 TV App 時都可使用。',
                'steps' => [
                    '請先將小雲電視盒連上 Wi-Fi 或網路線。',
                    '從主畫面開啟 Orz Browser。',
                    '在網址列輸入 8989c.cc 並開啟頁面。',
                    '在 App 頁面找到 Yogurt TV，然後下載並安裝。',
                    '如果 Android 要求權限，請允許從此可信任來源安裝，然後繼續。',
                    '安裝完成後開啟 Yogurt TV。若任何步驟失敗，請直接回覆此 Email，附上電視畫面照片與訂單編號。',
                ],
                'note' => '請勿公開分享此客戶專屬安裝連結。App 名稱與可用性可能會變動；如果頁面看起來不同，請先聯絡我們，不要隨便下載網路上的 APK。',
                'public_guide' => '一般公開 App 安裝指南',
                'support' => '需要協助？請直接回覆此 Email，附上清楚的電視畫面照片、訂單編號，以及卡在哪一個步驟。',
            ];
        }

        return [
            'title' => 'Customer setup: installing your apps',
            'intro' => 'Save these customer-only steps for first setup or reinstalling the TV app later.',
            'steps' => [
                'Connect your SVICLOUD box to Wi-Fi or Ethernet first.',
                'From the home screen, open Orz Browser.',
                'Enter 8989c.cc in the address bar and open the page.',
                'Find Yogurt TV on the app page, then download and install it.',
                'If Android asks for permission, allow installation from this trusted source, then continue.',
                'Open Yogurt TV once installation finishes. If anything fails, reply to this email with a photo of the TV screen and your order number.',
            ],
            'note' => 'Please keep this setup link private for customers. App availability and names can change over time; if the page looks different, contact us before trying random APK files from the web.',
            'public_guide' => 'General public app guide',
            'support' => 'Need help? Reply to this email with a clear photo of the screen, your order number, and which step you are stuck on.',
        ];
    }
}

if (!function_exists('svic_render_customer_setup_email_block')) {
    /**
     * Add customer-only setup steps to post-purchase WooCommerce emails.
     * Kept out of public pages so ad/compliance surfaces stay clean while
     * customers still receive the exact app install path they need.
     */
    function svic_render_customer_setup_email_block($order, $sent_to_admin, $plain_text, $email): void
    {
        if ($sent_to_admin || !is_a($order, 'WC_Order') || !is_object($email) || empty($email->id)) {
            return;
        }

        $allowed_email_ids = [
            'customer_processing_order',
            'customer_on_hold_order',
        ];

        if (!in_array((string) $email->id, $allowed_email_ids, true)) {
            return;
        }

        $locale = function_exists('get_locale') ? get_locale() : 'en_US';
        $copy = svic_customer_setup_email_copy($locale);
        $steps = $copy['steps'];
        $guide_url = function_exists('svic_url_with_lang')
            ? svic_url_with_lang(home_url('/guides-apps/'))
            : home_url('/guides-apps/');

        if ($plain_text) {
            echo "\n" . $copy['title'] . "\n";
            echo $copy['intro'] . "\n\n";
            foreach ($steps as $index => $step) {
                echo ((int) $index + 1) . '. ' . wp_strip_all_tags((string) $step) . "\n";
            }
            echo "\n" . $copy['note'] . "\n";
            echo $copy['public_guide'] . ': ' . esc_url_raw($guide_url) . "\n";
            echo $copy['support'] . "\n";
            return;
        }
        ?>
        <div style="margin:24px 0;padding:20px;border:1px solid #dbe7f3;border-radius:12px;background:#f8fbff;">
            <h2 style="margin:0 0 8px;color:#111827;font-size:20px;line-height:1.3;"><?php echo esc_html($copy['title']); ?></h2>
            <p style="margin:0 0 14px;color:#374151;"><?php echo esc_html($copy['intro']); ?></p>
            <ol style="margin:0 0 14px 20px;padding:0;color:#111827;">
                <?php foreach ($steps as $step) : ?>
                    <li style="margin:0 0 8px;"><?php echo esc_html((string) $step); ?></li>
                <?php endforeach; ?>
            </ol>
            <p style="margin:0 0 12px;color:#6b7280;font-size:13px;line-height:1.5;"><?php echo esc_html($copy['note']); ?></p>
            <p style="margin:0 0 8px;"><a href="<?php echo esc_url($guide_url); ?>" style="color:#0f766e;font-weight:700;"><?php echo esc_html($copy['public_guide']); ?></a></p>
            <p style="margin:0;color:#374151;"><?php echo esc_html($copy['support']); ?></p>
        </div>
        <?php
    }
}

add_action('woocommerce_email_after_order_table', 'svic_render_customer_setup_email_block', 20, 4);

// =============================================================================
// Epic D — WooCommerce: cleanup / reduce bloat
// =============================================================================

// Remove the WooCommerce HTML generator meta tag (<meta name="generator" ...>).
remove_action('wp_head', array( $GLOBALS['woocommerce'] ?? null, 'generator' ));
add_filter('woocommerce_show_generator_tag', '__return_false');

// Remove the RSS feed link WooCommerce adds to shop pages.
add_action('wp', function () {
    if (function_exists('is_woocommerce') && !is_woocommerce() && !is_cart() && !is_checkout()) {
        // Dequeue WooCommerce block styles on non-shop pages.
        add_action('wp_enqueue_scripts', function () {
            if (!is_woocommerce() && !is_cart() && !is_checkout() && !is_account_page()) {
                wp_dequeue_style('wc-blocks-style');
                wp_dequeue_style('wc-blocks-vendors-style');
            }
        }, 99);
    }
});

// =============================================================================
// Google Merchant Center: ensure AdTribes product feed carries rich offer data
// =============================================================================

if (!function_exists('svic_ensure_google_feed_shipping')) {
    /**
     * AdTribes can generate a valid Google feed without item-level shipping,
     * handling time, or offer images. Merchant Center may approve products while
     * still lowering the shopping experience score for those missing attributes.
     * Keep the feed self-healing by adding the account-critical offer data.
     */
    function svic_ensure_google_feed_shipping(): void
    {
        if (!function_exists('wp_upload_dir')) {
            return;
        }

        $uploads = wp_upload_dir(null, false);
        if (empty($uploads['basedir'])) {
            return;
        }

        $feed_path = trailingslashit($uploads['basedir']) . 'woo-product-feed-pro/xml/i34nvs0glefyr7skadxvsz8ip8xur3bo.xml';
        if (!is_readable($feed_path) || !is_writable($feed_path)) {
            return;
        }

        $last_checked = (int) get_transient('svic_google_feed_rich_offer_checked');
        $mtime        = (int) filemtime($feed_path);
        if ($last_checked >= $mtime) {
            return;
        }

        $xml = file_get_contents($feed_path);
        if (!is_string($xml) || $xml === '' || strpos($xml, '<item>') === false) {
            return;
        }

        $shipping_block = "      <g:shipping>\n"
            . "        <g:country>US</g:country>\n"
            . "        <g:service>Free Shipping</g:service>\n"
            . "        <g:price>USD 0.00</g:price>\n"
            . "        <g:min_transit_time>2</g:min_transit_time>\n"
            . "        <g:max_transit_time>5</g:max_transit_time>\n"
            . "      </g:shipping>\n"
            . "      <g:shipping>\n"
            . "        <g:country>CA</g:country>\n"
            . "        <g:service>Free Shipping</g:service>\n"
            . "        <g:price>USD 0.00</g:price>\n"
            . "        <g:min_transit_time>5</g:min_transit_time>\n"
            . "        <g:max_transit_time>10</g:max_transit_time>\n"
            . "      </g:shipping>\n";

        $offer_images = [
            '12' => [
                'https://svicloudtvbox.us/wp-content/uploads/2026/04/svicloud-10p-plus-lifestyle-1.png',
                'https://svicloudtvbox.us/wp-content/uploads/2026/04/svicloud-10p-plus-lifestyle-2.png',
                'https://svicloudtvbox.us/wp-content/uploads/2026/04/svicloud-10p-plus-lifestyle-3.png',
            ],
            '14' => [
                'https://svicloudtvbox.us/wp-content/uploads/2026/04/svicloud-10s-lifestyle-1.jpg',
                'https://svicloudtvbox.us/wp-content/uploads/2026/04/svicloud-10s-lifestyle-2.jpg',
                'https://svicloudtvbox.us/wp-content/uploads/2026/04/svicloud-10s-lifestyle-3.jpg',
            ],
            '840' => [
                'https://svicloudtvbox.us/wp-content/uploads/2026/04/remote-control-white-1536x1536.png',
                'https://svicloudtvbox.us/wp-content/uploads/2026/04/remote-control-white-1024x1024.png',
                'https://svicloudtvbox.us/wp-content/uploads/2026/04/remote-control-white-600x600.png',
            ],
        ];

        $image_link_overrides = [
            // The original 10S packshot is 750x470; Merchant Center warns when a
            // dimension is under 500px. Use the high-resolution lifestyle image.
            '14' => 'https://svicloudtvbox.us/wp-content/uploads/2026/04/svicloud-10s-lifestyle-1.jpg',
        ];

        $patched = preg_replace_callback('/<item>(.*?)<\/item>/s', function ($matches) use ($shipping_block, $offer_images, $image_link_overrides) {
            $item     = $matches[0];
            $offer_id = '';
            if (preg_match('/<g:id>(.*?)<\/g:id>/', $item, $id_matches)) {
                $offer_id = trim(html_entity_decode($id_matches[1], ENT_QUOTES | ENT_XML1, 'UTF-8'));
            }

            if (strpos($item, '<g:shipping>') !== false) {
                $patched_item = $item;
            } elseif (strpos($item, '<g:condition>') !== false) {
                $patched_item = str_replace('      <g:condition>', $shipping_block . '      <g:condition>', $item);
            } else {
                $patched_item = str_replace('    </item>', $shipping_block . '    </item>', $item);
            }

            if (isset($image_link_overrides[$offer_id]) && strpos($patched_item, '<g:image_link>') !== false) {
                $replacement = '      <g:image_link>' . esc_url($image_link_overrides[$offer_id]) . '</g:image_link>';
                $patched_item = preg_replace('/\s*<g:image_link>.*?<\/g:image_link>\s*/s', "\n" . $replacement . "\n", $patched_item, 1) ?: $patched_item;
            }

            $patched_item = preg_replace_callback('/<g:shipping>(.*?)<\/g:shipping>/s', function ($shipping_matches) {
                $shipping = $shipping_matches[0];
                if (strpos($shipping, '<g:min_transit_time>') !== false) {
                    return $shipping;
                }

                $country = '';
                if (preg_match('/<g:country>(.*?)<\/g:country>/', $shipping, $country_matches)) {
                    $country = strtoupper(trim($country_matches[1]));
                }

                $transit_block = $country === 'CA'
                    ? "        <g:min_transit_time>5</g:min_transit_time>\n        <g:max_transit_time>10</g:max_transit_time>\n"
                    : "        <g:min_transit_time>2</g:min_transit_time>\n        <g:max_transit_time>5</g:max_transit_time>\n";

                if (strpos($shipping, '<g:price>') !== false) {
                    return preg_replace('/(\s*<g:price>.*?<\/g:price>\s*)/s', '$1' . $transit_block, $shipping, 1) ?: $shipping;
                }

                return str_replace('</g:shipping>', $transit_block . '      </g:shipping>', $shipping);
            }, $patched_item) ?: $patched_item;

            if (strpos($patched_item, '<g:min_handling_time>') === false) {
                $handling_block = "      <g:min_handling_time>0</g:min_handling_time>\n"
                    . "      <g:max_handling_time>2</g:max_handling_time>\n";
                if (strpos($patched_item, '<g:condition>') !== false) {
                    $patched_item = str_replace('      <g:condition>', $handling_block . '      <g:condition>', $patched_item);
                } else {
                    $patched_item = str_replace('    </item>', $handling_block . '    </item>', $patched_item);
                }
            }

            if (strpos($patched_item, '<g:return_policy_label>') === false) {
                $return_policy_block = "      <g:return_policy_label>Standard for United States</g:return_policy_label>\n";
                if (strpos($patched_item, '<g:condition>') !== false) {
                    $patched_item = str_replace('      <g:condition>', $return_policy_block . '      <g:condition>', $patched_item);
                } else {
                    $patched_item = str_replace('    </item>', $return_policy_block . '    </item>', $patched_item);
                }
            }

            if ($offer_id !== '' && isset($offer_images[$offer_id]) && strpos($patched_item, '<g:additional_image_link>') === false) {
                $additional_images = '';
                foreach ($offer_images[$offer_id] as $image_url) {
                    $additional_images .= '      <g:additional_image_link>' . esc_url($image_url) . "</g:additional_image_link>\n";
                }

                if (strpos($patched_item, '<g:checkout_link_template>') !== false) {
                    $patched_item = str_replace('      <g:checkout_link_template>', $additional_images . '      <g:checkout_link_template>', $patched_item);
                } elseif (strpos($patched_item, '<g:availability>') !== false) {
                    $patched_item = str_replace('      <g:availability>', $additional_images . '      <g:availability>', $patched_item);
                } else {
                    $patched_item = str_replace('    </item>', $additional_images . '    </item>', $patched_item);
                }
            }

            return $patched_item;
        }, $xml);

        if (is_string($patched) && $patched !== $xml) {
            file_put_contents($feed_path, $patched, LOCK_EX);
        }

        set_transient('svic_google_feed_rich_offer_checked', max($mtime, time()), HOUR_IN_SECONDS);
    }
}

add_action('init', 'svic_ensure_google_feed_shipping', 30);

// Cache bust: 1769987003
