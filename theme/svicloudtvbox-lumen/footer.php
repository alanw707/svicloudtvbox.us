<?php
  $custom_logo_id  = get_theme_mod( 'custom_logo' );
  $has_custom_logo = function_exists( 'has_custom_logo' ) && has_custom_logo();
  $site_name       = get_bloginfo( 'name' );
  $logo_alt        = $custom_logo_id ? get_post_meta( $custom_logo_id, '_wp_attachment_image_alt', true ) : '';
  $logo_alt        = $logo_alt ? $logo_alt : $site_name;

  $footer_badge_keys = [
    'footer.badges.ships',
    'footer.badges.warranty',
    'footer.badges.concierge',
  ];

  $footer_benefit_map = [
    [
      'icon'      => 'icon-truck.svg',
      'label_key' => 'footer.benefits.shipping.label',
      'desc_key'  => 'footer.benefits.shipping.description',
    ],
    [
      'icon'      => 'icon-lock.svg',
      'label_key' => 'footer.benefits.dealer.label',
      'desc_key'  => 'footer.benefits.dealer.description',
    ],
    [
      'icon'      => 'icon-family.svg',
      'label_key' => 'footer.benefits.concierge.label',
      'desc_key'  => 'footer.benefits.concierge.description',
    ],
  ];
?>
  <footer class="site-footer" role="contentinfo">
    <div class="site-footer__glow" aria-hidden="true"></div>
    <div class="container footer-shell">
      <div class="footer-top">
        <div class="footer-brand">
          <a class="footer-logo" href="<?php echo esc_url( svic_url_with_lang( home_url( '/' ) ) ); ?>" aria-label="<?php echo esc_attr( $site_name ); ?>">
            <span class="footer-logo__mark">
              <?php if ( $has_custom_logo && $custom_logo_id ) : ?>
                <?php echo wp_get_attachment_image( $custom_logo_id, 'full', false, [
                  'class'   => 'footer-logo__image footer-logo__image--custom',
                  'alt'     => esc_attr( $logo_alt ),
                  'loading' => 'lazy',
                ] ); ?>
              <?php else : ?>
                <img class="footer-logo__image footer-logo__image--fallback" src="<?php echo esc_url( svic_theme_image_uri('/assets/images/favicon.png') ); ?>" alt="<?php echo esc_attr( $site_name ); ?>" loading="lazy" />
              <?php endif; ?>
            </span>
            <span class="footer-logo__text">
              <span class="footer-logo__tagline"><?php echo svic_translate_html('footer.tagline'); ?></span>
            </span>
          </a>
          <p class="footer-brand__summary"><?php echo svic_translate_html('footer.summary'); ?></p>
          <ul class="footer-brand__badges" role="list">
            <?php foreach ($footer_badge_keys as $badge_key) : ?>
              <li class="footer-brand__badge"><?php echo svic_translate_html($badge_key); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>

        <div class="footer-pillars">
          <article class="footer-pillar footer-pillar--list">
            <ul class="footer-benefits">
              <?php foreach ($footer_benefit_map as $benefit) : ?>
                <li class="footer-benefits__item">
                  <span class="footer-benefits__icon" style="color:rgba(236,246,255,0.95);">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/' . $benefit['icon'] ); ?>" alt="<?php echo esc_attr( svic_icon_label( $benefit['icon'] ) ); ?>" loading="lazy" />
                  </span>
                  <div class="footer-benefits__content">
                    <span class="footer-benefits__label"><?php echo svic_translate_html($benefit['label_key']); ?></span>
                    <p class="footer-benefits__desc"><?php echo svic_translate_html($benefit['desc_key']); ?></p>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          </article>
        </div>
      </div>

      <div class="footer-bottom">
        <?php if ( has_nav_menu( 'footer' ) ) : ?>
          <?php
            wp_nav_menu([
              'theme_location'       => 'footer',
              'container'            => 'nav',
              'container_class'      => 'footer-bottom__nav',
              'container_aria_label' => esc_attr__( 'Footer navigation', 'svicloudtvbox-lumen' ),
              'menu_class'           => 'menu footer-menu',
              'fallback_cb'          => false,
            ]);
          ?>
        <?php endif; ?>
        <p class="footer-copy"><?php echo esc_html( svic_translate('footer.copyright', ['year' => date('Y')]) ); ?></p>
      </div>
    </div>
  </footer>
  <?php wp_footer(); ?>
</body>
</html>
