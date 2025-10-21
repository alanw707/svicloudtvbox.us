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
<main class="policy-page policy-page--legal">
  <section class="policy-hero">
    <div class="policy-hero__inner">
      <span class="policy-hero__badge"><?php echo svic_translate_html('legal_disclaimer.hero.badge'); ?></span>
      <h1 class="policy-hero__title"><?php echo svic_translate_html('legal_disclaimer.hero.title'); ?></h1>
      <p class="policy-hero__subtitle"><?php echo svic_translate_html('legal_disclaimer.hero.subtitle'); ?></p>
    </div>
  </section>

  <section class="policy-sections">
    <div class="policy-sections__inner">
      <?php foreach ($sections as $section) : ?>
        <article class="policy-card">
          <h2 class="policy-card__title"><?php echo $section['title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
          <p class="policy-card__copy"><?php echo $section['copy']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
          <ul class="policy-list">
            <?php foreach ($section['items'] as $item) : ?>
              <li><?php echo $item; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></li>
            <?php endforeach; ?>
          </ul>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="policy-sections">
    <div class="policy-sections__inner">
      <aside class="policy-card policy-support">
        <div class="policy-support__copy">
          <h2 class="policy-support__title"><?php echo svic_translate_html('legal_disclaimer.contact.title'); ?></h2>
          <p class="policy-support__body"><?php echo svic_translate_html('legal_disclaimer.contact.copy'); ?></p>
        </div>
        <div class="policy-support__actions">
          <a class="policy-support__cta" href="<?php echo esc_url($contact_page_url); ?>">
            <?php echo svic_translate_html('legal_disclaimer.contact.cta'); ?>
          </a>
        </div>
      </aside>
    </div>
  </section>
</main>

<?php
get_footer();
