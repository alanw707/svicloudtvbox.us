<?php
/**
 * Template Name: About SVICLOUDTVBOX.US
 */

global $post;

get_header();

$timeline_items = [
    [
        'year_key'  => 'about.timeline.items.2019.year',
        'title_key' => 'about.timeline.items.2019.title',
        'copy_key'  => 'about.timeline.items.2019.copy',
    ],
    [
        'year_key'  => 'about.timeline.items.2020.year',
        'title_key' => 'about.timeline.items.2020.title',
        'copy_key'  => 'about.timeline.items.2020.copy',
    ],
    [
        'year_key'  => 'about.timeline.items.2022.year',
        'title_key' => 'about.timeline.items.2022.title',
        'copy_key'  => 'about.timeline.items.2022.copy',
    ],
    [
        'year_key'  => 'about.timeline.items.2024.year',
        'title_key' => 'about.timeline.items.2024.title',
        'copy_key'  => 'about.timeline.items.2024.copy',
    ],
    [
        'year_key'  => 'about.timeline.items.2025.year',
        'title_key' => 'about.timeline.items.2025.title',
        'copy_key'  => 'about.timeline.items.2025.copy',
    ],
];

$value_cards = [
    [
        'icon'      => 'icon-star.svg',
        'title_key' => 'about.values.authentic.title',
        'copy_key'  => 'about.values.authentic.copy',
    ],
    [
        'icon'      => 'icon-handshake.svg',
        'title_key' => 'about.values.concierge.title',
        'copy_key'  => 'about.values.concierge.copy',
    ],
    [
        'icon'      => 'icon-family.svg',
        'title_key' => 'about.values.community.title',
        'copy_key'  => 'about.values.community.copy',
    ],
];

$stats = [
    [
        'value_key' => 'about.stats.orders.value',
        'label_key' => 'about.stats.orders.label',
    ],
    [
        'value_key' => 'about.stats.concierge_hours.value',
        'label_key' => 'about.stats.concierge_hours.label',
    ],
    [
        'value_key' => 'about.stats.rating.value',
        'label_key' => 'about.stats.rating.label',
    ],
];

$certificate_asset_relative = '/assets/images/certification-authorized-dealer.webp';
$certificate_asset_path     = get_template_directory() . $certificate_asset_relative;
$certificate_asset_url      = file_exists($certificate_asset_path) ? svic_theme_image_uri($certificate_asset_relative) : '';

$concierge_car_asset_relative = '/assets/svg/illustration-concierge-car.svg';
$concierge_car_asset_path     = get_template_directory() . $concierge_car_asset_relative;
$concierge_car_asset_url      = file_exists($concierge_car_asset_path) ? get_template_directory_uri() . $concierge_car_asset_relative : '';
?>
<main class="about-page">
  <section class="about-hero">
    <div class="about-hero__inner">
      <span class="about-hero__badge"><?php echo svic_translate_html('about.hero.badge'); ?></span>
      <h1 class="about-hero__title"><?php echo svic_translate_html('about.hero.title'); ?></h1>
      <p class="about-hero__lead"><?php echo svic_translate_html('about.hero.lead'); ?></p>
      <div class="about-hero__cta lumen-action-group">
        <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url(svic_url_with_lang(home_url('/contact'))); ?>">
          <?php echo svic_translate_html('about.hero.cta'); ?>
        </a>
        <a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url(svic_url_with_lang(home_url('/compare'))); ?>">
          <?php echo svic_translate_html('about.hero.secondary_cta'); ?>
        </a>
      </div>
    </div>
  </section>

  <section class="about-story">
    <div class="about-story__inner">
      <div class="about-story__copy">
        <h2 class="about-section-title"><?php echo svic_translate_html('about.story.title'); ?></h2>
        <p class="about-section-lead"><?php echo svic_translate_html('about.story.lead'); ?></p>
        <div class="about-section-body"><?php echo wp_kses_post(svic_translate_rich('about.story.body')); ?></div>
      </div>
      <div class="about-story__stats" role="list">
        <?php foreach ($stats as $stat) : ?>
          <div class="about-stat" role="listitem">
            <span class="about-stat__value"><?php echo svic_translate_html($stat['value_key']); ?></span>
            <span class="about-stat__label"><?php echo svic_translate_html($stat['label_key']); ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="about-certification" id="manufacturer-certification">
    <div class="about-certification__inner">
      <div class="about-certification__copy">
        <span class="about-certification__badge"><?php echo svic_translate_html('about.certification.badge'); ?></span>
        <h2 class="about-section-title"><?php echo svic_translate_html('about.certification.title'); ?></h2>
        <p class="about-section-lead"><?php echo svic_translate_html('about.certification.lead'); ?></p>
        <ul class="about-certification__list">
          <li><?php echo svic_translate_html('about.certification.points.vetting'); ?></li>
          <li><?php echo svic_translate_html('about.certification.points.inventory'); ?></li>
          <li><?php echo svic_translate_html('about.certification.points.support'); ?></li>
        </ul>
        <dl class="about-certification__meta">
          <div class="about-certification__meta-row">
            <dt><?php echo svic_translate_html('frontpage.certification.meta.number.label'); ?></dt>
            <dd><?php echo svic_translate_html('frontpage.certification.meta.number.value'); ?></dd>
          </div>
          <div class="about-certification__meta-row">
            <dt><?php echo svic_translate_html('frontpage.certification.meta.territory.label'); ?></dt>
            <dd><?php echo svic_translate_html('frontpage.certification.meta.territory.value'); ?></dd>
          </div>
          <div class="about-certification__meta-row">
            <dt><?php echo svic_translate_html('frontpage.certification.meta.term.label'); ?></dt>
            <dd><?php echo svic_translate_html('frontpage.certification.meta.term.value'); ?></dd>
          </div>
        </dl>
        <?php if (!empty($certificate_asset_url)) : ?>
          <a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url($certificate_asset_url); ?>" target="_blank" rel="noopener">
            <?php echo svic_translate_html('frontpage.certification.cta'); ?>
          </a>
        <?php endif; ?>
      </div>
      <?php if (!empty($certificate_asset_url)) : ?>
        <figure class="about-certification__media">
          <img src="<?php echo esc_url($certificate_asset_url); ?>" alt="<?php echo esc_attr(svic_translate('frontpage.certification.alt')); ?>" loading="lazy" width="800" height="594" />
        </figure>
      <?php endif; ?>
    </div>
  </section>

  <section class="about-timeline">
    <div class="about-timeline__inner">
      <header class="about-section-header">
        <span class="about-section-badge"><?php echo svic_translate_html('about.timeline.badge'); ?></span>
        <h2 class="about-section-title"><?php echo svic_translate_html('about.timeline.title'); ?></h2>
        <p class="about-section-lead"><?php echo svic_translate_html('about.timeline.lead'); ?></p>
      </header>
      <div class="about-timeline__grid">
        <?php foreach ($timeline_items as $item) : ?>
          <article class="about-timeline__item">
            <span class="about-timeline__year"><?php echo svic_translate_html($item['year_key']); ?></span>
            <h3 class="about-timeline__headline"><?php echo svic_translate_html($item['title_key']); ?></h3>
            <p class="about-timeline__copy"><?php echo svic_translate_html($item['copy_key']); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="about-values">
    <div class="about-values__inner">
      <header class="about-section-header">
        <span class="about-section-badge"><?php echo svic_translate_html('about.values.badge'); ?></span>
        <h2 class="about-section-title"><?php echo svic_translate_html('about.values.title'); ?></h2>
        <p class="about-section-lead"><?php echo svic_translate_html('about.values.lead'); ?></p>
      </header>
      <div class="about-values__cards">
        <?php foreach ($value_cards as $card) : ?>
          <?php $icon_path = get_template_directory_uri() . '/assets/svg/' . $card['icon']; ?>
          <article class="about-value-card">
            <span class="about-value-card__icon"><img src="<?php echo esc_url($icon_path); ?>" alt="" loading="lazy" /></span>
            <h3 class="about-value-card__title"><?php echo svic_translate_html($card['title_key']); ?></h3>
            <p class="about-value-card__copy"><?php echo svic_translate_html($card['copy_key']); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="about-concierge">
    <div class="about-concierge__inner">
      <div class="about-concierge__copy">
        <span class="about-concierge__badge"><?php echo svic_translate_html('about.concierge.badge'); ?></span>
        <h2 class="about-section-title about-concierge__title"><?php echo svic_translate_html('about.concierge.title'); ?></h2>
        <p class="about-section-lead about-concierge__lead"><?php echo svic_translate_html('about.concierge.lead'); ?></p>
        <ul class="about-concierge__list">
          <li><?php echo svic_translate_html('about.concierge.points.setup'); ?></li>
          <li><?php echo svic_translate_html('about.concierge.points.renewals'); ?></li>
          <li><?php echo svic_translate_html('about.concierge.points.events'); ?></li>
        </ul>
      </div>
      <div class="about-concierge__support">
        <?php if (!empty($concierge_car_asset_url)) : ?>
          <figure class="about-concierge__visual">
            <img src="<?php echo esc_url($concierge_car_asset_url); ?>" alt="" role="presentation" loading="lazy" width="360" height="220" />
          </figure>
        <?php endif; ?>
        <div class="about-concierge__cta">
          <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url(svic_url_with_lang(home_url('/contact'))); ?>">
            <?php echo svic_translate_html('about.concierge.cta_primary'); ?>
          </a>
          <a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url(svic_url_with_lang(home_url('/shop'))); ?>">
            <?php echo svic_translate_html('about.concierge.cta_secondary'); ?>
          </a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php get_footer(); ?>
