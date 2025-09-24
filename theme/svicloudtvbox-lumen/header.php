<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
  <?php wp_body_open(); ?>
  <?php
    $custom_logo_id  = get_theme_mod( 'custom_logo' );
    $has_custom_logo = function_exists( 'has_custom_logo' ) && has_custom_logo();
    $logo_alt        = $custom_logo_id ? get_post_meta( $custom_logo_id, '_wp_attachment_image_alt', true ) : '';
    $logo_alt        = $logo_alt ? $logo_alt : get_bloginfo( 'name' );
    $site_name       = get_bloginfo( 'name' );

    $request_uri = $_SERVER['REQUEST_URI'] ?? '/';
    $parsed_uri  = wp_parse_url($request_uri);
    $path        = isset($parsed_uri['path']) ? $parsed_uri['path'] : '/';
    $query_args  = [];

    if (!empty($parsed_uri['query'])) {
        parse_str($parsed_uri['query'], $query_args);
    }

    unset($query_args['lang']);

    $base_url     = home_url($path);
    $english_url  = add_query_arg(array_merge($query_args, ['lang' => 'en']), $base_url);
    $chinese_url  = add_query_arg(array_merge($query_args, ['lang' => 'zh']), $base_url);
    $current_lang = function_exists('svic_current_locale') ? svic_current_locale() : get_locale();
    $logo_classes = ['lumen-header__logo'];
    $english_link_classes = 'lumen-lang-toggle__link' . ($current_lang === 'en_US' ? ' is-active' : '');
    $chinese_link_classes = 'lumen-lang-toggle__link' . ($current_lang !== 'en_US' ? ' is-active' : '');

    if ( $has_custom_logo && $custom_logo_id ) {
        $logo_classes[] = 'lumen-header__logo--image';
    }
  ?>
  <header class="lumen-header lumen-header--transparent" data-lumen-header>
    <div class="lumen-header__inner">
      <a class="lumen-header__brand" href="<?php echo esc_url( home_url('/') ); ?>" aria-label="<?php echo esc_attr( $site_name ); ?>">
        <span class="<?php echo esc_attr( implode( ' ', $logo_classes ) ); ?>">
          <?php if ( $has_custom_logo && $custom_logo_id ) : ?>
            <?php echo wp_get_attachment_image( $custom_logo_id, 'full', false, [
              'class'   => 'lumen-header__logo-image',
              'alt'     => esc_attr( $logo_alt ),
              'loading' => 'lazy',
            ] ); ?>
          <?php else : ?>
            <span class="lumen-header__logo-initials">SV</span>
          <?php endif; ?>
        </span>
        <span class="screen-reader-text"><?php echo esc_html( $site_name ); ?></span>
      </a>

      <nav class="lumen-nav" aria-label="<?php esc_attr_e('Primary navigation', 'svicloudtvbox-lumen'); ?>">
        <?php
          $primary_menu = wp_nav_menu([
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'lumen-nav__list',
            'fallback_cb'    => false,
            'echo'           => false,
            'depth'          => 1,
          ]);

          if ($primary_menu) {
            echo $primary_menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
          } else {
        ?>
          <ul class="lumen-nav__list">
            <li><a href="<?php echo esc_url( home_url('/') ); ?>"><?php esc_html_e('Home', 'svicloudtvbox-lumen'); ?></a></li>
            <li><a href="<?php echo esc_url( home_url('/compare') ); ?>"><?php esc_html_e('Compare', 'svicloudtvbox-lumen'); ?></a></li>
            <li><a href="<?php echo esc_url( home_url('/product/svicloud-10p-plus/') ); ?>"><?php esc_html_e('10P+', 'svicloudtvbox-lumen'); ?></a></li>
            <li><a href="<?php echo esc_url( home_url('/product/svicloud-10s/') ); ?>"><?php esc_html_e('10S', 'svicloudtvbox-lumen'); ?></a></li>
            <li><a href="<?php echo esc_url( home_url('/contact') ); ?>"><?php esc_html_e('Concierge', 'svicloudtvbox-lumen'); ?></a></li>
          </ul>
        <?php } ?>
      </nav>

      <div class="lumen-header__actions">
        <div class="lumen-header__pill-group">
          <div class="lumen-lang-toggle" role="group" aria-label="<?php esc_attr_e( 'Language selector', 'svicloudtvbox-lumen' ); ?>">
            <a class="<?php echo esc_attr($english_link_classes); ?>" href="<?php echo esc_url($english_url); ?>">EN</a>
            <a class="<?php echo esc_attr($chinese_link_classes); ?>" href="<?php echo esc_url($chinese_url); ?>">中文</a>
          </div>
          <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url( function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart') ); ?>">
            <?php
              /* translators: Header cart CTA label. Non-breaking space keeps text on one line. */
              echo wp_kses_post( __( 'View&nbsp;Cart', 'svicloudtvbox-lumen' ) );
            ?>
          </a>
        </div>
        <button class="lumen-header__toggle" type="button" aria-expanded="false" aria-controls="lumen-mobile-nav" data-lumen-toggle>
          <span class="screen-reader-text"><?php esc_html_e('Toggle navigation', 'svicloudtvbox-lumen'); ?></span>
          <span class="lumen-header__toggle-line" aria-hidden="true"></span>
        </button>
      </div>
    </div>

    <div class="lumen-mobile-nav" id="lumen-mobile-nav" hidden>
      <?php
        $mobile_menu = wp_nav_menu([
          'theme_location' => 'primary',
          'container'      => false,
          'menu_class'     => 'lumen-mobile-nav__list',
          'fallback_cb'    => false,
          'echo'           => false,
          'depth'          => 1,
          'items_wrap'     => '<ul class="lumen-mobile-nav__list">%3$s</ul>',
        ]);

        if ($mobile_menu) {
          echo $mobile_menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        } else {
      ?>
        <ul class="lumen-mobile-nav__list">
          <li><a href="<?php echo esc_url( home_url('/') ); ?>"><?php esc_html_e('Home', 'svicloudtvbox-lumen'); ?><span aria-hidden="true">→</span></a></li>
          <li><a href="<?php echo esc_url( home_url('/compare') ); ?>"><?php esc_html_e('Compare', 'svicloudtvbox-lumen'); ?><span aria-hidden="true">→</span></a></li>
          <li><a href="<?php echo esc_url( home_url('/product/svicloud-10p-plus/') ); ?>"><?php esc_html_e('10P+', 'svicloudtvbox-lumen'); ?><span aria-hidden="true">→</span></a></li>
          <li><a href="<?php echo esc_url( home_url('/product/svicloud-10s/') ); ?>"><?php esc_html_e('10S', 'svicloudtvbox-lumen'); ?><span aria-hidden="true">→</span></a></li>
          <li><a href="<?php echo esc_url( home_url('/contact') ); ?>"><?php esc_html_e('Concierge', 'svicloudtvbox-lumen'); ?><span aria-hidden="true">→</span></a></li>
        </ul>
      <?php } ?>

      <div class="lumen-mobile-nav__actions">
        <div class="lumen-lang-toggle lumen-lang-toggle--mobile" role="group" aria-label="<?php esc_attr_e( 'Language selector', 'svicloudtvbox-lumen' ); ?>">
          <a class="<?php echo esc_attr($english_link_classes); ?>" href="<?php echo esc_url($english_url); ?>">EN</a>
          <a class="<?php echo esc_attr($chinese_link_classes); ?>" href="<?php echo esc_url($chinese_url); ?>">中文</a>
        </div>
        <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url( function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart') ); ?>">
          <?php
            /* translators: Header cart CTA label. Non-breaking space keeps text on one line. */
            echo wp_kses_post( __( 'View&nbsp;Cart', 'svicloudtvbox-lumen' ) );
          ?>
        </a>
      </div>
    </div>
  </header>
