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
            return remove_query_arg('lang', $url);
        }

        $homeParts = wp_parse_url(home_url('/'));
        $homeHost  = isset($homeParts['host']) ? strtolower((string) $homeParts['host']) : '';
        $homePath  = isset($homeParts['path']) ? rtrim((string) $homeParts['path'], '/') : '';

        $urlParts = wp_parse_url($url);
        if ($urlParts === false) {
            return $url;
        }

        $isRelative = !isset($urlParts['host']);
        $isInternal = $isRelative;
        if (!$isInternal) {
            $targetHost = strtolower((string) ($urlParts['host'] ?? ''));
            $isInternal = ($homeHost !== '') && $targetHost === $homeHost;

            if (!$isInternal) {
                return remove_query_arg('lang', $url);
            }
        }

        $path = (string) ($urlParts['path'] ?? '');
        if ($path === '' && $isRelative) {
            $path = '/';
        }

        if ($path === '' || $path[0] !== '/') {
            $path = '/' . ltrim($path, '/');
        }

        if (!$isRelative && $homePath !== '' && $homePath !== '/') {
            if ($path === $homePath) {
                $path = '/';
            } elseif (strpos($path, $homePath . '/') === 0) {
                $path = substr($path, strlen($homePath));
                if ($path === false || $path === '') {
                    $path = '/';
                } elseif ($path[0] !== '/') {
                    $path = '/' . $path;
                }
            }
        }

        if ($path === '') {
            $path = '/';
        }

        $hadTrailingSlash = $path !== '/' && substr($path, -1) === '/';
        $trimmedPath = ltrim($path, '/');

        if ($langValue === 'zh') {
            if ($trimmedPath === '' || $trimmedPath === 'zh') {
                $trimmedPath = 'zh';
                $hadTrailingSlash = true;
            } elseif (strpos($trimmedPath, 'zh/') !== 0) {
                $trimmedPath = 'zh/' . $trimmedPath;
            }
        } else {
            if ($trimmedPath === 'zh') {
                $trimmedPath = '';
                $hadTrailingSlash = true;
            } elseif (strpos($trimmedPath, 'zh/') === 0) {
                $trimmedPath = substr($trimmedPath, 3);
            }
        }

        if ($trimmedPath === '') {
            $localizedPath = $langValue === 'zh' ? '/zh/' : '/';
        } else {
            $localizedPath = '/' . $trimmedPath;
            if ($hadTrailingSlash && substr($localizedPath, -1) !== '/') {
                $localizedPath .= '/';
            } elseif (!$hadTrailingSlash && substr($localizedPath, -1) === '/' && $localizedPath !== '/') {
                $localizedPath = rtrim($localizedPath, '/');
            }
        }

        $localizedPath = preg_replace('#/+#', '/', $localizedPath);
        $queryParams = [];
        if (isset($urlParts['query']) && $urlParts['query'] !== '') {
            parse_str($urlParts['query'], $queryParams);
            unset($queryParams['lang']);
        }

        $fragment = $urlParts['fragment'] ?? '';

        $localizedUrl = home_url($localizedPath);
        if (!empty($queryParams)) {
            $localizedUrl = add_query_arg($queryParams, $localizedUrl);
        }

        if ($fragment !== '') {
            $localizedUrl .= '#' . $fragment;
        }

        return $localizedUrl;
    }
}

if (!function_exists('svic_current_base_url')) {
    function svic_current_base_url(): string {
        $path = SVIC_Locale_Resolver::currentRequestPath();
        $url  = home_url($path);

        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        if (is_string($requestUri) && $requestUri !== '') {
            $parsed = wp_parse_url($requestUri);
            if (is_array($parsed)) {
                if (isset($parsed['query']) && $parsed['query'] !== '') {
                    $query = [];
                    parse_str($parsed['query'], $query);
                    if (!empty($query)) {
                        unset($query['lang']);
                        if (!empty($query)) {
                            $url = add_query_arg($query, $url);
                        }
                    }
                }

                if (isset($parsed['fragment']) && $parsed['fragment'] !== '') {
                    $url .= '#' . $parsed['fragment'];
                }
            }
        }

        return $url;
    }
}

if (!function_exists('svic_locale_to_hreflang')) {
    function svic_locale_to_hreflang(string $locale): string {
        $locale = trim($locale);
        if ($locale === '') {
            return '';
        }

        $segments = preg_split('/[_-]/', $locale);
        if (!is_array($segments) || !$segments) {
            return strtolower($locale);
        }

        $language = strtolower($segments[0]);
        $variants = array_slice($segments, 1);

        // Target Traditional Chinese content to US audiences
        // via hreflang zh-Hant-US regardless of internal zh_TW locale.
        if ($language === 'zh') {
            return 'zh-Hant-US';
        }

        if (!$variants) {
            return $language;
        }

        $variants = array_map(static function ($value) {
            return strtoupper((string) $value);
        }, $variants);

        return $language . '-' . implode('-', $variants);
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

if (!function_exists('svic_estimated_read_time')) {
    function svic_estimated_read_time(int $post_id, int $words_per_minute = 220): int {
        $content = get_post_field('post_content', $post_id);
        if (!is_string($content) || $content === '') {
            return 1;
        }

        $sanitized   = wp_strip_all_tags($content, true);
        $word_count  = str_word_count($sanitized);
        $wpm_sanitized = max(1, $words_per_minute);

        $minutes = (int) ceil($word_count / $wpm_sanitized);

        return $minutes > 0 ? $minutes : 1;
    }
}

if (!function_exists('svic_post_card_image')) {
    function svic_post_card_image(int $post_id, string $size = 'large', array $attr = []): string {
        if ($post_id <= 0) {
            return '';
        }

        $attr = is_array($attr) ? $attr : [];

        $html = get_the_post_thumbnail($post_id, $size, $attr);
        if (is_string($html) && $html !== '') {
            return $html;
        }

        $content = get_post_field('post_content', $post_id);
        if (is_string($content) && $content !== '') {
            if (preg_match('/<img[^>]+src=["\"]([^"\"]+)["\"][^>]*>/i', $content, $image_match)) {
                $img_html = $image_match[0];
                $src      = $image_match[1] ?? '';
                if ($src !== '') {
                    $alt = '';
                    if (preg_match('/alt=["\"](.*?)["\"]/i', $img_html, $alt_match)) {
                        $alt = $alt_match[1] ?? '';
                    }

                    $attributes = svic_prepare_image_attributes($attr, [
                        'src'     => esc_url($src),
                        'alt'     => $alt !== '' ? $alt : get_the_title($post_id),
                    ]);

                    return '<img ' . $attributes . ' />';
                }
            }
        }

        $fallback_relative = apply_filters('svic_blog_fallback_image', '/assets/images/svicloud-10p-plus-lifestyle-1.png', $post_id, $size, $attr);
        $meta = function_exists('svic_get_theme_image_meta') ? svic_get_theme_image_meta($fallback_relative) : [
            'url' => get_template_directory_uri() . '/' . ltrim((string) $fallback_relative, '/'),
            'width' => null,
            'height' => null,
        ];

        if (empty($meta['url'])) {
            return '';
        }

        $fallback_alt = svic_translate('blog.card.fallback_alt', ['title' => get_the_title($post_id)]);
        if (!is_string($fallback_alt) || trim($fallback_alt) === '' || trim($fallback_alt) === 'fallback_alt') {
            $fallback_alt = get_the_title($post_id);
        }

        $attr_defaults = [
            'src'     => esc_url($meta['url']),
            'alt'     => $fallback_alt,
        ];

        if (!empty($meta['width'])) {
            $attr_defaults['width'] = (string) (int) $meta['width'];
        }
        if (!empty($meta['height'])) {
            $attr_defaults['height'] = (string) (int) $meta['height'];
        }

        $attributes = svic_prepare_image_attributes($attr, $attr_defaults);

        return '<img ' . $attributes . ' />';
    }
}

if (!function_exists('svic_prepare_image_attributes')) {
    function svic_prepare_image_attributes(array $attr, array $overrides = []): string {
        $final = array_merge(
            [
                'loading'  => 'lazy',
                'decoding' => 'async',
            ],
            $attr,
            $overrides
        );

        if (isset($attr['class']) && isset($overrides['class'])) {
            $final['class'] = trim($attr['class'] . ' ' . $overrides['class']);
        }

        $compiled = [];
        foreach ($final as $key => $value) {
            if ($value === null) {
                continue;
            }
            if ($key === 'class') {
                $value = trim((string) $value);
                if ($value === '') {
                    continue;
                }
            }
            $compiled[] = sprintf('%s="%s"', esc_attr($key), esc_attr((string) $value));
        }

        return implode(' ', $compiled);
    }
}

if (!function_exists('svic_category_label')) {
    function svic_category_label($term): string {
        if (!($term instanceof WP_Term)) {
            return '';
        }

        $label = trim((string) $term->name);
        $slug  = sanitize_title($term->slug ?: $label);

        if ($slug !== '') {
            $translation = trim(svic_translate('blog.categories.' . $slug));

            if ($translation !== '' && strcasecmp($translation, $slug) !== 0) {
                $label = $translation;
            } elseif ($label === '' || strcasecmp($label, $slug) === 0) {
                $label = ucwords(str_replace(['-', '_'], ' ', $slug));
            }
        }

        return $label !== '' ? $label : $term->name;
    }
}

if (!function_exists('svic_post_title')) {
    function svic_post_title(int $post_id): string {
        $post = get_post($post_id);
        if (!$post instanceof WP_Post) {
            return '';
        }

        $title = get_the_title($post_id);

        $slug_candidates = [];
        $current_slug = sanitize_title($post->post_name ?: $post->post_title ?: '');
        if ($current_slug !== '') {
            $slug_candidates[] = $current_slug;
        }

        if (function_exists('pll_default_language') && function_exists('pll_get_post')) {
            $default_lang = pll_default_language('slug');
            if (is_string($default_lang) && $default_lang !== '') {
                $default_post_id = pll_get_post($post_id, $default_lang);
                if ($default_post_id && (int) $default_post_id !== $post_id) {
                    $default_post = get_post($default_post_id);
                    if ($default_post instanceof WP_Post) {
                        $default_slug = sanitize_title($default_post->post_name ?: $default_post->post_title ?: '');
                        if ($default_slug !== '') {
                            $slug_candidates[] = $default_slug;
                        }
                    }
                }
            }
        }

        $slug_candidates = array_values(array_unique(array_filter($slug_candidates)));

        foreach ($slug_candidates as $slug) {
            $translation_key = 'blog.posts.' . $slug . '.title';
            $translation = svic_translate($translation_key);
            if (!is_string($translation)) {
                continue;
            }
            $translation = trim($translation);
            if ($translation === '' || strcasecmp($translation, 'title') === 0 || $translation === $translation_key) {
                continue;
            }

            $title = $translation;
            break;
        }

        $locale = function_exists('svic_current_locale') ? svic_current_locale() : get_locale();
        if (is_string($locale) && stripos($locale, 'zh') !== false) {
            if (strpos($title, '—') !== false) {
                $parts = preg_split('/\s*—\s*/u', $title);
                if (is_array($parts) && $parts) {
                    $candidate = trim((string) end($parts));
                    if ($candidate !== '') {
                        $title = $candidate;
                    }
                }
            }
        }

        return $title;
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
        $regular_price = $product->get_regular_price();
        $current_price = $product->get_price();
        $sale_price    = $product->get_sale_price();

        if ($current_price === '' && $regular_price !== '') {
            $current_price = $regular_price;
        }

        $regular_html = $regular_price !== '' ? wc_price($regular_price) : '';
        $current_html = $current_price !== '' ? wc_price($current_price) : '';

        if ($product->is_on_sale() && $sale_price !== '' && $regular_html && $current_html && $current_html !== $regular_html) {
            $sr_template = svic_translate('pricing.sr_sale_announcement');
            if (!is_string($sr_template) || $sr_template === '') {
                $sr_template = __('Sale price %2$s, original price %1$s', svic_text_domain());
            }

            $sr_text = sprintf(
                $sr_template,
                wp_strip_all_tags($regular_html),
                wp_strip_all_tags($current_html)
            );

            return sprintf(
                '<span class="lumen-price lumen-price--sale"><span class="lumen-price__current">%1$s</span><span class="lumen-price__original">%2$s</span><span class="screen-reader-text">%3$s</span></span>',
                $current_html,
                $regular_html,
                esc_html($sr_text)
            );
        }

        if ($current_html) {
            return sprintf('<span class="lumen-price"><span class="lumen-price__current">%s</span></span>', $current_html);
        }

        return '';
    }
}

if (!function_exists('svic_product_primary_image')) {
    function svic_product_primary_image($product, string $size = 'medium'): string {
        if (!$product) {
            return '';
        }

        $image_id = $product->get_image_id();
        if (!$image_id && method_exists($product, 'get_gallery_image_ids')) {
            $gallery_ids = $product->get_gallery_image_ids();
            if (is_array($gallery_ids) && !empty($gallery_ids)) {
                $image_id = (int) reset($gallery_ids);
            }
        }

        if ($image_id) {
            return wp_get_attachment_image(
                $image_id,
                $size,
                false,
                [
                    'alt' => esc_attr($product->get_name()),
                    'loading' => 'lazy',
                    'decoding' => 'async',
                ]
            );
        }

        return '<img src="' . esc_url(get_template_directory_uri() . '/assets/images/svicloud-hero-product.png') . '" alt="' . esc_attr($product->get_name()) . '" loading="lazy" decoding="async" />';
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

if (!function_exists('svic_get_google_customer_reviews_optin_payload')) {
    /**
     * Prepare the payload for Google Customer Reviews opt-in.
     *
     * @param \WC_Order|null $order
     *
     * @return array|null
     */
    function svic_get_google_customer_reviews_optin_payload($order): ?array
    {
        if (!($order instanceof \WC_Order)) {
            return null;
        }

        $enabled = apply_filters('svic_google_customer_reviews_enabled', true, $order);
        if (!$enabled) {
            return null;
        }

        $merchant_id = (int) apply_filters(
            'svic_google_customer_reviews_merchant_id',
            defined('SVIC_GOOGLE_CUSTOMER_REVIEWS_MERCHANT_ID') ? SVIC_GOOGLE_CUSTOMER_REVIEWS_MERCHANT_ID : 0,
            $order
        );

        if ($merchant_id <= 0) {
            return null;
        }

        $order_number = $order->get_order_number();
        $order_id = preg_replace('/[^A-Za-z0-9\-]/', '', (string) $order_number);
        if ($order_id === '') {
            $order_id = (string) $order->get_id();
        }

        $email = sanitize_email((string) $order->get_billing_email());
        if ($email === '') {
            return null;
        }

        $country = $order->get_shipping_country();
        if (!$country) {
            $country = $order->get_billing_country();
        }
        $country = strtoupper(preg_replace('/[^A-Z]/', '', (string) $country));
        if ($country === '') {
            $country = 'US';
        }

        $created_timestamp = $order->get_date_created() ? $order->get_date_created()->getTimestamp() : current_time('timestamp', true);
        $transit_days = (int) apply_filters('svic_google_customer_reviews_estimated_transit_days', 5, $order);
        if ($transit_days < 0) {
            $transit_days = 0;
        }
        $estimated_timestamp = $created_timestamp + ($transit_days * DAY_IN_SECONDS);
        $estimated_date = gmdate('Y-m-d', $estimated_timestamp);

        $meta_keys = (array) apply_filters('svic_google_customer_reviews_gtin_meta_keys', ['_wc_gcr_gtin', '_gtin', 'gtin', 'svic_gtin'], $order);
        $products = [];

        foreach ($order->get_items() as $item) {
            if (!($item instanceof \WC_Order_Item_Product)) {
                continue;
            }

            $product = $item->get_product();
            if (!($product instanceof \WC_Product)) {
                continue;
            }

            $gtin_value = '';
            foreach ($meta_keys as $meta_key) {
                if (!is_string($meta_key) || $meta_key === '') {
                    continue;
                }
                $raw = $product->get_meta($meta_key, true);
                if (is_string($raw) && $raw !== '') {
                    $gtin_value = preg_replace('/[^0-9A-Za-z]/', '', $raw);
                }

                if ($gtin_value !== '') {
                    break;
                }
            }

            if ($gtin_value === '') {
                continue;
            }

            $products[] = ['gtin' => $gtin_value];
        }

        $payload = [
            'merchant_id'             => $merchant_id,
            'order_id'                => $order_id,
            'email'                   => $email,
            'delivery_country'        => $country,
            'estimated_delivery_date' => $estimated_date,
        ];

        if ($products) {
            $payload['products'] = $products;
        }

        $payload = apply_filters('svic_google_customer_reviews_optin_payload', $payload, $order);

        if (!is_array($payload)) {
            return null;
        }

        foreach (['merchant_id', 'order_id', 'email', 'delivery_country', 'estimated_delivery_date'] as $required_key) {
            if (!isset($payload[$required_key]) || $payload[$required_key] === '') {
                return null;
            }
        }

        $payload['merchant_id'] = (int) $payload['merchant_id'];
        $payload['order_id'] = (string) $payload['order_id'];
        $payload['email'] = sanitize_email((string) $payload['email']);
        $payload['delivery_country'] = strtoupper(preg_replace('/[^A-Z]/', '', (string) $payload['delivery_country']));
        $payload['estimated_delivery_date'] = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $payload['estimated_delivery_date']) ? (string) $payload['estimated_delivery_date'] : $estimated_date;

        if ($payload['merchant_id'] <= 0 || $payload['email'] === '' || $payload['delivery_country'] === '') {
            return null;
        }

        if (isset($payload['products']) && is_array($payload['products'])) {
            $filtered_products = [];
            foreach ($payload['products'] as $product_entry) {
                if (!is_array($product_entry) || !isset($product_entry['gtin'])) {
                    continue;
                }
                $gtin_clean = preg_replace('/[^0-9A-Za-z]/', '', (string) $product_entry['gtin']);
                if ($gtin_clean === '') {
                    continue;
                }
                $filtered_products[] = ['gtin' => $gtin_clean];
            }

            if ($filtered_products) {
                $payload['products'] = $filtered_products;
            } else {
                unset($payload['products']);
            }
        }

        return $payload;
    }
}

if (!function_exists('svic_render_google_customer_reviews_optin')) {
    /**
     * Output the Google Customer Reviews opt-in script block.
     *
     * @param \WC_Order|null $order
     *
     * @return void
     */
    function svic_render_google_customer_reviews_optin($order): void
    {
        static $rendered = false;

        if ($rendered) {
            return;
        }

        $payload = svic_get_google_customer_reviews_optin_payload($order);
        if (!$payload) {
            return;
        }

        $json_payload = wp_json_encode($payload, JSON_UNESCAPED_SLASHES);
        if (!is_string($json_payload) || $json_payload === '') {
            return;
        }

        $rendered = true;

        ?>
        <script src="https://apis.google.com/js/platform.js?onload=svicRenderGoogleCustomerReviews" async defer></script>
        <script>
          window.svicGoogleCustomerReviewsConfig = <?php echo $json_payload; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>;
          window.svicRenderGoogleCustomerReviews = function() {
            var config = window.svicGoogleCustomerReviewsConfig || null;
            if (!config || !window.gapi || !window.gapi.load) {
              return;
            }
            window.gapi.load('surveyoptin', function() {
              if (!window.gapi || !window.gapi.surveyoptin || !window.gapi.surveyoptin.render) {
                return;
              }
              window.gapi.surveyoptin.render(config);
            });
          };
        </script>
        <?php
    }
}
