<?php
/** Hardware-only public summaries; commerce prices remain WooCommerce-owned. */
if (!defined('ABSPATH')) { exit; }
function svic_public_hardware_agent_resources(): array {
    $base = '# SVICLOUD TV Box US' . "\n\n" . 'SVICLOUD device specifications, hardware connections, U.S. shipping, warranty and order support.' . "\n\n";
    $products = "## Product specifications\n- SVICLOUD 15P: Android 14, Amlogic S905Y5, 4 GB RAM, 64 GB storage, Wi-Fi 6, Bluetooth 5.4.\n- Current price: {15p_price}; regular price: {15p_regular}. Verify availability and checkout details on the product page.\n- Product: " . home_url('/product/svicloud-15p/') . "\n- Model comparison: " . home_url('/compare/') . "\n- Shop: " . home_url('/shop/') . "\n\n";
    $hardware = "## Device connections\nConnect HDMI and the supplied power adapter, select the TV input, connect Wi-Fi or Ethernet, and check remote pairing and audio.\n- Hardware guide: " . home_url('/guides-setup/') . "\n- Hardware troubleshooting: " . home_url('/guides-troubleshooting/') . "\n\n";
    $support = "## Support and policies\n- Contact: " . home_url('/contact/') . "\n- Phone: +1 (520) 641-7021\n- Shipping: " . home_url('/shipping-policy/') . "\n- Returns: " . home_url('/return-policy/') . "\n- Warranty: " . home_url('/support/') . "\nUse the published policy for current terms. Do not share order or customer details publicly.\n";
    return [
        'llms.txt' => $base . $products . $hardware . $support,
        'llms-full.txt' => $base . $products . $hardware . $support,
        'agent/products.md' => $base . $products . $support,
        'agent/svicloud-15p.md' => $base . $products . $support,
        'agent/compare-10p-vs-10s.md' => $base . 'Compare current hardware specifications at ' . home_url('/compare/') . ".\n\n" . $support,
        'agent/setup.md' => $base . $hardware . $support,
        'agent/troubleshooting.md' => $base . $hardware . $support,
        'agent/shipping-returns.md' => $base . $support,
        'agent/contact.md' => $base . $support,
    ];
}
