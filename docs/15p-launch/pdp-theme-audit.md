# PDP + Theme Structure Audit (for 15P launch + redesign)

Date: 2026-08-15

## How product pages are built today

- **WooCommerce products** with slugs `svicloud-10p-plus` and `svicloud-10s` (URLs `/product/svicloud-10p-plus/`, `/product/svicloud-10s/`). **No 9P product exists on this site** — lineup is 10P+ (flagship) and 10S (value).
- Rendering: custom classic template `theme/svicloudtvbox-lumen/woocommerce/single-product.php` (346 lines). Sections: hero (gallery + badges + best-for + price + add-to-cart + reassurance), description, in-the-box, traffic (why-buy) block, FAQ accordion, reviews.
- **All copy is translation-key driven**: per-product keys `products.{slug}.short_description|description|best_for|traffic` in `lang/en_US.php` / `zh_TW.php` / `zh_CN.php`, resolved via `svic_translate()` with slug fallback to generic `product.*` keys.
- Schema: `functions.php` — `svic_build_product_schema_from_wc_product()` (line ~2437, Product + Offer, Google Merchant enrichment ~2667), `svic_product_faq_schema_items()` (~3090, FAQPage), output hooks `svic_output_single_product_schema` (wp_head, prio 8).
- Titles/meta: `document_title_parts` filter (functions.php:1133) + hand-rolled `<meta name="description">` echoes (1192, 3563, 4057, 4187).
- Nav: slug→label map in functions.php (~4726: `'svicloud-10p-plus' => 'header.nav.ten_p'`).
- Internal-link surfaces referencing 10P/10S: `inc/decision-pages.php`, `page-compare.php`, `front-page.php`, `inc/agent-resources.php`, `inc/helpers-svic.php`.
- CSS: partials `50-woocommerce.css`, `70-lumen-woocommerce.css` (+ cart/checkout partials) in `woocommerce` bundle; homepage in `front-page` bundle (30-hero, 32/33/34 hero-dashboard, 44–51 lumen sections); header/nav in style bundle (12–20).

## What the 15P page must touch

1. **WC product** `svicloud-15p` — created in WP admin (Docker locally; prod manually later). Theme renders it automatically once lang keys exist.
2. **Lang keys** `products.svicloud-15p.*` in all 3 lang files (short_description, description, best_for, traffic).
3. **New PDP comparison section** — current template has NO per-product comparison block. Add a translation-key-driven section (15P vs 10P+, 15P vs 9P, who-should-upgrade, USA shipping/support/warranty) rendered only when `products.{slug}.comparison` keys exist. New CSS partial in woocommerce bundle.
4. **Nav map** + internal links from 10P+/10S pages, compare page, decision pages, homepage → 15P.
5. **Schema** — Product schema is automatic from WC product; FAQ schema items may need 15P entries.

## What the redesign must touch

- Homepage: `front-page.php` (1069 lines) + front-page bundle partials (30–51).
- Hero: `30-hero.css`, `32–34 hero-dashboard`.
- Nav/header: `header.php` (181 lines) + partials 12–20 + `header.nav.*` lang keys.
- PDP modernization: `woocommerce/single-product.php` + `50-woocommerce.css` / `70-lumen-woocommerce.css` — applies to all products automatically (shared template).

## Notes / risks

- **9P**: not sold here. 15P-vs-9P comparison stays as content (buyers upgrading from 9P bought elsewhere) — do not create a 9P product.
- **Docker unavailable in this WSL distro** (`docker` not found; Docker Desktop WSL integration off). Local verification (sync + Playwright vs svicloud10p.svic.local) blocked until Docker Desktop integration is enabled or tests are pointed at another env. Flag for task-9.
- Never edit generated CSS (`style.css`, `woocommerce.css`, ...); edit partials → `build_css.py` → sync.
