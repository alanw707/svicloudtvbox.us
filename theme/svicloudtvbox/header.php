<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
  <?php wp_body_open(); ?>
  <header class="site-header">
    <div class="container">
      <div class="site-logo">
        <?php if (function_exists('the_custom_logo') && has_custom_logo()) : ?>
          <?php the_custom_logo(); ?>
        <?php else : ?>
          <a class="site-logo-link" href="<?php echo esc_url(home_url('/')); ?>">
            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/site-logo.png' ); ?>" alt="<?php echo esc_attr( get_bloginfo('name') ); ?>" />
          </a>
        <?php endif; ?>
      </div>
      <nav class="primary-nav" aria-label="Primary">
        <?php
          $menu = wp_nav_menu([
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'menu',
            'fallback_cb'    => false,
            'echo'           => false,
          ]);

          if ($menu) {
            echo $menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
          } else {
        ?>
            <ul class="menu default-menu">
              <li><a href="<?php echo esc_url( home_url('/') ); ?>">Home</a></li>
              <li><a href="<?php echo esc_url( home_url('/shop') ); ?>">Shop</a></li>
              <li><a href="#pricing">Pricing</a></li>
              <li><a href="<?php echo esc_url( home_url('/compare') ); ?>">Compare</a></li>
              <li><a href="<?php echo esc_url( home_url('/contact') ); ?>">Support</a></li>
            </ul>
        <?php } ?>
      </nav>
      <div class="header-actions">
        <button class="mobile-menu-toggle" aria-label="Open menu" aria-expanded="false" title="Menu">☰</button>
      </div>
    </div>
  </header>
