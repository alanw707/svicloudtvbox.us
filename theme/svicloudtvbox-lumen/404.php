<?php
/**
 * 404 Page
 */
get_header();
?>

<main class="page-shell" style="text-align:center;">
  <span class="badge badge-muted">Oops!</span>
  <h1 class="page-title">We couldn't find that page.</h1>
  <p class="page-subtitle">The link might be outdated or the page is still being built. Explore our products or head back to the homepage.</p>
  <div class="comparison-cta" style="margin-top:1.5rem;">
    <a class="btn btn-primary" href="<?php echo esc_url( home_url('/shop') ); ?>">Shop Devices</a>
    <a class="btn btn-outline" href="<?php echo esc_url( home_url('/') ); ?>">Return Home</a>
  </div>
</main>

<?php get_footer(); ?>
