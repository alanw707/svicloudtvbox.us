<?php
/**
 * Custom order tracking results template.
 */

defined('ABSPATH') || exit;

$notes = $order->get_customer_order_notes();
$order_number_value = '#' . $order->get_order_number();
$order_date_value = wc_format_datetime($order->get_date_created());
$order_status_value = wc_get_order_status_name($order->get_status());
$order_total_value = wp_strip_all_tags($order->get_formatted_order_total());
$payment_method_value = $order->get_payment_method_title() ?: '';

$summary_order_meta = svic_translate('order_tracking.summary.placed', [
    'order_date' => '<span class="lumen-order-summary__meta-value">' . esc_html($order_date_value) . '</span>',
]);

$summary_payment_meta = '';
if ($payment_method_value !== '') {
    $summary_payment_meta = svic_translate('order_tracking.summary.total', [
        'order_total'     => '<span class="lumen-order-summary__meta-value">' . esc_html($order_total_value) . '</span>',
        'payment_method'  => '<span class="lumen-order-summary__meta-value">' . esc_html($payment_method_value) . '</span>',
        'dot'             => ' · ',
    ]);
}

$billing_name = $order->get_formatted_billing_full_name();
$order_details_html = '';
if (has_action('woocommerce_view_order')) {
    ob_start();
    do_action('woocommerce_view_order', $order->get_id());
    $order_details_html = trim(ob_get_clean());
}
?>
<section class="lumen-order-status" aria-labelledby="lumen-order-status-title">
  <header class="lumen-order-status__header">
    <div class="lumen-order-status__headline">
      <h2 id="lumen-order-status-title" class="lumen-order-status__title"><?php echo svic_translate_html('order_tracking.status_heading'); ?></h2>
      <?php if ($billing_name) : ?>
        <p class="lumen-order-status__customer">
          <?php echo svic_translate_html('order_tracking.customer_label'); ?>
          <span class="lumen-order-status__customer-name"><?php echo esc_html($billing_name); ?></span>
        </p>
      <?php endif; ?>
    </div>

  </header>

  <section class="lumen-order-status__overview">
    <article class="lumen-order-summary">
      <div class="lumen-order-summary__layout">
        <div class="lumen-order-summary__segment lumen-order-summary__segment--details">
          <span class="lumen-order-summary__eyebrow"><?php echo svic_translate_html('order_tracking.summary.order_label'); ?></span>
          <div class="lumen-order-summary__body">
            <span class="lumen-order-summary__value"><?php echo esc_html($order_number_value); ?></span>
            <div class="lumen-order-summary__meta-group">
              <span class="lumen-order-summary__meta"><?php echo wp_kses_post($summary_order_meta); ?></span>
              <?php if ($summary_payment_meta) : ?>
                <span class="lumen-order-summary__meta"><?php echo wp_kses_post($summary_payment_meta); ?></span>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <div class="lumen-order-summary__divider" aria-hidden="true"></div>
        <div class="lumen-order-summary__segment">
          <span class="lumen-order-summary__eyebrow"><?php echo svic_translate_html('order_tracking.summary.shipping_label'); ?></span>
          <div class="lumen-order-summary__body">
            <span class="lumen-order-summary__status" data-status="<?php echo esc_attr($order->get_status()); ?>"><?php echo esc_html($order_status_value); ?></span>
            <span class="lumen-order-summary__meta lumen-order-summary__meta--accent"><?php echo svic_translate_html('order_tracking.summary.shipping_note'); ?></span>
          </div>
        </div>
      </div>

      <?php if ($order_details_html !== '') : ?>
        <div class="lumen-order-summary__details">
          <header class="lumen-order-summary__details-header">
            <h3 class="lumen-order-summary__details-title"><?php echo svic_translate_html('order_tracking.summary.details_heading'); ?></h3>
            <p class="lumen-order-summary__details-note"><?php echo svic_translate_html('order_tracking.summary.details_note'); ?></p>
          </header>
          <div class="lumen-order-summary__details-content">
            <?php echo $order_details_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
          </div>
        </div>
      <?php endif; ?>
    </article>
  </section>

  <?php if ($notes) : ?>
    <section class="lumen-order-updates" aria-labelledby="lumen-order-updates-title">
      <h3 id="lumen-order-updates-title" class="lumen-order-updates__title"><?php echo svic_translate_html('order_tracking.updates_heading'); ?></h3>
      <ol class="lumen-order-updates__list">
        <?php foreach ($notes as $note) : ?>
          <li class="lumen-order-updates__item">
            <span class="lumen-order-updates__dot" aria-hidden="true"></span>
            <div class="lumen-order-updates__body">
              <p class="lumen-order-updates__timestamp">
                <?php echo esc_html(date_i18n(__('F j, Y \a\t g:ia', 'svicloudtvbox-lumen'), strtotime($note->comment_date))); ?>
              </p>
              <div class="lumen-order-updates__content">
                <?php echo wpautop(wp_kses_post($note->comment_content)); ?>
              </div>
            </div>
          </li>
        <?php endforeach; ?>
      </ol>
    </section>
  <?php else : ?>
    <p class="lumen-order-updates__empty"><?php echo svic_translate_html('order_tracking.no_updates'); ?></p>
  <?php endif; ?>

</section>
