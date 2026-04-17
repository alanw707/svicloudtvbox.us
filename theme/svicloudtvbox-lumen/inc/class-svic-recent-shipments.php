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
        private const CACHE_KEY = 'svic_recent_shipments_feed_v3';
        private const CACHE_TTL = 15 * MINUTE_IN_SECONDS;
        private const MAX_ITEMS = 10;
        private const MIN_ITEMS = 3;
        private const QUERY_LIMIT = 40;

        private const ZH_TW_STATE_LABELS = [
            'AL' => '阿拉巴馬州',
            'AK' => '阿拉斯加州',
            'AZ' => '亞利桑那州',
            'AR' => '阿肯色州',
            'CA' => '加州',
            'CO' => '科羅拉多州',
            'CT' => '康乃狄克州',
            'DE' => '德拉瓦州',
            'DC' => '華盛頓特區',
            'FL' => '佛羅里達州',
            'GA' => '喬治亞州',
            'HI' => '夏威夷州',
            'ID' => '愛達荷州',
            'IL' => '伊利諾州',
            'IN' => '印第安納州',
            'IA' => '愛荷華州',
            'KS' => '堪薩斯州',
            'KY' => '肯塔基州',
            'LA' => '路易斯安那州',
            'ME' => '緬因州',
            'MD' => '馬里蘭州',
            'MA' => '麻薩諸塞州',
            'MI' => '密西根州',
            'MN' => '明尼蘇達州',
            'MS' => '密西西比州',
            'MO' => '密蘇里州',
            'MT' => '蒙大拿州',
            'NE' => '內布拉斯加州',
            'NV' => '內華達州',
            'NH' => '新罕布夏州',
            'NJ' => '紐澤西州',
            'NM' => '新墨西哥州',
            'NY' => '紐約州',
            'NC' => '北卡羅來納州',
            'ND' => '北達科他州',
            'OH' => '俄亥俄州',
            'OK' => '奧克拉荷馬州',
            'OR' => '奧勒岡州',
            'PA' => '賓夕法尼亞州',
            'RI' => '羅德島州',
            'SC' => '南卡羅來納州',
            'SD' => '南達科他州',
            'TN' => '田納西州',
            'TX' => '德州',
            'UT' => '猶他州',
            'VT' => '佛蒙特州',
            'VA' => '維吉尼亞州',
            'WA' => '華盛頓州',
            'WV' => '西維吉尼亞州',
            'WI' => '威斯康辛州',
            'WY' => '懷俄明州',
            'AA' => '美軍郵政 AA',
            'AE' => '美軍郵政 AE',
            'AP' => '美軍郵政 AP',
        ];

        private const ZH_CN_STATE_LABELS = [
            'AL' => '阿拉巴马州',
            'AK' => '阿拉斯加州',
            'AZ' => '亚利桑那州',
            'AR' => '阿肯色州',
            'CA' => '加州',
            'CO' => '科罗拉多州',
            'CT' => '康涅狄格州',
            'DE' => '特拉华州',
            'DC' => '华盛顿特区',
            'FL' => '佛罗里达州',
            'GA' => '佐治亚州',
            'HI' => '夏威夷州',
            'ID' => '爱达荷州',
            'IL' => '伊利诺伊州',
            'IN' => '印第安纳州',
            'IA' => '爱荷华州',
            'KS' => '堪萨斯州',
            'KY' => '肯塔基州',
            'LA' => '路易斯安那州',
            'ME' => '缅因州',
            'MD' => '马里兰州',
            'MA' => '马萨诸塞州',
            'MI' => '密歇根州',
            'MN' => '明尼苏达州',
            'MS' => '密西西比州',
            'MO' => '密苏里州',
            'MT' => '蒙大拿州',
            'NE' => '内布拉斯加州',
            'NV' => '内华达州',
            'NH' => '新罕布什尔州',
            'NJ' => '新泽西州',
            'NM' => '新墨西哥州',
            'NY' => '纽约州',
            'NC' => '北卡罗来纳州',
            'ND' => '北达科他州',
            'OH' => '俄亥俄州',
            'OK' => '俄克拉何马州',
            'OR' => '俄勒冈州',
            'PA' => '宾夕法尼亚州',
            'RI' => '罗得岛州',
            'SC' => '南卡罗来纳州',
            'SD' => '南达科他州',
            'TN' => '田纳西州',
            'TX' => '得州',
            'UT' => '犹他州',
            'VT' => '佛蒙特州',
            'VA' => '弗吉尼亚州',
            'WA' => '华盛顿州',
            'WV' => '西弗吉尼亚州',
            'WI' => '威斯康星州',
            'WY' => '怀俄明州',
            'AA' => '美军邮政 AA',
            'AE' => '美军邮政 AE',
            'AP' => '美军邮政 AP',
        ];

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
            $duration    = max(26, min(64, count($items) * 6));
            ?>
            <section class="svic-recent-shipments" aria-label="<?php echo svic_translate_attr('recent_shipments.aria_label'); ?>">
                <div class="svic-recent-shipments__inner">
                    <span class="svic-recent-shipments__label"><?php echo svic_translate_html('recent_shipments.badge'); ?></span>
                    <span class="screen-reader-text"><?php echo svic_translate_html('recent_shipments.disclaimer'); ?></span>
                    <div class="svic-recent-shipments__marquee<?php echo $is_animated ? ' is-animated' : ''; ?>">
                        <?php if ($is_animated) : ?>
                            <div class="svic-recent-shipments__track" style="--svic-recent-shipments-duration: <?php echo esc_attr((string) $duration); ?>s;">
                                <?php self::render_group($items, false); ?>
                                <?php self::render_group($items, true); ?>
                            </div>
                        <?php else : ?>
                            <div class="svic-recent-shipments__track">
                                <?php self::render_group($items, false); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
            <?php
        }

        /**
         * @param array<int,array{city:string,estimated_days:int,service_name:string,shipped_at_ts:int,state:string}> $items
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
         * @return array<int,array{city:string,estimated_days:int,service_name:string,shipped_at_ts:int,state:string}>
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
         * @return array<int,array{city:string,estimated_days:int,service_name:string,shipped_at_ts:int,state:string}>
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

                $dedupe_key = implode('|', [$item['city'], $item['state'], (string) $item['estimated_days'], $item['service_name']]);
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
         * @return array{city:string,estimated_days:int,service_name:string,shipped_at_ts:int,state:string}|null
         */
        private static function build_item(WC_Order $order): ?array
        {
            $shipping_country = strtoupper(trim((string) $order->get_shipping_country()));
            if ($shipping_country !== 'US') {
                return null;
            }

            $state = strtoupper(trim((string) $order->get_shipping_state()));
            if ($state === '') {
                $state = strtoupper(trim((string) $order->get_billing_state()));
            }

            if ($state === '') {
                return null;
            }

            $city = self::normalize_city((string) $order->get_shipping_city());
            if ($city === '') {
                $city = self::normalize_city((string) $order->get_billing_city());
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
                'city'           => $city,
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

        private static function normalize_city(string $city): string
        {
            $city = trim(wp_strip_all_tags($city));
            $city = preg_replace('/\s+/', ' ', $city) ?? $city;

            if ($city !== '' && strtoupper($city) === $city && preg_match('/^[A-Z\s\-\'\.]+$/', $city) === 1) {
                if (function_exists('mb_convert_case') && function_exists('mb_strtolower')) {
                    $city = mb_convert_case(mb_strtolower($city, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
                } else {
                    $city = ucwords(strtolower($city));
                }
            }

            return $city;
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
         * @param array{city:string,estimated_days:int,service_name:string,shipped_at_ts:int,state:string} $item
         */
        private static function format_item_label(array $item): string
        {
            return svic_translate(
                'recent_shipments.item',
                [
                    'location' => self::format_location_label($item),
                    'time'     => self::format_estimated_days((int) $item['estimated_days']),
                ]
            );
        }

        /**
         * @param array{city:string,estimated_days:int,service_name:string,shipped_at_ts:int,state:string} $item
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

        /**
         * @param array{city:string,estimated_days:int,service_name:string,shipped_at_ts:int,state:string} $item
         */
        private static function format_location_label(array $item): string
        {
            $state_label = self::resolve_state_label((string) $item['state']);
            $city_label  = trim((string) ($item['city'] ?? ''));

            if ($city_label === '') {
                return $state_label;
            }

            return svic_translate(
                'recent_shipments.location_city_state',
                [
                    'city'  => $city_label,
                    'state' => $state_label,
                ]
            );
        }

        private static function resolve_state_label(string $state_code): string
        {
            $state_code = strtoupper(trim($state_code));
            if ($state_code === '') {
                return '';
            }

            $locale = function_exists('svic_current_locale') ? strtolower(svic_current_locale()) : strtolower(get_locale());

            if (strpos($locale, 'zh_cn') === 0 || strpos($locale, 'zh-cn') === 0) {
                return self::ZH_CN_STATE_LABELS[$state_code] ?? $state_code;
            }

            if (strpos($locale, 'zh') === 0) {
                return self::ZH_TW_STATE_LABELS[$state_code] ?? $state_code;
            }

            return $state_code;
        }
    }
}
