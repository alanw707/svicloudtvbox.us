<?php
/**
 * WooCommerce Archive (Shop)
 */

defined('ABSPATH') || exit;

get_header('shop');

$card_data = [
    '15p' => [
        'product'         => class_exists('WooCommerce') ? svic_get_product_by_slug('svicloud-15p') : null,
        'title_key'       => 'shop.cards.15p.title',
        'lead_key'        => 'shop.cards.15p.lead',
        'button_key'      => 'shop.cards.15p.button',
        'feature_keys'    => [
            'shop.cards.15p.features.hardware',
            'shop.cards.15p.features.apps',
            'shop.cards.15p.features.remote',
        ],
        'badge_key'       => 'shop.cards.15p.badge',
        'best_for_key'    => 'shop.cards.15p.best_for',
        'highlight'       => true,
        'modifier'        => 'shop-product-card--prelaunch',
        'prelaunch'       => true,
        'assurance_keys'  => [
            'shop.cards.15p.assurance.shipping',
            'shop.cards.15p.assurance.warranty',
            'shop.cards.15p.assurance.support',
        ],
        'price_label_key' => 'shop.cards.15p.price_label',
        'price_note_key'  => 'shop.cards.15p.price_note',
        'fallback_url'    => svic_url_with_lang(home_url('/product/svicloud-15p')),
        'fallback_price'  => '',
        'image_fallback'  => svic_theme_image_uri('/assets/images/svicloud-hero-product.webp'),
    ],
    '10p' => [
        'product'         => class_exists('WooCommerce') ? svic_get_product_by_slug('svicloud-10p-plus') : null,
        'title_key'       => 'shop.cards.10p.title',
        'lead_key'        => 'shop.cards.10p.lead',
        'button_key'      => 'shop.cards.10p.button',
        'feature_keys'    => [
            'shop.cards.10p.features.ram_storage',
            'shop.cards.10p.features.apps',
            'shop.cards.10p.features.remote',
        ],
        'badge_key'       => 'shop.cards.10p.badge',
        'best_for_key'    => 'shop.cards.10p.best_for',
        'highlight'       => true,
        'modifier'        => 'shop-product-card--premium',
        'assurance_keys'  => [
            'shop.cards.assurance.shipping',
            'shop.cards.assurance.warranty',
            'shop.cards.assurance.support',
        ],
        'price_note_key'  => 'shop.cards.price_note',
        'fallback_url'    => svic_url_with_lang(home_url('/product/svicloud-10p-plus')),
        'fallback_price'  => '$248.99',
        'image_fallback'  => svic_theme_image_uri('/assets/images/svicloud-hero-product.webp'),
    ],
    '10s' => [
        'product'         => class_exists('WooCommerce') ? svic_get_product_by_slug('svicloud-10s') : null,
        'title_key'       => 'shop.cards.10s.title',
        'lead_key'        => 'shop.cards.10s.lead',
        'button_key'      => 'shop.cards.10s.button',
        'feature_keys'    => [
            'shop.cards.10s.features.ram_storage',
            'shop.cards.10s.features.remote',
            'shop.cards.10s.features.ports',
        ],
        'badge_key'       => 'shop.cards.10s.badge',
        'best_for_key'    => 'shop.cards.10s.best_for',
        'highlight'       => false,
        'modifier'        => 'shop-product-card--best-value',
        'assurance_keys'  => [
            'shop.cards.assurance.shipping',
            'shop.cards.assurance.warranty',
            'shop.cards.assurance.support',
        ],
        'price_note_key'  => 'shop.cards.price_note',
        'fallback_url'    => svic_url_with_lang(home_url('/product/svicloud-10s')),
        'fallback_price'  => '$183.99',
        'image_fallback'  => svic_theme_image_uri('/assets/images/svicloud-hero-product.webp'),
    ],
];

foreach ($card_data as $key => $card) {
    $product = isset($card['product']) && $card['product'] ? $card['product'] : null;

    $url = isset($card['fallback_url']) ? $card['fallback_url'] : '#';
    $price_markup = '';
    $image_html = '';

    if (!empty($card['prelaunch'])) {
        $price_markup = '<span class="lumen-price"><span class="lumen-price__current">' . svic_translate_html('shop.cards.15p.price_tbc') . '</span></span>';
        if ($product) {
            $url = svic_url_with_lang(get_permalink($product->get_id()));
        }
        $image_html = '<div class="shop-product-card__prelaunch-media" role="img" aria-label="' . svic_translate_attr('shop.cards.15p.image_alt') . '"><strong>15P</strong><span>' . svic_translate_html('shop.cards.15p.image_tbc') . '</span></div>';
    } elseif ($product) {
        $price_markup = svic_price_html($product);
        if ($price_markup === '') {
            $price_markup = sprintf(
                '<span class="lumen-price"><span class="lumen-price__current">%s</span></span>',
                esc_html($card['fallback_price'])
            );
        }

        $url = svic_url_with_lang(get_permalink($product->get_id()));
        $image_html = svic_product_primary_image($product, 'large');
    } else {
        $price_markup = sprintf(
            '<span class="lumen-price"><span class="lumen-price__current">%s</span></span>',
            esc_html($card['fallback_price'])
        );
    }

    if ($image_html === '') {
        $fallback_src = isset($card['image_fallback']) ? $card['image_fallback'] : '';
        if ($fallback_src === '') {
            $fallback_src = svic_theme_image_uri('/assets/images/svicloud-hero-product.webp');
        }

        $alt_text = svic_translate($card['title_key']);
        if (!is_string($alt_text) || $alt_text === '') {
            $alt_text = __('SVICLOUD device', 'svicloudtvbox-lumen');
        }

        $image_html = sprintf(
            '<img src="%s" alt="%s" loading="lazy" decoding="async" />',
            esc_url($fallback_src),
            esc_attr($alt_text)
        );
    }

    $card_data[$key]['price_markup'] = $price_markup;
    $card_data[$key]['url'] = $url;
    $card_data[$key]['image_html'] = $image_html;
    $card_data[$key]['average_rating'] = $product instanceof WC_Product ? (float) $product->get_average_rating() : 0.0;
    $card_data[$key]['rating_count'] = $product instanceof WC_Product ? (int) $product->get_rating_count() : 0;
}
?>

<main id="main-content" class="page-shell shop-page" tabindex="-1">
  <header class="page-hero shop-hero">
    <span class="shop-hero__badge"><?php echo svic_translate_html('shop.hero.badge'); ?></span>
    <h1 class="shop-hero__title"><?php echo svic_translate_html('shop.hero.title'); ?></h1>
    <p class="shop-hero__subtitle"><?php echo svic_translate_html('shop.hero.subtitle'); ?></p>
  </header>

  <section class="shop-products">
    <div class="shop-products__grid">
      <?php foreach ($card_data as $key => $card) :
          $price_markup = isset($card['price_markup']) ? $card['price_markup'] : sprintf(
              '<span class="lumen-price"><span class="lumen-price__current">%s</span></span>',
              esc_html($card['fallback_price'])
          );
          $image_html = isset($card['image_html']) ? $card['image_html'] : '';
          $url = isset($card['url']) ? $card['url'] : $card['fallback_url'];
          $card_classes = 'shop-product-card';
          if (!empty($card['highlight'])) {
              $card_classes .= ' shop-product-card--highlight';
          }
          if (!empty($card['modifier'])) {
              $card_classes .= ' ' . sanitize_html_class($card['modifier']);
          }
      ?>
        <article class="<?php echo esc_attr($card_classes); ?>">
          <div class="shop-product-card__header">
            <?php if (!empty($card['badge_key'])) : ?>
              <span class="shop-product-card__badge"><?php echo svic_translate_html($card['badge_key']); ?></span>
            <?php endif; ?>
            <?php if ($image_html) : ?>
              <figure class="shop-product-card__media"><?php echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></figure>
            <?php endif; ?>
            <?php if (!empty($card['rating_count']) && !empty($card['average_rating'])) : ?>
              <div class="shop-product-card__product-rating" aria-label="<?php echo esc_attr(svic_translate('shop.cards.product_rating_aria', [ 'rating' => number_format((float) $card['average_rating'], 1), 'count' => (string) (int) $card['rating_count'] ])); ?>">
                <span class="shop-product-card__product-rating-label"><?php echo svic_translate_html('shop.cards.product_rating_label'); ?></span>
                <span class="shop-product-card__product-rating-score"><?php echo esc_html(number_format((float) $card['average_rating'], 1)); ?></span>
                <span class="shop-product-card__product-rating-stars" aria-hidden="true">★★★★★</span>
                <span class="shop-product-card__product-rating-count"><?php echo esc_html(sprintf(svic_translate('shop.cards.product_rating_count'), (int) $card['rating_count'])); ?></span>
              </div>
            <?php endif; ?>
            <div class="shop-product-card__price-line">
              <span class="shop-product-card__price-label"><?php echo svic_translate_html($card['price_label_key'] ?? 'shop.cards.price_label'); ?></span>
              <span class="shop-product-card__price-amount"><?php echo $price_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
            </div>
            <?php if (!empty($card['price_note_key'])) : ?>
              <span class="shop-product-card__price-note"><?php echo svic_translate_html($card['price_note_key']); ?></span>
            <?php endif; ?>
            <h2 class="shop-product-card__title"><?php echo svic_translate_html($card['title_key']); ?></h2>
            <p class="shop-product-card__lead"><?php echo svic_translate_html($card['lead_key']); ?></p>
            <a class="lumen-pill <?php echo !empty($card['highlight']) ? 'lumen-pill--primary' : 'lumen-pill--ghost'; ?> shop-product-card__cta" href="<?php echo esc_url($url); ?>">
              <?php echo svic_translate_html($card['button_key']); ?>
            </a>
          </div>
          <div class="shop-product-card__divider" aria-hidden="true"></div>
          <div class="shop-product-card__body">
            <?php if (!empty($card['feature_keys'])) : ?>
              <ul class="shop-product-card__features">
                <?php foreach ($card['feature_keys'] as $feature_key) : ?>
                  <li><?php echo svic_translate_html($feature_key); ?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>

            <?php if (!empty($card['best_for_key'])) : ?>
              <div class="shop-product-card__best-for">
                <span class="shop-product-card__best-for-label"><?php echo svic_translate_html('shop.cards.best_for_label'); ?></span>
                <span class="shop-product-card__best-for-value"><?php echo esc_html(svic_translate($card['best_for_key'])); ?></span>
              </div>
            <?php endif; ?>

            <?php if (!empty($card['assurance_keys'])) : ?>
              <ul class="shop-product-card__assurance">
                <?php foreach ($card['assurance_keys'] as $assurance_key) : ?>
                  <li><?php echo svic_translate_html($assurance_key); ?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </section>
</main>

<?php
$shop_schema_products = [];
$shop_item_list       = [];
$shop_position        = 1;

foreach ($card_data as $card) {
    if (!empty($card['prelaunch']) || empty($card['product']) || !$card['product'] instanceof WC_Product) {
        continue;
    }

    $product_node = svic_build_product_schema_from_wc_product($card['product']);
    if (empty($product_node)) {
        continue;
    }

    $shop_schema_products[] = $product_node;
    $shop_item_list[]       = [
        '@type'    => 'ListItem',
        'position' => $shop_position++,
        'item'     => [
            '@id'  => $product_node['@id'],
            'name' => $product_node['name'],
        ],
    ];
}

if (!empty($shop_schema_products)) {
    $shop_page_url = get_post_type_archive_link('product');
    if (!$shop_page_url && function_exists('wc_get_page_permalink')) {
        $shop_page_url = wc_get_page_permalink('shop');
    }
    if (!$shop_page_url) {
        $shop_page_url = home_url('/shop/');
    }
    if (function_exists('svic_url_with_lang')) {
        $shop_page_url = svic_url_with_lang($shop_page_url);
    }
    $shop_page_url = esc_url_raw($shop_page_url);

    $graph_nodes = [];
    if ($shop_page_url !== '' && !empty($shop_item_list)) {
        $graph_nodes[] = [
            '@type'           => 'ItemList',
            '@id'             => untrailingslashit($shop_page_url) . '#shop-itemlist',
            'name'            => esc_html__('SVICLOUD streaming devices catalog', 'svicloudtvbox-lumen'),
            'url'             => $shop_page_url,
            'numberOfItems'   => count($shop_item_list),
            'itemListOrder'   => 'https://schema.org/ItemListOrderAscending',
            'itemListElement' => $shop_item_list,
        ];
    }

    $graph_nodes = array_merge($graph_nodes, $shop_schema_products);

    echo '<script type="application/ld+json">' . wp_json_encode([
        '@context' => 'https://schema.org',
        '@graph'   => $graph_nodes,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
?>

<?php get_footer('shop'); ?>
