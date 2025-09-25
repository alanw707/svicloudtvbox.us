# Custom Bilingual Framework Plan

## Goals
- Deliver full English ↔ Chinese coverage across templates, WooCommerce views, and bespoke components without relying on third-party plugins.
- Give content editors a predictable workflow for maintaining both locales, including product copy and landing pages.
- Preserve current theme performance, accessibility, and SEO signals (hreflang, structured data) while adding localization awareness.

## Recommendation Summary
Building a bespoke layer keeps us in control of copy, styling, and future integrations. The trade-off is owning every localization concern—admin UX, caching, QA, and future locale expansion. If we anticipate only EN ↔ ZH and curated content, the custom approach is reasonable; beyond that scope, reevaluating a lightweight framework (e.g., Polylang in headless mode) would lower maintenance. For now we proceed with a custom solution and revisit if the locale list grows.

## Architecture Overview
1. **String Registry**
   - Store canonical strings under `theme/svicloudtvbox-lumen/lang/{en_US,zh_TW}.php` using dot-delimited keys (e.g., `footer.tagline`).
   - Provide `svic_translate( $key, $replacements = [] )` and `svic_rich_text( $key, $context = [] )` helpers with English fallback.
2. **Language State Management**
   - Accept `?lang=zh` query param, persist `svic_lang` cookie, optionally seed from browser language once per session.
   - Hook `determine_locale` so WordPress loads matching locale; call `load_theme_textdomain` for parity with core strings.
3. **Template Integration**
   - Replace `svic_bilingual_span` spans with helpers; expose utilities for WooCommerce templates, menu walkers, JS bundles.
   - Supply a reusable header/footer language switcher component that updates URL (and cookie) while keeping cart/session intact.
4. **Content Workflow**
   - Add admin settings page to manage global strings plus post/product meta fields (`_svic_title_zh`, `_svic_excerpt_zh`, etc.).
   - Build validation hooks ensuring both locales are filled before publish; add migration script to seed existing copy.
5. **Front-End Enhancements**
   - Ensure hreflang links, meta tags, and sitemaps reference both locales.
   - Provide localized data to JS via `wp_localize_script` for dynamic components.

## Implementation Phases
1. **Audit & Modeling (1–2 days)**
   - Inventory existing bilingual spans, hard-coded strings, WooCommerce overrides, and JS copy.
   - Finalize key naming conventions, locale codes, and data schema for post meta.
2. **Infrastructure (3–4 days)**
   - Implement translation registry loader, caching, and helper API.
   - Build language detection middleware (query/cookie) and tie into `determine_locale`.
   - Create admin settings page + developer docs.
3. **Template Refactor (4–6 days)**
   - Systematically swap inline spans and `esc_html__` calls to `svic_translate` keys.
   - Update WooCommerce templates, navigation, footer, and shared components.
   - Adjust JS bundles to read localized payloads.
4. **Dynamic Content & Editor Workflow (3 days)**
   - Introduce bilingual meta boxes for pages/products, extend REST if needed.
   - Write migration script to backfill registry/meta from current English copy.
   - Update Playwright flows for language toggle scenarios.
5. **QA & Rollout (2–3 days)**
   - Browser-language detection and locale persistence tests.
   - Validate hreflang/sitemaps, caching headers, and analytics tagging.
   - Stage deploy, regression list, and FTPS rollout checklist.

## Proposed Translation Key Taxonomy
- **Locales:** primary `en_US`, secondary `zh_TW`. Future locales append as ISO 639-1 + region (e.g., `zh_CN`).
- **File layout:** `theme/svicloudtvbox-lumen/lang/{locale}.php` returning associative arrays.
- **Namespace tiers:**
  - `core.*` for sitewide system copy (e.g., `core.cta.shop_now`).
  - `footer.*`, `header.*` for layout primitives.
  - `home.*`, `compare.*`, `shop.*`, `product.{slug}.*` for page- or SKU-specific copy.
  - `messages.*` for reusable notices (toasts, validation).
- **Array data:** represent compare tables and feature lists as keyed maps, e.g., `compare.table.ram.storage.label`, `compare.table.ram.storage.p10p`, `compare.table.ram.storage.p10s`.
- **Variant suffixes:** use `.html` when markup is required (e.g., line breaks), `.aria` for accessibility labels.
- **Meta fields:** post meta keys follow `_svic_{field}_{locale}`, e.g., `_svic_excerpt_zh_tw`. Registry falls back to English when translation missing.
- **Caching:** compiled registry cached in transients keyed by locale + revision to avoid repeated disk reads.

## Task Backlog

| ID | Category | Task | Deliverable | Est. | Status |
|----|----------|------|-------------|------|--------|
| I18N-01 | Discovery | Inventory all bilingual spans, copy blocks, WooCommerce strings, JS messages | Audit report + string key map draft | 1d | ✅ Completed (2025-04-07) |
| I18N-02 | Discovery | Define key taxonomy, locale codes, and storage schema | Architecture doc addendum | 0.5d | ✅ Completed (2025-04-07) |
| I18N-03 | Infrastructure | Implement translation registry loader + helpers (`svic_translate`, `svic_rich_text`) | PHP service class + unit tests | 1.5d | Todo |
| I18N-04 | Infrastructure | Build language state middleware (query, cookie, browser detect) via `determine_locale` hook | Functions hooked into theme bootstrap | 1d | ✅ Completed (2025-04-07) |
| I18N-05 | Infrastructure | Create admin translations settings page and register language files | Admin UI + options, docs | 1d | Todo |
| I18N-06 | Templates | Replace `svic_bilingual_span` usage across PHP templates with new helpers | Updated templates + regression checklist | 3d | Todo |
| I18N-07 | Templates | Adapt WooCommerce overrides and menus to translation helper | Refreshed WooCommerce templates | 1d | Todo |
| I18N-08 | Templates | Update JS bundles to pull localized strings (via localized script data) | JS modules + smoke tests | 1d | Todo |
| I18N-09 | Content Workflow | Add bilingual meta boxes for posts/products + REST exposure | CPT/panel changes + docs | 1.5d | Todo |
| I18N-10 | Content Workflow | Build migration script to backfill zh meta + registry from existing copy | CLI/one-off script | 0.5d | Todo |
| I18N-11 | QA | Extend Playwright suite with language toggle coverage | New Playwright tests | 1d | Todo |
| I18N-12 | QA | Validate SEO artifacts (hreflang, sitemap, meta tags, analytics) | QA report | 0.5d | Todo |
| I18N-13 | Rollout | Stage deploy + regression checklist + FTPS push | Deployment notes | 1d | Todo |
| I18N-14 | Documentation | Author editor workflow guide + dev contribution guidelines | Markdown docs | 0.5d | Todo |

## Open Questions
- Should we support alternate Chinese variants (Traditional vs Simplified) from the outset?
- Do we require localized slugs/permalinks, or is content-level translation sufficient?
- How should analytics and CRM segment language data (GA events, Klaviyo, etc.)?

## Next Steps
1. Review and approve this plan + backlog.
2. Assign ownership for Discovery tasks (I18N-01/02) and schedule kickoff.
3. Decide whether to pursue automatic browser-language detection or rely solely on explicit user choice.
