<?php
/**
 * Support Form Page
 *
 * Template Name: Support Form
 */

$status = isset($_GET['support_status']) ? sanitize_text_field(wp_unslash($_GET['support_status'])) : '';
$current_locale = svic_current_locale();
$translator     = SVIC_Translator::instance();

$registry         = $translator->registry($current_locale);
$fallbackRegistry = $translator->registry('en_US');

$device_options = $registry['support']['form']['device_options'] ?? ($fallbackRegistry['support']['form']['device_options'] ?? []);
$issue_options  = $registry['support']['form']['issue_options'] ?? ($fallbackRegistry['support']['form']['issue_options'] ?? []);

$contact_url = svic_url_with_lang(home_url('/contact/'));
$faq_url     = svic_url_with_lang(home_url('/faq/'));
$pdp_url     = svic_url_with_lang(home_url('/product/svicloud-10p-plus/'));
$compare_url = svic_url_with_lang(home_url('/compare/'));

$form_action = esc_url(admin_url('admin-post.php'));
$locale_query = svic_language_query_value($current_locale);

get_header();
?>

<main class="support-page">
  <section class="support-hero">
    <div class="support-hero__inner">
      <span class="support-hero__badge"><?php echo svic_translate_html('support.hero.badge'); ?></span>
      <h1 class="support-hero__title"><?php echo svic_translate_html('support.hero.title'); ?></h1>
      <p class="support-hero__subtitle"><?php echo svic_translate_html('support.hero.subtitle'); ?></p>
    </div>
  </section>

  <section class="support-intent" id="support-intent">
    <div class="support-intent__inner">
      <div class="support-intent__copy">
        <span class="support-intent__badge"><?php echo svic_translate_html('product.traffic.badge'); ?></span>
        <h2 class="support-intent__title"><?php echo svic_translate_html('product.traffic.title'); ?></h2>
        <p class="support-intent__lead"><?php echo svic_translate_html('product.traffic.lead'); ?></p>
        <ul class="support-intent__list" role="list">
          <li><?php echo svic_translate_html('product.traffic.bullets.shipping'); ?></li>
          <li><?php echo svic_translate_html('product.traffic.bullets.concierge'); ?></li>
          <li><?php echo svic_translate_html('product.traffic.bullets.warranty'); ?></li>
        </ul>
      </div>
      <div class="support-intent__links lumen-action-group" role="group" aria-label="<?php echo esc_attr__('Primary SVICLOUD actions', 'svicloudtvbox-lumen'); ?>">
        <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($pdp_url); ?>"><?php echo svic_translate_html('frontpage.hero.cta.primary'); ?></a>
        <a class="lumen-pill lumen-pill--ghost" href="<?php echo esc_url($compare_url); ?>"><?php echo svic_translate_html('frontpage.hero.cta.compare'); ?></a>
        <a class="support-intent__textlink" href="<?php echo esc_url($faq_url); ?>"><?php echo svic_translate_html('product.traffic.links.faq'); ?></a>
        <a class="support-intent__textlink" href="<?php echo esc_url($contact_url); ?>"><?php echo svic_translate_html('product.traffic.links.contact'); ?></a>
      </div>
    </div>
  </section>

  <section class="support-body">
    <?php if ($status === 'success') : ?>
      <div class="support-status support-status--success">
        <h2 class="support-status__title"><?php echo svic_translate_html('support.success.title'); ?></h2>
        <p class="support-status__body"><?php echo svic_translate_html('support.success.body'); ?></p>
      </div>
    <?php elseif ($status === 'error') : ?>
      <div class="support-status support-status--error">
        <p class="support-status__body"><?php echo svic_translate_html('support.error'); ?></p>
      </div>
    <?php endif; ?>

    <div class="support-grid">
      <section class="support-form">
        <h2 class="support-form__title"><?php echo svic_translate_html('support.form.title'); ?></h2>
        <p class="support-form__description"><?php echo svic_translate_html('support.form.description'); ?></p>
        <form class="support-form__fields" method="post" action="<?php echo $form_action; ?>" novalidate>
          <input type="hidden" name="action" value="svic_support_form" />
          <input type="hidden" name="svic_locale" value="<?php echo esc_attr($current_locale); ?>" />
          <?php wp_nonce_field('svic_support_form', 'svic_support_nonce'); ?>

          <div class="support-form__row">
            <label for="support-name"><?php echo svic_translate_html('support.form.fields.name.label'); ?></label>
            <input type="text" id="support-name" name="support_name" required aria-required="true" placeholder="<?php echo esc_attr(svic_translate('support.form.fields.name.placeholder')); ?>" />
          </div>

          <div class="support-form__row">
            <label for="support-email"><?php echo svic_translate_html('support.form.fields.email.label'); ?></label>
            <input type="email" id="support-email" name="support_email" required aria-required="true" placeholder="<?php echo esc_attr(svic_translate('support.form.fields.email.placeholder')); ?>" />
          </div>

          <div class="support-form__row">
            <label for="support-phone"><?php echo svic_translate_html('support.form.fields.phone.label'); ?></label>
            <input type="tel" id="support-phone" name="support_phone" placeholder="<?php echo esc_attr(svic_translate('support.form.fields.phone.placeholder')); ?>" />
          </div>

          <div class="support-form__row">
            <label for="support-order"><?php echo svic_translate_html('support.form.fields.order.label'); ?></label>
            <input type="text" id="support-order" name="support_order" placeholder="<?php echo esc_attr(svic_translate('support.form.fields.order.placeholder')); ?>" />
          </div>

          <div class="support-form__row">
            <label for="support-device"><?php echo svic_translate_html('support.form.fields.device.label'); ?></label>
            <select id="support-device" name="support_device">
              <option value="">
                <?php echo esc_html(svic_translate('support.form.fields.device.placeholder')); ?>
              </option>
              <?php foreach ($device_options as $value => $label) : ?>
                <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="support-form__row">
            <label for="support-issue"><?php echo svic_translate_html('support.form.fields.issue.label'); ?></label>
            <select id="support-issue" name="support_issue" required aria-required="true">
              <option value="">
                <?php echo esc_html(svic_translate('support.form.fields.issue.placeholder')); ?>
              </option>
              <?php foreach ($issue_options as $value => $label) : ?>
                <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="support-form__row support-form__row--wide">
            <label for="support-message"><?php echo svic_translate_html('support.form.fields.message.label'); ?></label>
            <textarea id="support-message" name="support_message" rows="6" required aria-required="true" placeholder="<?php echo esc_attr(svic_translate('support.form.fields.message.placeholder')); ?>"></textarea>
          </div>

          <p class="support-form__required-note"><?php echo svic_translate_html('support.form.required_note'); ?></p>

          <button type="submit" class="lumen-pill lumen-pill--primary support-form__submit">
            <?php echo svic_translate_html('support.form.submit'); ?>
          </button>
        </form>
      </section>

      <aside class="support-help">
        <article class="support-help__card">
          <h3 class="support-help__title"><?php echo svic_translate_html('support.help.contact.title'); ?></h3>
          <p class="support-help__copy"><?php echo svic_translate_html('support.help.contact.copy'); ?></p>
          <a class="lumen-pill lumen-pill--ghost" href="<?php echo esc_url($contact_url); ?>">
            <?php echo svic_translate_html('support.help.contact.cta'); ?>
          </a>
        </article>
        <article class="support-help__card">
          <h3 class="support-help__title"><?php echo svic_translate_html('support.help.faq.title'); ?></h3>
          <p class="support-help__copy"><?php echo svic_translate_html('support.help.faq.copy'); ?></p>
          <a class="lumen-pill lumen-pill--ghost" href="<?php echo esc_url($faq_url); ?>">
            <?php echo svic_translate_html('support.help.faq.cta'); ?>
          </a>
        </article>
      </aside>
    </div>
  </section>
</main>

<?php
get_footer();
