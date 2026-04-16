<?php
/**
 * Recent U.S. shipments strip powered by WooCommerce Shipping metadata.
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('SVIC_Recent_Shipments')) {
    class SVIC_Recent_Shipments
    {
        private const CACHE_KEY = 'svic_recent_shipments_feed_v1';
        private const CACHE_TTL = 15 * MINUTE_IN_SECONDS;
        private const MAX_ITEMS = 10;
        private const MIN_ITEMS = 3;
        private const QUERY_LIMIT = 40;

        public static function bootstrap(): void
        {
            add_action('woocommerce_order_status_changed', [self::class, 'flush_cache'], 10, 4);
            add_action('woocommerce_checkout_order_processed', [self::class, 'flush_cache'], 10, 1);
        }

        public static function flush_cache(...$args): void
        {
            delete_transient(self::CACHE_KEY);
        }

        public static function render(): void
        {
            if (!self::should_render()) {
                return;
            }

            $items = self::get_feed_items();
            if (count($items) < self::MIN_ITEMS) {
                return;
            }

            $is_animated = count($items) >= 4;
            $duration    = max(28, min(72, count($items) * 7));
            ?>
            <section class="svic-recent-shipments" aria-label="<?php echo svic_translate_attr('recent_shipments.aria_label'); ?>">
                <div class="svic-recent-shipments__inner">
                    <div class="svic-recent-shipments__header">
                        <span class="svic-recent-shipments__badge"><?php echo svic_translate_html('recent_shipments.badge'); ?></span>
                        <p class="svic-recent-shipments__disclaimer"><?php echo svic_translate_html('recent_shipments.disclaimer'); ?></p>
                    </div>

                    <div class="svic-recent-shipments__marquee<?php echo $is_animated ? ' is-animated' : ''; ?>">
                        <?php if ($is_animated) : ?>
                            <div class="svic-recent-shipments__track" style="--svic-recent-shipments-duration: <?php echo esc_attr((string) $duration); ?>s;">
                                <?php self::render_group($items, false); ?>
                                <?php self::render_group($items, true); ?>
                            </div>
                        <?php else : ?>
                            <?php self::render_group($items, false); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
            <?php
        }

        /**
         * @param array<int,array{estimated_days:int,service_name:string,shipped_at_ts:int,state:string}> $items
         */
        private static function render_group(array $items, bool $is_duplicate): void
        {
            ?>
            <ul class="svic-recent-shipments__group"<?php echo $is_duplicate ? ' aria-hidden="true"' : ''; ?>>
                <?php foreach ($items as $item) : ?>
                    <?php
                    $label = self::format_item_label($item);
                    $title = self::format_item_title($item);
                    ?>
                    <li class="svic-recent-shipments__item"<?php echo $title !== '' ? ' title="' . esc_attr($title) . '"' : ''; ?>>
                        <span class="svic-recent-shipments__item-text"><?php echo esc_html($label); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php
        }

        private static function should_render(): bool
        {
            if (!defined('SVIC_RECENT_SHIPMENTS_ENABLED') || !SVIC_RECENT_SHIPMENTS_ENABLED) {
                return false;
            }

            if (is_admin() || wp_doing_ajax()) {
                return false;
            }

            if (!function_exists('wc_get_orders')) {
                return false;
            }

            if (function_exists('is_cart') && is_cart()) {
                return false;
            }

            if (function_exists('is_checkout') && is_checkout()) {
                return false;
            }

            if (function_exists('is_account_page') && is_account_page()) {
                return false;
            }

            if (function_exists('svic_is_order_tracking_request') && svic_is_order_tracking_request()) {
                return false;
            }

            return (bool) apply_filters('svic_recent_shipments_should_render', true);
        }

        /**
         * @return array<int,array{estimated_days:int,service_name:string,shipped_at_ts:int,state:string}>
         */
        private static function get_feed_items(): array
        {
            $cached = get_transient(self::CACHE_KEY);
            if (is_array($cached)) {
                return $cached;
            }

            $items = self::build_feed_items();
            set_transient(self::CACHE_KEY, $items, self::CACHE_TTL);

            return $items;
        }

        /**
         * @return array<int,array{estimated_days:int,service_name:string,shipped_at_ts:int,state:string}>
         */
        private static function build_feed_items(): array
        {
            $items = [];
            $seen  = [];

            $orders = wc_get_orders([
                'type'    => 'shop_order',
                'status'  => ['completed'],
                'limit'   => (int) apply_filters('svic_recent_shipments_query_limit', self::QUERY_LIMIT),
                'orderby' => 'date',
                'order'   => 'DESC',
                'return'  => 'objects',
            ]);

            if (!is_array($orders) || $orders === []) {
                return [];
            }

            foreach ($orders as $order) {
                if (!($order instanceof WC_Order)) {
                    continue;
                }

                $item = self::build_item($order);
                if ($item === null) {
                    continue;
                }

                $dedupe_key = implode('|', [$item['state'], (string) $item['estimated_days'], $item['service_name']]);
                if (isset($seen[$dedupe_key])) {
                    continue;
                }

                $seen[$dedupe_key] = true;
                $items[]           = $item;
            }

            usort(
                $items,
                static function (array $left, array $right): int {
                    return $right['shipped_at_ts'] <=> $left['shipped_at_ts'];
                }
            );

            return array_slice($items, 0, self::MAX_ITEMS);
        }

        /**
         * @return array{estimated_days:int,service_name:string,shipped_at_ts:int,state:string}|null
         */
        private static function build_item(WC_Order $order): ?array
        {
            $shipping_country = strtoupper(trim((string) $order->get_shipping_country()));
            if ($shipping_country !== 'US') {
                return null;
            }

            $state = strtoupper(trim((string) $order->get_shipping_state()));
            if ($state === '') {
                return null;
            }

            $selected_rates = self::normalize_meta_value($order->get_meta('_wcshipping_selected_rates', true));
            if (!is_array($selected_rates) || $selected_rates === []) {
                return null;
            }

            $estimated_days = self::extract_estimated_days($selected_rates);
            if ($estimated_days === null || $estimated_days < 1 || $estimated_days > 10) {
                return null;
            }

            $labels         = self::normalize_meta_value($order->get_meta('wcshipping_labels', true));
            $shipment_dates = self::normalize_meta_value($order->get_meta('_wcshipping_shipment_dates', true));
            $shipped_at_ts  = self::extract_shipped_at_timestamp($shipment_dates, $labels, $order);

            if ($shipped_at_ts === null) {
                return null;
            }

            return [
                'state'          => $state,
                'estimated_days' => $estimated_days,
                'service_name'   => self::extract_service_name($labels, $selected_rates),
                'shipped_at_ts'  => $shipped_at_ts,
            ];
        }

        /**
         * @param mixed $value
         * @return mixed
         */
        private static function normalize_meta_value($value)
        {
            if (is_string($value)) {
                $value = maybe_unserialize($value);
            }

            return $value;
        }

        private static function extract_estimated_days(array $selected_rates): ?int
        {
            foreach ($selected_rates as $shipment) {
                if (!is_array($shipment)) {
                    continue;
                }

                $rate = isset($shipment['rate']) && is_array($shipment['rate']) ? $shipment['rate'] : null;
                if ($rate === null) {
                    continue;
                }

                $days = $rate['delivery_days'] ?? null;
                if (!is_numeric($days)) {
                    continue;
                }

                return (int) max(1, round((float) $days));
            }

            return null;
        }

        /**
         * @param mixed $labels
         */
        private static function extract_service_name($labels, array $selected_rates): string
        {
            if (is_array($labels)) {
                foreach ($labels as $label) {
                    if (!is_array($label)) {
                        continue;
                    }

                    $service_name = trim((string) ($label['service_name'] ?? ''));
                    if ($service_name !== '') {
                        return $service_name;
                    }
                }
            }

            foreach ($selected_rates as $shipment) {
                if (!is_array($shipment)) {
                    continue;
                }

                $rate = isset($shipment['rate']) && is_array($shipment['rate']) ? $shipment['rate'] : null;
                if ($rate === null) {
                    continue;
                }

                $title = trim((string) ($rate['title'] ?? ''));
                if ($title !== '') {
                    return $title;
                }
            }

            return '';
        }

        /**
         * @param mixed $shipment_dates
         * @param mixed $labels
         */
        private static function extract_shipped_at_timestamp($shipment_dates, $labels, WC_Order $order): ?int
        {
            if (is_array($shipment_dates)) {
                foreach ($shipment_dates as $shipment) {
                    if (!is_array($shipment)) {
                        continue;
                    }

                    $shipping_date = self::parse_timestamp($shipment['shipping_date'] ?? null);
                    if ($shipping_date !== null) {
                        return $shipping_date;
                    }
                }
            }

            if (is_array($labels)) {
                foreach ($labels as $label) {
                    if (!is_array($label)) {
                        continue;
                    }

                    foreach (['created_date', 'created', 'label_cached'] as $key) {
                        $timestamp = self::parse_timestamp($label[$key] ?? null);
                        if ($timestamp !== null) {
                            return $timestamp;
                        }
                    }
                }
            }

            $completed_at = $order->get_date_completed();
            if ($completed_at instanceof WC_DateTime) {
                return $completed_at->getTimestamp();
            }

            $created_at = $order->get_date_created();
            if ($created_at instanceof WC_DateTime) {
                return $created_at->getTimestamp();
            }

            return null;
        }

        /**
         * @param mixed $value
         */
        private static function parse_timestamp($value): ?int
        {
            if ($value instanceof DateTimeInterface) {
                return $value->getTimestamp();
            }

            if (is_numeric($value)) {
                $timestamp = (int) round((float) $value);
                if ($timestamp > 20000000000) {
                    $timestamp = (int) floor($timestamp / 1000);
                }

                return $timestamp > 946684800 ? $timestamp : null;
            }

            if (!is_string($value)) {
                return null;
            }

            $trimmed = trim($value);
            if ($trimmed === '') {
                return null;
            }

            $timestamp = strtotime($trimmed);
            if ($timestamp === false || $timestamp <= 0) {
                return null;
            }

            return $timestamp;
        }

        /**
         * @param array{estimated_days:int,service_name:string,shipped_at_ts:int,state:string} $item
         */
        private static function format_item_label(array $item): string
        {
            return svic_translate(
                'recent_shipments.item',
                [
                    'state' => self::resolve_state_label((string) $item['state']),
                    'time'  => self::format_estimated_days((int) $item['estimated_days']),
                ]
            );
        }

        /**
         * @param array{estimated_days:int,service_name:string,shipped_at_ts:int,state:string} $item
         */
        private static function format_item_title(array $item): string
        {
            $service_name = trim((string) ($item['service_name'] ?? ''));
            if ($service_name === '') {
                return '';
            }

            return svic_translate('recent_shipments.item_title', ['service' => $service_name]);
        }

        private static function format_estimated_days(int $days): string
        {
            if ($days === 1) {
                return svic_translate('recent_shipments.estimated_day_singular');
            }

            return svic_translate('recent_shipments.estimated_day_plural', ['count' => (string) $days]);
        }

        private static function resolve_state_label(string $state_code): string
        {
            $state_code = strtoupper(trim($state_code));
            if ($state_code === '') {
                return '';
            }

            $states = function_exists('WC') && WC()->countries && isset(WC()->countries->states['US']) && is_array(WC()->countries->states['US'])
                ? WC()->countries->states['US']
                : [];

            if (isset($states[$state_code]) && is_string($states[$state_code]) && $states[$state_code] !== '') {
                return $states[$state_code];
            }

            return $state_code;
        }
    }
}
