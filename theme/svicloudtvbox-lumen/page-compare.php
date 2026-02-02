<?php
/**
 * Compare Page
 * Template Name: Compare Models
 */
get_header();

$hero_product_10p = class_exists('WooCommerce') ? svic_get_product_by_slug('svicloud-10p-plus') : null;
$hero_product_10s = class_exists('WooCommerce') ? svic_get_product_by_slug('svicloud-10s') : null;
$hero_10p_url = $hero_product_10p ? svic_url_with_lang(get_permalink($hero_product_10p->get_id())) : svic_url_with_lang(home_url('/product/svicloud-10p-plus'));
$hero_10s_url = $hero_product_10s ? svic_url_with_lang(get_permalink($hero_product_10s->get_id())) : svic_url_with_lang(home_url('/product/svicloud-10s'));
$price_10p_markup = '';
if ($hero_product_10p && function_exists('svic_price_html')) {
    $price_10p_markup = svic_price_html($hero_product_10p);
}
if ($price_10p_markup === '') {
    $price_10p_markup = sprintf(
        '<span class="lumen-price"><span class="lumen-price__current">%s</span></span>',
        esc_html('$248.99')
    );
}

$price_10s_markup = '';
if ($hero_product_10s && function_exists('svic_price_html')) {
    $price_10s_markup = svic_price_html($hero_product_10s);
}
if ($price_10s_markup === '') {
    $price_10s_markup = sprintf(
        '<span class="lumen-price"><span class="lumen-price__current">%s</span></span>',
        esc_html('$183.99')
    );
}

$comparison_rows = [
    [
        'key'       => 'ram_storage',
        'highlight' => 'p10p',
    ],
    [
        'key'       => 'video_quality',
        'highlight' => '',
    ],
    [
        'key'       => 'voice_remote',
        'highlight' => '',
    ],
    [
        'key'       => 'kids_app',
        'highlight' => 'p10p',
    ],
    [
        'key'       => 'karaoke_mode',
        'highlight' => 'p10p',
    ],
    [
        'key'       => 'best_for',
        'highlight' => '',
    ],
];

$key_differences = [
    [
        'model'    => '10P+',
        'base_key' => 'compare.differences.premium_performance',
    ],
    [
        'model'    => '10P+',
        'base_key' => 'compare.differences.family_entertainment',
    ],
    [
        'model'    => '10S',
        'base_key' => 'compare.differences.smart_value',
    ],
];

$hero_highlights = array_slice($key_differences, 0, 3);

$compare_product_bullets = [
    '10p' => [
        'compare.products.10p.bullets.ram_storage',
        'compare.products.10p.bullets.apps',
        'compare.products.10p.bullets.remote',
    ],
    '10s' => [
        'compare.products.10s.bullets.ram_storage',
        'compare.products.10s.bullets.remote',
        'compare.products.10s.bullets.ports',
    ],
];

$hero_metric_keys = [
    'ram_storage',
    'video_quality',
    'best_for',
];

$compare_faq_items = [
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
];

$compare_cta_links = [
    'p10p'    => $hero_10p_url,
    'p10s'    => $hero_10s_url,
    'faq'     => svic_url_with_lang(home_url('/faq/')),
    'contact' => svic_url_with_lang(home_url('/contact/')),
];

?>

<main class="compare-page">
  <section class="compare-hero">
    <div class="compare-hero__background" aria-hidden="true">
      <div class="compare-hero__photo compare-hero__photo--primary"></div>
      <div class="compare-hero__photo compare-hero__photo--secondary"></div>
      <div class="compare-hero__gradient compare-hero__gradient--teal"></div>
      <div class="compare-hero__gradient compare-hero__gradient--iris"></div>
      <div class="compare-hero__gradient compare-hero__gradient--amber"></div>
      <div class="compare-hero__mesh"></div>
    </div>

    <div class="compare-hero__inner">
      <div class="compare-hero__surface">
        <div class="compare-hero__body">
          <span class="compare-hero__badge"><?php echo svic_translate_html('compare.hero.badge'); ?></span>
          <h1 class="compare-hero__title" id="compare-hero-title">
            <span class="page-title-text"><?php echo svic_translate_html('compare.hero.title'); ?></span>
          </h1>
          <p class="compare-hero__subtitle">
            <span class="page-subtitle-text"><?php echo svic_translate_html('compare.hero.subtitle'); ?></span>
          </p>

          <div class="compare-hero__actions lumen-action-group" role="group" aria-label="<?php echo esc_attr__('Primary product actions', 'svicloudtvbox-lumen'); ?>">
            <a class="lumen-pill lumen-pill--primary compare-hero__action" href="<?php echo esc_url($hero_10p_url); ?>">
              <?php echo svic_translate_html('compare.products.10p.cta'); ?>
            </a>
            <a class="lumen-pill lumen-pill--ghost compare-hero__action" href="<?php echo esc_url($hero_10s_url); ?>">
              <?php echo svic_translate_html('compare.products.10s.cta'); ?>
            </a>
          </div>
        </div>

        <div class="compare-hero__devices" aria-hidden="true">
          <img
            class="compare-hero__device compare-hero__device--10p"
            src="<?php echo esc_url(svic_theme_image_uri('/assets/images/svicloud-10p-plus.webp')); ?>"
            alt=""
            loading="lazy"
            decoding="async"
            width="800"
            height="600"
          />
          <img
            class="compare-hero__device compare-hero__device--10s"
            src="<?php echo esc_url(svic_theme_image_uri('/assets/images/svicloud-tvbox-10s.webp')); ?>"
            alt=""
            loading="lazy"
            decoding="async"
            width="800"
            height="600"
          />
        </div>

        <?php if (!empty($hero_highlights)) : ?>
          <ul class="compare-hero__highlights" aria-label="<?php echo esc_attr__('Why customers choose SVICLOUD', 'svicloudtvbox-lumen'); ?>">
            <?php foreach ($hero_highlights as $highlight) : ?>
              <li class="compare-hero__highlight">
                <span class="compare-hero__highlight-model"><?php echo esc_html($highlight['model']); ?></span>
                <span class="compare-hero__highlight-text"><?php echo svic_translate_html($highlight['base_key'] . '.title'); ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <section class="compare-traffic" id="why-us">
    <div class="compare-traffic__inner">
      <div class="compare-traffic__copy">
        <span class="compare-traffic__badge"><?php echo svic_translate_html('compare.traffic.badge'); ?></span>
        <h2 class="compare-traffic__title"><?php echo svic_translate_html('compare.traffic.title'); ?></h2>
        <p class="compare-traffic__lead"><?php echo svic_translate_html('compare.traffic.lead'); ?></p>
        <ul class="compare-traffic__list" role="list">
          <li><?php echo svic_translate_html('compare.traffic.bullets.shipping'); ?></li>
          <li><?php echo svic_translate_html('compare.traffic.bullets.concierge'); ?></li>
          <li><?php echo svic_translate_html('compare.traffic.bullets.warranty'); ?></li>
        </ul>
      </div>
      <div class="compare-traffic__links lumen-action-group" role="group" aria-label="<?php echo esc_attr__('Primary compare actions', 'svicloudtvbox-lumen'); ?>">
        <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($compare_cta_links['p10p']); ?>"><?php echo svic_translate_html('compare.traffic.links.p10p'); ?></a>
        <a class="lumen-pill lumen-pill--ghost" href="<?php echo esc_url($compare_cta_links['p10s']); ?>"><?php echo svic_translate_html('compare.traffic.links.p10s'); ?></a>
        <a class="compare-traffic__textlink" href="<?php echo esc_url($compare_cta_links['faq']); ?>"><?php echo svic_translate_html('compare.traffic.links.faq'); ?></a>
        <a class="compare-traffic__textlink" href="<?php echo esc_url($compare_cta_links['contact']); ?>"><?php echo svic_translate_html('compare.traffic.links.contact'); ?></a>
      </div>
    </div>
  </section>

  <section class="compare-differences" aria-label="<?php echo esc_attr__('Key differences between models', 'svicloudtvbox-lumen'); ?>">
    <div class="compare-differences__grid">
      <?php foreach ($key_differences as $difference) : ?>
        <article class="compare-difference-card compare-difference-card--<?php echo esc_attr(strtolower($difference['model'])); ?>">
          <span class="compare-difference-card__model"><?php echo esc_html($difference['model']); ?></span>
          <h3 class="compare-difference-card__title"><?php echo svic_translate_html($difference['base_key'] . '.title'); ?></h3>
          <p class="compare-difference-card__copy"><?php echo svic_translate_html($difference['base_key'] . '.description'); ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="compare-faq" id="compare-faq">
    <div class="compare-faq__inner">
      <header class="compare-faq__header">
        <span class="compare-faq__badge"><?php echo svic_translate_html('product.faq.badge'); ?></span>
        <h2 class="compare-faq__title"><?php echo svic_translate_html('product.faq.title'); ?></h2>
        <p class="compare-faq__lead"><?php echo svic_translate_html('product.faq.lead'); ?></p>
      </header>
      <div class="compare-faq__grid">
        <?php foreach ($compare_faq_items as $idx => $item) : ?>
          <details class="compare-faq__item"<?php echo $idx === 0 ? ' open' : ''; ?>>
            <summary class="compare-faq__question"><?php echo svic_translate_html($item['question_key']); ?></summary>
            <div class="compare-faq__answer"><?php echo wp_kses_post(svic_translate($item['answer_key'])); ?></div>
          </details>
        <?php endforeach; ?>
      </div>
      <div class="compare-faq__cta">
        <a class="lumen-pill lumen-pill--outline" href="<?php echo esc_url($compare_cta_links['faq']); ?>"><?php echo svic_translate_html('product.traffic.links.faq'); ?></a>
        <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($compare_cta_links['contact']); ?>"><?php echo svic_translate_html('product.traffic.links.contact'); ?></a>
      </div>
    </div>
  </section>

  <section class="compare-products" id="product-list" aria-label="<?php echo esc_attr__('Product spotlight cards', 'svicloudtvbox-lumen'); ?>">
    <div class="compare-products__grid">
      <article class="compare-product-card compare-product-card--highlight">
        <figure class="compare-product-card__media">
          <?php
          // Prefer WooCommerce primary image; fallback to theme product asset
          $img_10p = svic_product_primary_image($hero_product_10p, 'large');
          if ($img_10p) {
              echo $img_10p; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
          } else {
              echo '<img src="' . esc_url(svic_theme_image_uri('/assets/images/svicloud-10p-plus.webp')) . '" alt="' . esc_attr__('SVICLOUD 10P+', 'svicloudtvbox-lumen') . '" loading="lazy" decoding="async" />';
          }
          ?>
        </figure>
        <div class="compare-product-card__header">
          <h2 class="compare-product-card__title"><?php echo svic_translate_html('shop.cards.10p.title'); ?></h2>
          <p class="compare-product-card__price"><?php echo $price_10p_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
          <p class="compare-product-card__lead"><?php echo svic_translate_html('compare.products.10p.lead'); ?></p>
          <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($hero_10p_url); ?>"><?php echo svic_translate_html('compare.products.10p.cta'); ?></a>
        </div>
        <ul class="compare-product-card__list">
          <?php foreach ($compare_product_bullets['10p'] as $bullet_key) : ?>
            <li><?php echo svic_translate_html($bullet_key); ?></li>
          <?php endforeach; ?>
        </ul>
        <div class="compare-product-card__divider" aria-hidden="true"></div>
        <section class="compare-product-card__comparison" aria-label="<?php echo esc_attr__('Feature comparison for SVICLOUD 10P+', 'svicloudtvbox-lumen'); ?>">
          <h3 class="compare-product-card__comparison-title"><?php echo svic_translate_html('compare.comparison.title'); ?></h3>
          <dl class="compare-product-card__comparison-list">
            <?php foreach ($comparison_rows as $row) :
                $is_highlight = isset($row['highlight']) && $row['highlight'] === 'p10p';
                $base_key = 'compare.comparison.rows.' . $row['key'];
            ?>
              <div class="compare-product-card__comparison-item <?php echo $is_highlight ? 'is-highlight' : ''; ?>">
                <dt><?php echo svic_translate_html($base_key . '.label'); ?></dt>
                <dd><?php echo svic_translate_html($base_key . '.p10p'); ?></dd>
              </div>
            <?php endforeach; ?>
          </dl>
        </section>
      </article>

      <article class="compare-product-card">
        <figure class="compare-product-card__media">
          <img src="<?php echo esc_url(svic_theme_image_uri('/assets/images/svicloud-tvbox-10s.webp')); ?>" alt="<?php echo esc_attr__('SVICLOUD 10S', 'svicloudtvbox-lumen'); ?>" loading="lazy" decoding="async" />
        </figure>
        <div class="compare-product-card__header">
          <h2 class="compare-product-card__title"><?php echo svic_translate_html('shop.cards.10s.title'); ?></h2>
          <p class="compare-product-card__price"><?php echo $price_10s_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
          <p class="compare-product-card__lead"><?php echo svic_translate_html('compare.products.10s.lead'); ?></p>
          <a class="lumen-pill lumen-pill--ghost" href="<?php echo esc_url($hero_10s_url); ?>"><?php echo svic_translate_html('compare.products.10s.cta'); ?></a>
        </div>
        <ul class="compare-product-card__list">
          <?php foreach ($compare_product_bullets['10s'] as $bullet_key) : ?>
            <li><?php echo svic_translate_html($bullet_key); ?></li>
          <?php endforeach; ?>
        </ul>
        <div class="compare-product-card__divider" aria-hidden="true"></div>
        <section class="compare-product-card__comparison" aria-label="<?php echo esc_attr__('Feature comparison for SVICLOUD 10S', 'svicloudtvbox-lumen'); ?>">
          <h3 class="compare-product-card__comparison-title"><?php echo svic_translate_html('compare.comparison.title'); ?></h3>
          <dl class="compare-product-card__comparison-list">
            <?php foreach ($comparison_rows as $row) :
                $is_highlight = isset($row['highlight']) && $row['highlight'] === 'p10s';
                $base_key = 'compare.comparison.rows.' . $row['key'];
            ?>
              <div class="compare-product-card__comparison-item <?php echo $is_highlight ? 'is-highlight' : ''; ?>">
                <dt><?php echo svic_translate_html($base_key . '.label'); ?></dt>
                <dd><?php echo svic_translate_html($base_key . '.p10s'); ?></dd>
              </div>
            <?php endforeach; ?>
          </dl>
        </section>
      </article>
    </div>
  </section>



  <section class="compare-final-cta" aria-label="<?php echo esc_attr__('Final call to action', 'svicloudtvbox-lumen'); ?>">
    <div class="compare-final-cta__inner">
      <span class="compare-final-cta__badge"><?php echo svic_translate_html('compare.final_cta.badge'); ?></span>
      <h2 class="compare-final-cta__title"><?php echo svic_translate_html('compare.final_cta.title'); ?></h2>
      <p class="compare-final-cta__copy"><?php echo svic_translate_html('compare.final_cta.copy'); ?></p>
      <div class="compare-final-cta__actions lumen-action-group">
        <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($hero_10p_url); ?>"><?php echo svic_translate_html('compare.final_cta.cta_10p'); ?></a>
        <a class="lumen-pill lumen-pill--ghost" href="<?php echo esc_url($hero_10s_url); ?>"><?php echo svic_translate_html('compare.final_cta.cta_10s'); ?></a>
      </div>
    </div>
  </section>
</main>

<?php
$compare_schema_products = [];
$compare_item_list       = [];
$compare_position        = 1;

$compare_page_id  = get_queried_object_id();
$compare_page_url = $compare_page_id ? get_permalink($compare_page_id) : get_permalink();
if (function_exists('svic_url_with_lang')) {
    $compare_page_url = svic_url_with_lang($compare_page_url);
}
$compare_page_url = esc_url_raw($compare_page_url);

$schema_products = [
    $hero_product_10p,
    $hero_product_10s,
];

foreach ($schema_products as $schema_product) {
    if (!$schema_product instanceof WC_Product) {
        continue;
    }

    $product_node = svic_build_product_schema_from_wc_product($schema_product);
    if (empty($product_node)) {
        continue;
    }

    $compare_schema_products[] = $product_node;
    $compare_item_list[]       = [
        '@type'    => 'ListItem',
        'position' => $compare_position++,
        'item'     => [
            '@id'  => $product_node['@id'],
            'name' => $product_node['name'],
        ],
    ];
}

if (!empty($compare_schema_products)) {
    $graph_nodes = [];

    if (!empty($compare_item_list) && $compare_page_url !== '') {
        $graph_nodes[] = [
            '@type'           => 'ItemList',
            '@id'             => untrailingslashit($compare_page_url) . '#compare-itemlist',
            'name'            => esc_html__('SVICLOUD comparison lineup', 'svicloudtvbox-lumen'),
            'url'             => $compare_page_url,
            'numberOfItems'   => count($compare_item_list),
            'itemListOrder'   => 'https://schema.org/ItemListOrderAscending',
            'itemListElement' => $compare_item_list,
        ];
    }

    $graph_nodes = array_merge($graph_nodes, $compare_schema_products);

    echo '<script type="application/ld+json">' . wp_json_encode([
        '@context' => 'https://schema.org',
        '@graph'   => $graph_nodes,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
?>

<?php get_footer(); ?>
