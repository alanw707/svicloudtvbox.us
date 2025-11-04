<?php
/**
 * Front Page Template (Classic)
 */
get_header();

$hero_product_10p = class_exists('WooCommerce') ? svic_get_product_by_slug('svicloud-10p-plus') : null;
$hero_product_10s = class_exists('WooCommerce') ? svic_get_product_by_slug('svicloud-10s') : null;
$hero_10p_url = $hero_product_10p ? svic_url_with_lang(get_permalink($hero_product_10p->get_id())) : svic_url_with_lang(home_url('/product/svicloud-10p-plus'));
$hero_10s_url = $hero_product_10s ? svic_url_with_lang(get_permalink($hero_product_10s->get_id())) : svic_url_with_lang(home_url('/product/svicloud-10s'));

$hero_bullet_keys = [
    'frontpage.hero.bullets.shipping',
    'frontpage.hero.bullets.warranty',
    'frontpage.hero.bullets.fees',
];

$hero_spec_rows = [
    [
        'label_key' => 'frontpage.hero.card.specs.processor.label',
        'value_key' => 'frontpage.hero.card.specs.processor.value',
    ],
    [
        'label_key' => 'frontpage.hero.card.specs.connectivity.label',
        'value_key' => 'frontpage.hero.card.specs.connectivity.value',
    ],
    [
        'label_key' => 'frontpage.hero.card.specs.video.label',
        'value_key' => 'frontpage.hero.card.specs.video.value',
    ],
    [
        'label_key' => 'frontpage.hero.card.specs.extras.label',
        'value_key' => 'frontpage.hero.card.specs.extras.value',
    ],
];

$metrics = [
    [
        'icon'      => 'icon-truck.svg',
        'title_key' => 'frontpage.metrics.shipping.title',
        'copy_key'  => 'frontpage.metrics.shipping.copy',
    ],
    [
        'icon'      => 'icon-tool.svg',
        'title_key' => 'frontpage.metrics.concierge.title',
        'copy_key'  => 'frontpage.metrics.concierge.copy',
    ],
    [
        'icon'      => 'icon-lock.svg',
        'title_key' => 'frontpage.metrics.security.title',
        'copy_key'  => 'frontpage.metrics.security.copy',
    ],
    [
        'icon'      => 'icon-star.svg',
        'title_key' => 'frontpage.metrics.dealer.title',
        'copy_key'  => 'frontpage.metrics.dealer.copy',
    ],
];

$feature_cards = [
    [
        'icon'      => 'icon-box.svg',
        'title_key' => 'frontpage.feature_grid.cards.entertainment.title',
        'copy_key'  => 'frontpage.feature_grid.cards.entertainment.copy',
    ],
    [
        'icon'      => 'icon-bolt.svg',
        'title_key' => 'frontpage.feature_grid.cards.hardware.title',
        'copy_key'  => 'frontpage.feature_grid.cards.hardware.copy',
    ],
    [
        'icon'      => 'icon-handshake.svg',
        'title_key' => 'frontpage.feature_grid.cards.support.title',
        'copy_key'  => 'frontpage.feature_grid.cards.support.copy',
    ],
];

$experience_services = [
    [
        'icon'     => 'icon-satellite.svg',
        'text_key' => 'frontpage.experience.services.activation',
    ],
    [
        'icon'     => 'icon-wifi.svg',
        'text_key' => 'frontpage.experience.services.wifi',
    ],
    [
        'icon'     => 'icon-mic.svg',
        'text_key' => 'frontpage.experience.services.karaoke',
    ],
    [
        'icon'     => 'icon-family.svg',
        'text_key' => 'frontpage.experience.services.kids',
    ],
];

$faq_groups = [
    [
        'title_key' => 'frontpage.faq.groups.orders.title',
        'items'     => [
            [
                'question_key' => 'frontpage.faq.groups.orders.items.fulfillment.question',
                'answer_key'   => 'frontpage.faq.groups.orders.items.fulfillment.answer',
            ],
            [
                'question_key' => 'frontpage.faq.groups.orders.items.warranty.question',
                'answer_key'   => 'frontpage.faq.groups.orders.items.warranty.answer',
            ],
        ],
    ],
    [
        'title_key' => 'frontpage.faq.groups.setup.title',
        'items'     => [
            [
                'question_key' => 'frontpage.faq.groups.setup.items.compatibility.question',
                'answer_key'   => 'frontpage.faq.groups.setup.items.compatibility.answer',
            ],
            [
                'question_key' => 'frontpage.faq.groups.setup.items.concierge.question',
                'answer_key'   => 'frontpage.faq.groups.setup.items.concierge.answer',
            ],
        ],
    ],
];

$pricing_card_images = [
    '10p' => array_merge(
        ['alt' => esc_html__('SVICLOUD 10P+ flagship streaming device and remote', 'svicloudtvbox-lumen')],
        svic_get_theme_image_meta('/assets/images/svicloud-10p-plus.webp')
    ),
    '10s' => array_merge(
        ['alt' => esc_html__('SVICLOUD 10S compact streaming device with HDMI and power accessories', 'svicloudtvbox-lumen')],
        svic_get_theme_image_meta('/assets/images/svicloud-tvbox-10s.webp')
    ),
];

$pricing_cards = [
    '10p' => [
        'product'        => $hero_product_10p,
        'fallback_price' => '$248.99',
        'fallback_url'   => svic_url_with_lang(home_url('/product/svicloud-10p-plus')),
        'image'          => $pricing_card_images['10p'],
        'highlight'      => true,
        'badge_key'      => 'frontpage.pricing.cards.10p.badge',
        'title_key'      => 'frontpage.pricing.cards.10p.title',
        'interval_key'   => 'frontpage.pricing.cards.10p.interval',
        'copy_key'       => 'frontpage.pricing.cards.10p.copy',
        'feature_keys'   => [
            'frontpage.pricing.cards.10p.features.hdr',
            'frontpage.pricing.cards.10p.features.apps',
            'frontpage.pricing.cards.10p.features.wifi',
        ],
        'cta_key'   => 'frontpage.pricing.cards.10p.cta',
        'meta_keys' => [
            'frontpage.pricing.cards.10p.meta.shipping',
            'frontpage.pricing.cards.10p.meta.warranty',
            'frontpage.pricing.cards.10p.meta.concierge',
        ],
    ],
    '10s' => [
        'product'        => $hero_product_10s,
        'fallback_price' => '$183.99',
        'fallback_url'   => svic_url_with_lang(home_url('/product/svicloud-10s')),
        'image'          => $pricing_card_images['10s'],
        'highlight'      => false,
        'badge_key'      => null,
        'title_key'      => 'frontpage.pricing.cards.10s.title',
        'interval_key'   => 'frontpage.pricing.cards.10s.interval',
        'copy_key'       => 'frontpage.pricing.cards.10s.copy',
        'feature_keys'   => [
            'frontpage.pricing.cards.10s.features.hdr',
            'frontpage.pricing.cards.10s.features.remote',
            'frontpage.pricing.cards.10s.features.bundle',
        ],
        'cta_key'   => 'frontpage.pricing.cards.10s.cta',
        'meta_keys' => [
            'frontpage.pricing.cards.10s.meta.shipping',
            'frontpage.pricing.cards.10s.meta.warranty',
            'frontpage.pricing.cards.10s.meta.fees',
        ],
    ],
];

$product_schema_nodes = [];
$item_list_elements   = [];
$list_position        = 1;
$organization_id      = trailingslashit(home_url('/')) . '#organization';
$pricing_canonical    = function_exists('svic_get_localized_canonical_url') ? svic_get_localized_canonical_url() : home_url('/');
$pricing_section_url  = untrailingslashit($pricing_canonical) . '/#pricing';

foreach ($pricing_cards as $slug => $card) {
    $product = $card['product'] ?? null;
    $has_wc_product = false;
    if ($product && class_exists('WC_Product')) {
        $has_wc_product = $product instanceof WC_Product;
    }

    if ($has_wc_product) {
        $pricing_cards[$slug]['price_html'] = svic_price_html($product);
        $pricing_cards[$slug]['cta_url'] = svic_url_with_lang(get_permalink($product->get_id()));
    } else {
        $pricing_cards[$slug]['price_html'] = sprintf('<span class="amount">%s</span>', esc_html($card['fallback_price']));
        $pricing_cards[$slug]['cta_url'] = $card['fallback_url'];
    }

    $product_url = esc_url_raw($pricing_cards[$slug]['cta_url']);
    $product_name_source = '';
    if ($has_wc_product) {
        $product_name_source = $product->get_name();
    } else {
        $product_name_source = svic_translate($card['title_key']);
    }
    $product_name = trim(wp_strip_all_tags((string) $product_name_source));

    $description_source = '';
    if ($has_wc_product) {
        $description_source = $product->get_short_description();
        if ($description_source === '') {
            $description_source = $product->get_description();
        }
    }
    if ($description_source === '' && isset($card['copy_key'])) {
        $description_source = svic_translate($card['copy_key']);
    }
    $product_description = trim(wp_strip_all_tags((string) $description_source));

    $image_object = [];
    if (!empty($card['image']['url'])) {
        $image_object = [
            '@type' => 'ImageObject',
            'url'   => esc_url_raw($card['image']['url']),
        ];
        if (!empty($card['image']['width'])) {
            $image_object['width'] = (int) $card['image']['width'];
        }
        if (!empty($card['image']['height'])) {
            $image_object['height'] = (int) $card['image']['height'];
        }
        if (!empty($card['image']['alt'])) {
            $image_object['caption'] = $card['image']['alt'];
        }
    }

    $price_value = null;
    if ($has_wc_product) {
        $price_string = $product->get_price();
        if ($price_string === '') {
            $price_string = $product->get_regular_price();
        }
        if ($price_string !== '') {
            $price_value = number_format((float) $price_string, 2, '.', '');
        }
    } elseif (!empty($card['fallback_price'])) {
        $numeric = preg_replace('/[^\d.]/', '', (string) $card['fallback_price']);
        if ($numeric !== '') {
            $price_value = number_format((float) $numeric, 2, '.', '');
        }
    }

    $availability = 'https://schema.org/InStock';
    if ($has_wc_product && !$product->is_in_stock()) {
        $availability = 'https://schema.org/OutOfStock';
    }

    $sku = '';
    if ($has_wc_product) {
        $sku = (string) $product->get_sku();
        if ($sku === '' && method_exists($product, 'get_slug')) {
            $sku = strtoupper(str_replace('-', '', (string) $product->get_slug()));
        }
    } else {
        $fallback_slug = '';
        if (!empty($card['fallback_url'])) {
            $path = parse_url($card['fallback_url'], PHP_URL_PATH);
            $fallback_slug = is_string($path) ? basename($path) : '';
        }
        $sku = strtoupper(str_replace('-', '', $fallback_slug ?: $slug));
    }

    if ($product_name === '' || $product_url === '') {
        continue;
    }

    $product_node = [
        '@type'        => 'Product',
        '@id'          => untrailingslashit($product_url) . '#product',
        'url'          => $product_url,
        'name'         => $product_name,
        'itemCondition'=> 'https://schema.org/NewCondition',
        'brand'        => [
            '@type' => 'Brand',
            'name'  => 'SVICLOUD',
        ],
        'category'     => 'Electronics > Streaming Players',
    ];

    if ($product_description !== '') {
        $product_node['description'] = $product_description;
    }

    if ($sku !== '') {
        $product_node['sku'] = $sku;
    }

    if (!empty($image_object)) {
        $product_node['image'] = $image_object;
    }

    if ($price_value !== null) {
        $product_node['offers'] = [
            '@type'         => 'Offer',
            'priceCurrency' => 'USD',
            'price'         => $price_value,
            'availability'  => $availability,
            'url'           => $product_url,
            'seller'        => [
                '@id' => $organization_id,
            ],
        ];
    }

    $product_schema_nodes[] = $product_node;
    $item_list_elements[] = [
        '@type'    => 'ListItem',
        'position' => $list_position++,
        'item'     => [
            '@id'   => $product_node['@id'],
            'name'  => $product_node['name'],
        ],
    ];
}

$blog_page_id = (int) get_option('page_for_posts');
$blog_archive_url = $blog_page_id ? svic_url_with_lang(get_permalink($blog_page_id)) : svic_url_with_lang(home_url('/blog/'));
$blog_posts_query = null;
$current_lang_slug = null;
if (function_exists('pll_current_language')) {
    $current_lang_slug = pll_current_language('slug');
} else {
    $current_lang_slug = svic_language_query_value();
}

$sticky_ids = array_filter(array_map('intval', (array) get_option('sticky_posts')));
if ($sticky_ids) {
    $sticky_ids_localized = [];
    foreach ($sticky_ids as $post_id_candidate) {
        if (get_post_status($post_id_candidate) !== 'publish') {
            continue;
        }

        if ($current_lang_slug && function_exists('pll_get_post_language')) {
            $post_lang = pll_get_post_language($post_id_candidate, 'slug');
            if (is_string($post_lang) && $post_lang !== '' && $post_lang !== $current_lang_slug) {
                continue;
            }
        }

        $sticky_ids_localized[] = $post_id_candidate;
    }

    if ($sticky_ids_localized) {
        $blog_posts_query = new WP_Query([
            'post_type'           => 'post',
            'post__in'            => $sticky_ids_localized,
            'orderby'             => 'post__in',
            'posts_per_page'      => 3,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => false,
            'no_found_rows'       => true,
        ]);
    }
}

if (!$blog_posts_query instanceof WP_Query) {
    $blog_posts_query = new WP_Query([
        'post_type'           => 'post',
        'posts_per_page'      => 3,
        'post_status'         => 'publish',
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,
    ]);
}
?>
<main class="main-content">
  <!-- Hero Section -->
  <section class="hero-dashboard" id="hero">
    <div class="hero-dashboard__background" aria-hidden="true">
      <span class="hero-dashboard__globe hero-dashboard__globe--left"></span>
      <span class="hero-dashboard__globe hero-dashboard__globe--right"></span>
    </div>
    <div class="hero-dashboard__inner">
      <div class="hero-dashboard__content">
        <span class="hero-dashboard__eyebrow"><?php echo svic_translate_html('frontpage.hero.badge'); ?></span>
        <h1 class="hero-dashboard__title"><?php echo svic_translate_html('frontpage.hero.title'); ?></h1>
        <p class="hero-dashboard__copy"><?php echo svic_translate_html('frontpage.hero.copy'); ?></p>
        <ul class="hero-dashboard__list" role="list">
          <?php foreach ($hero_bullet_keys as $bullet_key) : ?>
            <li><?php echo svic_translate_html($bullet_key); ?></li>
          <?php endforeach; ?>
        </ul>
        <div class="hero-dashboard__cta">
          <a class="hero-dashboard__button hero-dashboard__button--primary" href="<?php echo esc_url($hero_10p_url); ?>"><?php echo svic_translate_html('frontpage.hero.cta.primary'); ?></a>
          <a class="hero-dashboard__button hero-dashboard__button--secondary" href="#pricing">
            <span><?php echo svic_translate_html('frontpage.hero.cta.bundles'); ?></span>
          </a>
          <a class="hero-dashboard__button hero-dashboard__button--secondary" href="<?php echo esc_url(svic_url_with_lang(home_url('/compare'))); ?>">
            <span><?php echo svic_translate_html('frontpage.hero.cta.compare'); ?></span>
          </a>
        </div>
      </div>
      <div class="hero-dashboard__visual">
        <div class="hero-dashboard__badge"><?php echo svic_translate_html('frontpage.hero.card.badge'); ?></div>
        <div class="hero-dashboard__card">
          <div class="hero-dashboard__product">
            <picture>
              <source srcset="<?php echo esc_url(svic_theme_image_uri('/assets/images/hero-voice-assistant.png')); ?>" type="image/webp" />
              <img class="hero-dashboard__product-main" src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/hero-voice-assistant.png'); ?>" alt="<?php esc_attr_e('SVICLOUD voice assistant interface with Google Play, movies, and YouTube apps', 'svicloudtvbox-lumen'); ?>" loading="lazy" decoding="async" width="1601" height="898" />
            </picture>
          </div>
          <div class="hero-dashboard__card-header">
            <span><?php echo svic_translate_html('frontpage.hero.card.headline'); ?></span>
            <span><?php echo svic_translate_html('frontpage.hero.card.timestamp'); ?></span>
          </div>
          <div class="hero-dashboard__stat">
            <strong><?php echo svic_translate_html('frontpage.hero.card.stat'); ?></strong>
          </div>
          <div class="hero-dashboard__spec-grid" role="list">
            <?php foreach ($hero_spec_rows as $row) : ?>
              <div class="hero-dashboard__spec-pill" role="listitem">
                <span class="hero-dashboard__spec-label"><?php echo svic_translate_html($row['label_key']); ?></span>
                <span class="hero-dashboard__spec-value"><?php echo svic_translate_html($row['value_key']); ?></span>
              </div>
            <?php endforeach; ?>
          </div>
          <div class="hero-dashboard__card-footer">
            <span><?php echo svic_translate_html('frontpage.hero.card.footer.shipping'); ?></span>
            <span><?php echo svic_translate_html('frontpage.hero.card.footer.support'); ?></span>
          </div>
        </div>
        <span class="hero-dashboard__spark" aria-hidden="true"></span>
      </div>
    </div>
  </section>

  <?php
$certificate_asset_relative = '/assets/images/certification-authorized-dealer.webp';
  $certificate_asset_path     = get_template_directory() . $certificate_asset_relative;
  $certificate_asset_url      = file_exists($certificate_asset_path) ? svic_theme_image_uri($certificate_asset_relative) : '';
  ?>
  <!-- Authorized Dealer Certificate -->
  <section class="lumen-certification" id="authorized-dealer">
    <div class="lumen-certification__inner">
      <div class="lumen-certification__copy">
        <span class="lumen-certification__badge"><?php echo svic_translate_html('frontpage.certification.badge'); ?></span>
        <h2 class="lumen-certification__title"><?php echo svic_translate_html('frontpage.certification.title'); ?></h2>
        <p class="lumen-certification__lead"><?php echo svic_translate_html('frontpage.certification.lead'); ?></p>
        <dl class="lumen-certification__meta">
          <div class="lumen-certification__meta-row">
            <dt><?php echo svic_translate_html('frontpage.certification.meta.number.label'); ?></dt>
            <dd><?php echo svic_translate_html('frontpage.certification.meta.number.value'); ?></dd>
          </div>
          <div class="lumen-certification__meta-row">
            <dt><?php echo svic_translate_html('frontpage.certification.meta.territory.label'); ?></dt>
            <dd><?php echo svic_translate_html('frontpage.certification.meta.territory.value'); ?></dd>
          </div>
          <div class="lumen-certification__meta-row">
            <dt><?php echo svic_translate_html('frontpage.certification.meta.term.label'); ?></dt>
            <dd><?php echo svic_translate_html('frontpage.certification.meta.term.value'); ?></dd>
          </div>
        </dl>
        <p class="lumen-certification__footnote"><?php echo svic_translate_html('frontpage.certification.footnote'); ?></p>
        <?php if (!empty($certificate_asset_url)) : ?>
          <a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url($certificate_asset_url); ?>" target="_blank" rel="noopener">
            <?php echo svic_translate_html('frontpage.certification.cta'); ?>
          </a>
        <?php endif; ?>
      </div>
      <?php if (!empty($certificate_asset_url)) : ?>
        <figure class="lumen-certification__media">
          <img src="<?php echo esc_url($certificate_asset_url); ?>" alt="<?php echo esc_attr(svic_translate('frontpage.certification.alt')); ?>" loading="lazy" width="800" height="594" />
        </figure>
      <?php endif; ?>
    </div>
  </section>

  <!-- Credibility Bar -->
  <section class="lumen-metrics" aria-label="<?php esc_attr_e('Key SVICLOUD advantages', 'svicloudtvbox-lumen'); ?>">
    <div class="lumen-metrics__inner">
      <?php foreach ($metrics as $metric) : ?>
        <?php $icon_path = get_template_directory_uri() . '/assets/svg/' . $metric['icon']; ?>
        <article class="lumen-metric">
          <span class="lumen-metric__glow" aria-hidden="true"></span>
          <span class="lumen-metric__icon">
            <img src="<?php echo esc_url($icon_path); ?>" alt="<?php echo esc_attr(svic_icon_label($metric['icon'])); ?>" loading="lazy" />
          </span>
          <div class="lumen-metric__copy">
            <strong><?php echo svic_translate_html($metric['title_key']); ?></strong>
            <span><?php echo svic_translate_html($metric['copy_key']); ?></span>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Feature Highlights -->
  <section class="lumen-feature-grid" id="experience">
    <div class="lumen-feature-grid__inner">
      <header class="lumen-section-header">
        <h2 class="lumen-section-header__title"><?php echo svic_translate_html('frontpage.feature_grid.title'); ?></h2>
        <p class="lumen-section-header__subtitle"><?php echo svic_translate_html('frontpage.feature_grid.subtitle'); ?></p>
      </header>
      <div class="lumen-feature-grid__cards">
        <?php foreach ($feature_cards as $card) : ?>
          <article class="lumen-feature-card">
            <span class="lumen-feature-card__icon">
              <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/svg/' . $card['icon']); ?>" alt="<?php echo esc_attr(svic_icon_label($card['icon'])); ?>" loading="lazy" />
            </span>
            <h3 class="lumen-feature-card__title"><?php echo svic_translate_html($card['title_key']); ?></h3>
            <p class="lumen-feature-card__copy"><?php echo svic_translate_html($card['copy_key']); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Experience Section -->
  <section class="lumen-experience">
    <div class="lumen-experience__inner">
      <div class="lumen-experience__copy">
        <span class="lumen-experience__badge"><?php echo svic_translate_html('frontpage.experience.badge'); ?></span>
        <h2 class="lumen-experience__title"><?php echo svic_translate_html('frontpage.experience.title'); ?></h2>
        <p class="lumen-experience__lead"><?php echo svic_translate_html('frontpage.experience.lead'); ?></p>
        <ul class="lumen-experience__list">
          <li><?php echo svic_translate_html('frontpage.concierge.personalized_walkthrough'); ?></li>
          <li><?php echo svic_translate_html('frontpage.concierge.remote_updates'); ?></li>
          <li><?php echo svic_translate_html('frontpage.concierge.community_access'); ?></li>
        </ul>
      </div>
      <aside class="lumen-experience__card">
        <h3 class="lumen-experience__card-title"><?php echo svic_translate_html('frontpage.experience.card_title'); ?></h3>
        <ul class="lumen-experience__card-list">
          <?php foreach ($experience_services as $service) : ?>
            <li>
              <span class="lumen-experience__icon"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/svg/' . $service['icon']); ?>" alt="<?php echo esc_attr(svic_icon_label($service['icon'])); ?>" loading="lazy" /></span>
            <?php echo svic_translate_html($service['text_key']); ?>
            </li>
          <?php endforeach; ?>
        </ul>
        <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url(svic_url_with_lang(home_url('/contact'))); ?>"><?php echo svic_translate_html('frontpage.experience.cta'); ?></a>
      </aside>
    </div>
  </section>

  <?php if ($blog_posts_query->have_posts()) : ?>
    <section class="frontpage-blog" id="blog-insights">
      <header class="lumen-section-header frontpage-blog__header">
        <span class="lumen-section-header__badge"><?php echo svic_translate_html('frontpage.blog.badge'); ?></span>
        <h2 class="lumen-section-header__title"><?php echo svic_translate_html('frontpage.blog.title'); ?></h2>
        <p class="lumen-section-header__subtitle"><?php echo svic_translate_html('frontpage.blog.lead'); ?></p>
      </header>
      <div class="frontpage-blog__cards">
        <?php while ($blog_posts_query->have_posts()) : $blog_posts_query->the_post();
          $post_id        = get_the_ID();
          $permalink      = svic_url_with_lang(get_permalink());
          $categories     = get_the_category($post_id);
          $primary_cat    = $categories ? $categories[0] : null;
          $reading_time   = svic_estimated_read_time($post_id);
          $reading_label  = sprintf(
              /* translators: %d: estimated reading time in minutes */
              esc_html__('%d min read', 'svicloudtvbox-lumen'),
              $reading_time
          );
          $published_attr    = get_the_date(DATE_W3C, $post_id);
          $published_display = get_the_date('', $post_id);
          $raw_excerpt       = get_the_excerpt($post_id);
          $excerpt           = wp_trim_words(wp_strip_all_tags((string) $raw_excerpt), 26, '…');
          $thumbnail_html    = svic_post_card_image(
              $post_id,
              'medium_large',
              [
                  'class' => 'frontpage-blog__image',
              ]
          );
          ?>
          <article class="frontpage-blog__card">
            <a class="frontpage-blog__link" href="<?php echo esc_url($permalink); ?>">
              <div class="frontpage-blog__media">
                <?php if ($thumbnail_html) : ?>
                  <?php echo $thumbnail_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <?php else : ?>
                  <div class="frontpage-blog__media-placeholder" aria-hidden="true"></div>
                <?php endif; ?>
              </div>
              <div class="frontpage-blog__content">
                <div class="frontpage-blog__meta">
                  <?php if ($primary_cat instanceof WP_Term) : ?>
                    <span class="frontpage-blog__chip"><?php echo esc_html(svic_category_label($primary_cat)); ?></span>
                  <?php endif; ?>
                  <time datetime="<?php echo esc_attr($published_attr); ?>" class="frontpage-blog__time"><?php echo esc_html($published_display); ?></time>
                  <span class="frontpage-blog__readtime"><?php echo esc_html($reading_label); ?></span>
                </div>
                <h3 class="frontpage-blog__card-title"><?php echo esc_html(svic_post_title($post_id)); ?></h3>
                <?php if ($excerpt !== '') : ?>
                  <p class="frontpage-blog__card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <?php endif; ?>
                <span class="frontpage-blog__more"><?php echo svic_translate_html('frontpage.blog.read_more'); ?></span>
              </div>
            </a>
          </article>
        <?php endwhile; ?>
      </div>
      <div class="frontpage-blog__actions">
        <a class="btn btn-outline" href="<?php echo esc_url($blog_archive_url); ?>"><?php echo svic_translate_html('frontpage.blog.cta_label'); ?></a>
      </div>
    </section>
  <?php endif; ?>
  <?php wp_reset_postdata(); ?>

  <!-- FAQ Section -->
  <section class="lumen-faq" id="faq">
    <div class="lumen-faq__inner">
      <header class="lumen-section-header">
        <span class="lumen-faq__badge"><?php echo svic_translate_html('frontpage.faq.badge'); ?></span>
        <h2 class="lumen-section-header__title"><?php echo svic_translate_html('frontpage.faq.title'); ?></h2>
        <p class="lumen-section-header__subtitle"><?php echo svic_translate_html('frontpage.faq.lead'); ?></p>
      </header>
      <div class="lumen-faq__grid">
        <?php foreach ($faq_groups as $group) : ?>
          <div class="lumen-faq-card">
            <h3 class="lumen-faq-card__title"><?php echo svic_translate_html($group['title_key']); ?></h3>
            <div class="lumen-faq-card__items">
              <?php foreach ($group['items'] as $index => $item) : ?>
                <details class="lumen-faq-item"<?php echo $index === 0 ? ' open' : ''; ?>>
                  <summary class="lumen-faq-item__question"><?php echo svic_translate_html($item['question_key']); ?></summary>
                  <div class="lumen-faq-item__answer"><?php echo wp_kses_post(svic_translate($item['answer_key'])); ?></div>
                </details>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="lumen-faq__cta">
        <a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url(svic_url_with_lang(home_url('/support'))); ?>"><?php echo svic_translate_html('frontpage.faq.cta'); ?></a>
      </div>
    </div>
  </section>

  <?php
  $faq_entities = [];
  foreach ($faq_groups as $group) {
      foreach ($group['items'] as $item) {
          $question_text = trim(wp_strip_all_tags(svic_translate($item['question_key'])));
          $answer_text   = trim(wp_strip_all_tags(svic_translate($item['answer_key'])));

          if ($question_text === '' || $answer_text === '') {
              continue;
          }

          $faq_entities[] = [
              '@type' => 'Question',
              'name'  => $question_text,
              'acceptedAnswer' => [
                  '@type' => 'Answer',
                  'text'  => $answer_text,
              ],
          ];
      }
  }

  if (!empty($faq_entities)) {
      $faq_schema = [
          '@context'    => 'https://schema.org',
          '@type'       => 'FAQPage',
          'mainEntity'  => $faq_entities,
      ];

      echo '<script type="application/ld+json">' . wp_json_encode($faq_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
  }
  ?>

  <!-- Pricing Section -->
  <section class="lumen-pricing" id="pricing">
    <div class="lumen-pricing__inner">
      <header class="lumen-section-header">
        <h2 class="lumen-section-header__title"><?php echo svic_translate_html('frontpage.pricing.title'); ?></h2>
        <p class="lumen-section-header__subtitle"><?php echo svic_translate_html('frontpage.pricing.subtitle'); ?></p>
      </header>
      <div class="lumen-pricing__grid">
        <?php foreach ($pricing_cards as $card) : ?>
          <?php
          $card_classes   = 'lumen-pricing-card';
          $button_classes = 'lumen-pill lumen-pill--outline';

          // Featured cards get primary styling by default.
          if (!empty($card['highlight'])) {
              $card_classes   .= ' lumen-pricing-card--featured';
              $button_classes  = 'lumen-pill lumen-pill--primary';
          } else {
              // Allow per-card CTA style override via translations, e.g., frontpage.pricing.cards.10s.cta_style => 'primary'.
              // Derive the cta_style key from the provided cta key path.
              $cta_style_key = is_string($card['cta_key']) ? str_replace('.cta', '.cta_style', $card['cta_key']) : '';
              if ($cta_style_key !== '') {
                  $cta_style_value = svic_translate($cta_style_key);
                  if (is_string($cta_style_value) && strtolower($cta_style_value) === 'primary') {
                      $button_classes = 'lumen-pill lumen-pill--primary';
                  }
              }
          }
          ?>
          <article class="<?php echo esc_attr($card_classes); ?>">
            <?php if (!empty($card['badge_key'])) : ?>
              <div class="lumen-pricing-card__badge"><?php echo svic_translate_html($card['badge_key']); ?></div>
            <?php endif; ?>
            <?php if (!empty($card['image']['url'])) : ?>
              <figure class="lumen-pricing-card__figure">
                <img
                  src="<?php echo esc_url($card['image']['url']); ?>"
                  alt="<?php echo esc_attr($card['image']['alt'] ?? ''); ?>"
                  loading="lazy"
                  decoding="async"
                  width="<?php echo esc_attr((string) ($card['image']['width'] ?? 800)); ?>"
                  height="<?php echo esc_attr((string) ($card['image']['height'] ?? 600)); ?>"
                />
              </figure>
            <?php endif; ?>
            <h3 class="lumen-pricing-card__title"><?php echo svic_translate_html($card['title_key']); ?></h3>
            <div class="lumen-pricing-card__price">
              <?php echo $card['price_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
              <span class="lumen-pricing-card__interval"><?php echo svic_translate_html($card['interval_key']); ?></span>
            </div>
            <p class="lumen-pricing-card__copy"><?php echo svic_translate_html($card['copy_key']); ?></p>
            <ul class="lumen-pricing-card__features">
              <?php foreach ($card['feature_keys'] as $feature_key) : ?>
                <li><?php echo svic_translate_html($feature_key); ?></li>
              <?php endforeach; ?>
            </ul>
            <a class="<?php echo esc_attr($button_classes); ?>" href="<?php echo esc_url($card['cta_url']); ?>"><?php echo svic_translate_html($card['cta_key']); ?></a>
            <div class="lumen-pricing-card__meta">
              <?php foreach ($card['meta_keys'] as $meta_key) : ?>
                <span><?php echo svic_translate_html($meta_key); ?></span>
              <?php endforeach; ?>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <?php
  if (!empty($product_schema_nodes)) {
      $graph_nodes = [];

      if (!empty($item_list_elements)) {
          $graph_nodes[] = [
              '@type'            => 'ItemList',
              '@id'              => untrailingslashit($pricing_canonical) . '/#pricing-itemlist',
              'name'             => esc_html__('SVICLOUD streaming devices available in North America', 'svicloudtvbox-lumen'),
              'url'              => esc_url_raw($pricing_section_url),
              'numberOfItems'    => count($item_list_elements),
              'itemListOrder'    => 'https://schema.org/ItemListOrderAscending',
              'itemListElement'  => $item_list_elements,
          ];
      }

      $graph_nodes = array_merge($graph_nodes, $product_schema_nodes);

      if (!empty($graph_nodes)) {
          echo '<script type="application/ld+json">' . wp_json_encode([
              '@context' => 'https://schema.org',
              '@graph'   => $graph_nodes,
          ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
      }
  }
  ?>
</main>

<?php get_footer(); ?>
