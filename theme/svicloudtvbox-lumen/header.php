<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <?php
    if (is_front_page() || is_page_template('front-page.php')) {
        $hero_image_sizes = '(max-width: 600px) 88vw, (max-width: 1024px) 62vw, 640px';
        $hero_image_base = get_template_directory_uri() . '/assets/images/';
        $hero_lcp_srcset = sprintf(
            '%1$s 800w, %2$s 1200w, %3$s 1601w',
            esc_url($hero_image_base . 'hero-voice-assistant-800.webp'),
            esc_url($hero_image_base . 'hero-voice-assistant-1200.webp'),
            esc_url($hero_image_base . 'hero-voice-assistant.webp')
        );
        $hero_lcp_href = esc_url($hero_image_base . 'hero-voice-assistant-1200.webp');
        echo '<link rel="preload" as="image" href="' . $hero_lcp_href . '" imagesrcset="' . $hero_lcp_srcset . '" imagesizes="' . esc_attr($hero_image_sizes) . '" fetchpriority="high" type="image/webp" />';
    }
  ?>
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

    $base_url       = function_exists('svic_current_base_url') ? svic_current_base_url() : home_url('/');
    $english_url    = svic_url_with_lang($base_url, 'en');
    $chinese_url_tw = svic_url_with_lang($base_url, 'zh');
    $chinese_url_cn = svic_url_with_lang($base_url, 'zh-cn');
    $current_lang   = function_exists('svic_current_locale') ? svic_current_locale() : get_locale();
    $logo_classes = ['lumen-header__logo'];
    $english_link_classes = 'lumen-lang-toggle__link' . ($current_lang === 'en_US' ? ' is-active' : '');
    $tw_active = ($current_lang === 'zh_TW');
    $cn_active = ($current_lang === 'zh_CN');
    $chinese_tw_link_classes = 'lumen-lang-toggle__link' . ($tw_active ? ' is-active' : '');
    $chinese_cn_link_classes = 'lumen-lang-toggle__link' . ($cn_active ? ' is-active' : '');

    if ( $has_custom_logo && $custom_logo_id ) {
        $logo_classes[] = 'lumen-header__logo--image';
    }
    $fallback_nav_items = [
        [
            'href'      => svic_url_with_lang( home_url( '/' ) ),
            'label_key' => 'header.nav.home',
        ],
        [
            'href'      => svic_url_with_lang( home_url( '/compare/' ) ),
            'label_key' => 'header.nav.compare',
        ],
        [
            'href'      => svic_url_with_lang( home_url( '/faq/' ) ),
            'label_key' => 'header.nav.faq',
        ],
        [
            'href'      => svic_url_with_lang( home_url( '/product/svicloud-10p-plus/' ) ),
            'label_key' => 'header.nav.ten_p',
        ],
        [
            'href'      => svic_url_with_lang( home_url( '/product/svicloud-10s/' ) ),
            'label_key' => 'header.nav.ten_s',
        ],
        [
            'href'      => svic_url_with_lang( home_url( '/contact/' ) ),
            'label_key' => 'header.nav.concierge',
        ],
        [
            'href'      => svic_url_with_lang( home_url( '/legal-disclaimer/' ) ),
            'label_key' => 'header.nav.legal',
        ],
    ];

  ?>
  <?php svic_render_announcement_bar(); ?>
  <header class="lumen-header lumen-header--transparent" data-lumen-header>
    <div class="lumen-header__inner">
      <a class="lumen-header__brand" href="<?php echo esc_url( svic_url_with_lang( home_url('/') ) ); ?>" aria-label="<?php echo esc_attr( $site_name ); ?>">
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
            'depth'          => 2,
          ]);

          if ($primary_menu) {
            echo $primary_menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
          } else {
        ?>
          <ul class="lumen-nav__list">
            <?php foreach ($fallback_nav_items as $item) : ?>
              <li><a href="<?php echo esc_url($item['href']); ?>"><?php echo svic_translate_html($item['label_key']); ?></a></li>
            <?php endforeach; ?>
          </ul>
        <?php } ?>
      </nav>

      <div class="lumen-header__actions">
        <div class="lumen-lang-toggle lumen-lang-toggle--desktop" role="group" aria-label="<?php esc_attr_e( 'Language selector', 'svicloudtvbox-lumen' ); ?>">
          <a class="<?php echo esc_attr($english_link_classes); ?>" href="<?php echo esc_url($english_url); ?>" data-locale="en_US" hreflang="en-US">EN</a>
          <a class="<?php echo esc_attr($chinese_tw_link_classes); ?>" href="<?php echo esc_url($chinese_url_tw); ?>" data-locale="zh_TW" hreflang="zh-Hant-US">繁</a>
          <a class="<?php echo esc_attr($chinese_cn_link_classes); ?>" href="<?php echo esc_url($chinese_url_cn); ?>" data-locale="zh_CN" hreflang="zh-Hans-US">简</a>
        </div>
        <?php
          echo svic_header_cart_link([
            'class' => 'lumen-cart-link--desktop',
          ]);
        ?>
        <button class="lumen-header__toggle" type="button" aria-expanded="false" aria-controls="lumen-mobile-nav" data-lumen-toggle>
          <span class="screen-reader-text"><?php esc_html_e('Toggle navigation', 'svicloudtvbox-lumen'); ?></span>
          <span class="lumen-header__toggle-line" aria-hidden="true"></span>
        </button>
      </div>
    </div>

    <div
      class="lumen-mobile-nav"
      id="lumen-mobile-nav"
      hidden
      data-submenu-expand="<?php echo svic_translate_attr('header.nav.submenu_expand'); ?>"
      data-submenu-collapse="<?php echo svic_translate_attr('header.nav.submenu_collapse'); ?>"
    >
      <?php
        $mobile_menu = wp_nav_menu([
          'theme_location' => 'primary',
          'container'      => false,
          'menu_class'     => 'lumen-mobile-nav__list',
          'fallback_cb'    => false,
          'echo'           => false,
          'depth'          => 2,
          'items_wrap'     => '<ul class="lumen-mobile-nav__list">%3$s</ul>',
        ]);

        if ($mobile_menu) {
          echo $mobile_menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        } else {
      ?>
        <ul class="lumen-mobile-nav__list">
          <?php foreach ($fallback_nav_items as $item) : ?>
            <li><a href="<?php echo esc_url($item['href']); ?>"><?php echo svic_translate_html($item['label_key']); ?><span aria-hidden="true">→</span></a></li>
          <?php endforeach; ?>
        </ul>
      <?php } ?>

      <div class="lumen-mobile-nav__actions">
        <div class="lumen-lang-toggle lumen-lang-toggle--mobile" role="group" aria-label="<?php esc_attr_e( 'Language selector', 'svicloudtvbox-lumen' ); ?>">
          <a class="<?php echo esc_attr($english_link_classes); ?>" href="<?php echo esc_url($english_url); ?>" data-locale="en_US" hreflang="en-US">EN</a>
          <a class="<?php echo esc_attr($chinese_tw_link_classes); ?>" href="<?php echo esc_url($chinese_url_tw); ?>" data-locale="zh_TW" hreflang="zh-Hant-US">繁</a>
          <a class="<?php echo esc_attr($chinese_cn_link_classes); ?>" href="<?php echo esc_url($chinese_url_cn); ?>" data-locale="zh_CN" hreflang="zh-Hans-US">简</a>
        </div>
        <?php
          echo svic_header_cart_link([
            'class' => 'lumen-cart-link--mobile',
          ]);
        ?>
      </div>
    </div>
  </header>
  <?php svic_render_breadcrumbs(); ?>
