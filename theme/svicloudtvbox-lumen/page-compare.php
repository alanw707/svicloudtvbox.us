<?php
/**
 * Compare Page
 * Template Name: Compare Models
 */
get_header();

$hero_product_15p = class_exists('WooCommerce') ? svic_get_product_by_slug('svicloud-15p') : null;
$hero_15p_url = $hero_product_15p ? svic_url_with_lang(get_permalink($hero_product_15p->get_id())) : svic_url_with_lang(home_url('/product/svicloud-15p'));
$price_15p_markup = $hero_product_15p && function_exists('svic_price_html')
    ? svic_price_html($hero_product_15p)
    : '';
if ($price_15p_markup === '') {
    $price_15p_markup = '<span class="lumen-price"><span class="lumen-price__current">$288.00</span><span class="lumen-price__original">$379.00</span></span>';
}

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
        'key'       => 'processor',
        'highlight' => 'p15p',
    ],
    [
        'key'       => 'ram_storage',
        'highlight' => 'p10p',
    ],
    [
        'key'       => 'operating_system',
        'highlight' => 'p15p',
    ],
    [
        'key'       => 'connectivity',
        'highlight' => 'p15p',
    ],
    [
        'key'       => 'video_quality',
        'highlight' => 'p15p',
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
        'model'    => '15P',
        'base_key' => 'compare.differences.next_generation',
    ],
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

$hero_highlights = array_slice($key_differences, 0, 4);

$compare_product_bullets = [
    '15p' => [
        'compare.products.15p.bullets.processor_os',
        'compare.products.15p.bullets.memory_connectivity',
        'compare.products.15p.bullets.video_remote',
    ],
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
    'p15p'    => $hero_15p_url,
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

$compare_card_15p_image_html = $hero_product_15p ? svic_product_primary_image($hero_product_15p, 'large') : '';
$compare_card_10p_image_html = $hero_product_10p ? svic_product_primary_image($hero_product_10p, 'large') : '';
$compare_card_10s_image_html = $hero_product_10s ? svic_product_primary_image($hero_product_10s, 'large') : '';
// These two decision cards must render their product media without a scroll
// event; WebKit otherwise defers below-fold lazy images and leaves no visible
// comparison cue.
$compare_card_15p_image_html = str_replace('loading="lazy"', 'loading="eager"', $compare_card_15p_image_html);
$compare_card_10p_image_html = str_replace('loading="lazy"', 'loading="eager"', $compare_card_10p_image_html);
$compare_card_10s_image_html = str_replace('loading="lazy"', 'loading="eager"', $compare_card_10s_image_html);

if ($compare_card_15p_image_html === '') {
    $compare_card_15p_image_html = '<img src="' . esc_url(svic_theme_image_uri('/assets/images/products/svicloud-15p-primary-studio-v3-remote-watermarked.webp')) . '" alt="' . svic_translate_attr('compare.aria.product_alt_15p') . '" loading="eager" decoding="async" />';
}

if ($compare_card_10p_image_html === '') {
    $compare_card_10p_image_html = '<img src="' . esc_url(svic_theme_image_uri('/assets/images/svicloud-hero-product.webp')) . '" alt="' . svic_translate_attr('compare.aria.product_alt_10p') . '" loading="eager" decoding="async" />';
}

if ($compare_card_10s_image_html === '') {
    $compare_card_10s_image_html = '<img src="' . esc_url(svic_theme_image_uri('/assets/images/svicloud-hero-product.webp')) . '" alt="' . svic_translate_attr('compare.aria.product_alt_10s') . '" loading="eager" decoding="async" />';
}

$compare_cards = [
    '15p' => [
        'badge_key'             => 'shop.cards.15p.badge',
        'title_key'             => 'shop.cards.15p.title',
        'lead_key'              => 'compare.products.15p.lead',
        'fit_label_key'         => 'compare.products.15p.fit_label',
        'fit_copy_key'          => 'compare.products.15p.fit_copy',
        'cta_key'               => 'compare.products.15p.cta',
        'cta_url'               => $hero_15p_url,
        'image_html'            => $compare_card_15p_image_html,
        'price_markup'          => $price_15p_markup,
        'price_label_key'       => 'shop.cards.15p.price_label',
        'price_note_key'        => 'shop.cards.15p.price_note',
        'feature_keys'          => $compare_product_bullets['15p'],
        'highlight'             => true,
        'modifier'              => 'shop-product-card--backorder',
        'comparison_aria_key'   => 'compare.aria.comparison_15p',
        'comparison_value_key'  => 'p15p',
        'comparison_highlight'  => 'p15p',
        'model'                 => 'svicloud-15p',
    ],
    '10p' => [
        'badge_key'             => 'shop.cards.10p.badge',
        'title_key'             => 'shop.cards.10p.title',
        'lead_key'              => 'compare.products.10p.lead',
        'fit_label_key'         => 'compare.products.10p.fit_label',
        'fit_copy_key'          => 'compare.products.10p.fit_copy',
        'cta_key'               => 'compare.products.10p.cta',
        'cta_url'               => $hero_10p_url,
        'image_html'            => $compare_card_10p_image_html,
        'price_markup'          => $price_10p_markup,
        'price_note_key'        => 'shop.cards.price_note',
        'feature_keys'          => $compare_product_bullets['10p'],
        'highlight'             => true,
        'modifier'              => '',
        'comparison_aria_key'   => 'compare.aria.comparison_10p',
        'comparison_value_key'  => 'p10p',
        'comparison_highlight'  => 'p10p',
        'model'                 => 'svicloud-10p-plus',
    ],
    '10s' => [
        'badge_key'             => 'shop.cards.10s.badge',
        'title_key'             => 'shop.cards.10s.title',
        'lead_key'              => 'compare.products.10s.lead',
        'fit_label_key'         => 'compare.products.10s.fit_label',
        'fit_copy_key'          => 'compare.products.10s.fit_copy',
        'cta_key'               => 'compare.products.10s.cta',
        'cta_url'               => $hero_10s_url,
        'image_html'            => $compare_card_10s_image_html,
        'price_markup'          => $price_10s_markup,
        'price_note_key'        => 'shop.cards.price_note',
        'feature_keys'          => $compare_product_bullets['10s'],
        'highlight'             => false,
        'modifier'              => 'shop-product-card--best-value',
        'comparison_aria_key'   => 'compare.aria.comparison_10s',
        'comparison_value_key'  => 'p10s',
        'comparison_highlight'  => 'p10s',
        'model'                 => 'svicloud-10s',
    ],
];

$compare_hero_15p_image = function_exists('svic_get_product_image_meta')
    ? svic_get_product_image_meta($hero_product_15p, 0, 'large')
    : svic_get_theme_image_meta('/assets/images/products/svicloud-15p-primary-studio-v3-remote-watermarked.webp');
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
if (!empty($compare_hero_15p_image['url'])) {
    $compare_hero_background_style[] = "--compare-hero-photo-15p:url('" . esc_url_raw($compare_hero_15p_image['url']) . "')";
}
if (!empty($compare_hero_10p_background['url'])) {
    $compare_hero_background_style[] = "--compare-hero-photo-primary:url('" . esc_url_raw($compare_hero_10p_background['url']) . "')";
}
if (!empty($compare_hero_10s_background['url'])) {
    $compare_hero_background_style[] = "--compare-hero-photo-secondary:url('" . esc_url_raw($compare_hero_10s_background['url']) . "')";
}

?>

<main id="main-content" class="compare-page" tabindex="-1">
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
            <a class="lumen-pill lumen-pill--ghost compare-hero__action" href="<?php echo esc_url($hero_15p_url); ?>" data-svic-event="svic_cta_click" data-svic-location="compare_hero" data-svic-label="explore_15p" data-svic-model="svicloud-15p">
              <?php echo svic_translate_html('compare.products.15p.cta'); ?>
            </a>
          </div>
        </div>

        <div class="compare-hero__devices" aria-hidden="true">
          <img
            class="compare-hero__device compare-hero__device--15p"
            src="<?php echo esc_url($compare_hero_15p_image['url'] ?? svic_theme_image_uri('/assets/images/products/svicloud-15p-primary-studio-v3-remote-watermarked.webp')); ?>"
            alt=""
            loading="eager"
            decoding="async"
            fetchpriority="high"
            width="<?php echo esc_attr((string) ($compare_hero_15p_image['width'] ?? 1280)); ?>"
            height="<?php echo esc_attr((string) ($compare_hero_15p_image['height'] ?? 788)); ?>"
          />
          <img
            class="compare-hero__device compare-hero__device--10p"
            src="<?php echo esc_url($compare_hero_10p_image['url'] ?? svic_theme_image_uri('/assets/images/svicloud-hero-product.webp')); ?>"
            alt=""
            loading="eager"
            decoding="async"
            fetchpriority="high"
            width="<?php echo esc_attr((string) ($compare_hero_10p_image['width'] ?? 1024)); ?>"
            height="<?php echo esc_attr((string) ($compare_hero_10p_image['height'] ?? 1024)); ?>"
          />
          <img
            class="compare-hero__device compare-hero__device--10s"
            src="<?php echo esc_url($compare_hero_10s_image['url'] ?? svic_theme_image_uri('/assets/images/svicloud-hero-product.webp')); ?>"
            alt=""
            loading="eager"
            decoding="async"
            width="<?php echo esc_attr((string) ($compare_hero_10s_image['width'] ?? 750)); ?>"
            height="<?php echo esc_attr((string) ($compare_hero_10s_image['height'] ?? 470)); ?>"
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
        <a class="lumen-pill lumen-pill--ghost" href="<?php echo esc_url($compare_cta_links['p15p']); ?>" data-svic-event="svic_cta_click" data-svic-location="compare_traffic" data-svic-label="explore_15p" data-svic-model="svicloud-15p"><?php echo svic_translate_html('compare.traffic.links.p15p'); ?></a>
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
      <?php foreach ($compare_cards as $slug => $card) : ?>
        <?php
          $card_classes = 'shop-product-card compare-product-card';
          if (!empty($card['highlight'])) {
              $card_classes .= ' shop-product-card--highlight compare-product-card--highlight';
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
            <figure class="shop-product-card__media compare-product-card__media"><?php echo $card['image_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></figure>
            <div class="shop-product-card__price-line">
              <span class="shop-product-card__price-label"><?php echo svic_translate_html($card['price_label_key'] ?? 'shop.cards.price_label'); ?></span>
              <span class="shop-product-card__price-amount"><?php echo $card['price_markup']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
            </div>
            <?php if (!empty($card['price_note_key'])) : ?>
              <span class="shop-product-card__price-note"><?php echo svic_translate_html($card['price_note_key']); ?></span>
            <?php endif; ?>
            <h2 class="shop-product-card__title"><?php echo svic_translate_html($card['title_key']); ?></h2>
            <p class="shop-product-card__lead"><?php echo svic_translate_html($card['lead_key']); ?></p>
            <a class="lumen-pill <?php echo !empty($card['highlight']) ? 'lumen-pill--primary' : 'lumen-pill--ghost'; ?> shop-product-card__cta" href="<?php echo esc_url($card['cta_url']); ?>" data-svic-event="svic_cta_click" data-svic-location="compare_product_card" data-svic-label="<?php echo esc_attr($slug); ?>_card_cta" data-svic-model="<?php echo esc_attr($card['model']); ?>"><?php echo svic_translate_html($card['cta_key']); ?></a>
          </div>
          <div class="shop-product-card__divider" aria-hidden="true"></div>
          <div class="shop-product-card__body compare-product-card__body">
            <div class="shop-product-card__best-for compare-product-card__fit">
              <span class="shop-product-card__best-for-label"><?php echo svic_translate_html($card['fit_label_key']); ?></span>
              <p class="shop-product-card__best-for-value"><?php echo svic_translate_html($card['fit_copy_key']); ?></p>
            </div>
            <ul class="shop-product-card__features">
              <?php foreach ($card['feature_keys'] as $feature_key) : ?>
                <li><?php echo svic_translate_html($feature_key); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <section class="compare-product-card__comparison" aria-label="<?php echo svic_translate_attr($card['comparison_aria_key']); ?>">
            <h3 class="compare-product-card__comparison-title"><?php echo svic_translate_html('compare.comparison.title'); ?></h3>
            <dl class="compare-product-card__comparison-list">
              <?php foreach ($comparison_rows as $row) :
                  $is_highlight = isset($row['highlight']) && $row['highlight'] === $card['comparison_highlight'];
                  $base_key = 'compare.comparison.rows.' . $row['key'];
              ?>
                <div class="compare-product-card__comparison-item <?php echo $is_highlight ? 'is-highlight' : ''; ?>">
                  <dt><?php echo svic_translate_html($base_key . '.label'); ?></dt>
                  <dd><?php echo svic_translate_html($base_key . '.' . $card['comparison_value_key']); ?></dd>
                </div>
              <?php endforeach; ?>
            </dl>
          </section>
        </article>
      <?php endforeach; ?>
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
        <a class="lumen-pill lumen-pill--ghost" href="<?php echo esc_url($hero_15p_url); ?>" data-svic-event="svic_cta_click" data-svic-location="compare_final_cta" data-svic-label="explore_15p" data-svic-model="svicloud-15p"><?php echo svic_translate_html('compare.final_cta.cta_15p'); ?></a>
      </div>
    </div>
  </section>
</main>

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
    $hero_product_15p,
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
