<?php
/**
 * WooCommerce Archive (Shop)
 */

defined('ABSPATH') || exit;

get_header('shop');

$products = [];
if (class_exists('WooCommerce')) {
    $p10p = svic_get_product_by_slug('svicloud-10p-plus');
    $p10s = svic_get_product_by_slug('svicloud-10s');
    if ($p10p) $products['10p'] = $p10p;
    if ($p10s) $products['10s'] = $p10s;
}
?>

<main class="page-shell">
  <header class="page-hero" style="text-align:left;">
    <span class="badge badge-muted">Shop</span>
    <h1 class="page-title">SVICLOUD TV Boxes</h1>
    <p class="page-subtitle">Authorized U.S. dealer with fast domestic shipping, 1-year U.S. warranty, and English/中文 support.</p>
  </header>

  <section class="product-showcase">
    <div class="grid-toggle" aria-label="Change product grid layout">
      <button class="grid-btn active" data-mode="2col" type="button">2 column</button>
      <button class="grid-btn" data-mode="4col" type="button">4 column</button>
      <button class="grid-btn" data-mode="list" type="button">List</button>
    </div>

    <div class="product-grid grid-2">
      <?php if (!empty($products)) : ?>
        <?php if (isset($products['10p'])) svic_render_product_card($products['10p']); ?>
        <?php if (isset($products['10s'])) svic_render_product_card($products['10s']); ?>
      <?php else : ?>
        <article class="product-card">
          <a class="product-image" href="<?php echo esc_url( home_url('/product/svicloud-10p-plus') ); ?>">
            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/svicloud-10p-plus.png' ); ?>" alt="SVICLOUD 10P+ TV Box" />
          </a>
          <h3 class="pcard-title"><a href="<?php echo esc_url( home_url('/product/svicloud-10p-plus') ); ?>">SVICLOUD 10P+</a></h3>
          <div class="pcard-meta"><span class="pcard-price">$248.99</span></div>
          <p class="product-blurb">Flagship 4K HDR box with 4GB RAM, karaoke & kids apps, and AI voice remote.</p>
          <div class="pcard-actions"><a class="btn btn-primary btn-cta" href="<?php echo esc_url( home_url('/shop') ); ?>">Shop 10P+</a></div>
        </article>
        <article class="product-card">
          <a class="product-image" href="<?php echo esc_url( home_url('/product/svicloud-10s') ); ?>">
            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/svicloud-10s.png' ); ?>" alt="SVICLOUD 10S TV Box" />
          </a>
          <h3 class="pcard-title"><a href="<?php echo esc_url( home_url('/product/svicloud-10s') ); ?>">SVICLOUD 10S</a></h3>
          <div class="pcard-meta"><span class="pcard-price">$183.99</span></div>
          <p class="product-blurb">Value model with 2GB RAM, 32GB storage, 4K HDR playback, and AI voice remote.</p>
          <div class="pcard-actions"><a class="btn btn-primary btn-cta" href="<?php echo esc_url( home_url('/shop') ); ?>">Shop 10S</a></div>
        </article>
      <?php endif; ?>
    </div>
  </section>
</main>

<?php get_footer('shop'); ?>
