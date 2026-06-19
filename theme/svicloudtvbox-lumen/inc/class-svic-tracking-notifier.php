<?php
/**
 * Customer tracking-note safety net for WooCommerce Shipping labels.
 *
 * @package SVICloudTVBoxClassic
 */

declare(strict_types=1);

if (!defined('ABSPATH')) { exit; }

if (!class_exists('SVIC_Tracking_Notifier')) {
    final class SVIC_Tracking_Notifier {
        private const SENT_META_KEY = '_svic_tracking_customer_note_sent';

        /** @var array<int, bool> */
        private static array $processing = [];

        public static function init(): void {
            add_action('woocommerce_order_status_completed', [self::class, 'maybe_add_tracking_note_for_order_id'], 30, 1);
            add_action('woocommerce_after_order_object_save', [self::class, 'maybe_add_tracking_note_for_order'], 30, 1);
        }

        public static function maybe_add_tracking_note_for_order_id(int $order_id): void {
            if (!function_exists('wc_get_order')) {
                return;
            }

            $order = wc_get_order($order_id);
            if (!$order instanceof WC_Order) {
                return;
            }

            self::maybe_add_tracking_note_for_order($order);
        }

        public static function maybe_add_tracking_note_for_order(WC_Order $order): void {
            $order_id = (int) $order->get_id();
            if ($order_id <= 0 || isset(self::$processing[$order_id])) {
                return;
            }

            if ($order->get_status() !== 'completed') {
                return;
            }

            if ((string) $order->get_meta(self::SENT_META_KEY, true) !== '') {
                return;
            }

            $tracking_items = self::get_tracking_items($order);
            if (empty($tracking_items)) {
                return;
            }

            if (self::has_customer_tracking_note($order_id, $tracking_items)) {
                $order->update_meta_data(self::SENT_META_KEY, 'existing-note');
                $order->save();
                return;
            }

            self::$processing[$order_id] = true;
            try {
                $note = self::build_customer_note($tracking_items);
                if ($note === '') {
                    return;
                }

                // Customer note emails are handled by WooCommerce/WP Mail SMTP.
                $order->add_order_note($note, true, true);
                $order->update_meta_data(self::SENT_META_KEY, gmdate('c'));
                $order->save();
            } finally {
                unset(self::$processing[$order_id]);
            }
        }

        /**
         * @return array<int, array{number: string, url: string}>
         */
        private static function get_tracking_items(WC_Order $order): array {
            $items = [];

            $shipment_items = $order->get_meta('_wc_shipment_tracking_items', true);
            self::collect_tracking_items($shipment_items, $items);

            $labels = $order->get_meta('wcshipping_labels', true);
            self::collect_tracking_items($labels, $items);

            $unique = [];
            foreach ($items as $item) {
                $number = $item['number'];
                if (isset($unique[$number])) {
                    continue;
                }
                $unique[$number] = $item;
            }

            return array_values($unique);
        }

        /**
         * @param mixed $source
         * @param array<int, array{number: string, url: string}> $items
         */
        private static function collect_tracking_items($source, array &$items): void {
            if (is_string($source)) {
                $decoded = json_decode($source, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    self::collect_tracking_items($decoded, $items);
                } else {
                    self::collect_tracking_number($source, '', $items);
                }
                return;
            }

            if (is_array($source)) {
                $number = '';
                $url    = '';

                foreach (['tracking_number', 'tracking'] as $key) {
                    if (!empty($source[$key]) && is_scalar($source[$key])) {
                        $number = (string) $source[$key];
                        break;
                    }
                }

                foreach (['custom_tracking_link', 'tracking_link'] as $key) {
                    if (!empty($source[$key]) && is_scalar($source[$key])) {
                        $url = (string) $source[$key];
                        break;
                    }
                }

                self::collect_tracking_number($number, $url, $items);

                foreach ($source as $value) {
                    self::collect_tracking_items($value, $items);
                }
            }
        }

        /**
         * @param array<int, array{number: string, url: string}> $items
         */
        private static function collect_tracking_number(string $number, string $url, array &$items): void {
            $number = trim($number);
            if ($number === '' || !preg_match('/^(92|93|94|95|96)\d{18,22}$/', $number)) {
                return;
            }

            $items[] = [
                'number' => $number,
                'url'    => $url !== '' ? $url : 'https://tools.usps.com/go/TrackConfirmAction?tLabels=' . rawurlencode($number),
            ];
        }

        /**
         * @param array<int, array{number: string, url: string}> $tracking_items
         */
        private static function has_customer_tracking_note(int $order_id, array $tracking_items): bool {
            $notes = wc_get_order_notes([
                'order_id' => $order_id,
                'type'     => 'customer',
                'limit'    => 20,
            ]);

            foreach ($notes as $note) {
                $content = isset($note->content) ? (string) $note->content : '';
                foreach ($tracking_items as $item) {
                    if (strpos($content, $item['number']) !== false) {
                        return true;
                    }
                }
            }

            return false;
        }

        /**
         * @param array<int, array{number: string, url: string}> $tracking_items
         */
        private static function build_customer_note(array $tracking_items): string {
            $lines = [];
            foreach ($tracking_items as $item) {
                $lines[] = sprintf('USPS tracking: %s %s', $item['number'], $item['url']);
            }

            return implode("\n", $lines);
        }
    }

    SVIC_Tracking_Notifier::init();
}
