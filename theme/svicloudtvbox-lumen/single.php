<?php
/**
 * Template for single blog posts.
 *
 * @package SVICloudTVBoxClassic
 */

get_header();

$post_id        = get_the_ID();
$categories     = get_the_category($post_id);
$primary_cat    = $categories ? $categories[0] : null;
$published_time = get_the_date('', $post_id);
$updated_time   = get_the_modified_date('', $post_id);
$reading_time   = svic_estimated_read_time($post_id);
$reading_label  = sprintf(
    /* translators: %d: estimated reading time in minutes */
    esc_html__('%d min read', 'svicloudtvbox-lumen'),
    $reading_time
);
?>

<main class="page-shell blog-shell">
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class('blog-article'); ?>>
      <header class="blog-hero">
        <div class="blog-hero__meta">
          <?php if ($primary_cat instanceof WP_Term) : ?>
            <span class="blog-hero__chip"><?php echo esc_html($primary_cat->name); ?></span>
          <?php endif; ?>
          <time datetime="<?php echo esc_attr(get_the_date(DATE_W3C, $post_id)); ?>" class="blog-hero__time">
            <?php echo esc_html($published_time); ?>
          </time>
          <?php if ($updated_time && $updated_time !== $published_time) : ?>
            <span class="blog-hero__update"><?php printf(esc_html__('Updated %s', 'svicloudtvbox-lumen'), esc_html($updated_time)); ?></span>
          <?php endif; ?>
          <span class="blog-hero__reading-time"><?php echo esc_html($reading_label); ?></span>
        </div>
        <h1 class="blog-hero__title"><?php the_title(); ?></h1>
        <?php if (has_excerpt()) : ?>
          <p class="blog-hero__excerpt"><?php echo esc_html(get_the_excerpt()); ?></p>
        <?php endif; ?>
      </header>

      <div class="blog-article__content entry-content">
        <?php if (has_post_thumbnail()) : ?>
          <figure class="blog-article__featured">
            <?php
              echo get_the_post_thumbnail(
                  $post_id,
                  'full',
                  [
                      'class'   => 'blog-article__featured-image',
                      'loading' => 'lazy',
                  ]
              ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

              $featured_caption = wp_get_attachment_caption(get_post_thumbnail_id($post_id));
            ?>
            <?php if (!empty($featured_caption)) : ?>
              <figcaption class="blog-article__featured-caption">
                <?php echo esc_html($featured_caption); ?>
              </figcaption>
            <?php endif; ?>
          </figure>
        <?php endif; ?>
        <?php the_content(); ?>
      </div>

      <footer class="blog-article__footer">
        <div class="blog-article__cta">
          <h2 class="blog-article__cta-title"><?php echo svic_translate_html('blog.cta.title'); ?></h2>
          <p class="blog-article__cta-copy">
            <?php echo svic_translate_html('blog.cta.copy'); ?>
          </p>
          <div class="blog-article__cta-actions">
            <a class="btn btn-primary" href="<?php echo esc_url(svic_url_with_lang(home_url('/compare/'))); ?>">
              <?php echo svic_translate_html('blog.cta.primary_label'); ?>
            </a>
            <a class="btn btn-outline" href="<?php echo esc_url(svic_url_with_lang(home_url('/support/'))); ?>">
              <?php echo svic_translate_html('blog.cta.secondary_label'); ?>
            </a>
          </div>
        </div>
      </footer>
    </article>
  <?php endwhile; endif; ?>
</main>

<?php get_footer(); ?>
