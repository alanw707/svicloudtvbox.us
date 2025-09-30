<?php
/**
 * Cart Page Template
 *
 * @package SVICLOUDTVBOX\Lumen
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

do_action('woocommerce_before_cart');

$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop');
$shop_url = svic_url_with_lang($shop_url);
$cart = WC()->cart;
$cart_items = [];
$has_items = false;

if (is_object($cart)) {
    if (method_exists($cart, 'get_cart')) {
        $cart_items = (array) $cart->get_cart();
    }

    if (method_exists($cart, 'get_cart_contents_count')) {
        $has_items = (int) $cart->get_cart_contents_count() > 0;
    } else {
        $has_items = !empty($cart_items);
    }
}
?>
<section class="lumen-cart <?php echo !$has_items ? 'lumen-cart--empty' : ''; ?>" data-cart-page>
    <header class="lumen-cart__header">
        <h1 class="lumen-cart__title"><?php echo svic_translate_html('cart_page.title'); ?></h1>
        <?php if ($has_items): ?>
            <a class="lumen-cart__back" href="<?php echo esc_url($shop_url); ?>">
                <span class="lumen-cart__back-icon" aria-hidden="true">&#8592;</span>
                <span><?php echo svic_translate_html('cart_page.continue_shopping'); ?></span>
            </a>
        <?php endif; ?>
    </header>

    <form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
        <?php if ($has_items): ?>
            <?php do_action('woocommerce_before_cart_table'); ?>
        <?php endif; ?>

        <div class="lumen-cart__layout">
            <div class="lumen-cart__items">
                <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents lumen-cart-table" cellspacing="0">
                    <?php if ($has_items): ?>
                        <thead>
                            <tr>
                                <th class="product-thumbnail"><?php echo svic_translate_html('cart_page.table.thumbnail'); ?></th>
                                <th class="product-name"><?php echo svic_translate_html('cart_page.table.product'); ?></th>
                                <th class="product-price"><?php echo svic_translate_html('cart_page.table.price'); ?></th>
                                <th class="product-quantity"><?php echo svic_translate_html('cart_page.table.quantity'); ?></th>
                                <th class="product-subtotal"><?php echo svic_translate_html('cart_page.table.total'); ?></th>
                                <th class="product-remove"><span class="screen-reader-text"><?php echo svic_translate_html('cart_page.remove'); ?></span></th>
                            </tr>
                        </thead>
                    <?php endif; ?>
                    <tbody>
                        <?php do_action('woocommerce_before_cart_contents'); ?>
                        <?php if ($has_items): ?>
                            <?php foreach ($cart_items as $cart_item_key => $cart_item): ?>
                                <?php
                                $product = apply_filters('woocommerce_cart_item_product', $cart_item['data'] ?? null, $cart_item, $cart_item_key);
                                $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'] ?? 0, $cart_item, $cart_item_key);

                                if (!$product || !$product->exists() || ($cart_item['quantity'] ?? 0) <= 0) {
                                    continue;
                                }

                                $product_permalink = apply_filters('woocommerce_cart_item_permalink', $product->is_visible() ? $product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                                $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $product->get_image('woocommerce_thumbnail', [
                                    'class' => 'lumen-cart-product__image',
                                    'alt'   => $product->get_name(),
                                ]), $cart_item, $cart_item_key);

                                if (!$thumbnail || stripos($thumbnail, 'woocommerce-placeholder') !== false) {
                                    $fallback_url = get_template_directory_uri() . '/assets/images/svicloud-hero-product.png';
                                    $thumbnail    = '<img class="lumen-cart-product__image" src="' . esc_url($fallback_url) . '" alt="' . esc_attr($product->get_name()) . '" />';
                                }

                                $product_name = $product_permalink ? sprintf('<a href="%s" class="lumen-cart-product__link">%s</a>', esc_url($product_permalink), esc_html($product->get_name())) : esc_html($product->get_name());
                                $product_name = apply_filters('woocommerce_cart_item_name', $product_name, $cart_item, $cart_item_key);

                                $product_price = apply_filters('woocommerce_cart_item_price', $cart->get_product_price($product), $cart_item, $cart_item_key);
                                $product_subtotal = apply_filters('woocommerce_cart_item_subtotal', $cart->get_product_subtotal($product, (int) $cart_item['quantity']), $cart_item, $cart_item_key);
                                ?>
                                <tr class="woocommerce-cart-form__cart-item lumen-cart-product" data-product-id="<?php echo esc_attr((string) $product_id); ?>" data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>">
                                    <td class="product-thumbnail" data-title="<?php echo esc_attr(svic_translate('cart_page.table.thumbnail')); ?>">
                                        <div class="lumen-cart-product__thumb">
                                            <?php echo wp_kses_post($thumbnail); ?>
                                        </div>
                                    </td>
                                    <td class="product-name" data-title="<?php echo esc_attr(svic_translate('cart_page.table.product')); ?>">
                                        <div class="lumen-cart-product__details">
                                            <div class="lumen-cart-product__title"><?php echo wp_kses_post($product_name); ?></div>
                                            <ul class="lumen-cart-product__badges" role="list">
                                                <li class="lumen-cart-product__badge lumen-cart-product__badge--authorized"><?php echo svic_translate_html('cart_page.meta.authorized'); ?></li>
                                                <li class="lumen-cart-product__badge lumen-cart-product__badge--shipping"><?php echo svic_translate_html('cart_page.meta.shipping'); ?></li>
                                                <li class="lumen-cart-product__badge lumen-cart-product__badge--warranty"><?php echo svic_translate_html('cart_page.meta.warranty'); ?></li>
                                            </ul>
                                            <?php echo wc_get_formatted_cart_item_data($cart_item); ?>
                                            <?php if ($product->backorders_require_notification() && $product->is_on_backorder((int) $cart_item['quantity'])): ?>
                                                <p class="lumen-cart-product__notice"><?php echo esc_html($product->get_backorder_notification()); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="product-price" data-title="<?php echo esc_attr(svic_translate('cart_page.table.price')); ?>">
                                        <span class="lumen-cart-product__price"><?php echo wp_kses_post($product_price); ?></span>
                                    </td>
                                    <td class="product-quantity" data-title="<?php echo esc_attr(svic_translate('cart_page.table.quantity')); ?>">
                                        <div class="lumen-cart-qty">
                                            <?php
                                            if ($product->is_sold_individually()) {
                                                echo apply_filters('woocommerce_cart_item_quantity', '<span class="lumen-cart-qty__single">1</span>', $cart_item_key, $cart_item);
                                            } else {
                                                echo apply_filters(
                                                    'woocommerce_cart_item_quantity',
                                                    woocommerce_quantity_input([
                                                        'input_name'   => "cart[{$cart_item_key}][qty]",
                                                        'input_value'  => (int) $cart_item['quantity'],
                                                        'max_value'    => $product->get_max_purchase_quantity(),
                                                        'min_value'    => '0',
                                                        'product_name' => $product->get_name(),
                                                        'classes'      => ['input-text', 'qty', 'text', 'lumen-cart-qty__input'],
                                                    ], $product, false),
                                                    $cart_item_key,
                                                    $cart_item
                                                );
                                            }
                                            ?>
                                        </div>
                                    </td>
                                    <td class="product-subtotal" data-title="<?php echo esc_attr(svic_translate('cart_page.table.total')); ?>">
                                        <span class="lumen-cart-product__subtotal"><?php echo wp_kses_post($product_subtotal); ?></span>
                                    </td>
                                    <td class="product-remove" data-title="<?php echo esc_attr(svic_translate('cart_page.remove')); ?>">
                                        <?php
                                        echo apply_filters(
                                            'woocommerce_cart_item_remove_link',
                                            sprintf(
                                                '<a href="%s" class="lumen-cart-remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
                                                esc_url(wc_get_cart_remove_url($cart_item_key)),
                                                esc_attr(sprintf(svic_translate('cart_page.remove_aria'), $product->get_name())),
                                                esc_attr((string) $product_id),
                                                esc_attr($product->get_sku())
                                            ),
                                            $cart_item_key
                                        );
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="lumen-cart-empty">
                                    <div class="lumen-cart-empty__content">
                                        <svg class="lumen-cart-empty__icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                        <p class="lumen-cart-empty__message"><?php echo svic_translate_html('cart_page.empty'); ?></p>
                                        <a class="lumen-cart-empty__link" href="<?php echo esc_url($shop_url); ?>"><?php echo svic_translate_html('cart_page.empty_cta'); ?></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php do_action('woocommerce_cart_contents'); ?>
                    </tbody>
                </table>

                <?php if ($has_items): ?>
                    <div class="lumen-cart__actions">
                        <button type="submit" class="button lumen-cart-update" name="update_cart" value="<?php echo esc_attr(svic_translate('cart_page.update')); ?>">
                            <?php echo svic_translate_html('cart_page.update'); ?>
                        </button>
                        <?php do_action('woocommerce_cart_actions'); ?>
                        <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
                    </div>
                <?php endif; ?>
            </div>

            <aside class="lumen-cart__summary" aria-labelledby="lumen-cart-summary-title">
                <div class="lumen-cart-summary__card">
                    <h2 id="lumen-cart-summary-title" class="lumen-cart-summary__title"><?php echo svic_translate_html('cart_page.summary.title'); ?></h2>
                    <p class="lumen-cart-summary__intro"><?php echo svic_translate_html('cart_page.summary.intro'); ?></p>
                    <div class="lumen-cart-summary__notices" aria-live="polite">
                        <?php if (function_exists('woocommerce_output_all_notices')) {
                            woocommerce_output_all_notices();
                        } else {
                            wc_print_notices();
                        } ?>
                    </div>

                    <?php if ($has_items): ?>
                        <?php if (wc_coupons_enabled()): ?>
                            <div class="lumen-cart-summary__coupon">
                                <label class="lumen-cart-summary__coupon-label" for="coupon_code"><?php echo svic_translate_html('cart_page.coupon.label'); ?></label>
                                <div class="lumen-cart-summary__coupon-controls">
                                    <input type="text" name="coupon_code" class="input-text lumen-cart-summary__coupon-input" id="coupon_code" value="" placeholder="<?php echo esc_attr(svic_translate('cart_page.coupon.placeholder')); ?>" aria-describedby="coupon_help" />
                                    <button type="submit" class="button lumen-cart-summary__coupon-button" name="apply_coupon" value="<?php echo esc_attr(svic_translate('cart_page.coupon.apply')); ?>">
                                        <?php echo svic_translate_html('cart_page.coupon.apply'); ?>
                                    </button>
                                </div>
                                <?php do_action('woocommerce_cart_coupon'); ?>
                                <p id="coupon_help" class="lumen-cart-summary__coupon-hint"><?php echo svic_translate_html('cart_page.coupon.hint'); ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="lumen-cart-summary__totals">
                            <?php woocommerce_cart_totals(); ?>
                        </div>

                        <p class="lumen-cart-summary__reassurance"><?php echo svic_translate_html('cart_page.summary.reassurance'); ?></p>

                        <ul class="lumen-cart-summary__benefits">
                            <li>
                                <span class="lumen-cart-summary__benefit-title"><?php echo svic_translate_html('cart_page.benefits.shipping.title'); ?></span>
                                <span class="lumen-cart-summary__benefit-copy"><?php echo svic_translate_html('cart_page.benefits.shipping.copy'); ?></span>
                            </li>
                            <li>
                                <span class="lumen-cart-summary__benefit-title"><?php echo svic_translate_html('cart_page.benefits.warranty.title'); ?></span>
                                <span class="lumen-cart-summary__benefit-copy"><?php echo svic_translate_html('cart_page.benefits.warranty.copy'); ?></span>
                            </li>
                            <li>
                                <span class="lumen-cart-summary__benefit-title"><?php echo svic_translate_html('cart_page.benefits.concierge.title'); ?></span>
                                <span class="lumen-cart-summary__benefit-copy"><?php echo svic_translate_html('cart_page.benefits.concierge.copy'); ?></span>
                            </li>
                        </ul>
                    <?php else: ?>
                        <p class="lumen-cart-summary__empty"><?php echo svic_translate_html('cart_page.summary.empty'); ?></p>
                    <?php endif; ?>
                </div>
            </aside>
        </div>

        <?php do_action('woocommerce_after_cart_table'); ?>

        <?php if ($has_items): ?>
            <div class="lumen-cart__cross-sells">
                <?php do_action('woocommerce_before_cart_collaterals'); ?>
                <?php woocommerce_cross_sell_display(); ?>
            </div>
        <?php endif; ?>
    </form>
</section>

<?php do_action('woocommerce_after_cart'); ?>
