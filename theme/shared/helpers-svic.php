<?php
/**
 * Shared SVICLOUD helper functions.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('svic_text_domain')) {
    function svic_text_domain(): string {
        if (defined('SVIC_THEME_TEXT_DOMAIN')) {
            return constant('SVIC_THEME_TEXT_DOMAIN');
        }

        return 'svicloudtvbox';
    }
}

if (!function_exists('svic_get_product_by_slug')) {
    function svic_get_product_by_slug(string $slug) {
        if (!class_exists('WooCommerce')) {
            return null;
        }

        $post = get_page_by_path($slug, OBJECT, 'product');
        if (!$post) {
            return null;
        }

        $product = wc_get_product($post->ID);
        return $product ?: null;
    }
}

if (!function_exists('svic_price_html')) {
    function svic_price_html($product): string {
        if (!$product) {
            return '';
        }

        return wp_kses_post($product->get_price_html());
    }
}

if (!function_exists('svic_product_primary_image')) {
    function svic_product_primary_image($product, $size = 'medium'): string {
        if (!$product) {
            return '';
        }

        $image_id = $product->get_image_id();
        if ($image_id) {
            return wp_get_attachment_image($image_id, $size, false, [
                'alt' => esc_attr($product->get_name()),
            ]);
        }

        return '<img src="' . esc_url(get_template_directory_uri() . '/assets/images/svicloud-hero-product.png') . '" alt="' . esc_attr($product->get_name()) . '" />';
    }
}

if (!function_exists('svic_add_to_cart_url')) {
    function svic_add_to_cart_url($product): string {
        if (!$product) {
            return '#';
        }

        return esc_url(add_query_arg('add-to-cart', $product->get_id(), wc_get_cart_url()));
    }
}

if (!function_exists('svic_bilingual_span')) {
    function svic_bilingual_span(string $en, string $zh, string $extra_class = ''): string {
        $extra_class = trim($extra_class);
        $suffix = $extra_class !== '' ? ' ' . esc_attr($extra_class) : '';
        $domain = svic_text_domain();

        $en_span = sprintf('<span class="hide-zh%s">%s</span>', $suffix, esc_html__($en, $domain));
        $zh_span = sprintf('<span class="hide-en%s" lang="zh">%s</span>', $suffix, esc_html__($zh, $domain));

        return $en_span . $zh_span;
    }
}

if (!function_exists('svic_render_product_card')) {
    function svic_render_product_card($product) {
        if (!$product) {
            return;
        }

        $permalink   = get_permalink($product->get_id());
        $gallery_ids = method_exists($product, 'get_gallery_image_ids') ? (array) $product->get_gallery_image_ids() : [];
        $slides      = [];
        $primary_id  = $product->get_image_id();

        if ($primary_id) {
            $slides[] = wp_get_attachment_image_url($primary_id, 'product-tile');
        }

        foreach ($gallery_ids as $gid) {
            $url = wp_get_attachment_image_url($gid, 'product-tile');
            if ($url) {
                $slides[] = $url;
            }
        }

        if (!$slides) {
            $slides[] = get_template_directory_uri() . '/assets/images/svicloud-hero-product.png';
        }

        $feature_tags = [];
        if (method_exists($product, 'get_slug')) {
            $slug = $product->get_slug();
            if ($slug === 'svicloud-10p-plus') {
                $feature_tags = ['4K HDR', 'Wi-Fi 6', 'Kids & Karaoke'];
            } elseif ($slug === 'svicloud-10s') {
                $feature_tags = ['4K HDR', 'Compact Footprint', 'Dual-Band Wi-Fi'];
            }
        }

        if (!$feature_tags) {
            $term_names = wp_get_post_terms($product->get_id(), 'product_tag', ['fields' => 'names']);
            if (empty($term_names)) {
                $term_names = wp_get_post_terms($product->get_id(), 'product_cat', ['fields' => 'names']);
            }

            if ($term_names && !is_wp_error($term_names)) {
                $feature_tags = array_slice($term_names, 0, 3);
            }
        }
        ?>
        <article class="product-card">
          <div class="pcard-carousel">
            <?php foreach ($slides as $i => $src) : ?>
              <div class="pcard-slide<?php echo $i === 0 ? ' active' : ''; ?>">
                <a href="<?php echo esc_url($permalink); ?>" aria-label="<?php echo esc_attr($product->get_name()); ?>">
                  <img src="<?php echo esc_url($src); ?>" alt="<?php echo esc_attr($product->get_name()); ?> image <?php echo (int) ($i + 1); ?>" loading="lazy" />
                </a>
              </div>
            <?php endforeach; ?>
            <div class="pcard-nav">
              <button class="pcard-btn pcard-prev" aria-label="<?php esc_attr_e('Previous image', svic_text_domain()); ?>">‹</button>
              <button class="pcard-btn pcard-next" aria-label="<?php esc_attr_e('Next image', svic_text_domain()); ?>">›</button>
            </div>
            <div class="pcard-dots">
              <?php foreach ($slides as $i => $src) : ?>
                <button class="pcard-dot<?php echo $i === 0 ? ' active' : ''; ?>" data-i="<?php echo (int) $i; ?>" aria-label="<?php esc_attr_e('Go to image', svic_text_domain()); ?> <?php echo (int) ($i + 1); ?>"></button>
              <?php endforeach; ?>
            </div>
          </div>
          <h3 class="pcard-title"><a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($product->get_name()); ?></a></h3>
          <div class="pcard-meta">
            <span class="pcard-price"><?php echo svic_price_html($product); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
          </div>
          <?php
            $summary = wp_strip_all_tags($product->get_short_description() ?: $product->get_description());
            if ($summary) {
                echo '<p class="product-blurb">' . esc_html(wp_trim_words($summary, 18)) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }

            $render_tags = array_filter(array_map('trim', (array) $feature_tags));
            if ($render_tags) {
                echo '<ul class="pcard-tags" role="list">';
                foreach ($render_tags as $tag) {
                    echo '<li>' . esc_html($tag) . '</li>';
                }
                echo '</ul>';
            }
          ?>
          <div class="pcard-actions">
            <a class="btn btn-primary btn-cta" href="<?php echo svic_add_to_cart_url($product); ?>"><?php esc_html_e('Add to Cart', svic_text_domain()); ?></a>
          </div>
        </article>
        <?php
    }
}
