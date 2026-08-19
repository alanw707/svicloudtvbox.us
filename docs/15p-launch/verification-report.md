# SVICLOUD 15P backorder verification report

Verified against `http://svicloud10p.svic.local` after a complete production-derived public-fixture refresh plus the deterministic local 15P supplement. Corrected production deployment evidence is recorded in `production-gallery-price-update-evidence.md`.

## Product invariant

- Status/visibility: `publish` / `visible`
- Regular price: `$379.00`
- Sale/effective price: `$288.00`
- Managed stock: enabled, quantity `0`
- Backorders: `notify`; stock status `onbackorder`
- Purchasable / on backorder / notification required: `true / true / true`
- Product images: exactly five delivered gallery images; primary/front, angle/rear ports, packaging, and two watermarked lifestyle/AI images. Homepage/Shop/metadata artwork is also watermarked.
- `/product/svicloud-15p/`: HTTP 200 in EN/繁中/简中
- Product schema: exactly one Product and one `288.00 USD` Offer with `https://schema.org/BackOrder`
- Offer delivery timing: absent
- Customer action: localized `Pre-order 15P`
- Customer status: localized `Available for pre-order` and `Shipping date not announced`

## Source, content, and policy boundaries

- PDF/PPTX hashes match `docs/15p-source-traceability.md`; source files remain external/untracked.
- Parent-media inventory has 89 rows (85 images + 4 videos); embedded-PPTX inventory has 27 excluded assets.
- Hardware/app claims remain mapped to source evidence or direct supplier confirmation.
- Site-owner commerce decision is recorded separately from supplier facts.
- “Coming Soon” remains only inside approved v4 artwork.
- Homepage, Shop, Compare, PDP, cart, checkout, metadata, and JSON-LD consistently display `$288/$379` and the pre-order state.
- Standard WooCommerce checkout/cart/payment/shipping-rate/cancellation/return paths remain; the local environment has no default active payment gateway, so successful order completion is proven separately with a temporary offline test gateway only.
- Cart/checkout switch to 15P-specific pre-order badges, summary copy, notice, and footer rather than current-model fulfillment/warranty marketing.
- No model-specific shipping-speed, dispatch-date, delivery-date, or warranty promise is introduced.

## Automated results

| Check | Result |
|---|---:|
| `tests/playwright/launch-15p.spec.ts` | 10 passed, 0 failed |
| `scripts/audit_15p_storefront.mjs` | 36/36 passed |
| `scripts/audit_storefront_seo.mjs` | 24 page observations + 77 internal links, 0 issues |
| `tests/playwright/frontend-quality.spec.ts` | 18 passed, 0 failed |
| `tests/playwright/checkout-layout.spec.ts` | 2 passed, 4 environment skips, 0 failed |
| `tests/playwright/guides-locale.spec.ts` | 4 passed across Chromium/WebKit, 0 failed |
| `tests/playwright/locale-commerce.spec.ts` | 4 passed across Chromium/WebKit, 0 failed |
| `scripts/test_public_fixture_security.py` | 3 passed, 0 failed |
| Isolated local offline checkout | order-received reached; dummy orders deleted; gateway and stock restored |
| `tests/playwright/smoke.spec.ts` | 14 passed, 2 known `/my-account/` baseline failures |
| Full `npm test` | 112 passed, 8 skipped, 8 proven pre-existing failures |
| Header/hero accessibility audit | 25 named/focusable controls, 41 contrast checks, heading/decorative checks: pass |
| Header/hero layout audit | 1920/1512/1280/1024/390: no overlap or horizontal overflow |
| Product permalink verification | 4/4 HTTP 200 |
| Remote Shop card audit | one visible card/link, image loaded, no errors |
| Private fixture preservation probe | five seeded records plus content/media/term identity preserved |
| Full refresh private counts | 1 user, 80 HPOS orders, 80 customers, 84 private/unpublished records preserved |
| PHP lint / Python compile / Node syntax / CSS+JS builds | passed |
| `git diff --check` | passed |
| Adversarial review security/fixture regressions | 3 security tests passed; controlled incomplete fixture exited nonzero; valid full refresh passed |
| Localized Guides header-route regressions | 4 passed across Chromium/WebKit |
| Localized 15P commerce regressions | 4 passed across Chromium/WebKit |
| Isolated offline checkout | order-received reached with dummy buyer; all temporary orders deleted; gateway restored |

### Full-suite baseline classification

The eight remaining full-suite failures are unrelated and predate this implementation:

1. **Two `/my-account/` gutter failures:** exactly match the captured pre-implementation smoke baseline (`x=0`, expected ≥12).
2. **Two blog-image tests:** report 19 missing historical upload files. All 19 are absent from the verified pre-implementation `uploads.tar.gz`, proving this launch did not remove them.
3. **Four dynamic Blog-menu tests:** expect `.menu-item-svic-dynamic`, but that implementation does not exist in baseline HEAD `34af29c` or its theme source. Current header/menu layout and accessibility audits pass.

No new failure remains after updating stale hard-coded product-ID and non-purchasable-15P test assumptions. The localized Guides and commerce regressions now exercise the actual header links and PDP form actions.

## SEO audit

Detailed findings: `docs/scope-research/15p-storefront-seo-audit.md`. Machine evidence: `15p-storefront-seo-baseline.json` and `15p-storefront-seo-final.json`.

- Every required route is HTTP 200, indexable, self-canonical, and has four reciprocal hreflang values.
- Titles/descriptions, OG/Twitter metadata, headings, internal links, image metadata, and JSON-LD pass.
- Active local `wp-sitemap.xml` includes the English 15P PDP.
- Zero broken audited links or unintended non-transactional redirects.
- Lighthouse SEO: desktop 100, mobile 100.
- Local performance score: desktop 96, mobile 90; key observations and runner variance are documented.

## Preserved data/environment integrations

- Complete recovery points: original safety commit `9846579656fd99de71bb3dfdc4b336eb2b5e01f7` plus adversarial pre-fix branch `safety/adversarial-review-pre-fix-20260818` at `6f619f8492b98cbb3d881a1137210d3e4b32e120`; the latter has a gzip-tested database backup under the external Pi backup directory.
- Complete fixture refresh now fails nonzero on partial media/post/product/term/menu imports and verifies the full source-ID manifest; valid refresh and private-preservation probe pass.
- Recent-shipment strip constant and renderer remain enabled/present; local absence remains data-dependent because the fixture has no completed eligible shipments.
- Google Customer Reviews constant, merchant ID, renderer, footer hook, and CSP support remain present. Development filter intentionally disables the external badge; production behavior remains enabled.

## Screenshots

Tracked final desktop/mobile captures:

- Homepage: `after-home-*` and `redesign-home-*`
- Shop: `final-shop-*`
- Compare: `final-compare-*`
- PDP: `final-pdp-15p-*`
- Cart: `final-cart-15p-*`
- Checkout: `final-checkout-15p-*`

Runtime locale/viewports remain under `.playwright/15p-audit/` and are intentionally untracked.
