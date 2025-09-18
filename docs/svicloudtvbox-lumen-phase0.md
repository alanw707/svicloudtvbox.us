# Phase 0 Discovery Notes – svicloudtvbox-lumen

Date: 2025-09-16
Prepared by: Codex

## Required Templates for v1 Launch
- `header.php`, `footer.php`, `index.php`, `404.php` — baseline WordPress routing and layout shell.
- `front-page.php` — homepage hero + marketing sections (primary redesign target).
- `page.php` — default static page layout.
- `page-compare.php` — dedicated compare table experience.
- WooCommerce overrides:
  - `woocommerce/archive-product.php`
  - `woocommerce/single-product.php`
- Asset pipeline expectations: `assets/css/style.css` built from `assets/css/parts/*.css`; `assets/js/theme.js` handles interactive behaviours.

## Content, Copy, and CTA Assumptions
- Homepage hero, CTAs, and pricing copy remain identical to the legacy theme during initial migration.
- Static metrics (e.g., "99.3% uptime") stay as-is; no real-time data integrations planned for v1.
- Stakeholder confirmation still required on any text/image swaps before implementation begins.

## Shared PHP Helpers (candidates for reuse)
Located in `functions.php` of the legacy theme:
- `svic_get_product_by_slug()` – retrieves WooCommerce product by slug.
- `svic_price_html()` – renders WooCommerce price markup.
- `svic_product_primary_image()` – outputs primary product image with fallback asset.
- `svic_add_to_cart_url()` – builds add-to-cart link targeting cart URL with product ID.
- `svic_bilingual_span()` – outputs bilingual span pair (en/zh) with helper classes.
- `svic_render_product_card()` – renders full product card (carousel, tags, CTA) for PDP/landing use.

Recommendation: move these helpers into a shared include (e.g., `includes/helpers-woocommerce.php`) so both themes consume identical logic and stay in sync.

## Outstanding Questions / Next Actions
- Confirm whether `page-compare.php` should ship unchanged for v1 or receive immediate styling uplift.
- Decide how to structure shared includes between themes (symlink, composer-style include, or copy with unit tests).
- Collect final hero imagery (WebP + PNG fallback) sized for 2× displays before Phase 1 kicks off.
