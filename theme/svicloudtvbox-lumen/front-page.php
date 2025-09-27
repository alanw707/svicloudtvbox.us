<?php
/**
 * Front Page Template (Classic)
 */
get_header();

$hero_product_10p = class_exists('WooCommerce') ? svic_get_product_by_slug('svicloud-10p-plus') : null;
$hero_product_10s = class_exists('WooCommerce') ? svic_get_product_by_slug('svicloud-10s') : null;
$hero_10p_url = $hero_product_10p ? svic_url_with_lang(get_permalink($hero_product_10p->get_id())) : svic_url_with_lang(home_url('/product/svicloud-10p-plus'));
$hero_10s_url = $hero_product_10s ? svic_url_with_lang(get_permalink($hero_product_10s->get_id())) : svic_url_with_lang(home_url('/product/svicloud-10s'));

$hero_bullet_keys = [
    'frontpage.hero.bullets.shipping',
    'frontpage.hero.bullets.warranty',
    'frontpage.hero.bullets.fees',
];

$hero_spec_rows = [
    [
        'label_key' => 'frontpage.hero.card.specs.processor.label',
        'value_key' => 'frontpage.hero.card.specs.processor.value',
    ],
    [
        'label_key' => 'frontpage.hero.card.specs.connectivity.label',
        'value_key' => 'frontpage.hero.card.specs.connectivity.value',
    ],
    [
        'label_key' => 'frontpage.hero.card.specs.video.label',
        'value_key' => 'frontpage.hero.card.specs.video.value',
    ],
    [
        'label_key' => 'frontpage.hero.card.specs.extras.label',
        'value_key' => 'frontpage.hero.card.specs.extras.value',
    ],
];

$metrics = [
    [
        'icon'      => 'icon-truck.svg',
        'title_key' => 'frontpage.metrics.shipping.title',
        'copy_key'  => 'frontpage.metrics.shipping.copy',
    ],
    [
        'icon'      => 'icon-tool.svg',
        'title_key' => 'frontpage.metrics.concierge.title',
        'copy_key'  => 'frontpage.metrics.concierge.copy',
    ],
    [
        'icon'      => 'icon-lock.svg',
        'title_key' => 'frontpage.metrics.security.title',
        'copy_key'  => 'frontpage.metrics.security.copy',
    ],
    [
        'icon'      => 'icon-star.svg',
        'title_key' => 'frontpage.metrics.dealer.title',
        'copy_key'  => 'frontpage.metrics.dealer.copy',
    ],
];

$feature_cards = [
    [
        'icon'      => 'icon-box.svg',
        'title_key' => 'frontpage.feature_grid.cards.entertainment.title',
        'copy_key'  => 'frontpage.feature_grid.cards.entertainment.copy',
    ],
    [
        'icon'      => 'icon-bolt.svg',
        'title_key' => 'frontpage.feature_grid.cards.hardware.title',
        'copy_key'  => 'frontpage.feature_grid.cards.hardware.copy',
    ],
    [
        'icon'      => 'icon-handshake.svg',
        'title_key' => 'frontpage.feature_grid.cards.support.title',
        'copy_key'  => 'frontpage.feature_grid.cards.support.copy',
    ],
];

$experience_services = [
    [
        'icon'     => 'icon-satellite.svg',
        'text_key' => 'frontpage.experience.services.activation',
    ],
    [
        'icon'     => 'icon-wifi.svg',
        'text_key' => 'frontpage.experience.services.wifi',
    ],
    [
        'icon'     => 'icon-mic.svg',
        'text_key' => 'frontpage.experience.services.karaoke',
    ],
    [
        'icon'     => 'icon-family.svg',
        'text_key' => 'frontpage.experience.services.kids',
    ],
];

$pricing_cards = [
    '10p' => [
        'product'        => $hero_product_10p,
        'fallback_price' => '$248.99',
        'fallback_url'   => svic_url_with_lang(home_url('/product/svicloud-10p-plus')),
        'highlight'      => true,
        'badge_key'      => 'frontpage.pricing.cards.10p.badge',
        'title_key'      => 'frontpage.pricing.cards.10p.title',
        'interval_key'   => 'frontpage.pricing.cards.10p.interval',
        'copy_key'       => 'frontpage.pricing.cards.10p.copy',
        'feature_keys'   => [
            'frontpage.pricing.cards.10p.features.hdr',
            'frontpage.pricing.cards.10p.features.apps',
            'frontpage.pricing.cards.10p.features.wifi',
        ],
        'cta_key'   => 'frontpage.pricing.cards.10p.cta',
        'meta_keys' => [
            'frontpage.pricing.cards.10p.meta.shipping',
            'frontpage.pricing.cards.10p.meta.warranty',
            'frontpage.pricing.cards.10p.meta.concierge',
        ],
    ],
    '10s' => [
        'product'        => $hero_product_10s,
        'fallback_price' => '$183.99',
        'fallback_url'   => svic_url_with_lang(home_url('/product/svicloud-10s')),
        'highlight'      => false,
        'badge_key'      => null,
        'title_key'      => 'frontpage.pricing.cards.10s.title',
        'interval_key'   => 'frontpage.pricing.cards.10s.interval',
        'copy_key'       => 'frontpage.pricing.cards.10s.copy',
        'feature_keys'   => [
            'frontpage.pricing.cards.10s.features.hdr',
            'frontpage.pricing.cards.10s.features.remote',
            'frontpage.pricing.cards.10s.features.bundle',
        ],
        'cta_key'   => 'frontpage.pricing.cards.10s.cta',
        'meta_keys' => [
            'frontpage.pricing.cards.10s.meta.shipping',
            'frontpage.pricing.cards.10s.meta.warranty',
            'frontpage.pricing.cards.10s.meta.fees',
        ],
    ],
];

foreach ($pricing_cards as $slug => $card) {
    if (!empty($card['product'])) {
        $product = $card['product'];
        $pricing_cards[$slug]['price_html'] = svic_price_html($product);
        $pricing_cards[$slug]['cta_url'] = svic_url_with_lang(get_permalink($product->get_id()));
    } else {
        $pricing_cards[$slug]['price_html'] = sprintf('<span class="amount">%s</span>', esc_html($card['fallback_price']));
        $pricing_cards[$slug]['cta_url'] = $card['fallback_url'];
    }
}
?>
<main class="main-content">
  <!-- Hero Section -->
  <section class="hero-dashboard" id="hero">
    <div class="hero-dashboard__background" aria-hidden="true">
      <span class="hero-dashboard__globe hero-dashboard__globe--left"></span>
      <span class="hero-dashboard__globe hero-dashboard__globe--right"></span>
    </div>
    <div class="hero-dashboard__inner">
      <div class="hero-dashboard__content">
        <span class="hero-dashboard__eyebrow"><?php echo svic_translate_html('frontpage.hero.badge'); ?></span>
        <h1 class="hero-dashboard__title"><?php echo svic_translate_html('frontpage.hero.title'); ?></h1>
        <p class="hero-dashboard__copy"><?php echo svic_translate_html('frontpage.hero.copy'); ?></p>
        <ul class="hero-dashboard__list" role="list">
          <?php foreach ($hero_bullet_keys as $bullet_key) : ?>
            <li><?php echo svic_translate_html($bullet_key); ?></li>
          <?php endforeach; ?>
        </ul>
        <div class="hero-dashboard__cta">
          <a class="hero-dashboard__button hero-dashboard__button--primary" href="<?php echo esc_url($hero_10p_url); ?>"><?php echo svic_translate_html('frontpage.hero.cta.primary'); ?></a>
          <a class="hero-dashboard__button hero-dashboard__button--secondary" href="#pricing">
            <span><?php echo svic_translate_html('frontpage.hero.cta.bundles'); ?></span>
          </a>
          <a class="hero-dashboard__button hero-dashboard__button--secondary" href="<?php echo esc_url(svic_url_with_lang(home_url('/compare'))); ?>">
            <span><?php echo svic_translate_html('frontpage.hero.cta.compare'); ?></span>
          </a>
        </div>
      </div>
      <div class="hero-dashboard__visual">
        <div class="hero-dashboard__badge"><?php echo svic_translate_html('frontpage.hero.card.badge'); ?></div>
        <div class="hero-dashboard__card">
          <div class="hero-dashboard__product">
            <img class="hero-dashboard__product-main" src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/svicloud-hero-product.png'); ?>" alt="<?php esc_attr_e('SVICLOUD streaming device with remote', 'svicloudtvbox-lumen'); ?>" loading="lazy" />
          </div>
          <div class="hero-dashboard__card-header">
            <span><?php echo svic_translate_html('frontpage.hero.card.headline'); ?></span>
            <span><?php echo svic_translate_html('frontpage.hero.card.timestamp'); ?></span>
          </div>
          <div class="hero-dashboard__stat">
            <strong><?php echo svic_translate_html('frontpage.hero.card.stat'); ?></strong>
          </div>
          <div class="hero-dashboard__spec-grid" role="list">
            <?php foreach ($hero_spec_rows as $row) : ?>
              <div class="hero-dashboard__spec-pill" role="listitem">
                <span class="hero-dashboard__spec-label"><?php echo svic_translate_html($row['label_key']); ?></span>
                <span class="hero-dashboard__spec-value"><?php echo svic_translate_html($row['value_key']); ?></span>
              </div>
            <?php endforeach; ?>
          </div>
          <div class="hero-dashboard__card-footer">
            <span><?php echo svic_translate_html('frontpage.hero.card.footer.shipping'); ?></span>
            <span><?php echo svic_translate_html('frontpage.hero.card.footer.support'); ?></span>
          </div>
        </div>
        <span class="hero-dashboard__spark" aria-hidden="true"></span>
      </div>
    </div>
  </section>

  <?php
  $certificate_asset_relative = '/assets/images/certification-authorized-dealer.jpg';
  $certificate_asset_path     = get_template_directory() . $certificate_asset_relative;
  $certificate_asset_url      = file_exists($certificate_asset_path) ? get_template_directory_uri() . $certificate_asset_relative : '';
  ?>
  <!-- Authorized Dealer Certificate -->
  <section class="lumen-certification" id="authorized-dealer">
    <div class="lumen-certification__inner">
      <div class="lumen-certification__copy">
        <span class="lumen-certification__badge"><?php echo svic_translate_html('frontpage.certification.badge'); ?></span>
        <h2 class="lumen-certification__title"><?php echo svic_translate_html('frontpage.certification.title'); ?></h2>
        <p class="lumen-certification__lead"><?php echo svic_translate_html('frontpage.certification.lead'); ?></p>
        <dl class="lumen-certification__meta">
          <div class="lumen-certification__meta-row">
            <dt><?php echo svic_translate_html('frontpage.certification.meta.number.label'); ?></dt>
            <dd><?php echo svic_translate_html('frontpage.certification.meta.number.value'); ?></dd>
          </div>
          <div class="lumen-certification__meta-row">
            <dt><?php echo svic_translate_html('frontpage.certification.meta.territory.label'); ?></dt>
            <dd><?php echo svic_translate_html('frontpage.certification.meta.territory.value'); ?></dd>
          </div>
          <div class="lumen-certification__meta-row">
            <dt><?php echo svic_translate_html('frontpage.certification.meta.term.label'); ?></dt>
            <dd><?php echo svic_translate_html('frontpage.certification.meta.term.value'); ?></dd>
          </div>
        </dl>
        <p class="lumen-certification__footnote"><?php echo svic_translate_html('frontpage.certification.footnote'); ?></p>
        <?php if (!empty($certificate_asset_url)) : ?>
          <a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url($certificate_asset_url); ?>" target="_blank" rel="noopener">
            <?php echo svic_translate_html('frontpage.certification.cta'); ?>
          </a>
        <?php endif; ?>
      </div>
      <?php if (!empty($certificate_asset_url)) : ?>
        <figure class="lumen-certification__media">
          <img src="<?php echo esc_url($certificate_asset_url); ?>" alt="<?php echo esc_attr(svic_translate('frontpage.certification.alt')); ?>" loading="lazy" width="800" height="594" />
        </figure>
      <?php endif; ?>
    </div>
  </section>

  <!-- Credibility Bar -->
  <section class="lumen-metrics" aria-label="<?php esc_attr_e('Key SVICLOUD advantages', 'svicloudtvbox-lumen'); ?>">
    <div class="lumen-metrics__inner">
      <?php foreach ($metrics as $metric) : ?>
        <?php $icon_path = get_template_directory_uri() . '/assets/svg/' . $metric['icon']; ?>
        <article class="lumen-metric">
          <span class="lumen-metric__glow" aria-hidden="true"></span>
          <span class="lumen-metric__icon">
            <img src="<?php echo esc_url($icon_path); ?>" alt="" loading="lazy" />
          </span>
          <div class="lumen-metric__copy">
            <strong><?php echo svic_translate_html($metric['title_key']); ?></strong>
            <span><?php echo svic_translate_html($metric['copy_key']); ?></span>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Feature Highlights -->
  <section class="lumen-feature-grid" id="experience">
    <div class="lumen-feature-grid__inner">
      <header class="lumen-section-header">
        <h2 class="lumen-section-header__title"><?php echo svic_translate_html('frontpage.feature_grid.title'); ?></h2>
        <p class="lumen-section-header__subtitle"><?php echo svic_translate_html('frontpage.feature_grid.subtitle'); ?></p>
      </header>
      <div class="lumen-feature-grid__cards">
        <?php foreach ($feature_cards as $card) : ?>
          <article class="lumen-feature-card">
            <span class="lumen-feature-card__icon">
              <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/svg/' . $card['icon']); ?>" alt="" loading="lazy" />
            </span>
            <h3 class="lumen-feature-card__title"><?php echo svic_translate_html($card['title_key']); ?></h3>
            <p class="lumen-feature-card__copy"><?php echo svic_translate_html($card['copy_key']); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Experience Section -->
  <section class="lumen-experience">
    <div class="lumen-experience__inner">
      <div class="lumen-experience__copy">
        <span class="lumen-experience__badge"><?php echo svic_translate_html('frontpage.experience.badge'); ?></span>
        <h2 class="lumen-experience__title"><?php echo svic_translate_html('frontpage.experience.title'); ?></h2>
        <p class="lumen-experience__lead"><?php echo svic_translate_html('frontpage.experience.lead'); ?></p>
        <ul class="lumen-experience__list">
          <li><?php echo svic_translate_html('frontpage.concierge.personalized_walkthrough'); ?></li>
          <li><?php echo svic_translate_html('frontpage.concierge.remote_updates'); ?></li>
          <li><?php echo svic_translate_html('frontpage.concierge.community_access'); ?></li>
        </ul>
      </div>
      <aside class="lumen-experience__card">
        <h3 class="lumen-experience__card-title"><?php echo svic_translate_html('frontpage.experience.card_title'); ?></h3>
        <ul class="lumen-experience__card-list">
          <?php foreach ($experience_services as $service) : ?>
            <li>
              <span class="lumen-experience__icon"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/svg/' . $service['icon']); ?>" alt="" loading="lazy" /></span>
              <?php echo svic_translate_html($service['text_key']); ?>
            </li>
          <?php endforeach; ?>
        </ul>
        <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url(svic_url_with_lang(home_url('/contact'))); ?>"><?php echo svic_translate_html('frontpage.experience.cta'); ?></a>
      </aside>
    </div>
  </section>

  <!-- Pricing Section -->
  <section class="lumen-pricing" id="pricing">
    <div class="lumen-pricing__inner">
      <header class="lumen-section-header">
        <h2 class="lumen-section-header__title"><?php echo svic_translate_html('frontpage.pricing.title'); ?></h2>
        <p class="lumen-section-header__subtitle"><?php echo svic_translate_html('frontpage.pricing.subtitle'); ?></p>
      </header>
      <div class="lumen-pricing__grid">
        <?php foreach ($pricing_cards as $card) : ?>
          <?php
          $card_classes   = 'lumen-pricing-card';
          $button_classes = 'lumen-pill lumen-pill--outline';

          // Featured cards get primary styling by default.
          if (!empty($card['highlight'])) {
              $card_classes   .= ' lumen-pricing-card--featured';
              $button_classes  = 'lumen-pill lumen-pill--primary';
          } else {
              // Allow per-card CTA style override via translations, e.g., frontpage.pricing.cards.10s.cta_style => 'primary'.
              // Derive the cta_style key from the provided cta key path.
              $cta_style_key = is_string($card['cta_key']) ? str_replace('.cta', '.cta_style', $card['cta_key']) : '';
              if ($cta_style_key !== '') {
                  $cta_style_value = svic_translate($cta_style_key);
                  if (is_string($cta_style_value) && strtolower($cta_style_value) === 'primary') {
                      $button_classes = 'lumen-pill lumen-pill--primary';
                  }
              }
          }
          ?>
          <article class="<?php echo esc_attr($card_classes); ?>">
            <?php if (!empty($card['badge_key'])) : ?>
              <div class="lumen-pricing-card__badge"><?php echo svic_translate_html($card['badge_key']); ?></div>
            <?php endif; ?>
            <h3 class="lumen-pricing-card__title"><?php echo svic_translate_html($card['title_key']); ?></h3>
            <div class="lumen-pricing-card__price">
              <?php echo $card['price_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
              <span class="lumen-pricing-card__interval"><?php echo svic_translate_html($card['interval_key']); ?></span>
            </div>
            <p class="lumen-pricing-card__copy"><?php echo svic_translate_html($card['copy_key']); ?></p>
            <ul class="lumen-pricing-card__features">
              <?php foreach ($card['feature_keys'] as $feature_key) : ?>
                <li><?php echo svic_translate_html($feature_key); ?></li>
              <?php endforeach; ?>
            </ul>
            <a class="<?php echo esc_attr($button_classes); ?>" href="<?php echo esc_url($card['cta_url']); ?>"><?php echo svic_translate_html($card['cta_key']); ?></a>
            <div class="lumen-pricing-card__meta">
              <?php foreach ($card['meta_keys'] as $meta_key) : ?>
                <span><?php echo svic_translate_html($meta_key); ?></span>
              <?php endforeach; ?>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</main>

<?php get_footer(); ?>
