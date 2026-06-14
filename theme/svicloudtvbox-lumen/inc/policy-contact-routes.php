<?php
/** Local fallback routes for canonical contact and policy pages when DB pages are absent. */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('svic_policy_contact_routes')) {
    function svic_policy_contact_routes(): array {
        return [
            'contact' => ['title' => 'Contact SVICLOUD TV Box US', 'copy' => 'For setup, app, remote, product-choice, warranty, authenticity, or order support, contact the official US support team.', 'primary' => '702-389-3416'],
            'shipping-policy' => ['title' => 'Shipping policy', 'copy' => 'Use this canonical page for current SVICLOUD TV Box US shipping guidance. Contact support before relying on any third-party shipping promise.', 'primary' => 'Contact support'],
            'return-policy' => ['title' => 'Return policy', 'copy' => 'Use this canonical page for current SVICLOUD TV Box US return and warranty guidance. Do not invent return promises beyond the published policy.', 'primary' => 'Contact support'],
        ];
    }
}

if (!function_exists('svic_render_policy_contact_route')) {
    function svic_render_policy_contact_route(): void {
        if (is_admin()) { return; }
        $path = parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
        $path = is_string($path) ? trim(rawurldecode($path), '/') : '';
        if (preg_match('#^(zh|zh-cn|zh-tw)/(.+)$#i', $path, $matches)) {
            $path = $matches[2];
        }
        $routes = svic_policy_contact_routes();
        if (!isset($routes[$path])) { return; }

        $existing_page = get_page_by_path($path, OBJECT, 'page');
        if ($existing_page instanceof WP_Post && $existing_page->post_status === 'publish') {
            return;
        }

        $page = $routes[$path];
        if (function_exists('svic_mark_virtual_page_request')) {
            svic_mark_virtual_page_request($page['title']);
        } else {
            status_header(200);
        }
        get_header();
        ?>
        <main class="guides-detail guides-detail--policy surface--dark">
          <header class="guides-detail__hero"><div class="guides-detail__hero-inner"><div class="guides-detail__hero-copy">
            <span class="guides-badge guides-badge--on-dark">Official US support</span>
            <h1 class="guides-detail__title"><?php echo esc_html($page['title']); ?></h1>
            <p class="guides-detail__lead"><?php echo esc_html($page['copy']); ?></p>
            <div class="guides-detail__hero-actions"><a class="lumen-pill lumen-pill--primary" href="tel:+17023893416">702-389-3416</a><a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url(svic_url_with_lang(home_url('/compare/'))); ?>">Compare models</a></div>
          </div></div></header>
        </main>
        <?php
        get_footer();
        exit;
    }
}
add_action('template_redirect', 'svic_render_policy_contact_route', -70);
