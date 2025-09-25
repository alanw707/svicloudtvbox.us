<?php
/**
 * WooCommerce Archive (Shop)
 */

defined('ABSPATH') || exit;

get_header('shop');

$card_data = [
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
        'highlight'       => true,
        'fallback_url'    => home_url('/product/svicloud-10p-plus'),
        'fallback_price'  => '$248.99',
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
        'highlight'       => false,
        'fallback_url'    => home_url('/product/svicloud-10s'),
        'fallback_price'  => '$183.99',
    ],
];

foreach ($card_data as $key => $card) {
    if (!isset($card['product']) || !$card['product']) {
        $card_data[$key]['price_text'] = $card['fallback_price'];
        $card_data[$key]['url'] = $card['fallback_url'];
        continue;
    }

    $product = $card['product'];
    $price_html = method_exists($product, 'get_price_html') ? $product->get_price_html() : '';
    $card_data[$key]['price_text'] = $price_html ? wp_strip_all_tags($price_html) : $card['fallback_price'];
    $card_data[$key]['url'] = get_permalink($product->get_id());
}
?>

<main class="page-shell shop-page">
  <header class="page-hero shop-hero">
    <span class="compare-hero__badge"><?php echo svic_translate_html('shop.hero.badge'); ?></span>
    <h1 class="compare-hero__title"><?php echo svic_translate_html('shop.hero.title'); ?></h1>
    <p class="compare-hero__subtitle"><?php echo svic_translate_html('shop.hero.subtitle'); ?></p>
  </header>

  <section class="shop-products">
    <div class="compare-products__grid">
      <?php foreach ($card_data as $key => $card) :
          $price_text = isset($card['price_text']) ? $card['price_text'] : $card['fallback_price'];
          $url = isset($card['url']) ? $card['url'] : $card['fallback_url'];
          $highlight_class = !empty($card['highlight']) ? ' compare-product-card--highlight' : '';
      ?>
        <article class="compare-product-card<?php echo $highlight_class; ?>">
          <div class="compare-product-card__header">
            <h2 class="compare-product-card__title"><?php echo svic_translate_html($card['title_key']); ?></h2>
            <p class="compare-product-card__price"><?php echo esc_html($price_text); ?></p>
            <p class="compare-product-card__lead"><?php echo svic_translate_html($card['lead_key']); ?></p>
            <a class="lumen-pill <?php echo !empty($card['highlight']) ? 'lumen-pill--primary' : 'lumen-pill--ghost'; ?>" href="<?php echo esc_url($url); ?>">
              <?php echo svic_translate_html($card['button_key']); ?>
            </a>
          </div>
          <ul class="compare-product-card__list">
            <?php foreach ($card['feature_keys'] as $feature_key) : ?>
              <li><?php echo svic_translate_html($feature_key); ?></li>
            <?php endforeach; ?>
          </ul>
        </article>
      <?php endforeach; ?>
    </div>
  </section>
</main>

<?php get_footer('shop'); ?>
