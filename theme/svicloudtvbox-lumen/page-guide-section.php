<?php
/**
 * Template Name: SviCloud Guide Section
 */

global $post, $svic_guides_detail_key;

get_header();

$guides_url = svic_url_with_lang(home_url('/guides/'));
$contact_url = svic_url_with_lang(home_url('/contact/'));
$faq_url = svic_url_with_lang(home_url('/faq/'));
$compare_url = svic_url_with_lang(home_url('/compare/'));
$decision_compare_url = home_url('/compare/');
$decision_best_url = home_url('/best-svicloud-box-for-chinese-tv-usa/');
$decision_yogurt_url = home_url('/yogurt-tv-not-working-upgrade-guide/');
$decision_authenticity_url = home_url('/svicloud-box-authenticity-guide/');
$product_10p = class_exists('WooCommerce') ? svic_get_product_by_slug('svicloud-10p-plus') : null;
$product_10s = class_exists('WooCommerce') ? svic_get_product_by_slug('svicloud-10s') : null;
$product_10p_url = $product_10p ? svic_url_with_lang(get_permalink($product_10p->get_id())) : svic_url_with_lang(home_url('/product/svicloud-10p-plus/'));
$product_10s_url = $product_10s ? svic_url_with_lang(get_permalink($product_10s->get_id())) : svic_url_with_lang(home_url('/product/svicloud-10s/'));

$forced_section_key = isset($svic_guides_detail_key) ? $svic_guides_detail_key : null;

$section_key = null;
$section_bundle = null;

if ($forced_section_key) {
    $section_key = $forced_section_key;
    $section_bundle = svic_guides_get_section_content($section_key);
} else {
    $slug = '';
    if ($post instanceof WP_Post) {
        $slug = $post->post_name;
    }

    $section_key = svic_guides_resolve_section_key($slug);
    $section_bundle = $section_key ? svic_guides_get_section_content($section_key) : null;
}

if (!$section_key || !$section_bundle) {
    wp_safe_redirect($guides_url);
    exit;
}

$svic_guides_detail_key = null;
unset($GLOBALS['svic_guides_detail_key']);

$section_meta   = $section_bundle['section'];
$content_items  = $section_bundle['items'];
$translation_root = $section_meta['translation_root'] ?? '';

$has_translation = static function ($key) {
    if (!$key) {
        return false;
    }

    $translated = svic_translate($key);
    if ($translated === $key) {
        return false;
    }

    $last_segment = '';
    if (strpos($key, '.') !== false) {
        $parts = explode('.', $key);
        $last_segment = end($parts) ?: '';
    }

    // Translator falls back to the last segment when a string is missing; treat that as untranslated.
    return $last_segment === '' || $translated !== $last_segment;
};

$translate_html = static function ($key) use ($has_translation) {
    if (!$has_translation($key)) {
        return '';
    }

    return svic_translate_html($key);
};

$translate_rich = static function ($key) use ($has_translation) {
    if (!$has_translation($key)) {
        return '';
    }

    return wp_kses_post(svic_translate_rich($key));
};

$badge = $translation_root ? $translate_html($translation_root . '.badge') : '';
$title = $translation_root ? $translate_html($translation_root . '.title') : '';
$lead  = '';

if ($translation_root) {
    $lead = $translate_rich($translation_root . '.lead');
    if (!$lead) {
        $lead = $translate_rich($translation_root . '.copy');
    }
}

if (!$title) {
    $title = get_the_title();
}

$other_sections = array_filter(
    svic_guides_get_anchor_items(),
    static function ($item) use ($section_key) {
        return isset($item['key']) && $item['key'] && $item['key'] !== $section_key && $item['key'] !== 'overview';
    }
);

$resolve_guide_section_url = static function (string $slug_candidate) use ($guides_url): string {
    $slug_candidate = trim($slug_candidate, '/');
    if ($slug_candidate === '') {
        return $guides_url;
    }

    $detail_link = get_page_by_path($slug_candidate);
    if ($detail_link instanceof WP_Post && $detail_link->post_status === 'publish') {
        return get_permalink($detail_link);
    }

    return home_url('/' . $slug_candidate . '/');
};

$hero_callouts = svic_guides_get_content_item('hero_callouts', []);
$hero_callouts = is_array($hero_callouts) ? array_values(array_filter($hero_callouts)) : [];

$hero_callouts_headline = $translate_html('guides.hero.callouts_headline');
$hero_pill_headline     = $translate_html('guides.hero.pill_headline');
$hero_pill_copy         = $translate_html('guides.hero.pill_copy');
$hero_primary_label     = $translate_html('guides.support.primary_label');
$hero_secondary_label   = $translate_html('guides.support.secondary_label');

$hero_nav_links = [];
foreach ($other_sections as $item) {
    if (count($hero_nav_links) >= 3) {
        break;
    }

    $key       = $item['key'] ?? '';
    $label_key = $item['label_key'] ?? '';

    if (!$key || !$label_key) {
        continue;
    }

    $label = $translate_html($label_key);
    if (!$label) {
        continue;
    }

    $slug_hint     = $item['slug'] ?? '';
    $slug_candidate = $slug_hint ?: ('guides-' . str_replace('_', '-', $key));
    $href          = $resolve_guide_section_url($slug_candidate);

    $hero_nav_links[] = [
        'label' => $label,
        'href'  => svic_url_with_lang($href),
    ];
}

$answer_hubs = [
    'apps' => [
        'quick' => [
            'title' => strpos(svic_current_locale(), 'zh') === 0 ? '快速答案：Yogurt TV 下載與 8989c' : 'Quick answer: Yogurt TV download and 8989c',
            'copy'  => strpos(svic_current_locale(), 'zh') === 0 ? '先確認網路可用，再依照本頁步驟開啟安裝入口、搜尋 Yogurt TV 或 8989c。若無法下載、無法開啟或顯示錯誤，請改走疑難排解頁或聯絡官方美國客服 702-389-3416。' : 'Confirm the network first, then use this guide to open the installer, search for Yogurt TV or 8989c, and install safely. If download, launch, or playback still fails, use troubleshooting or contact official US support at 702-389-3416.',
        ],
        'faqs' => [
            [strpos(svic_current_locale(), 'zh') === 0 ? 'Yogurt TV 怎麼下載？' : 'How do I download Yogurt TV?', strpos(svic_current_locale(), 'zh') === 0 ? '依序檢查網路、安裝入口與搜尋字詞；避免使用來路不明的第三方連結。' : 'Check network, installer entry, and search terms in order; avoid unknown third-party links.'],
            [strpos(svic_current_locale(), 'zh') === 0 ? '8989c 或 8989c.cc 無法開怎麼辦？' : 'What if 8989c or 8989c.cc does not open?', strpos(svic_current_locale(), 'zh') === 0 ? '先重啟網路與盒子，再確認網址輸入正確；仍失敗時請走疑難排解或聯絡客服。' : 'Restart the network and box, confirm the address, then use troubleshooting or support if it still fails.'],
            [strpos(svic_current_locale(), 'zh') === 0 ? 'App 啟動失敗是否代表盒子壞了？' : 'Does an app launch failure mean the box is broken?', strpos(svic_current_locale(), 'zh') === 0 ? '不一定。常見原因包含網路、App 版本、安裝來源或暫時服務異常；先排除後再考慮升級。' : 'Not always. Network, app version, installer source, or temporary service issues are common; troubleshoot before considering an upgrade.'],
        ],
    ],
    'troubleshooting' => [
        'quick' => [
            'title' => strpos(svic_current_locale(), 'zh') === 0 ? '快速答案：先症狀、再修復、最後客服' : 'Quick answer: symptom first, fix second, support last',
            'copy'  => strpos(svic_current_locale(), 'zh') === 0 ? '遙控器沒反應、Yogurt TV 不能看、無訊號、Wi-Fi 斷線或畫面卡住時，請先比對下方症狀並逐步排除。不要立刻恢復出廠設定；不確定時請撥 702-389-3416 或使用聯絡頁。' : 'For remote, Yogurt TV, no signal, Wi-Fi, or frozen-screen issues, match the symptom below and follow the visible fixes. Do not jump to factory reset; call 702-389-3416 or use contact if unsure.',
        ],
        'faqs' => [
            [strpos(svic_current_locale(), 'zh') === 0 ? '小雲遙控器沒反應怎麼辦？' : 'What if the SVICLOUD remote does not respond?', strpos(svic_current_locale(), 'zh') === 0 ? '先換電池、靠近盒子、重新配對，再檢查是否有遮擋或干擾。' : 'Replace batteries, move closer, re-pair, and check for obstruction or interference.'],
            [strpos(svic_current_locale(), 'zh') === 0 ? 'Yogurt TV 不能看 2026 怎麼處理？' : 'How should Yogurt TV not working be handled?', strpos(svic_current_locale(), 'zh') === 0 ? '先檢查網路、重開 App、確認安裝來源；仍失敗時聯絡客服，不要相信非官方保證。' : 'Check network, restart the app, verify installer source, then contact support; avoid unofficial guarantees.'],
            [strpos(svic_current_locale(), 'zh') === 0 ? '什麼時候該考慮升級？' : 'When should I consider upgrading?', strpos(svic_current_locale(), 'zh') === 0 ? '只有在網路、App、遙控器與設定都排除後，或舊機效能明顯不足時，再比較 10P+ 與 10S。' : 'Compare 10P+ and 10S only after network, app, remote, and setup causes are ruled out or an old box is clearly too slow.'],
        ],
    ],
    'setup' => [
        'quick' => [
            'title' => strpos(svic_current_locale(), 'zh') === 0 ? '快速答案：首次安裝順序' : 'Quick answer: first setup order',
            'copy'  => strpos(svic_current_locale(), 'zh') === 0 ? '先接 HDMI 與電源，再設定語言、遙控器、時間與網路，最後依 App 指南安裝常用 App。卡關時請看疑難排解或聯絡 702-389-3416。' : 'Connect HDMI and power, set language, remote, time, and network, then use the app guide for apps. If blocked, use troubleshooting or call 702-389-3416.',
        ],
        'faqs' => [],
    ],
];

$render_answer_hub = static function () use ($section_key, $answer_hubs, $compare_url, $decision_compare_url, $decision_best_url, $decision_yogurt_url, $decision_authenticity_url, $contact_url, $guides_url) {
    if (empty($answer_hubs[$section_key])) {
        return;
    }
    $hub = $answer_hubs[$section_key];
    ?>
    <section class="guides-answer-hub surface--light" aria-labelledby="guides-answer-hub-title">
      <div class="guides-answer-hub__quick">
        <h2 id="guides-answer-hub-title"><?php echo esc_html($hub['quick']['title']); ?></h2>
        <p><?php echo esc_html($hub['quick']['copy']); ?></p>
      </div>
      <div class="guides-answer-hub__links">
        <a href="<?php echo esc_url($guides_url); ?>"><?php echo esc_html(svic_translate('guides.detail.back_to_hub')); ?></a>
        <a href="<?php echo esc_url($compare_url); ?>"><?php echo esc_html(svic_translate('product.traffic.links.compare')); ?></a>
        <a href="<?php echo esc_url($decision_compare_url); ?>">10P+ vs 10S guide</a>
        <?php if ($section_key === 'apps') : ?>
          <a href="<?php echo esc_url($decision_yogurt_url); ?>">Yogurt TV upgrade guide</a>
        <?php elseif ($section_key === 'troubleshooting') : ?>
          <a href="<?php echo esc_url($decision_authenticity_url); ?>">Authenticity guide</a>
        <?php else : ?>
          <a href="<?php echo esc_url($decision_best_url); ?>">Best SVICLOUD box in USA</a>
        <?php endif; ?>
        <a href="<?php echo esc_url($contact_url); ?>"><?php echo esc_html(svic_translate('product.traffic.links.contact')); ?></a>
      </div>
      <?php if (!empty($hub['faqs'])) : ?>
        <?php
        $faq_entities_inline = [];
        foreach ($hub['faqs'] as $faq_inline) {
            if (!is_array($faq_inline) || count($faq_inline) < 2) {
                continue;
            }
            $faq_question_inline = trim(wp_strip_all_tags((string) $faq_inline[0]));
            $faq_answer_inline   = trim(wp_strip_all_tags((string) $faq_inline[1]));
            if ($faq_question_inline === '' || $faq_answer_inline === '') {
                continue;
            }
            $faq_entities_inline[] = [
                '@type' => 'Question',
                'name'  => $faq_question_inline,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => $faq_answer_inline,
                ],
            ];
        }
        if ($faq_entities_inline) {
            echo '<script type="application/ld+json">' . wp_json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => $faq_entities_inline,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
        ?>
        <div class="guides-answer-hub__faq">
          <h3><?php echo esc_html(strpos(svic_current_locale(), 'zh') === 0 ? '常見問題' : 'FAQ'); ?></h3>
          <?php foreach ($hub['faqs'] as $faq) : ?>
            <details>
              <summary><?php echo esc_html($faq[0]); ?></summary>
              <p><?php echo esc_html($faq[1]); ?></p>
            </details>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
    <?php
};

$render_inline_cro_cta = static function () use ($product_10p_url, $product_10s_url, $compare_url, $contact_url) {
    ?>
    <section class="guides-inline-cta" aria-label="<?php echo esc_attr(svic_translate('compare.final_cta.badge')); ?>">
      <span class="guides-inline-cta__badge"><?php echo svic_translate_html('compare.final_cta.badge'); ?></span>
      <h2 class="guides-inline-cta__title"><?php echo svic_translate_html('compare.final_cta.title'); ?></h2>
      <p class="guides-inline-cta__copy"><?php echo svic_translate_html('compare.final_cta.copy'); ?></p>
      <div class="guides-inline-cta__actions lumen-action-group">
        <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($product_10p_url); ?>"><?php echo svic_translate_html('compare.final_cta.cta_10p'); ?></a>
        <a class="lumen-pill lumen-pill--ghost" href="<?php echo esc_url($product_10s_url); ?>"><?php echo svic_translate_html('compare.final_cta.cta_10s'); ?></a>
        <a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url($compare_url); ?>"><?php echo svic_translate_html('product.traffic.links.compare'); ?></a>
        <a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url($contact_url); ?>"><?php echo svic_translate_html('product.traffic.links.contact'); ?></a>
      </div>
    </section>
    <?php
};

?>
<main class="guides-detail guides-detail--<?php echo esc_attr($section_key); ?> surface--dark">
  <header class="guides-detail__hero">
    <div class="guides-detail__hero-inner">
      <div class="guides-detail__hero-copy">
        <div class="guides-detail__hero-top">
          <a class="guides-detail__back" href="<?php echo esc_url($guides_url); ?>">
            <span class="guides-detail__back-icon" aria-hidden="true"></span>
            <span><?php echo $translate_html('guides.detail.back_to_hub'); ?></span>
          </a>
          <?php if ($badge) : ?>
            <span class="guides-badge guides-badge--on-dark guides-detail__badge"><?php echo $badge; ?></span>
          <?php endif; ?>
        </div>

        <div class="guides-detail__hero-heading">
          <h1 class="guides-detail__title"><?php echo $title; ?></h1>
          <?php if ($lead) : ?>
            <p class="guides-detail__lead"><?php echo $lead; ?></p>
          <?php endif; ?>
        </div>

        <?php if ($hero_pill_headline || $hero_pill_copy) : ?>
          <div class="guides-detail__hero-pill">
            <?php if ($hero_callouts_headline) : ?>
              <span class="guides-detail__hero-pill-badge"><?php echo $hero_callouts_headline; ?></span>
            <?php endif; ?>
            <span class="guides-detail__hero-pill-text">
              <?php if ($hero_pill_headline) : ?>
                <span class="guides-detail__hero-pill-headline"><?php echo $hero_pill_headline; ?></span>
              <?php endif; ?>
              <?php if ($hero_pill_copy) : ?>
                <span class="guides-detail__hero-pill-copy"><?php echo $hero_pill_copy; ?></span>
              <?php endif; ?>
            </span>
          </div>
        <?php endif; ?>

        <?php if ($hero_callouts) : ?>
          <div class="guides-detail__hero-callouts">
            <?php if ($hero_callouts_headline && !$hero_pill_headline && !$hero_pill_copy) : ?>
              <span class="guides-detail__hero-callouts-label"><?php echo $hero_callouts_headline; ?></span>
            <?php endif; ?>
            <ul class="guides-detail__callouts-list">
              <?php foreach ($hero_callouts as $callout_key) : ?>
                <li><?php echo svic_translate_html($callout_key); ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <?php if ($hero_primary_label || $hero_secondary_label) : ?>
          <div class="guides-detail__hero-actions">
            <?php if ($hero_primary_label) : ?>
              <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($contact_url); ?>">
                <?php echo $hero_primary_label; ?>
              </a>
            <?php endif; ?>
            <?php if ($hero_secondary_label) : ?>
              <a class="lumen-pill lumen-pill--outline guides-detail__hero-action-secondary" href="<?php echo esc_url($faq_url); ?>">
                <?php echo $hero_secondary_label; ?>
              </a>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>

      <?php if ($hero_nav_links) : ?>
        <div class="guides-detail__hero-nav">
          <span class="guides-detail__hero-nav-label"><?php echo $translate_html('guides.detail.more_guides'); ?></span>
          <ul class="guides-detail__hero-nav-list">
            <?php foreach ($hero_nav_links as $nav_item) : ?>
              <li>
                <a class="guides-detail__hero-nav-link" href="<?php echo esc_url($nav_item['href']); ?>">
                  <span class="guides-detail__hero-nav-text"><?php echo $nav_item['label']; ?></span>
                  <span class="guides-detail__hero-nav-icon" aria-hidden="true"></span>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
    </div>
  </header>

  <div class="guides-detail__layout">
    <article class="guides-detail__content">
      <?php $render_answer_hub(); ?>
      <?php if ($section_key === 'setup') : ?>
        <ol class="guides-steps guides-detail__steps surface--light">
          <?php foreach ($content_items as $index => $step) :
            $title_key = $step['title_key'] ?? '';
            $copy_key  = $step['copy_key'] ?? '';
          ?>
            <li class="guides-step">
              <div class="guides-step__content">
                <h2 class="guides-step__title"><?php echo $translate_html($title_key); ?></h2>
                <p class="guides-step__copy"><?php echo $translate_rich($copy_key); ?></p>
              </div>
            </li>
            <?php if ($index === 0) : ?>
              <li class="guides-step guides-step--cta">
                <?php $render_inline_cro_cta(); ?>
              </li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ol>
        <?php
          $setup_note_title = $translate_html('guides.setup.note_title');
          $setup_note_copy  = $translate_html('guides.setup.note_copy');
          if ($setup_note_title || $setup_note_copy) :
        ?>
          <aside class="guides-note guides-detail__note surface--light">
            <?php if ($setup_note_title) : ?>
              <h2 class="guides-note__title"><?php echo $setup_note_title; ?></h2>
            <?php endif; ?>
            <?php if ($setup_note_copy) : ?>
              <p class="guides-note__copy"><?php echo $setup_note_copy; ?></p>
            <?php endif; ?>
          </aside>
        <?php endif; ?>
      <?php elseif ($section_key === 'apps' || $section_key === 'post_setup') : ?>
        <div class="guides-grid guides-grid--detail surface--light">
          <?php foreach ($content_items as $index => $card) :
            $title_key = $card['title_key'] ?? '';
            $copy_key  = $card['copy_key'] ?? '';
          ?>
            <article class="guides-card">
              <h2 class="guides-card__title"><?php echo $translate_html($title_key); ?></h2>
              <p class="guides-card__copy"><?php echo $translate_html($copy_key); ?></p>
            </article>
            <?php if ($index === 0) : ?>
              <?php $render_inline_cro_cta(); ?>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      <?php elseif ($section_key === 'troubleshooting') : ?>
        <div class="guides-grid guides-grid--troubleshooting surface--light">
          <?php foreach ($content_items as $index => $card) :
            $title_key = $card['title_key'] ?? '';
            $copy_key  = $card['copy_key'] ?? '';
          ?>
            <article class="guides-card guides-card--troubleshoot">
              <h2 class="guides-card__title"><?php echo $translate_html($title_key); ?></h2>
              <p class="guides-card__copy"><?php echo $translate_rich($copy_key); ?></p>
            </article>
            <?php if ($index === 0) : ?>
              <?php $render_inline_cro_cta(); ?>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      <?php elseif ($section_key === 'resources') : ?>
        <ul class="guides-resource-list surface--light">
          <?php foreach ($content_items as $index => $resource) :
            $title_key = $resource['title_key'] ?? '';
            $copy_key  = $resource['copy_key'] ?? '';
            $link      = svic_guides_get_resource_link($resource);
            if (!$link) {
                continue;
            }
            $is_external = !empty($resource['external']) && !empty($resource['url']);
            $link_rel    = $is_external ? ' rel="noopener"' : '';
            $link_target = $is_external ? ' target="_blank"' : '';
          ?>
            <li class="guides-resource-item">
              <a class="guides-resource" href="<?php echo esc_url($link); ?>"<?php echo $link_target; ?><?php echo $link_rel; ?>>
                <span class="guides-resource__title"><?php echo $translate_html($title_key); ?></span>
                <span class="guides-resource__copy"><?php echo $translate_html($copy_key); ?></span>
              </a>
            </li>
            <?php if ($index === 0) : ?>
              <li class="guides-resource-item guides-resource-item--cta">
                <?php $render_inline_cro_cta(); ?>
              </li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ul>
      <?php elseif ($section_key === 'support') : ?>
        <section class="guides-support guides-support--detail">
          <div class="guides-support__inner">
            <?php if ($translate_html('guides.support.badge')) : ?>
              <span class="guides-badge guides-badge--on-dark"><?php echo $translate_html('guides.support.badge'); ?></span>
            <?php endif; ?>
            <h2 class="guides-support__title"><?php echo $translate_html('guides.support.title'); ?></h2>
            <p class="guides-support__copy"><?php echo $translate_html('guides.support.copy'); ?></p>
            <div class="guides-support__actions lumen-action-group">
              <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($contact_url); ?>"><?php echo $translate_html('guides.support.primary_label'); ?></a>
              <a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url($faq_url); ?>"><?php echo $translate_html('guides.support.secondary_label'); ?></a>
            </div>
          </div>
        </section>
      <?php else : ?>
        <div class="guides-grid guides-grid--detail surface--light">
          <?php foreach ($content_items as $index => $card) :
            $title_key = $card['title_key'] ?? '';
            $copy_key  = $card['copy_key'] ?? '';
          ?>
            <article class="guides-card">
              <h2 class="guides-card__title"><?php echo $translate_html($title_key); ?></h2>
              <p class="guides-card__copy"><?php echo $translate_html($copy_key); ?></p>
            </article>
            <?php if ($index === 0) : ?>
              <?php $render_inline_cro_cta(); ?>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </article>

    <?php if ($other_sections) : ?>
      <aside class="guides-detail__sidebar">
        <h2 class="guides-detail__sidebar-title"><?php echo $translate_html('guides.detail.more_guides'); ?></h2>
        <ul class="guides-detail__sidebar-list">
          <?php foreach ($other_sections as $item) :
            $key       = $item['key'] ?? '';
            $label_key = $item['label_key'] ?? '';
            $slug_hint = $item['slug'] ?? '';
            if (!$key || !$label_key || $key === 'overview') {
                continue;
            }

            $slug_candidate = $slug_hint ?: ('guides-' . str_replace('_', '-', $key));
            $href = $resolve_guide_section_url($slug_candidate);
          ?>
            <li>
              <a class="guides-detail__sidebar-link" href="<?php echo esc_url(svic_url_with_lang($href)); ?>">
                <span class="guides-detail__sidebar-label"><?php echo $translate_html($label_key); ?></span>
                <span class="guides-detail__sidebar-arrow" aria-hidden="true"></span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </aside>
    <?php endif; ?>
  </div>

  <?php if ($section_key !== 'support') : ?>
    <section class="guides-support guides-support--detail-cta">
      <div class="guides-support__inner">
        <span class="guides-badge guides-badge--on-dark"><?php echo svic_translate_html('compare.final_cta.badge'); ?></span>
        <h2 class="guides-support__title"><?php echo svic_translate_html('compare.final_cta.title'); ?></h2>
        <p class="guides-support__copy"><?php echo svic_translate_html('compare.final_cta.copy'); ?></p>
        <div class="guides-support__actions lumen-action-group">
          <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($product_10p_url); ?>"><?php echo svic_translate_html('compare.final_cta.cta_10p'); ?></a>
          <a class="lumen-pill lumen-pill--ghost" href="<?php echo esc_url($product_10s_url); ?>"><?php echo svic_translate_html('compare.final_cta.cta_10s'); ?></a>
          <a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url($compare_url); ?>"><?php echo svic_translate_html('product.traffic.links.compare'); ?></a>
          <a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url($contact_url); ?>"><?php echo svic_translate_html('product.traffic.links.contact'); ?></a>
        </div>
      </div>
    </section>
  <?php endif; ?>
</main>

<?php
if ($section_key === 'setup' && !empty($content_items) && is_array($content_items)) {
    $howto_steps = [];
    $canonical   = function_exists('svic_get_localized_canonical_url') ? svic_get_localized_canonical_url() : '';
    if (!$canonical && isset($post) && $post instanceof WP_Post) {
        $canonical = get_permalink($post);
    }

    $language = '';
    if (function_exists('svic_locale_to_hreflang') && function_exists('svic_current_locale')) {
        $language = svic_locale_to_hreflang(svic_current_locale());
    } elseif (function_exists('get_locale')) {
        $language = get_locale();
    }
    $language = $language ? strtolower(str_replace('_', '-', $language)) : 'en-us';

    foreach ($content_items as $index => $step) {
        if (!is_array($step)) {
            continue;
        }

        $title_key = $step['title_key'] ?? '';
        $copy_key  = $step['copy_key'] ?? '';

        $step_title = $title_key ? svic_translate($title_key) : '';
        $step_title = trim(wp_strip_all_tags((string) $step_title));
        if ($step_title === '') {
            $step_title = sprintf(__('Step %d', 'svicloudtvbox-lumen'), $index + 1);
        }

        $copy_value = $copy_key ? svic_translate_rich($copy_key) : '';
        $step_text  = trim(wp_strip_all_tags((string) $copy_value));
        $step_text  = preg_replace('/[\x00-\x1F\x7F]/u', ' ', $step_text) ?: $step_text;

        if ($step_title === '' && $step_text === '') {
            continue;
        }

        $step_url = '';
        if ($canonical) {
            $step_url = untrailingslashit($canonical) . '#setup-step-' . ($index + 1);
        }

        $step_entry = [
            '@type' => 'HowToStep',
            'name'  => $step_title,
        ];

        if ($step_text !== '') {
            $step_entry['text'] = $step_text;
        }
        if ($step_url !== '') {
            $step_entry['url'] = $step_url;
        }

        $howto_steps[] = $step_entry;
    }

    if ($howto_steps) {
        $howto_name        = trim(wp_strip_all_tags((string) $title));
        $howto_description = trim(wp_strip_all_tags((string) $lead));
        if ($howto_description === '') {
            $howto_description = $howto_name;
        }

        $howto_schema = [
            '@context'        => 'https://schema.org',
            '@type'           => 'HowTo',
            'name'            => $howto_name ?: __('SVICLOUD Setup Guide', 'svicloudtvbox-lumen'),
            'description'     => $howto_description ?: __('Follow these steps to set up your SVICLOUD TV box.', 'svicloudtvbox-lumen'),
            'inLanguage'      => $language,
            'step'            => $howto_steps,
            'totalTime'       => 'PT15M',
            'mainEntityOfPage'=> $canonical ?: home_url('/guides-setup/'),
        ];

        echo '<script type="application/ld+json">' . wp_json_encode($howto_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}
?>

<?php
get_footer();
