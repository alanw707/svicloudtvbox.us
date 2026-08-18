# 15P storefront/backorder integration — execution plan

## Plan Status

**Ready for explicit user approval.** Research verdict is Ready; all material commerce, content, SEO, recovery, and commit decisions are resolved. Implementation remains blocked until approval is recorded.

## Preconditions

- Source contract: `docs/specs/15p-storefront-backorder-integration.md` (R1–R21, AC1–AC22).
- Evidence: inventory, research, SEO baseline, design discussion, and planned structure share this slug.
- Branch/HEAD baseline: local `main` at `34af29c604cc782569df8e7db994dddca61a1b7d` with 67 inventoried pre-goal files plus RPI artifacts.
- Runtime baseline: launch 10/10, storefront audit 36/36, smoke 14 pass/2 known `/my-account/` failures.
- No production write, push, deployment, Search Console action, or fulfillment-date invention is authorized.

## Clarifications Resolved

- Visible action: `Backorder 15P`; natural locale equivalents preserve the same meaning.
- Price/state: regular `$379`, sale/effective `$299`, managed zero stock, backorders `notify`, `onbackorder`.
- Card: match 10P+ rhythm; left-aligned price/status/title/copy, centered CTA.
- “Coming Soon”: artwork only. UI says Available on backorder / Shipping date not announced.
- Policies: normal checkout/payment/shipping-rate/cancellation/return behavior; no 15P-specific speed/date/warranty promise.
- Schema: `BackOrder`; no handling/transit estimate on the 15P Offer.
- SEO: preserve authorized-dealer/`小雲盒子`/buying intent while adding 15P Backorder.
- Recovery: local safety snapshot commit + external DB backup/manifest.
- Integration: three approved local commits; no push.

## Design Summary

Use existing WooCommerce product state as the single commerce authority, existing `svic_price_html()` for sale markup, and one minimal shared WC→schema availability helper. Remove commerce suppression from 15P presentation branches while retaining 15P content/media selection. Make the fixture and post-sync invariant authoritative. Preserve canonical/hreflang architecture; add one Playwright SEO audit rather than a runtime dependency. BackOrder Offers omit delivery time through the existing merchant-enrichment seam.

## Structure Summary

- **Current:** fixture and four templates encode non-commerce independently; schema has no BackOrder mapping; SEO checks are partial.
- **Planned:** fixture WC state → shared price/availability behavior → all customer surfaces/schema → launch/SEO/regression audits.
- **Dependencies:** T2/T3/T4 may proceed after recovery; T6 joins them; all integration/review tasks are serial afterward.
- **Canonical file handoff:** `15p-storefront-backorder-integration-planned-structure.md`.

## Solution Path

1. Preserve every current artifact and private runtime state.
2. Change the deterministic product state and its invariant.
3. Update shared schema/meta behavior and customer surfaces without broad abstractions.
4. Rebuild/sync and apply a full fixture refresh so tests exercise the repeatable state.
5. Run SEO and storefront audits, repair only introduced/in-scope findings, then review and commit by approved boundaries.

## Task Breakdown

### T1. Establish and verify recovery point
- Files: external `$HOME/.pi/backups/svicloudtvbox.us/2026-08-17-pre-15p-backorder/`; local branch `safety/15p-storefront-pre-implementation-20260817`
- Action: hash every inventoried tracked/untracked deliverable; export/gzip local DB and private-count manifest outside Git; create a local snapshot commit; return to `main`, restore the snapshot as the same uncommitted worktree, and verify hashes/status. Record paths, hashes, commit, branch, DB permissions, and no-push proof in a small recovery evidence file under the external backup directory.
- Depends on: none
- Rollback: stop on any manifest mismatch; remain on safety branch or restore DB/files from the verified external backup.
- Parallel: no
- Risk: high
- Review required: yes
- Verify: `git branch --show-current && git show --stat --oneline safety/15p-storefront-pre-implementation-20260817 && sha256sum -c "$HOME/.pi/backups/svicloudtvbox.us/2026-08-17-pre-15p-backorder/worktree.sha256" && gzip -t "$HOME/.pi/backups/svicloudtvbox.us/2026-08-17-pre-15p-backorder/local-wordpress.sql.gz"`

### T2. Make fixture product state authoritative
- Files: `scripts/import_public_theme_fixture.php`, `scripts/sync_public_theme_fixture.py`, `scripts/verify_private_fixture_preservation.py`, `scripts/verify_public_fixture_routes.mjs`, `docs/production-data-refresh.md`
- Action: set exact 15P regular/sale/effective prices, managed quantity zero, notified backorders, `onbackorder`, published visibility, reviews/media/categories; remove obsolete commerce marker authority; strengthen post-sync state verification; keep preservation probe independent of 15P-only media for commit separation.
- Depends on: T1
- Rollback: restore T2 files from safety snapshot; do not apply a failed fixture twice without restoring the local DB backup.
- Parallel: yes
- Risk: high
- Review required: yes
- Verify: `php -l scripts/import_public_theme_fixture.php && python3 -m py_compile scripts/sync_public_theme_fixture.py scripts/verify_private_fixture_preservation.py && node --check scripts/verify_public_fixture_routes.mjs`

### T3. Align shared schema and SEO metadata
- Files: `theme/svicloudtvbox-lumen/inc/helpers-svic.php`, `theme/svicloudtvbox-lumen/functions.php`, `theme/svicloudtvbox-lumen/lang/en_US.php`, `lang/zh_TW.php`, `lang/zh_CN.php`
- Action: add the smallest shared BackOrder/InStock/OutOfStock mapping; make homepage/PDP/Rank Math Offer paths use it; omit deliveryTime only for BackOrder; update approved localized homepage/PDP/social titles/descriptions and query-locale button/notice filters; preserve one canonical, reciprocal hreflang, URLs, and current-model schema.
- Depends on: T1
- Rollback: restore T3 hunks from safety snapshot; leave fixture state untouched if metadata/schema verification fails.
- Parallel: yes
- Risk: high
- Review required: yes
- Verify: `php -l theme/svicloudtvbox-lumen/inc/helpers-svic.php && php -l theme/svicloudtvbox-lumen/functions.php && php -l theme/svicloudtvbox-lumen/lang/en_US.php && php -l theme/svicloudtvbox-lumen/lang/zh_TW.php && php -l theme/svicloudtvbox-lumen/lang/zh_CN.php`

### T4. Implement consistent storefront and localized backorder flow
- Files: `theme/svicloudtvbox-lumen/front-page.php`, `woocommerce/archive-product.php`, `page-compare.php`, `woocommerce/single-product.php`, `woocommerce/cart/cart.php`, locale registries, `assets/css/parts/32b-15p-launch-redesign.css`, `65-shop.css`, `70-lumen-woocommerce.css`, generated CSS bundles, `tests/playwright/launch-15p.spec.ts`, `scripts/audit_15p_storefront.mjs`
- Action: remove prelaunch commerce suppression while retaining 15P media/content; render `$299/$379`, Available on backorder, Shipping date not announced, and Backorder 15P on all approved surfaces; use standard WC add-to-cart/cart/checkout; align card geometry; retain v4 on hero/cards and clean media on PDP/Compare; rewrite obsolete negative tests for cart/locale/schema/accessibility behavior.
- Depends on: T1
- Rollback: restore template/locale/CSS/test hunks from safety snapshot and rebuild bundles.
- Parallel: yes, except locale files must be coordinated with T3
- Risk: high
- Review required: yes
- Verify: `find theme/svicloudtvbox-lumen -path '*/vendor/*' -prune -o -name '*.php' -print0 | xargs -0 -n1 php -l && node --check scripts/audit_15p_storefront.mjs && npx playwright test tests/playwright/launch-15p.spec.ts --list`

### T5. Add bounded SEO audit tooling
- Files: `scripts/audit_storefront_seo.mjs`, `package.json` only if an existing script alias is useful without adding dependencies
- Action: reuse installed Playwright/Node built-ins to audit required EN/繁中/简中 routes, final URLs, noindex, unique metadata/canonical, reciprocal hreflang, H1/main/headings, social tags, image loading/alt/dimensions, internal link status, JSON-LD parse/duplicates/BackOrder accuracy, robots, and active sitemap. Emit bounded JSON/Markdown evidence; do not scrape or persist raw production HTML.
- Depends on: T1
- Rollback: delete the new audit file/optional alias; no runtime theme state is involved.
- Parallel: yes
- Risk: medium
- Review required: yes
- Verify: `node --check scripts/audit_storefront_seo.mjs && PLAYWRIGHT_BASE_URL=http://svicloud10p.svic.local node scripts/audit_storefront_seo.mjs`

### T6. Rebuild, sync, refresh fixture, and prove targeted commerce
- Files: generated CSS/JS selected by repository build scripts; local WordPress runtime only
- Action: build source bundles, sync theme, apply one full read-only-production/public fixture refresh, verify exact private state and 15P WC state, then run targeted launch/audit/cart/checkout checks across locales and viewports.
- Depends on: T2, T3, T4, T5
- Rollback: restore verified external DB backup and safety-snapshot files if refresh or preservation differs; do not proceed to SEO/regression gate.
- Parallel: no
- Risk: high
- Review required: yes
- Verify: `python3 scripts/build_css.py --theme svicloudtvbox-lumen && npm run build:js && ./scripts/sync_theme_container.sh && python3 scripts/verify_private_fixture_preservation.py && node scripts/verify_public_fixture_routes.mjs && npx playwright test tests/playwright/launch-15p.spec.ts && node scripts/audit_15p_storefront.mjs`

### T7. Run homepage-first SEO audit and remediate in-scope regressions
- Files: `docs/15p-launch/verification-report.md`, SEO audit evidence under `docs/scope-research/`; only T2–T5 files when a finding proves a root-cause fix is required
- Action: compare final local crawl with the machine baseline and read-only production context; check approved titles/descriptions, canonical/hreflang, robots/active sitemap, English 15P inclusion, localized discovery, links, images, social tags, one BackOrder Product Offer, no deliveryTime, mobile rendering, and Lighthouse. Classify each finding as introduced/fixed/baseline/deferred; fix every introduced blocking issue.
- Depends on: T6
- Rollback: revert only the remediation hunk to the T6 verified state; re-run the failing audit before broader validation.
- Parallel: no
- Risk: high
- Review required: yes
- Verify: `PLAYWRIGHT_BASE_URL=http://svicloud10p.svic.local node scripts/audit_storefront_seo.mjs && CHROME_PATH=/usr/bin/google-chrome npx --yes lighthouse http://svicloud10p.svic.local/ --only-categories=seo,performance --preset=desktop --output=json --output-path=/tmp/lighthouse-home-desktop-final.json --quiet --chrome-flags='--headless --no-sandbox' && CHROME_PATH=/usr/bin/google-chrome npx --yes lighthouse http://svicloud10p.svic.local/ --only-categories=seo,performance --output=json --output-path=/tmp/lighthouse-home-mobile-final.json --quiet --chrome-flags='--headless --no-sandbox'`

### T8. Run complete regression, preservation, and visual gate
- Files: final screenshots/evidence in `docs/15p-launch/screenshots/`, verification report; runtime `.playwright/` and `test-results/` remain untracked
- Action: run full PHP/build/diff checks, launch/SEO/storefront/header/remote/smoke coverage, cart/checkout and locale routes, second private-state comparison, and desktop/mobile captures. Prove no new failure beyond the two recorded `/my-account/` baseline failures or fix the regression.
- Depends on: T7
- Rollback: route any failure to T2–T5 root cause; restore safety/DB snapshot if data preservation fails.
- Parallel: no
- Risk: high
- Review required: yes
- Verify: `git diff --check && find theme/svicloudtvbox-lumen scripts -name '*.php' -print0 | xargs -0 -n1 php -l && npm test`

### T9. Perform RPI post-implementation review and close findings
- Files: `docs/scope-research/15p-storefront-backorder-integration-review.md`; any planned file required by a blocking fix
- Action: review artifact chain, Standards, Spec/AC1–AC22, diff against planned structure, SEO findings, and inline architecture seams; route and fix every blocker, then rerun its owning validation.
- Depends on: T8
- Rollback: revert review-driven hunk if its owning validation regresses; reopen the originating task or replan on scope drift.
- Parallel: no
- Risk: medium
- Review required: yes
- Verify: `git diff --check && test -s docs/scope-research/15p-storefront-backorder-integration-review.md`

### T10. Stage and create three reviewed local commits
- Files: every final file listed in planned structure/inventory, staged by feature and reviewed hunks
- Action: create fixture-core, header/Guides, and 15P backorder commits in approved order. Keep generated files/tests/docs with their source; exclude runtime evidence, credentials, source PDF/PPTX, backups, and unrelated files. Inspect cached diff and run commit-specific checks before each commit; confirm `main`, no push, and final worktree disposition.
- Depends on: T9
- Rollback: before commit, unstage with `git restore --staged` only; after a bad local commit, stop and ask rather than rewriting published history. Safety branch remains recovery source.
- Parallel: no
- Risk: high
- Review required: yes
- Verify: `git branch --show-current && git log -3 --format='%h %s' && git status --short && git rev-parse origin/main && git rev-parse main`

## Requirements Traceability

| Tasks | Requirements / acceptance coverage |
|---|---|
| T1 | R1–R3, R13, R15; AC1–AC4 |
| T2 | R4–R5, R9, R13; AC5, AC12 |
| T3 | R6–R10, R16–R20; AC7–AC8, AC16–AC21 |
| T4 | R4–R12, R14; AC5–AC11, AC13 |
| T5 | R16–R21; AC16–AC22 |
| T6 | R4–R14; AC5–AC13 |
| T7 | R16–R21; AC16–AC22 |
| T8 | R11–R14, R20; AC9–AC13, AC21 |
| T9 | all requirements; AC14, AC22 |
| T10 | R1, R3, R14–R15; AC15 |

## Constraints

- Production REST stays read-only; importer boundaries/private counts remain enforced (`sync_public_theme_fixture.py:306-353`).
- WC state must survive full refresh (`import_public_theme_fixture.php:456-490`).
- PDP slug may select 15P content but cannot suppress valid commerce (`woocommerce/single-product.php:39,260-268`).
- Cart notice depends on notified backorders (`woocommerce/cart/cart.php:100-105`; Woo 10.3.8 behavior documented in research).
- BackOrder must precede `is_in_stock()` in schema mapping (`functions.php:2605-2634`).
- No BackOrder Offer delivery timing (`functions.php:2701-2774`).
- Canonical/hreflang URLs remain language-aware (`functions.php:533-579,1589-1918`).
- Active sitemap endpoint remains adapter-aware (`functions.php:5048-5079`).
- Never hand-edit generated bundles; rebuild from partials/JS source.
- No private DB/backup artifact enters Git; backup permissions must be owner-only.

## Validation

| Task | Evidence target | Command |
|---|---|---|
| T1 | snapshot/DB/manifest hashes | T1 Verify command |
| T2 | exact fixture state and preservation | T2 then T6 commands |
| T3 | one BackOrder Offer, localized meta, no deliveryTime | SEO audit + launch suite |
| T4 | `$299/$379`, actions/notices/cart flow, alignment | launch suite + 36-check audit |
| T5 | bounded route/infrastructure audit | `node scripts/audit_storefront_seo.mjs` |
| T6 | repeatability and targeted green gate | complete T6 command |
| T7 | SEO report + Lighthouse comparison | complete T7 command |
| T8 | full no-new-regression evidence and screenshots | complete T8 command |
| T9 | approved review with no blocker | review artifact + reruns |
| T10 | three local commits; no push/deploy | log/status/revision inspection |

## Replan Triggers

- Recovery manifest or private DB/count identity cannot be proven.
- Full refresh changes private data or cannot reproduce exact 15P commerce/media state.
- WooCommerce does not produce a localized notified backorder through standard cart/checkout seams.
- Rank Math/Woo transforms or duplicates the 15P Product/Offer outside researched paths.
- BackOrder cannot be emitted without adding unverified delivery timing.
- Any required locale canonicalizes/redirects to another locale or loses reciprocal hreflang.
- SEO audit requires a new runtime dependency or reaches files outside planned structure.
- A new smoke/accessibility/visual failure cannot be tied to a planned task.
- Commit separation would create a broken intermediate commit or require unreviewed files.
- User changes price, availability, wording, policy, metadata, commit, or deployment decisions.
