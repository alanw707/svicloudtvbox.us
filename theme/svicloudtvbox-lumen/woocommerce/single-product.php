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
        echo '<main class="page-shell lumen-product"><p class="woocommerce-info">' . esc_html__('Product unavailable.', 'svicloudtvbox-lumen') . '</p></main>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        do_action('woocommerce_after_single_product');
        continue;
    }

    // Images
    $image_id = $product->get_image_id();
    $gallery = method_exists($product, 'get_gallery_image_ids') ? (array) $product->get_gallery_image_ids() : [];
    $slug = method_exists($product, 'get_slug') ? $product->get_slug() : '';

    $fallback_gallery_files = [];

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
            'alt'   => esc_attr(get_the_title()),
        ]);
    } elseif (!empty($gallery_entries)) {
        $primary_image_html = '<img class="product-hero-image" src="' . esc_url($gallery_entries[0]['full']) . '" alt="' . esc_attr(get_the_title()) . '" loading="lazy" />';
    } else {
        $primary_image_html = '<img class="product-hero-image" src="' . esc_url(svic_theme_image_uri('/assets/images/svicloud-hero-product.webp')) . '" alt="' . esc_attr(get_the_title()) . '" />';
    }

    $product_highlight_keys = [
        'product.highlights.inventory',
        'product.highlights.concierge',
        'product.highlights.no_fees',
    ];

    $compare_url     = svic_url_with_lang(home_url('/compare/'));
    $faq_url         = svic_url_with_lang(home_url('/faq/'));
    $contact_url     = svic_url_with_lang(home_url('/contact/'));
    $setup_guide_url = svic_url_with_lang(home_url('/guides-setup/'));

    $product_box_item_keys = [
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

    $product_reassurance_title = svic_translate('product.hero.reassurance.title');
    $product_reassurance_items = [
        'product.hero.reassurance.bullets.shipping',
        'product.hero.reassurance.bullets.warranty',
        'product.hero.reassurance.bullets.concierge',
    ];

    $product_faq_items = [
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

    <main class="page-shell lumen-product">
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
              <span class="badge"><?php echo svic_translate_html('core.badges.authorized_dealer'); ?></span>
              <span class="badge"><?php echo svic_translate_html('core.badges.ships_from_usa'); ?></span>
              <span class="badge"><?php echo svic_translate_html('core.badges.one_year_warranty'); ?></span>
            </div>
            <h1 class="product-hero-title"><?php the_title(); ?></h1>
            <p class="product-hero-subtitle">
              <?php echo svic_translate_html('product.hero.subtitle'); ?>
            </p>
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
            <?php if (is_string($product_reassurance_title) && $product_reassurance_title !== '' && $product_reassurance_title !== 'title') : ?>
              <section class="product-hero-reassurance" aria-labelledby="product-hero-reassurance-heading">
                <span class="product-hero-reassurance__badge"><?php echo svic_translate_html('product.hero.reassurance.badge'); ?></span>
                <h2 class="product-hero-reassurance__title" id="product-hero-reassurance-heading"><?php echo esc_html($product_reassurance_title); ?></h2>
                <p class="product-hero-reassurance__copy"><?php echo svic_translate_html('product.hero.reassurance.copy'); ?></p>
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
              <?php echo svic_translate_html('product.hero.detail'); ?>
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

      <section class="product-faq" id="product-faq">
        <div class="product-faq__inner">
          <header class="product-faq__header">
            <span class="product-faq__badge"><?php echo svic_translate_html('product.faq.badge'); ?></span>
            <h2 class="product-faq__title"><?php echo svic_translate_html('product.faq.title'); ?></h2>
            <p class="product-faq__lead"><?php echo svic_translate_html('product.faq.lead'); ?></p>
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
