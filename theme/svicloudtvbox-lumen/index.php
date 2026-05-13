<?php get_header(); ?>
<main class="page-shell">
  <?php if (have_posts()) : ?>
    <?php if (is_archive() || is_search()) : ?>
      <header class="page-hero">
        <h1 class="page-title">
          <?php
          if (is_search()) {
              printf(
                  /* translators: %s: search query */
                  esc_html__('Search results for “%s”', 'svicloudtvbox-lumen'),
                  esc_html(get_search_query())
              );
          } else {
              echo esc_html(get_the_archive_title());
          }
          ?>
        </h1>
        <?php if (get_the_archive_description()) : ?>
          <div class="page-subtitle"><?php echo wp_kses_post(get_the_archive_description()); ?></div>
        <?php endif; ?>
      </header>
    <?php endif; ?>

    <?php while (have_posts()) : the_post(); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class('page-content'); ?>>
        <header class="page-hero">
          <?php if (is_singular()) : ?>
            <h1 class="page-title"><?php the_title(); ?></h1>
          <?php else : ?>
            <h2 class="page-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
          <?php endif; ?>
        </header>
        <div class="entry-content">
          <?php if (is_singular()) : ?>
            <?php the_content(); ?>
          <?php else : ?>
            <?php the_excerpt(); ?>
          <?php endif; ?>
        </div>
      </article>
    <?php endwhile; ?>
  <?php else: ?>
    <section class="page-hero">
      <span class="badge badge-muted"><?php esc_html_e('Oops!', 'svicloudtvbox-lumen'); ?></span>
      <h1 class="page-title"><?php esc_html_e("We couldn't find that page.", 'svicloudtvbox-lumen'); ?></h1>
      <p class="page-subtitle"><?php esc_html_e('The link might be outdated or the page is still being built. Try browsing the links below.', 'svicloudtvbox-lumen'); ?></p>
      <div class="comparison-cta">
        <a class="btn btn-primary" href="<?php echo esc_url( svic_url_with_lang( home_url('/') ) ); ?>"><?php esc_html_e('Go Home', 'svicloudtvbox-lumen'); ?></a>
        <a class="btn btn-outline" href="<?php echo esc_url( svic_url_with_lang( home_url('/shop') ) ); ?>"><?php esc_html_e('Browse Shop', 'svicloudtvbox-lumen'); ?></a>
      </div>
    </section>
  <?php endif; ?>
</main>
<?php get_footer(); ?>
