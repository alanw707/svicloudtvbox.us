# 15P storefront/backorder integration — post-implementation review

## Mode

**Post-implementation.** Git context: local `main` contains the final reconstructed three-commit sequence; the final tree matches safety snapshot `63c2bfe` and is bounded by `15p-storefront-backorder-integration-planned-structure.md`. Cached and committed diffs were inspected.

## Artifact Coherence

| Check | Verdict | Evidence |
|---|---|---|
| Spec → research | Pass | R1–R21 and AC1–AC22 are classified/traced; commerce, SEO, data, locale, schema, and preservation seams have file/runtime evidence. |
| Research → plan | Pass | Ready verdict feeds T1–T10; every task has files, dependency, rollback, risk, review, and exact verification. |
| Plan → implementation | Pass | Approved Backorder wording, card alignment, artwork boundary, policy boundary, recovery, metadata, and three-commit decisions are honored. |
| Replan triggers | Pass | SEO audit findings and stale hard-coded test product IDs were fixed within planned files; no unresearched runtime boundary was added. |
| Planned structure | Pass | Implementation changes are inside the canonical file list; new SEO evidence/audit and final screenshots are explicitly planned outputs. |
| Recovery | Pass | Original safety commit `9846579`, adversarial pre-fix branch `6f619f8`, post-fix snapshot `63c2bfe`, gzip-tested DB backups, and private identity hashes verified. |

## AC Coverage

| AC | T task | Result/evidence |
|---|---|---|
| AC1 | T1 | 67 pre-goal files itemized; shipment marquee and both Google review surfaces traced. |
| AC2 | T1–T3 | Research has workflow/file maps, design evidence, SEO extension, and Ready verdict. |
| AC3 | T3 | Complete pack approved explicitly before recovery/implementation. |
| AC4 | T1 | Git/DB/uploads/private recovery proof passed. |
| AC5 | T2/T6 | Runtime and post-refresh state: `379/288/288`, managed `0`, `notify`, `onbackorder`, purchasable. |
| AC6 | T4/T6 | PDP → cart → checkout browser flow passes with localized backorder/date notices. |
| AC7 | T3/T4/T7 | Homepage/Shop/Compare/PDP/cart/checkout/meta/schema agree; one BackOrder Offer. |
| AC8 | T4/T6 | EN/繁中/简中 route/action/status/meta assertions pass in both projects. |
| AC9 | T4/T8 | v4 hero/cards and clean PDP/Compare images pass desktop/mobile visual and target/focus checks. |
| AC10 | T4/T6 | Launch 10/10 and storefront 36/36. |
| AC11 | T8 | Smoke 14 pass/2 identical `/my-account/` baseline failures; no new smoke failure. |
| AC12 | T2/T6/T8 | Full refresh, 4/4 routes, probe identity, and private counts pass. |
| AC13 | T6/T8 | 12 generated artifacts idempotent; 58 PHP files, JS/Python syntax, sync, and diff check pass. |
| AC14 | T9 | This Standards/Spec/architecture review has no blocking implementation finding. |
| AC15 | T10 | Three exact local Conventional Commits exist in approved order; cached/committed diffs and intermediate coherence pass; no push/deployment. |
| AC16 | T5/T7 | Machine baseline covers 24 local/production pages, infrastructure, links, schema, and Lighthouse. |
| AC17 | T5/T7 | Final 24-page audit: 200, self-canonical, four hreflang, H1/main, social/schema/image checks, zero issues. |
| AC18 | T3/T7 | Homepage authorized-dealer/小雲盒子/15P relevance and approved metadata preserved. |
| AC19 | T5/T7 | Robots advertises active sitemap; English 15P included; locale discovery/self-canonical links pass. |
| AC20 | T3/T7 | Exactly one PDP Product Offer: `288.00 USD`, `BackOrder`, no `deliveryTime`. |
| AC21 | T7 | Lighthouse SEO 100 desktop/mobile; performance observations and production `NO_FCP` limitation documented. |
| AC22 | T7/T9 | Final SEO report has zero blockers and routes baseline/deferred observations explicitly. |

## Code Review

### Standards axis

**0 blocking findings.** PHP changes retain `svic_` prefixes, guards, type declarations where established, escaped URLs/attributes/text, WooCommerce public APIs, and early returns. The money/stock path is fixture-backed and browser-tested. CSS changed only in partials and generated bundles rebuild idempotently. JS uses installed Playwright/Node built-ins; no dependency or raw production HTML is persisted. Private backups remain owner-only outside Git.

### Spec axis

**0 blocking findings.** Woo state is authoritative; template-only prelaunch suppression is removed. `svic_price_html()` is reused; one shared availability mapper prevents schema drift. BackOrder delivery timing is removed without changing current-product timing. Cart/checkout/footer stop showing current-model speed/warranty claims when 15P is present. Approved titles, actions, images, locale copy, sitemap discovery, and policy boundaries are covered by executable checks.

### Adversarial follow-up

| Finding | Resolution/evidence |
|---|---|
| Cross-origin REST credential forwarding | Same-origin redirect handlers and endpoint-override guards; `scripts/test_public_fixture_security.py`: 3/3 pass with dummy credentials. |
| Partial fixture success | Importer records fatal resource errors and checks complete source-ID manifests; controlled malformed fixture exits nonzero; valid refresh and private-preservation checks pass. |
| Chinese Guides route | `template_include` routes encoded aliases; actual header-link test: 4/4 Chromium/WebKit pass, no literal HTML or external fixture images. |
| Chinese 15P cart locale | Woo add-to-cart form action is locale-aware; `locale-commerce.spec.ts`: 4/4 pass across both projects, including cart/checkout notices. |
| Checkout evidence gap | Temporary offline gateway reaches order-received with dummy buyer; orders deleted and gateway/stock restored. Default local payment absence remains documented. |
| Dead Rank Math hook | Unreachable conditional hook removed; PHP lint and SEO/schema audits pass. |
| Commit boundary | 15P supplement/assets moved out of commit 1; final three-commit inspection passes. |

### Non-blocking findings

1. Full suite retains 8 proven pre-existing failures (historical missing blog uploads, never-implemented dynamic Blog submenu, `/my-account/` gutter). They do not regress the captured launch baseline and are outside this slice. **→ defer** — separate fixture/header/account maintenance work with its own baseline.
2. One-run mobile LCP changed 2.9→3.6 s while score/FCP/TBT improved and the same H1 geometry/subparts improved; repeated runner attempts can return `NO_FCP`. **→ defer** — monitor after a separately approved deployment; no material local regression evidence.

## Architecture Opportunities

1. `functions.php` owns locale routing, SEO, schema, analytics, and commerce filters; deletion testing shows unrelated edits require broad review. Split only when a dedicated refactor can preserve hook order. **Worth exploring → improve-codebase-architecture**.
2. `products.svicloud-15p.prelaunch.*` keys now describe a backorder launch. Renaming would improve vocabulary but touch three large registries/templates with no runtime gain. **Speculative → defer** — rename during the next planned translation-schema migration.
3. Homepage, Shop, and Compare still assemble similar card arrays independently. A small shared read-only card-state function could reduce future price/status drift, but current shared Woo price/schema helpers cover the risky logic. **Worth exploring → improve-codebase-architecture**.

## Summary

- Blocking Standards findings: **0**
- Blocking Spec/AC findings: **0**
- SEO blocking findings: **0**
- Deferred proven-baseline findings: **2**
- Architecture candidates: **3**
- Final state: three reviewed local commits, clean worktree, safety references preserved, `origin/main` unchanged, and no push/deployment.
