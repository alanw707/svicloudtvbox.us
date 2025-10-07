<?php
/**
 * Custom order tracking form template.
 */

defined('ABSPATH') || exit;

$order_id_value = isset($_REQUEST['orderid']) ? wp_unslash((string) $_REQUEST['orderid']) : '';
$order_email_value = isset($_REQUEST['order_email']) ? wp_unslash((string) $_REQUEST['order_email']) : '';

$help_url = svic_url_with_lang(home_url('/contact/'));
?>

<section class="lumen-order-tracking" aria-labelledby="lumen-order-tracking-title">
  <header class="lumen-order-tracking__header">
    <div class="lumen-order-tracking__visual" aria-hidden="true">
      <span class="lumen-order-tracking__glow"></span>
      <img class="lumen-order-tracking__icon" src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-box.svg' ); ?>" alt="" />
    </div>
    <div class="lumen-order-tracking__heading">
      <span class="lumen-order-tracking__badge"><?php echo svic_translate_html('order_tracking.badge'); ?></span>
      <h1 id="lumen-order-tracking-title" class="lumen-order-tracking__title"><?php echo svic_translate_html('order_tracking.title'); ?></h1>
      <p class="lumen-order-tracking__intro"><?php echo svic_translate_html('order_tracking.subtitle'); ?></p>
    </div>
  </header>

  <?php if (function_exists('wc_print_notices')) : ?>
    <div class="lumen-order-tracking__notices" aria-live="polite">
      <?php wc_print_notices(); ?>
    </div>
  <?php endif; ?>

  <form action="<?php echo esc_url(get_permalink()); ?>" method="post" class="lumen-order-tracking__form" novalidate>
    <?php do_action('woocommerce_order_tracking_form_start'); ?>

    <div class="lumen-order-tracking__fields">
      <div class="lumen-order-tracking__field" data-field-icon="order">
        <label class="lumen-order-tracking__label" for="orderid"><?php echo svic_translate_html('order_tracking.order_id_label'); ?></label>
        <div class="lumen-order-tracking__control">
          <span class="lumen-order-tracking__icon" aria-hidden="true"></span>
          <input
            class="lumen-order-tracking__input"
            type="text"
            name="orderid"
            id="orderid"
            value="<?php echo esc_attr($order_id_value); ?>"
            placeholder="<?php echo svic_translate_attr('order_tracking.order_id_placeholder'); ?>"
            autocomplete="off"
            required
          />
        </div>
      </div>

      <div class="lumen-order-tracking__field" data-field-icon="email">
        <label class="lumen-order-tracking__label" for="order_email"><?php echo svic_translate_html('order_tracking.email_label'); ?></label>
        <div class="lumen-order-tracking__control">
          <span class="lumen-order-tracking__icon" aria-hidden="true"></span>
          <input
            class="lumen-order-tracking__input"
            type="email"
            name="order_email"
            id="order_email"
            value="<?php echo esc_attr($order_email_value); ?>"
            placeholder="<?php echo svic_translate_attr('order_tracking.email_placeholder'); ?>"
            autocomplete="email"
            required
          />
        </div>
      </div>
    </div>

    <?php do_action('woocommerce_order_tracking_form'); ?>

    <div class="lumen-order-tracking__actions">
      <button type="submit" class="btn btn-primary" name="track" value="<?php echo svic_translate_attr('order_tracking.submit_value'); ?>">
        <?php echo svic_translate_html('order_tracking.submit'); ?>
        <span class="lumen-order-tracking__spinner" aria-hidden="true"></span>
      </button>
      <?php wp_nonce_field('woocommerce-order_tracking', 'woocommerce-order-tracking-nonce'); ?>
    </div>

    <?php do_action('woocommerce_order_tracking_form_end'); ?>
  </form>

  <footer class="lumen-order-tracking__footer">
    <div class="lumen-order-tracking__support">
      <span class="lumen-order-tracking__support-icon" aria-hidden="true"></span>
      <div class="lumen-order-tracking__support-copy">
        <p class="lumen-order-tracking__help"><?php echo svic_translate_html('order_tracking.help_text'); ?></p>
        <div class="lumen-order-tracking__support-links">
          <a class="lumen-order-tracking__help-link lumen-order-tracking__help-link--cta" href="<?php echo esc_url($help_url); ?>" aria-describedby="lumen-order-tracking-support-hours">
            <span class="lumen-order-tracking__help-link-label"><?php echo svic_translate_html('order_tracking.help_link'); ?></span>
          </a>
          <span class="lumen-order-tracking__support-hours" id="lumen-order-tracking-support-hours" role="text"><?php echo svic_translate_html('order_tracking.support_hours'); ?></span>
        </div>
      </div>
    </div>
  </footer>
</section>
