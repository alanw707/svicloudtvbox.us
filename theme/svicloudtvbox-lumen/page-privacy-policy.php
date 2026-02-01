<?php
/**
 * Template Name: Privacy Policy
 */

get_header();

$policy_sections = [
    [
        'title'  => svic_translate_html('privacy_policy.sections.collect.title'),
        'anchor' => 'collect',
        'type'   => 'list',
        'items'  => [
            svic_translate_html('privacy_policy.sections.collect.items.personal'),
            svic_translate_html('privacy_policy.sections.collect.items.payment'),
            svic_translate_html('privacy_policy.sections.collect.items.device'),
            svic_translate_html('privacy_policy.sections.collect.items.usage'),
        ],
    ],
    [
        'title'  => svic_translate_html('privacy_policy.sections.use.title'),
        'anchor' => 'use',
        'type'   => 'list',
        'items'  => [
            svic_translate_html('privacy_policy.sections.use.items.orders'),
            svic_translate_html('privacy_policy.sections.use.items.support'),
            svic_translate_html('privacy_policy.sections.use.items.improve'),
            svic_translate_html('privacy_policy.sections.use.items.communicate'),
        ],
    ],
    [
        'title'  => svic_translate_html('privacy_policy.sections.sharing.title'),
        'anchor' => 'sharing',
        'type'   => 'list',
        'items'  => [
            svic_translate_html('privacy_policy.sections.sharing.items.processors'),
            svic_translate_html('privacy_policy.sections.sharing.items.shipping'),
            svic_translate_html('privacy_policy.sections.sharing.items.legal'),
            svic_translate_html('privacy_policy.sections.sharing.items.nosell'),
        ],
    ],
    [
        'title'  => svic_translate_html('privacy_policy.sections.cookies.title'),
        'anchor' => 'cookies',
        'type'   => 'list',
        'items'  => [
            svic_translate_html('privacy_policy.sections.cookies.items.essential'),
            svic_translate_html('privacy_policy.sections.cookies.items.analytics'),
            svic_translate_html('privacy_policy.sections.cookies.items.control'),
        ],
    ],
    [
        'title'  => svic_translate_html('privacy_policy.sections.security.title'),
        'anchor' => 'security',
        'type'   => 'list',
        'items'  => [
            svic_translate_html('privacy_policy.sections.security.items.ssl'),
            svic_translate_html('privacy_policy.sections.security.items.pci'),
            svic_translate_html('privacy_policy.sections.security.items.access'),
        ],
    ],
    [
        'title'  => svic_translate_html('privacy_policy.sections.rights.title'),
        'anchor' => 'rights',
        'type'   => 'list',
        'items'  => [
            svic_translate_html('privacy_policy.sections.rights.items.access'),
            svic_translate_html('privacy_policy.sections.rights.items.correct'),
            svic_translate_html('privacy_policy.sections.rights.items.delete'),
            svic_translate_html('privacy_policy.sections.rights.items.optout'),
        ],
    ],
];

$support_title = svic_translate_html('privacy_policy.support.title');
$support_copy  = svic_translate_html('privacy_policy.support.copy');

$contact_page_url = function_exists('svic_url_with_lang')
    ? svic_url_with_lang(home_url('/contact/'))
    : home_url('/contact/');
?>
<main class="policy-page policy-page--privacy">
  <section class="policy-hero">
    <div class="policy-hero__inner">
      <span class="policy-hero__badge"><?php echo svic_translate_html('privacy_policy.hero.badge'); ?></span>
      <h1 class="policy-hero__title"><?php echo svic_translate_html('privacy_policy.hero.title'); ?></h1>
      <p class="policy-hero__subtitle"><?php echo svic_translate_html('privacy_policy.hero.subtitle'); ?></p>
      <p class="policy-hero__effective"><?php echo svic_translate_html('privacy_policy.hero.effective'); ?></p>
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
            <?php echo svic_translate_html('privacy_policy.support.cta'); ?>
          </a>
        </div>
      </aside>
    </div>
  </section>
</main>

<?php
get_footer();
