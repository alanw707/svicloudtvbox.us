<?php
/**
 * Template Name: SviCloud Guides Hub
 */

global $post;

get_header();

$contact_url  = svic_url_with_lang(home_url('/contact'));
$faq_url      = svic_url_with_lang(home_url('/faq'));
$guides_url   = svic_url_with_lang(home_url('/guides'));

$setup_detail_url = svic_guides_get_section_link('setup');
if ($setup_detail_url) {
    $setup_detail_url = svic_url_with_lang($setup_detail_url);
} else {
    $setup_detail_url = $guides_url;
}

$guides_content  = svic_guides_get_content();
$hero_callouts   = svic_guides_get_content_item('hero_callouts');
$highlight_cards = svic_guides_get_content_item('highlight_cards');
$anchor_items    = svic_guides_get_anchor_items();
?>
<main class="guides-page surface--dark">
  <section class="guides-hero" id="guides-hero">
    <div class="guides-hero__inner">
      <div class="guides-hero__copy">
        <span class="guides-badge guides-badge--on-dark"><?php echo svic_translate_html('guides.hero.badge'); ?></span>
        <h1 class="guides-hero__title"><?php echo svic_translate_html('guides.hero.title'); ?></h1>
        <p class="guides-hero__lead"><?php echo svic_translate_html('guides.hero.lead'); ?></p>
        <div class="guides-hero__pill">
          <span class="guides-hero__pill-badge"><?php echo svic_translate_html('guides.hero.callouts_headline'); ?></span>
          <span class="guides-hero__pill-text">
            <span class="guides-hero__pill-headline"><?php echo svic_translate_html('guides.hero.pill_headline'); ?></span>
            <span class="guides-hero__pill-copy"><?php echo svic_translate_html('guides.hero.pill_copy'); ?></span>
          </span>
        </div>
        <ul class="guides-hero__highlights">
          <?php foreach ($hero_callouts as $callout_key) : ?>
            <li><?php echo svic_translate_html($callout_key); ?></li>
          <?php endforeach; ?>
        </ul>
        <div class="guides-hero__actions">
          <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($setup_detail_url); ?>">
            <?php echo svic_translate_html('guides.hero.primary_label'); ?>
          </a>
          <a class="lumen-pill lumen-pill--outline guides-hero__secondary" href="<?php echo esc_url($contact_url); ?>">
            <?php echo svic_translate_html('guides.hero.secondary_label'); ?>
          </a>
        </div>
      </div>
      <figure class="guides-hero__media" aria-hidden="true">
        <span class="guides-hero__blur"></span>
        <span class="guides-hero__device">
          <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/svicloud-10p-plus.png'); ?>" alt="" loading="lazy" width="360" height="240" />
        </span>
        <span class="guides-hero__remote">
          <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/svicloud-hero-product.png'); ?>" alt="" loading="lazy" width="220" height="160" />
        </span>
      </figure>
    </div>
  </section>

  <section class="guides-section guides-highlights" id="guides-highlights">
    <div class="guides-container">
      <header class="guides-section__header">
        <span class="guides-badge"><?php echo svic_translate_html('guides.highlights.badge'); ?></span>
        <h2 class="guides-section__title"><?php echo svic_translate_html('guides.highlights.title'); ?></h2>
      </header>
      <div class="guides-grid guides-grid--highlights">
        <?php foreach ($highlight_cards as $card) : ?>
          <article class="lumen-feature-card">
            <h3 class="lumen-feature-card__title"><?php echo svic_translate_html($card['title_key']); ?></h3>
            <p class="lumen-feature-card__copy"><?php echo svic_translate_html($card['copy_key']); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="guides-directory" id="guides-directory">
    <div class="guides-container">
      <header class="guides-section__header guides-directory__header">
        <span class="guides-badge"><?php echo svic_translate_html('guides.meta.anchor_nav_badge'); ?></span>
        <h2 class="guides-section__title"><?php echo svic_translate_html('guides.meta.anchor_nav_title'); ?></h2>
        <p class="guides-section__lead"><?php echo svic_translate_html('guides.meta.anchor_nav_description'); ?></p>
      </header>
      <div class="guides-directory__grid">
        <?php
        $directory_items = array_values(array_filter($anchor_items, static function ($item) {
            return isset($item['key']) && $item['key'] && $item['key'] !== 'overview';
        }));
        foreach ($directory_items as $index => $item) :
            $label_key   = $item['label_key'] ?? '';
            $summary_key = $item['summary_key'] ?? '';
            $section_key = $item['key'] ?? '';

            if (!$section_key) {
                continue;
            }

            $detail_link = svic_guides_get_section_link($section_key);
            if ($detail_link) {
                $detail_link = svic_url_with_lang($detail_link);
            } else {
                $detail_link = $guides_url;
            }

            $step_number = sprintf('%02d', $index + 1);
        ?>
          <article class="guides-directory__card guides-directory__card--<?php echo esc_attr($section_key); ?>">
            <a class="guides-directory__card-link" href="<?php echo esc_url($detail_link); ?>">
              <span class="guides-directory__step"><?php echo esc_html($step_number); ?></span>
              <h3 class="guides-directory__title"><?php echo svic_translate_html($label_key); ?></h3>
              <?php if (!empty($summary_key)) : ?>
                <p class="guides-directory__summary"><?php echo svic_translate_html($summary_key); ?></p>
              <?php endif; ?>
              <span class="guides-directory__cta">
                <span class="guides-directory__cta-label"><?php echo svic_translate_html('guides.detail.open_section'); ?></span>
                <span class="guides-directory__cta-icon" aria-hidden="true"></span>
              </span>
            </a>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>



  <section class="guides-support" id="guides-support">
    <div class="guides-support__inner">
      <span class="guides-badge guides-badge--on-dark"><?php echo svic_translate_html('guides.support.badge'); ?></span>
      <h2 class="guides-support__title"><?php echo svic_translate_html('guides.support.title'); ?></h2>
      <p class="guides-support__copy"><?php echo svic_translate_html('guides.support.copy'); ?></p>
      <div class="guides-support__actions">
        <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($contact_url); ?>">
          <?php echo svic_translate_html('guides.support.primary_label'); ?>
        </a>
        <a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url($faq_url); ?>">
          <?php echo svic_translate_html('guides.support.secondary_label'); ?>
        </a>
      </div>
    </div>
  </section>
</main>
<?php get_footer(); ?>
