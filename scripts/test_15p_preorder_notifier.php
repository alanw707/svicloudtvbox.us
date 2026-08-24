<?php
declare(strict_types=1);

define('ABSPATH', __DIR__);

function add_action(string $hook, callable $callback, int $priority = 10, int $accepted_args = 1): void {}
function get_option(string $key) { return $GLOBALS['options'][$key] ?? false; }
function update_option(string $key, $value, bool $autoload = true): void { $GLOBALS['options'][$key] = $value; }
function sanitize_email(string $email): string { return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : ''; }
function wc_get_template_html(string $template, array $args = [], string $template_path = '', string $default_path = ''): string { return '<p>message</p>'; }
function get_template_directory(): string { return __DIR__; }
function wc_get_orders(array $args): array {
    $GLOBALS['captured_order_queries'][] = $args;
    return $GLOBALS['wc_get_orders_pages'][$args['page'] ?? 1] ?? [];
}
function WC() {
    return new class {
        public function mailer() {
            return new class {
                public function send($to, $subject, $message, $headers): bool {
                    $GLOBALS['send_attempts'][] = compact('to', 'subject', 'message', 'headers');
                    return (bool) $GLOBALS['mail_send_result'];
                }
            };
        }
    };
}

class WC_Product {
    public function __construct(private string $name, private string $sku, private string $slug) {}
    public function get_name(): string { return $this->name; }
    public function get_sku(): string { return $this->sku; }
    public function get_slug(): string { return $this->slug; }
}

class WC_Order {
    public array $meta = [];
    public array $notes = [];

    public function __construct(
        private array $items,
        private string $order_number = '9999',
        private string $billing_email = 'customer@example.com'
    ) {}

    public function get_items(): array { return $this->items; }
    public function get_meta(string $key, bool $single = true) { return $this->meta[$key] ?? ''; }
    public function get_billing_email(): string { return $this->billing_email; }
    public function get_billing_first_name(): string { return 'Customer'; }
    public function get_formatted_billing_full_name(): string { return 'Customer Example'; }
    public function get_order_number(): string { return $this->order_number; }
    public function update_meta_data(string $key, $value): void { $this->meta[$key] = $value; }
    public function add_order_note(string $note, bool $is_customer_note = false, bool $added_by_user = false): void { $this->notes[] = $note; }
    public function save(): void {}
}

class Test_Order_Item {
    public function __construct(private string $name, private ?WC_Product $product = null) {}
    public function get_name(): string { return $this->name; }
    public function get_product(): ?WC_Product { return $this->product; }
}

require_once __DIR__ . '/../theme/svicloudtvbox-lumen/inc/class-svic-15p-preorder-notifier.php';

$matching_order = new WC_Order([
    new Test_Order_Item('SVICLOUD 15P order', new WC_Product('SVICLOUD 15P', 'SVIC-15P', 'svicloud-15p')),
]);

$non_matching_order = new WC_Order([
    new Test_Order_Item('SVICLOUD 10P+ TV Box', new WC_Product('SVICLOUD 10P+', 'SVIC-10P', 'svicloud-10p-plus')),
]);

if (!SVIC_15P_Preorder_Notifier::order_contains_15p_preorder($matching_order)) {
    fwrite(STDERR, "Expected 15P order to match\n");
    exit(1);
}

if (SVIC_15P_Preorder_Notifier::order_contains_15p_preorder($non_matching_order)) {
    fwrite(STDERR, "Did not expect 10P order to match 15P launch notifier\n");
    exit(1);
}

echo "15p launch notifier matcher ok\n";

$GLOBALS['options'] = [];
$GLOBALS['captured_order_queries'] = [];
$GLOBALS['send_attempts'] = [];
$GLOBALS['mail_send_result'] = false;
$failed_order = new WC_Order([
    new Test_Order_Item('SVICLOUD 15P order', new WC_Product('SVICLOUD 15P', 'SVIC-15P', 'svicloud-15p')),
]);
$GLOBALS['wc_get_orders_pages'] = [1 => [$failed_order], 2 => []];

SVIC_15P_Preorder_Notifier::send_recent_unsent_once();

if (isset($GLOBALS['options']['svic_15p_preorder_backfill_20260818'])) {
    fwrite(STDERR, "Backfill should not close when mail send fails\n");
    exit(1);
}

if (($GLOBALS['captured_order_queries'][0]['limit'] ?? null) !== 100 || ($GLOBALS['captured_order_queries'][0]['date_created'] ?? '') !== '>=2026-08-18 00:00:00') {
    fwrite(STDERR, "Backfill query should scan launch-date orders in 100-order pages\n");
    exit(1);
}

echo "15p launch failed-send backfill retry ok\n";

$GLOBALS['options'] = [];
$GLOBALS['captured_order_queries'] = [];
$GLOBALS['send_attempts'] = [];
$GLOBALS['mail_send_result'] = true;
$sent_order = new WC_Order([
    new Test_Order_Item('SVICLOUD 15P order', new WC_Product('SVICLOUD 15P', 'SVIC-15P', 'svicloud-15p')),
]);
$GLOBALS['wc_get_orders_pages'] = [1 => [$sent_order], 2 => []];

SVIC_15P_Preorder_Notifier::send_recent_unsent_once();

if (!isset($GLOBALS['options']['svic_15p_preorder_backfill_20260818'])) {
    fwrite(STDERR, "Backfill should close when all matching orders are handled\n");
    exit(1);
}

if (($sent_order->meta['_svic_15p_preorder_email_sent'] ?? '') === '') {
    fwrite(STDERR, "Successful send should mark order meta\n");
    exit(1);
}

echo "15p launch successful backfill close ok\n";

$GLOBALS['send_attempts'] = [];
$manual_order = new WC_Order([
    new Test_Order_Item('SVICLOUD 15P order', new WC_Product('SVICLOUD 15P', 'SVIC-15P', 'svicloud-15p')),
], '1215');

$manual_result = SVIC_15P_Preorder_Notifier::maybe_send($manual_order);

if ($manual_result !== 'manual-marked' || count($GLOBALS['send_attempts']) !== 0) {
    fwrite(STDERR, "Manually sent order #1215 should be marked without sending\n");
    exit(1);
}

echo "15p launch manual duplicate guard ok\n";
