<?php
/**
 * FAQ Page Template
 *
 * Template Name: FAQ
 */

get_header();

$setup_guide_url = svic_url_with_lang(home_url('/guides-setup/'));
$compare_url     = svic_url_with_lang(home_url('/compare/'));
$support_url     = svic_url_with_lang(home_url('/contact/'));
$pdp_url         = svic_url_with_lang(home_url('/product/svicloud-10p-plus/'));
$faq_url         = svic_url_with_lang(home_url('/faq/'));

$faq_sections = [
    [
        'title_key' => 'faq.sections.device_models.title',
        'items'     => [
            [
                'question_key'    => 'faq.sections.device_models.items.model_choice.question',
                'answer_key'      => 'faq.sections.device_models.items.model_choice.answer',
                'replacements'    => [
                    'setup_guide_url' => esc_url($setup_guide_url),
                    'compare_url'     => esc_url($compare_url),
                ],
            ],
            [
                'question_key' => 'faq.sections.device_models.items.international_use.question',
                'answer_key'   => 'faq.sections.device_models.items.international_use.answer',
            ],
            [
                'question_key' => 'faq.sections.device_models.items.box_contents.question',
                'answer_key'   => 'faq.sections.device_models.items.box_contents.answer',
            ],
        ],
    ],
    [
        'title_key' => 'faq.sections.setup_activation.title',
        'items'     => [
            [
                'question_key'    => 'faq.sections.setup_activation.items.power_on.question',
                'answer_key'      => 'faq.sections.setup_activation.items.power_on.answer',
                'replacements'    => [
                    'setup_guide_url' => esc_url($setup_guide_url),
                ],
            ],
            [
                'question_key' => 'faq.sections.setup_activation.items.change_language.question',
                'answer_key'   => 'faq.sections.setup_activation.items.change_language.answer',
            ],
            [
                'question_key' => 'faq.sections.setup_activation.items.remote_pairing.question',
                'answer_key'   => 'faq.sections.setup_activation.items.remote_pairing.answer',
            ],
        ],
    ],
    [
        'title_key' => 'faq.sections.apps_content.title',
        'items'     => [
            [
                'question_key' => 'faq.sections.apps_content.items.preinstalled.question',
                'answer_key'   => 'faq.sections.apps_content.items.preinstalled.answer',
            ],
            [
                'question_key' => 'faq.sections.apps_content.items.third_party.question',
                'answer_key'   => 'faq.sections.apps_content.items.third_party.answer',
            ],
            [
                'question_key' => 'faq.sections.apps_content.items.family_content.question',
                'answer_key'   => 'faq.sections.apps_content.items.family_content.answer',
            ],
            [
                'question_key' => 'faq.sections.apps_content.items.adult_content.question',
                'answer_key'   => 'faq.sections.apps_content.items.adult_content.answer',
            ],
        ],
    ],
    [
        'title_key' => 'faq.sections.features_limitations.title',
        'items'     => [
            [
                'question_key' => 'faq.sections.features_limitations.items.karaoke_support.question',
                'answer_key'   => 'faq.sections.features_limitations.items.karaoke_support.answer',
            ],
            [
                'question_key' => 'faq.sections.features_limitations.items.voice_control.question',
                'answer_key'   => 'faq.sections.features_limitations.items.voice_control.answer',
            ],
            [
                'question_key' => 'faq.sections.features_limitations.items.subtitle_speed.question',
                'answer_key'   => 'faq.sections.features_limitations.items.subtitle_speed.answer',
            ],
        ],
    ],
    [
        'title_key' => 'faq.sections.troubleshooting_support.title',
        'items'     => [
            [
                'question_key'    => 'faq.sections.troubleshooting_support.items.buffering.question',
                'answer_key'      => 'faq.sections.troubleshooting_support.items.buffering.answer',
                'replacements'    => [
                    'setup_guide_url' => esc_url($setup_guide_url),
                ],
            ],
            [
                'question_key' => 'faq.sections.troubleshooting_support.items.orz_installer.question',
                'answer_key'   => 'faq.sections.troubleshooting_support.items.orz_installer.answer',
            ],
            [
                'question_key' => 'faq.sections.troubleshooting_support.items.stuck_loading.question',
                'answer_key'   => 'faq.sections.troubleshooting_support.items.stuck_loading.answer',
            ],
            [
                'question_key'    => 'faq.sections.troubleshooting_support.items.contact_support.question',
                'answer_key'      => 'faq.sections.troubleshooting_support.items.contact_support.answer',
                'replacements'    => [
                    'support_url' => esc_url($support_url),
                ],
            ],
        ],
    ],
];
?>
<main class="faq-page">
  <section class="faq-hero">
    <div class="faq-hero__inner">
      <span class="faq-hero__badge"><?php echo svic_translate_html('faq.hero.badge'); ?></span>
      <h1 class="faq-hero__title"><?php echo svic_translate_html('faq.hero.title'); ?></h1>
      <p class="faq-hero__subtitle"><?php echo svic_translate_html('faq.hero.subtitle'); ?></p>
      <div class="faq-hero__actions lumen-action-group" role="group">
        <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($setup_guide_url); ?>">
          <?php echo svic_translate_html('faq.hero.cta_primary'); ?>
        </a>
        <a class="lumen-pill lumen-pill--ghost" href="<?php echo esc_url($compare_url); ?>">
          <?php echo svic_translate_html('faq.hero.cta_secondary'); ?>
        </a>
      </div>
    </div>
  </section>

  <section class="faq-intent" id="faq-intent">
    <div class="faq-intent__inner">
      <div class="faq-intent__copy">
        <span class="faq-intent__badge"><?php echo svic_translate_html('product.traffic.badge'); ?></span>
        <h2 class="faq-intent__title"><?php echo svic_translate_html('product.traffic.title'); ?></h2>
        <p class="faq-intent__lead"><?php echo svic_translate_html('product.traffic.lead'); ?></p>
        <ul class="faq-intent__list" role="list">
          <li><?php echo svic_translate_html('product.traffic.bullets.shipping'); ?></li>
          <li><?php echo svic_translate_html('product.traffic.bullets.concierge'); ?></li>
          <li><?php echo svic_translate_html('product.traffic.bullets.warranty'); ?></li>
        </ul>
      </div>
      <div class="faq-intent__links lumen-action-group" role="group" aria-label="<?php echo esc_attr__('Key actions', 'svicloudtvbox-lumen'); ?>">
        <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($pdp_url); ?>"><?php echo svic_translate_html('frontpage.hero.cta.primary'); ?></a>
        <a class="lumen-pill lumen-pill--ghost" href="<?php echo esc_url($compare_url); ?>"><?php echo svic_translate_html('frontpage.hero.cta.compare'); ?></a>
        <a class="faq-intent__textlink" href="<?php echo esc_url($faq_url); ?>"><?php echo svic_translate_html('product.traffic.links.faq'); ?></a>
        <a class="faq-intent__textlink" href="<?php echo esc_url($support_url); ?>"><?php echo svic_translate_html('product.traffic.links.contact'); ?></a>
      </div>
    </div>
  </section>

  <section class="faq-sections">
    <div class="faq-sections__grid">
      <?php foreach ($faq_sections as $section) : ?>
        <article class="faq-section">
          <h2 class="faq-section__title"><?php echo svic_translate_html($section['title_key']); ?></h2>
          <div class="faq-section__items">
            <?php foreach ($section['items'] as $item) :
                $question = svic_translate_html($item['question_key']);
                $answer_replacements = $item['replacements'] ?? [];
                $answer = svic_translate_rich($item['answer_key'], $answer_replacements);
            ?>
              <details class="faq-item">
                <summary class="faq-item__question">
                  <span><?php echo $question; ?></span>
                </summary>
                <div class="faq-item__answer">
                  <?php echo wp_kses_post($answer); ?>
                </div>
              </details>
            <?php endforeach; ?>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </section>
</main>

<?php
get_footer();
