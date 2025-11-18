# SEO Task Log – November 2025

This log tracks the follow-up work requested after the navigation schema deployment review. Each task is paired with owners, dependencies, and a concrete verification plan so we can close the loop in Google Search Console (GSC) and the Rich Results Test.

| # | Task | Owner(s) | Inputs / Files | Acceptance + Verification |
|---|------|----------|----------------|---------------------------|
| 1 | Restore homepage `WebPage` schema when Rank Math is active | Dev (Theme) | `theme/svicloudtvbox-lumen/functions.php` (`svic_disable_rank_math_front_page_schema`, `svic_output_homepage_webpage_schema`) | *Implementation*: allow `svic_output_homepage_webpage_schema()` to run even if Rank Math exists or stop disabling Rank Math’s schema. *Verification*: (a) Flush LiteSpeed cache + rerun `python3 scripts/sync_theme_container.sh`; (b) Run [Rich Results Test](https://search.google.com/test/rich-results) on `/` (live fetch) and confirm `WebPage` + `FAQ` + `Product`; (c) Validate `rank_math/json_ld` graph shows homepage node with `isPartOf` pointing to `#website`; (d) Capture rendered HTML snippet for task notes. |
| 2 | Emit FAQ JSON-LD on `/faq/` | Dev (Theme) + SEO | `theme/svicloudtvbox-lumen/page-faq.php` | *Implementation*: mirror the FAQ builder used on the homepage so each accordion item becomes a `Question`/`Answer` in the `FAQPage` graph, localized per language. *Verification*: (a) View-source on `/faq/` and confirm a single FAQ JSON-LD block; (b) Run Rich Results Test on `/faq/`; (c) Re-submit `/faq/` via GSC “Inspect URL → Request Indexing”. |
| 3 | Add HowTo schema to Guides (starting with setup guide) | Dev (Theme) + Content | `theme/svicloudtvbox-lumen/page-guide-section.php`, translation keys in `inc/guides-data.php` | *Implementation*: map each numbered instruction into `HowToStep` with `image`/`url` references where available. *Verification*: (a) Validate `/guides-setup/` via Rich Results; (b) confirm HowTo is bilingual (EN + zh) and that hreflang pairs still match; (c) update docs/launch-search-social-seo-plan.md checklist to mark “HowTo schema on Guides” complete. |
| 4 | Publish Traditional Chinese blog/landing pages per ranking strategy | Content + SEO + Marketing | `claudedocs/chinese-keyword-ranking-strategy-nov-2025.md`, WordPress posts | *Implementation*: ship at least 5 zh articles + 3 state-specific landing pages mentioned in the strategy, ensure internal links from `/zh/` nav, add them to the zh sitemap. *Verification*: (a) After each publish, request indexing for the zh URL; (b) Monitor GSC “Coverage → Submitted and indexed” for `/zh/`; (c) Track target queries (“小雲電視盒 美國”, etc.) weekly; (d) Document backlinks/outreach in marketing tracker. |
| 5 | Update navigation schema documentation + confirm output | Dev (Theme) + Docs | `claudedocs/site-navigation-schema-implementation.md`, `claudedocs/seo-diagnosis-nov-2025.md` | *Implementation*: explain that Rank Math sites rely on the `rank_math/json_ld` filter (not `wp_head`), remove outdated PHP 7.0 notes, and link to verification procedure. *Verification*: (a) After docs update, capture screenshot of Rich Results Test showing SiteNavigationElement; (b) add excerpt + timestamp to the doc for future audits. |
| 6 | Weekly GSC verification loop | SEO | GSC property + LiteSpeed admin | *Implementation*: standing checklist after each deploy—flush LiteSpeed twice, `./scripts/sync_theme_container.sh`, run Rich Results live tests (home, `/zh/`, `/faq/`, `/guides-setup/`, `/product/svicloud-10p-plus/`), re-submit updated sitemap, inspect URLs, and log results here. *Verification*: mark each week’s run with date + ✅/⚠️ summary in this table (append rows as we progress). |

## Verification Log Template

Add rows below as work ships.

| Date | Pages Tested | Result | Notes |
|------|--------------|--------|-------|
| 2025-11-18 | `/`, `/faq/`, `/guides-setup/`, `/product/svicloud-10p-plus/`, `/zh/` | ✅ Rich Results clean on all pages | Screenshots: `gshot-2025-11-18-085000-OGQY.png`, `gshot-2025-11-18-085335-JURb.png`, `gshot-2025-11-18-085405-rgaL.png`, `gshot-2025-11-18-085447-JAuL.png`, `gshot-2025-11-18-085521-BZom.png`. Pending: request indexing `/faq/` and `/guides-setup/` in GSC, resubmit sitemap. |

---

**Next Steps**
1. Assign owners/dates in Asana/Jira referencing the table IDs above.
2. Once a task ships, paste Rich Results / GSC evidence links into the verification log.
3. Keep this log under version control so future audits know when each SEO fix went live.
