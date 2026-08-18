# SVICLOUD 15P storefront/backorder integration research

## Current State Assessment

Source: `docs/specs/15p-storefront-backorder-integration.md`. Baseline: `docs/scope-research/15p-storefront-integration-inventory.md`.

| Target | State | Evidence |
|---|---|---|
| AC1 inventory | **Already true** | Inventory contains all 44 modified tracked and 23 untracked pre-goal files plus the shipment/Google feature mapping. |
| AC2 research/readiness | **Already true when this artifact is accepted** | This artifact traces the affected workflows and ends with an explicit readiness verdict. |
| AC3 plan pack/approval | **Not true** | No task-scoped plan, design discussion, planned structure, or approval exists yet. |
| AC4 recovery point | **Not true** | Worktree remains a mixed uncommitted diff directly on `main`. |
| AC5 price/backorder product state | **Not true** | Runtime: empty prices, out of stock, backorders `no`, non-purchasable; importer reproduces this at `scripts/import_public_theme_fixture.php:469-489`. |
| AC6 PDP/cart/checkout flow | **Not true** | 15P slug forces the prelaunch branch and suppresses price/Add to Cart at `woocommerce/single-product.php:39,260-268`. |
| AC7 consistent surfaces/schema | **Not true** | Homepage, Shop, Compare, PDP, translations, tests, and schema all encode the old non-commerce state. |
| AC8 locale commerce behavior | **Unknown** | All three locales support informational 15P content, but backorder actions/notices have not been exercised. |
| AC9 artwork/responsive actions | **Partly already true** | v4 is loaded in hero/cards; current captures show no clipping. Commerce action does not exist yet. |
| AC10 dedicated verification | **Not true for requested behavior** | Current launch 10/10 and audit 36/36 explicitly reject purchasing and prices. |
| AC11 broad smoke baseline | **Already established** | 14 pass; two `/my-account/` gutter failures, one per browser project. |
| AC12 fixture/private preservation | **Unknown for new state** | Workflow and preservation probe exist; invariant currently requires the obsolete non-purchasable state. |
| AC13 builds/lint/diff | **Unknown until final state** | Commands are known; final sources have not been implemented. |
| AC14 RPI review | **Not true** | Post-implementation phase. |
| AC15 local commits | **Not true** | No reviewed integration commit exists. |
| AC16 SEO baseline | **Already true** | `docs/scope-research/15p-storefront-seo-baseline.json` contains local/read-only-production route, infrastructure, link, image, JSON-LD, and Lighthouse observations. |
| AC17 final locale crawl | **Unknown** | Current local baseline is healthy; final commerce content/schema do not exist. |
| AC18 homepage SEO preservation | **Unknown** | Structure/indexability are healthy, but local launch metadata displaced some production dealer/buying intent and still says price is unannounced. |
| AC19 sitemap/localized discovery | **Partly true** | English 15P is in the local product sitemap and all locale pages have reciprocal hreflang; the custom Chinese sitemap provider emits only `zh`, not `zh-cn`. |
| AC20 BackOrder Product schema | **Not true** | Current local 15P Product node has no Offer. |
| AC21 Lighthouse comparison | **Baseline established** | Local desktop/mobile and production desktop succeeded; production mobile repeatedly returned `NO_FCP`. |
| AC22 final SEO report | **Not true** | Post-implementation phase. |

## Workflow Trace

### Public fixture → local 15P

1. `scripts/sync_public_theme_fixture.py` fetches only classified public REST resources, invokes the PHP importer, verifies representative hashes/counts, and compares private-record counts (`scripts/sync_public_theme_fixture.py:306-353`).
2. The importer creates three managed 15P attachments, then creates a new simple product with empty prices, stock disabled, `outofstock`, and `_svic_coming_soon=yes` (`scripts/import_public_theme_fixture.php:456-490`).
3. Post-import verification hard-codes `publish|visible|0||outofstock|3|15p` (`scripts/sync_public_theme_fixture.py:326-329`).
4. `scripts/verify_private_fixture_preservation.py:28-139` seeds draft/private probes, applies a full refresh, validates content/media/term identity, cleans probes, and checks counts return to baseline.

### Product data → customer commerce surfaces

1. Homepage, Shop, Compare, and PDP resolve the same local product by slug through `svic_get_product_by_slug()` (`inc/helpers-svic.php:1059-1072`).
2. Homepage marks the 15P card `prelaunch=true`; that flag bypasses WC price, buy URL, sale savings, and Offer generation even if the product later has a price (`front-page.php:116-139,213-252,282-303`).
3. Shop marks 15P `prelaunch=true`; it hard-codes the translated Coming Soon amount instead of calling `svic_price_html()` (`woocommerce/archive-product.php:10-32,114-124`).
4. Compare hard-codes the Coming Soon price markup and informational CTA (`page-compare.php:8-13,185-203`).
5. PDP defines prelaunch solely as slug `svicloud-15p`; that branch renders Coming Soon text and omits `woocommerce_template_single_add_to_cart()` regardless of WC product state (`woocommerce/single-product.php:35-43,260-268`).
6. If the standard form is rendered for a purchasable product, WooCommerce owns add-to-cart validation/session behavior. The custom cart already renders a notice when `backorders_require_notification()` and `is_on_backorder(quantity)` are both true (`woocommerce/cart/cart.php:100-105`).

### Product data → price and structured data

1. `svic_price_html()` reads regular/current/sale prices and renders accessible sale/current markup, including localized screen-reader sale order (`inc/helpers-svic.php:1074-1115`).
2. Homepage builds its own Product nodes. `prelaunch=true` prevents 15P price/Offer generation, and availability maps only to InStock/OutOfStock (`front-page.php:282-303,336-353`).
3. PDP custom schema falls back from effective to regular price and emits an Offer when any price exists, but availability maps only `is_in_stock()` to InStock versus OutOfStock (`functions.php:2548-2634`).
4. Every emitted Offer is enriched with fixed handling 0–2 and transit 2–5 day data if shipping details are absent (`functions.php:2701-2774`). Those current-model timings are not verified for a 15P backorder.
5. Schema.org defines `https://schema.org/BackOrder` as an `ItemAvailability` value indicating that an item is available on back order.

### Product state → WooCommerce behavior

- Local stack: WordPress 6.8.3, WooCommerce 10.3.8, USD, development environment.
- WooCommerce requires an existing published product with a non-empty price for `is_purchasable()` (`abstract-wc-product.php:1692-1700` in the installed plugin).
- `is_in_stock()` treats every stock status except `outofstock` as in stock (`abstract-wc-product.php:1750-1758`).
- `is_on_backorder()` is true for explicit `onbackorder`, or for managed stock below requested quantity when backorders are allowed (`abstract-wc-product.php:1831-1836`).
- Backorder notifications require managed stock plus mode `notify` (`abstract-wc-product.php:1821-1822`).
- A runtime-only unsaved probe using prices 379/299, managed quantity 0, `backorders=notify`, and `stock_status=onbackorder` returned: sale=true, purchasable=true, in_stock=true, on_backorder=true, notification=true. No repository or product data was changed.

### Technical/on-page SEO baseline

1. A bounded Playwright crawl captured local and read-only production homepage, Shop, Compare, and requested 15P PDP routes for EN/繁中/简中 in `docs/scope-research/15p-storefront-seo-baseline.json`.
2. Local: all 12 routes return 200 with one H1/main, one description, one self-canonical, reciprocal `en-US`/`zh-Hant-US`/`zh-Hans-US`/`x-default`, complete OG/Twitter metadata, parseable JSON-LD, and no broken homepage internal link.
3. Production: all existing home/Shop/Compare routes return 200, but requested 15P paths redirect to the corresponding homepage; 15P is absent from the production product sitemap. That is a pre-launch baseline, not a post-launch target.
4. Canonical/hreflang generation is owned by `functions.php:533-579,1589-1918`; homepage and 15P metadata are owned by `functions.php:1922-2330`.
5. Local robots advertises core `wp-sitemap.xml`; production advertises Rank Math `sitemap_index.xml`. Selection is environment-aware at `functions.php:5048-5079`.
6. Local English 15P appears in `wp-sitemap-posts-product-1.xml`. The custom mirror provider includes products but emits only the `zh` locale (`inc/class-svic-zh-sitemap.php:30-168`), so `zh-cn` relies on reciprocal hreflang/internal links rather than its own mirror entry.
7. Local launch homepage title is 67 characters and centers 15P Coming Soon; production title is 39 characters and centers the established `小雲盒子` authorized-dealer topic. Local descriptions still claim price is unannounced. This is a content decision for planning, not proof of ranking impact.
8. Local image checks found no failed images and no absent `alt` attributes on required routes. Three Compare hero images correctly use empty alt as decorative; recurring SVG footer icons omit width/height attributes.
9. Heading sequences have no skipped levels across the 24 local/production observations.
10. Lighthouse baseline: local desktop SEO 100/performance 96; local mobile SEO 100/performance 83; production desktop SEO 100/performance 87. Production mobile returned `NO_FCP` on three retries and is an explicit baseline limitation.

### Preservation-only features

- Recent-shipment strip is called after the header (`header.php:186`), requires at least three eligible completed U.S. orders, and animates with four or more (`inc/class-svic-recent-shipments.php:148-176,223-334`). Local fixture currently has zero completed orders, so absence in the DOM is data-dependent.
- Official Google Customer Reviews badge is rendered from the footer through the helper (`footer.php:124-126`; `inc/helpers-svic.php:1684-1728`). A development-only filter disables it locally (`functions.php:67-82`); CSP additions are production integration support (`functions.php:4119-4178`).
- Homepage store-rating HTML is a separate theme surface (`front-page.php:424,438-443`).

## Project Slice Code Map

```text
Production public REST (read only)
  -> sync_public_theme_fixture.py
     -> import_public_theme_fixture.php
        -> WC_Product_Simple svicloud-15p + three local attachments
           -> svic_get_product_by_slug()
              -> front-page.php (hero, pricing, homepage schema)
              -> archive-product.php (Shop card)
              -> page-compare.php (Compare card)
              -> single-product.php (PDP and standard WC add-to-cart seam)
                 -> WooCommerce cart/session/checkout
                    -> custom cart.php backorder notice
           -> svic_build_product_schema_from_wc_product()
              -> svic_enrich_product_schema_for_google_merchant()
     -> hard-coded post-import invariant
     -> private count verification

Independent preservation seams:
  header.php -> SVIC_Recent_Shipments -> private order metadata
  footer.php -> Google Customer Reviews helper -> external Google platform (production only)

SEO request
  -> localized canonical + hreflang generators
  -> homepage/static/product meta definitions and Rank Math filters
  -> Product/FAQ/ItemList/Organization JSON-LD registry
  -> environment-selected robots sitemap URL
     -> core product sitemap + custom zh mirror (local)
     -> Rank Math product sitemap (production)
```

## File Map

| File | Current responsibility / evidence |
|---|---|
| `docs/specs/15p-storefront-backorder-integration.md:1` | R1–R15 and AC1–AC15 source contract. |
| `docs/scope-research/15p-storefront-integration-inventory.md:1` | Pre-goal 67-file inventory and baseline evidence. |
| `scripts/import_public_theme_fixture.php:456-490` | Deterministic 15P media/product creation; old empty-price/out-of-stock state. |
| `scripts/sync_public_theme_fixture.py:306-353` | Apply verification and private-count boundary; old 15P invariant. |
| `scripts/verify_private_fixture_preservation.py:28-139` | Identity-level private fixture probe around full refresh. |
| `theme/svicloudtvbox-lumen/inc/helpers-svic.php:1059-1115` | Slug lookup and reusable accessible sale-price renderer. |
| `theme/svicloudtvbox-lumen/front-page.php:7-10,98-303,336-353` | v4 hero, prelaunch pricing state, buy URL, and homepage Product nodes. |
| `theme/svicloudtvbox-lumen/woocommerce/archive-product.php:8-32,110-151` | Shop prelaunch branch and marketing image. |
| `theme/svicloudtvbox-lumen/page-compare.php:8-13,185-203` | Compare hard-coded 15P status/price/action. |
| `theme/svicloudtvbox-lumen/woocommerce/single-product.php:35-43,260-268` | Slug-driven prelaunch display that suppresses standard commerce. |
| `theme/svicloudtvbox-lumen/woocommerce/cart/cart.php:100-105` | Existing custom cart backorder notice seam. |
| `theme/svicloudtvbox-lumen/functions.php:2548-2774` | PDP Product/Offer schema, availability mapping, merchant enrichment. |
| `theme/svicloudtvbox-lumen/lang/en_US.php:1218-1249,1360-1507` | English Shop/PDP/Compare old availability language. |
| `theme/svicloudtvbox-lumen/lang/zh_TW.php:1335-1366,1477-1600` | Traditional Chinese old availability language. |
| `theme/svicloudtvbox-lumen/lang/zh_CN.php:82-111,1164-1280` | Simplified Chinese old availability language. |
| `tests/playwright/launch-15p.spec.ts:28-153` | Current two-project launch contract; explicitly forbids prices/controls/Offer. |
| `scripts/audit_15p_storefront.mjs:8-149` | 36 locale×viewport×route audit; explicitly rejects purchasable controls. |
| `theme/svicloudtvbox-lumen/inc/class-svic-recent-shipments.php:148-334` | Data-driven shipment marquee preservation target. |
| `theme/svicloudtvbox-lumen/inc/helpers-svic.php:1684-1728`; `theme/svicloudtvbox-lumen/footer.php:124-126` | Google Customer Reviews badge preservation target. |
| `theme/svicloudtvbox-lumen/assets/css/parts/32b-15p-launch-redesign.css:195-202,390-412` | Hero uses cover; pricing card uses contain for v4 image. |
| `theme/svicloudtvbox-lumen/functions.php:533-579,1589-2330` | Localized canonical/hreflang, redirect preservation, homepage/15P metadata and social-image filters. |
| `theme/svicloudtvbox-lumen/functions.php:5048-5232` | Environment-aware robots sitemap, sitemap redirects/exclusions, SEO text trimming. |
| `theme/svicloudtvbox-lumen/inc/class-svic-zh-sitemap.php:30-168` | Core-sitemap mirror currently emits one `zh` URL per public page/post/product. |
| `docs/scope-research/15p-storefront-seo-baseline.json:1` | Machine-readable 24-route local/production SEO and Lighthouse baseline. |

## Structure Outline

- **Fixture boundary:** Production-derived public content and deterministic local-only 15P supplement are imported together; private records remain outside the managed set.
- **Domain state:** 15P is a normal `WC_Product_Simple`, but templates currently layer a slug/flag-driven “prelaunch” presentation over it.
- **Presentation:** Homepage, Shop, Compare, and PDP each contain separate prelaunch branches rather than consuming one shared commerce-state presenter.
- **Commerce:** Standard WooCommerce product/cart/checkout behavior is available behind the PDP branch; custom cart presentation already recognizes backorders.
- **Metadata:** Homepage and PDP build Product schema through different paths; merchant enrichment is shared downstream for Rank Math/Woo nodes.
- **Localization:** Theme registries provide three locale variants, while WooCommerce supplies stock/backorder strings according to its active locale.
- **Verification:** Dedicated launch tests and the audit cover the right routes/viewports but encode the old state; broad smoke is independent.
- **SEO:** Localized metadata/canonical/schema logic is concentrated in `functions.php`, while page templates add page-level ItemList/FAQ/Product structures and WordPress/Rank Math own environment-specific sitemap indices.

## Verified Facts

1. The user-approved prices are not present anywhere in current product data.
2. Changing only WooCommerce product metadata would not expose commerce: four separate presentation/schema branches still classify 15P as non-commerce.
3. Existing `svic_price_html()` already renders the requested sale/original hierarchy accessibly.
4. Existing custom cart can show a backorder notice when Woo product configuration requests notification.
5. `BackOrder` exists as a specific schema availability value; current custom mappings cannot emit it.
6. Existing merchant enrichment would attach current-model delivery timing to a 15P Offer unless the backorder case is distinguished.
7. The importer and sync verifier are the repeatability source of truth; a dashboard-only edit would be lost on refresh.
8. v4 is 1536×1024 (3:2). Baseline desktop hero renders near 628×399; cover-cropping is small and remains inside the image's intentional 12% safe bands. Shop/pricing media use `object-fit: contain`.
9. Current launch verification is green only because it asserts the obsolete non-purchasable contract.
10. Recent shipments and the Google badge are real integrations even though neither renders in the current development homepage baseline.
11. Current local canonical, hreflang, indexability, heading, internal-link, social metadata, and JSON-LD parsing baselines are healthy across all 12 required routes.
12. Local homepage metadata must change because it still says price is unannounced; preserving the production dealer/buying topic while adding 15P BackOrder is a material content decision.
13. Production has no 15P indexable PDP today: requested 15P locale URLs resolve to homepage. Local self-canonical 15P pages are therefore a new SEO surface, not a replacement URL migration.
14. Local and production use different active sitemap adapters by design; robots.txt correctly advertises each active endpoint.
15. The current Chinese sitemap provider proves Traditional Chinese discovery but does not emit Simplified Chinese entries.
16. Lighthouse SEO 100 does not validate content strategy or schema business accuracy; those remain explicit audit checks.

## Design Question Evidence

### DQ1 — Customer-facing action language

- Existing 15P actions say Explore/Preview; current models use Buy/View.
- Standard WooCommerce supplies Add to cart and availability strings; the theme has its own EN/繁中/简中 registry for surrounding card/PDP copy.
- A custom cart notice appears only when Woo reports notification-required backorders.
- Decision must keep action, availability notice, and locale source coherent without implying a ship date.

### DQ2 — WooCommerce stock representation

- Explicit `onbackorder` makes `is_on_backorder()` true.
- Managed stock + quantity 0 + `backorders=notify` makes notification true and preserves purchasability when price/status are valid.
- Non-managed stock can display available-on-backorder for explicit status, but `backorders_require_notification()` remains false, so the current custom cart notice condition would not render.

### DQ3 — Coming Soon versus backorder

- “COMING SOON” is baked into approved v4 artwork and cannot be localized without generating separate assets.
- User explicitly wants the artwork retained and purchasing enabled now.
- Current surrounding copy also claims price is unknown; that part is directly contradictory and must change.
- Coming Soon can describe release status, while backorder describes purchasing state; the plan must prevent either from obscuring the other.

### DQ4 — Shipment marquee verification

- Local absence results from zero completed orders, not missing code.
- Rendering depends on private order address/shipping metadata and a minimum-three threshold.
- Any test evidence must avoid committing customer labels or mutating preserved orders; available seams include the query-limit/render filters and an isolated local test fixture.

### DQ5 — Google badge verification

- Development intentionally disables the external badge to avoid third-party CSP/report-only noise.
- Production code path consists of enabled constant, merchant ID, footer call, platform loader, renderer config, and CSP sources.
- Deterministic local verification can prove generated configuration/static integration separately from external Google rendering; live third-party visual output is not locally deterministic.

### DQ6 — Commit boundaries

- Inventory categories reveal four coupled groups: fixture workflow, header/Guides, 15P launch/commerce, and generated/docs evidence.
- Shared hotspots (`functions.php`, three locale files, generated bundles, screenshots) span groups.
- Generated CSS/JS cannot be separated from source partials/scripts; fixture importer/verifier cannot be separated from the state they enforce.
- User approved three local commits: fixture refresh; header/Guides; 15P backorder launch. Nothing is pushed.

### DQ7 — Homepage search intent

- Production homepage metadata emphasizes `小雲盒子`, U.S. authorized dealer, buying, current models, warranty, and bilingual support.
- Local launch metadata emphasizes 15P Coming Soon and removes some established dealer/buying phrasing; English title reaches 67 characters.
- Price-unannounced wording is now false under the approved commerce state.
- Planning must choose a concise title/description that preserves established dealer intent while adding 15P backorder relevance without keyword stuffing.

### DQ8 — Sitemap and Lighthouse environment differences

- Core WordPress and Rank Math expose different valid sitemap endpoints; `svic_preferred_sitemap_url()` already selects by active plugin.
- The custom core provider emits `zh` only; `zh-cn` is still reachable through reciprocal hreflang and internal links but lacks a mirrored sitemap entry.
- Production mobile Lighthouse repeatedly returned `NO_FCP`; local mobile and both desktop baselines succeeded. Final evidence must distinguish site regression from runner/edge behavior.

## Open Unknowns

- English action is approved as `Backorder 15P`; exact natural Traditional/Simplified Chinese equivalents remain a planning copy decision, not a factual blocker.
- Whether the two baseline `/my-account/` failures are present at HEAD before the mixed diff has not been proven. The goal permits them to remain only if final evidence shows no regression relative to the captured baseline.
- Production Google may or may not display a rating because Google controls eligibility; local code cannot establish survey sufficiency.
- No 15P fulfillment date exists, so all delivery-time claims for its Offer must remain absent or explicitly noncommittal.
- Homepage metadata wording that balances established authorized-dealer intent with 15P backorder relevance requires user approval during plan clarification.
- Production mobile Lighthouse paint failure is reproducible; production mobile scoring may remain unavailable unless the runner/edge path changes.

## Remaining Blocker

- **Planning blocker:** none. Facts needed to design the change are established.
- **Implementation blocker:** the RPI planning pack and explicit user approval are still mandatory.

## Plan Readiness

**Ready.** Current facts are separated from unknowns; requested behaviors and ACs are classified; commerce, fixture, schema, localization, image, SEO, verification, and preservation workflows are traced; material design questions have bounded evidence. Proceed to `rpi-plan`; obtain explicit homepage metadata approval and final pack approval before implementation.

## Exact Validation Commands for Planning

```bash
python3 scripts/build_css.py --theme svicloudtvbox-lumen
npm run build:js
./scripts/sync_theme_container.sh
npx playwright test tests/playwright/launch-15p.spec.ts
node scripts/audit_15p_storefront.mjs
npx playwright test tests/playwright/smoke.spec.ts
python3 scripts/verify_private_fixture_preservation.py
node scripts/verify_public_fixture_routes.mjs
CHROME_PATH=/usr/bin/google-chrome npx --yes lighthouse http://svicloud10p.svic.local/ --only-categories=seo,performance --preset=desktop --output=json --output-path=/tmp/lighthouse-home-desktop.json --quiet --chrome-flags='--headless --no-sandbox'
CHROME_PATH=/usr/bin/google-chrome npx --yes lighthouse http://svicloud10p.svic.local/ --only-categories=seo,performance --output=json --output-path=/tmp/lighthouse-home-mobile.json --quiet --chrome-flags='--headless --no-sandbox'
find theme/svicloudtvbox-lumen scripts -name '*.php' -print0 | xargs -0 -n1 php -l
git diff --check
```
