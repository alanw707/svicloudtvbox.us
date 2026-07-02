<?php
/**
 * Contact Page Template
 *
 * Template Name: Contact
 */

get_header();

$phone_display = svic_translate('contact.channels.items.phone.value');
$phone_digits  = preg_replace('/[^0-9+]/', '', $phone_display);
$phone_digits  = ltrim($phone_digits ?? '', '+');
$phone_href    = $phone_digits ? 'tel:+' . $phone_digits : '';

$email_address = svic_translate('contact.channels.items.email.value');
$email_href    = filter_var($email_address, FILTER_VALIDATE_EMAIL) ? 'mailto:' . $email_address : '';

$support_form_url = svic_url_with_lang(home_url('/support/'));
$faq_url          = svic_url_with_lang(home_url('/faq/'));
$guides_url       = svic_url_with_lang(home_url('/guides-setup/'));
$compare_url      = svic_url_with_lang(home_url('/compare/'));
$pdp_url          = svic_url_with_lang(home_url('/product/svicloud-10p-plus/'));

$hero_primary_href   = $phone_href ?: $email_href;
$hero_secondary_href = $support_form_url;

$channel_cards = [
    [
        'icon_class' => 'contact-card__icon--phone',
        'label_key' => 'contact.channels.items.phone.label',
        'value_key' => 'contact.channels.items.phone.value',
        'cta_key'   => 'contact.channels.items.phone.cta',
        'href'      => $phone_href,
    ],
    [
        'icon_class' => 'contact-card__icon--mail',
        'label_key' => 'contact.channels.items.email.label',
        'value_key' => 'contact.channels.items.email.value',
        'cta_key'   => 'contact.channels.items.email.cta',
        'href'      => $email_href,
    ],
];

$form_copy = svic_translate_rich('contact.form.copy', [
    'support_url' => esc_url($support_form_url),
]);

$faq_copy = svic_translate_rich('contact.faq.copy', [
    'faq_url' => esc_url($faq_url),
]);
?>

<main class="contact-page">
  <section class="contact-hero">
    <div class="contact-hero__inner">
      <span class="contact-hero__badge"><?php echo svic_translate_html('contact.hero.badge'); ?></span>
      <h1 class="contact-hero__title"><?php echo svic_translate_html('contact.hero.title'); ?></h1>
      <p class="contact-hero__subtitle"><?php echo svic_translate_html('contact.hero.subtitle'); ?></p>
      <div class="contact-hero__actions lumen-action-group">
        <?php if ($hero_primary_href) : ?>
          <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($hero_primary_href); ?>">
            <?php echo svic_translate_html('contact.hero.primary_cta'); ?>
          </a>
        <?php endif; ?>
        <a class="lumen-pill lumen-pill--ghost" href="<?php echo esc_url($hero_secondary_href); ?>">
          <?php echo svic_translate_html('contact.hero.secondary_cta'); ?>
        </a>
      </div>
    </div>
  </section>

  <section class="contact-intent" id="contact-intent">
    <div class="contact-intent__inner">
      <div class="contact-intent__copy">
        <span class="contact-intent__badge"><?php echo svic_translate_html('product.traffic.badge'); ?></span>
        <h2 class="contact-intent__title"><?php echo svic_translate_html('product.traffic.title'); ?></h2>
        <p class="contact-intent__lead"><?php echo svic_translate_html('product.traffic.lead'); ?></p>
        <ul class="contact-intent__list" role="list">
          <li><?php echo svic_translate_html('product.traffic.bullets.shipping'); ?></li>
          <li><?php echo svic_translate_html('product.traffic.bullets.concierge'); ?></li>
          <li><?php echo svic_translate_html('product.traffic.bullets.warranty'); ?></li>
        </ul>
      </div>
      <div class="contact-intent__links lumen-action-group" role="group" aria-label="<?php echo esc_attr__('Primary SVICLOUD actions', 'svicloudtvbox-lumen'); ?>">
        <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($pdp_url); ?>"><?php echo svic_translate_html('frontpage.hero.cta.primary'); ?></a>
        <a class="lumen-pill lumen-pill--ghost" href="<?php echo esc_url($compare_url); ?>"><?php echo svic_translate_html('frontpage.hero.cta.compare'); ?></a>
        <a class="contact-intent__textlink" href="<?php echo esc_url($faq_url); ?>"><?php echo svic_translate_html('product.traffic.links.faq'); ?></a>
        <a class="contact-intent__textlink" href="<?php echo esc_url($support_form_url); ?>"><?php echo svic_translate_html('product.traffic.links.contact'); ?></a>
      </div>
    </div>
  </section>

  <section class="contact-body">
    <div class="contact-body__grid">
      <article class="contact-card contact-card--wide">
        <h2 class="contact-card__title"><?php echo svic_translate_html('contact.hours.title'); ?></h2>
        <p class="contact-card__lead"><?php echo svic_translate_html('contact.hours.copy'); ?></p>
        <p class="contact-card__note"><?php echo svic_translate_html('contact.hours.note'); ?></p>
      </article>

      <?php foreach ($channel_cards as $card) :
          $label = svic_translate_html($card['label_key']);
          $value = svic_translate_html($card['value_key']);
          $cta   = svic_translate_html($card['cta_key']);
          $href  = $card['href'] ?: $hero_secondary_href;
      ?>
        <article class="contact-card contact-card--channel">
          <div class="contact-card__icon <?php echo esc_attr($card['icon_class']); ?>">
            <span aria-hidden="true"></span>
          </div>
          <div class="contact-card__content">
            <?php if ($label) : ?><span class="contact-card__label"><?php echo $label; ?></span><?php endif; ?>
            <span class="contact-card__value"><?php echo $value; ?></span>
            <?php if ($cta) : ?><a class="contact-card__cta" href="<?php echo esc_url($href); ?>"><?php echo $cta; ?></a><?php endif; ?>
          </div>
        </article>
      <?php endforeach; ?>

      <article class="contact-card contact-card--help">
        <div class="contact-card__content">
          <h3 class="contact-card__title"><?php echo svic_translate_html('contact.form.title'); ?></h3>
          <p class="contact-card__lead"><?php echo wp_kses_post($form_copy); ?></p>
          <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($support_form_url); ?>">
            <?php echo svic_translate_html('contact.form.cta'); ?>
          </a>
        </div>
      </article>

      <article class="contact-card contact-card--faq">
        <div class="contact-card__content">
          <h3 class="contact-card__title"><?php echo svic_translate_html('contact.faq.title'); ?></h3>
          <p class="contact-card__lead"><?php echo wp_kses_post($faq_copy); ?></p>
          <a class="lumen-pill lumen-pill--ghost" href="<?php echo esc_url($faq_url); ?>">
            <?php echo svic_translate_html('contact.faq.cta'); ?>
          </a>
        </div>
      </article>
    </div>
  </section>
</main>

<?php
get_footer();
