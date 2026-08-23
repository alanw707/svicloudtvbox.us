<?php
/**
 * Customer SVICLOUD 15P preorder timing notice.
 *
 * @package SVICloudTVBoxClassic
 */

defined('ABSPATH') || exit;

/**
 * @var WC_Order $order
 * @var string   $email_heading
 * @var string   $customer_name
 */

do_action('woocommerce_email_header', $email_heading, $email ?? null);
?>

<p><?php echo esc_html(sprintf('Hi %s,', $customer_name)); ?></p>

<p>
    Thank you for your SVICLOUD 15P preorder. Your order has been received and reserved.
</p>

<p>
    SVICLOUD 15P is currently in preorder for the U.S. launch. The official U.S. launch window is now <strong>1 to 2 weeks</strong>.
</p>

<p>
    We will email your tracking information as soon as your order ships.
</p>

<p>
    <strong>Order:</strong> #<?php echo esc_html($order->get_order_number()); ?><br>
    <strong>Product:</strong> SVICLOUD 15P preorder<br>
    <strong>Release window:</strong> 1 to 2 weeks
</p>

<p>
    If you have any questions, reply to this email or contact us at
    <a href="mailto:support@svicloudtvbox.us">support@svicloudtvbox.us</a>.
</p>

<p>
    Best,<br>
    SVICLOUD TV Box Support
</p>

<?php
do_action('woocommerce_email_footer', $email ?? null);
