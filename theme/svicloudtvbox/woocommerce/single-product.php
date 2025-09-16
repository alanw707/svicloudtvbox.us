<?php
/**
 * WooCommerce Single Product (Classic)
 */

defined('ABSPATH') || exit;

get_header('shop');

global $product;
if (!$product) { wc_get_template('single-product.php'); exit; }

// Images
$image_id = $product->get_image_id();
$gallery = method_exists($product, 'get_gallery_image_ids') ? (array)$product->get_gallery_image_ids() : [];

?>

<main class="page-shell">
  <div class="single-product-hero">
    <div>
      <div class="gallery-main">
        <?php if ($image_id) echo wp_get_attachment_image($image_id, 'large', false, ['style'=>'width:100%;height:100%;object-fit:contain']); ?>
      </div>
      <?php if ($gallery): ?>
        <div class="gallery-thumbs">
          <?php foreach ($gallery as $gid): ?>
            <?php echo wp_get_attachment_image($gid, 'thumbnail'); ?>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div>
      <div class="badge-row">
        <span class="badge">Authorized Dealer</span>
        <span class="badge">Ships from USA</span>
        <span class="badge">1-Year US Warranty</span>
      </div>
      <h1 class="h2 spacing-tight"><?php the_title(); ?></h1>
      <div class="price spacing-normal"><?php echo $product->get_price_html(); ?></div>

      <div class="spacing-normal">
        <?php woocommerce_template_single_add_to_cart(); ?>
      </div>

      <div class="text-small" style="color:var(--subtle);">
        Secure checkout • Free US shipping • English/中文 support
      </div>

      <div class="spacing-loose">
        <?php woocommerce_template_single_meta(); ?>
      </div>
    </div>
  </div>

  <section style="margin-top:2rem;">
    <h2 class="h3 spacing-normal">Description</h2>
    <div class="entry-content">
      <?php the_content(); ?>
    </div>
  </section>
</main>

<?php get_footer('shop'); ?>

