# Current storefront integration inventory and baseline

**Captured:** 2026-08-17 before 15P pricing/backorder implementation
**Branch:** `main`
**HEAD:** `34af29c604cc782569df8e7db994dddca61a1b7d` (`fix(i18n): localize accessibility controls`)
**Scope:** Existing uncommitted storefront work only; this document is a planning artifact created after the snapshot.

## Snapshot summary

- 44 modified tracked files; no tracked deletions or renames.
- 23 untracked deliverable files.
- Repository was already on `main`; no commit, push, reset, checkout, cleanup, or deployment was performed.
- Runtime containers: local WordPress and MariaDB; production remains read-only.
- Generated outputs are present beside their source partials/JavaScript and must be rebuilt before integration.

### Files by feature group

- 15P launch evidence/content: 19
- 15P product media: 4
- 15P verification: 2
- Broad regression coverage: 1
- Compare source CSS: 3
- Compare/15P integration: 1
- Cross-cutting theme integration: 1
- Cross-feature localization: 3
- Footer/15P + Google badge integration: 1
- Generated CSS bundle: 5
- Generated JavaScript: 1
- Guides source: 2
- Header redesign source CSS: 2
- Header/hero audit evidence: 1
- Header/hero verification: 3
- Header/navigation source JavaScript: 1
- Homepage/15P integration: 1
- Homepage/15P source CSS: 1
- PDP/15P integration: 1
- REST public-fixture workflow: 6
- Remote product verification: 1
- Shop source CSS: 1
- Shop/15P + remote integration: 1
- Source traceability: 4
- WooCommerce/PDP source CSS: 1

## Explicit preservation inventory

### Recent-shipment estimate marquee

- Toggle and integration: `theme/svicloudtvbox-lumen/functions.php:24-28,515,522-529` and `theme/svicloudtvbox-lumen/header.php:186`.
- Data/rendering: `theme/svicloudtvbox-lumen/inc/class-svic-recent-shipments.php:148-176,223-274` reads completed WooCommerce orders, requires at least three eligible items, and animates at four or more.
- Styling/accessibility: `theme/svicloudtvbox-lumen/assets/css/parts/06-recent-shipments-strip.css:6-216`; animation pauses on hover/focus and honors reduced motion.
- Local baseline: enabled in code, but not rendered (`count=0`) because the local latest-order query currently has zero completed orders and therefore zero `_wcshipping_selected_rates` candidates. This is a data-dependent absence, not proof the feature is dead.
- Preservation rule: do not remove its class, CSS partial, translation keys, function include, header call, order data, or cache hooks. Verification must use either qualifying non-private test fixtures or a controlled render test without exposing customer data.

### Google Customer Reviews/store-rating surfaces

- Official badge constants and local-environment guard: `theme/svicloudtvbox-lumen/functions.php:67-82`.
- Badge renderer: `theme/svicloudtvbox-lumen/inc/helpers-svic.php:1684-1728`; merchant ID required, default position `BOTTOM_LEFT`.
- Footer call: `theme/svicloudtvbox-lumen/footer.php:124-126`; CSP extension: `theme/svicloudtvbox-lumen/functions.php:4119-4178`.
- Local baseline: official Google badge and platform script are intentionally absent in `development` (`badgeCount=0`, `platformScriptCount=0`). Production is expected to request the badge; Google displays a rating only after enough eligible survey responses.
- Separate homepage store-rating summary: `theme/svicloudtvbox-lumen/front-page.php:424,438-443`; this is theme HTML, not the external Google badge.
- Preservation rule: keep both surfaces distinct, preserve the development guard/CSP behavior, and never fabricate review counts or Google ratings.

### Other behavior that must survive

- Responsive header/submenus and keyboard controls; Guides route and submenu; WooCommerce system-page routing.
- Homepage hero, pricing cards, comparison, localized metadata, FAQ/schema, footer, and current 10P+/10S/remote cards.
- Public REST fixture refresh and private-data preservation boundaries.
- Android 12 corrections for 10P+/10S and source-supported 15P claims/media.

## Runtime/data baseline

```json
{"15p":{"id":4475,"status":"publish","regular_price":"","sale_price":"","price":"","stock_status":"outofstock","manage_stock":false,"backorders":"no","purchasable":false,"image_id":4472,"gallery_ids":[4473,4474],"coming_soon":"yes"},"private_state":{"users":1,"hpos_orders":80,"customers":80,"private_or_unpublished_posts":84}}
```

- Homepage 15P artwork: `svicloud-15p-marketing-v4.webp`, natural size 1536×1024; desktop rendered box approximately 628×399 with no console errors.
- Required commerce delta is not yet implemented: regular `$379.00`, sale `$288.00`, purchasable, backorders allowed, no unverified fulfillment date.

## Test baseline

| Check | Result | Baseline interpretation |
|---|---:|---|
| `npx playwright test tests/playwright/launch-15p.spec.ts` | 10/10 passed | Existing tests enforce the old non-purchasable state and must be deliberately updated after plan approval. |
| `node scripts/audit_15p_storefront.mjs` | 36/36 passed | Existing audit covers current Coming Soon/non-commerce state. |
| `npx playwright test tests/playwright/smoke.spec.ts` | 14 passed, 2 failed | Both failures are `/my-account/` form `x=0` versus expected gutter ≥12, one per browser project. Treat as known baseline unless research ties it to this slice. |
| Homepage DOM probe | no console errors | Shipment strip absent due data; Google badge intentionally disabled locally; v4 hero loaded at 1536×1024. |

Baseline screenshots already tracked or generated under `docs/15p-launch/screenshots/`, `.playwright/15p-audit/`, `.playwright/header-audit/`, and the two smoke failure artifacts under `test-results/`. Runtime-only `.playwright/` and `test-results/` evidence remains untracked.

## Route baseline

- `/`, `/shop/`, `/compare/`, `/product/svicloud-15p/`
- `/product/svicloud-10p-plus/`, `/product/svicloud-10s/`, `/product/svicloud-bluetooth-remote-10p-plus/`
- `/cart/`, `/checkout/`, `/my-account/`, `/guides/`
- Localized equivalents using `?lang=zh` and `?lang=zh-cn`.

## Git integration risks

1. Large mixed diff combines REST fixture tooling, header/Guides work, 15P launch work, generated assets, and documentation; commits must be staged from the inventory rather than with a blanket add.
2. `functions.php`, locale registries, generated bundles, and screenshots are shared hotspots spanning multiple feature groups.
3. Existing launch tests assert non-purchasability, so a green old suite after commerce implementation would indicate the requested behavior was not implemented.
4. Recent shipments and Google badge are data/environment-dependent and cannot be judged solely by current local visibility.
5. Current work is directly on `main` with no recovery commit; the approved plan must establish a non-destructive recovery point before implementation/integration.

## Itemized file inventory

| Status | File | Feature group | Source/data dependency | Generated | Intended disposition |
|---|---|---|---|---|---|
| `M` | `docs/15p-launch/15p-product-intel.md` | 15P launch evidence/content | Repository source | No | Keep and review |
| `M` | `docs/15p-launch/google-ads-brief.md` | 15P launch evidence/content | Repository source | No | Keep and review |
| `M` | `docs/15p-launch/launch-checklist.md` | 15P launch evidence/content | Repository source | No | Keep and review |
| `M` | `docs/15p-launch/launch-plan-coverage.md` | 15P launch evidence/content | Repository source | No | Keep and review |
| `M` | `docs/15p-launch/pdp-theme-audit.md` | 15P launch evidence/content | Repository source | No | Keep and review |
| `M` | `docs/15p-launch/screenshots/after-home-desktop.png` | 15P launch evidence/content | Screenshot/reference source or generated image | Yes | Keep if referenced by approved launch evidence/theme |
| `M` | `docs/15p-launch/screenshots/after-home-mobile.png` | 15P launch evidence/content | Screenshot/reference source or generated image | Yes | Keep if referenced by approved launch evidence/theme |
| `M` | `docs/15p-launch/screenshots/final-pdp-15p-desktop.png` | 15P launch evidence/content | Screenshot/reference source or generated image | Yes | Keep if referenced by approved launch evidence/theme |
| `M` | `docs/15p-launch/screenshots/final-pdp-15p-mobile.png` | 15P launch evidence/content | Screenshot/reference source or generated image | Yes | Keep if referenced by approved launch evidence/theme |
| `M` | `docs/15p-launch/screenshots/final-shop-desktop.png` | 15P launch evidence/content | Screenshot/reference source or generated image | Yes | Keep if referenced by approved launch evidence/theme |
| `M` | `docs/15p-launch/screenshots/final-shop-mobile.png` | 15P launch evidence/content | Screenshot/reference source or generated image | Yes | Keep if referenced by approved launch evidence/theme |
| `M` | `docs/15p-launch/screenshots/redesign-home-desktop.png` | 15P launch evidence/content | Screenshot/reference source or generated image | Yes | Keep if referenced by approved launch evidence/theme |
| `M` | `docs/15p-launch/screenshots/redesign-home-mobile.png` | 15P launch evidence/content | Screenshot/reference source or generated image | Yes | Keep if referenced by approved launch evidence/theme |
| `M` | `docs/15p-launch/support-faq-15p.md` | 15P launch evidence/content | Repository source | No | Keep and review |
| `M` | `docs/15p-launch/verification-report.md` | 15P launch evidence/content | Repository source | No | Keep and review |
| `M` | `docs/15p-launch/warranty-return-wording.md` | 15P launch evidence/content | Repository source | No | Keep and review |
| `M` | `tests/playwright/launch-15p.spec.ts` | 15P verification | Local Playwright storefront | No | Keep and review |
| `M` | `tests/playwright/smoke.spec.ts` | Broad regression coverage | Local public routes, desktop + mobile | No | Keep and review |
| `M` | `theme/svicloudtvbox-lumen/assets/css/compare.css` | Generated CSS bundle | CSS partials + assets/css/bundles.json | Yes | Rebuild, verify, and keep with source partials |
| `M` | `theme/svicloudtvbox-lumen/assets/css/front-page.css` | Generated CSS bundle | CSS partials + assets/css/bundles.json | Yes | Rebuild, verify, and keep with source partials |
| `M` | `theme/svicloudtvbox-lumen/assets/css/guides.css` | Generated CSS bundle | CSS partials + assets/css/bundles.json | Yes | Rebuild, verify, and keep with source partials |
| `M` | `theme/svicloudtvbox-lumen/assets/css/parts/12-header-base.css` | Header redesign source CSS | Repository source | No | Keep and review |
| `M` | `theme/svicloudtvbox-lumen/assets/css/parts/16-header-redesign.css` | Header redesign source CSS | Repository source | No | Keep and review |
| `M` | `theme/svicloudtvbox-lumen/assets/css/parts/32b-15p-launch-redesign.css` | Homepage/15P source CSS | Repository source | No | Keep and review |
| `M` | `theme/svicloudtvbox-lumen/assets/css/parts/61-compare-hero.css` | Compare source CSS | Repository source | No | Keep and review |
| `M` | `theme/svicloudtvbox-lumen/assets/css/parts/63-compare-products.css` | Compare source CSS | Repository source | No | Keep and review |
| `M` | `theme/svicloudtvbox-lumen/assets/css/parts/64b-compare-sticky-buy.css` | Compare source CSS | Repository source | No | Keep and review |
| `M` | `theme/svicloudtvbox-lumen/assets/css/parts/65-shop.css` | Shop source CSS | Repository source | No | Keep and review |
| `M` | `theme/svicloudtvbox-lumen/assets/css/parts/67-guides.css` | Guides source | Repository source | No | Keep and review |
| `M` | `theme/svicloudtvbox-lumen/assets/css/parts/70-lumen-woocommerce.css` | WooCommerce/PDP source CSS | Repository source | No | Keep and review |
| `M` | `theme/svicloudtvbox-lumen/assets/css/style.css` | Generated CSS bundle | CSS partials + assets/css/bundles.json | Yes | Rebuild, verify, and keep with source partials |
| `M` | `theme/svicloudtvbox-lumen/assets/css/woocommerce.css` | Generated CSS bundle | CSS partials + assets/css/bundles.json | Yes | Rebuild, verify, and keep with source partials |
| `M` | `theme/svicloudtvbox-lumen/assets/js/theme.js` | Header/navigation source JavaScript | Repository source | No | Keep and review |
| `M` | `theme/svicloudtvbox-lumen/assets/js/theme.min.js` | Generated JavaScript | assets/js/theme.js via npm build | Yes | Rebuild, verify, and keep with source JS |
| `M` | `theme/svicloudtvbox-lumen/footer.php` | Footer/15P + Google badge integration | Translations + Google Customer Reviews helper | No | Keep and review |
| `M` | `theme/svicloudtvbox-lumen/front-page.php` | Homepage/15P integration | Translations, Woo product, 15P artwork | No | Keep and review |
| `M` | `theme/svicloudtvbox-lumen/functions.php` | Cross-cutting theme integration | WooCommerce, metadata/schema, fixture helpers, Google/recent-shipment integration | No | Keep only after high-risk regression review |
| `M` | `theme/svicloudtvbox-lumen/lang/en_US.php` | Cross-feature localization | EN/zh-TW/zh-CN translation keys | No | Keep and review |
| `M` | `theme/svicloudtvbox-lumen/lang/zh_CN.php` | Cross-feature localization | EN/zh-TW/zh-CN translation keys | No | Keep and review |
| `M` | `theme/svicloudtvbox-lumen/lang/zh_TW.php` | Cross-feature localization | EN/zh-TW/zh-CN translation keys | No | Keep and review |
| `M` | `theme/svicloudtvbox-lumen/page-compare.php` | Compare/15P integration | Translations, Woo products, product media | No | Keep and review |
| `M` | `theme/svicloudtvbox-lumen/page-guides.php` | Guides source | Repository source | No | Keep and review |
| `M` | `theme/svicloudtvbox-lumen/woocommerce/archive-product.php` | Shop/15P + remote integration | Woo products, translations, product media | No | Keep and review |
| `M` | `theme/svicloudtvbox-lumen/woocommerce/single-product.php` | PDP/15P integration | Woo product metadata, translations, gallery media | No | Keep and review |
| `??` | `docs/15p-launch/15p-marketing-v4-prompt.md` | 15P launch evidence/content | Repository source | No | Keep and review |
| `??` | `docs/15p-launch/screenshots/15p-marketing-v4-home-preview.png` | 15P launch evidence/content | Screenshot/reference source or generated image | Yes | Keep if referenced by approved launch evidence/theme |
| `??` | `docs/15p-launch/screenshots/15p-marketing-v4-shop-preview.png` | 15P launch evidence/content | Screenshot/reference source or generated image | Yes | Keep if referenced by approved launch evidence/theme |
| `??` | `docs/15p-parent-media-inventory.md` | Source traceability | Repository source | No | Keep and review |
| `??` | `docs/15p-pptx-media-inventory.md` | Source traceability | Repository source | No | Keep and review |
| `??` | `docs/15p-source-traceability.md` | Source traceability | Repository source | No | Keep and review |
| `??` | `docs/current-model-source-notes.md` | Source traceability | Repository source | No | Keep and review |
| `??` | `docs/frontend-audit/header-hero-design-review.md` | Header/hero audit evidence | Repository source | No | Keep and review |
| `??` | `docs/production-data-refresh.md` | REST public-fixture workflow | Production REST read + local WP-CLI; private data excluded | No | Keep and review |
| `??` | `scripts/audit_15p_storefront.mjs` | 15P verification | Local Playwright storefront | No | Keep and review |
| `??` | `scripts/audit_header_hero_a11y.mjs` | Header/hero verification | Local Playwright storefront | No | Keep and review |
| `??` | `scripts/audit_header_hero_layout.mjs` | Header/hero verification | Local Playwright storefront | No | Keep and review |
| `??` | `scripts/audit_public_theme_fixture_rest.py` | REST public-fixture workflow | Production REST read + local WP-CLI; private data excluded | No | Keep and review |
| `??` | `scripts/capture_header_hero_before_after.mjs` | Header/hero verification | Local Playwright storefront | No | Keep and review |
| `??` | `scripts/check_remote_product_card.mjs` | Remote product verification | Local WooCommerce product fixture | No | Keep and review |
| `??` | `scripts/import_public_theme_fixture.php` | REST public-fixture workflow | Production REST read + local WP-CLI; private data excluded | No | Keep and review |
| `??` | `scripts/sync_public_theme_fixture.py` | REST public-fixture workflow | Production REST read + local WP-CLI; private data excluded | No | Keep and review |
| `??` | `scripts/verify_private_fixture_preservation.py` | REST public-fixture workflow | Production REST read + local WP-CLI; private data excluded | No | Keep and review |
| `??` | `scripts/verify_public_fixture_routes.mjs` | REST public-fixture workflow | Production REST read + local WP-CLI; private data excluded | No | Keep and review |
| `??` | `theme/svicloudtvbox-lumen/assets/images/products/svicloud-15p-angle.webp` | 15P product media | Approved PDF references or grounded image generation | No | Keep referenced optimized WebP assets |
| `??` | `theme/svicloudtvbox-lumen/assets/images/products/svicloud-15p-front.webp` | 15P product media | Approved PDF references or grounded image generation | No | Keep referenced optimized WebP assets |
| `??` | `theme/svicloudtvbox-lumen/assets/images/products/svicloud-15p-marketing-v4.webp` | 15P product media | Approved PDF references or grounded image generation | Yes | Keep referenced optimized WebP assets |
| `??` | `theme/svicloudtvbox-lumen/assets/images/products/svicloud-15p-packaging-mockup-watermarked.webp` | 15P packaging gallery media | Supplied packaging artwork presented as a watermarked mockup | Yes | Keep referenced optimized WebP asset |

## Inventory verdict

- **Complete for planning:** every file present in the pre-goal Git snapshot is itemized above.
- **Known ambiguity resolved:** “scrolling shipping estimate marketing bar” maps to `SVIC_Recent_Shipments`; “Google store review” maps primarily to the official Google Customer Reviews badge, with the separate homepage store-rating summary also inventoried.
- **Not ready for implementation:** RPI spec, evidence-backed research, plan pack, explicit approval, and recovery point are still required.
