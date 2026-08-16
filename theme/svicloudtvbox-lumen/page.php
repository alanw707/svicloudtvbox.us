<?php get_header(); ?>
<main id="main-content" class="page-shell" tabindex="-1">
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class('page-content'); ?>>
      <?php
        $is_cart_page = function_exists('is_cart') && is_cart();
        $is_checkout_page = function_exists('is_checkout') && is_checkout();
        $is_order_tracking_page = false;

        if (function_exists('is_wc_endpoint_url') && is_wc_endpoint_url('order-tracking')) {
            $is_order_tracking_page = true;
        } elseif (! $is_cart_page && ! $is_checkout_page) {
            $current_page = get_post();
            if ($current_page instanceof WP_Post) {
                $raw_content = $current_page->post_content;
                if (function_exists('has_shortcode') && has_shortcode($raw_content, 'woocommerce_order_tracking')) {
                    $is_order_tracking_page = true;
                } elseif (function_exists('has_block') && has_block('woocommerce/order-tracking', $current_page)) {
                    $is_order_tracking_page = true;
                }
            }
        }
      ?>
      <?php if (!($is_cart_page || $is_checkout_page || $is_order_tracking_page)) : ?>
        <header class="page-hero">
          <h1 class="page-title"><?php the_title(); ?></h1>
          <?php if (has_excerpt()) : ?>
            <p class="page-subtitle"><?php echo esc_html(get_the_excerpt()); ?></p>
          <?php endif; ?>
        </header>
      <?php endif; ?>
      <div class="entry-content"><?php the_content(); ?></div>
    </article>
  <?php endwhile; endif; ?>
</main>
<?php get_footer(); ?>
