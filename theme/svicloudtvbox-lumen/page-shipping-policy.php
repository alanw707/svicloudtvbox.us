<?php
/**
 * Template Name: Shipping Policy
 */

get_header();

$policy_sections = [
    [
        'title'  => svic_translate_html('shipping_policy.sections.processing.title'),
        'anchor' => 'processing',
        'type'   => 'list',
        'items'  => [
            svic_translate_rich('shipping_policy.sections.processing.items.cutoff'),
            svic_translate_rich('shipping_policy.sections.processing.items.dispatch'),
            svic_translate_rich('shipping_policy.sections.processing.items.tracking'),
        ],
    ],
    [
        'title'  => svic_translate_html('shipping_policy.sections.domestic.title'),
        'anchor' => 'domestic',
        'type'   => 'list',
        'items'  => [
            svic_translate_rich('shipping_policy.sections.domestic.items.standard'),
            svic_translate_rich('shipping_policy.sections.domestic.items.carriers'),
            svic_translate_rich('shipping_policy.sections.domestic.items.free'),
        ],
    ],
    [
        'title'  => svic_translate_html('shipping_policy.sections.canada.title'),
        'anchor' => 'canada',
        'type'   => 'list',
        'items'  => [
            svic_translate_rich('shipping_policy.sections.canada.items.timeframe'),
            svic_translate_rich('shipping_policy.sections.canada.items.customs'),
            svic_translate_rich('shipping_policy.sections.canada.items.duties'),
        ],
    ],
    [
        'title'  => svic_translate_html('shipping_policy.sections.issues.title'),
        'anchor' => 'issues',
        'type'   => 'list',
        'items'  => [
            svic_translate_rich('shipping_policy.sections.issues.items.delays'),
            svic_translate_rich('shipping_policy.sections.issues.items.damaged'),
            svic_translate_rich('shipping_policy.sections.issues.items.lost'),
        ],
    ],
];

$support_title = svic_translate_html('shipping_policy.support.title');
$support_copy  = svic_translate_html('shipping_policy.support.copy');

$contact_page_url = function_exists('svic_url_with_lang')
    ? svic_url_with_lang(home_url('/contact/'))
    : home_url('/contact/');
?>
<main class="policy-page policy-page--shipping">
  <section class="policy-hero">
    <div class="policy-hero__inner">
      <span class="policy-hero__badge"><?php echo svic_translate_html('shipping_policy.hero.badge'); ?></span>
      <h1 class="policy-hero__title"><?php echo svic_translate_html('shipping_policy.hero.title'); ?></h1>
      <p class="policy-hero__subtitle"><?php echo svic_translate_html('shipping_policy.hero.subtitle'); ?></p>
    </div>
  </section>

  <section class="policy-sections">
    <div class="policy-sections__inner">
      <?php foreach ($policy_sections as $section) : ?>
        <article id="<?php echo esc_attr('policy-section-' . $section['anchor']); ?>" class="policy-card">
          <h2 class="policy-card__title"><?php echo $section['title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
          <ul class="policy-list">
            <?php foreach ($section['items'] as $item) : ?>
              <li><?php echo $item; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></li>
            <?php endforeach; ?>
          </ul>
        </article>
      <?php endforeach; ?>

      <aside id="policy-section-support" class="policy-card policy-support">
        <div class="policy-support__copy">
          <h2 class="policy-support__title"><?php echo $support_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
          <p class="policy-support__body"><?php echo $support_copy; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
        </div>
        <div class="policy-support__actions lumen-action-group">
          <a class="policy-support__cta" href="<?php echo esc_url($contact_page_url); ?>">
            <?php echo svic_translate_html('shipping_policy.support.cta'); ?>
          </a>
        </div>
      </aside>
    </div>
  </section>
</main>

<?php
get_footer();
