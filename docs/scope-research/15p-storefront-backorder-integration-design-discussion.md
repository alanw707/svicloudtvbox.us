# 15P storefront/backorder integration — design discussion

## Context Summary

The worktree combines a read-only public fixture refresh, header/Guides work, and an informational 15P launch. The 15P product currently has no price, is non-purchasable, and is hard-coded as prelaunch across four surfaces. The approved change makes it a notified backorder at `$288` sale / `$379` regular while preserving current features and search visibility. Research and baseline evidence live in `15p-storefront-backorder-integration-research.md`, `15p-storefront-integration-inventory.md`, and `15p-storefront-seo-baseline.json`.

## Design Goals

- Treat WooCommerce product state—not a presentation flag—as commerce authority.
- Keep the source-supported 15P product story and approved v4 artwork while removing contradictory surrounding copy.
- Present the same price, backorder state, and action across homepage, Shop, Compare, PDP, cart/checkout, fixture verification, metadata, and JSON-LD.
- Preserve established homepage `小雲盒子`/authorized-dealer/buying intent while adding 15P Backorder relevance.
- Keep public fixture refresh repeatable and private data untouched.
- Produce deterministic audits that distinguish introduced regressions from baseline/environment limitations.

## Proposed Solution Shape

### Product and fixture authority

The deterministic local 15P supplement remains a normal `WC_Product_Simple`. Its fixture state becomes: regular `379`, sale/effective `288`, managed stock enabled, quantity `0`, backorders `notify`, stock status `onbackorder`, published/visible, and three approved gallery attachments. The obsolete coming-soon product marker is removed or made non-authoritative. Post-sync verification asserts exact price, sale, stock, backorder, purchasability, media, and fixture-key state.

### Storefront presentation

15P-specific content remains selected by product slug, but commerce rendering reads the WC product state. Existing `svic_price_html()` supplies accessible sale/original markup. Homepage hero, launch banner, pricing card, Shop, Compare, and PDP actions use localized `Backorder 15P`; the PDP uses WooCommerce’s standard add-to-cart flow with a localized button label. Cart/checkout show a localized backorder notice without a date.

The card mirrors 10P+ structure: left-aligned price/status/title/copy, centered CTA, equal content width/rhythm. Outer badges and copy use `Available on backorder` and `Shipping date not announced`; “Coming Soon” remains only inside v4 artwork. Marketing v4 remains on hero/pricing/Shop; clean source media remains on Compare/PDP.

### Availability and structured data

One theme helper maps WC state in this order: on backorder → `https://schema.org/BackOrder`; in stock → `InStock`; otherwise → `OutOfStock`. Homepage and PDP schema builders consume the same mapping. BackOrder Offers keep price/currency/URL/seller and normal policy links but omit handling/transit `deliveryTime`; current-model Offers retain existing delivery data. Exactly one Product node remains on PDP.

### SEO preservation

Homepage metadata combines established intent with the approved launch:

- EN title: `SVICLOUD 15P Backorder | 小雲盒子 U.S. Authorized Dealer`
- 繁中 title: `小雲 15P 缺貨訂購｜小雲盒子美國授權經銷`
- 简中 title: `小云 15P 缺货订购｜小云盒子美国授权经销`

Descriptions include `$288`, regular `$379`, authorized-dealer context, 10P+/10S comparison, and no announced shipping date. They do not add shipping-speed or warranty promises. Existing self-canonical and reciprocal hreflang behavior remains. The active sitemap is environment-specific: core WordPress locally and Rank Math in production. English 15P must appear in the product sitemap; localized virtual URLs remain discoverable through reciprocal hreflang and internal links rather than introducing a second sitemap architecture in this slice.

A new repository audit script records route-level status/final URL, indexability, title/description, canonical/hreflang, headings, social metadata, images, internal links, JSON-LD, robots/sitemap, and 15P Offer accuracy. Lighthouse remains a separate command: local desktop/mobile required; production comparisons best-effort because production mobile repeatedly returned `NO_FCP` at baseline.

### Recovery and integration

Before implementation, create a local safety branch snapshot commit covering every current tracked/untracked deliverable, plus an external private database backup and content manifest. Return to `main`, restore the identical uncommitted worktree from the snapshot, and compare file manifests. Nothing is pushed.

Final changes are split into three coherent commits. Fixture-core hunks must not reference 15P-only assets until the 15P commit; shared files are staged by reviewed hunks.

## Intended Placement

- Product creation/invariants: existing fixture importer/sync verifier.
- Reusable WC price and schema availability behavior: existing theme helper layer.
- Surface composition: existing homepage, Shop, Compare, PDP, cart templates.
- Localized customer/SEO copy: existing three locale registries.
- Metadata/schema integration: existing `functions.php` SEO/schema hooks.
- Layout: existing 15P/Shop/WooCommerce CSS partials and generated bundles.
- Verification: existing launch/audit scripts plus one new SEO audit script.
- Evidence/reasoning: existing RPI and launch documentation directories.

## Architecture Patterns

- **Single state authority:** WC product data drives purchasing, stock, notices, and schema.
- **Thin templates:** templates select localized presentation but do not invent price or stock state.
- **Shared availability mapping:** homepage/PDP schema cannot drift between BackOrder/InStock/OutOfStock.
- **Environment adapter:** validate whichever sitemap adapter is active; do not hard-code Rank Math locally.
- **Deterministic supplement:** production REST remains read-only; local 15P state is recreated after every refresh.
- **Evidence before integration:** baseline → approved plan → recovery → implementation → audits → review → staged commits.

## Design Questions and Answers

1. **Purchase action?** `Backorder 15P` (user approved), localized naturally in Traditional/Simplified Chinese while preserving backorder meaning.
2. **Card alignment?** Match 10P+ content structure; left-align informational content and center CTA (approved).
3. **Where does Coming Soon remain?** Only inside approved artwork (Option A approved).
4. **Which policies apply?** Normal checkout/payment/shipping-rate/cancellation/return behavior; no 15P-specific shipping speed/date or warranty promise; BackOrder schema omits delivery estimates (Option A approved).
5. **Recovery mechanism?** Local safety branch snapshot plus external DB backup/private manifest, then identical restore on `main` (Option A approved).
6. **Commit shape?** Three local Conventional Commits for fixture core, header/Guides, and 15P backorder launch (Option A approved).
7. **Homepage metadata?** Preserve established dealer/buying intent while adding localized 15P Backorder relevance using the titles above (Option A approved).
8. **Sitemap/Lighthouse differences?** Validate active adapter; require English 15P sitemap plus reciprocal locale discovery. Local mobile Lighthouse is required; production mobile `NO_FCP` may be documented if reproducible.

## Tradeoffs and Rejected Options

- Rejected keeping 15P non-purchasable or merely displaying prices: contradicts approved backorders.
- Rejected using `Pre-order 15P` or generic Add to cart as the main action: user chose Backorder 15P.
- Rejected repeating Coming Soon outside artwork: weakens commerce clarity.
- Rejected inheriting current-model shipping-speed/warranty marketing: not verified for 15P.
- Rejected schema InStock for a backorder: semantically inaccurate even though Woo’s `is_in_stock()` returns true.
- Rejected applying fixed current-model delivery estimates to BackOrder Offers.
- Rejected launch-only homepage metadata: loses established dealer/buying intent and keeps false price-unannounced copy.
- Rejected adding a package dependency for SEO auditing: Playwright plus ephemeral Lighthouse CLI provide evidence.
- Rejected a single large commit or stash-only recovery: weaker inspection and rollback.

## Follow-Up Decisions

- If implementation reveals Woo/Rank Math transforms `BackOrder` or duplicates Product nodes differently than research, stop and replan schema integration.
- If production mobile Lighthouse still returns `NO_FCP`, preserve the error artifact and compare local mobile plus production desktop; do not fabricate a score.
- Any copy that implies a 15P ship date, shipping speed, or warranty is a blocking review finding.
- Production deployment, Search Console submission, and live indexing validation remain separate user-approved work.
