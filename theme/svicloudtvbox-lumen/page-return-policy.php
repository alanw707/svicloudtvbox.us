<?php
/**
 * Template Name: Return Policy
 */

get_header();

$timeline_items = [
    svic_translate_html('return_policy.timeline.items.request'),
    svic_translate_html('return_policy.timeline.items.approval'),
    svic_translate_html('return_policy.timeline.items.ship'),
    svic_translate_html('return_policy.timeline.items.refund'),
];

$policy_sections = [
    [
        'title' => svic_translate_html('return_policy.sections.eligibility.title'),
        'anchor' => 'eligibility',
        'type'  => 'list',
        'items' => [
            svic_translate_html('return_policy.sections.eligibility.items.window'),
            svic_translate_html('return_policy.sections.eligibility.items.condition'),
            svic_translate_html('return_policy.sections.eligibility.items.activation'),
            svic_translate_html('return_policy.sections.eligibility.items.proof'),
        ],
    ],
    [
        'title' => svic_translate_html('return_policy.sections.start.title'),
        'anchor' => 'start',
        'type'  => 'steps',
        'items' => [
            svic_translate_html('return_policy.sections.start.steps.contact'),
            svic_translate_html('return_policy.sections.start.steps.label'),
            svic_translate_html('return_policy.sections.start.steps.pack'),
        ],
    ],
    [
        'title' => svic_translate_html('return_policy.sections.shipping.title'),
        'anchor' => 'shipping',
        'type'  => 'list',
        'items' => [
            svic_translate_html('return_policy.sections.shipping.items.responsibility'),
            svic_translate_html('return_policy.sections.shipping.items.restock'),
            svic_translate_html('return_policy.sections.shipping.items.tracking'),
        ],
    ],
    [
        'title' => svic_translate_html('return_policy.sections.refunds.title'),
        'anchor' => 'refunds',
        'type'  => 'list',
        'items' => [
            svic_translate_html('return_policy.sections.refunds.items.inspection'),
            svic_translate_html('return_policy.sections.refunds.items.method'),
            svic_translate_html('return_policy.sections.refunds.items.exchange'),
        ],
    ],
];

$support_title = svic_translate_html('return_policy.support.title');
$support_copy  = svic_translate_html('return_policy.support.copy');
$support_hours = svic_translate_html('return_policy.support.hours');
$support_cta   = svic_translate_html('return_policy.support.cta');

$contact_phone_label = svic_translate_html('contact.channels.items.phone.label');
$contact_phone_value = svic_translate_html('contact.channels.items.phone.value');
$contact_phone_href  = preg_replace('/[^0-9+]/', '', html_entity_decode($contact_phone_value, ENT_QUOTES, 'UTF-8'));

$contact_email_label = svic_translate_html('contact.channels.items.email.label');
$contact_email_value = svic_translate_html('contact.channels.items.email.value');
$contact_email_href  = sanitize_email(html_entity_decode($contact_email_value, ENT_QUOTES, 'UTF-8'));

$contact_page_url = function_exists('svic_url_with_lang')
    ? svic_url_with_lang(home_url('/contact/'))
    : home_url('/contact/');
?>
<main class="policy-page policy-page--returns">
  <section class="policy-hero">
    <div class="policy-hero__inner">
      <span class="policy-hero__badge"><?php echo svic_translate_html('return_policy.hero.badge'); ?></span>
      <h1 class="policy-hero__title"><?php echo svic_translate_html('return_policy.hero.title'); ?></h1>
      <p class="policy-hero__subtitle"><?php echo svic_translate_html('return_policy.hero.subtitle'); ?></p>
      <div class="policy-timeline" aria-label="<?php echo esc_attr(svic_translate_html('return_policy.timeline.title')); ?>">
        <?php foreach ($timeline_items as $index => $item) : ?>
          <div class="policy-timeline__item">
            <span class="policy-timeline__label"><?php echo esc_html($index + 1); ?></span>
            <p class="policy-timeline__copy"><?php echo $item; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="policy-sections">
    <div class="policy-sections__inner">
      <?php foreach ($policy_sections as $section) : ?>
        <article id="<?php echo esc_attr('policy-section-' . $section['anchor']); ?>" class="policy-card">
          <h2 class="policy-card__title"><?php echo $section['title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
          <?php if ($section['type'] === 'steps') : ?>
            <ol class="policy-steps">
              <?php foreach ($section['items'] as $item) : ?>
                <li class="policy-step">
                  <?php echo $item; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </li>
              <?php endforeach; ?>
            </ol>
          <?php else : ?>
            <ul class="policy-list">
              <?php foreach ($section['items'] as $item) : ?>
                <li>
                  <?php echo $item; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>

      <aside id="policy-section-support" class="policy-card policy-support">
        <div class="policy-support__copy">
          <h2 class="policy-support__title"><?php echo $support_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
          <p class="policy-support__body"><?php echo $support_copy; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
          <div class="policy-support__meta">
            <?php if ($contact_phone_label && $contact_phone_value && $contact_phone_href) : ?>
              <span>
                <strong><?php echo $contact_phone_label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
                <a href="<?php echo esc_url('tel:' . $contact_phone_href); ?>"><?php echo $contact_phone_value; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
              </span>
            <?php endif; ?>
            <?php if ($contact_email_label && $contact_email_value && $contact_email_href) : ?>
              <span>
                <strong><?php echo $contact_email_label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
                <a href="<?php echo esc_url('mailto:' . $contact_email_href); ?>">
                  <?php echo $contact_email_value; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </a>
              </span>
            <?php endif; ?>
            <?php if ($support_hours) : ?>
              <span class="policy-support__note"><?php echo $support_hours; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
            <?php endif; ?>
          </div>
        </div>
        <div class="policy-support__actions">
          <a class="policy-support__cta" href="<?php echo esc_url($contact_page_url); ?>">
            <?php echo $support_cta; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
          </a>
        </div>
      </aside>
    </div>
  </section>
</main>

<?php
get_footer();
