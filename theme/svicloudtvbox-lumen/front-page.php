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
        'label_key'   => 'frontpage.hero.card.specs.processor.label',
        'value_key'   => 'frontpage.hero.card.specs.processor.value',
        'benefit_key' => 'frontpage.hero.card.specs.processor.benefit',
    ],
    [
        'label_key'   => 'frontpage.hero.card.specs.connectivity.label',
        'value_key'   => 'frontpage.hero.card.specs.connectivity.value',
        'benefit_key' => 'frontpage.hero.card.specs.connectivity.benefit',
    ],
    [
        'label_key'   => 'frontpage.hero.card.specs.video.label',
        'value_key'   => 'frontpage.hero.card.specs.video.value',
        'benefit_key' => 'frontpage.hero.card.specs.video.benefit',
    ],
    [
        'label_key'   => 'frontpage.hero.card.specs.extras.label',
        'value_key'   => 'frontpage.hero.card.specs.extras.value',
        'benefit_key' => 'frontpage.hero.card.specs.extras.benefit',
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
            [
                'question_key' => 'frontpage.faq.groups.orders.items.payment.question',
                'answer_key'   => 'frontpage.faq.groups.orders.items.payment.answer',
            ],
            [
                'question_key' => 'frontpage.faq.groups.orders.items.invoice.question',
                'answer_key'   => 'frontpage.faq.groups.orders.items.invoice.answer',
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
            [
                'question_key' => 'frontpage.faq.groups.setup.items.preloaded.question',
                'answer_key'   => 'frontpage.faq.groups.setup.items.preloaded.answer',
            ],
            [
                'question_key' => 'frontpage.faq.groups.setup.items.vpn.question',
                'answer_key'   => 'frontpage.faq.groups.setup.items.vpn.answer',
            ],
        ],
    ],
    [
        'title_key' => 'frontpage.faq.groups.models.title',
        'items'     => [
            [
                'question_key' => 'frontpage.faq.groups.models.items.vs_competitors.question',
                'answer_key'   => 'frontpage.faq.groups.models.items.vs_competitors.answer',
            ],
            [
                'question_key' => 'frontpage.faq.groups.models.items.after_warranty.question',
                'answer_key'   => 'frontpage.faq.groups.models.items.after_warranty.answer',
            ],
        ],
    ],
];

$frontpage_compare_url = svic_url_with_lang(home_url('/compare'));
$frontpage_contact_url = svic_url_with_lang(home_url('/contact'));

$frontpage_confidence_cards = [
    'frontpage.confidence.cards.official',
    'frontpage.confidence.cards.shipping',
    'frontpage.confidence.cards.concierge',
    'frontpage.confidence.cards.warranty',
];

$frontpage_confidence_steps = [
    'frontpage.confidence.timeline.steps.order',
    'frontpage.confidence.timeline.steps.dispatch',
    'frontpage.confidence.timeline.steps.setup',
    'frontpage.confidence.timeline.steps.support',
];

$pricing_card_images = [
    '10p' => array_merge(
        ['alt' => svic_translate('frontpage.pricing.cards.10p.image_alt')],
        function_exists('svic_get_product_image_meta')
            ? svic_get_product_image_meta($hero_product_10p, 0, 'large')
            : svic_get_theme_image_meta('/assets/images/svicloud-hero-product.webp')
    ),
    '10s' => array_merge(
        ['alt' => svic_translate('frontpage.pricing.cards.10s.image_alt')],
        function_exists('svic_get_product_image_meta')
            ? svic_get_product_image_meta($hero_product_10s, 0, 'large')
            : svic_get_theme_image_meta('/assets/images/svicloud-hero-product.webp')
    ),
];

$pricing_cards = [
    '10p' => [
        'product'         => $hero_product_10p,
        'fallback_price'  => '$248.99',
        'fallback_url'    => svic_url_with_lang(home_url('/product/svicloud-10p-plus')),
        'image'           => $pricing_card_images['10p'],
        'highlight'       => true,
        'modifier'        => '',
        'badge_key'       => 'shop.cards.10p.badge',
        'price_note_key'  => 'shop.cards.price_note',
        'title_key'       => 'frontpage.pricing.cards.10p.title',
        'copy_key'        => 'frontpage.pricing.cards.10p.copy',
        'feature_keys'    => [
            'frontpage.pricing.cards.10p.features.hdr',
            'frontpage.pricing.cards.10p.features.apps',
            'frontpage.pricing.cards.10p.features.wifi',
        ],
        'assurance_keys'  => [
            'shop.cards.assurance.shipping',
            'shop.cards.assurance.warranty',
            'shop.cards.assurance.support',
        ],
        'cta_key'         => 'frontpage.pricing.cards.10p.cta',
        'buy_cta_key'     => 'frontpage.pricing.cards.10p.buy_cta',
    ],
    '10s' => [
        'product'         => $hero_product_10s,
        'fallback_price'  => '$183.99',
        'fallback_url'    => svic_url_with_lang(home_url('/product/svicloud-10s')),
        'image'           => $pricing_card_images['10s'],
        'highlight'       => false,
        'modifier'        => 'shop-product-card--best-value',
        'badge_key'       => 'shop.cards.10s.badge',
        'price_note_key'  => 'shop.cards.price_note',
        'title_key'       => 'frontpage.pricing.cards.10s.title',
        'copy_key'        => 'frontpage.pricing.cards.10s.copy',
        'feature_keys'    => [
            'frontpage.pricing.cards.10s.features.hdr',
            'frontpage.pricing.cards.10s.features.remote',
            'frontpage.pricing.cards.10s.features.bundle',
        ],
        'assurance_keys'  => [
            'shop.cards.assurance.shipping',
            'shop.cards.assurance.warranty',
            'shop.hero.highlights.fees',
        ],
        'cta_key'         => 'frontpage.pricing.cards.10s.cta',
        'buy_cta_key'     => 'frontpage.pricing.cards.10s.buy_cta',
    ],
];

$product_schema_nodes = [];
$item_list_elements   = [];
$list_position        = 1;
$organization_id      = trailingslashit(home_url('/')) . '#organization';
$pricing_canonical    = function_exists('svic_get_localized_canonical_url') ? svic_get_localized_canonical_url() : home_url('/');
$pricing_section_url  = svic_url_with_lang(home_url('/compare'));

foreach ($pricing_cards as $slug => $card) {
    $product = $card['product'] ?? null;
    $has_wc_product = false;
    if ($product && class_exists('WC_Product')) {
        $has_wc_product = $product instanceof WC_Product;
    }

    if ($has_wc_product) {
        $pricing_cards[$slug]['price_html'] = svic_price_html($product);
        $pricing_cards[$slug]['cta_url']    = svic_url_with_lang(get_permalink($product->get_id()));
        $pricing_cards[$slug]['buy_url']    = svic_url_with_lang(
            add_query_arg([
                'add-to-cart' => $product->get_id(),
                'quantity'    => 1,
                'svic_buynow' => 1,
            ], wc_get_checkout_url())
        );

        $pricing_cards[$slug]['savings_html'] = '';
        if ($product->is_on_sale()) {
            $regular = (float) $product->get_regular_price();
            $sale    = (float) $product->get_sale_price();
            if ($regular > 0 && $sale > 0 && $sale < $regular) {
                $diff    = $regular - $sale;
                $percent = (int) round(($diff / $regular) * 100);
                $pricing_cards[$slug]['savings_html'] = sprintf(
                    svic_translate('frontpage.pricing.savings_label'),
                    wp_strip_all_tags(wc_price($diff)),
                    $percent
                );
            }
        }
    } else {
        $pricing_cards[$slug]['price_html'] = sprintf(
            '<span class="lumen-price"><span class="lumen-price__current">%s</span></span>',
            esc_html($card['fallback_price'])
        );
        $pricing_cards[$slug]['cta_url']    = $card['fallback_url'];
        $pricing_cards[$slug]['buy_url']    = $card['fallback_url'];
        $pricing_cards[$slug]['savings_html'] = '';
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
  <!-- Hero Section: store rating above headline -->
  <section class="hero-dashboard" id="hero">
    <div class="hero-dashboard__background" aria-hidden="true">
      <span class="hero-dashboard__globe hero-dashboard__globe--left"></span>
      <span class="hero-dashboard__globe hero-dashboard__globe--right"></span>
    </div>
    <div class="hero-dashboard__inner">
      <div class="hero-dashboard__content">
        <span class="hero-dashboard__eyebrow"><?php echo svic_translate_html('frontpage.hero.badge'); ?></span>
        <div class="hero-dashboard__store-rating" aria-label="<?php echo esc_attr(svic_translate('frontpage.hero.store_rating.aria')); ?>">
          <span class="hero-dashboard__store-rating-label"><?php echo svic_translate_html('frontpage.hero.store_rating.label'); ?></span>
          <span class="hero-dashboard__store-rating-score"><?php echo esc_html(svic_translate('product.reviews.average_score')); ?></span>
          <span class="hero-dashboard__store-rating-stars" aria-hidden="true">★★★★★</span>
          <span class="hero-dashboard__store-rating-note"><?php echo svic_translate_html('frontpage.hero.store_rating.note'); ?></span>
        </div>
        <h1 class="hero-dashboard__title"><?php echo svic_translate_html('frontpage.hero.title'); ?></h1>
        <p class="hero-dashboard__copy"><?php echo svic_translate_html('frontpage.hero.copy'); ?></p>
        <ul class="hero-dashboard__list" role="list">
          <?php foreach ($hero_bullet_keys as $bullet_key) : ?>
            <li><?php echo svic_translate_html($bullet_key); ?></li>
          <?php endforeach; ?>
        </ul>
        <div class="hero-dashboard__cta">
          <a class="hero-dashboard__button hero-dashboard__button--primary" href="<?php echo esc_url($hero_10p_url); ?>" data-svic-event="svic_cta_click" data-svic-location="homepage_hero" data-svic-label="10p_primary" data-svic-model="svicloud-10p-plus"><?php echo svic_translate_html('frontpage.hero.cta.primary'); ?></a>
          <a class="hero-dashboard__button hero-dashboard__button--secondary" href="#pricing">
            <span><?php echo svic_translate_html('frontpage.hero.cta.secondary'); ?></span>
          </a>
        </div>
      </div>
      <div class="hero-dashboard__visual">
        <div class="hero-dashboard__badge"><?php echo svic_translate_html('frontpage.hero.card.badge'); ?></div>
        <div class="hero-dashboard__card">
          <div class="hero-dashboard__product">
            <?php
              $hero_image_sizes = '(max-width: 600px) 88vw, (max-width: 1024px) 62vw, 640px';
              $hero_webp_srcset = sprintf(
                  '%1$s 800w, %2$s 1200w, %3$s 1601w',
                  esc_url(svic_theme_image_uri('/assets/images/hero-voice-assistant-800.webp')),
                  esc_url(svic_theme_image_uri('/assets/images/hero-voice-assistant-1200.webp')),
                  esc_url(svic_theme_image_uri('/assets/images/hero-voice-assistant.webp'))
              );
              $hero_png_srcset = sprintf(
                  '%1$s 800w, %2$s 1200w, %3$s 1601w',
                  esc_url(svic_theme_image_uri('/assets/images/hero-voice-assistant-800.png')),
                  esc_url(svic_theme_image_uri('/assets/images/hero-voice-assistant-1200.png')),
                  esc_url(svic_theme_image_uri('/assets/images/hero-voice-assistant.png'))
              );
            ?>
            <picture>
              <source
                type="image/webp"
                srcset="<?php echo $hero_webp_srcset; ?>"
                sizes="<?php echo esc_attr($hero_image_sizes); ?>"
              />
              <img
                class="hero-dashboard__product-main"
                src="<?php echo esc_url(svic_theme_image_uri('/assets/images/hero-voice-assistant-1200.png')); ?>"
                srcset="<?php echo $hero_png_srcset; ?>"
                sizes="<?php echo esc_attr($hero_image_sizes); ?>"
                alt="<?php echo svic_translate_attr('frontpage.aria.hero_visual'); ?>"
                decoding="async"
                fetchpriority="high"
                width="1601"
                height="898"
              />
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
                <?php $spec_benefit = svic_translate($row['benefit_key']); ?>
                <?php if ($spec_benefit !== '') : ?>
                  <span class="hero-dashboard__spec-benefit"><?php echo esc_html($spec_benefit); ?></span>
                <?php endif; ?>
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

  <!-- Traffic & intent copy for 10P+ -->
  <section class="frontpage-traffic" id="traffic">
    <div class="frontpage-traffic__inner">
      <div class="frontpage-traffic__copy">
        <span class="frontpage-traffic__badge"><?php echo svic_translate_html('frontpage.traffic.badge'); ?></span>
        <h2 class="frontpage-traffic__title"><?php echo svic_translate_html('frontpage.traffic.title'); ?></h2>
        <p class="frontpage-traffic__lead"><?php echo svic_translate_html('frontpage.traffic.lead'); ?></p>
        <ul class="frontpage-traffic__list" role="list">
          <li><?php echo svic_translate_html('frontpage.traffic.bullets.shipping'); ?></li>
          <li><?php echo svic_translate_html('frontpage.traffic.bullets.concierge'); ?></li>
          <li><?php echo svic_translate_html('frontpage.traffic.bullets.warranty'); ?></li>
        </ul>
      </div>
      <div class="frontpage-traffic__links lumen-action-group" role="group" aria-label="<?php echo svic_translate_attr('frontpage.aria.traffic_actions'); ?>">
        <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($hero_10p_url); ?>"><?php echo svic_translate_html('frontpage.traffic.links.pdp'); ?></a>
        <a class="lumen-pill lumen-pill--ghost" href="<?php echo esc_url(svic_url_with_lang(home_url('/compare'))); ?>"><?php echo svic_translate_html('frontpage.traffic.links.compare'); ?></a>
        <a class="frontpage-traffic__textlink" href="<?php echo esc_url(svic_url_with_lang(home_url('/svicloud-tv-box-usa-guide-2026/'))); ?>"><?php echo svic_translate_html('frontpage.traffic.links.guide_2026'); ?></a>
        <a class="frontpage-traffic__textlink" href="<?php echo esc_url(svic_url_with_lang(home_url('/faq'))); ?>"><?php echo svic_translate_html('frontpage.traffic.links.faq'); ?></a>
        <a class="frontpage-traffic__textlink" href="<?php echo esc_url(svic_url_with_lang(home_url('/contact'))); ?>"><?php echo svic_translate_html('frontpage.traffic.links.contact'); ?></a>
      </div>
    </div>
  </section>

  <?php
$certificate_asset_relative = '/assets/images/certification-authorized-dealer.webp';
  $certificate_asset_path     = get_template_directory() . $certificate_asset_relative;
  $certificate_asset_url      = file_exists($certificate_asset_path) ? svic_theme_image_uri($certificate_asset_relative) : '';
  ?>
  <!-- What's in the Box -->
  <section class="lumen-inbox" id="whats-in-the-box" aria-labelledby="inbox-heading">
    <div class="lumen-inbox__inner">
      <h2 class="lumen-inbox__heading" id="inbox-heading"><?php echo svic_translate_html('frontpage.inbox.title'); ?></h2>
      <ul class="lumen-inbox__list" role="list">
        <?php
        $inbox_items = [
          'frontpage.inbox.items.box',
          'frontpage.inbox.items.power',
          'frontpage.inbox.items.hdmi',
          'frontpage.inbox.items.remote',
          'frontpage.inbox.items.guide',
        ];
        foreach ($inbox_items as $item_key) : ?>
          <li class="lumen-inbox__item"><?php echo svic_translate_html($item_key); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

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

  <!-- Inline CTA: post-certification -->
  <div class="lumen-inline-cta" aria-label="<?php echo svic_translate_attr('frontpage.aria.shop_now'); ?>">
    <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($hero_10p_url); ?>"><?php echo svic_translate_html('frontpage.inline_cta.button'); ?></a>
    <ul class="lumen-inline-cta__trust" role="list">
      <li><?php echo svic_translate_html('frontpage.inline_cta.trust.shipping'); ?></li>
      <li><?php echo svic_translate_html('frontpage.inline_cta.trust.speed'); ?></li>
      <li><?php echo svic_translate_html('frontpage.inline_cta.trust.returns'); ?></li>
    </ul>
  </div>

  <!-- Credibility Bar -->
  <section class="lumen-metrics" aria-label="<?php echo svic_translate_attr('frontpage.aria.metrics'); ?>" data-bg>
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
  <section class="lumen-feature-grid" id="experience" data-bg>
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

  <!-- Inline CTA: post-features -->
  <div class="lumen-inline-cta" aria-label="<?php echo svic_translate_attr('frontpage.aria.shop_now'); ?>">
    <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($hero_10p_url); ?>"><?php echo svic_translate_html('frontpage.inline_cta.button'); ?></a>
    <ul class="lumen-inline-cta__trust" role="list">
      <li><?php echo svic_translate_html('frontpage.inline_cta.trust.shipping'); ?></li>
      <li><?php echo svic_translate_html('frontpage.inline_cta.trust.speed'); ?></li>
      <li><?php echo svic_translate_html('frontpage.inline_cta.trust.returns'); ?></li>
    </ul>
  </div>

  <!-- Experience Section -->
  <section class="lumen-experience" data-bg>
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

  <?php wp_reset_postdata(); ?>

  <?php if (defined('SVIC_TESTIMONIALS_ENABLED') && SVIC_TESTIMONIALS_ENABLED) : ?>
  <!-- Testimonials Section -->
  <?php
    $testimonial_quotes = svic_translate_array('frontpage.testimonials.quotes');
    if (!is_array($testimonial_quotes)) { $testimonial_quotes = []; }
  ?>
  <?php if ($testimonial_quotes) : ?>
  <section class="lumen-testimonials" id="testimonials" aria-labelledby="testimonials-title">
    <div class="lumen-testimonials__inner">
      <header class="lumen-section-header">
        <span class="lumen-testimonials__badge"><?php echo svic_translate_html('frontpage.testimonials.badge'); ?></span>
        <h2 id="testimonials-title" class="lumen-section-header__title"><?php echo svic_translate_html('frontpage.testimonials.title'); ?></h2>
        <p class="lumen-section-header__subtitle"><?php echo svic_translate_html('frontpage.testimonials.subtitle'); ?></p>
      </header>
      <div class="lumen-testimonials__grid">
        <?php foreach ($testimonial_quotes as $q) : if (!is_array($q)) continue; ?>
          <figure class="lumen-testimonial-card">
            <blockquote class="lumen-testimonial-card__quote">
              <p>“<?php echo esc_html($q['quote'] ?? ''); ?>”</p>
            </blockquote>
            <figcaption class="lumen-testimonial-card__attribution">
              <span class="lumen-testimonial-card__name"><?php echo esc_html($q['name'] ?? ''); ?></span>
              <span class="lumen-testimonial-card__city"><?php echo esc_html($q['city'] ?? ''); ?></span>
              <?php if (!empty($q['source'])) : ?>
                <span class="lumen-testimonial-card__source"><?php echo esc_html($q['source']); ?></span>
              <?php endif; ?>
            </figcaption>
          </figure>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>
  <?php endif; ?>

  <!-- Pricing Section -->
  <section class="lumen-pricing" id="pricing">
    <div class="lumen-pricing__inner">
      <header class="lumen-section-header">
        <h2 class="lumen-section-header__title"><?php echo svic_translate_html('frontpage.pricing.title'); ?></h2>
        <p class="lumen-section-header__subtitle"><?php echo svic_translate_html('frontpage.pricing.subtitle'); ?></p>
        <div class="lumen-pricing__quick-buy" aria-label="<?php echo esc_attr(svic_translate('frontpage.pricing.cards.10p.buy_cta')); ?>">
          <a class="lumen-pill lumen-pill--primary lumen-pricing__quick-buy-cta" href="<?php echo esc_url($hero_10p_url); ?>" rel="nofollow" data-svic-event="svic_cta_click" data-svic-location="homepage_pricing_header" data-svic-label="pricing_header_buy_10p" data-svic-model="svicloud-10p-plus">
            <?php echo svic_translate_html('frontpage.pricing.cards.10p.buy_cta'); ?>
          </a>
          <span class="lumen-pricing__quick-buy-note"><?php echo svic_translate_html('frontpage.pricing.stock_note'); ?></span>
        </div>
      </header>
      <?php
$svic_all_in_stock = true;
foreach ($pricing_cards as $_svic_card) {
    $_svic_p = $_svic_card['product'] ?? null;
    if ($_svic_p instanceof WC_Product && ! $_svic_p->is_in_stock()) {
        $svic_all_in_stock = false;
        break;
    }
}
?>
      <?php if ($svic_all_in_stock) : ?>
        <p class="lumen-pricing__stock-note"><?php echo svic_translate_html('frontpage.pricing.stock_note'); ?></p>
      <?php endif; ?>
      <!-- SVIC_CTA_20260511_INLINE: intentionally inline-styled so CDN/CSS cache cannot hide the primary mobile CTA. -->
      <div class="lumen-pricing__inline-buy-banner" style="display:flex!important;align-items:center!important;justify-content:center!important;gap:12px!important;flex-wrap:wrap!important;width:100%!important;max-width:680px!important;margin:0 auto 22px!important;padding:14px 16px!important;border:1px solid rgba(94,230,208,.42)!important;border-radius:18px!important;background:linear-gradient(135deg,rgba(94,230,208,.22),rgba(56,189,248,.12))!important;box-shadow:0 18px 42px rgba(3,10,28,.38)!important;text-align:center!important;box-sizing:border-box!important;">
        <span style="display:block!important;color:#f4f8ff!important;font-weight:700!important;font-size:15px!important;line-height:1.35!important;"><?php echo svic_translate_html('frontpage.pricing.cards.10p.title'); ?></span>
        <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($hero_10p_url); ?>" rel="nofollow" data-svic-event="svic_cta_click" data-svic-location="homepage_pricing_inline_banner" data-svic-label="inline_banner_buy_10p" data-svic-model="svicloud-10p-plus" style="display:inline-flex!important;align-items:center!important;justify-content:center!important;min-height:46px!important;min-width:190px!important;padding:12px 22px!important;border-radius:999px!important;background:#5ee6d0!important;color:#03101f!important;font-weight:800!important;text-decoration:none!important;box-shadow:0 14px 32px rgba(94,230,208,.32)!important;">
          <?php echo svic_translate_html('frontpage.pricing.cards.10p.buy_cta'); ?>
        </a>
        <span style="display:block!important;width:100%!important;color:rgba(220,236,255,.86)!important;font-size:13px!important;font-weight:600!important;line-height:1.35!important;"><?php echo svic_translate_html('frontpage.pricing.stock_note'); ?></span>
      </div>
      <div class="lumen-pricing__grid">
        <?php foreach ($pricing_cards as $slug => $card) : ?>
          <?php
            $card_classes = 'shop-product-card';
            if (!empty($card['highlight'])) {
                $card_classes .= ' shop-product-card--highlight';
            }
            if (!empty($card['modifier'])) {
                $card_classes .= ' ' . sanitize_html_class($card['modifier']);
            }
            $model_slug = $slug === '10p' ? 'svicloud-10p-plus' : 'svicloud-10s';
          ?>
          <article class="<?php echo esc_attr($card_classes); ?>">
            <div class="shop-product-card__header">
              <?php if (!empty($card['badge_key'])) : ?>
                <span class="shop-product-card__badge"><?php echo svic_translate_html($card['badge_key']); ?></span>
              <?php endif; ?>
              <?php if (!empty($card['image']['url'])) : ?>
                <figure class="shop-product-card__media">
                  <img src="<?php echo esc_url($card['image']['url']); ?>" alt="<?php echo esc_attr($card['image']['alt'] ?? ''); ?>" loading="lazy" width="520" height="520" />
                </figure>
              <?php endif; ?>
              <div class="shop-product-card__price-line">
                <span class="shop-product-card__price-label"><?php echo svic_translate_html('shop.cards.price_label'); ?></span>
                <span class="shop-product-card__price-amount"><?php echo $card['price_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
              </div>
              <?php if (!empty($card['price_note_key'])) : ?>
                <span class="shop-product-card__price-note"><?php echo svic_translate_html($card['price_note_key']); ?></span>
              <?php endif; ?>
              <?php if (!empty($card['savings_html'])) : ?>
                <span class="shop-product-card__price-note shop-product-card__price-note--sale"><?php echo esc_html($card['savings_html']); ?></span>
              <?php endif; ?>
              <h3 class="shop-product-card__title"><?php echo svic_translate_html($card['title_key']); ?></h3>
              <p class="shop-product-card__lead"><?php echo svic_translate_html($card['copy_key']); ?></p>
              <a class="lumen-pill <?php echo !empty($card['highlight']) ? 'lumen-pill--primary' : 'lumen-pill--ghost'; ?> shop-product-card__cta" href="<?php echo esc_url($card['buy_url']); ?>" rel="nofollow" data-svic-event="svic_cta_click" data-svic-location="homepage_pricing_buy" data-svic-label="<?php echo esc_attr($slug); ?>_buy" data-svic-model="<?php echo esc_attr($model_slug); ?>"><?php echo svic_translate_html($card['buy_cta_key']); ?></a>
            </div>
            <div class="shop-product-card__divider" aria-hidden="true"></div>
            <div class="shop-product-card__body">
              <ul class="shop-product-card__features">
                <?php foreach ($card['feature_keys'] as $fk) : ?>
                  <li><?php echo svic_translate_html($fk); ?></li>
                <?php endforeach; ?>
              </ul>
              <ul class="shop-product-card__assurance">
                <?php foreach ($card['assurance_keys'] as $ak) : ?>
                  <li><?php echo svic_translate_html($ak); ?></li>
                <?php endforeach; ?>
              </ul>
              <a class="lumen-pricing__details-link" href="<?php echo esc_url($card['cta_url']); ?>" data-svic-event="svic_cta_click" data-svic-location="homepage_pricing_detail" data-svic-label="<?php echo esc_attr($slug); ?>_details" data-svic-model="<?php echo esc_attr($model_slug); ?>"><?php echo svic_translate_html($card['cta_key']); ?></a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="lumen-confidence" id="buy-confidence" aria-labelledby="buy-confidence-title">
    <div class="lumen-confidence__inner">
      <header class="lumen-section-header lumen-confidence__header">
        <span class="lumen-confidence__badge"><?php echo svic_translate_html('frontpage.confidence.badge'); ?></span>
        <h2 class="lumen-section-header__title" id="buy-confidence-title"><?php echo svic_translate_html('frontpage.confidence.title'); ?></h2>
        <p class="lumen-section-header__subtitle"><?php echo svic_translate_html('frontpage.confidence.subtitle'); ?></p>
      </header>

      <div class="lumen-confidence__layout">
        <div class="lumen-confidence__grid">
          <?php foreach ($frontpage_confidence_cards as $confidence_base_key) : ?>
            <article class="lumen-confidence-card">
              <h3 class="lumen-confidence-card__title"><?php echo svic_translate_html($confidence_base_key . '.title'); ?></h3>
              <p class="lumen-confidence-card__copy"><?php echo svic_translate_html($confidence_base_key . '.copy'); ?></p>
            </article>
          <?php endforeach; ?>
        </div>

        <aside class="lumen-confidence__timeline" aria-label="<?php echo svic_translate_attr('frontpage.confidence.timeline.aria_label'); ?>">
          <div class="lumen-confidence__timeline-header">
            <span class="lumen-confidence__timeline-badge"><?php echo svic_translate_html('frontpage.confidence.timeline.badge'); ?></span>
            <h3 class="lumen-confidence__timeline-title"><?php echo svic_translate_html('frontpage.confidence.timeline.title'); ?></h3>
            <p class="lumen-confidence__timeline-lead"><?php echo svic_translate_html('frontpage.confidence.timeline.lead'); ?></p>
          </div>
          <ol class="lumen-confidence__steps">
            <?php foreach ($frontpage_confidence_steps as $index => $step_base_key) : ?>
              <li class="lumen-confidence__step">
                <span class="lumen-confidence__step-count"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
                <div class="lumen-confidence__step-copy">
                  <strong><?php echo svic_translate_html($step_base_key . '.title'); ?></strong>
                  <p><?php echo svic_translate_html($step_base_key . '.copy'); ?></p>
                </div>
              </li>
            <?php endforeach; ?>
          </ol>
          <div class="lumen-confidence__actions lumen-action-group">
            <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($hero_10p_url); ?>" data-svic-event="svic_cta_click" data-svic-location="homepage_confidence" data-svic-label="buy_10p" data-svic-model="svicloud-10p-plus"><?php echo svic_translate_html('frontpage.pricing.cards.10p.buy_cta'); ?></a>
            <a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url($frontpage_compare_url); ?>" data-svic-event="svic_cta_click" data-svic-location="homepage_confidence" data-svic-label="compare_models"><?php echo svic_translate_html('frontpage.traffic.links.compare'); ?></a>
            <a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url($frontpage_contact_url); ?>" data-svic-event="svic_cta_click" data-svic-location="homepage_confidence" data-svic-label="contact_concierge"><?php echo svic_translate_html('frontpage.traffic.links.contact'); ?></a>
          </div>
        </aside>
      </div>
    </div>
  </section>

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
  $graph_nodes = [];

  if (!empty($product_schema_nodes)) {
      if (!empty($item_list_elements)) {
          $graph_nodes[] = [
              '@type'            => 'ItemList',
              '@id'              => svic_url_with_lang(home_url('/compare')) . '#product-list',
              'name'             => svic_translate('frontpage.schema.item_list_name'),
              'url'              => esc_url_raw($pricing_section_url),
              'numberOfItems'    => count($item_list_elements),
              'itemListOrder'    => 'https://schema.org/ItemListOrderAscending',
              'itemListElement'  => $item_list_elements,
          ];
      }

      $graph_nodes = array_merge($graph_nodes, $product_schema_nodes);
  }

  // FAQPage schema — surfaces rich snippets in Google SERPs.
  $faq_schema_entries = [];
  foreach ($faq_groups as $group) {
      foreach ($group['items'] as $item) {
          $q = trim(wp_strip_all_tags(svic_translate($item['question_key'])));
          $a = trim(wp_strip_all_tags(svic_translate($item['answer_key'])));
          if ($q !== '' && $a !== '') {
              $faq_schema_entries[] = [
                  '@type'          => 'Question',
                  'name'           => $q,
                  'acceptedAnswer' => [
                      '@type' => 'Answer',
                      'text'  => $a,
                  ],
              ];
          }
      }
  }
  if (!empty($faq_schema_entries)) {
      $graph_nodes[] = [
          '@type'      => 'FAQPage',
          '@id'        => esc_url_raw(svic_get_localized_canonical_url()) . '#faqpage',
          'mainEntity' => $faq_schema_entries,
      ];
  }

  if (!empty($graph_nodes)) {
      echo '<script type="application/ld+json">' . wp_json_encode([
          '@context' => 'https://schema.org',
          '@graph'   => $graph_nodes,
      ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
  }
  ?>

<?php
$sticky_10p_url = $hero_10p_url; // already computed earlier in the file
?>
</main>

<!-- Mobile sticky buy bar — outside <main> to preserve landmark semantics -->
<div class="lumen-sticky-buy" id="lumen-sticky-buy" aria-hidden="true">
  <div class="lumen-sticky-buy__inner">
    <span class="lumen-sticky-buy__label"><?php echo svic_translate_html('frontpage.sticky_buy.label'); ?></span>
    <a class="lumen-sticky-buy__cta lumen-pill lumen-pill--primary" href="<?php echo esc_url($sticky_10p_url); ?>" rel="nofollow" data-svic-event="svic_cta_click" data-svic-location="homepage_sticky_buy" data-svic-label="sticky_buy_10p" data-svic-model="svicloud-10p-plus">
      <?php echo svic_translate_html('frontpage.sticky_buy.cta'); ?>
    </a>
  </div>
</div>
<script>
(function() {
  var bar = document.getElementById('lumen-sticky-buy');
  if (!bar) return;
  var hero = document.getElementById('hero');
  var header = document.querySelector('[data-lumen-header]');
  var mobileQuery = window.matchMedia('(max-width: 767px)');
  var ticking = false;

  function setMobileTopOffset() {
    if (!mobileQuery.matches || !header) {
      return;
    }

    var rect = header.getBoundingClientRect();
    var top = Math.max(0, Math.round(rect.bottom + 6));
    document.documentElement.style.setProperty('--lumen-sticky-buy-top', top + 'px');
  }

  function update() {
    ticking = false;

    if (mobileQuery.matches) {
      setMobileTopOffset();
      bar.classList.add('is-visible');
      bar.removeAttribute('aria-hidden');
      document.body.classList.add('has-lumen-sticky-buy');
      return;
    }

    var threshold = hero ? (hero.offsetTop + hero.offsetHeight) : 400;
    var visible = window.scrollY > threshold;
    bar.classList.toggle('is-visible', visible);
    document.body.classList.toggle('has-lumen-sticky-buy', visible);
    if (visible) {
      bar.removeAttribute('aria-hidden');
    } else {
      bar.setAttribute('aria-hidden', 'true');
    }
  }

  function requestUpdate() {
    if (ticking) {
      return;
    }
    ticking = true;
    window.requestAnimationFrame ? window.requestAnimationFrame(update) : window.setTimeout(update, 16);
  }

  window.addEventListener('scroll', requestUpdate, { passive: true });
  window.addEventListener('resize', requestUpdate);
  window.addEventListener('orientationchange', requestUpdate);
  if (mobileQuery.addEventListener) {
    mobileQuery.addEventListener('change', requestUpdate);
  } else if (mobileQuery.addListener) {
    mobileQuery.addListener(requestUpdate);
  }
  requestUpdate();
})();
</script>

<?php get_footer(); ?>
