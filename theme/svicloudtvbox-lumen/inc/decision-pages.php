<?php
/** Static product-decision landing pages for support-to-purchase routing. */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('svic_decision_pages')) {
    function svic_decision_pages(): array {
        return [
            'svicloud-10p-vs-10s' => [
                'title' => 'SVICLOUD 10P+ vs 10S: Which model should you choose?',
                'lead' => 'Choose 10P+ for the strongest performance and more headroom. Choose 10S for everyday family viewing and value. If you arrived from a support issue, troubleshoot first, then upgrade only when the old box is the bottleneck.',
                'sections' => [
                    ['Best for 10P+', ['Heavy app use and multitasking.', 'Buyers who want the premium model.', 'Users replacing an older slow box after troubleshooting.']],
                    ['Best for 10S', ['Parents or family viewers who want a simpler value choice.', 'Everyday Chinese TV use.', 'Buyers who want official US support without overbuying.']],
                    ['Trust and support', ['Official US support phone: +1 (520) 641-7021.', 'Use shipping, return, and warranty policy pages for current terms.', 'Contact support before assuming an app issue requires replacement.']],
                ],
            ],
            'best-svicloud-box-for-chinese-tv-usa' => [
                'title' => 'Best SVICLOUD box for Chinese TV in the USA',
                'lead' => 'For US Chinese-language buyers, compare 10P+ and 10S by performance needs, family setup, official US support, shipping, and warranty expectations.',
                'sections' => [
                    ['Quick recommendation', ['10P+ is best for maximum performance.', '10S is best for everyday family value.', 'Use official support when setup, app, or remote questions appear.']],
                    ['Why buy from the official US storefront', ['US-focused product guidance.', 'Support phone: +1 (520) 641-7021.', 'Canonical shipping and return policy pages.']],
                ],
            ],
            'yogurt-tv-not-working-upgrade-guide' => [
                'title' => 'Yogurt TV not working: fixes first, upgrade second',
                'lead' => 'Start with network, installer, app, and setup checks. Only compare new models after the common causes are ruled out or an old box is clearly underpowered.',
                'sections' => [
                    ['Fixes to try first', ['Restart network and box.', 'Confirm installer/source guidance from the app guide.', 'Check troubleshooting for app launch, playback, Wi-Fi, and firmware symptoms.']],
                    ['When to compare models', ['Older box remains slow after fixes.', 'You want smoother multitasking.', 'You need help choosing between 10P+ and 10S.']],
                ],
            ],
            'svicloud-box-authenticity-guide' => [
                'title' => 'SVICLOUD box authenticity and scam-avoidance guide',
                'lead' => 'Use official purchase, policy, and contact pages to verify US support expectations. Avoid unsupported claims about third-party sellers; verify uncertain purchases with support.',
                'sections' => [
                    ['Safe verification steps', ['Check official product pages and policy pages.', 'Keep order details private when asking public agents.', 'Call +1 (520) 641-7021 or use the contact page if authenticity is unclear.']],
                    ['What to avoid', ['Do not rely on gray-market promises.', 'Do not assume unofficial app claims are guaranteed.', 'Do not share private order/customer data with public tools.']],
                ],
            ],
        ];
    }
}

if (!function_exists('svic_current_decision_page_key')) {
    function svic_current_decision_page_key(): string {
        $path = parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
        $path = is_string($path) ? trim(rawurldecode($path), '/') : '';
        return $path;
    }
}

if (!function_exists('svic_redirect_localized_decision_page')) {
    function svic_redirect_localized_decision_page(array $pages, string $path): bool {
        if (!preg_match('#^(zh|zh-cn|zh-tw)/(.+)$#i', $path, $matches)) {
            return false;
        }

        $canonical_key = trim($matches[2], '/');
        if (!isset($pages[$canonical_key])) {
            return false;
        }

        wp_safe_redirect(home_url('/' . $canonical_key . '/'), 301);
        exit;
    }
}

if (!function_exists('svic_render_decision_page')) {
    function svic_render_decision_page(): void {
        if (is_admin()) { return; }
        $pages = svic_decision_pages();
        $key = svic_current_decision_page_key();
        svic_redirect_localized_decision_page($pages, $key);
        if (!isset($pages[$key])) {
            // Redirect removed decision pages to canonical compare page
            if (in_array($key, ['svicloud-10p-vs-10s'], true)) {
                wp_safe_redirect(svic_url_with_lang(home_url('/compare/')), 301);
                exit;
            }
            return;
        }
        $existing_page = get_page_by_path($key, OBJECT, 'page');
        if ($existing_page instanceof WP_Post && $existing_page->post_status === 'publish') {
            return;
        }
        $page = $pages[$key];
        if (function_exists('svic_mark_virtual_page_request')) {
            svic_mark_virtual_page_request($page['title'], 'guides');
        } else {
            status_header(200);
        }
        get_header();
        $compare = svic_url_with_lang(home_url('/compare/'));
        $contact = svic_url_with_lang(home_url('/contact/'));
        $apps = svic_url_with_lang(home_url('/guides-apps/'));
        $trouble = svic_url_with_lang(home_url('/guides-troubleshooting/'));
        $setup = svic_url_with_lang(home_url('/guides-setup/'));
        $shipping = svic_url_with_lang(home_url('/shipping-policy/'));
        $returns = svic_url_with_lang(home_url('/return-policy/'));
        $product_10p_url = svic_url_with_lang(home_url('/product/svicloud-10p-plus/'));
        $product_10s_url = svic_url_with_lang(home_url('/product/svicloud-10s/'));
        ?>
        <main id="main-content" class="guides-detail guides-detail--decision surface--dark" tabindex="-1">
          <header class="guides-detail__hero"><div class="guides-detail__hero-inner"><div class="guides-detail__hero-copy">
            <span class="guides-badge guides-badge--on-dark">Official US guidance</span>
            <h1 class="guides-detail__title"><?php echo esc_html($page['title']); ?></h1>
            <p class="guides-detail__lead"><?php echo esc_html($page['lead']); ?></p>
            <div class="guides-detail__hero-actions">
              <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($product_10p_url); ?>" data-svic-event="svic_decision_cta_click" data-svic-label="product_10p">Shop 10P+</a>
              <a class="lumen-pill lumen-pill--ghost" href="<?php echo esc_url($product_10s_url); ?>" data-svic-event="svic_decision_cta_click" data-svic-label="product_10s">Shop 10S</a>
              <a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url($compare); ?>" data-svic-event="svic_decision_cta_click" data-svic-label="compare">Compare 10P+ vs 10S</a>
              <a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url($contact); ?>" data-svic-event="svic_decision_cta_click" data-svic-label="contact">Contact support</a>
            </div>
          </div></div></header>
          <div class="guides-detail__layout"><article class="guides-detail__content">
            <?php foreach ($page['sections'] as $section) : ?>
              <section class="guides-answer-hub surface--light"><h2><?php echo esc_html($section[0]); ?></h2><ul><?php foreach ($section[1] as $item) : ?><li><?php echo esc_html($item); ?></li><?php endforeach; ?></ul></section>
            <?php endforeach; ?>
            <section class="guides-answer-hub surface--light">
              <h2>Product purchase paths</h2>
              <p>Use these official product pages only after support-first checks or when the buyer is ready to choose a model.</p>
              <ul>
                <li><a href="<?php echo esc_url($product_10p_url); ?>" data-svic-event="svic_decision_cta_click" data-svic-label="product_10p_card">SVICLOUD 10P+ product page</a> — premium performance and more headroom.</li>
                <li><a href="<?php echo esc_url($product_10s_url); ?>" data-svic-event="svic_decision_cta_click" data-svic-label="product_10s_card">SVICLOUD 10S product page</a> — everyday family/value choice.</li>
                <li><a href="<?php echo esc_url($compare); ?>" data-svic-event="svic_decision_cta_click" data-svic-label="compare_card">Compare 10P+ vs 10S</a> before buying if the fit is unclear.</li>
              </ul>
            </section>
            <section class="guides-answer-hub surface--light">
              <h2>Setup, shipping, returns, and support</h2>
              <p>These routes keep product decisions accurate and policy-safe.</p>
              <ul>
                <li><a href="<?php echo esc_url($setup); ?>" data-svic-event="svic_decision_cta_click" data-svic-label="setup_card">Setup guide</a> for first-time installation and configuration.</li>
                <li><a href="<?php echo esc_url($shipping); ?>" data-svic-event="svic_decision_cta_click" data-svic-label="shipping_card">Shipping policy</a> for current delivery guidance.</li>
                <li><a href="<?php echo esc_url($returns); ?>" data-svic-event="svic_decision_cta_click" data-svic-label="returns_card">Return policy</a> for current return/warranty guidance.</li>
                <li><a href="<?php echo esc_url($contact); ?>" data-svic-event="svic_decision_cta_click" data-svic-label="contact_card">Contact official US support</a> or call +1 (520) 641-7021.</li>
              </ul>
            </section>
            <section class="guides-inline-cta"><h2>Need help before buying?</h2><p>Use support-first guides and policy pages before deciding a replacement is needed.</p><div class="guides-support__actions lumen-action-group"><a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($product_10p_url); ?>" data-svic-event="svic_decision_cta_click" data-svic-label="product_10p_secondary">Shop 10P+</a><a class="lumen-pill lumen-pill--ghost" href="<?php echo esc_url($product_10s_url); ?>" data-svic-event="svic_decision_cta_click" data-svic-label="product_10s_secondary">Shop 10S</a><a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url($apps); ?>" data-svic-event="svic_decision_cta_click" data-svic-label="apps">App guide</a><a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url($trouble); ?>" data-svic-event="svic_decision_cta_click" data-svic-label="troubleshooting">Troubleshooting</a><a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url($setup); ?>" data-svic-event="svic_decision_cta_click" data-svic-label="setup">Setup guide</a><a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url($shipping); ?>" data-svic-event="svic_decision_cta_click" data-svic-label="shipping">Shipping policy</a><a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url($returns); ?>" data-svic-event="svic_decision_cta_click" data-svic-label="returns">Return policy</a><a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($contact); ?>" data-svic-event="svic_decision_cta_click" data-svic-label="phone">+1 (520) 641-7021</a></div></section>
          </article></div>
        </main>
        <?php
        get_footer();
        exit;
    }
}
add_action('parse_request', 'svic_render_decision_page', 0);
add_action('template_redirect', 'svic_render_decision_page', -1000000);
