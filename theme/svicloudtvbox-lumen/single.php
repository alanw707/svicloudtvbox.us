<?php
/**
 * Template for single blog posts.
 *
 * @package SVICloudTVBoxClassic
 */

get_header();

?>

<main class="page-shell blog-shell">
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <?php
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
      $hero_title = function_exists('svic_post_title') ? svic_post_title($post_id) : get_the_title($post_id);
      $hero_excerpt = '';
      if (function_exists('svic_post_locale_meta')) {
          $hero_excerpt = svic_post_locale_meta($post_id, 'description');
      }
      if ($hero_excerpt === '' && has_excerpt()) {
          $hero_excerpt = get_the_excerpt();
      }
      $raw_content        = get_post_field('post_content', $post_id);
      $localized_content  = function_exists('svic_post_localized_content') ? svic_post_localized_content($post_id) : '';
      $rendered_content   = $localized_content !== ''
          ? $localized_content
          : (is_string($raw_content) ? apply_filters('the_content', $raw_content) : '');
      $featured_image_url = has_post_thumbnail($post_id) ? get_the_post_thumbnail_url($post_id, 'full') : null;
      $content_segments   = svic_extract_intro_media_blocks($rendered_content, $featured_image_url);
      $hero_highlights    = isset($content_segments['blocks']) ? $content_segments['blocks'] : [];
      $article_body       = isset($content_segments['content']) ? $content_segments['content'] : '';
      if ($article_body === '' && $rendered_content !== '' && empty($hero_highlights)) {
          $article_body = $rendered_content;
      }
      // Blog content imported from automation can include its own H1. The
      // template already outputs the canonical article H1 above the body, so
      // downgrade body H1s to H2s to keep a single primary heading per post.
      $article_body = preg_replace('/<h1(\s[^>]*)?>/i', '<h2$1>', (string) $article_body);
      $article_body = preg_replace('/<\/h1>/i', '</h2>', (string) $article_body);
    ?>
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
        <h1 class="blog-hero__title"><?php echo esc_html($hero_title); ?></h1>
        <?php if ($hero_excerpt !== '') : ?>
          <p class="blog-hero__excerpt"><?php echo esc_html($hero_excerpt); ?></p>
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
        <?php if (!empty($hero_highlights)) : ?>
          <section class="blog-hero-highlights" aria-label="<?php esc_attr_e('Key delivery highlights', 'svicloudtvbox-lumen'); ?>">
            <?php foreach ($hero_highlights as $highlight_block) : ?>
              <div class="blog-hero-highlights__item">
                <?php echo $highlight_block; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
              </div>
            <?php endforeach; ?>
          </section>
        <?php endif; ?>
        <?php echo $article_body; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
      </div>

      <footer class="blog-article__footer">
        <div class="blog-article__cta">
          <?php
            $cta_product_10p = class_exists('WooCommerce') && function_exists('svic_get_product_by_slug') ? svic_get_product_by_slug('svicloud-10p-plus') : null;
            $cta_product_10s = class_exists('WooCommerce') && function_exists('svic_get_product_by_slug') ? svic_get_product_by_slug('svicloud-10s') : null;
            $cta_product_10p_url = $cta_product_10p ? get_permalink($cta_product_10p->get_id()) : home_url('/product/svicloud-10p-plus/');
            $cta_product_10s_url = $cta_product_10s ? get_permalink($cta_product_10s->get_id()) : home_url('/product/svicloud-10s/');
          ?>
          <h2 class="blog-article__cta-title"><?php echo svic_translate_html('blog.cta.title'); ?></h2>
          <p class="blog-article__cta-copy">
            <?php echo svic_translate_html('blog.cta.copy'); ?>
          </p>
          <div class="blog-article__cta-actions">
            <a class="btn btn-primary" href="<?php echo esc_url(svic_url_with_lang($cta_product_10p_url)); ?>">
              <?php echo svic_translate_html('compare.final_cta.cta_10p'); ?>
            </a>
            <a class="btn btn-outline" href="<?php echo esc_url(svic_url_with_lang($cta_product_10s_url)); ?>">
              <?php echo svic_translate_html('compare.final_cta.cta_10s'); ?>
            </a>
            <a class="btn btn-primary" href="<?php echo esc_url(svic_url_with_lang(home_url('/compare/'))); ?>">
              <?php echo svic_translate_html('blog.cta.primary_label'); ?>
            </a>
            <a class="btn btn-outline" href="<?php echo esc_url(svic_url_with_lang(home_url('/support/'))); ?>">
              <?php echo svic_translate_html('blog.cta.secondary_label'); ?>
            </a>
            <a class="blog-article__cta-text" href="<?php echo esc_url(svic_url_with_lang(home_url('/contact/'))); ?>">
              <?php echo svic_translate_html('contact.hero.title'); ?>
            </a>
          </div>
        </div>
      </footer>
    </article>
  <?php endwhile; endif; ?>
</main>

<?php get_footer(); ?>
