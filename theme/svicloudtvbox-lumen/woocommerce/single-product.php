<?php
/**
 * WooCommerce Single Product (Classic)
 */

defined('ABSPATH') || exit;

get_header('shop');

while (have_posts()) :
    the_post();

    global $product;
    if (!$product) {
        $product = function_exists('wc_get_product') ? wc_get_product(get_the_ID()) : null;
    }

    remove_action('woocommerce_before_single_product', 'woocommerce_output_all_notices', 10);
    remove_action('woocommerce_before_single_product', 'wc_print_notices', 10);

    do_action('woocommerce_before_single_product');

    if (post_password_required()) {
        echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        do_action('woocommerce_after_single_product');
        continue;
    }

    if (!$product) {
        echo '<main id="main-content" class="page-shell lumen-product" tabindex="-1"><p class="woocommerce-info">' . esc_html__('Product unavailable.', 'svicloudtvbox-lumen') . '</p></main>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        do_action('woocommerce_after_single_product');
        continue;
    }

    // Images
    $image_id = $product->get_image_id();
    $gallery = method_exists($product, 'get_gallery_image_ids') ? (array) $product->get_gallery_image_ids() : [];
    $slug = method_exists($product, 'get_slug') ? $product->get_slug() : '';
    $is_15p_product = $slug === 'svicloud-15p';

    $fallback_gallery_files = [];
    $localized_product_title = $is_15p_product
        ? svic_translate('products.svicloud-15p.title')
        : get_the_title();

    $gallery_entries = [];
    if ($gallery) {
        foreach ($gallery as $gid) {
            $full = wp_get_attachment_image_url($gid, 'large');
            if (!$full) {
                continue;
            }
            $thumb_html = wp_get_attachment_image($gid, 'thumbnail', false, ['class' => 'product-thumb-img', 'loading' => 'lazy']);
            $srcset = wp_get_attachment_image_srcset($gid, 'large') ?: '';
            $gallery_entries[] = [
                'full'   => $full,
                'thumb'  => $thumb_html,
                'srcset' => $srcset,
            ];
        }
    }

    if (empty($gallery_entries) && $fallback_gallery_files) {
        foreach ($fallback_gallery_files as $file) {
            $full = svic_theme_image_uri('/assets/images/' . $file);
            $thumb_html = '<img src="' . esc_url($full) . '" alt="" class="product-thumb-img" loading="lazy" />';
            $gallery_entries[] = [
                'full'   => $full,
                'thumb'  => $thumb_html,
                'srcset' => '',
            ];
        }
    }

    $primary_image_html = '';
    if ($image_id) {
        $primary_image_html = wp_get_attachment_image($image_id, 'large', false, [
            'class' => 'product-hero-image',
            'alt'   => esc_attr($localized_product_title),
        ]);
    } elseif (!empty($gallery_entries)) {
        $primary_image_html = '<img class="product-hero-image" src="' . esc_url($gallery_entries[0]['full']) . '" alt="' . esc_attr(get_the_title()) . '" loading="lazy" />';
    } elseif ($is_15p_product) {
        $primary_image_html = '<img class="product-hero-image" src="' . esc_url(svic_theme_image_uri('/assets/images/products/svicloud-15p-primary-studio-v2-watermarked.webp')) . '" alt="' . esc_attr($localized_product_title) . '" />';
    } else {
        $primary_image_html = '<img class="product-hero-image" src="' . esc_url(svic_theme_image_uri('/assets/images/svicloud-hero-product.webp')) . '" alt="' . esc_attr(get_the_title()) . '" />';
    }

    $product_highlight_keys = $is_15p_product
        ? [
            'products.svicloud-15p.prelaunch.highlights.specs',
            'products.svicloud-15p.prelaunch.highlights.availability',
            'products.svicloud-15p.prelaunch.highlights.policy',
        ]
        : [
            'product.highlights.inventory',
            'product.highlights.concierge',
            'product.highlights.no_fees',
        ];
    $product_badge_keys = $is_15p_product
        ? [
            'products.svicloud-15p.prelaunch.badges.specs',
            'products.svicloud-15p.prelaunch.badges.availability',
            'products.svicloud-15p.prelaunch.badges.policy',
        ]
        : [
            'core.badges.authorized_dealer',
            'core.badges.ships_from_usa',
            'core.badges.one_year_warranty',
        ];
    $product_subtitle_key = $is_15p_product
        ? 'products.svicloud-15p.prelaunch.subtitle'
        : 'product.hero.subtitle';
    $product_detail_key = $is_15p_product
        ? 'products.svicloud-15p.prelaunch.detail'
        : 'product.hero.detail';

    $compare_url     = svic_url_with_lang(home_url('/compare/'));
    $faq_url         = svic_url_with_lang(home_url('/faq/'));
    $contact_url     = svic_url_with_lang(home_url('/contact/'));
    $setup_guide_url = svic_url_with_lang(home_url('/guides-setup/'));
$fifteenp_features_url = function_exists('svic_15p_promo_url')
    ? svic_15p_promo_url()
    : svic_url_with_lang(home_url('/svicloud-15p-features/'));
$fifteenp_promo_content = function_exists('svic_15p_promo_content') ? svic_15p_promo_content() : [];
$fifteenp_features_label = $fifteenp_promo_content['features_link'] ?? 'Explore 15P features, specs, and Yogurt TV Go guidance';

    $product_box_item_keys = $is_15p_product
        ? [
            'products.svicloud-15p.inbox.items.box',
            'products.svicloud-15p.inbox.items.power',
            'products.svicloud-15p.inbox.items.hdmi',
            'products.svicloud-15p.inbox.items.remote',
            'products.svicloud-15p.inbox.items.manual',
        ]
        : [
            'frontpage.inbox.items.box',
            'frontpage.inbox.items.power',
            'frontpage.inbox.items.hdmi',
            'frontpage.inbox.items.remote',
            'frontpage.inbox.items.guide',
        ];

    $product_best_for_key_base = 'products.' . $slug . '.best_for';
    $product_best_for_title = svic_translate($product_best_for_key_base . '.title');
    $product_best_for_items = [
        $product_best_for_key_base . '.bullets.primary',
        $product_best_for_key_base . '.bullets.secondary',
        $product_best_for_key_base . '.bullets.tertiary',
    ];

    $product_reassurance_key_base = $is_15p_product
        ? 'products.svicloud-15p.prelaunch.reassurance'
        : 'product.hero.reassurance';
    $product_reassurance_title = svic_translate($product_reassurance_key_base . '.title');
    $product_reassurance_items = [
        $product_reassurance_key_base . '.bullets.shipping',
        $product_reassurance_key_base . '.bullets.warranty',
        $product_reassurance_key_base . '.bullets.concierge',
    ];

    $product_faq_header_base = $is_15p_product
        ? 'products.svicloud-15p.prelaunch.faq_header'
        : 'product.faq';
    $product_faq_items = $is_15p_product ? [
        [
            'question_key' => 'products.svicloud-15p.prelaunch.faq.specs.q',
            'answer_key'   => 'products.svicloud-15p.prelaunch.faq.specs.a',
        ],
        [
            'question_key' => 'products.svicloud-15p.prelaunch.faq.availability.q',
            'answer_key'   => 'products.svicloud-15p.prelaunch.faq.availability.a',
        ],
        [
            'question_key' => 'products.svicloud-15p.prelaunch.faq.policy.q',
            'answer_key'   => 'products.svicloud-15p.prelaunch.faq.policy.a',
        ],
    ] : [
        [
            'question_key' => 'product.faq.items.shipping.q',
            'answer_key'   => 'product.faq.items.shipping.a',
        ],
        [
            'question_key' => 'product.faq.items.warranty.q',
            'answer_key'   => 'product.faq.items.warranty.a',
        ],
        [
            'question_key' => 'product.faq.items.concierge.q',
            'answer_key'   => 'product.faq.items.concierge.a',
        ],
        [
            'question_key' => 'product.faq.sections.device_models.items.model_choice.question',
            'answer_key'   => 'product.faq.sections.device_models.items.model_choice.answer',
            'replacements' => [
                'compare_url' => esc_url($compare_url),
            ],
        ],
        [
            'question_key' => 'product.faq.sections.device_models.items.box_contents.question',
            'answer_key'   => 'product.faq.sections.device_models.items.box_contents.answer',
        ],
        [
            'question_key' => 'product.faq.sections.models.items.after_warranty.question',
            'answer_key'   => 'product.faq.sections.models.items.after_warranty.answer',
        ],
        [
            'question_key' => 'product.faq.sections.troubleshooting_support.items.buffering.question',
            'answer_key'   => 'product.faq.sections.troubleshooting_support.items.buffering.answer',
            'replacements' => [
                'setup_guide_url' => esc_url($setup_guide_url),
            ],
        ],
    ];
    ?>

    <main id="main-content" class="page-shell lumen-product" tabindex="-1">
      <section class="product-hero">
        <div class="product-hero-inner">
          <div class="product-hero-media">
            <div class="product-hero-stage">
              <?php echo $primary_image_html; ?>
              <div class="product-hero-glow" aria-hidden="true"></div>
            </div>
            <?php if (!empty($gallery_entries)) : ?>
              <div class="product-hero-thumbs" role="list">
                <?php foreach ($gallery_entries as $index => $entry) :
                    $is_active = ($index === 0);
                    $button_classes = $is_active ? 'product-thumb active' : 'product-thumb';
                ?>
                  <button type="button" class="<?php echo esc_attr($button_classes); ?>" data-image="<?php echo esc_url($entry['full']); ?>" data-srcset="<?php echo esc_attr($entry['srcset']); ?>" aria-pressed="<?php echo $is_active ? 'true' : 'false'; ?>">
                    <?php echo wp_kses_post($entry['thumb']); ?>
                  </button>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>

          <div class="product-hero-content">
            <?php
            if (function_exists('woocommerce_output_all_notices')) {
                woocommerce_output_all_notices();
            } elseif (function_exists('wc_print_notices')) {
                wc_print_notices();
            }
            ?>
            <div class="badge-row">
              <?php foreach ($product_badge_keys as $product_badge_key) : ?>
                <span class="badge"><?php echo svic_translate_html($product_badge_key); ?></span>
              <?php endforeach; ?>
            </div>
            <h1 class="product-hero-title"><?php echo esc_html($localized_product_title); ?></h1>
            <p class="product-hero-subtitle">
              <?php echo svic_translate_html($product_subtitle_key); ?>
            </p>
            <?php if ($is_15p_product) :
                $localized_short_description = apply_filters('woocommerce_short_description', $product->get_short_description());
                $localized_short_description_text = wp_strip_all_tags((string) $localized_short_description);
            ?>
              <?php if ($localized_short_description_text !== '') : ?>
                <p class="product-hero-subtitle product-hero-short-description">
                  <?php echo esc_html($localized_short_description_text); ?>
                </p>
              <?php endif; ?>
            <?php endif; ?>
            <?php if (is_string($product_best_for_title) && $product_best_for_title !== '' && $product_best_for_title !== 'title') : ?>
              <section class="product-hero-best-for" aria-labelledby="product-best-for-heading">
                <span class="product-hero-best-for__badge"><?php echo svic_translate_html($product_best_for_key_base . '.badge'); ?></span>
                <h2 class="product-hero-best-for__title" id="product-best-for-heading"><?php echo esc_html($product_best_for_title); ?></h2>
                <p class="product-hero-best-for__copy"><?php echo svic_translate_html($product_best_for_key_base . '.copy'); ?></p>
                <ul class="product-hero-best-for__list" role="list">
                  <?php foreach ($product_best_for_items as $best_for_item_key) : ?>
                    <li><?php echo svic_translate_html($best_for_item_key); ?></li>
                  <?php endforeach; ?>
                </ul>
                <a class="product-hero-best-for__link" href="<?php echo esc_url($compare_url); ?>"><?php echo svic_translate_html('product.traffic.links.compare'); ?></a>
              </section>
            <?php endif; ?>
            <div class="product-hero-price"><?php echo $product->get_price_html(); ?></div>
            <div class="product-hero-cta">
              <?php woocommerce_template_single_add_to_cart(); ?>
            </div>
            <?php if ($is_15p_product) : ?>
              <?php svic_render_15p_delivery_banner(); ?>
              <p class="svic-15p-feature-link">
                <a href="<?php echo esc_url($fifteenp_features_url); ?>"><?php echo esc_html($fifteenp_features_label); ?></a>
              </p>
            <?php endif; ?>
            <?php if (is_string($product_reassurance_title) && $product_reassurance_title !== '' && $product_reassurance_title !== 'title') : ?>
              <section class="product-hero-reassurance" aria-labelledby="product-hero-reassurance-heading">
                <span class="product-hero-reassurance__badge"><?php echo svic_translate_html($product_reassurance_key_base . '.badge'); ?></span>
                <h2 class="product-hero-reassurance__title" id="product-hero-reassurance-heading"><?php echo esc_html($product_reassurance_title); ?></h2>
                <p class="product-hero-reassurance__copy"><?php echo svic_translate_html($product_reassurance_key_base . '.copy'); ?></p>
                <ul class="product-hero-reassurance__list" role="list">
                  <?php foreach ($product_reassurance_items as $product_reassurance_item_key) : ?>
                    <li><?php echo svic_translate_html($product_reassurance_item_key); ?></li>
                  <?php endforeach; ?>
                </ul>
                <div class="product-hero-reassurance__links">
                  <a href="<?php echo esc_url($faq_url); ?>"><?php echo svic_translate_html('product.traffic.links.faq'); ?></a>
                  <a href="<?php echo esc_url($contact_url); ?>"><?php echo svic_translate_html('product.traffic.links.contact'); ?></a>
                </div>
              </section>
            <?php endif; ?>
            <div class="product-hero-detail text-small">
              <?php echo svic_translate_html($product_detail_key); ?>
            </div>
            <ul class="product-hero-points">
              <?php foreach ($product_highlight_keys as $highlight_key) : ?>
                <li><?php echo svic_translate_html($highlight_key); ?></li>
              <?php endforeach; ?>
            </ul>
            <div class="product-meta-block">
              <?php woocommerce_template_single_meta(); ?>
            </div>
          </div>
        </div>
      </section>

      <section class="product-description">
        <h2 class="h3 spacing-normal"><?php echo esc_html__('Description', 'svicloudtvbox-lumen'); ?></h2>
        <div class="entry-content">
          <?php
          $description_html = apply_filters('the_content', get_post_field('post_content', get_the_ID()));
          $description_key = 'products.' . $slug . '.description';
          $translated_description = svic_translate_rich($description_key);

          if (is_string($translated_description) && $translated_description !== '' && $translated_description !== 'description') {
              $description_html = $translated_description;
          }

          echo wp_kses_post($description_html);
          ?>
        </div>
      </section>

      <?php
      $crosslink_base  = 'products.' . $slug . '.crosslink';
      $crosslink_title = svic_translate($crosslink_base . '.title');
      if (is_string($crosslink_title) && $crosslink_title !== '' && $crosslink_title !== 'title') :
          $crosslink_target = svic_translate($crosslink_base . '.target');
          $crosslink_url = svic_url_with_lang(home_url('/product/' . sanitize_title($crosslink_target) . '/'));
      ?>
      <aside class="pdp-crosslink" aria-labelledby="pdp-crosslink-title">
        <div class="pdp-crosslink__copy">
          <span class="pdp-crosslink__badge"><?php echo svic_translate_html($crosslink_base . '.badge'); ?></span>
          <h2 class="pdp-crosslink__title" id="pdp-crosslink-title"><?php echo esc_html($crosslink_title); ?></h2>
          <p class="pdp-crosslink__lead"><?php echo svic_translate_html($crosslink_base . '.lead'); ?></p>
        </div>
        <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($crosslink_url); ?>"><?php echo svic_translate_html($crosslink_base . '.cta'); ?></a>
      </aside>
      <?php endif; ?>

      <section class="product-description product-description--inbox">
        <h2 class="h3 spacing-normal"><?php echo svic_translate_html('frontpage.inbox.title'); ?></h2>
        <div class="entry-content">
          <ul>
            <?php foreach ($product_box_item_keys as $box_item_key) : ?>
              <li><?php echo svic_translate_html($box_item_key); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </section>

      <section class="product-traffic">
        <div class="product-traffic__inner">
          <div class="product-traffic__copy">
            <?php
            $traffic_key_base = 'products.' . $slug . '.traffic';
            $traffic_badge = svic_translate($traffic_key_base . '.badge');
            if (!is_string($traffic_badge) || $traffic_badge === '' || $traffic_badge === 'badge') {
                $traffic_key_base = 'product.traffic';
                $traffic_badge = svic_translate($traffic_key_base . '.badge');
            }
            ?>
            <span class="product-traffic__badge"><?php echo esc_html($traffic_badge); ?></span>
            <h2 class="product-traffic__title"><?php echo svic_translate_html($traffic_key_base . '.title'); ?></h2>
            <p class="product-traffic__lead"><?php echo svic_translate_html($traffic_key_base . '.lead'); ?></p>
            <ul class="product-traffic__list" role="list">
              <li><?php echo svic_translate_html($traffic_key_base . '.bullets.shipping'); ?></li>
              <li><?php echo svic_translate_html($traffic_key_base . '.bullets.concierge'); ?></li>
              <li><?php echo svic_translate_html($traffic_key_base . '.bullets.warranty'); ?></li>
            </ul>
          </div>
          <div class="product-traffic__links lumen-action-group" role="group" aria-label="<?php echo svic_translate_attr('product.aria.traffic_actions'); ?>">
            <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($compare_url); ?>"><?php echo svic_translate_html('product.traffic.links.compare'); ?></a>
            <a class="lumen-pill lumen-pill--ghost" href="<?php echo esc_url($faq_url); ?>"><?php echo svic_translate_html('product.traffic.links.faq'); ?></a>
            <a class="product-traffic__textlink" href="<?php echo esc_url($contact_url); ?>"><?php echo svic_translate_html('product.traffic.links.contact'); ?></a>
          </div>
        </div>
      </section>

      <?php
      $pdp_compare_base  = 'products.' . $slug . '.comparison';
      $pdp_compare_title = svic_translate($pdp_compare_base . '.title');
      if (is_string($pdp_compare_title) && $pdp_compare_title !== '' && $pdp_compare_title !== 'title') :
          $product_10p_url = svic_url_with_lang(home_url('/product/svicloud-10p-plus/'));
          $product_9p     = function_exists('svic_get_product_by_slug') ? svic_get_product_by_slug('svicloud-9p') : null;
          $product_9p_url = $product_9p instanceof WC_Product ? svic_url_with_lang(get_permalink($product_9p->get_id())) : '';
          $pdp_compare_cards = [
              'vs_10p' => ['bullets' => ['one', 'two', 'three'], 'link' => $product_10p_url],
              'vs_9p'  => ['bullets' => ['one', 'two', 'three'], 'link' => $product_9p_url],
          ];
      ?>
      <section class="pdp-compare" id="pdp-compare" aria-labelledby="pdp-compare-title">
        <div class="pdp-compare__inner">
          <header class="pdp-compare__header">
            <span class="pdp-compare__badge"><?php echo svic_translate_html($pdp_compare_base . '.badge'); ?></span>
            <h2 class="pdp-compare__title" id="pdp-compare-title"><?php echo esc_html($pdp_compare_title); ?></h2>
            <p class="pdp-compare__lead"><?php echo svic_translate_html($pdp_compare_base . '.lead'); ?></p>
          </header>
          <div class="pdp-compare__grid">
            <?php foreach ($pdp_compare_cards as $card_key => $card) :
                $card_base = $pdp_compare_base . '.cards.' . $card_key; ?>
              <article class="pdp-compare__card">
                <h3 class="pdp-compare__card-title"><?php echo svic_translate_html($card_base . '.title'); ?></h3>
                <p class="pdp-compare__card-summary"><?php echo svic_translate_html($card_base . '.summary'); ?></p>
                <ul class="pdp-compare__card-list" role="list">
                  <?php foreach ($card['bullets'] as $bullet_key) : ?>
                    <li><?php echo svic_translate_html($card_base . '.bullets.' . $bullet_key); ?></li>
                  <?php endforeach; ?>
                </ul>
                <?php if ($card['link'] !== '') : ?>
                  <a class="pdp-compare__card-link" href="<?php echo esc_url($card['link']); ?>"><?php echo svic_translate_html($card_base . '.link_label'); ?></a>
                <?php endif; ?>
              </article>
            <?php endforeach; ?>
          </div>
          <div class="pdp-compare__panels">
            <section class="pdp-compare__panel" aria-labelledby="pdp-compare-upgrade-title">
              <h3 class="pdp-compare__panel-title" id="pdp-compare-upgrade-title"><?php echo svic_translate_html($pdp_compare_base . '.upgrade.title'); ?></h3>
              <ul class="pdp-compare__panel-list" role="list">
                <li><?php echo svic_translate_html($pdp_compare_base . '.upgrade.items.from_9p'); ?></li>
                <li><?php echo svic_translate_html($pdp_compare_base . '.upgrade.items.from_10p'); ?></li>
                <li><?php echo svic_translate_html($pdp_compare_base . '.upgrade.items.new_buyer'); ?></li>
              </ul>
            </section>
            <section class="pdp-compare__panel" aria-labelledby="pdp-compare-assurance-title">
              <h3 class="pdp-compare__panel-title" id="pdp-compare-assurance-title"><?php echo svic_translate_html($pdp_compare_base . '.assurance.title'); ?></h3>
              <ul class="pdp-compare__panel-list" role="list">
                <li><?php echo svic_translate_html($pdp_compare_base . '.assurance.items.shipping'); ?></li>
                <li><?php echo svic_translate_html($pdp_compare_base . '.assurance.items.support'); ?></li>
                <li><?php echo svic_translate_html($pdp_compare_base . '.assurance.items.warranty'); ?></li>
              </ul>
            </section>
          </div>
        </div>
      </section>
      <?php endif; ?>

      <section class="product-faq" id="product-faq">
        <div class="product-faq__inner">
          <header class="product-faq__header">
            <span class="product-faq__badge"><?php echo svic_translate_html($product_faq_header_base . '.badge'); ?></span>
            <h2 class="product-faq__title"><?php echo svic_translate_html($product_faq_header_base . '.title'); ?></h2>
            <p class="product-faq__lead"><?php echo svic_translate_html($product_faq_header_base . '.lead'); ?></p>
          </header>
          <div class="product-faq__grid">
            <?php foreach ($product_faq_items as $idx => $item) : ?>
              <?php $answer = svic_translate_rich($item['answer_key'], $item['replacements'] ?? []); ?>
              <details class="product-faq__item"<?php echo $idx === 0 ? ' open' : ''; ?>>
                <summary class="product-faq__question"><?php echo svic_translate_html($item['question_key']); ?></summary>
                <div class="product-faq__answer"><?php echo wp_kses_post($answer); ?></div>
              </details>
            <?php endforeach; ?>
          </div>
          <div class="product-faq__cta lumen-action-group">
            <a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url($faq_url); ?>"><?php echo svic_translate_html('product.traffic.links.faq'); ?></a>
            <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($contact_url); ?>"><?php echo svic_translate_html('product.traffic.links.contact'); ?></a>
          </div>
        </div>
      </section>

      <section class="product-reviews product-reviews--google-store" id="store-reviews" aria-labelledby="product-reviews-title">
        <header class="product-reviews__header">
          <span class="product-reviews__badge"><?php echo svic_translate_html('product.reviews.badge'); ?></span>
          <h2 class="product-reviews__title" id="product-reviews-title"><?php echo svic_translate_html('product.reviews.title'); ?></h2>
          <p class="product-reviews__lead"><?php echo svic_translate_html('product.reviews.lead'); ?></p>
        </header>
        <div class="product-reviews__panel product-reviews__panel--store">
          <div class="product-reviews__average-card" aria-label="<?php echo esc_attr(svic_translate('product.reviews.average_aria')); ?>">
            <span class="product-reviews__source-label"><?php echo svic_translate_html('product.reviews.average_label'); ?></span>
            <div class="product-reviews__average-score">
              <span class="product-reviews__score-value"><?php echo esc_html(svic_translate('product.reviews.average_score')); ?></span>
              <span class="product-reviews__score-scale"><?php echo svic_translate_html('product.reviews.average_scale'); ?></span>
            </div>
            <div class="product-reviews__stars" aria-hidden="true">★★★★★</div>
            <p class="product-reviews__average-note"><?php echo svic_translate_html('product.reviews.average_note'); ?></p>
          </div>
        </div>
        <?php comments_template(); ?>
      </section>
    </main>

    <?php
    do_action('woocommerce_after_single_product');
endwhile;

get_footer('shop');
