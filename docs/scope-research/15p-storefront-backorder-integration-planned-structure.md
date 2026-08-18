# 15P storefront/backorder integration — planned structure

## Scope and Intent

Canonical file/shape handoff for implementing the approved 15P backorder launch, homepage SEO preservation, repeatable fixture state, regression audits, and safe three-commit integration. No production deployment or write is included.

## Current Shape

- Deterministic local 15P product exists, but fixture forces empty price, `outofstock`, backorders off, and non-purchasability.
- Homepage, Shop, Compare, and PDP independently hard-code 15P prelaunch commerce suppression.
- Homepage and PDP schema map only InStock/OutOfStock and merchant enrichment applies current-model delivery timing to every Offer.
- EN/繁中/简中 registries contain contradictory price-unannounced and Explore copy.
- Launch tests/audit correctly describe the old state and must change with the contract.
- Canonical/hreflang/social metadata are healthy; local core and production Rank Math use different sitemap adapters.

## Planned Shape

```text
public REST fixture (read only)
  -> generic public importer [commit 1]
  -> deterministic local 15P supplement [commit 3]
       WC price 379/288 + managed qty 0 + notify backorders + onbackorder
       -> shared price renderer + shared schema availability mapper
       -> homepage / Shop / Compare / PDP / cart / checkout
       -> one Product Offer: 288 USD, BackOrder, no deliveryTime
       -> post-sync invariant + private-data verification

localized SEO/content
  -> approved homepage titles/descriptions
  -> self-canonical + reciprocal hreflang unchanged
  -> active robots/sitemap adapter
  -> Playwright SEO route audit + Lighthouse observations
```

## File List

### Recovery/evidence

- `docs/specs/15p-storefront-backorder-integration.md` — approved requirements/ACs.
- `docs/scope-research/15p-storefront-integration-inventory.md` — pre-goal file/data/test baseline.
- `docs/scope-research/15p-storefront-seo-baseline.json` — machine-readable local/production SEO baseline.
- `docs/scope-research/15p-storefront-backorder-integration-{research,design-discussion,planned-structure,plan,review}.md` — RPI chain.
- External only: safety manifest/patch and private database backup under the user’s Pi backup directory, never Git.

### Fixture and product state

- `scripts/import_public_theme_fixture.php` — set deterministic 15P price/managed-stock/backorders/media; keep generic importer separable.
- `scripts/sync_public_theme_fixture.py` — verify exact BackOrder commerce/media state after refresh.
- `scripts/verify_private_fixture_preservation.py` — keep preservation probe independent of 15P-only media where needed.
- `scripts/verify_public_fixture_routes.mjs` — verify public product/cart route outcomes without private data.
- `docs/production-data-refresh.md` — document the new deterministic supplement invariant.

### Theme state, presentation, and schema

- `theme/svicloudtvbox-lumen/inc/helpers-svic.php` — retain accessible sale renderer; add one WC→schema availability mapper.
- `theme/svicloudtvbox-lumen/front-page.php` — use WC commerce state, approved card alignment/action, BackOrder homepage Offer.
- `theme/svicloudtvbox-lumen/woocommerce/archive-product.php` — replace prelaunch price branch with WC price/backorder presentation.
- `theme/svicloudtvbox-lumen/page-compare.php` — display sale/original price and Backorder action.
- `theme/svicloudtvbox-lumen/woocommerce/single-product.php` — keep 15P content/media but render standard price/add-to-cart commerce.
- `theme/svicloudtvbox-lumen/woocommerce/cart/cart.php` — localized 15P backorder notice if Woo’s default is not query-locale aware.
- `theme/svicloudtvbox-lumen/functions.php` — localized button/notice filters, homepage/15P metadata, BackOrder Product schema, conditional delivery-time enrichment.
- `theme/svicloudtvbox-lumen/lang/en_US.php`, `zh_TW.php`, `zh_CN.php` — price/status/action/SEO copy; remove contradictory surrounding copy.
- `theme/svicloudtvbox-lumen/assets/css/parts/32b-15p-launch-redesign.css` — homepage hero/pricing alignment.
- `theme/svicloudtvbox-lumen/assets/css/parts/65-shop.css` — Shop 15P alignment if shared card rules are insufficient.
- `theme/svicloudtvbox-lumen/assets/css/parts/70-lumen-woocommerce.css` — PDP/backorder notice treatment if needed.
- Generated from partials: `assets/css/front-page.css`, `assets/css/woocommerce.css`, and any bundle selected by `bundles.json`; never hand-edit.

### Media

- `theme/svicloudtvbox-lumen/assets/images/products/svicloud-15p-marketing-v4.webp` — hero/pricing/Shop only.
- `svicloud-15p-front.webp`, `svicloud-15p-angle.webp`, `svicloud-15p-package.webp` — PDP/Compare/fixture gallery.
- `docs/15p-launch/15p-marketing-v4-prompt.md` and approved screenshots — provenance/evidence.

### Verification

- `tests/playwright/launch-15p.spec.ts` — replace obsolete no-commerce assertions with BackOrder/cart/locale/schema ACs.
- `scripts/audit_15p_storefront.mjs` — update 36 route×viewport checks and screenshots for backorder state.
- `scripts/audit_storefront_seo.mjs` — new bounded audit for status/final URL, indexability, metadata, canonical/hreflang, headings, links, images, social tags, JSON-LD, robots/sitemap.
- `tests/playwright/smoke.spec.ts` — preserve broad baseline; modify only if required to test an approved regression.
- Existing header/hero/remote audits remain unchanged unless evidence requires a fixture-safe assertion.

## Responsibility Changes

| Area | Before | After |
|---|---|---|
| Commerce authority | WC data plus hard-coded prelaunch overrides | WC data is authoritative; 15P slug selects content/media only |
| Price rendering | Coming Soon strings on three surfaces | shared accessible WC sale/original markup |
| Availability schema | InStock/OutOfStock only | shared BackOrder/InStock/OutOfStock mapper |
| Delivery schema | fixed timing on every Offer | BackOrder omits delivery time; current products unchanged |
| Customer action | Explore/Preview or no form | localized Backorder 15P + standard WC cart flow |
| SEO metadata | launch-first, price-unannounced | authorized-dealer intent + 15P Backorder `$288/$379` |
| SEO verification | partial metadata assertions | reusable route/infrastructure/schema audit + Lighthouse evidence |
| Recovery | none on mixed `main` worktree | local snapshot commit + external DB/manifest |

## Dependency Notes

- Fixture state must land before runtime commerce verification.
- Template commerce requires the WC product and shared availability helper.
- Schema/meta changes depend on approved product state and localized copy.
- CSS bundle output depends on partials; JS build is required only if `theme.js` remains changed from the inventoried header work.
- SEO audit must understand both core `wp-sitemap.xml` and Rank Math `sitemap_index.xml`.
- Commit 1 must exclude 15P supplement hunks/assets; commit 3 owns those coupled changes.
- Shared `functions.php`, locale registries, footer, and importer/sync files require hunk-level staging and cached-diff review.

## Review Diff Basis

- Primary implementation diff: safety snapshot commit → final reviewed `main` tree.
- Commit review bases:
  1. pre-goal HEAD → fixture-core commit;
  2. fixture-core → header/Guides commit;
  3. header/Guides → 15P backorder commit.
- Runtime evidence bases: `15p-storefront-integration-inventory.md` and `15p-storefront-seo-baseline.json`.
- Review must reject any file absent from this list or the original inventory unless a documented replan adds it.
