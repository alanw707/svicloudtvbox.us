<?php
/**
 * Template for the main blog index (/blog) showing latest posts.
 *
 * @package SVICloudTVBoxClassic
 */

get_header();

$blog_page_id = (int) get_option('page_for_posts');

$hero_title = $blog_page_id ? get_the_title($blog_page_id) : svic_translate('blog.index.hero.title');
if (!is_string($hero_title) || $hero_title === '') {
    $hero_title = svic_translate('blog.index.hero.title');
}

$hero_summary_source = '';
if ($blog_page_id) {
    $maybe_excerpt = get_post_field('post_excerpt', $blog_page_id);
    if (is_string($maybe_excerpt)) {
        $hero_summary_source = trim($maybe_excerpt);
    }
}

if ($hero_summary_source === '') {
    $hero_summary_source = svic_translate('blog.index.hero.lead');
}

$hero_summary = wpautop(wp_kses_post($hero_summary_source));

$hero_eyebrow = svic_translate('blog.index.hero.eyebrow');

$hero_primary_label   = svic_translate('blog.index.hero.primary_label');
$hero_secondary_label = svic_translate('blog.index.hero.secondary_label');

$hero_primary_url   = svic_url_with_lang(home_url('/compare/'));
$hero_secondary_url = svic_url_with_lang(home_url('/support/'));
$hero_tertiary_url  = svic_url_with_lang(home_url('/contact/'));

$highlight_keys = ['cadence', 'categories', 'support'];
$highlights     = [];
foreach ($highlight_keys as $slug) {
    $label = svic_translate('blog.index.highlights.' . $slug . '.label');
    $copy  = svic_translate('blog.index.highlights.' . $slug . '.copy');

    if (($label === '' || !is_string($label)) && ($copy === '' || !is_string($copy))) {
        continue;
    }

    $highlights[] = [
        'label' => $label,
        'copy'  => $copy,
    ];
}

$list_label = svic_translate('blog.index.list_label');

?>

<main class="page-shell blog-shell blog-archive">
  <div class="blog-archive__container">
    <section class="blog-archive__hero" aria-labelledby="blog-archive-title">
      <div class="blog-hero__main">
        <?php if ($hero_eyebrow !== '') : ?>
          <div class="blog-archive__eyebrow"><?php echo esc_html($hero_eyebrow); ?></div>
        <?php endif; ?>
        <h1 id="blog-archive-title" class="blog-archive__title"><?php echo esc_html($hero_title); ?></h1>
        <div class="blog-archive__lead blog-hero__lead"><?php echo $hero_summary; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
        <?php if ($hero_primary_label !== '' || $hero_secondary_label !== '') : ?>
          <div class="blog-archive__actions blog-hero__actions">
            <?php if ($hero_primary_label !== '') : ?>
              <a class="btn btn-primary" href="<?php echo esc_url($hero_primary_url); ?>"><?php echo esc_html($hero_primary_label); ?></a>
            <?php endif; ?>
            <?php if ($hero_secondary_label !== '') : ?>
              <a class="btn btn-outline" href="<?php echo esc_url($hero_secondary_url); ?>"><?php echo esc_html($hero_secondary_label); ?></a>
            <?php endif; ?>
            <a class="blog-archive__textlink" href="<?php echo esc_url($hero_tertiary_url); ?>"><?php echo svic_translate_html('contact.hero.primary_cta'); ?></a>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <?php if (have_posts()) : ?>
      <section class="blog-grid" aria-label="<?php echo esc_attr($list_label !== '' ? $list_label : __('Latest posts', svic_text_domain())); ?>">
        <h2 class="screen-reader-text"><?php echo esc_html($list_label !== '' ? $list_label : __('Latest posts', svic_text_domain())); ?></h2>
        <div class="blog-grid__list">
          <?php while (have_posts()) : the_post();
            $post_id      = get_the_ID();
            $permalink    = svic_url_with_lang(get_permalink());
            $categories   = get_the_category($post_id);
            $primary_cat  = $categories ? $categories[0] : null;
            $reading_time = svic_estimated_read_time($post_id);
            $reading_label = sprintf(
                /* translators: %d: estimated reading time in minutes */
                esc_html__('%d min read', 'svicloudtvbox-lumen'),
                $reading_time
            );
            $published_time_display = get_the_date('', $post_id);
            $published_time_attr    = get_the_date(DATE_W3C, $post_id);
            $updated_time_display   = get_the_modified_date('', $post_id);
            $show_updated           = $updated_time_display && $updated_time_display !== $published_time_display;
            $updated_label          = $show_updated ? svic_translate_html('blog.card.updated', ['date' => $updated_time_display]) : '';

            $excerpt_source = '';
            if (function_exists('svic_post_locale_meta')) {
                $excerpt_source = svic_post_locale_meta($post_id, 'description');
                if ($excerpt_source === '') {
                    $excerpt_source = svic_post_locale_meta($post_id, 'content');
                }
            }

            if ($excerpt_source !== '') {
                $trimmed_excerpt = wp_trim_words(wp_strip_all_tags($excerpt_source), 26, '…');
            } else {
                $raw_excerpt     = get_the_excerpt($post_id);
                $trimmed_excerpt = wp_trim_words(wp_strip_all_tags((string) $raw_excerpt), 26, '…');
            }

            $thumbnail_html = svic_post_card_image(
                $post_id,
                'large',
                [
                    'class' => 'blog-card__image',
                ]
            );
            ?>
            <article class="blog-card">
              <a class="blog-card__link" href="<?php echo esc_url($permalink); ?>">
                <figure class="blog-card__media">
                  <?php if ($thumbnail_html) : ?>
                    <?php echo $thumbnail_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                  <?php else : ?>
                    <div class="blog-card__media-placeholder" aria-hidden="true"></div>
                  <?php endif; ?>
                </figure>
                <div class="blog-card__body">
                  <div class="blog-card__meta">
                    <?php if ($primary_cat instanceof WP_Term) : ?>
                      <span class="blog-card__chip"><?php echo esc_html(svic_category_label($primary_cat)); ?></span>
                    <?php endif; ?>
                    <time datetime="<?php echo esc_attr($published_time_attr); ?>" class="blog-card__time"><?php echo esc_html($published_time_display); ?></time>
                    <span class="blog-card__reading-time"><?php echo esc_html($reading_label); ?></span>
                  </div>
                  <h3 class="blog-card__title"><?php echo esc_html(svic_post_title($post_id)); ?></h3>
                  <?php if ($trimmed_excerpt !== '') : ?>
                    <p class="blog-card__excerpt"><?php echo esc_html($trimmed_excerpt); ?></p>
                  <?php endif; ?>
                  <?php if ($updated_label !== '') : ?>
                    <span class="blog-card__updated"><?php echo $updated_label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                  <?php endif; ?>
                </div>
              </a>
            </article>
          <?php endwhile; ?>
        </div>
      </section>

      <?php
      $prev_label = svic_translate('blog.index.pagination.previous');
      $next_label = svic_translate('blog.index.pagination.next');

      $pagination_links = paginate_links([
          'type'      => 'array',
          'mid_size'  => 1,
          'prev_text' => ($prev_label !== '' ? '← ' . $prev_label : '← ' . __('Newer posts', svic_text_domain())),
          'next_text' => ($next_label !== '' ? $next_label . ' →' : __('Older posts', svic_text_domain()) . ' →'),
      ]);

      if (is_array($pagination_links) && $pagination_links) :
          $pagination_label = svic_translate('blog.index.pagination.label');
          ?>
          <nav class="blog-pagination" aria-label="<?php echo esc_attr($pagination_label !== '' ? $pagination_label : __('Pagination', svic_text_domain())); ?>">
            <ul class="blog-pagination__list">
              <?php foreach ($pagination_links as $link) : ?>
                <li class="blog-pagination__item"><?php echo wp_kses_post($link); ?></li>
              <?php endforeach; ?>
            </ul>
          </nav>
      <?php endif; ?>

    <?php else : ?>
      <section class="blog-empty" aria-labelledby="blog-empty-title">
        <span class="blog-empty__eyebrow"><?php echo esc_html($hero_eyebrow); ?></span>
        <h2 id="blog-empty-title" class="blog-empty__title"><?php echo svic_translate_html('blog.index.empty_title'); ?></h2>
        <p class="blog-empty__copy"><?php echo svic_translate_html('blog.index.empty_copy'); ?></p>
        <a class="btn btn-primary" href="<?php echo esc_url(svic_url_with_lang(home_url('/'))); ?>"><?php echo svic_translate_html('blog.index.empty_cta'); ?></a>
      </section>
    <?php endif; ?>
  </div>
</main>

<?php
get_footer();
