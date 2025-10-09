<?php
/**
 * Theme-scoped SVICLOUD helper functions.
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('svic_current_locale')) {
    function svic_current_locale(): string {
        $locale = apply_filters('svic_current_locale', get_locale());
        if (!is_string($locale) || $locale === '') {
            $locale = 'zh_TW';
        }

        return SVIC_Translator::normalizeLocaleCode($locale);
    }
}

if (!function_exists('svic_language_query_value')) {
    function svic_language_query_value(?string $locale = null): string {
        $normalized = $locale ? SVIC_Translator::normalizeLocaleCode($locale) : svic_current_locale();
        $normalized = strtolower($normalized);

        if ($normalized === '') {
            return 'zh';
        }

        $parts = preg_split('/[-_]/', $normalized);
        $language = is_array($parts) && isset($parts[0]) ? trim((string) $parts[0]) : $normalized;
        if ($language === '') {
            $language = $normalized;
        }

        $language = strtolower($language);

        return $language === '' ? 'zh' : $language;
    }
}

if (!function_exists('svic_url_with_lang')) {
    function svic_url_with_lang($url, ?string $lang = null): string {
        if (!is_string($url) || $url === '') {
            return '';
        }

        $trimmed = trim($url);
        if ($trimmed === '') {
            return '';
        }

        if ($trimmed[0] === '#') {
            return $url;
        }

        if (preg_match('/^(?:mailto|tel|javascript|data):/i', $trimmed)) {
            return $url;
        }

        $langValue = $lang !== null ? strtolower(trim($lang)) : svic_language_query_value();
        if ($langValue === '') {
            return $url;
        }

        $siteHost = wp_parse_url(home_url(), PHP_URL_HOST);
        $urlParts = wp_parse_url($url);

        if (is_array($urlParts)) {
            if (isset($urlParts['host']) && $urlParts['host'] !== '' && $siteHost && strcasecmp($urlParts['host'], $siteHost) !== 0) {
                return $url;
            }
        }

        $updated = remove_query_arg('lang', $url);

        return add_query_arg('lang', $langValue, $updated);
    }
}

if (!function_exists('svic_translate')) {
    function svic_translate(string $key, array $replacements = [], ?string $locale = null): string {
        return SVIC_Translator::instance()->translate($key, $replacements, $locale);
    }
}

if (!function_exists('svic_translate_html')) {
    function svic_translate_html(string $key, array $replacements = [], ?string $locale = null): string {
        return esc_html(svic_translate($key, $replacements, $locale));
    }
}

if (!function_exists('svic_translate_attr')) {
    function svic_translate_attr(string $key, array $replacements = [], ?string $locale = null): string {
        return esc_attr(svic_translate($key, $replacements, $locale));
    }
}

if (!function_exists('svic_translate_rich')) {
    function svic_translate_rich(string $key, array $replacements = [], ?string $locale = null): string {
        $text = svic_translate($key, $replacements, $locale);
        /**
         * Allow filters to adjust rich text output (e.g., additional sanitization).
         */
        return apply_filters('svic_translate_rich_text', $text, $key, $locale);
    }
}

if (!function_exists('svic_text_domain')) {
    function svic_text_domain(): string {
        if (defined('SVIC_THEME_TEXT_DOMAIN')) {
            return constant('SVIC_THEME_TEXT_DOMAIN');
        }

        return 'svicloudtvbox-lumen';
    }
}

if (!function_exists('svic_get_product_by_slug')) {
    function svic_get_product_by_slug(string $slug) {
        if (!class_exists('WooCommerce')) {
            return null;
        }

        $post = get_page_by_path($slug, OBJECT, 'product');
        if (!$post instanceof WP_Post) {
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
    function svic_product_primary_image($product, string $size = 'medium'): string {
        if (!$product) {
            return '';
        }

        $image_id = $product->get_image_id();
        if ($image_id) {
            return wp_get_attachment_image(
                $image_id,
                $size,
                false,
                [
                    'alt' => esc_attr($product->get_name()),
                ]
            );
        }

        return '<img src="' . esc_url(get_template_directory_uri() . '/assets/images/svicloud-hero-product.png') . '" alt="' . esc_attr($product->get_name()) . '" />';
    }
}

if (!function_exists('svic_add_to_cart_url')) {
    function svic_add_to_cart_url($product): string {
        if (!$product) {
            return '#';
        }

        return esc_url(svic_url_with_lang(add_query_arg('add-to-cart', $product->get_id(), wc_get_cart_url())));
    }
}

if (!function_exists('svic_cart_contents_count')) {
    function svic_cart_contents_count(): int
    {
        if (!class_exists('WooCommerce') || !function_exists('WC')) {
            return 0;
        }

        $cart = WC()->cart;
        if (!is_object($cart) || !method_exists($cart, 'get_cart_contents_count')) {
            return 0;
        }

        $count = (int) $cart->get_cart_contents_count();

        return $count > 0 ? $count : 0;
    }
}

if (!function_exists('svic_header_cart_count_markup')) {
    function svic_header_cart_count_markup(): string
    {
        $count = svic_cart_contents_count();
        $classes = ['lumen-cart-count'];

        if ($count === 0) {
            $classes[] = 'is-empty';
        }

        return sprintf(
            '<span class="%1$s" data-cart-count data-count="%2$s">%3$s</span>',
            esc_attr(implode(' ', $classes)),
            esc_attr((string) $count),
            esc_html(number_format_i18n($count))
        );
    }
}

if (!function_exists('svic_header_cart_link')) {
    function svic_header_cart_link(array $args = []): string
    {
        $defaults = [
            'class' => '',
            /* translators: Header cart CTA label. Non-breaking space keeps text on one line. */
            'label' => wp_kses_post(__('View&nbsp;Cart', svic_text_domain())),
        ];
        $args = wp_parse_args($args, $defaults);

        $count   = svic_cart_contents_count();
        $base_classes = [
            'lumen-pill',
            'lumen-pill--primary',
            'lumen-cart-link',
        ];

        $extra_classes = trim((string) $args['class']);
        if ($extra_classes !== '') {
            $base_classes[] = $extra_classes;
        }

        $classes = trim(implode(' ', $base_classes));

        if ($count > 0) {
            $classes .= ' has-items';
        }

        $cart_url = svic_url_with_lang(function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart'));

        $count_display = number_format_i18n($count);
        if ($count === 1) {
            $sr_label = svic_translate('core.cart.count_label_single', ['count' => $count_display]);
        } elseif ($count > 1) {
            $sr_label = svic_translate('core.cart.count_label_plural', ['count' => $count_display]);
        } else {
            $sr_label = svic_translate('core.cart.count_label_empty');
        }

        $link_aria_label = wp_strip_all_tags($args['label']);
        $html  = '<a class="' . esc_attr($classes) . '" href="' . esc_url($cart_url) . '" data-cart-link aria-label="' . esc_attr($link_aria_label) . '">';
        $html .= '<span class="lumen-cart-link__icon" aria-hidden="true"></span>';
        $html .= '<span class="lumen-cart-link__label">' . $args['label'] . '</span>';
        $html .= svic_header_cart_count_markup();
        $html .= '<span class="screen-reader-text">' . esc_html($sr_label) . '</span>';
        $html .= '</a>';

        return $html;
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
    function svic_render_product_card($product): void {
        if (!$product) {
            return;
        }

        $permalink   = svic_url_with_lang(get_permalink($product->get_id()));
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
            if (!$term_names || is_wp_error($term_names)) {
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
