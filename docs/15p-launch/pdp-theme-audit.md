# SVICLOUD 15P PDP implementation note

The local theme renders `svicloud-15p` through `woocommerce/single-product.php`, retaining the source-grounded gallery/content while using standard WooCommerce commerce behavior.

## Current behavior

- Local supplemental WooCommerce product is published and catalog-visible.
- Regular price is `$379.00`; sale/effective price is `$288.00`.
- Managed stock is zero with notified backorders and `onbackorder` status.
- PDP renders localized `Backorder 15P`, WooCommerce quantity/Add to Cart behavior, `Available on backorder`, and `Shipping date not announced`.
- Product schema emits one `$288.00` Offer with `https://schema.org/BackOrder` and no delivery-time estimate.
- Primary image plus four gallery images are recreated from tracked watermarked media after every fixture refresh; all delivered 15P theme artwork is watermarked.
- EN/繁/简 title, detailed specification, package list, FAQ, comparison qualification, price, and availability text come from locale registries/product state.
- Normal checkout/payment/shipping-rate/cancellation/return behavior applies; no 15P-specific shipping-speed, dispatch-date, delivery-date, or warranty promise is added.

## Evidence

- Facts/assets/commerce decision: `docs/15p-source-traceability.md`
- Import: `scripts/import_public_theme_fixture.php`
- Regression: `tests/playwright/launch-15p.spec.ts`
- Responsive/accessibility audit: `scripts/audit_15p_storefront.mjs`
