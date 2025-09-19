<?php
/**
 * WooCommerce Single Product (Classic)
 */

defined('ABSPATH') || exit;

get_header('shop');

while (have_posts()) :
    the_post();

    global $product;
    if (!$product) {
        $product = function_exists('wc_get_product') ? wc_get_product(get_the_ID()) : null;
    }

    do_action('woocommerce_before_single_product');

    if (post_password_required()) {
        echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        do_action('woocommerce_after_single_product');
        continue;
    }

    if (!$product) {
        echo '<main class="page-shell lumen-product"><p class="woocommerce-info">' . esc_html__('Product unavailable.', 'svicloudtvbox-lumen') . '</p></main>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        do_action('woocommerce_after_single_product');
        continue;
    }

    // Images
    $image_id = $product->get_image_id();
    $gallery = method_exists($product, 'get_gallery_image_ids') ? (array) $product->get_gallery_image_ids() : [];
    $slug = method_exists($product, 'get_slug') ? $product->get_slug() : '';

    $fallback_gallery_files = [];
    if ($slug === 'svicloud-10p-plus') {
        $fallback_gallery_files = [
            'svicloud-10p-plus-lifestyle-1.png',
            'svicloud-10p-plus-lifestyle-2.png',
            'svicloud-10p-plus-lifestyle-3.png',
        ];
    } elseif ($slug === 'svicloud-10s') {
        $fallback_gallery_files = [
            'svicloud-10s-lifestyle-1.png',
            'svicloud-10s-lifestyle-2.png',
            'svicloud-10s-lifestyle-3.png',
        ];
    }

    $gallery_entries = [];
    if ($gallery) {
        foreach ($gallery as $gid) {
            $full = wp_get_attachment_image_url($gid, 'large');
            if (!$full) {
                continue;
            }
            $thumb_html = wp_get_attachment_image($gid, 'thumbnail', false, ['class' => 'product-thumb-img', 'loading' => 'lazy']);
            $srcset = wp_get_attachment_image_srcset($gid, 'large') ?: '';
            $gallery_entries[] = [
                'full'   => $full,
                'thumb'  => $thumb_html,
                'srcset' => $srcset,
            ];
        }
    }

    if (empty($gallery_entries) && $fallback_gallery_files) {
        foreach ($fallback_gallery_files as $file) {
            $full = get_template_directory_uri() . '/assets/images/' . $file;
            $thumb_html = '<img src="' . esc_url($full) . '" alt="" class="product-thumb-img" loading="lazy" />';
            $gallery_entries[] = [
                'full'   => $full,
                'thumb'  => $thumb_html,
                'srcset' => '',
            ];
        }
    }

    $primary_image_html = '';
    if ($image_id) {
        $primary_image_html = wp_get_attachment_image($image_id, 'large', false, [
            'class' => 'product-hero-image',
            'alt'   => esc_attr(get_the_title()),
        ]);
    } elseif (!empty($gallery_entries)) {
        $primary_image_html = '<img class="product-hero-image" src="' . esc_url($gallery_entries[0]['full']) . '" alt="' . esc_attr(get_the_title()) . '" loading="lazy" />';
    } else {
        $primary_image_html = '<img class="product-hero-image" src="' . esc_url(get_template_directory_uri() . '/assets/images/svicloud-hero-product.png') . '" alt="' . esc_attr(get_the_title()) . '" />';
    }

    $product_highlights = [
        ['en' => 'Certified U.S. inventory & warranty', 'zh' => '美國現貨與一年保固'],
        ['en' => 'Bilingual concierge setup support', 'zh' => '英/中文專屬開箱服務'],
        ['en' => 'No monthly fees or hidden renewals', 'zh' => '無月費與隱藏續費'],
    ];
    ?>

    <main class="page-shell lumen-product">
      <section class="product-hero">
        <div class="product-hero-inner">
          <div class="product-hero-media">
            <div class="product-hero-stage">
              <?php echo $primary_image_html; ?>
              <div class="product-hero-glow" aria-hidden="true"></div>
            </div>
            <?php if (!empty($gallery_entries)) : ?>
              <div class="product-hero-thumbs" role="list">
                <?php foreach ($gallery_entries as $index => $entry) :
                    $is_active = ($index === 0);
                    $button_classes = $is_active ? 'product-thumb active' : 'product-thumb';
                ?>
                  <button type="button" class="<?php echo esc_attr($button_classes); ?>" data-image="<?php echo esc_url($entry['full']); ?>" data-srcset="<?php echo esc_attr($entry['srcset']); ?>" aria-pressed="<?php echo $is_active ? 'true' : 'false'; ?>">
                    <?php echo wp_kses_post($entry['thumb']); ?>
                  </button>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="product-hero-content">
            <div class="badge-row">
              <span class="badge">Authorized Dealer</span>
              <span class="badge">Ships from USA</span>
              <span class="badge">1-Year US Warranty</span>
            </div>
            <h1 class="product-hero-title"><?php the_title(); ?></h1>
            <p class="product-hero-subtitle">
              <?php echo wp_kses_post(svic_bilingual_span('Shipped from the U.S. with concierge onboarding in English & Chinese.', '美國倉庫出貨，提供中英文專人安裝服務。', 'product-hero-subtitle-text')); ?>
            </p>
            <div class="product-hero-price"><?php echo $product->get_price_html(); ?></div>
            <div class="product-hero-cta">
              <?php woocommerce_template_single_add_to_cart(); ?>
            </div>
            <div class="product-hero-detail text-small">
              <?php echo wp_kses_post(svic_bilingual_span('Secure checkout • Free U.S. shipping • English/中文 support', '安全結帳 • 美國免運 • 英/中文客服')); ?>
            </div>
            <ul class="product-hero-points">
              <?php foreach ($product_highlights as $highlight) : ?>
                <li><?php echo wp_kses_post(svic_bilingual_span($highlight['en'], $highlight['zh'])); ?></li>
              <?php endforeach; ?>
            </ul>
            <div class="product-meta-block">
              <?php woocommerce_template_single_meta(); ?>
            </div>
          </div>
        </div>
      </section>

      <section class="product-description">
        <h2 class="h3 spacing-normal"><?php echo esc_html__('Description', 'svicloudtvbox-lumen'); ?></h2>
        <div class="entry-content">
          <?php the_content(); ?>
        </div>
      </section>
    </main>

    <?php
    do_action('woocommerce_after_single_product');
endwhile;

get_footer('shop');
