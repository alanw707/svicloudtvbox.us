<?php get_header(); ?>
<main class="page-shell">
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class('page-content'); ?>>
      <header class="page-hero">
        <h1 class="page-title"><?php the_title(); ?></h1>
        <?php if (has_excerpt()) : ?>
          <p class="page-subtitle"><?php echo esc_html(get_the_excerpt()); ?></p>
        <?php endif; ?>
      </header>
      <div class="entry-content"><?php the_content(); ?></div>
    </article>
  <?php endwhile; endif; ?>
</main>
<?php get_footer(); ?>
