<?php
/**
 * Lumen-styled Thank You (order received) template
 *
 * Overrides WooCommerce checkout/thankyou.php
 */

defined('ABSPATH') || exit;

// Ensure we have an order context when possible
$order = isset($order) ? $order : (function_exists('wc_get_order') ? wc_get_order(get_query_var('order-received')) : null);
$gtag_debug = isset($_GET['svic_gtag_debug']) && $_GET['svic_gtag_debug'] === '1';

if ($gtag_debug) {
    nocache_headers();
    echo '<!-- SVIC gtag debug enabled -->';
}

// Helper to localize links with active language
$home_url         = function_exists('svic_url_with_lang') ? svic_url_with_lang(home_url('/')) : home_url('/');
$order_tracking   = function_exists('svic_url_with_lang') ? svic_url_with_lang(home_url('/order-tracking/')) : home_url('/order-tracking/');
$shop_url         = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : '';
$compare_url      = function_exists('svic_url_with_lang') ? svic_url_with_lang(home_url('/compare/')) : home_url('/compare/');
$guides_url       = function_exists('svic_url_with_lang') ? svic_url_with_lang(home_url('/guides-setup/')) : home_url('/guides-setup/');
$contact_url      = function_exists('svic_url_with_lang') ? svic_url_with_lang(home_url('/contact/')) : home_url('/contact/');
if (!$shop_url) { $shop_url = $home_url; }

if ($order instanceof WC_Order && $order->has_status('failed')) : ?>
  <section class="lumen-order-status" aria-labelledby="lumen-order-status-title">
    <div class="lumen-order-status__headline">
      <h2 id="lumen-order-status-title" class="lumen-order-status__title"><?php echo svic_translate_html('order_thankyou.failed.title'); ?></h2>
      <p class="lumen-order-status__meta"><?php echo svic_translate_html('order_thankyou.failed.copy'); ?></p>
    </div>

    <p class="lumen-order-status__meta">
      <a class="button" href="<?php echo esc_url($order->get_checkout_payment_url()); ?>"><?php echo svic_translate_html('order_thankyou.failed.pay_now'); ?></a>
      <?php if (is_user_logged_in()) : ?>
        <a class="button" href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>"><?php echo svic_translate_html('order_thankyou.failed.my_account'); ?></a>
      <?php endif; ?>
    </p>
  </section>
<?php elseif ($order instanceof WC_Order) :
  // Summary values
  $order_number_value   = '#' . $order->get_order_number();
  $order_date_value     = wc_format_datetime($order->get_date_created());
  $order_total_value    = wp_strip_all_tags($order->get_formatted_order_total());
  $payment_method_value = $order->get_payment_method_title() ?: '';
  $billing_name         = $order->get_formatted_billing_full_name();
  $billing_email        = $order->get_billing_email();
  // $gtag_debug set above for cache control + debug visibility.

  // Capture default Woo thankyou output so we can place it inside our layout
  ob_start();
  do_action('woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id());
  do_action('woocommerce_thankyou', $order->get_id());
  $thankyou_inner = trim(ob_get_clean());
?>
  <section class="lumen-order-status lumen-order-thankyou" aria-labelledby="lumen-order-status-title">
    <header class="lumen-order-status__header">
      <div class="lumen-order-status__headline">
        <h2 id="lumen-order-status-title" class="lumen-order-status__title"><?php echo svic_translate_html('order_thankyou.title'); ?></h2>
        <?php if ($billing_name || $billing_email) : ?>
          <p class="lumen-order-status__customer">
            <?php
            $name_markup  = $billing_name ? '<span class="lumen-order-status__customer-name">' . esc_html($billing_name) . '</span>' : '';
            $email_markup = $billing_email ? '<span>' . esc_html($billing_email) . '</span>' : '';
            $dot_sep      = ($name_markup && $email_markup) ? '<span aria-hidden="true"> · </span>' : '';
            echo svic_translate(
              'order_thankyou.email_intro',
              [ 'name' => $name_markup, 'email' => $email_markup, 'dot' => $dot_sep ]
            ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            ?>
          </p>
        <?php endif; ?>
      </div>
    </header>

    <section class="lumen-order-status__overview">
      <article class="lumen-order-summary">
        <div class="lumen-order-summary__layout">
          <div class="lumen-order-summary__segment lumen-order-summary__segment--details">
            <span class="lumen-order-summary__eyebrow"><?php echo svic_translate_html('order_thankyou.summary.order_label'); ?></span>
            <div class="lumen-order-summary__body">
              <span class="lumen-order-summary__value" data-order-number><?php echo esc_html($order_number_value); ?></span>
              <div class="lumen-order-summary__meta-group">
                <span class="lumen-order-summary__meta"><?php echo svic_translate('order_thankyou.summary.placed_on', [ 'order_date' => '<strong>' . esc_html($order_date_value) . '</strong>' ]); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                <?php $pm_dot = $payment_method_value ? '<span aria-hidden="true"> · </span>' : ''; ?>
                <span class="lumen-order-summary__meta"><?php echo svic_translate('order_thankyou.summary.total', [ 'order_total' => '<strong>' . esc_html($order_total_value) . '</strong>', 'payment_method' => $payment_method_value ? '<span>' . esc_html($payment_method_value) . '</span>' : '', 'dot' => $pm_dot ]); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
              </div>
            </div>
          </div>
          <div class="lumen-order-summary__divider" aria-hidden="true"></div>
          <div class="lumen-order-summary__segment">
            <span class="lumen-order-summary__eyebrow"><?php echo svic_translate_html('order_thankyou.summary.next_label'); ?></span>
            <div class="lumen-order-summary__body">
              <p class="lumen-order-summary__meta lumen-order-summary__meta--accent"><?php echo svic_translate_html('order_thankyou.summary.tracking_note'); ?></p>
              <div class="lumen-order-thankyou__actions">
                <a class="lumen-order-thankyou__btn lumen-order-thankyou__btn--primary" href="<?php echo esc_url($order_tracking); ?>" data-svic-event="svic_cta_click" data-svic-location="thankyou_actions" data-svic-label="track_order"><?php echo svic_translate_html('order_thankyou.cta.track'); ?></a>
                <a class="lumen-order-thankyou__btn" href="<?php echo esc_url($shop_url); ?>" data-svic-event="svic_cta_click" data-svic-location="thankyou_actions" data-svic-label="continue_shopping"><?php echo svic_translate_html('order_thankyou.cta.continue'); ?></a>
              </div>
              <div class="lumen-order-summary__meta-group" style="margin-top:14px;">
                <span class="lumen-order-summary__eyebrow"><?php echo svic_translate_html('order_thankyou.review.badge'); ?></span>
                <p class="lumen-order-summary__meta"><?php echo svic_translate_html('order_thankyou.review.copy'); ?></p>
              </div>
            </div>
          </div>
        </div>

        <section class="lumen-order-thankyou-next">
          <header class="lumen-order-thankyou-next__header">
            <span class="lumen-order-summary__eyebrow"><?php echo svic_translate_html('order_thankyou.next.badge'); ?></span>
            <h3 class="lumen-order-summary__details-title"><?php echo svic_translate_html('order_thankyou.next.title'); ?></h3>
            <p class="lumen-order-summary__details-note"><?php echo svic_translate_html('order_thankyou.next.copy'); ?></p>
          </header>
          <div class="lumen-order-thankyou-next__grid">
            <article class="lumen-order-thankyou-card">
              <h4 class="lumen-order-thankyou-card__title"><?php echo svic_translate_html('order_thankyou.next.cards.setup.title'); ?></h4>
              <p class="lumen-order-thankyou-card__copy"><?php echo svic_translate_html('order_thankyou.next.cards.setup.copy'); ?></p>
              <a class="lumen-order-thankyou-card__link" href="<?php echo esc_url($guides_url); ?>" data-svic-event="svic_cta_click" data-svic-location="thankyou_next" data-svic-label="open_setup_guide"><?php echo svic_translate_html('order_thankyou.next.cards.setup.cta'); ?></a>
            </article>
            <article class="lumen-order-thankyou-card">
              <h4 class="lumen-order-thankyou-card__title"><?php echo svic_translate_html('order_thankyou.next.cards.share.title'); ?></h4>
              <p class="lumen-order-thankyou-card__copy"><?php echo svic_translate_html('order_thankyou.next.cards.share.copy'); ?></p>
              <a class="lumen-order-thankyou-card__link" href="<?php echo esc_url($compare_url); ?>" data-svic-event="svic_cta_click" data-svic-location="thankyou_next" data-svic-label="share_compare_page"><?php echo svic_translate_html('order_thankyou.next.cards.share.cta'); ?></a>
            </article>
            <article class="lumen-order-thankyou-card">
              <h4 class="lumen-order-thankyou-card__title"><?php echo svic_translate_html('order_thankyou.next.cards.support.title'); ?></h4>
              <p class="lumen-order-thankyou-card__copy"><?php echo svic_translate_html('order_thankyou.next.cards.support.copy'); ?></p>
              <a class="lumen-order-thankyou-card__link" href="<?php echo esc_url($contact_url); ?>" data-svic-event="svic_cta_click" data-svic-location="thankyou_next" data-svic-label="contact_support_after_order"><?php echo svic_translate_html('order_thankyou.next.cards.support.cta'); ?></a>
            </article>
          </div>
        </section>

        <?php if ($thankyou_inner !== '') : ?>
          <div class="lumen-order-summary__details" id="order-details">
            <header class="lumen-order-summary__details-header">
              <h3 class="lumen-order-summary__details-title"><?php echo svic_translate_html('order_thankyou.summary.details_heading'); ?></h3>
              <p class="lumen-order-summary__details-note"><?php echo svic_translate_html('order_thankyou.summary.details_note'); ?></p>
            </header>
            <div class="lumen-order-summary__details-content">
              <?php echo $thankyou_inner; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </div>
          </div>
        <?php endif; ?>
      </article>
    </section>
  </section>

  <?php svic_render_google_customer_reviews_optin($order); ?>

  <script>
  (function() {
      if (!Array.isArray(window.dataLayer)) {
          window.dataLayer = [];
      }
      var payload = {
          event: 'svic_order_received',
          svic_location: 'thankyou',
          svic_label: '<?php echo esc_js($order_number_value); ?>',
          svic_order_number: '<?php echo esc_js($order->get_order_number()); ?>',
          svic_order_total: '<?php echo esc_js($order_total_value); ?>'
      };
      window.dataLayer.push(payload);
      if (typeof window.gtag === 'function') {
          window.gtag('event', 'svic_order_received', {
              event_category: 'conversion_path',
              event_label: payload.svic_label,
              location: payload.svic_location,
              order_number: payload.svic_order_number,
              order_total: payload.svic_order_total
          });
      }
      try {
          window.dispatchEvent(new CustomEvent('svic:track', { detail: payload }));
      } catch (e) {
          // no-op
      }
  })();
  </script>

  <?php if ($gtag_debug) : ?>
    <script>
    (function () {
        var gtagReady = (typeof gtag === 'function');
        console.info('[SVIC] gtag present on thank-you:', gtagReady);
        if (!gtagReady) {
            console.warn('[SVIC] gtag not found; Google Ads conversion will not fire.');
        }
    })();
    </script>
  <?php endif; ?>

<?php else : ?>
  <section class="lumen-order-status lumen-order-thankyou">
    <div class="lumen-order-status__headline">
      <h2 class="lumen-order-status__title"><?php echo svic_translate_html('order_thankyou.title'); ?></h2>
    </div>
    <p class="lumen-order-status__meta"><?php echo svic_translate_html('order_thankyou.summary.tracking_note'); ?></p>
  </section>
<?php endif; ?>
