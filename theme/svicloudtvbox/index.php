<?php get_header(); ?>
<main class="page-shell">
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class('page-content'); ?>>
      <header class="page-hero">
        <h1 class="page-title"><?php the_title(); ?></h1>
      </header>
      <div class="entry-content"><?php the_content(); ?></div>
    </article>
  <?php endwhile; else: ?>
    <section class="page-hero">
      <span class="badge badge-muted"><?php esc_html_e('Oops!', 'svicloudtvbox'); ?></span>
      <h1 class="page-title"><?php esc_html_e('We couldn't find that page.', 'svicloudtvbox'); ?></h1>
      <p class="page-subtitle"><?php esc_html_e('The link might be outdated or the page is still being built. Try browsing the links below.', 'svicloudtvbox'); ?></p>
      <div class="comparison-cta">
        <a class="btn btn-primary" href="<?php echo esc_url( home_url('/') ); ?>"><?php esc_html_e('Go Home', 'svicloudtvbox'); ?></a>
        <a class="btn btn-outline" href="<?php echo esc_url( home_url('/shop') ); ?>"><?php esc_html_e('Browse Shop', 'svicloudtvbox'); ?></a>
      </div>
    </section>
  <?php endif; ?>
</main>
<?php get_footer(); ?>
