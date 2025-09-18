  <footer class="site-footer" role="contentinfo">
    <div class="container footer-grid">
      <div class="footer-brand">
        <p class="footer-copy"><?php echo wp_kses_post( svic_bilingual_span('© ' . date('Y') . ' SVICLOUDTVBOX.US • All rights reserved.', '© ' . date('Y') . ' SVICLOUDTVBOX.US • 保留一切權利') ); ?></p>
        <ul class="footer-trust" role="list">
          <li>
            <span class="footer-trust-icon"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-truck.svg' ); ?>" alt="" loading="lazy" /></span>
            <span class="footer-trust-text"><?php echo wp_kses_post( svic_bilingual_span('48-hour U.S. shipping', '48 小時美國出貨') ); ?></span>
          </li>
          <li>
            <span class="footer-trust-icon"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-lock.svg' ); ?>" alt="" loading="lazy" /></span>
            <span class="footer-trust-text"><?php echo wp_kses_post( svic_bilingual_span('Authorized SVICLOUD dealer', 'SVICLOUD 官方授權經銷商') ); ?></span>
          </li>
          <li>
            <span class="footer-trust-icon"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-family.svg' ); ?>" alt="" loading="lazy" /></span>
            <span class="footer-trust-text"><?php echo wp_kses_post( svic_bilingual_span('Bilingual support & concierge', '中/英雙語服務團隊') ); ?></span>
          </li>
        </ul>
      </div>
      <div class="footer-nav">
        <?php
          wp_nav_menu([
            'theme_location' => 'footer',
            'container'      => false,
            'menu_class'     => 'menu footer-menu',
            'fallback_cb'    => false,
          ]);
        ?>
      </div>
    </div>
  </footer>
  <?php wp_footer(); ?>
</body>
</html>
