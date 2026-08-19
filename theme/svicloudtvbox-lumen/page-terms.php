<?php
/**
 * Template Name: Terms and Conditions
 */

get_header();

$policy_sections = [
    [
        'title'  => svic_translate_html('terms.sections.agreement.title'),
        'anchor' => 'agreement',
        'type'   => 'list',
        'items'  => [
            svic_translate_html('terms.sections.agreement.items.acceptance'),
            svic_translate_html('terms.sections.agreement.items.age'),
            svic_translate_html('terms.sections.agreement.items.changes'),
        ],
    ],
    [
        'title'  => svic_translate_html('terms.sections.products.title'),
        'anchor' => 'products',
        'type'   => 'list',
        'items'  => [
            svic_translate_html('terms.sections.products.items.description'),
            svic_translate_html('terms.sections.products.items.pricing'),
            svic_translate_html('terms.sections.products.items.availability'),
            svic_translate_html('terms.sections.products.items.warranty'),
        ],
    ],
    [
        'title'  => svic_translate_html('terms.sections.orders.title'),
        'anchor' => 'orders',
        'type'   => 'list',
        'items'  => [
            svic_translate_html('terms.sections.orders.items.acceptance'),
            svic_translate_html('terms.sections.orders.items.payment'),
            svic_translate_html('terms.sections.orders.items.cancellation'),
        ],
    ],
    [
        'title'  => svic_translate_html('terms.sections.use.title'),
        'anchor' => 'use',
        'type'   => 'list',
        'items'  => [
            svic_translate_html('terms.sections.use.items.personal'),
            svic_translate_html('terms.sections.use.items.lawful'),
            svic_translate_html('terms.sections.use.items.compliance'),
        ],
    ],
    [
        'title'  => svic_translate_html('terms.sections.ip.title'),
        'anchor' => 'ip',
        'type'   => 'list',
        'items'  => [
            svic_translate_html('terms.sections.ip.items.ownership'),
            svic_translate_html('terms.sections.ip.items.license'),
            svic_translate_html('terms.sections.ip.items.restrictions'),
        ],
    ],
    [
        'title'  => svic_translate_html('terms.sections.liability.title'),
        'anchor' => 'liability',
        'type'   => 'list',
        'items'  => [
            svic_translate_html('terms.sections.liability.items.limitation'),
            svic_translate_html('terms.sections.liability.items.disclaimer'),
            svic_translate_html('terms.sections.liability.items.indemnification'),
        ],
    ],
    [
        'title'  => svic_translate_html('terms.sections.governing.title'),
        'anchor' => 'governing',
        'type'   => 'list',
        'items'  => [
            svic_translate_html('terms.sections.governing.items.law'),
            svic_translate_html('terms.sections.governing.items.disputes'),
            svic_translate_html('terms.sections.governing.items.severability'),
        ],
    ],
];

$support_title = svic_translate_html('terms.support.title');
$support_copy  = svic_translate_html('terms.support.copy');

$contact_page_url = function_exists('svic_url_with_lang')
    ? svic_url_with_lang(home_url('/contact/'))
    : home_url('/contact/');
?>
<main id="main-content" class="policy-page policy-page--terms" tabindex="-1">
  <section class="policy-hero">
    <div class="policy-hero__inner">
      <span class="policy-hero__badge"><?php echo svic_translate_html('terms.hero.badge'); ?></span>
      <h1 class="policy-hero__title"><?php echo svic_translate_html('terms.hero.title'); ?></h1>
      <p class="policy-hero__subtitle"><?php echo svic_translate_html('terms.hero.subtitle'); ?></p>
      <p class="policy-hero__effective"><?php echo svic_translate_html('terms.hero.effective'); ?></p>
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
            <?php echo svic_translate_html('terms.support.cta'); ?>
          </a>
        </div>
      </aside>
    </div>
  </section>
</main>

<?php
get_footer();
