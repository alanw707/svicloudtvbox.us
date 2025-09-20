<?php
/**
 * WooCommerce Archive (Shop)
 */

defined('ABSPATH') || exit;

get_header('shop');

$card_data = [
    '10p' => [
        'product'      => class_exists('WooCommerce') ? svic_get_product_by_slug('svicloud-10p-plus') : null,
        'title'        => ['en' => 'SVICLOUD 10P+', 'zh' => 'SVICLOUD 10P+'],
        'lead'         => [
            'en' => 'Flagship hardware with exclusive family features and the fastest performance we ship.',
            'zh' => '旗艦硬體配上專屬家庭功能與最快速的運算效能。',
        ],
        'features'     => [
            ['en' => '4GB RAM / 64GB storage with AV1 decode', 'zh' => '4GB 記憶體 / 64GB 儲存與 AV1 解碼'],
            ['en' => 'Kids Mode & Karaoke apps included', 'zh' => '內建兒童模式與卡拉 OK 應用'],
            ['en' => 'Bluetooth AI voice remote + Wi-Fi 6', 'zh' => '藍牙 AI 語音遙控 + Wi-Fi 6'],
        ],
        'button'       => ['en' => 'View 10P+', 'zh' => '查看 10P+'],
        'highlight'    => true,
        'fallback_url' => home_url('/product/svicloud-10p-plus'),
        'fallback_price' => '$248.99',
    ],
    '10s' => [
        'product'      => class_exists('WooCommerce') ? svic_get_product_by_slug('svicloud-10s') : null,
        'title'        => ['en' => 'SVICLOUD 10S', 'zh' => 'SVICLOUD 10S'],
        'lead'         => [
            'en' => 'Essential 4K streaming with streamlined hardware for value-conscious homes.',
            'zh' => '提供 4K 串流的精省配置，適合重視性價比的家庭。',
        ],
        'features'     => [
            ['en' => '2GB RAM / 32GB storage for everyday streaming', 'zh' => '2GB 記憶體 / 32GB 儲存，滿足日常播放'],
            ['en' => '4K HDR + AV1 decode with voice remote', 'zh' => '4K HDR + AV1 解碼，搭配語音遙控'],
            ['en' => 'HDMI, USB 3.0, and wired Ethernet included', 'zh' => '含 HDMI、USB 3.0 與有線網路端口'],
        ],
        'button'       => ['en' => 'View 10S', 'zh' => '查看 10S'],
        'highlight'    => false,
        'fallback_url' => home_url('/product/svicloud-10s'),
        'fallback_price' => '$183.99',
    ],
];

foreach ($card_data as $key => $card) {
    if (!isset($card['product']) || !$card['product']) {
        $card_data[$key]['price_text'] = $card['fallback_price'];
        $card_data[$key]['url'] = $card['fallback_url'];
        continue;
    }

    $product = $card['product'];
    $price_html = method_exists($product, 'get_price_html') ? $product->get_price_html() : '';
    $card_data[$key]['price_text'] = $price_html ? wp_strip_all_tags($price_html) : $card['fallback_price'];
    $card_data[$key]['url'] = get_permalink($product->get_id());
}
?>

<main class="page-shell shop-page">
  <header class="page-hero shop-hero">
    <span class="compare-hero__badge"><?php esc_html_e('Shop', 'svicloudtvbox-lumen'); ?></span>
    <h1 class="compare-hero__title"><?php esc_html_e('SVICLOUD TV Boxes', 'svicloudtvbox-lumen'); ?></h1>
    <p class="compare-hero__subtitle"><?php echo wp_kses_post(svic_bilingual_span('Authorized U.S. dealer with fast domestic shipping, 1-year warranty, and English/中文 support.', '美國授權經銷，提供快速出貨、一年保固與英文/中文客服。')); ?></p>
  </header>

  <section class="shop-products">
    <div class="compare-products__grid">
      <?php foreach ($card_data as $key => $card) :
          $price_text = isset($card['price_text']) ? $card['price_text'] : $card['fallback_price'];
          $url = isset($card['url']) ? $card['url'] : $card['fallback_url'];
          $highlight_class = !empty($card['highlight']) ? ' compare-product-card--highlight' : '';
      ?>
        <article class="compare-product-card<?php echo $highlight_class; ?>">
          <div class="compare-product-card__header">
            <h2 class="compare-product-card__title"><?php echo wp_kses_post(svic_bilingual_span($card['title']['en'], $card['title']['zh'])); ?></h2>
            <p class="compare-product-card__price"><?php echo esc_html($price_text); ?></p>
            <p class="compare-product-card__lead"><?php echo wp_kses_post(svic_bilingual_span($card['lead']['en'], $card['lead']['zh'])); ?></p>
            <a class="lumen-pill <?php echo !empty($card['highlight']) ? 'lumen-pill--primary' : 'lumen-pill--ghost'; ?>" href="<?php echo esc_url($url); ?>">
              <?php echo wp_kses_post(svic_bilingual_span($card['button']['en'], $card['button']['zh'])); ?>
            </a>
          </div>
          <ul class="compare-product-card__list">
            <?php foreach ($card['features'] as $feature) : ?>
              <li><?php echo wp_kses_post(svic_bilingual_span($feature['en'], $feature['zh'])); ?></li>
            <?php endforeach; ?>
          </ul>
        </article>
      <?php endforeach; ?>
    </div>
  </section>
</main>

<?php get_footer('shop'); ?>
