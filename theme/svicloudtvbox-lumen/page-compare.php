<?php
/**
 * Compare Page
 * Template Name: Compare Models
 */
get_header();

$hero_product_10p = class_exists('WooCommerce') ? svic_get_product_by_slug('svicloud-10p-plus') : null;
$hero_product_10s = class_exists('WooCommerce') ? svic_get_product_by_slug('svicloud-10s') : null;
$hero_10p_url = $hero_product_10p ? get_permalink($hero_product_10p->get_id()) : home_url('/product/svicloud-10p-plus');
$hero_10s_url = $hero_product_10s ? get_permalink($hero_product_10s->get_id()) : home_url('/product/svicloud-10s');
$price_10p_text = $hero_product_10p ? wp_strip_all_tags($hero_product_10p->get_price_html()) : '$248.99';
$price_10s_text = $hero_product_10s ? wp_strip_all_tags($hero_product_10s->get_price_html()) : '$183.99';

$comparison_rows = [
    [
        'key'       => 'ram_storage',
        'highlight' => 'p10p',
    ],
    [
        'key'       => 'video_quality',
        'highlight' => '',
    ],
    [
        'key'       => 'voice_remote',
        'highlight' => '',
    ],
    [
        'key'       => 'kids_app',
        'highlight' => 'p10p',
    ],
    [
        'key'       => 'karaoke_mode',
        'highlight' => 'p10p',
    ],
    [
        'key'       => 'best_for',
        'highlight' => '',
    ],
];

$key_differences = [
    [
        'model'    => '10P+',
        'base_key' => 'compare.differences.premium_performance',
    ],
    [
        'model'    => '10P+',
        'base_key' => 'compare.differences.family_entertainment',
    ],
    [
        'model'    => '10S',
        'base_key' => 'compare.differences.smart_value',
    ],
];

$compare_product_bullets = [
    '10p' => [
        'compare.products.10p.bullets.ram_storage',
        'compare.products.10p.bullets.apps',
        'compare.products.10p.bullets.remote',
    ],
    '10s' => [
        'compare.products.10s.bullets.ram_storage',
        'compare.products.10s.bullets.remote',
        'compare.products.10s.bullets.ports',
    ],
];

?>

<main class="compare-page">
  <section class="compare-hero">
    <span class="compare-hero__badge"><?php echo svic_translate_html('compare.hero.badge'); ?></span>
    <h1 class="compare-hero__title">
      <span class="page-title-text"><?php echo svic_translate_html('compare.hero.title'); ?></span>
    </h1>
    <p class="compare-hero__subtitle">
      <span class="page-subtitle-text"><?php echo svic_translate_html('compare.hero.subtitle'); ?></span>
    </p>
  </section>

  <section class="compare-differences" aria-label="<?php echo esc_attr__('Key differences between models', 'svicloudtvbox-lumen'); ?>">
    <div class="compare-differences__grid">
      <?php foreach ($key_differences as $difference) : ?>
        <article class="compare-difference-card compare-difference-card--<?php echo esc_attr(strtolower($difference['model'])); ?>">
          <span class="compare-difference-card__model"><?php echo esc_html($difference['model']); ?></span>
          <h3 class="compare-difference-card__title"><?php echo svic_translate_html($difference['base_key'] . '.title'); ?></h3>
          <p class="compare-difference-card__copy"><?php echo svic_translate_html($difference['base_key'] . '.description'); ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="compare-products" aria-label="<?php echo esc_attr__('Product spotlight cards', 'svicloudtvbox-lumen'); ?>">
    <div class="compare-products__grid">
      <article class="compare-product-card compare-product-card--highlight">
        <div class="compare-product-card__header">
          <h2 class="compare-product-card__title"><?php echo svic_translate_html('shop.cards.10p.title'); ?></h2>
          <p class="compare-product-card__price"><?php echo esc_html($price_10p_text); ?></p>
          <p class="compare-product-card__lead"><?php echo svic_translate_html('compare.products.10p.lead'); ?></p>
          <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($hero_10p_url); ?>"><?php echo svic_translate_html('compare.products.10p.cta'); ?></a>
        </div>
        <ul class="compare-product-card__list">
          <?php foreach ($compare_product_bullets['10p'] as $bullet_key) : ?>
            <li><?php echo svic_translate_html($bullet_key); ?></li>
          <?php endforeach; ?>
        </ul>
        <div class="compare-product-card__divider" aria-hidden="true"></div>
        <section class="compare-product-card__comparison" aria-label="<?php echo esc_attr__('Feature comparison for SVICLOUD 10P+', 'svicloudtvbox-lumen'); ?>">
          <h3 class="compare-product-card__comparison-title"><?php echo svic_translate_html('compare.comparison.title'); ?></h3>
          <dl class="compare-product-card__comparison-list">
            <?php foreach ($comparison_rows as $row) :
                $is_highlight = isset($row['highlight']) && $row['highlight'] === 'p10p';
                $base_key = 'compare.comparison.rows.' . $row['key'];
            ?>
              <div class="compare-product-card__comparison-item <?php echo $is_highlight ? 'is-highlight' : ''; ?>">
                <dt><?php echo svic_translate_html($base_key . '.label'); ?></dt>
                <dd><?php echo svic_translate_html($base_key . '.p10p'); ?></dd>
              </div>
            <?php endforeach; ?>
          </dl>
        </section>
      </article>

      <article class="compare-product-card">
        <div class="compare-product-card__header">
          <h2 class="compare-product-card__title"><?php echo svic_translate_html('shop.cards.10s.title'); ?></h2>
          <p class="compare-product-card__price"><?php echo esc_html($price_10s_text); ?></p>
          <p class="compare-product-card__lead"><?php echo svic_translate_html('compare.products.10s.lead'); ?></p>
          <a class="lumen-pill lumen-pill--ghost" href="<?php echo esc_url($hero_10s_url); ?>"><?php echo svic_translate_html('compare.products.10s.cta'); ?></a>
        </div>
        <ul class="compare-product-card__list">
          <?php foreach ($compare_product_bullets['10s'] as $bullet_key) : ?>
            <li><?php echo svic_translate_html($bullet_key); ?></li>
          <?php endforeach; ?>
        </ul>
        <div class="compare-product-card__divider" aria-hidden="true"></div>
        <section class="compare-product-card__comparison" aria-label="<?php echo esc_attr__('Feature comparison for SVICLOUD 10S', 'svicloudtvbox-lumen'); ?>">
          <h3 class="compare-product-card__comparison-title"><?php echo svic_translate_html('compare.comparison.title'); ?></h3>
          <dl class="compare-product-card__comparison-list">
            <?php foreach ($comparison_rows as $row) :
                $is_highlight = isset($row['highlight']) && $row['highlight'] === 'p10s';
                $base_key = 'compare.comparison.rows.' . $row['key'];
            ?>
              <div class="compare-product-card__comparison-item <?php echo $is_highlight ? 'is-highlight' : ''; ?>">
                <dt><?php echo svic_translate_html($base_key . '.label'); ?></dt>
                <dd><?php echo svic_translate_html($base_key . '.p10s'); ?></dd>
              </div>
            <?php endforeach; ?>
          </dl>
        </section>
      </article>
    </div>
  </section>



  <section class="compare-final-cta" aria-label="<?php echo esc_attr__('Final call to action', 'svicloudtvbox-lumen'); ?>">
    <div class="compare-final-cta__inner">
      <span class="compare-final-cta__badge"><?php echo svic_translate_html('compare.final_cta.badge'); ?></span>
      <h2 class="compare-final-cta__title"><?php echo svic_translate_html('compare.final_cta.title'); ?></h2>
      <p class="compare-final-cta__copy"><?php echo svic_translate_html('compare.final_cta.copy'); ?></p>
      <div class="compare-final-cta__actions">
        <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($hero_10p_url); ?>"><?php echo svic_translate_html('compare.final_cta.cta_10p'); ?></a>
        <a class="lumen-pill lumen-pill--ghost" href="<?php echo esc_url($hero_10s_url); ?>"><?php echo svic_translate_html('compare.final_cta.cta_10s'); ?></a>
      </div>
    </div>
  </section>
</main>

<?php get_footer(); ?>
