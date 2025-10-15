<?php
/**
 * Checkout coupon form override.
 *
 * @package SVICloudTVBoxClassic
 */

defined( 'ABSPATH' ) || exit;

if ( ! wc_coupons_enabled() ) { // @codingStandardsIgnoreLine.
	return;
}

$coupon_code = '';
if ( isset( $_POST['coupon_code'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$coupon_code = wc_format_coupon_code( wp_unslash( (string) $_POST['coupon_code'] ) );
}

$theme_button_class = wc_wp_theme_get_element_class_name( 'button' );
$button_class_attr  = $theme_button_class ? ' ' . $theme_button_class : '';

$applied_coupons = [];
if ( function_exists( 'WC' ) ) {
	$cart = WC()->cart;
	if ( $cart ) {
		$applied_coupons = $cart->get_applied_coupons();
	}
}

?>
<div class="lumen-checkout-coupon" data-lumen-coupon-original="true">
	<form class="checkout_coupon woocommerce-form-coupon lumen-checkout-coupon__form" method="post" id="woocommerce-checkout-form-coupon">
		<p class="lumen-checkout-coupon__intro"><?php echo svic_translate_html( 'checkout_page.coupon.intro' ); ?></p>

		<div class="lumen-checkout-coupon__controls">
			<p class="form-row form-row-first">
				<label for="coupon_code" class="screen-reader-text"><?php echo svic_translate_html( 'checkout_page.coupon.label' ); ?></label>
				<input type="text" name="coupon_code" class="input-text" placeholder="<?php echo svic_translate_attr( 'checkout_page.coupon.placeholder' ); ?>" id="coupon_code" value="<?php echo esc_attr( $coupon_code ); ?>" />
			</p>

			<p class="form-row form-row-last">
				<button type="submit" class="button<?php echo esc_attr( $button_class_attr ); ?>" name="apply_coupon" value="<?php echo svic_translate_attr( 'checkout_page.coupon.apply' ); ?>">
					<?php echo svic_translate_html( 'checkout_page.coupon.apply' ); ?>
				</button>
			</p>
		</div>

		<p class="lumen-checkout-coupon__hint"><?php echo svic_translate_html( 'checkout_page.coupon.hint' ); ?></p>

		<?php if ( ! empty( $applied_coupons ) ) : ?>
			<div class="lumen-checkout-coupon__applied" role="status" aria-live="polite">
				<span class="lumen-checkout-coupon__applied-label"><?php echo svic_translate_html( 'checkout_page.coupon.applied_label' ); ?></span>
				<div class="lumen-checkout-coupon__applied-list">
					<?php foreach ( $applied_coupons as $code ) : ?>
						<?php
						$formatted_code = wc_format_coupon_code( $code );
						$remove_url     = esc_url( wc_get_cart_remove_coupon_url( $code ) );
						?>
						<span class="lumen-checkout-coupon__chip" data-coupon-code="<?php echo esc_attr( $formatted_code ); ?>">
							<span class="lumen-checkout-coupon__chip-code"><?php echo esc_html( $formatted_code ); ?></span>
							<a class="lumen-checkout-coupon__chip-remove" href="<?php echo $remove_url; ?>" data-coupon="<?php echo esc_attr( $formatted_code ); ?>">
								<?php echo svic_translate_html( 'checkout_page.coupon.remove' ); ?>
							</a>
						</span>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

		<div class="clear"></div>
	</form>
</div>
