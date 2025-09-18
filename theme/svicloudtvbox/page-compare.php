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
?>

<main class="page-shell">
  <header class="page-hero">
    <span class="badge badge-muted"><?php echo wp_kses_post(svic_bilingual_span('Compare Models', '機型比較')); ?></span>
    <h1 class="page-title">
      <?php echo wp_kses_post(svic_bilingual_span('SVICLOUD 10P+ vs 10S', 'SVICLOUD 10P+ 與 10S 比較', 'page-title-text')); ?>
    </h1>
    <p class="page-subtitle">
      <?php echo wp_kses_post(svic_bilingual_span(
        'See the hardware, features, and best-use scenarios side-by-side to pick the perfect SVICLOUD for your home.',
        '逐項比較硬體規格、功能與使用情境，幫你挑到最適合家庭的 SVICLOUD 盒子。',
        'page-subtitle-text'
      )); ?>
    </p>
  </header>

  <section class="comparison-panel">
    <div class="comparison-table" role="region" aria-label="<?php echo esc_attr__('SVICLOUD model comparison table', 'svicloudtvbox'); ?>">
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
              $p10p_class = $highlight === 'p10p' ? ' class="highlight"' : '';
              $p10s_class = $highlight === 'p10s' ? ' class="highlight"' : '';
          ?>
            <tr>
              <th scope="row"><?php echo wp_kses_post(svic_bilingual_span($row['feature']['en'], $row['feature']['zh'])); ?></th>
              <td<?php echo $p10p_class; ?>><?php echo wp_kses_post(svic_bilingual_span($row['p10p']['en'], $row['p10p']['zh'])); ?></td>
              <td<?php echo $p10s_class; ?>><?php echo wp_kses_post(svic_bilingual_span($row['p10s']['en'], $row['p10s']['zh'])); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="comparison-cards" aria-label="<?php echo esc_attr__('SVICLOUD model comparison cards', 'svicloudtvbox'); ?>">
      <?php foreach ($comparison_rows as $row) :
          $highlight = isset($row['highlight']) ? $row['highlight'] : '';
      ?>
        <article class="comparison-card">
          <h3 class="comparison-card-feature"><?php echo wp_kses_post(svic_bilingual_span($row['feature']['en'], $row['feature']['zh'])); ?></h3>
          <dl class="comparison-card-list">
            <div class="comparison-card-item<?php echo $highlight === 'p10p' ? ' highlight' : ''; ?>">
              <dt><?php esc_html_e('SVICLOUD 10P+', 'svicloudtvbox'); ?></dt>
              <dd><?php echo wp_kses_post(svic_bilingual_span($row['p10p']['en'], $row['p10p']['zh'])); ?></dd>
            </div>
            <div class="comparison-card-item<?php echo $highlight === 'p10s' ? ' highlight' : ''; ?>">
              <dt><?php esc_html_e('SVICLOUD 10S', 'svicloudtvbox'); ?></dt>
              <dd><?php echo wp_kses_post(svic_bilingual_span($row['p10s']['en'], $row['p10s']['zh'])); ?></dd>
            </div>
          </dl>
        </article>
      <?php endforeach; ?>
    </div>

    <div class="comparison-cta">
      <a class="btn btn-primary" href="<?php echo esc_url($hero_10p_url); ?>">
        <?php echo wp_kses_post(svic_bilingual_span('Buy 10P+ – ' . $price_10p_text, '選購 10P+ – ' . $price_10p_text)); ?>
      </a>
      <a class="btn btn-accent" href="<?php echo esc_url($hero_10s_url); ?>">
        <?php echo wp_kses_post(svic_bilingual_span('Buy 10S – ' . $price_10s_text, '選購 10S – ' . $price_10s_text)); ?>
      </a>
    </div>
  </section>

  <section class="specs-grid">
    <article class="spec-card">
      <h3><?php echo wp_kses_post(svic_bilingual_span('Performance', '效能規格')); ?></h3>
      <ul>
        <li>
          <strong><?php echo wp_kses_post(svic_bilingual_span('Processor', '處理器')); ?>:</strong>
          <?php echo wp_kses_post(svic_bilingual_span('ARM Cortex-A73 quad-core', 'ARM Cortex-A73 四核心')); ?>
        </li>
        <li>
          <strong><?php echo wp_kses_post(svic_bilingual_span('GPU', 'GPU')); ?>:</strong>
          <?php echo wp_kses_post(svic_bilingual_span('ARM Mali-G31 MP2', 'ARM Mali-G31 MP2')); ?>
        </li>
        <li>
          <strong><?php echo wp_kses_post(svic_bilingual_span('OS', '作業系統')); ?>:</strong>
          <?php echo wp_kses_post(svic_bilingual_span('Android 11.0', 'Android 11.0')); ?>
        </li>
        <li>
          <strong><?php echo wp_kses_post(svic_bilingual_span('Boot time', '開機時間')); ?>:</strong>
          <?php echo wp_kses_post(svic_bilingual_span('~25 seconds', '約 25 秒')); ?>
        </li>
      </ul>
    </article>
    <article class="spec-card">
      <h3><?php echo wp_kses_post(svic_bilingual_span('Connectivity & Ports', '連線與介面')); ?></h3>
      <ul>
        <li>
          <strong><?php echo wp_kses_post(svic_bilingual_span('HDMI', 'HDMI')); ?>:</strong>
          <?php echo wp_kses_post(svic_bilingual_span('2.1 (4K@60Hz)', '2.1（4K@60Hz）')); ?>
        </li>
        <li>
          <strong><?php echo wp_kses_post(svic_bilingual_span('USB', 'USB')); ?>:</strong>
          <?php echo wp_kses_post(svic_bilingual_span('2 × USB 3.0', '2 組 USB 3.0')); ?>
        </li>
        <li>
          <strong><?php echo wp_kses_post(svic_bilingual_span('Wi‑Fi', 'Wi‑Fi')); ?>:</strong>
          <?php echo wp_kses_post(svic_bilingual_span('802.11ac dual-band', '802.11ac 雙頻')); ?>
        </li>
        <li>
          <strong><?php echo wp_kses_post(svic_bilingual_span('Ethernet', '有線網路')); ?>:</strong>
          <?php echo wp_kses_post(svic_bilingual_span('10/100 Mbps', '10/100 Mbps')); ?>
        </li>
      </ul>
    </article>
  </section>
</main>

<?php get_footer(); ?>