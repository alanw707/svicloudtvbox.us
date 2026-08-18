# SVICLOUD 15P storefront and backorder integration specification

## Source Workstream Checklist

- Preserve and safely integrate the existing large local storefront change without breaking the current site.
- Inventory the current worktree, including the recent-shipment estimate marquee and Google store-review surfaces.
- Use the RPI workflow before implementation: specification, current-state research, planning pack, explicit approval, implementation, and review.
- Keep the approved 15P marketing image on the homepage hero and commerce cards without clipping.
- Make 15P purchasable on backorder with regular price **$379.00** and sale price **$288.00**.
- Verify the complete local change, then commit it safely to local `main`; do not push or deploy.
- Establish a read-only production/local SEO baseline and audit the changed homepage first, followed by Shop, Compare, 15P PDP, and localized routes.

## Goals

- **G1:** Establish a recoverable, reviewable path from the current mixed worktree to approved local commits.
- **G2:** Offer 15P for backorder at the user-approved regular and sale prices throughout the storefront.
- **G3:** Keep 15P release and fulfillment messaging honest: no invented ship or release date.
- **G4:** Preserve existing public features, locale behavior, accessibility, WooCommerce routes, fixture refresh, and private local data.
- **G5:** Produce evidence that the integrated change introduces no regression relative to the captured baseline.
- **G6:** Preserve or improve technical/on-page SEO signals, especially homepage topic relevance, indexability, locale relationships, metadata, structured data, and internal discovery.

## Non-Goals

- Production deployment, Git push, production writes, or production database cloning.
- Selecting or promising a release date, shipping date, fulfillment window, stock quantity, or preorder cutoff.
- Changing prices or availability for 10P+, 10S, or the Bluetooth remote.
- Redesigning unrelated pages or fixing unrelated baseline defects unless research proves this work caused them.
- Importing users, orders, customers, payments, credentials, logs, private plugin data, or private/draft content.
- Fabricating reviews, Google ratings, shipment records, product claims, performance claims, or availability claims.
- Replacing standard WooCommerce order/payment/email behavior unless required for correct backorder handling.
- Search Console submissions, paid-search changes, backlink campaigns, ranking guarantees, or broad unrelated content rewrites.

## Preliminary Assessment

| ID | Requested state | Current assessment | Baseline evidence |
|---|---|---|---|
| R1 | Complete worktree/feature inventory | **Already true** | `docs/scope-research/15p-storefront-integration-inventory.md` itemizes 67 pre-goal files and dependent features. |
| R2 | RPI artifacts and approval before implementation | **Not true** | Specification is in progress; research, plan pack, and approval do not yet exist. |
| R3 | 15P regular `$379`, sale `$288` | **Not true** | Local WooCommerce product currently has empty regular, sale, and effective prices. |
| R4 | 15P purchasable with backorders allowed | **Not true** | Local state: out of stock, backorders `no`, `is_purchasable=false`, coming-soon marker `yes`. |
| R5 | Consistent commerce copy and structured data | **Not true** | Current tests intentionally require no Offer, no Add to Cart, and “price/release not announced” messaging. |
| R6 | v4 artwork on hero and commerce cards without clipping | **Already true** | Baseline image is 1536×1024; homepage and Shop captures show complete device. |
| R7 | EN/繁中/简中 launch localization | **Already true for informational state; unknown for commerce state** | Current 10-test launch suite passes in both browser projects; backorder wording is not implemented. |
| R8 | Existing features and private data preserved | **Unknown until final verification** | Baseline identifies the data-dependent shipment marquee, environment-dependent Google badge, 1 user, 80 orders, 80 customers, and 84 private/unpublished posts. |
| R9 | No new regression | **Unknown until final verification** | Baseline: launch 10/10, audit 36/36, smoke 14 passed/2 known `/my-account/` failures. |
| R10 | Reviewed local commits on `main` | **Not true** | Worktree is a large uncommitted mixed diff directly on `main`. |
| R11 | Local/read-only-production SEO baseline | **Already true** | `docs/scope-research/15p-storefront-seo-baseline.json` records 24 route observations, infrastructure, internal-link, image, metadata, JSON-LD, and Lighthouse evidence. |
| R12 | Post-change SEO audit without blocking regressions | **Unknown** | Implementation has not started; final route crawl, sitemap checks, structured data, content review, and Lighthouse comparison remain required. |

## Requirements

- **R1 — Inventory integrity:** Every file and runtime dependency present in the pre-implementation snapshot must have a documented feature owner, generated/source classification, and intended disposition.
- **R2 — Approval gate:** No storefront or commerce implementation may begin until the user explicitly approves a plan pack grounded in current-state research.
- **R3 — Recovery:** A verified, non-destructive recovery mechanism must cover tracked edits, untracked deliverables, generated assets, and relevant local WordPress state before implementation or Git integration.
- **R4 — Pricing:** 15P must expose `$379.00` as its regular price and `$288.00` as its current sale/effective price.
- **R5 — Backorder commerce:** Customers must be able to add 15P to cart and complete normal checkout while the product is available on backorder.
- **R6 — Availability truthfulness:** The storefront must clearly identify backorder availability without publishing an unverified ship date, release date, stock count, or fulfillment promise.
- **R7 — Message consistency:** “Price not announced,” “non-purchasable,” and equivalent contradictory copy must be removed from commerce surfaces. “Coming Soon” may remain as release-status artwork/copy only when paired with unambiguous backorder purchasing state.
- **R8 — Surface consistency:** Homepage, Shop, Compare, PDP, cart, checkout, product metadata, and product structured data must agree on price and purchasing state.
- **R9 — Repeatability:** A full public-fixture refresh must recreate the local 15P product, approved media relationships, prices, backorder state, and purchasing behavior without importing or changing private data.
- **R10 — Localization:** Customer-facing price, sale, backorder, action, and availability copy must be coherent in English, Traditional Chinese, and Simplified Chinese routes.
- **R11 — Artwork:** The approved v4 graphic must remain visible on the homepage hero, homepage pricing card, and Shop card with all essential content and the complete product inside the rendered frame. Clean source imagery remains appropriate for PDP/Compare galleries unless the approved plan establishes otherwise.
- **R12 — Feature preservation:** Header/submenus, Guides, recent-shipment marquee, Google Customer Reviews integration, homepage store-rating summary, current product cards, remote card, WooCommerce system pages, accessibility, and responsive behavior must remain intact.
- **R13 — Data preservation:** Users, orders, customers, payments, credentials, logs, private/draft content, attached media, terms, branding, HPOS data, and infrastructure settings must not be replaced or exposed.
- **R14 — Generated artifacts:** CSS and minified JavaScript outputs must be rebuilt from their source files and committed only with corresponding source changes.
- **R15 — Safe integration:** Approved deliverables must be reviewed against the inventory and committed locally using Conventional Commits; no push or deployment occurs.
- **R16 — SEO metadata/indexability:** Required EN/繁中/简中 routes must remain HTTP 200 and indexable with exactly one self-canonical, one description, complete reciprocal hreflang plus x-default, coherent title, robots directives, and matching Open Graph/Twitter URL/content.
- **R17 — Homepage SEO:** The homepage must retain clear SVICLOUD/15P subject relevance, one H1, valid heading progression, discoverable product links, useful source-supported copy, and no accidental noindex, canonical drift, metadata loss, or destructive URL change.
- **R18 — Search discovery:** robots.txt must advertise the active sitemap; English and both Chinese 15P routes must be discoverable through sitemap and/or explicit reciprocal alternate relationships, with no broken internal link or avoidable redirect chain.
- **R19 — SEO structured data:** Homepage, Shop, Compare, and PDP JSON-LD must be parseable, non-duplicative, URL-consistent, and accurately represent the 15P `$288` BackOrder Offer without unverified delivery timing.
- **R20 — SEO/performance evidence:** Desktop/mobile Lighthouse SEO and key performance observations must be compared with baseline; tool/runtime limitations must be recorded and every new material regression investigated.
- **R21 — SEO audit artifact:** A final issue-by-issue technical/on-page SEO report must separate baseline defects from introduced regressions and contain no unresolved blocking finding.

## Acceptance Criteria

- **AC1:** The inventory lists every pre-goal modified/untracked file and explicitly traces both the shipment marquee and Google review surfaces.
- **AC2:** Research classifies every requirement as already true, not true, or unknown with `file:line` or runtime evidence and declares plan readiness.
- **AC3:** The plan pack maps stable task IDs to requirements, dependencies, rollback, risk, review, and exact verification commands; user approval is recorded before implementation.
- **AC4:** Before implementation, recovery verification demonstrates restoration coverage for tracked files, untracked files, generated media/bundles, and relevant local data.
- **AC5:** WooCommerce reports 15P regular price `379`, sale/effective price `288`, purchasing enabled, and backorders allowed after both direct implementation and a full fixture refresh.
- **AC6:** A guest can open the 15P PDP, see `$379.00` struck through and `$288.00` active, add one unit to cart, and reach checkout with a clear backorder notice and no invented fulfillment date.
- **AC7:** Homepage, Shop, Compare, PDP, cart, checkout, metadata, and JSON-LD expose consistent 15P price/availability; structured data contains one valid Product node and an Offer representing backorder availability.
- **AC8:** EN, `?lang=zh`, and `?lang=zh-cn` browser checks confirm localized availability/action copy, correct prices, working cart flow, and no untranslated fixture-only placeholders.
- **AC9:** Desktop and mobile captures prove the v4 hero/card image does not clip essential text or the product and that all primary actions have accessible names, visible focus, and adequate target size.
- **AC10:** Dedicated 15P tests and storefront audit pass completely after being updated from the obsolete non-commerce contract.
- **AC11:** Full smoke results have no new failures versus the 14-pass/2-known-failure baseline; any remaining `/my-account/` failures are shown by evidence to be unchanged and unrelated.
- **AC12:** A complete public-fixture refresh passes route verification and private-preservation verification while retaining the required 15P commerce/media state and baseline private counts or stronger identity/hash checks.
- **AC13:** PHP lint, CSS build, applicable JS build, theme sync, generated-source consistency, and `git diff --check` pass.
- **AC14:** Post-implementation RPI review finds no unresolved blocking Standards or Spec issue.
- **AC15:** Final staged-file review matches the approved inventory/plan; local Conventional Commit(s) exist on `main`, with no push or deployment.
- **AC16:** A machine-readable baseline captures local and read-only production status/final URL, titles/descriptions, robots, canonical/hreflang, social metadata, headings, images, links, JSON-LD, sitemap/robots infrastructure, and Lighthouse observations for the required route matrix.
- **AC17:** Final EN/繁中/简中 crawl reports HTTP 200, one self-canonical, four correct alternate links, one H1/main, no accidental noindex, complete social metadata, parseable JSON-LD, and no newly broken internal links for every required route.
- **AC18:** Final homepage review confirms SVICLOUD/15P topic relevance, source-supported copy, stable URL, valid heading structure, product discoverability, and no material title/description or mobile-rendering regression.
- **AC19:** robots.txt points to the active sitemap; the English 15P product is in the product sitemap, and both Chinese 15P variants are discoverable without canonicalizing or redirecting to English/homepage.
- **AC20:** Exactly one 15P Product node exists on PDP with a `$288.00` Offer and `https://schema.org/BackOrder`; no 15P Offer contains an unverified handling/transit estimate.
- **AC21:** Local desktop/mobile Lighthouse SEO and performance observations are recorded after implementation and compared to baseline; production read-only comparisons are recorded where the runner paints successfully, with failures explicitly documented.
- **AC22:** Final SEO report has no unresolved blocking issue and routes each non-blocking baseline issue to preserve, fix-in-scope, or defer-with-evidence.

## Design Questions

- **DQ1:** Should customer-facing action copy use “Backorder 15P,” “Pre-order 15P,” or WooCommerce’s standard “Add to cart” plus an “Available on backorder” notice in each locale?
- **DQ2:** Which WooCommerce stock-management configuration expresses unlimited backorders without inventing a stock quantity and still produces correct schema availability?
- **DQ3:** Should “Coming Soon” remain outside the approved image on every commerce surface, or only in the artwork/release-status context once purchasing is enabled?
- **DQ4:** What controlled local verification proves the recent-shipment marquee still renders without persisting or exposing real customer shipment data?
- **DQ5:** What production-equivalent test can verify the Google badge integration while preserving the intentional local-development disablement and avoiding third-party test flakiness?
- **DQ6:** How should the large mixed diff be divided into recoverable Conventional Commits without separating generated artifacts from their source changes?
- **DQ7:** How should sitemap verification account for local WordPress core sitemap (`wp-sitemap.xml`) versus production Rank Math (`sitemap_index.xml`) while proving both Chinese 15P routes are discoverable?
- **DQ8:** What fallback evidence is acceptable when production mobile Lighthouse repeatedly returns `NO_FCP`, while local mobile and desktop runs succeed?

## Open Questions

- No price, commerce-state, deployment, or scope decision remains open: the user approved `$379/$288`, backorders enabled now, local-main commit, and no deployment.
- DQ1–DQ8 are implementation-shaping questions for research and planning; any unresolved critical question blocks implementation.
- Production currently redirects all requested 15P PDP locale paths to the homepage and omits 15P from its product sitemap; this is baseline context, not behavior to preserve after the local launch is eventually deployed.
