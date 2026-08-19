# SVICLOUD 15P storefront SEO audit

**Audit date:** 2026-08-17
**Scope:** Homepage first, then Shop, Compare, and 15P PDP in EN/繁中/简中 at desktop and mobile widths.
**Machine evidence:** `docs/scope-research/15p-storefront-seo-baseline.json` and `docs/scope-research/15p-storefront-seo-final.json`.

## Executive verdict

**Pass — no unresolved blocking SEO regression.** The final bounded audit checked 24 page observations and 77 unique internal links with zero issues. Local desktop/mobile Lighthouse SEO remains 100. Homepage metadata preserves authorized-dealer/`小雲盒子` intent while adding 15P Backorder relevance and approved pricing.

## Homepage-first review

| Check | Final evidence |
|---|---|
| HTTP/indexability | EN/繁中/简中 return 200; no redirect; no `noindex`. |
| Title | EN `SVICLOUD 15P Backorder \| 小雲盒子 U.S. Authorized Dealer` (52 chars); approved localized titles present. |
| Description | EN 160 chars; localized descriptions contain 15P, `$288/$379`, authorized-dealer context, model comparison, and no announced shipping date. |
| Canonical/hreflang | One self-canonical plus reciprocal `en-US`, `zh-Hant-US`, `zh-Hans-US`, and `x-default` on every locale. |
| Headings | One H1 and one main landmark; no skipped heading level. |
| Internal discovery | Homepage links expose 15P PDP, Shop, Compare, current models, Guides, and support; no broken link among the audited site-wide set. |
| Images/social | v4 hero has loaded source, non-empty alt, intrinsic dimensions; one complete OG and Twitter set uses the approved 15P marketing image. |
| Structured data | Parseable Organization/WebSite/WebPage/SiteNavigation/FAQ/ItemList/Product graph; one 15P Product/Offer with BackOrder state. |
| Mobile rendering | 390 px audit reports no horizontal overflow, missing primary image metadata, failed image, or runtime error. |
| Content relevance | Visible copy retains SVICLOUD, 15P, Android 14, authorized U.S. dealer context, prices, backorder status, and current-model comparison without unsupported performance/policy claims. |

## Route matrix

All four required routes passed in all three locales and both viewports:

- `/`, `/zh/`, `/zh-cn/`
- `/shop/` and localized variants
- `/compare/` and localized variants
- `/product/svicloud-15p/` and localized variants

For every observation the audit found:

- HTTP 200 and requested final URL;
- one description and one self-canonical;
- four correct reciprocal alternate links;
- one H1/main and valid heading progression;
- one complete Open Graph/Twitter metadata set with canonical `og:url`;
- no absent image `alt`, failed image, or missing width/height attribute;
- no horizontal overflow or application console/page error;
- parseable JSON-LD with unique Product IDs;
- one 15P Product Offer at `288.00 USD`, availability `https://schema.org/BackOrder`, and no `deliveryTime`.

## Robots, sitemap, links, and redirects

- `robots.txt`: HTTP 200, does not block `/`, and advertises the active core sitemap.
- Active local sitemap: `http://svicloud10p.svic.local/wp-sitemap.xml`.
- English `/product/svicloud-15p/` is present in the product sitemap.
- Localized virtual PDP routes are discoverable through reciprocal hreflang and visible internal links; none canonicalizes or redirects to English/homepage.
- Final link crawl: 77 unique internal URLs, zero HTTP 4xx/5xx, zero unintended non-transactional redirect.
- Baseline production remains pre-launch: 15P requests redirect to the homepage and production product sitemap omits 15P. This is context, not final local behavior to preserve.

## Image metadata

Initial baseline recorded recurring footer SVGs without HTML dimensions. The footer now supplies the SVG-native `24×24` dimensions. Final route audit reports zero images missing width/height, zero absent alt attributes, and zero failed images on all required routes. Decorative Compare hero images retain valid empty alt text while product-card/PDP images have descriptive alt text.

## Social metadata

Each required local route emits exactly one of every checked node:

- `og:title`, `og:description`, `og:url`, `og:image`;
- `twitter:card`, `twitter:title`, `twitter:description`, `twitter:image`.

Homepage social imagery now matches the v4 15P marketing creative; PDP/Compare retain clean source media.

## Structured-data review

- PDP has exactly one 15P Product node and one Offer.
- Effective Offer price is `$288.00`; storefront markup also exposes regular `$379.00`.
- Availability is `BackOrder`, not WooCommerce’s broader in-stock boolean.
- BackOrder shipping details retain normal destination/rate/policy information but omit handling/transit timing.
- Current products keep their existing InStock delivery data.
- Homepage, Shop, and Compare each contain one matching 15P Product entry without duplicate `@id` values.
- JSON-LD parser reported zero invalid block across 24 observations.

## Lighthouse and performance observations

| Target | SEO baseline → final | Performance baseline → final | FCP | LCP | CLS | TBT |
|---|---:|---:|---:|---:|---:|---:|
| Local desktop | 100 → 100 | 96 → 96 | 0.9 s → 0.9 s | 0.9 s → 1.0 s | 0.089 → 0.090 | 0 ms → 0 ms |
| Local mobile | 100 → 100 | 83 → 90 | 1.9 s → 1.2 s | 2.9 s → 3.6 s | 0.047 → 0.047 | 410 ms → 50 ms |
| Production desktop baseline | 100 | 87 | 0.4 s | 1.1 s | 0.054 | 270 ms |
| Production mobile baseline | `NO_FCP` | `NO_FCP` | — | — | — | — |

The one-run local mobile LCP value increased by 0.7 s while the overall performance score, FCP, TBT, and CLS improved or held. Baseline and final identify the same H1 as LCP with identical geometry; measured TTFB and element-render-delay subparts improved (about 286→175 ms and 810→375 ms). Additional Lighthouse mobile attempts intermittently returned runner `NO_FCP`. This is recorded as lab variance/runner instability rather than a material content regression; monitor after any future deployment.

## Findings resolved during audit

1. Compare descriptions mentioned `$288` but omitted regular `$379`; all locale metadata now includes both.
2. 15P PDP linked to nonexistent localized 9P product routes; links are now omitted when the product does not exist.
3. Footer SVGs lacked explicit HTML dimensions; native 24×24 dimensions are now present.
4. English 15P sitemap inclusion, localized self-canonicals, and reciprocal hreflang were explicitly verified.

## Deferred baseline observations

- Production social images and 15P indexability cannot reflect this local work until a separately approved deployment.
- Production mobile Lighthouse `NO_FCP` is reproducible in this runner; no score is fabricated.
- Search Console submission, live rich-results validation, ranking change, backlink work, and paid-search activation remain out of scope.
