<?php
/**
 * Compare Page
 * Template Name: Compare Models
 */
get_header();

$hero_product_10p = class_exists('WooCommerce') ? svic_get_product_by_slug('svicloud-10p-plus') : null;
$hero_product_10s = class_exists('WooCommerce') ? svic_get_product_by_slug('svicloud-10s') : null;
$hero_10p_url = $hero_product_10p ? get_permalink($hero_product_10p->get_id()) : home_url('/product/svicloud-10p-plus');
$hero_10s_url = $hero_product_10s ? get_permalink($hero_product_10s->get_id()) : home_url('/product/svicloud-10s');
$price_10p_text = $hero_product_10p ? wp_strip_all_tags($hero_product_10p->get_price_html()) : '$248.99';
$price_10s_text = $hero_product_10s ? wp_strip_all_tags($hero_product_10s->get_price_html()) : '$183.99';

$comparison_rows = [
    [
        'feature'   => ['en' => 'RAM / Storage', 'zh' => '記憶體 / 儲存'],
        'p10p'      => ['en' => '4GB / 64GB', 'zh' => '4GB / 64GB'],
        'p10s'      => ['en' => '2GB / 32GB', 'zh' => '2GB / 32GB'],
        'highlight' => 'p10p',
    ],
    [
        'feature'   => ['en' => 'Video Quality', 'zh' => '影像品質'],
        'p10p'      => ['en' => '4K HDR, AV1 decode', 'zh' => '4K HDR、AV1 解碼'],
        'p10s'      => ['en' => '4K HDR, AV1 decode', 'zh' => '4K HDR、AV1 解碼'],
        'highlight' => '',
    ],
    [
        'feature'   => ['en' => 'Voice Remote', 'zh' => '語音遙控器'],
        'p10p'      => ['en' => 'Included', 'zh' => '支援'],
        'p10s'      => ['en' => 'Included', 'zh' => '支援'],
        'highlight' => '',
    ],
    [
        'feature'   => ['en' => 'Kids App', 'zh' => '兒童應用'],
        'p10p'      => ['en' => 'Exclusive', 'zh' => '10P+ 獨享'],
        'p10s'      => ['en' => 'Not available', 'zh' => '無'],
        'highlight' => 'p10p',
    ],
    [
        'feature'   => ['en' => 'Karaoke Mode', 'zh' => '卡拉 OK 模式'],
        'p10p'      => ['en' => 'Exclusive', 'zh' => '10P+ 獨享'],
        'p10s'      => ['en' => 'Not available', 'zh' => '無'],
        'highlight' => 'p10p',
    ],
    [
        'feature'   => ['en' => 'Best For', 'zh' => '最適合'],
        'p10p'      => ['en' => 'Families, sports, 4K home theaters', 'zh' => '家庭、運動迷、4K 家庭劇院'],
        'p10s'      => ['en' => 'Value / secondary rooms', 'zh' => '精省用戶 / 次要房間'],
        'highlight' => '',
    ],
];

$key_differences = [
    [
        'title' => ['en' => 'Premium Performance', 'zh' => '頂級效能'],
        'model' => '10P+',
        'description' => ['en' => 'Double the RAM and storage for smoother multitasking and more apps.', 'zh' => '雙倍記憶體與儲存空間，多工處理更順暢，可安裝更多應用程式。'],
    ],
    [
        'title' => ['en' => 'Family Entertainment', 'zh' => '家庭娛樂'],
        'model' => '10P+',
        'description' => ['en' => 'Exclusive Kids Mode and Karaoke features for the whole family.', 'zh' => '獨家兒童模式與卡拉 OK 功能，全家同樂。'],
    ],
    [
        'title' => ['en' => 'Smart Value', 'zh' => '聰明首選'],
        'model' => '10S',
        'description' => ['en' => 'Same 4K picture quality at a more approachable price.', 'zh' => '維持 4K 畫質，同時擁有更親民的價格。'],
    ],
];

$specs_categories = [
    'performance' => [
        'title' => ['en' => 'Performance', 'zh' => '效能規格'],
        'items' => [
            ['label' => ['en' => 'Processor', 'zh' => '處理器'], 'value' => ['en' => 'ARM Cortex-A73 quad-core', 'zh' => 'ARM Cortex-A73 四核心']],
            ['label' => ['en' => 'GPU', 'zh' => 'GPU'], 'value' => ['en' => 'ARM Mali-G31 MP2', 'zh' => 'ARM Mali-G31 MP2']],
            ['label' => ['en' => 'OS', 'zh' => '作業系統'], 'value' => ['en' => 'Android 11.0', 'zh' => 'Android 11.0']],
            ['label' => ['en' => 'Boot time', 'zh' => '開機時間'], 'value' => ['en' => '~25 seconds', 'zh' => '約 25 秒']],
        ],
    ],
    'connectivity' => [
        'title' => ['en' => 'Connectivity & Ports', 'zh' => '連線與介面'],
        'items' => [
            ['label' => ['en' => 'HDMI', 'zh' => 'HDMI'], 'value' => ['en' => '2.1 (4K@60Hz)', 'zh' => '2.1（4K@60Hz）']],
            ['label' => ['en' => 'USB', 'zh' => 'USB'], 'value' => ['en' => '2 × USB 3.0', 'zh' => '2 組 USB 3.0']],
            ['label' => ['en' => 'Wi‑Fi', 'zh' => 'Wi‑Fi'], 'value' => ['en' => '802.11ac dual-band', 'zh' => '802.11ac 雙頻']],
            ['label' => ['en' => 'Ethernet', 'zh' => '有線網路'], 'value' => ['en' => '10/100 Mbps', 'zh' => '10/100 Mbps']],
        ],
    ],
    'software' => [
        'title' => ['en' => 'Software & Apps', 'zh' => '軟體與應用'],
        'items' => [
            ['label' => ['en' => 'Streaming Apps', 'zh' => '串流應用'], 'value' => ['en' => 'Netflix, YouTube, Prime Video, more', 'zh' => 'Netflix、YouTube、Prime Video 等']],
            ['label' => ['en' => 'Voice Assistant', 'zh' => '語音助理'], 'value' => ['en' => 'Google Assistant', 'zh' => 'Google 助理']],
        ],
    ],
];
?>

<main class="compare-page">
  <section class="compare-hero">
    <span class="compare-hero__badge"><?php echo wp_kses_post(svic_bilingual_span('Compare Models', '機型比較')); ?></span>
    <h1 class="compare-hero__title">
      <?php echo wp_kses_post(svic_bilingual_span('SVICLOUD 10P+ vs 10S', 'SVICLOUD 10P+ 與 10S 比較', 'page-title-text')); ?>
    </h1>
    <p class="compare-hero__subtitle">
      <?php echo wp_kses_post(svic_bilingual_span(
        'See the hardware, features, and best-use scenarios side-by-side to pick the perfect SVICLOUD for your home.',
        '逐項比較硬體規格、功能與使用情境，幫你挑到最適合家庭的 SVICLOUD 盒子。',
        'page-subtitle-text'
      )); ?>
    </p>
  </section>

  <section class="compare-differences" aria-label="<?php echo esc_attr__('Key differences between models', 'svicloudtvbox-lumen'); ?>">
    <div class="compare-differences__grid">
      <?php foreach ($key_differences as $difference) : ?>
        <article class="compare-difference-card compare-difference-card--<?php echo esc_attr(strtolower($difference['model'])); ?>">
          <span class="compare-difference-card__model"><?php echo esc_html($difference['model']); ?></span>
          <h3 class="compare-difference-card__title"><?php echo wp_kses_post(svic_bilingual_span($difference['title']['en'], $difference['title']['zh'])); ?></h3>
          <p class="compare-difference-card__copy"><?php echo wp_kses_post(svic_bilingual_span($difference['description']['en'], $difference['description']['zh'])); ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="compare-products" aria-label="<?php echo esc_attr__('Product spotlight cards', 'svicloudtvbox-lumen'); ?>">
    <div class="compare-products__grid">
      <article class="compare-product-card compare-product-card--highlight">
        <div class="compare-product-card__header">
          <h2 class="compare-product-card__title"><?php esc_html_e('SVICLOUD 10P+', 'svicloudtvbox-lumen'); ?></h2>
          <p class="compare-product-card__price"><?php echo esc_html($price_10p_text); ?></p>
          <p class="compare-product-card__lead"><?php echo wp_kses_post(svic_bilingual_span('Premium hardware, exclusive family features, and the fastest performance available.', '旗艦硬體、專屬家庭功能與最快速的運算效能。')); ?></p>
          <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($hero_10p_url); ?>"><?php echo wp_kses_post(svic_bilingual_span('Buy 10P+', '選購 10P+')); ?></a>
        </div>
        <ul class="compare-product-card__list">
          <li><?php echo wp_kses_post(svic_bilingual_span('4GB RAM / 64GB storage with AV1 decode', '4GB 記憶體、64GB 儲存與 AV1 解碼')); ?></li>
          <li><?php echo wp_kses_post(svic_bilingual_span('Kids Mode and Karaoke apps included', '內建兒童模式與卡拉 OK 應用')); ?></li>
          <li><?php echo wp_kses_post(svic_bilingual_span('Bluetooth AI voice remote + Wi-Fi 6', '藍牙 AI 語音遙控與 Wi-Fi 6')); ?></li>
        </ul>
      </article>

      <article class="compare-product-card">
        <div class="compare-product-card__header">
          <h2 class="compare-product-card__title"><?php esc_html_e('SVICLOUD 10S', 'svicloudtvbox-lumen'); ?></h2>
          <p class="compare-product-card__price"><?php echo esc_html($price_10s_text); ?></p>
          <p class="compare-product-card__lead"><?php echo wp_kses_post(svic_bilingual_span('Essential 4K streaming with streamlined hardware for value-conscious homes.', '提供 4K 串流的精省配置，適合重視性價比的家庭。')); ?></p>
          <a class="lumen-pill lumen-pill--ghost" href="<?php echo esc_url($hero_10s_url); ?>"><?php echo wp_kses_post(svic_bilingual_span('Buy 10S', '選購 10S')); ?></a>
        </div>
        <ul class="compare-product-card__list">
          <li><?php echo wp_kses_post(svic_bilingual_span('2GB RAM / 32GB storage for everyday streaming', '2GB 記憶體、32GB 儲存，滿足日常播放')); ?></li>
          <li><?php echo wp_kses_post(svic_bilingual_span('4K HDR + AV1 decode with voice remote', '4K HDR + AV1 解碼，搭配語音遙控')); ?></li>
          <li><?php echo wp_kses_post(svic_bilingual_span('HDMI, USB 3.0, and wired Ethernet included', '內建 HDMI、USB 3.0 與有線網路接口')); ?></li>
        </ul>
      </article>
    </div>
  </section>

  <div class="compare-sticky" aria-hidden="true">
    <div class="compare-sticky__inner">
      <div class="compare-sticky__prices">
        <span><?php echo wp_kses_post(svic_bilingual_span('SVICLOUD 10P+', 'SVICLOUD 10P+')); ?> · <strong><?php echo esc_html($price_10p_text); ?></strong></span>
        <span><?php echo wp_kses_post(svic_bilingual_span('SVICLOUD 10S', 'SVICLOUD 10S')); ?> · <strong><?php echo esc_html($price_10s_text); ?></strong></span>
      </div>
      <div class="compare-sticky__actions">
        <a class="compare-sticky__cta lumen-pill lumen-pill--primary" href="<?php echo esc_url($hero_10p_url); ?>"><?php echo wp_kses_post(svic_bilingual_span('Buy 10P+', '選購 10P+')); ?></a>
        <a class="compare-sticky__cta lumen-pill lumen-pill--ghost" href="<?php echo esc_url($hero_10s_url); ?>"><?php echo wp_kses_post(svic_bilingual_span('Buy 10S', '選購 10S')); ?></a>
      </div>
    </div>
  </div>

  <section class="compare-panel" aria-label="<?php echo esc_attr__('Feature comparison table', 'svicloudtvbox-lumen'); ?>">
    <header class="compare-panel__header">
      <h2 class="compare-panel__$title"><?php echo wp_kses_post(svic_bilingual_span('Feature Comparison', '功能比較')); ?></h2>
      <p class="compare-panel__copy"><?php echo wp_kses_post(svic_bilingual_span('Highlighting the upgrades you get with SVICLOUD 10P+ alongside shared essentials.', '一眼看出 SVICLOUD 10P+ 與 10S 共享的核心與 10P+ 帶來的升級。')); ?></p>
    </header>
    <div class="comparison-table comparison-table--desktop" role="table">
      <table>
        <thead>
          <tr>
            <th scope="col"><?php echo wp_kses_post(svic_bilingual_span('Feature', '功能指標')); ?></th>
            <th scope="col"><?php echo wp_kses_post(svic_bilingual_span('SVICLOUD 10P+', 'SVICLOUD 10P+')); ?></th>
            <th scope="col"><?php echo wp_kses_post(svic_bilingual_span('SVICLOUD 10S', 'SVICLOUD 10S')); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($comparison_rows as $row) :
              $highlight = isset($row['highlight']) ? $row['highlight'] : '';
          ?>
            <tr>
              <th scope="row"><?php echo wp_kses_post(svic_bilingual_span($row['feature']['en'], $row['feature']['zh'])); ?></th>
              <td class="<?php echo $highlight === 'p10p' ? 'is-highlight' : ''; ?>"><?php echo wp_kses_post(svic_bilingual_span($row['p10p']['en'], $row['p10p']['zh'])); ?></td>
              <td class="<?php echo $highlight === 'p10s' ? 'is-highlight' : ''; ?>"><?php echo wp_kses_post(svic_bilingual_span($row['p10s']['en'], $row['p10s']['zh'])); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="comparison-cards comparison-cards--mobile" role="list">
      <?php foreach ($comparison_rows as $row) :
          $highlight = isset($row['highlight']) ? $row['highlight'] : '';
      ?>
        <article class="comparison-card" role="listitem">
          <h3 class="comparison-card__feature"><?php echo wp_kses_post(svic_bilingual_span($row['feature']['en'], $row['feature']['zh'])); ?></h3>
          <dl class="comparison-card__list">
            <div class="comparison-card__item <?php echo $highlight === 'p10p' ? 'is-highlight' : ''; ?>">
              <dt><?php esc_html_e('SVICLOUD 10P+', 'svicloudtvbox-lumen'); ?></dt>
              <dd><?php echo wp_kses_post(svic_bilingual_span($row['p10p']['en'], $row['p10p']['zh'])); ?></dd>
            </div>
            <div class="comparison-card__item <?php echo $highlight === 'p10s' ? 'is-highlight' : ''; ?>">
              <dt><?php esc_html_e('SVICLOUD 10S', 'svicloudtvbox-lumen'); ?></dt>
              <dd><?php echo wp_kses_post(svic_bilingual_span($row['p10s']['en'], $row['p10s']['zh'])); ?></dd>
            </div>
          </dl>
        </article>
      <?php endforeach; ?>
    </div>
    <div class="compare-panel__cta">
      <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($hero_10p_url); ?>"><?php echo wp_kses_post(svic_bilingual_span('Buy 10P+ – ' . $price_10p_text, '選購 10P+ – ' . $price_10p_text)); ?></a>
      <a class="lumen-pill lumen-pill--ghost" href="<?php echo esc_url($hero_10s_url); ?>"><?php echo wp_kses_post(svic_bilingual_span('Buy 10S – ' . $price_10s_text, '選購 10S – ' . $price_10s_text)); ?></a>
    </div>
  </section>

  <section class="compare-specs" aria-label="<?php echo esc_attr__('Technical specifications', 'svicloudtvbox-lumen'); ?>">
    <header class="compare-specs__header">
      <h2 class="compare-specs__title"><?php echo wp_kses_post(svic_bilingual_span('Technical Specifications', '技術規格')); ?></h2>
      <div class="compare-specs__tabs" role="tablist">
        <?php $index = 0; ?>
        <?php foreach ($specs_categories as $key => $category) :
            $is_active = $index === 0 ? ' is-active' : '';
        ?>
          <button class="compare-specs__tab<?php echo $is_active; ?>" type="button" role="tab" data-target="specs-<?php echo esc_attr($key); ?>">
            <?php echo wp_kses_post(svic_bilingual_span($category['title']['en'], $category['title']['zh'])); ?>
          </button>
          <?php $index++; ?>
        <?php endforeach; ?>
      </div>
    </header>
    <div class="compare-specs__body">
      <div class="compare-specs__panes">
        <?php $index = 0; ?>
        <?php foreach ($specs_categories as $key => $category) :
            $is_active = $index === 0 ? ' is-active' : '';
        ?>
          <div class="compare-specs__pane<?php echo $is_active; ?>" id="specs-<?php echo esc_attr($key); ?>" role="tabpanel">
            <ul class="compare-specs__list">
              <?php foreach ($category['items'] as $item) : ?>
                <li class="compare-specs__item">
                  <span class="compare-specs__item-label"><?php echo wp_kses_post(svic_bilingual_span($item['label']['en'], $item['label']['zh'])); ?></span>
                  <span class="compare-specs__item-value"><?php echo wp_kses_post(svic_bilingual_span($item['value']['en'], $item['value']['zh'])); ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php $index++; ?>
        <?php endforeach; ?>
      </div>
      <aside class="compare-specs__media" aria-hidden="true">
        <img class="compare-specs__image" src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/svicloud-hero-product.png'); ?>" alt="" loading="lazy" />
      </aside>
    </div>
  </section>

  <section class="compare-final-cta" aria-label="<?php echo esc_attr__('Final call to action', 'svicloudtvbox-lumen'); ?>">
    <div class="compare-final-cta__inner">
      <span class="compare-final-cta__badge"><?php echo wp_kses_post(svic_bilingual_span('Make Your Choice', '選擇您的機型')); ?></span>
      <h2 class="compare-final-cta__title"><?php echo wp_kses_post(svic_bilingual_span('Ready to enhance your streaming experience?', '準備好提升您的串流體驗了嗎？')); ?></h2>
      <p class="compare-final-cta__copy"><?php echo wp_kses_post(svic_bilingual_span('Choose the SVICLOUD model that best fits your needs and budget.', '選擇最符合您需求與預算的 SVICLOUD 機型。')); ?></p>
      <div class="compare-final-cta__actions">
        <a class="lumen-pill lumen-pill--primary" href="<?php echo esc_url($hero_10p_url); ?>"><?php echo wp_kses_post(svic_bilingual_span('Buy SVICLOUD 10P+', '選購 SVICLOUD 10P+')); ?></a>
        <a class="lumen-pill lumen-pill--ghost" href="<?php echo esc_url($hero_10s_url); ?>"><?php echo wp_kses_post(svic_bilingual_span('Buy SVICLOUD 10S', '選購 SVICLOUD 10S')); ?></a>
      </div>
    </div>
  </section>
</main>

<script>
(function () {
  const root = document.querySelector('.compare-page');
  if (!root) {
    return;
  }

  const tabButtons = root.querySelectorAll('.compare-specs__tab');
  const panes = root.querySelectorAll('.compare-specs__pane');

  tabButtons.forEach((button) => {
    button.addEventListener('click', () => {
      const targetId = button.getAttribute('data-target');
      if (!targetId) {
        return;
      }

      tabButtons.forEach((btn) => btn.classList.remove('is-active'));
      panes.forEach((pane) => pane.classList.remove('is-active'));

      button.classList.add('is-active');
      const targetPane = root.querySelector('#' + targetId);
      if (targetPane) {
        targetPane.classList.add('is-active');
      }
    });
  });
})();
</script>

<?php get_footer(); ?>
