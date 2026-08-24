<?php
/**
 * SVICLOUD 15P promotional feature page.
 * Template Name: SVICLOUD 15P Features
 */
if (!defined('ABSPATH')) { exit; }

$content = function_exists('svic_15p_promo_content') ? svic_15p_promo_content() : [];
$product_url = function_exists('svic_url_with_lang') ? svic_url_with_lang(home_url('/product/svicloud-15p/')) : home_url('/product/svicloud-15p/');
$compare_url = function_exists('svic_url_with_lang') ? svic_url_with_lang(home_url('/compare/')) : home_url('/compare/');
$apps_url = function_exists('svic_url_with_lang') ? svic_url_with_lang(home_url('/guides-apps/')) : home_url('/guides-apps/');
$shop_url = function_exists('svic_url_with_lang') ? svic_url_with_lang(home_url('/shop/')) : home_url('/shop/');
$image_meta = function_exists('svic_get_theme_image_meta') ? svic_get_theme_image_meta('/assets/images/products/svicloud-15p-marketing-v4-watermarked.webp') : [];
$image_url = $image_meta['url'] ?? get_template_directory_uri() . '/assets/images/products/svicloud-15p-marketing-v4-watermarked.webp';

get_header();
?>
<main id="main-content" class="fifteenp-promo surface--dark" tabindex="-1">
  <section class="fifteenp-hero">
    <div class="fifteenp-hero__inner">
      <div class="fifteenp-hero__copy">
        <span class="fifteenp-badge"><?php echo esc_html($content['badge'] ?? 'SVICLOUD 15P'); ?></span>
        <h1 class="fifteenp-hero__title"><?php echo esc_html($content['title'] ?? 'SVICLOUD 15P features'); ?></h1>
        <p class="fifteenp-hero__lead"><?php echo esc_html($content['lead'] ?? ''); ?></p>
        <div class="fifteenp-hero__actions lumen-action-group">
          <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($product_url); ?>"><?php echo esc_html($content['primary_cta'] ?? 'Buy 15P'); ?></a>
          <a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url($compare_url); ?>"><?php echo esc_html($content['secondary_cta'] ?? 'Compare models'); ?></a>
          <a class="fifteenp-hero__textlink" href="<?php echo esc_url($apps_url); ?>"><?php echo esc_html($content['tertiary_cta'] ?? 'App guide'); ?></a>
        </div>
        <ul class="fifteenp-hero__notes" role="list">
          <?php foreach ((array) ($content['hero_notes'] ?? []) as $note) : ?>
            <li><?php echo esc_html((string) $note); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <figure class="fifteenp-hero__media">
        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($content['title'] ?? 'SVICLOUD 15P TV Box'); ?>" width="<?php echo esc_attr((string) ($image_meta['width'] ?? 1200)); ?>" height="<?php echo esc_attr((string) ($image_meta['height'] ?? 580)); ?>" loading="eager" decoding="async" fetchpriority="high" />
      </figure>
    </div>
  </section>

  <section class="fifteenp-section fifteenp-section--features" aria-labelledby="fifteenp-features-title">
    <div class="fifteenp-container">
      <header class="fifteenp-section__header">
        <h2 id="fifteenp-features-title" class="fifteenp-section__title"><?php echo esc_html($content['feature_title'] ?? 'What 15P adds'); ?></h2>
        <p class="fifteenp-section__lead"><?php echo esc_html($content['feature_lead'] ?? ''); ?></p>
      </header>
      <div class="fifteenp-feature-grid">
        <?php foreach ((array) ($content['features'] ?? []) as $feature) : ?>
          <article class="fifteenp-feature">
            <h3 class="fifteenp-feature__title"><?php echo esc_html((string) ($feature[0] ?? '')); ?></h3>
            <p class="fifteenp-feature__copy"><?php echo esc_html((string) ($feature[1] ?? '')); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="fifteenp-section fifteenp-section--compare" aria-labelledby="fifteenp-compare-title">
    <div class="fifteenp-container fifteenp-container--split">
      <div class="fifteenp-compare__copy">
        <h2 id="fifteenp-compare-title" class="fifteenp-section__title"><?php echo esc_html($content['compare_title'] ?? '15P vs 10P+'); ?></h2>
        <p class="fifteenp-section__lead"><?php echo esc_html($content['compare_copy'] ?? ''); ?></p>
        <div class="fifteenp-compare__actions">
          <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($product_url); ?>"><?php echo esc_html($content['primary_cta'] ?? 'Buy 15P'); ?></a>
          <a class="lumen-pill lumen-pill--ghost" href="<?php echo esc_url($shop_url); ?>"><?php echo esc_html($content['shop_cta'] ?? 'Shop lineup'); ?></a>
        </div>
      </div>
      <div class="fifteenp-compare-table" role="table" aria-label="<?php echo esc_attr($content['compare_title'] ?? '15P comparison'); ?>">
        <?php foreach ((array) ($content['compare_rows'] ?? []) as $row) : ?>
          <div class="fifteenp-compare-table__row" role="row">
            <span role="cell" class="fifteenp-compare-table__label"><?php echo esc_html((string) ($row[0] ?? '')); ?></span>
            <span role="cell" class="fifteenp-compare-table__primary"><?php echo esc_html((string) ($row[1] ?? '')); ?></span>
            <span role="cell" class="fifteenp-compare-table__secondary"><?php echo esc_html((string) ($row[2] ?? '')); ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="fifteenp-section fifteenp-section--apps" aria-labelledby="fifteenp-apps-title">
    <div class="fifteenp-container fifteenp-app-callout">
      <div>
        <h2 id="fifteenp-apps-title" class="fifteenp-section__title"><?php echo esc_html($content['app_title'] ?? 'Yogurt TV Go and app downloads'); ?></h2>
        <p class="fifteenp-section__lead"><?php echo esc_html($content['app_copy'] ?? ''); ?></p>
      </div>
      <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($apps_url); ?>"><?php echo esc_html($content['tertiary_cta'] ?? 'App guide'); ?></a>
    </div>
  </section>

  <section class="fifteenp-section fifteenp-section--specs" aria-labelledby="fifteenp-specs-title">
    <div class="fifteenp-container">
      <h2 id="fifteenp-specs-title" class="fifteenp-section__title"><?php echo esc_html($content['spec_title'] ?? 'Core 15P specs'); ?></h2>
      <dl class="fifteenp-spec-list">
        <?php foreach ((array) ($content['specs'] ?? []) as $spec) : ?>
          <div class="fifteenp-spec-list__item">
            <dt><?php echo esc_html((string) ($spec[0] ?? '')); ?></dt>
            <dd><?php echo esc_html((string) ($spec[1] ?? '')); ?></dd>
          </div>
        <?php endforeach; ?>
      </dl>
    </div>
  </section>

  <section class="fifteenp-section fifteenp-section--faq" aria-labelledby="fifteenp-faq-title">
    <div class="fifteenp-container">
      <h2 id="fifteenp-faq-title" class="fifteenp-section__title"><?php echo esc_html($content['faq_title'] ?? '15P questions'); ?></h2>
      <div class="fifteenp-faq">
        <?php foreach ((array) ($content['faqs'] ?? []) as $faq) : ?>
          <details class="fifteenp-faq__item">
            <summary><?php echo esc_html((string) ($faq[0] ?? '')); ?></summary>
            <p><?php echo esc_html((string) ($faq[1] ?? '')); ?></p>
          </details>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</main>
<?php
$schema_faq = [];
foreach ((array) ($content['faqs'] ?? []) as $faq) {
    $schema_faq[] = [
        '@type' => 'Question',
        'name' => wp_strip_all_tags((string) ($faq[0] ?? '')),
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => wp_strip_all_tags((string) ($faq[1] ?? '')),
        ],
    ];
}
if ($schema_faq) {
    echo '<script type="application/ld+json">' . wp_json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        '@id' => untrailingslashit(svic_15p_promo_url()) . '#faqpage',
        'mainEntity' => $schema_faq,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
get_footer();
