<?php
/**
 * Template Name: SviCloud Guides Hub
 */

global $post;

get_header();

$setup_anchor = '#setup-guide';
$contact_url  = svic_url_with_lang(home_url('/contact'));
$faq_url      = svic_url_with_lang(home_url('/faq'));

$highlight_cards = [
    [
        'title_key' => 'guides.highlights.items.install.title',
        'copy_key'  => 'guides.highlights.items.install.copy',
    ],
    [
        'title_key' => 'guides.highlights.items.models.title',
        'copy_key'  => 'guides.highlights.items.models.copy',
    ],
    [
        'title_key' => 'guides.highlights.items.concierge.title',
        'copy_key'  => 'guides.highlights.items.concierge.copy',
    ],
];

$setup_steps = [
    [
        'title_key' => 'guides.setup.steps.connect.title',
        'copy_key'  => 'guides.setup.steps.connect.copy',
    ],
    [
        'title_key' => 'guides.setup.steps.language.title',
        'copy_key'  => 'guides.setup.steps.language.copy',
    ],
    [
        'title_key' => 'guides.setup.steps.disclaimer.title',
        'copy_key'  => 'guides.setup.steps.disclaimer.copy',
    ],
    [
        'title_key' => 'guides.setup.steps.remote.title',
        'copy_key'  => 'guides.setup.steps.remote.copy',
    ],
    [
        'title_key' => 'guides.setup.steps.time.title',
        'copy_key'  => 'guides.setup.steps.time.copy',
    ],
    [
        'title_key' => 'guides.setup.steps.network.title',
        'copy_key'  => 'guides.setup.steps.network.copy',
    ],
    [
        'title_key' => 'guides.setup.steps.apps.title',
        'copy_key'  => 'guides.setup.steps.apps.copy',
    ],
];

$app_cards = [
    [
        'title_key' => 'guides.apps.items.yogurt.title',
        'copy_key'  => 'guides.apps.items.yogurt.copy',
    ],
    [
        'title_key' => 'guides.apps.items.kids.title',
        'copy_key'  => 'guides.apps.items.kids.copy',
    ],
    [
        'title_key' => 'guides.apps.items.karaoke.title',
        'copy_key'  => 'guides.apps.items.karaoke.copy',
    ],
    [
        'title_key' => 'guides.apps.items.regional.title',
        'copy_key'  => 'guides.apps.items.regional.copy',
    ],
    [
        'title_key' => 'guides.apps.items.cherry.title',
        'copy_key'  => 'guides.apps.items.cherry.copy',
    ],
];

$post_setup_cards = [
    [
        'title_key' => 'guides.post_setup.items.explore.title',
        'copy_key'  => 'guides.post_setup.items.explore.copy',
    ],
    [
        'title_key' => 'guides.post_setup.items.install.title',
        'copy_key'  => 'guides.post_setup.items.install.copy',
    ],
    [
        'title_key' => 'guides.post_setup.items.tune.title',
        'copy_key'  => 'guides.post_setup.items.tune.copy',
    ],
];

$troubleshooting_cards = [
    [
        'title_key' => 'guides.troubleshooting.items.remote.title',
        'copy_key'  => 'guides.troubleshooting.items.remote.copy',
    ],
    [
        'title_key' => 'guides.troubleshooting.items.streaming.title',
        'copy_key'  => 'guides.troubleshooting.items.streaming.copy',
    ],
    [
        'title_key' => 'guides.troubleshooting.items.orz.title',
        'copy_key'  => 'guides.troubleshooting.items.orz.copy',
    ],
];

$resource_links = [
    [
        'title_key' => 'guides.resources.items.why.title',
        'copy_key'  => 'guides.resources.items.why.copy',
        'url'       => 'https://www.svicloudtvbox.com/why-buy-svicloud-tv-box-from-us-a0061.html',
    ],
    [
        'title_key' => 'guides.resources.items.channels.title',
        'copy_key'  => 'guides.resources.items.channels.copy',
        'url'       => 'https://www.svicloudtvbox.com/svicloud-tv-box-channels-list-a0063.html',
    ],
    [
        'title_key' => 'guides.resources.items.features.title',
        'copy_key'  => 'guides.resources.items.features.copy',
        'url'       => 'https://www.svicloudtvbox.com/blog/top-8-advantages-and-features-of-svicloud-tv-box-b0187.html',
    ],
    [
        'title_key' => 'guides.resources.items.which.title',
        'copy_key'  => 'guides.resources.items.which.copy',
        'url'       => 'https://www.svicloudtvbox.com/blog/which-svicloud-tv-box-should-i-buy-b0216.html',
    ],
    [
        'title_key' => 'guides.resources.items.compare.title',
        'copy_key'  => 'guides.resources.items.compare.copy',
        'url'       => 'https://www.svicloudtvbox.com/blog/svicloud-8p-vs-evpad-6p-vs-unblock-ubox9-b0212.html',
    ],
];
?>
<main class="guides-page">
  <section class="guides-hero">
    <div class="guides-hero__inner">
      <span class="guides-badge guides-badge--on-dark"><?php echo svic_translate_html('guides.hero.badge'); ?></span>
      <h1 class="guides-hero__title"><?php echo svic_translate_html('guides.hero.title'); ?></h1>
      <p class="guides-hero__lead"><?php echo svic_translate_html('guides.hero.lead'); ?></p>
      <div class="guides-hero__actions">
        <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($setup_anchor); ?>">
          <?php echo svic_translate_html('guides.hero.primary_label'); ?>
        </a>
        <a class="lumen-pill lumen-pill--outline guides-hero__secondary" href="<?php echo esc_url($contact_url); ?>">
          <?php echo svic_translate_html('guides.hero.secondary_label'); ?>
        </a>
      </div>
    </div>
  </section>

  <section class="guides-section guides-highlights">
    <div class="guides-container">
      <header class="guides-section__header">
        <span class="guides-badge"><?php echo svic_translate_html('guides.highlights.badge'); ?></span>
        <h2 class="guides-section__title"><?php echo svic_translate_html('guides.highlights.title'); ?></h2>
      </header>
      <div class="guides-grid guides-grid--highlights">
        <?php foreach ($highlight_cards as $card) : ?>
          <article class="guides-card guides-card--highlight">
            <h3 class="guides-card__title"><?php echo svic_translate_html($card['title_key']); ?></h3>
            <p class="guides-card__copy"><?php echo svic_translate_html($card['copy_key']); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="guides-section guides-setup" id="<?php echo esc_attr(ltrim($setup_anchor, '#')); ?>">
    <div class="guides-container">
      <header class="guides-section__header">
        <span class="guides-badge"><?php echo svic_translate_html('guides.setup.badge'); ?></span>
        <h2 class="guides-section__title"><?php echo svic_translate_html('guides.setup.title'); ?></h2>
        <p class="guides-section__lead"><?php echo svic_translate_html('guides.setup.lead'); ?></p>
      </header>
      <ol class="guides-steps">
        <?php foreach ($setup_steps as $step) : ?>
          <li class="guides-step">
            <div class="guides-step__content">
              <h3 class="guides-step__title"><?php echo svic_translate_html($step['title_key']); ?></h3>
              <p class="guides-step__copy"><?php echo wp_kses_post(svic_translate_rich($step['copy_key'])); ?></p>
            </div>
          </li>
        <?php endforeach; ?>
      </ol>
      <aside class="guides-note">
        <h3 class="guides-note__title"><?php echo svic_translate_html('guides.setup.note_title'); ?></h3>
        <p class="guides-note__copy"><?php echo svic_translate_html('guides.setup.note_copy'); ?></p>
      </aside>
    </div>
  </section>

  <section class="guides-section guides-apps">
    <div class="guides-container">
      <header class="guides-section__header">
        <span class="guides-badge"><?php echo svic_translate_html('guides.apps.badge'); ?></span>
        <h2 class="guides-section__title"><?php echo svic_translate_html('guides.apps.title'); ?></h2>
        <p class="guides-section__lead"><?php echo svic_translate_html('guides.apps.lead'); ?></p>
      </header>
      <div class="guides-grid guides-grid--apps">
        <?php foreach ($app_cards as $card) : ?>
          <article class="guides-card">
            <h3 class="guides-card__title"><?php echo svic_translate_html($card['title_key']); ?></h3>
            <p class="guides-card__copy"><?php echo svic_translate_html($card['copy_key']); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="guides-section guides-post-setup">
    <div class="guides-container">
      <header class="guides-section__header">
        <span class="guides-badge"><?php echo svic_translate_html('guides.post_setup.badge'); ?></span>
        <h2 class="guides-section__title"><?php echo svic_translate_html('guides.post_setup.title'); ?></h2>
      </header>
      <div class="guides-grid guides-grid--post">
        <?php foreach ($post_setup_cards as $card) : ?>
          <article class="guides-card">
            <h3 class="guides-card__title"><?php echo svic_translate_html($card['title_key']); ?></h3>
            <p class="guides-card__copy"><?php echo svic_translate_html($card['copy_key']); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="guides-section guides-troubleshooting">
    <div class="guides-container">
      <header class="guides-section__header">
        <span class="guides-badge"><?php echo svic_translate_html('guides.troubleshooting.badge'); ?></span>
        <h2 class="guides-section__title"><?php echo svic_translate_html('guides.troubleshooting.title'); ?></h2>
        <p class="guides-section__lead"><?php echo svic_translate_html('guides.troubleshooting.lead'); ?></p>
      </header>
      <div class="guides-grid guides-grid--troubleshooting">
        <?php foreach ($troubleshooting_cards as $card) : ?>
          <article class="guides-card guides-card--troubleshoot">
            <h3 class="guides-card__title"><?php echo svic_translate_html($card['title_key']); ?></h3>
            <p class="guides-card__copy"><?php echo wp_kses_post(svic_translate_rich($card['copy_key'])); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="guides-section guides-resources">
    <div class="guides-container">
      <header class="guides-section__header">
        <span class="guides-badge"><?php echo svic_translate_html('guides.resources.badge'); ?></span>
        <h2 class="guides-section__title"><?php echo svic_translate_html('guides.resources.title'); ?></h2>
        <p class="guides-section__lead"><?php echo svic_translate_html('guides.resources.lead'); ?></p>
      </header>
      <ul class="guides-resource-list">
        <?php foreach ($resource_links as $resource) : ?>
          <li class="guides-resource-item">
            <a class="guides-resource" href="<?php echo esc_url($resource['url']); ?>" target="_blank" rel="noopener">
              <span class="guides-resource__title"><?php echo svic_translate_html($resource['title_key']); ?></span>
              <span class="guides-resource__copy"><?php echo svic_translate_html($resource['copy_key']); ?></span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <section class="guides-support">
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
