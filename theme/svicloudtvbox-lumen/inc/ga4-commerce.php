<?php
/** Standard commerce events; never infer a successful cart add from a click. */
if (!defined('ABSPATH')) { exit; }

function svic_ga4_commerce_item(WC_Product $product, float $quantity = 1, ?float $line_total = null): array
{
    $price = $line_total !== null && $quantity > 0
        ? $line_total / $quantity
        : (float) wc_get_price_excluding_tax($product);
    return [
        'item_id' => (string) ($product->get_sku() ?: $product->get_id()),
        'item_name' => $product->get_name(),
        'price' => $price,
        'quantity' => $quantity,
    ];
}

function svic_queue_ga4_cart_add($cart_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data): void
{
    if (!svic_is_tracking_enabled() || !WC()->session) { return; }
    $product = wc_get_product($variation_id ?: $product_id);
    if (!$product instanceof WC_Product) { return; }
    $events = (array) WC()->session->get('svic_ga4_pending_adds', []);
    $events[] = svic_ga4_commerce_item($product, (float) $quantity);
    WC()->session->set('svic_ga4_pending_adds', $events);
}
add_action('woocommerce_add_to_cart', 'svic_queue_ga4_cart_add', 10, 6);

function svic_render_ga4_commerce_events(): void
{
    if (is_admin() || !function_exists('WC') || !svic_is_tracking_enabled()) { return; }
    $events = [];
    $currency = get_woocommerce_currency();
    $payload = static function (array $items) use ($currency): array {
        $value = 0.0;
        foreach ($items as $item) { $value += $item['price'] * $item['quantity']; }
        return ['currency' => $currency, 'value' => round($value, 2), 'items' => $items];
    };
    if (WC()->session) {
        foreach ((array) WC()->session->get('svic_ga4_pending_adds', []) as $item) {
            $events[] = ['add_to_cart', $payload([$item])];
        }
        // Consume only on a rendered response. AJAX additions are delivered on
        // the next page; fragments alone must not duplicate the event.
        WC()->session->set('svic_ga4_pending_adds', []);
    }
    if (is_product()) {
        $product = wc_get_product(get_queried_object_id());
        if ($product instanceof WC_Product) {
            $events[] = ['view_item', $payload([svic_ga4_commerce_item($product)])];
        }
    } elseif ((is_cart() || is_checkout()) && !is_order_received_page() && WC()->cart) {
        $items = [];
        foreach (WC()->cart->get_cart() as $line) {
            if (!($line['data'] ?? null) instanceof WC_Product || $line['quantity'] <= 0) { continue; }
            $items[] = svic_ga4_commerce_item($line['data'], (float) $line['quantity'], (float) $line['line_total']);
        }
        if ($items) { $events[] = [is_cart() ? 'view_cart' : 'begin_checkout', $payload($items)]; }
    }
    if (!$events) { return; }
    ?>
    <script id="svic-ga4-commerce-events">
    (function () {
        window.dataLayer = window.dataLayer || [];
        window.gtag = window.gtag || function () { window.dataLayer.push(arguments); };
        var events = <?php echo wp_json_encode($events, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        events.forEach(function (entry) { window.gtag('event', entry[0], entry[1]); });
    })();
    </script>
    <?php
}
add_action('wp_footer', 'svic_render_ga4_commerce_events', 25);
