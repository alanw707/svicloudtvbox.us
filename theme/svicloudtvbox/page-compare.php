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
?>

<main class="page-shell">
  <header class="page-hero">
    <span class="badge badge-muted">Compare Models</span>
    <h1 class="page-title">SVICLOUD 10P+ vs 10S</h1>
    <p class="page-subtitle">See the hardware, features, and best-use scenarios side-by-side to pick the perfect SVICLOUD for your home.</p>
  </header>

  <section class="comparison-panel">
    <div class="comparison-table">
      <table>
        <thead>
          <tr>
            <th>Feature</th>
            <th>SVICLOUD 10P+</th>
            <th>SVICLOUD 10S</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>RAM / Storage</td>
            <td class="highlight">4GB / 64GB</td>
            <td>2GB / 32GB</td>
          </tr>
          <tr>
            <td>Video Quality</td>
            <td>4K HDR, AV1 decode</td>
            <td>4K HDR, AV1 decode</td>
          </tr>
          <tr>
            <td>Voice Remote</td>
            <td>✔</td>
            <td>✔</td>
          </tr>
          <tr>
            <td>Kids App</td>
            <td class="highlight">✔</td>
            <td>—</td>
          </tr>
          <tr>
            <td>Karaoke Mode</td>
            <td class="highlight">✔</td>
            <td>—</td>
          </tr>
          <tr>
            <td>Best For</td>
            <td>Families, sports, 4K home theaters</td>
            <td>Value/secondary rooms</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="comparison-cta">
      <a class="btn btn-primary" href="<?php echo esc_url( $hero_10p_url ); ?>">Buy 10P+ - $248.99</a>
      <a class="btn btn-accent" href="<?php echo esc_url( $hero_10s_url ); ?>">Buy 10S - $183.99</a>
    </div>
  </section>

  <section class="specs-grid">
    <article class="spec-card">
      <h3>Performance</h3>
      <ul>
        <li><strong>Processor:</strong> ARM Cortex-A73 quad-core</li>
        <li><strong>GPU:</strong> ARM Mali-G31 MP2</li>
        <li><strong>OS:</strong> Android 11.0</li>
        <li><strong>Boot time:</strong> ~25 seconds</li>
      </ul>
    </article>
    <article class="spec-card">
      <h3>Connectivity & Ports</h3>
      <ul>
        <li><strong>HDMI:</strong> 2.1 (4K@60Hz)</li>
        <li><strong>USB:</strong> 2 × USB 3.0</li>
        <li><strong>Wi‑Fi:</strong> 802.11ac dual‑band</li>
        <li><strong>Ethernet:</strong> 10/100 Mbps</li>
      </ul>
    </article>
  </section>
</main>

<?php get_footer(); ?>
