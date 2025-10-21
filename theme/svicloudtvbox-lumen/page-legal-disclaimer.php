<?php
/**
 * Template Name: Legal Disclaimer
 */

defined('ABSPATH') || exit;

global $post;

get_header();

$sections = [
    [
        'title' => svic_translate_html('legal_disclaimer.sections.hardware.title'),
        'copy'  => svic_translate_html('legal_disclaimer.sections.hardware.copy'),
        'items' => [
            svic_translate_html('legal_disclaimer.sections.hardware.items.line1'),
            svic_translate_html('legal_disclaimer.sections.hardware.items.line2'),
            svic_translate_html('legal_disclaimer.sections.hardware.items.line3'),
        ],
    ],
    [
        'title' => svic_translate_html('legal_disclaimer.sections.responsibilities.title'),
        'copy'  => svic_translate_html('legal_disclaimer.sections.responsibilities.copy'),
        'items' => [
            svic_translate_html('legal_disclaimer.sections.responsibilities.items.line1'),
            svic_translate_html('legal_disclaimer.sections.responsibilities.items.line2'),
            svic_translate_html('legal_disclaimer.sections.responsibilities.items.line3'),
        ],
    ],
    [
        'title' => svic_translate_html('legal_disclaimer.sections.prohibited.title'),
        'copy'  => svic_translate_html('legal_disclaimer.sections.prohibited.copy'),
        'items' => [
            svic_translate_html('legal_disclaimer.sections.prohibited.items.line1'),
            svic_translate_html('legal_disclaimer.sections.prohibited.items.line2'),
            svic_translate_html('legal_disclaimer.sections.prohibited.items.line3'),
        ],
    ],
    [
        'title' => svic_translate_html('legal_disclaimer.sections.support.title'),
        'copy'  => svic_translate_html('legal_disclaimer.sections.support.copy'),
        'items' => [
            svic_translate_html('legal_disclaimer.sections.support.items.line1'),
            svic_translate_html('legal_disclaimer.sections.support.items.line2'),
            svic_translate_html('legal_disclaimer.sections.support.items.line3'),
        ],
    ],
    [
        'title' => svic_translate_html('legal_disclaimer.sections.updates.title'),
        'copy'  => svic_translate_html('legal_disclaimer.sections.updates.copy'),
        'items' => [
            svic_translate_html('legal_disclaimer.sections.updates.items.line1'),
            svic_translate_html('legal_disclaimer.sections.updates.items.line2'),
        ],
    ],
];

$contact_page_url = function_exists('svic_url_with_lang')
    ? svic_url_with_lang(home_url('/contact/'))
    : home_url('/contact/');
?>
<main class="return-policy-page return-policy-page--legal">
  <section class="return-policy-hero">
    <div class="return-policy-hero__inner">
      <span class="return-policy-hero__badge"><?php echo svic_translate_html('legal_disclaimer.hero.badge'); ?></span>
      <h1 class="return-policy-hero__title"><?php echo svic_translate_html('legal_disclaimer.hero.title'); ?></h1>
      <p class="return-policy-hero__subtitle"><?php echo svic_translate_html('legal_disclaimer.hero.subtitle'); ?></p>
    </div>
  </section>

  <section class="return-policy-sections">
    <div class="return-policy-sections__inner">
      <?php foreach ($sections as $section) : ?>
        <article class="return-policy-card">
          <h2 class="return-policy-card__title"><?php echo $section['title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
          <p class="return-policy-card__copy"><?php echo $section['copy']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
          <ul class="return-policy-list">
            <?php foreach ($section['items'] as $item) : ?>
              <li><?php echo $item; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></li>
            <?php endforeach; ?>
          </ul>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="return-policy-sections">
    <div class="return-policy-sections__inner">
      <aside class="return-policy-card return-policy-support">
        <div class="return-policy-support__copy">
          <h2><?php echo svic_translate_html('legal_disclaimer.contact.title'); ?></h2>
          <p><?php echo svic_translate_html('legal_disclaimer.contact.copy'); ?></p>
        </div>
        <div class="return-policy-support__actions">
          <a class="return-policy-support__cta" href="<?php echo esc_url($contact_page_url); ?>">
            <?php echo svic_translate_html('legal_disclaimer.contact.cta'); ?>
          </a>
        </div>
      </aside>
    </div>
  </section>
</main>

<?php
get_footer();
