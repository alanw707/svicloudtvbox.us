<?php
/**
 * Checkout Form (Lumen override)
 *
 * @package WooCommerce\Templates
 * @version 9.8.0
 */

if (!defined('ABSPATH')) {
    exit;
}

do_action('woocommerce_before_checkout_form', $checkout);

if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
    echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
    return;
}

$has_translator = function_exists('svic_translate_html');
$summary_benefits = [];
if ($has_translator) {
    $summary_benefits = array_filter([
        svic_translate_html('checkout_page.benefits.shipping'),
        svic_translate_html('checkout_page.benefits.warranty'),
        svic_translate_html('checkout_page.benefits.concierge'),
    ]);
}
?>
<section class="lumen-checkout" data-checkout-page>
    <header class="lumen-checkout__header">
        <?php if ($has_translator) : ?>
            <span class="lumen-checkout__badge"><?php echo svic_translate_html('checkout_page.badge'); ?></span>
            <h1 class="lumen-checkout__title"><?php echo svic_translate_html('checkout_page.title'); ?></h1>
            <p class="lumen-checkout__subtitle"><?php echo svic_translate_html('checkout_page.subtitle'); ?></p>
        <?php else : ?>
            <h1 class="lumen-checkout__title"><?php esc_html_e('Checkout', 'woocommerce'); ?></h1>
        <?php endif; ?>
    </header>

    <form name="checkout" method="post" class="checkout woocommerce-checkout lumen-checkout__form" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__('Checkout', 'woocommerce'); ?>">
        <div class="lumen-checkout__layout">
            <div class="lumen-checkout__primary">
                <?php if ($checkout->get_checkout_fields()) : ?>
                    <?php do_action('woocommerce_checkout_before_customer_details'); ?>

                    <div class="col2-set lumen-checkout__details" id="customer_details">
                        <div class="col-1 lumen-checkout__panel lumen-checkout__panel--billing">
                            <div class="lumen-checkout__panel-inner">
                                <?php do_action('woocommerce_checkout_billing'); ?>
                            </div>
                        </div>

                        <div class="col-2 lumen-checkout__panel lumen-checkout__panel--shipping">
                            <div class="lumen-checkout__panel-inner">
                                <?php do_action('woocommerce_checkout_shipping'); ?>
                            </div>
                        </div>
                    </div>

                    <?php do_action('woocommerce_checkout_after_customer_details'); ?>
                <?php endif; ?>

                <div class="lumen-checkout__panel lumen-checkout__panel--payment">
                    <div class="lumen-checkout__panel-inner">
                        <?php if ($has_translator) : ?>
                            <h2 class="lumen-checkout__panel-title"><?php echo svic_translate_html('checkout_page.payment.title'); ?></h2>
                            <p class="lumen-checkout__panel-subtitle"><?php echo svic_translate_html('checkout_page.payment.intro'); ?></p>
                            <p class="screen-reader-text" id="lumen-checkout-accepted-cards"><?php echo svic_translate_html('checkout_page.payment.cards_label'); ?></p>
                        <?php else : ?>
                            <h2 class="lumen-checkout__panel-title"><?php esc_html_e('Payment', 'woocommerce'); ?></h2>
                            <p class="lumen-checkout__panel-subtitle"><?php esc_html_e('Choose a saved card or enter a new payment method, then place your order with secure checkout.', 'woocommerce'); ?></p>
                            <p class="screen-reader-text" id="lumen-checkout-accepted-cards"><?php esc_html_e('Accepted cards: Visa, Mastercard, American Express, and Discover.', 'svicloudtvbox-lumen'); ?></p>
                        <?php endif; ?>

                        <div class="lumen-checkout__payment-group" aria-describedby="lumen-checkout-accepted-cards">
                            <?php woocommerce_checkout_payment(); ?>
                        </div>
                    </div>
                </div>

                <?php if ($has_translator) : ?>
                    <div class="lumen-checkout__assurance" aria-live="polite">
                        <h2 class="lumen-checkout__assurance-title"><?php echo svic_translate_html('checkout_page.assurance.title'); ?></h2>
                        <p class="lumen-checkout__assurance-copy"><?php echo svic_translate_html('checkout_page.assurance.copy'); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <aside class="lumen-checkout__summary" aria-labelledby="lumen-checkout-summary-title">
                <div class="lumen-checkout-summary__card">
                    <?php if ($has_translator) : ?>
                        <h2 id="lumen-checkout-summary-title" class="lumen-checkout-summary__title"><?php echo svic_translate_html('checkout_page.summary.title'); ?></h2>
                        <p class="lumen-checkout-summary__intro"><?php echo svic_translate_html('checkout_page.summary.intro'); ?></p>
                    <?php else : ?>
                        <h2 id="lumen-checkout-summary-title" class="lumen-checkout-summary__title"><?php esc_html_e('Your order', 'woocommerce'); ?></h2>
                    <?php endif; ?>

                    <?php if ($summary_benefits) : ?>
                        <ul class="lumen-checkout-summary__benefits">
                            <?php foreach ($summary_benefits as $benefit) : ?>
                                <li class="lumen-checkout-summary__benefit"><?php echo $benefit; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <?php if ($has_translator) : ?>
                        <p class="lumen-checkout-summary__reassurance"><?php echo svic_translate_html('checkout_page.summary.reassurance'); ?></p>
                        <div class="lumen-checkout-summary__links lumen-action-group">
                            <a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url(svic_url_with_lang(home_url('/faq/'))); ?>" data-svic-event="svic_cta_click" data-svic-location="checkout_summary" data-svic-label="faq_before_order"><?php echo svic_translate_html('product.traffic.links.faq'); ?></a>
                            <a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url(svic_url_with_lang(home_url('/contact/'))); ?>" data-svic-event="svic_cta_click" data-svic-location="checkout_summary" data-svic-label="contact_concierge"><?php echo svic_translate_html('product.traffic.links.contact'); ?></a>
                        </div>
                    <?php endif; ?>

                    <?php do_action('woocommerce_checkout_before_order_review_heading'); ?>

                    <h3 id="order_review_heading" class="lumen-checkout-summary__heading"><?php echo $has_translator ? svic_translate_html('checkout_page.summary.order_heading') : esc_html__('Your order', 'woocommerce'); ?></h3>

                    <?php do_action('woocommerce_checkout_before_order_review'); ?>

                    <div id="order_review" class="woocommerce-checkout-review-order lumen-checkout-summary__order">
                        <?php woocommerce_order_review(); ?>
                    </div>

                    <?php do_action('woocommerce_checkout_after_order_review'); ?>
                </div>
            </aside>
        </div>
    </form>
</section>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>
