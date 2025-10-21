<?php
/**
 * Guides Detail: Resources (hub + local articles)
 */

$requested_topic = '';
if (isset($_GET['topic'])) {
    $requested_topic = sanitize_text_field((string) wp_unslash($_GET['topic']));
}

$topic_slug = $requested_topic ? sanitize_title($requested_topic) : '';
$article    = $topic_slug ? svic_guides_get_resource_article($topic_slug) : null;

if ($article) {
    get_header();

    $resources_base = svic_guides_get_section_link('resources');
    if (!$resources_base) {
        $resources_base = home_url('/guides-resources/');
    }
    $resources_base = svic_url_with_lang($resources_base);

    $links = [
        'contact'                 => svic_url_with_lang(home_url('/contact/')),
        'faq'                     => svic_url_with_lang(home_url('/faq/')),
        'guides'                  => svic_url_with_lang(home_url('/guides/')),
        'guides-resources'        => $resources_base,
        'legal-disclaimer'        => svic_url_with_lang(home_url('/legal-disclaimer/')),
    ];

    $section_identifiers = [
        'guides-setup'            => 'setup',
        'guides-apps'             => 'apps',
        'guides-troubleshooting'  => 'troubleshooting',
    ];

    foreach ($section_identifiers as $identifier => $section_key) {
        $section_link = svic_guides_get_section_link($section_key);
        if (!$section_link) {
            $section_link = home_url("/{$identifier}/");
        }
        $links[$identifier] = svic_url_with_lang($section_link);
    }

    $hero           = $article['hero'] ?? [];
    $sections       = $article['sections'] ?? [];
    $resource_cards = svic_guides_get_content_item('resource_links', []);

    $other_resources = [];
    foreach ($resource_cards as $card) {
        if (!isset($card['slug']) || $card['slug'] === $article['slug']) {
            continue;
        }
        $other_resources[] = $card;
    }

    ?>
    <main class="guides-article surface--dark">
      <header class="guides-article__hero">
        <div class="guides-article__hero-inner">
          <a class="guides-detail__back" href="<?php echo esc_url($resources_base); ?>">
            <span class="guides-detail__back-icon" aria-hidden="true"></span>
            <span><?php echo svic_translate_html('guides.resources.articles.shared.back_label'); ?></span>
          </a>
          <?php
          $badge_key = $hero['badge_key'] ?? '';
          if ($badge_key) :
          ?>
            <span class="guides-badge guides-badge--on-dark guides-article__badge"><?php echo svic_translate_html($badge_key); ?></span>
          <?php endif; ?>
          <h1 class="guides-article__title">
            <?php
            $title_key = $hero['title_key'] ?? ($article['title_key'] ?? '');
            echo $title_key ? svic_translate_html($title_key) : get_the_title();
            ?>
          </h1>
          <?php
          $lead_key = $hero['lead_key'] ?? '';
          if ($lead_key) :
          ?>
            <p class="guides-article__lead"><?php echo svic_translate_html($lead_key); ?></p>
          <?php endif; ?>
          <?php
          $updated_key = $hero['updated_key'] ?? '';
          if ($updated_key) :
          ?>
            <p class="guides-article__meta"><?php echo svic_translate_html($updated_key); ?></p>
          <?php endif; ?>
        </div>
      </header>

      <div class="guides-article__layout">
        <article class="guides-article__content surface--light">
          <?php foreach ($sections as $section) :
            $section_id    = $section['id'] ?? '';
            $heading_key   = $section['heading_key'] ?? '';
            $body_blocks   = $section['body'] ?? [];
            ?>
            <section <?php if ($section_id) : ?>id="<?php echo esc_attr($section_id); ?>"<?php endif; ?> class="guides-article__section">
              <?php if ($heading_key) : ?>
                <h2 class="guides-article__section-heading"><?php echo svic_translate_html($heading_key); ?></h2>
              <?php endif; ?>
              <?php if ($body_blocks) : ?>
                <div class="guides-article__section-body">
                  <?php
                  foreach ($body_blocks as $block) {
                      $block_key = $block['key'] ?? '';
                      if (!$block_key) {
                          continue;
                      }

                      $replacements = [];
                      if (!empty($block['tokens']) && is_array($block['tokens'])) {
                          foreach ($block['tokens'] as $token => $identifier) {
                              if (isset($links[$identifier])) {
                                  $replacements[$token] = $links[$identifier];
                              } else {
                                  $replacements[$token] = $identifier;
                              }
                          }
                      }

                      echo '<div class="guides-article__paragraph">';
                      echo wp_kses_post(svic_translate_rich($block_key, $replacements));
                      echo '</div>';
                  }
                  ?>
                </div>
              <?php endif; ?>
            </section>
          <?php endforeach; ?>

          <section class="guides-support guides-support--detail guides-article__support">
            <div class="guides-support__inner">
              <?php if (svic_translate_html('guides.support.badge')) : ?>
                <span class="guides-badge guides-badge--on-dark"><?php echo svic_translate_html('guides.support.badge'); ?></span>
              <?php endif; ?>
              <h2 class="guides-support__title"><?php echo svic_translate_html('guides.support.title'); ?></h2>
              <p class="guides-support__copy"><?php echo svic_translate_html('guides.support.copy'); ?></p>
              <div class="guides-support__actions">
                <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($links['contact']); ?>">
                  <?php echo svic_translate_html('guides.support.primary_label'); ?>
                </a>
                <a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url($links['faq']); ?>">
                  <?php echo svic_translate_html('guides.support.secondary_label'); ?>
                </a>
              </div>
            </div>
          </section>
        </article>

        <?php if ($other_resources) : ?>
          <aside class="guides-detail__sidebar guides-article__sidebar">
            <h2 class="guides-detail__sidebar-title"><?php echo svic_translate_html('guides.resources.articles.shared.more_guides_title'); ?></h2>
            <ul class="guides-detail__sidebar-list">
              <?php
              $displayed = 0;
              foreach ($other_resources as $resource) {
                  if ($displayed >= 3) {
                      break;
                  }

                  $resource_link = svic_guides_get_resource_link($resource);
                  if (!$resource_link) {
                      continue;
                  }

                  $displayed++;
                  ?>
                  <li>
                    <a class="guides-detail__sidebar-link" href="<?php echo esc_url($resource_link); ?>">
                      <span><?php echo svic_translate_html($resource['title_key'] ?? ''); ?></span>
                      <span class="guides-detail__sidebar-arrow" aria-hidden="true"></span>
                    </a>
                  </li>
                  <?php
              }
              ?>
            </ul>
          </aside>
        <?php endif; ?>
      </div>
    </main>
    <?php

    get_footer();
    return;
}

global $svic_guides_detail_key;
$svic_guides_detail_key = 'resources';

require get_template_directory() . '/page-guide-section.php';
