<?php
/**
 * Front Page Template (Classic)
 */
get_header();

$hero_product_10p = class_exists('WooCommerce') ? svic_get_product_by_slug('svicloud-10p-plus') : null;
$hero_product_10s = class_exists('WooCommerce') ? svic_get_product_by_slug('svicloud-10s') : null;
$hero_10p_url = $hero_product_10p ? get_permalink($hero_product_10p->get_id()) : home_url('/product/svicloud-10p-plus');
$hero_10s_url = $hero_product_10s ? get_permalink($hero_product_10s->get_id()) : home_url('/product/svicloud-10s');
?>

<main class="main-content">
  <!-- Hero Section -->
  <section class="hero-modern" id="hero">
    <div class="hero-glow" aria-hidden="true"></div>
    <div class="container hero-inner">
      <div class="hero-copy">
        <span class="hero-eyebrow">Authorized U.S. Dealer</span>
        <h1 class="hero-title">Premium Chinese IPTV &amp; Global Entertainment, Ready for Any TV</h1>
        <p class="hero-subtitle">SVICLOUD delivers 4K sports, Asian dramas, karaoke, and kids content in one box. Ships fast from the USA with bilingual support.</p>
        <div class="hero-pills" role="list">
          <span class="hero-pill">Ships from USA</span>
          <span class="hero-pill">1-Year U.S. Warranty</span>
          <span class="hero-pill">No Monthly Fees</span>
        </div>
        <div class="hero-ctas">
          <a class="btn btn-primary btn-cta" href="<?php echo esc_url( $hero_10p_url ); ?>"><?php esc_html_e('Shop 10P+', 'svicloudtvbox'); ?></a>
          <a class="btn btn-outline" href="#pricing">
            <span class="btn-text"><?php esc_html_e('View Bundles', 'svicloudtvbox'); ?></span>
            <span class="btn-icon" aria-hidden="true">→</span>
          </a>
          <a class="btn btn-link hero-link-cta" href="<?php echo esc_url( home_url('/compare') ); ?>">
            <span class="btn-text"><?php esc_html_e('Compare Models', 'svicloudtvbox'); ?></span>
            <span aria-hidden="true">→</span>
          </a>
        </div>
        <ul class="hero-highlights" role="list">
          <li>
            <span class="hero-highlight-icon"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-truck.svg' ); ?>" alt="" loading="lazy" /></span>
            <span class="hero-highlight-text"><?php echo wp_kses_post( svic_bilingual_span('Ships from USA', '美國境內發貨') ); ?></span>
          </li>
          <li>
            <span class="hero-highlight-icon"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-lock.svg' ); ?>" alt="" loading="lazy" /></span>
            <span class="hero-highlight-text"><?php echo wp_kses_post( svic_bilingual_span('1-Year U.S. Warranty', '一年美國保固') ); ?></span>
          </li>
          <li>
            <span class="hero-highlight-icon"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-family.svg' ); ?>" alt="" loading="lazy" /></span>
            <span class="hero-highlight-text"><?php echo wp_kses_post( svic_bilingual_span('Bilingual setup concierge', '專屬中/英文安裝服務') ); ?></span>
          </li>
        </ul>
      </div>
      <div class="hero-product">
        <div class="product-card-floating">
          <div class="product-card-header">
            <span class="badge badge-primary">Most Popular</span>
            <span class="product-tag">SVICLOUD 10P+</span>
          </div>
          <div class="product-image-shell">
            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/svicloud-hero-product.png' ); ?>" alt="SVICLOUD Hero Product" />
            <div class="product-orbit"></div>
          </div>
          <div class="product-card-body">
            <div class="product-price">$248.99</div>
            <p class="product-copy">Includes remote, global apps, and full-channel unlock. Upgrade-ready firmware.</p>
            <a class="btn btn-primary btn-cta" href="<?php echo esc_url( $hero_10p_url ); ?>">Add to Cart</a>
            <div class="product-meta">
              <span><?php esc_html_e('Priority support', 'svicloudtvbox'); ?></span>
              <span><?php esc_html_e('Kids & Karaoke apps', 'svicloudtvbox'); ?></span>
              <span><?php esc_html_e('Free U.S. shipping', 'svicloudtvbox'); ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Credibility Bar -->
  <section class="credibility-strip" aria-label="Key advantages">
    <div class="container metrics-grid">
      <div class="metric-card">
        <div class="metric-icon"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-truck.svg' ); ?>" alt="" loading="lazy" /></div>
        <div class="metric-text"><strong>48-hour shipping</strong><span>Fast fulfillment from U.S. inventory</span></div>
      </div>
      <div class="metric-card">
        <div class="metric-icon"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-tool.svg' ); ?>" alt="" loading="lazy" /></div>
        <div class="metric-text"><strong>White-glove setup</strong><span>English &amp; 中文 onboarding assistance</span></div>
      </div>
      <div class="metric-card">
        <div class="metric-icon"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-lock.svg' ); ?>" alt="" loading="lazy" /></div>
        <div class="metric-text"><strong>Secure checkout</strong><span>SSL encrypted payment and warranty</span></div>
      </div>
      <div class="metric-card">
        <div class="metric-icon"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-star.svg' ); ?>" alt="" loading="lazy" /></div>
        <div class="metric-text"><strong>Top-rated dealer</strong><span>Trusted by U.S. SVICLOUD users since 2019</span></div>
      </div>
    </div>
  </section>

  <!-- Feature Highlights -->
  <section class="feature-panels" id="experience">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Why SVICLOUD Beats Generic IPTV Boxes</h2>
        <p class="section-subtitle">Engineered for crystal-clear 4K sports, drama marathons, and karaoke nights without buffering.</p>
      </div>
      <div class="panel-grid">
        <article class="panel">
          <div class="panel-icon"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-box.svg' ); ?>" alt="" loading="lazy" /></div>
          <h3>Complete Entertainment</h3>
          <p>4K live channels across Asia &amp; North America, plus bilingual VOD, karaoke, and kids zones curated for families.</p>
        </article>
        <article class="panel">
          <div class="panel-icon"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-bolt.svg' ); ?>" alt="" loading="lazy" /></div>
          <h3>Next-Gen Hardware</h3>
          <p>Latest Amlogic chipset, AV1 decode, and Wi-Fi 6 keep streams stable—even on crowded networks.</p>
        </article>
        <article class="panel">
          <div class="panel-icon"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-handshake.svg' ); ?>" alt="" loading="lazy" /></div>
          <h3>Local Expert Support</h3>
          <p>U.S.-based SVICLOUD pros walk you through setup, updates, and your favorite channel lineup.</p>
        </article>
      </div>
    </div>
  </section>

  <!-- Product Tiles -->
  <section class="product-showcase" id="shop">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Pick the SVICLOUD That Fits Your Home</h2>
        <p class="section-subtitle">Both boxes include English/中文 interface, Google Play Store access, and OTA updates.</p>
      </div>
      <div class="grid-toggle" role="group" aria-label="<?php echo esc_attr__('Change product grid layout', 'svicloudtvbox'); ?>">
        <button class="grid-btn active" data-mode="2col" type="button" aria-pressed="true">
          <span class="grid-btn-icon"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-grid-2.svg' ); ?>" alt="" loading="lazy" /></span>
          <span class="grid-btn-label"><?php esc_html_e('2 column', 'svicloudtvbox'); ?></span>
        </button>
        <button class="grid-btn" data-mode="4col" type="button" aria-pressed="false">
          <span class="grid-btn-icon"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-grid-4.svg' ); ?>" alt="" loading="lazy" /></span>
          <span class="grid-btn-label"><?php esc_html_e('4 column', 'svicloudtvbox'); ?></span>
        </button>
        <button class="grid-btn" data-mode="list" type="button" aria-pressed="false">
          <span class="grid-btn-icon"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-list.svg' ); ?>" alt="" loading="lazy" /></span>
          <span class="grid-btn-label"><?php esc_html_e('List view', 'svicloudtvbox'); ?></span>
        </button>
      </div>

      <div class="product-grid grid-2">
        <?php
        // Render WooCommerce products if available, else fallback static
        if (class_exists('WooCommerce')) {
            $p10p = svic_get_product_by_slug('svicloud-10p-plus');
            $p10s = svic_get_product_by_slug('svicloud-10s');
            if ($p10p) { svic_render_product_card($p10p); }
            if ($p10s) { svic_render_product_card($p10s); }
        } else {
        ?>
          <article class="product-card">
            <a class="product-image" href="<?php echo esc_url( $hero_10p_url ); ?>">
              <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/svicloud-10p-plus.png' ); ?>" alt="SVICLOUD 10P+ TV Box" />
            </a>
            <h3 class="pcard-title"><a href="<?php echo esc_url( $hero_10p_url ); ?>">SVICLOUD 10P+</a></h3>
            <div class="pcard-meta"><span class="pcard-price">$248.99</span></div>
            <p class="product-blurb">Premium chipset, Wi-Fi 6, and karaoke-ready audio for dedicated home theaters.</p>
            <ul class="pcard-tags" role="list">
              <li>4K HDR</li>
              <li>Wi-Fi 6</li>
              <li>Kids &amp; Karaoke</li>
            </ul>
            <div class="pcard-actions"><a class="btn btn-primary btn-cta" href="<?php echo esc_url( $hero_10p_url ); ?>">Shop 10P+</a></div>
          </article>
          <article class="product-card">
            <a class="product-image" href="<?php echo esc_url( $hero_10s_url ); ?>">
              <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/svicloud-10s.png' ); ?>" alt="SVICLOUD 10S TV Box" />
            </a>
            <h3 class="pcard-title"><a href="<?php echo esc_url( $hero_10s_url ); ?>">SVICLOUD 10S</a></h3>
            <div class="pcard-meta"><span class="pcard-price">$183.99</span></div>
            <p class="product-blurb">Compact powerhouse for secondary TVs or condos needing reliable IPTV streaming.</p>
            <ul class="pcard-tags" role="list">
              <li>4K HDR</li>
              <li>Dual-Band Wi-Fi</li>
              <li>Space-Saving</li>
            </ul>
            <div class="pcard-actions"><a class="btn btn-primary btn-cta" href="<?php echo esc_url( $hero_10s_url ); ?>">Shop 10S</a></div>
          </article>
        <?php } ?>
      </div>

      <div class="compare-cta" style="margin-top:2rem;text-align:center;">
        <a class="btn btn-accent" href="<?php echo esc_url( home_url('/compare') ); ?>">Compare Models →</a>
      </div>
    </div>
  </section>

  <!-- Experience Section -->
  <section class="experience-split">
    <div class="container experience-inner">
      <div class="experience-copy">
        <span class="badge badge-muted">Real People. Real Support.</span>
        <h2>White-Glove Concierge From Setup To Streaming Night</h2>
        <p>We configure, troubleshoot, and update your box so you can just enjoy the content. Every bundle includes priority access to our bilingual SVICLOUD specialists.</p>
        <ul class="experience-list">
          <li><span class="experience-check"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-check.svg' ); ?>" alt="" loading="lazy" /></span> Personalized channel walkthroughs and favorites setup</li>
          <li><span class="experience-check"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-check.svg' ); ?>" alt="" loading="lazy" /></span> Firmware &amp; app updates pushed remotely</li>
          <li><span class="experience-check"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-check.svg' ); ?>" alt="" loading="lazy" /></span> Access to community events, karaoke playlists, and seasonal sports packages</li>
        </ul>
      </div>
      <div class="experience-media">
        <div class="support-card">
          <h3>What We Handle For You</h3>
          <ul>
            <li><span class="support-icon"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-satellite.svg' ); ?>" alt="" loading="lazy" /></span> IPTV activation &amp; renewals</li>
            <li><span class="support-icon"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-wifi.svg' ); ?>" alt="" loading="lazy" /></span> Wi-Fi optimization tips</li>
            <li><span class="support-icon"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-mic.svg' ); ?>" alt="" loading="lazy" /></span> Karaoke playlists &amp; mic pairing</li>
            <li><span class="support-icon"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-family.svg' ); ?>" alt="" loading="lazy" /></span> Kid-safe profiles &amp; timers</li>
          </ul>
          <a class="btn btn-primary btn-cta" href="<?php echo esc_url( home_url('/contact') ); ?>">Talk to an expert</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Pricing Section -->
  <section class="pricing-section" id="pricing">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Choose Your SVICLOUD Device</h2>
        <p class="section-subtitle">Pick the hardware that fits your home. No IPTV bundles—just authentic SVICLOUD boxes shipping from the USA.</p>
      </div>
      <div class="pricing-grid">
        <div class="pricing-card featured">
          <div class="pricing-name">SVICLOUD 10P+</div>
          <div class="pricing-price">
            <?php echo $hero_product_10p ? svic_price_html($hero_product_10p) : '$248.99'; ?>
            <span class="pricing-interval">device</span>
          </div>
          <p class="pricing-desc">Flagship 4GB RAM / 64GB storage with Kids &amp; Karaoke apps included.</p>
          <ul class="pricing-features">
            <li>✔ 4K HDR + AV1 decode</li>
            <li>✔ Kids &amp; Karaoke apps included</li>
            <li>✔ AI voice remote + dual-band Wi-Fi</li>
          </ul>
          <div class="pricing-cta"><a class="btn btn-primary btn-cta" href="<?php echo esc_url( $hero_10p_url ); ?>">View 10P+</a></div>
          <div class="pricing-trust">
            <span class="item">✔ Ships from USA</span>
            <span class="item">✔ 1-Year U.S. Warranty</span>
            <span class="item">✔ English/中文 support</span>
          </div>
        </div>

        <div class="pricing-card">
          <div class="pricing-name">SVICLOUD 10S</div>
          <div class="pricing-price">
            <?php echo $hero_product_10s ? svic_price_html($hero_product_10s) : '$183.99'; ?>
            <span class="pricing-interval">device</span>
          </div>
          <p class="pricing-desc">Best value with 2GB RAM / 32GB storage—ideal for bedrooms or secondary TVs.</p>
          <ul class="pricing-features">
            <li>✔ 4K HDR + AV1 decode</li>
            <li>✔ AI voice remote</li>
            <li>✔ Includes HDMI &amp; power accessories</li>
          </ul>
          <div class="pricing-cta"><a class="btn btn-secondary btn-cta" href="<?php echo esc_url( $hero_10s_url ); ?>">View 10S</a></div>
          <div class="pricing-trust">
            <span class="item">✔ Ships from USA</span>
            <span class="item">✔ 1-Year U.S. Warranty</span>
            <span class="item">✔ No monthly device fees</span>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php get_footer(); ?>
