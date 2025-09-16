<?php
/**
 * SVICLOUD TV Box Classic Theme Functions
 *
 * @package SVICloudTVBoxClassic
 */

if (!defined('ABSPATH')) { exit; }

// Theme setup
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('woocommerce');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
    register_nav_menus([
        'primary' => __('Primary Menu', 'svicloudtvbox'),
        'footer'  => __('Footer Menu', 'svicloudtvbox'),
    ]);
});

// Enqueue assets
add_action('wp_enqueue_scripts', function () {
    $theme_version = wp_get_theme()->get('Version');

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
        $theme_version
    );

    // Theme script
    wp_enqueue_script(
        'svicloudtvbox-script',
        get_template_directory_uri() . '/assets/js/theme.js',
        ['jquery'],
        $theme_version,
        true
    );

    wp_localize_script('svicloudtvbox-script', 'svicTheme', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'homeUrl' => home_url('/'),
        'isWoo'   => class_exists('WooCommerce'),
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

// -------------------------------------------
// Helpers: WooCommerce product access/rendering
// -------------------------------------------

if (!function_exists('svic_get_product_by_slug')) {
    function svic_get_product_by_slug(string $slug) {
        if (!class_exists('WooCommerce')) return null;
        $post = get_page_by_path($slug, OBJECT, 'product');
        if (!$post) return null;
        $product = wc_get_product($post->ID);
        return $product ?: null;
    }
}

if (!function_exists('svic_price_html')) {
    function svic_price_html($product): string {
        if (!$product) return '';
        return wp_kses_post($product->get_price_html());
    }
}

if (!function_exists('svic_product_primary_image')) {
    function svic_product_primary_image($product, $size = 'medium'): string {
        if (!$product) return '';
        $image_id = $product->get_image_id();
        if ($image_id) {
            return wp_get_attachment_image($image_id, $size, false, ['alt' => esc_attr($product->get_name())]);
        }
        return '<img src="' . esc_url(get_template_directory_uri() . '/assets/images/svicloud-hero-product.png') . '" alt="' . esc_attr($product->get_name()) . '" />';
    }
}

if (!function_exists('svic_add_to_cart_url')) {
    function svic_add_to_cart_url($product): string {
        if (!$product) return '#';
        return esc_url(add_query_arg('add-to-cart', $product->get_id(), wc_get_cart_url()));
    }
}

if (!function_exists('svic_render_product_card')) {
    function svic_render_product_card($product) {
        if (!$product) return;
        $permalink = get_permalink($product->get_id());
        $gallery_ids = method_exists($product, 'get_gallery_image_ids') ? (array) $product->get_gallery_image_ids() : [];
        $slides = [];
        $primary_id = $product->get_image_id();
        if ($primary_id) { $slides[] = wp_get_attachment_image_url($primary_id, 'product-tile'); }
        foreach ($gallery_ids as $gid) {
            $url = wp_get_attachment_image_url($gid, 'product-tile');
            if ($url) $slides[] = $url;
        }
        if (!$slides) {
            $slides[] = get_template_directory_uri() . '/assets/images/svicloud-hero-product.png';
        }
        ?>
        <article class="product-card">
          <div class="pcard-carousel">
            <?php foreach ($slides as $i => $src): ?>
              <div class="pcard-slide<?php echo $i===0 ? ' active' : ''; ?>">
                <a href="<?php echo esc_url($permalink); ?>" aria-label="<?php echo esc_attr($product->get_name()); ?>">
                  <img src="<?php echo esc_url($src); ?>" alt="<?php echo esc_attr($product->get_name()); ?> image <?php echo (int) ($i+1); ?>" />
                </a>
              </div>
            <?php endforeach; ?>
            <div class="pcard-nav">
              <button class="pcard-btn pcard-prev" aria-label="Previous image">‹</button>
              <button class="pcard-btn pcard-next" aria-label="Next image">›</button>
            </div>
            <div class="pcard-dots">
              <?php foreach ($slides as $i => $src): ?>
                <button class="pcard-dot<?php echo $i===0 ? ' active' : ''; ?>" data-i="<?php echo (int)$i; ?>" aria-label="Go to image <?php echo (int) ($i+1); ?>"></button>
              <?php endforeach; ?>
            </div>
          </div>
          <h3 class="pcard-title"><a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($product->get_name()); ?></a></h3>
          <div class="pcard-meta">
            <span class="pcard-price"><?php echo svic_price_html($product); ?></span>
          </div>
          <?php
            $summary = wp_strip_all_tags($product->get_short_description() ?: $product->get_description());
            if ($summary) {
              echo '<p class="product-blurb">' . esc_html(wp_trim_words($summary, 22)) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }
          ?>
          <div class="pcard-actions">
            <a class="btn btn-primary w-full" href="<?php echo svic_add_to_cart_url($product); ?>"><?php esc_html_e('Add to Cart', 'svicloudtvbox'); ?></a>
          </div>
        </article>
        <?php
    }
}
