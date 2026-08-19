<?php
/**
 * SVICLOUD 15P preorder customer notice.
 *
 * @package SVICloudTVBoxClassic
 */

declare(strict_types=1);

if (!defined('ABSPATH')) { exit; }

if (!class_exists('SVIC_15P_Preorder_Notifier')) {
    final class SVIC_15P_Preorder_Notifier {
        private const SENT_META_KEY = '_svic_15p_preorder_email_sent';
        private const BACKFILL_OPTION_KEY = 'svic_15p_preorder_backfill_20260818';
        private const BACKFILL_START_DATE = '2026-08-18 00:00:00';
        private const MANUALLY_SENT_ORDER_NUMBERS = ['1215'];

        public static function init(): void {
            add_action('woocommerce_order_status_processing', [self::class, 'maybe_send_for_order_id'], 20, 1);
            add_action('woocommerce_order_status_on-hold', [self::class, 'maybe_send_for_order_id'], 20, 1);
            add_action('woocommerce_order_status_completed', [self::class, 'maybe_send_for_order_id'], 20, 1);
            add_action('admin_init', [self::class, 'send_recent_unsent_once'], 20, 0);
        }

        public static function maybe_send_for_order_id(int $order_id): void {
            if (!function_exists('wc_get_order')) {
                return;
            }

            $order = wc_get_order($order_id);
            if (!$order instanceof WC_Order) {
                return;
            }

            self::maybe_send($order);
        }

        public static function send_recent_unsent_once(): void {
            if (!function_exists('wc_get_orders') || get_option(self::BACKFILL_OPTION_KEY)) {
                return;
            }

            $page = 1;
            $has_incomplete_order = false;

            do {
                $orders = wc_get_orders([
                    'limit'        => 100,
                    'page'         => $page,
                    'paginate'     => false,
                    'orderby'      => 'date',
                    'order'        => 'DESC',
                    'status'       => ['processing', 'on-hold', 'completed'],
                    'date_created' => '>=' . self::BACKFILL_START_DATE,
                ]);

                foreach ($orders as $order) {
                    if (!$order instanceof WC_Order) {
                        continue;
                    }

                    $result = self::maybe_send($order);
                    if (in_array($result, ['failed', 'missing-message', 'missing-recipient'], true)) {
                        $has_incomplete_order = true;
                    }
                }

                $page++;
            } while (count($orders) === 100);

            if (!$has_incomplete_order) {
                update_option(self::BACKFILL_OPTION_KEY, gmdate('c'), false);
            }
        }

        public static function maybe_send(WC_Order $order): string {
            if (!self::order_contains_15p_preorder($order)) {
                return 'not-15p';
            }

            if ((string) $order->get_meta(self::SENT_META_KEY, true) !== '') {
                return 'already-sent';
            }

            if (self::was_manually_sent($order)) {
                $order->update_meta_data(self::SENT_META_KEY, 'manual-titan-2026-08-18');
                $order->add_order_note('SVICLOUD 15P preorder delivery timing email was sent manually from Titan; automation marked it complete.', false, true);
                $order->save();
                return 'manual-marked';
            }

            $recipient = sanitize_email((string) $order->get_billing_email());
            if ($recipient === '') {
                return 'missing-recipient';
            }

            $customer_name = trim((string) $order->get_billing_first_name());
            if ($customer_name === '') {
                $customer_name = trim((string) $order->get_formatted_billing_full_name());
            }
            if ($customer_name === '') {
                $customer_name = 'there';
            }

            $subject = sprintf(
                'SVICLOUD 15P preorder update for order #%s',
                $order->get_order_number()
            );
            $heading = 'Your SVICLOUD 15P preorder is confirmed';

            $message = self::build_email_message($order, $heading, $customer_name);
            if ($message === '') {
                return 'missing-message';
            }

            $mailer = function_exists('WC') && WC() ? WC()->mailer() : null;
            $sent = false;
            if ($mailer && method_exists($mailer, 'send')) {
                $sent = (bool) $mailer->send($recipient, $subject, $message, ['Content-Type: text/html; charset=UTF-8']);
            } else {
                $sent = (bool) wp_mail($recipient, $subject, $message, ['Content-Type: text/html; charset=UTF-8']);
            }

            if (!$sent) {
                return 'failed';
            }

            $order->update_meta_data(self::SENT_META_KEY, gmdate('c'));
            $order->add_order_note('SVICLOUD 15P preorder delivery timing email sent to customer.', false, true);
            $order->save();
            return 'sent';
        }

        private static function was_manually_sent(WC_Order $order): bool {
            return in_array((string) $order->get_order_number(), self::MANUALLY_SENT_ORDER_NUMBERS, true);
        }

        public static function order_contains_15p_preorder(WC_Order $order): bool {
            foreach ($order->get_items() as $item) {
                if (!is_object($item)) {
                    continue;
                }

                $name = method_exists($item, 'get_name') ? (string) $item->get_name() : '';
                $product = method_exists($item, 'get_product') ? $item->get_product() : null;

                $values = [$name];
                if ($product instanceof WC_Product) {
                    $values[] = (string) $product->get_name();
                    $values[] = (string) $product->get_sku();
                    if (method_exists($product, 'get_slug')) {
                        $values[] = (string) $product->get_slug();
                    }
                }

                foreach ($values as $value) {
                    if (preg_match('/(^|[^a-z0-9])15p([^a-z0-9]|$)/i', $value) === 1) {
                        return true;
                    }
                }
            }

            return false;
        }

        private static function build_email_message(WC_Order $order, string $heading, string $customer_name): string {
            if (!function_exists('wc_get_template_html')) {
                return '';
            }

            return wc_get_template_html(
                'emails/customer-svic-15p-preorder.php',
                [
                    'order'         => $order,
                    'email_heading' => $heading,
                    'customer_name' => $customer_name,
                    'sent_to_admin' => false,
                    'plain_text'    => false,
                    'email'         => null,
                ],
                '',
                get_template_directory() . '/woocommerce/'
            );
        }
    }

    SVIC_15P_Preorder_Notifier::init();
}
