  <footer class="site-footer">
    <div class="container">
      <p>&copy; <?php echo date('Y'); ?> SVICLOUDTVBOX.US • All rights reserved.</p>
      <?php
        wp_nav_menu([
          'theme_location' => 'footer',
          'container'      => false,
          'menu_class'     => 'menu footer-menu',
          'fallback_cb'    => false,
        ]);
      ?>
    </div>
  </footer>
  <?php wp_footer(); ?>
</body>
</html>

