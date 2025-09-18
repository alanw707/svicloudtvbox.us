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
  <section class="hero-dashboard" id="hero">
    <div class="hero-dashboard__inner">
      <div class="hero-dashboard__content">
        <span class="hero-dashboard__eyebrow"><?php esc_html_e('Authorized U.S. Dealer', 'svicloudtvbox-lumen'); ?></span>
        <h1 class="hero-dashboard__title"><?php esc_html_e('Premium Chinese IPTV & Global Entertainment, Ready for Any TV', 'svicloudtvbox-lumen'); ?></h1>
        <p class="hero-dashboard__copy"><?php esc_html_e('SVICLOUD delivers 4K sports, Asian dramas, karaoke, and kids content in one box. Ships fast from the USA with bilingual support.', 'svicloudtvbox-lumen'); ?></p>
        <ul class="hero-dashboard__list" role="list">
          <li><?php echo wp_kses_post( svic_bilingual_span('Ships from USA', '美國境內發貨') ); ?></li>
          <li><?php echo wp_kses_post( svic_bilingual_span('1-Year U.S. Warranty', '一年美國保固') ); ?></li>
          <li><?php echo wp_kses_post( svic_bilingual_span('No Monthly Fees', '免月費') ); ?></li>
        </ul>
        <div class="hero-dashboard__cta">
          <a class="hero-dashboard__button hero-dashboard__button--primary" href="<?php echo esc_url( $hero_10p_url ); ?>"><?php esc_html_e('Shop 10P+', 'svicloudtvbox-lumen'); ?></a>
          <a class="hero-dashboard__button hero-dashboard__button--secondary" href="#pricing">
            <span><?php esc_html_e('View Bundles', 'svicloudtvbox-lumen'); ?></span>
          </a>
          <a class="hero-dashboard__button hero-dashboard__button--secondary" href="<?php echo esc_url( home_url('/compare') ); ?>">
            <span><?php esc_html_e('Compare Models', 'svicloudtvbox-lumen'); ?></span>
          </a>
        </div>
      </div>
      <div class="hero-dashboard__visual">
        <div class="hero-dashboard__badge"><?php esc_html_e('Live', 'svicloudtvbox-lumen'); ?></div>
        <div class="hero-dashboard__card">
          <div class="hero-dashboard__product">
            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/svicloud-hero-product.png' ); ?>" alt="<?php esc_attr_e('SVICLOUD streaming device with remote', 'svicloudtvbox-lumen'); ?>" loading="lazy" />
            <div class="hero-dashboard__product-caption">
              <span><?php esc_html_e('SVICLOUD 10P Plus', 'svicloudtvbox-lumen'); ?></span>
              <span><?php esc_html_e('4K · Wi-Fi 6 · Dolby Vision', 'svicloudtvbox-lumen'); ?></span>
            </div>
          </div>
          <div class="hero-dashboard__card-header">
            <span><?php esc_html_e('Neural live dashboard', 'svicloudtvbox-lumen'); ?></span>
            <span><?php esc_html_e('Updated 00:03 ago', 'svicloudtvbox-lumen'); ?></span>
          </div>
          <div class="hero-dashboard__stat">
            <strong>99.3% <?php esc_html_e('uptime', 'svicloudtvbox-lumen'); ?></strong>
            <span class="hero-dashboard__chip"><?php esc_html_e('Priority region · North America', 'svicloudtvbox-lumen'); ?></span>
          </div>
          <div class="hero-dashboard__grid" aria-hidden="true">
            <?php for ( $i = 0; $i < 18; $i++ ) : ?>
              <span></span>
            <?php endfor; ?>
          </div>
          <div class="hero-dashboard__card-footer">
            <span><?php esc_html_e('Average latency · 18 ms', 'svicloudtvbox-lumen'); ?></span>
            <span><?php esc_html_e('Peak viewers · 12,480 households', 'svicloudtvbox-lumen'); ?></span>
          </div>
        </div>
        <span class="hero-dashboard__spark" aria-hidden="true"></span>
      </div>
    </div>
  </section>

  <!-- Credibility Bar -->
  <section class="lumen-metrics" aria-label="<?php esc_attr_e('Key SVICLOUD advantages', 'svicloudtvbox-lumen'); ?>">
    <div class="lumen-metrics__inner">
      <?php
      $metrics = [
        [
          'icon' => 'icon-truck.svg',
          'title' => __('48-hour shipping', 'svicloudtvbox-lumen'),
          'copy'  => __('Fast fulfillment from U.S. inventory', 'svicloudtvbox-lumen'),
        ],
        [
          'icon' => 'icon-tool.svg',
          'title' => __('White-glove setup', 'svicloudtvbox-lumen'),
          'copy'  => __('English & 中文 onboarding assistance', 'svicloudtvbox-lumen'),
        ],
        [
          'icon' => 'icon-lock.svg',
          'title' => __('Secure checkout', 'svicloudtvbox-lumen'),
          'copy'  => __('SSL encrypted payment and warranty', 'svicloudtvbox-lumen'),
        ],
        [
          'icon' => 'icon-star.svg',
          'title' => __('Top-rated dealer', 'svicloudtvbox-lumen'),
          'copy'  => __('Trusted by U.S. SVICLOUD users since 2019', 'svicloudtvbox-lumen'),
        ],
      ];

      foreach ( $metrics as $metric ) :
        $icon_path = get_template_directory_uri() . '/assets/svg/' . $metric['icon'];
        ?>
        <article class="lumen-metric">
          <span class="lumen-metric__glow" aria-hidden="true"></span>
          <span class="lumen-metric__icon">
            <img src="<?php echo esc_url( $icon_path ); ?>" alt="" loading="lazy" />
          </span>
          <div class="lumen-metric__copy">
            <strong><?php echo esc_html( $metric['title'] ); ?></strong>
            <span><?php echo esc_html( $metric['copy'] ); ?></span>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Feature Highlights -->
  <section class="lumen-feature-grid" id="experience">
    <div class="lumen-feature-grid__inner">
      <header class="lumen-section-header">
        <h2 class="lumen-section-header__title"><?php esc_html_e('Why SVICLOUD Beats Generic IPTV Boxes', 'svicloudtvbox-lumen'); ?></h2>
        <p class="lumen-section-header__subtitle"><?php esc_html_e('Engineered for crystal-clear 4K sports, drama marathons, and karaoke nights without buffering.', 'svicloudtvbox-lumen'); ?></p>
      </header>
      <div class="lumen-feature-grid__cards">
        <article class="lumen-feature-card">
          <span class="lumen-feature-card__icon">
            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-box.svg' ); ?>" alt="" loading="lazy" />
          </span>
          <h3 class="lumen-feature-card__title"><?php esc_html_e('Complete Entertainment', 'svicloudtvbox-lumen'); ?></h3>
          <p class="lumen-feature-card__copy"><?php esc_html_e('4K live channels across Asia & North America, plus bilingual VOD, karaoke, and kids zones curated for families.', 'svicloudtvbox-lumen'); ?></p>
        </article>
        <article class="lumen-feature-card">
          <span class="lumen-feature-card__icon">
            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-bolt.svg' ); ?>" alt="" loading="lazy" />
          </span>
          <h3 class="lumen-feature-card__title"><?php esc_html_e('Next-Gen Hardware', 'svicloudtvbox-lumen'); ?></h3>
          <p class="lumen-feature-card__copy"><?php esc_html_e('Latest Amlogic chipset, AV1 decode, and Wi-Fi 6 keep streams stable—even on crowded networks.', 'svicloudtvbox-lumen'); ?></p>
        </article>
        <article class="lumen-feature-card">
          <span class="lumen-feature-card__icon">
            <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-handshake.svg' ); ?>" alt="" loading="lazy" />
          </span>
          <h3 class="lumen-feature-card__title"><?php esc_html_e('Local Expert Support', 'svicloudtvbox-lumen'); ?></h3>
          <p class="lumen-feature-card__copy"><?php esc_html_e('U.S.-based SVICLOUD pros walk you through setup, updates, and your favorite channel lineup.', 'svicloudtvbox-lumen'); ?></p>
        </article>
      </div>
    </div>
  </section>

  <!-- Experience Section -->
  <section class="lumen-experience">
    <div class="lumen-experience__inner">
      <div class="lumen-experience__copy">
        <span class="lumen-experience__badge"><?php esc_html_e('Real People. Real Support.', 'svicloudtvbox-lumen'); ?></span>
        <h2 class="lumen-experience__title"><?php esc_html_e('White-Glove Concierge From Setup To Streaming Night', 'svicloudtvbox-lumen'); ?></h2>
        <p class="lumen-experience__lead"><?php esc_html_e('We configure, troubleshoot, and update your box so you can just enjoy the content. Every bundle includes priority access to our bilingual SVICLOUD specialists.', 'svicloudtvbox-lumen'); ?></p>
        <ul class="lumen-experience__list">
          <li><?php echo wp_kses_post( svic_bilingual_span('Personalized channel walkthroughs and favorites setup', '專屬頻道導覽與最愛設定') ); ?></li>
          <li><?php echo wp_kses_post( svic_bilingual_span('Firmware & app updates pushed remotely', '遠端推送韌體與應用程式更新') ); ?></li>
          <li><?php echo wp_kses_post( svic_bilingual_span('Access to community events, karaoke playlists, and seasonal sports packages', '社群活動、卡拉 OK 歌單與體育賽事包') ); ?></li>
        </ul>
      </div>
      <aside class="lumen-experience__card">
        <h3 class="lumen-experience__card-title"><?php esc_html_e('What We Handle For You', 'svicloudtvbox-lumen'); ?></h3>
        <ul class="lumen-experience__card-list">
          <li>
            <span class="lumen-experience__icon"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-satellite.svg' ); ?>" alt="" loading="lazy" /></span>
            <?php esc_html_e('IPTV activation & renewals', 'svicloudtvbox-lumen'); ?>
          </li>
          <li>
            <span class="lumen-experience__icon"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-wifi.svg' ); ?>" alt="" loading="lazy" /></span>
            <?php esc_html_e('Wi-Fi optimization tips', 'svicloudtvbox-lumen'); ?>
          </li>
          <li>
            <span class="lumen-experience__icon"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-mic.svg' ); ?>" alt="" loading="lazy" /></span>
            <?php esc_html_e('Karaoke playlists & mic pairing', 'svicloudtvbox-lumen'); ?>
          </li>
          <li>
            <span class="lumen-experience__icon"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/svg/icon-family.svg' ); ?>" alt="" loading="lazy" /></span>
            <?php esc_html_e('Kid-safe profiles & timers', 'svicloudtvbox-lumen'); ?>
          </li>
        </ul>
        <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url( home_url('/contact') ); ?>"><?php esc_html_e('Talk to an expert', 'svicloudtvbox-lumen'); ?></a>
      </aside>
    </div>
  </section>

  <!-- Pricing Section -->
  <section class="lumen-pricing" id="pricing">
    <div class="lumen-pricing__inner">
      <header class="lumen-section-header">
        <h2 class="lumen-section-header__title"><?php esc_html_e('Choose Your SVICLOUD Device', 'svicloudtvbox-lumen'); ?></h2>
        <p class="lumen-section-header__subtitle"><?php esc_html_e('Pick the hardware that fits your home. No IPTV bundles—just authentic SVICLOUD boxes shipping from the USA.', 'svicloudtvbox-lumen'); ?></p>
      </header>
      <div class="lumen-pricing__grid">
        <article class="lumen-pricing-card lumen-pricing-card--featured">
          <div class="lumen-pricing-card__badge"><?php esc_html_e('Most Popular', 'svicloudtvbox-lumen'); ?></div>
          <h3 class="lumen-pricing-card__title"><?php esc_html_e('SVICLOUD 10P+', 'svicloudtvbox-lumen'); ?></h3>
          <div class="lumen-pricing-card__price">
            <?php echo $hero_product_10p ? svic_price_html( $hero_product_10p ) : '<span class="amount">$248.99</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <span class="lumen-pricing-card__interval"><?php esc_html_e('device', 'svicloudtvbox-lumen'); ?></span>
          </div>
          <p class="lumen-pricing-card__copy"><?php esc_html_e('Flagship 4GB RAM / 64GB storage with Kids & Karaoke apps included.', 'svicloudtvbox-lumen'); ?></p>
          <ul class="lumen-pricing-card__features">
            <li><?php esc_html_e('4K HDR + AV1 decode', 'svicloudtvbox-lumen'); ?></li>
            <li><?php esc_html_e('Kids & Karaoke apps included', 'svicloudtvbox-lumen'); ?></li>
            <li><?php esc_html_e('AI voice remote + dual-band Wi-Fi', 'svicloudtvbox-lumen'); ?></li>
          </ul>
          <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url( $hero_10p_url ); ?>"><?php esc_html_e('View 10P+', 'svicloudtvbox-lumen'); ?></a>
          <div class="lumen-pricing-card__meta">
            <span><?php esc_html_e('✔ Ships from USA', 'svicloudtvbox-lumen'); ?></span>
            <span><?php esc_html_e('✔ 1-Year U.S. Warranty', 'svicloudtvbox-lumen'); ?></span>
            <span><?php esc_html_e('✔ English/中文 support', 'svicloudtvbox-lumen'); ?></span>
          </div>
        </article>

        <article class="lumen-pricing-card">
          <h3 class="lumen-pricing-card__title"><?php esc_html_e('SVICLOUD 10S', 'svicloudtvbox-lumen'); ?></h3>
          <div class="lumen-pricing-card__price">
            <?php echo $hero_product_10s ? svic_price_html( $hero_product_10s ) : '<span class="amount">$183.99</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <span class="lumen-pricing-card__interval"><?php esc_html_e('device', 'svicloudtvbox-lumen'); ?></span>
          </div>
          <p class="lumen-pricing-card__copy"><?php esc_html_e('Best value with 2GB RAM / 32GB storage—ideal for bedrooms or secondary TVs.', 'svicloudtvbox-lumen'); ?></p>
          <ul class="lumen-pricing-card__features">
            <li><?php esc_html_e('4K HDR + AV1 decode', 'svicloudtvbox-lumen'); ?></li>
            <li><?php esc_html_e('AI voice remote', 'svicloudtvbox-lumen'); ?></li>
            <li><?php esc_html_e('Includes HDMI & power accessories', 'svicloudtvbox-lumen'); ?></li>
          </ul>
          <a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url( $hero_10s_url ); ?>"><?php esc_html_e('View 10S', 'svicloudtvbox-lumen'); ?></a>
          <div class="lumen-pricing-card__meta">
            <span><?php esc_html_e('✔ Ships from USA', 'svicloudtvbox-lumen'); ?></span>
            <span><?php esc_html_e('✔ 1-Year U.S. Warranty', 'svicloudtvbox-lumen'); ?></span>
            <span><?php esc_html_e('✔ No monthly device fees', 'svicloudtvbox-lumen'); ?></span>
          </div>
        </article>
      </div>
    </div>
  </section>
</main>

<?php get_footer(); ?>
