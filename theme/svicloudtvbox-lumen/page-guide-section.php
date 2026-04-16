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
    $detail_link   = get_page_by_path($slug_candidate);
    $href          = $detail_link instanceof WP_Post ? get_permalink($detail_link) : $guides_url;

    $hero_nav_links[] = [
        'label' => $label,
        'href'  => svic_url_with_lang($href),
    ];
}

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
            $detail_link = get_page_by_path($slug_candidate);
            $href = $detail_link instanceof WP_Post ? get_permalink($detail_link) : $guides_url;
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
