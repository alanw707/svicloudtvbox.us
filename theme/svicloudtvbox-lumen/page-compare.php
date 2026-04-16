<?php
/**
 * Compare Page
 * Template Name: Compare Models
 */
get_header();

$hero_product_10p = class_exists('WooCommerce') ? svic_get_product_by_slug('svicloud-10p-plus') : null;
$hero_product_10s = class_exists('WooCommerce') ? svic_get_product_by_slug('svicloud-10s') : null;
$hero_10p_url = $hero_product_10p ? svic_url_with_lang(get_permalink($hero_product_10p->get_id())) : svic_url_with_lang(home_url('/product/svicloud-10p-plus'));
$hero_10s_url = $hero_product_10s ? svic_url_with_lang(get_permalink($hero_product_10s->get_id())) : svic_url_with_lang(home_url('/product/svicloud-10s'));
$price_10p_markup = '';
if ($hero_product_10p && function_exists('svic_price_html')) {
    $price_10p_markup = svic_price_html($hero_product_10p);
}
if ($price_10p_markup === '') {
    $price_10p_markup = sprintf(
        '<span class="lumen-price"><span class="lumen-price__current">%s</span></span>',
        esc_html('$248.99')
    );
}

$price_10s_markup = '';
if ($hero_product_10s && function_exists('svic_price_html')) {
    $price_10s_markup = svic_price_html($hero_product_10s);
}
if ($price_10s_markup === '') {
    $price_10s_markup = sprintf(
        '<span class="lumen-price"><span class="lumen-price__current">%s</span></span>',
        esc_html('$183.99')
    );
}

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

$hero_highlights = array_slice($key_differences, 0, 3);

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

$hero_metric_keys = [
    'ram_storage',
    'video_quality',
    'best_for',
];

$compare_faq_items = [
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

$compare_cta_links = [
    'p10p'    => $hero_10p_url,
    'p10s'    => $hero_10s_url,
    'faq'     => svic_url_with_lang(home_url('/faq/')),
    'contact' => svic_url_with_lang(home_url('/contact/')),
];

$compare_confidence_cards = [
    'compare.confidence.cards.official',
    'compare.confidence.cards.shipping',
    'compare.confidence.cards.concierge',
    'compare.confidence.cards.warranty',
];

$compare_confidence_steps = [
    'compare.confidence.timeline.steps.choose',
    'compare.confidence.timeline.steps.order',
    'compare.confidence.timeline.steps.dispatch',
    'compare.confidence.timeline.steps.setup',
];

$compare_hero_10p_image = function_exists('svic_get_product_image_meta')
    ? svic_get_product_image_meta($hero_product_10p, 0, 'large')
    : svic_get_theme_image_meta('/assets/images/svicloud-hero-product.webp');
$compare_hero_10s_image = function_exists('svic_get_product_image_meta')
    ? svic_get_product_image_meta($hero_product_10s, 0, 'large')
    : svic_get_theme_image_meta('/assets/images/svicloud-hero-product.webp');
$compare_hero_10p_background = function_exists('svic_get_product_image_meta')
    ? svic_get_product_image_meta($hero_product_10p, 1, 'large')
    : svic_get_theme_image_meta('/assets/images/svicloud-hero-product.webp');
$compare_hero_10s_background = function_exists('svic_get_product_image_meta')
    ? svic_get_product_image_meta($hero_product_10s, 1, 'large')
    : svic_get_theme_image_meta('/assets/images/svicloud-hero-product.webp');
$compare_hero_background_style = [];
if (!empty($compare_hero_10p_background['url'])) {
    $compare_hero_background_style[] = "--compare-hero-photo-primary:url('" . esc_url_raw($compare_hero_10p_background['url']) . "')";
}
if (!empty($compare_hero_10s_background['url'])) {
    $compare_hero_background_style[] = "--compare-hero-photo-secondary:url('" . esc_url_raw($compare_hero_10s_background['url']) . "')";
}

?>

<main class="compare-page">
  <section class="compare-hero">
    <div class="compare-hero__background" aria-hidden="true"<?php if (!empty($compare_hero_background_style)) : ?> style="<?php echo esc_attr(implode('; ', $compare_hero_background_style)); ?>"<?php endif; ?>>
      <div class="compare-hero__photo compare-hero__photo--primary"></div>
      <div class="compare-hero__photo compare-hero__photo--secondary"></div>
      <div class="compare-hero__gradient compare-hero__gradient--teal"></div>
      <div class="compare-hero__gradient compare-hero__gradient--iris"></div>
      <div class="compare-hero__gradient compare-hero__gradient--amber"></div>
      <div class="compare-hero__mesh"></div>
    </div>

    <div class="compare-hero__inner">
      <div class="compare-hero__surface">
        <div class="compare-hero__body">
          <span class="compare-hero__badge"><?php echo svic_translate_html('compare.hero.badge'); ?></span>
          <h1 class="compare-hero__title" id="compare-hero-title">
            <span class="page-title-text"><?php echo svic_translate_html('compare.hero.title'); ?></span>
          </h1>
          <p class="compare-hero__subtitle">
            <span class="page-subtitle-text"><?php echo svic_translate_html('compare.hero.subtitle'); ?></span>
          </p>

          <div class="compare-hero__actions lumen-action-group" role="group" aria-label="<?php echo svic_translate_attr('compare.aria.hero_actions'); ?>">
            <a class="lumen-pill lumen-pill--primary compare-hero__action" href="<?php echo esc_url($hero_10p_url); ?>" data-svic-event="svic_cta_click" data-svic-location="compare_hero" data-svic-label="buy_10p" data-svic-model="svicloud-10p-plus">
              <?php echo svic_translate_html('compare.products.10p.cta'); ?>
            </a>
            <a class="lumen-pill lumen-pill--ghost compare-hero__action" href="<?php echo esc_url($hero_10s_url); ?>" data-svic-event="svic_cta_click" data-svic-location="compare_hero" data-svic-label="buy_10s" data-svic-model="svicloud-10s">
              <?php echo svic_translate_html('compare.products.10s.cta'); ?>
            </a>
          </div>
        </div>

        <div class="compare-hero__devices" aria-hidden="true">
          <img
            class="compare-hero__device compare-hero__device--10p"
            src="<?php echo esc_url($compare_hero_10p_image['url'] ?? svic_theme_image_uri('/assets/images/svicloud-hero-product.webp')); ?>"
            alt=""
            loading="lazy"
            decoding="async"
            width="800"
            height="600"
          />
          <img
            class="compare-hero__device compare-hero__device--10s"
            src="<?php echo esc_url($compare_hero_10s_image['url'] ?? svic_theme_image_uri('/assets/images/svicloud-hero-product.webp')); ?>"
            alt=""
            loading="lazy"
            decoding="async"
            width="800"
            height="600"
          />
        </div>

        <?php if (!empty($hero_highlights)) : ?>
          <ul class="compare-hero__highlights" aria-label="<?php echo svic_translate_attr('compare.aria.hero_highlights'); ?>">
            <?php foreach ($hero_highlights as $highlight) : ?>
              <li class="compare-hero__highlight">
                <span class="compare-hero__highlight-model"><?php echo esc_html($highlight['model']); ?></span>
                <span class="compare-hero__highlight-text"><?php echo svic_translate_html($highlight['base_key'] . '.title'); ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <section class="compare-traffic" id="why-us">
    <div class="compare-traffic__inner">
      <div class="compare-traffic__copy">
        <span class="compare-traffic__badge"><?php echo svic_translate_html('compare.traffic.badge'); ?></span>
        <h2 class="compare-traffic__title"><?php echo svic_translate_html('compare.traffic.title'); ?></h2>
        <p class="compare-traffic__lead"><?php echo svic_translate_html('compare.traffic.lead'); ?></p>
        <ul class="compare-traffic__list" role="list">
          <li><?php echo svic_translate_html('compare.traffic.bullets.shipping'); ?></li>
          <li><?php echo svic_translate_html('compare.traffic.bullets.concierge'); ?></li>
          <li><?php echo svic_translate_html('compare.traffic.bullets.warranty'); ?></li>
        </ul>
      </div>
      <div class="compare-traffic__links lumen-action-group" role="group" aria-label="<?php echo svic_translate_attr('compare.aria.traffic_actions'); ?>">
        <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($compare_cta_links['p10p']); ?>" data-svic-event="svic_cta_click" data-svic-location="compare_traffic" data-svic-label="buy_10p" data-svic-model="svicloud-10p-plus"><?php echo svic_translate_html('compare.traffic.links.p10p'); ?></a>
        <a class="lumen-pill lumen-pill--ghost" href="<?php echo esc_url($compare_cta_links['p10s']); ?>" data-svic-event="svic_cta_click" data-svic-location="compare_traffic" data-svic-label="buy_10s" data-svic-model="svicloud-10s"><?php echo svic_translate_html('compare.traffic.links.p10s'); ?></a>
        <a class="compare-traffic__textlink" href="<?php echo esc_url($compare_cta_links['faq']); ?>"><?php echo svic_translate_html('compare.traffic.links.faq'); ?></a>
        <a class="compare-traffic__textlink" href="<?php echo esc_url($compare_cta_links['contact']); ?>"><?php echo svic_translate_html('compare.traffic.links.contact'); ?></a>
      </div>
    </div>
  </section>

  <section class="compare-differences" aria-label="<?php echo svic_translate_attr('compare.aria.differences'); ?>">
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

  <section class="compare-faq" id="compare-faq">
    <div class="compare-faq__inner">
      <header class="compare-faq__header">
        <span class="compare-faq__badge"><?php echo svic_translate_html('product.faq.badge'); ?></span>
        <h2 class="compare-faq__title"><?php echo svic_translate_html('product.faq.title'); ?></h2>
        <p class="compare-faq__lead"><?php echo svic_translate_html('product.faq.lead'); ?></p>
      </header>
      <div class="compare-faq__grid">
        <?php foreach ($compare_faq_items as $idx => $item) : ?>
          <details class="compare-faq__item"<?php echo $idx === 0 ? ' open' : ''; ?>>
            <summary class="compare-faq__question"><?php echo svic_translate_html($item['question_key']); ?></summary>
            <div class="compare-faq__answer"><?php echo wp_kses_post(svic_translate($item['answer_key'])); ?></div>
          </details>
        <?php endforeach; ?>
      </div>
      <div class="compare-faq__cta">
        <a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url($compare_cta_links['faq']); ?>"><?php echo svic_translate_html('product.traffic.links.faq'); ?></a>
        <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($compare_cta_links['contact']); ?>"><?php echo svic_translate_html('product.traffic.links.contact'); ?></a>
      </div>
    </div>
  </section>

  <section class="compare-products" id="product-list" aria-label="<?php echo svic_translate_attr('compare.aria.product_list'); ?>">
    <div class="compare-products__grid">
      <article class="compare-product-card compare-product-card--highlight">
        <figure class="compare-product-card__media">
          <?php
          // Prefer WooCommerce primary image; fallback to theme product asset
          $img_10p = svic_product_primary_image($hero_product_10p, 'large');
          if ($img_10p) {
              echo $img_10p; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
          } else {
              echo '<img src="' . esc_url(svic_theme_image_uri('/assets/images/svicloud-hero-product.webp')) . '" alt="' . svic_translate_attr('compare.aria.product_alt_10p') . '" loading="lazy" decoding="async" />';
          }
          ?>
        </figure>
        <div class="compare-product-card__header">
          <h2 class="compare-product-card__title"><?php echo svic_translate_html('shop.cards.10p.title'); ?></h2>
          <p class="compare-product-card__price"><?php echo $price_10p_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
          <p class="compare-product-card__lead"><?php echo svic_translate_html('compare.products.10p.lead'); ?></p>
          <div class="compare-product-card__fit">
            <span class="compare-product-card__fit-label"><?php echo svic_translate_html('compare.products.10p.fit_label'); ?></span>
            <p class="compare-product-card__fit-copy"><?php echo svic_translate_html('compare.products.10p.fit_copy'); ?></p>
          </div>
          <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($hero_10p_url); ?>" data-svic-event="svic_cta_click" data-svic-location="compare_product_card" data-svic-label="10p_card_cta" data-svic-model="svicloud-10p-plus"><?php echo svic_translate_html('compare.products.10p.cta'); ?></a>
        </div>
        <ul class="compare-product-card__list">
          <?php foreach ($compare_product_bullets['10p'] as $bullet_key) : ?>
            <li><?php echo svic_translate_html($bullet_key); ?></li>
          <?php endforeach; ?>
        </ul>
        <div class="compare-product-card__divider" aria-hidden="true"></div>
        <section class="compare-product-card__comparison" aria-label="<?php echo svic_translate_attr('compare.aria.comparison_10p'); ?>">
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
        <figure class="compare-product-card__media">
          <?php
          $img_10s = svic_product_primary_image($hero_product_10s, 'large');
          if ($img_10s) {
              echo $img_10s; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
          } else {
              echo '<img src="' . esc_url(svic_theme_image_uri('/assets/images/svicloud-hero-product.webp')) . '" alt="' . svic_translate_attr('compare.aria.product_alt_10s') . '" loading="lazy" decoding="async" />';
          }
          ?>
        </figure>
        <div class="compare-product-card__header">
          <h2 class="compare-product-card__title"><?php echo svic_translate_html('shop.cards.10s.title'); ?></h2>
          <p class="compare-product-card__price"><?php echo $price_10s_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
          <p class="compare-product-card__lead"><?php echo svic_translate_html('compare.products.10s.lead'); ?></p>
          <div class="compare-product-card__fit">
            <span class="compare-product-card__fit-label"><?php echo svic_translate_html('compare.products.10s.fit_label'); ?></span>
            <p class="compare-product-card__fit-copy"><?php echo svic_translate_html('compare.products.10s.fit_copy'); ?></p>
          </div>
          <a class="lumen-pill lumen-pill--ghost" href="<?php echo esc_url($hero_10s_url); ?>" data-svic-event="svic_cta_click" data-svic-location="compare_product_card" data-svic-label="10s_card_cta" data-svic-model="svicloud-10s"><?php echo svic_translate_html('compare.products.10s.cta'); ?></a>
        </div>
        <ul class="compare-product-card__list">
          <?php foreach ($compare_product_bullets['10s'] as $bullet_key) : ?>
            <li><?php echo svic_translate_html($bullet_key); ?></li>
          <?php endforeach; ?>
        </ul>
        <div class="compare-product-card__divider" aria-hidden="true"></div>
        <section class="compare-product-card__comparison" aria-label="<?php echo svic_translate_attr('compare.aria.comparison_10s'); ?>">
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


  <section class="compare-confidence" id="compare-confidence" aria-labelledby="compare-confidence-title">
    <div class="compare-confidence__inner">
      <header class="compare-confidence__header">
        <span class="compare-confidence__badge"><?php echo svic_translate_html('compare.confidence.badge'); ?></span>
        <h2 class="compare-confidence__title" id="compare-confidence-title"><?php echo svic_translate_html('compare.confidence.title'); ?></h2>
        <p class="compare-confidence__lead"><?php echo svic_translate_html('compare.confidence.lead'); ?></p>
      </header>

      <div class="compare-confidence__grid">
        <?php foreach ($compare_confidence_cards as $confidence_base_key) : ?>
          <article class="compare-confidence-card">
            <h3 class="compare-confidence-card__title"><?php echo svic_translate_html($confidence_base_key . '.title'); ?></h3>
            <p class="compare-confidence-card__copy"><?php echo svic_translate_html($confidence_base_key . '.copy'); ?></p>
          </article>
        <?php endforeach; ?>
      </div>

      <div class="compare-confidence__timeline">
        <div class="compare-confidence__timeline-header">
          <span class="compare-confidence__timeline-badge"><?php echo svic_translate_html('compare.confidence.timeline.badge'); ?></span>
          <h3 class="compare-confidence__timeline-title"><?php echo svic_translate_html('compare.confidence.timeline.title'); ?></h3>
          <p class="compare-confidence__timeline-lead"><?php echo svic_translate_html('compare.confidence.timeline.lead'); ?></p>
        </div>
        <ol class="compare-confidence__steps">
          <?php foreach ($compare_confidence_steps as $index => $step_base_key) : ?>
            <li class="compare-confidence__step">
              <span class="compare-confidence__step-count"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
              <div class="compare-confidence__step-copy">
                <strong><?php echo svic_translate_html($step_base_key . '.title'); ?></strong>
                <p><?php echo svic_translate_html($step_base_key . '.copy'); ?></p>
              </div>
            </li>
          <?php endforeach; ?>
        </ol>
      </div>
    </div>
  </section>

  <section class="compare-final-cta" aria-label="<?php echo svic_translate_attr('compare.aria.final_cta'); ?>">
    <div class="compare-final-cta__inner" id="compare-final-cta">
      <span class="compare-final-cta__badge"><?php echo svic_translate_html('compare.final_cta.badge'); ?></span>
      <h2 class="compare-final-cta__title"><?php echo svic_translate_html('compare.final_cta.title'); ?></h2>
      <p class="compare-final-cta__copy"><?php echo svic_translate_html('compare.final_cta.copy'); ?></p>
      <div class="compare-final-cta__actions lumen-action-group">
        <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($hero_10p_url); ?>" data-svic-event="svic_cta_click" data-svic-location="compare_final_cta" data-svic-label="buy_10p" data-svic-model="svicloud-10p-plus"><?php echo svic_translate_html('compare.final_cta.cta_10p'); ?></a>
        <a class="lumen-pill lumen-pill--ghost" href="<?php echo esc_url($hero_10s_url); ?>" data-svic-event="svic_cta_click" data-svic-location="compare_final_cta" data-svic-label="buy_10s" data-svic-model="svicloud-10s"><?php echo svic_translate_html('compare.final_cta.cta_10s'); ?></a>
      </div>
    </div>
  </section>
</main>

<div class="compare-sticky-buy" id="compare-sticky-buy" aria-hidden="true" aria-label="<?php echo svic_translate_attr('compare.sticky_buy.aria_label'); ?>">
  <div class="compare-sticky-buy__inner">
    <span class="compare-sticky-buy__label"><?php echo svic_translate_html('compare.sticky_buy.label'); ?></span>
    <div class="compare-sticky-buy__actions">
      <a class="compare-sticky-buy__cta lumen-pill lumen-pill--primary" href="<?php echo esc_url($hero_10p_url); ?>" rel="nofollow" data-svic-event="svic_cta_click" data-svic-location="compare_sticky_buy" data-svic-label="sticky_buy_10p" data-svic-model="svicloud-10p-plus"><?php echo svic_translate_html('compare.sticky_buy.cta_10p'); ?></a>
      <a class="compare-sticky-buy__cta lumen-pill lumen-pill--ghost" href="<?php echo esc_url($hero_10s_url); ?>" rel="nofollow" data-svic-event="svic_cta_click" data-svic-location="compare_sticky_buy" data-svic-label="sticky_buy_10s" data-svic-model="svicloud-10s"><?php echo svic_translate_html('compare.sticky_buy.cta_10s'); ?></a>
    </div>
  </div>
</div>
<script>
(function() {
  var bar = document.getElementById('compare-sticky-buy');
  if (!bar) return;
  var hero = document.querySelector('.compare-hero');
  var finalCta = document.getElementById('compare-final-cta');
  function update() {
    var afterHero = hero ? window.scrollY > (hero.offsetTop + hero.offsetHeight) : window.scrollY > 400;
    var beforeFinalCta = finalCta ? window.scrollY + window.innerHeight < (finalCta.offsetTop + 120) : true;
    if (afterHero && beforeFinalCta) {
      bar.classList.add('is-visible');
      bar.removeAttribute('aria-hidden');
    } else {
      bar.classList.remove('is-visible');
      bar.setAttribute('aria-hidden', 'true');
    }
  }
  window.addEventListener('scroll', update, { passive: true });
  window.addEventListener('resize', update);
  update();
})();
</script>

<?php
$compare_schema_products = [];
$compare_item_list       = [];
$compare_position        = 1;

$compare_page_id  = get_queried_object_id();
$compare_page_url = $compare_page_id ? get_permalink($compare_page_id) : get_permalink();
if (function_exists('svic_url_with_lang')) {
    $compare_page_url = svic_url_with_lang($compare_page_url);
}
$compare_page_url = esc_url_raw($compare_page_url);

$schema_products = [
    $hero_product_10p,
    $hero_product_10s,
];

foreach ($schema_products as $schema_product) {
    if (!$schema_product instanceof WC_Product) {
        continue;
    }

    $product_node = svic_build_product_schema_from_wc_product($schema_product);
    if (empty($product_node)) {
        continue;
    }

    $compare_schema_products[] = $product_node;
    $compare_item_list[]       = [
        '@type'    => 'ListItem',
        'position' => $compare_position++,
        'item'     => [
            '@id'  => $product_node['@id'],
            'name' => $product_node['name'],
        ],
    ];
}

if (!empty($compare_schema_products)) {
    $graph_nodes = [];

    if (!empty($compare_item_list) && $compare_page_url !== '') {
        $graph_nodes[] = [
            '@type'           => 'ItemList',
            '@id'             => untrailingslashit($compare_page_url) . '#compare-itemlist',
            'name'            => svic_translate('compare.schema.item_list_name'),
            'url'             => $compare_page_url,
            'numberOfItems'   => count($compare_item_list),
            'itemListOrder'   => 'https://schema.org/ItemListOrderAscending',
            'itemListElement' => $compare_item_list,
        ];
    }

    $graph_nodes = array_merge($graph_nodes, $compare_schema_products);

    echo '<script type="application/ld+json">' . wp_json_encode([
        '@context' => 'https://schema.org',
        '@graph'   => $graph_nodes,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
?>

<?php get_footer(); ?>
