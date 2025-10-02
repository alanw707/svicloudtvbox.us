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
    $card_data[$key]['url'] = svic_url_with_lang(get_permalink($product->get_id()));
}
?>

<main class="page-shell shop-page">
  <header class="page-hero shop-hero">
    <span class="shop-hero__badge"><?php echo svic_translate_html('shop.hero.badge'); ?></span>
    <h1 class="shop-hero__title"><?php echo svic_translate_html('shop.hero.title'); ?></h1>
    <p class="shop-hero__subtitle"><?php echo svic_translate_html('shop.hero.subtitle'); ?></p>
  </header>

  <section class="shop-products">
    <div class="shop-products__grid">
      <?php foreach ($card_data as $key => $card) :
          $price_text = isset($card['price_text']) ? $card['price_text'] : $card['fallback_price'];
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
            <div class="shop-product-card__price-line">
              <span class="shop-product-card__price-label"><?php echo svic_translate_html('shop.cards.price_label'); ?></span>
              <span class="shop-product-card__price-amount"><?php echo esc_html($price_text); ?></span>
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

<?php get_footer('shop'); ?>
