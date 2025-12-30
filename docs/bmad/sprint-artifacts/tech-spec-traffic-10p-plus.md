# Tech-Spec: Traffic & Conversion Lift for 10P+ (EN/zh-TW/zh-CN)

**Created:** 2025-12-07
**Status:** Completed

## Overview

### Problem Statement

Organic and social traffic to svicloudtvbox.us is limited, reducing exposure and sales of the flagship 10P+ product for US-based Chinese/Asian customers.

### Solution

Implement multilingual (EN, zh-TW, zh-CN) SEO/content improvements focused on the 10P+ product, comparison, supporting pages, and blog posts. Strengthen internal links, metadata, and page speed signals; add structured content (FAQ/schema) and social-ready snippets; ensure Rank Math handles meta while theme supports bilingual routing and content blocks.

### Scope (In/Out)

- In: front page, 10P+ product page, compare page, FAQ/support/contact, blog/landing content sections, internal linking, multilingual content blocks, social preview readiness, blog post translations (EN/zh-TW/zh-CN).
- Out: New backend services or headless re-platforming.

## Context for Development

### Codebase Patterns

- WP theme `theme/svicloudtvbox-lumen`; multilingual via `lang/en_US.php`, `lang/zh_TW.php`, `lang/zh_CN.php` (zh_CN extends zh_TW), `SVIC_Translator`, and `SVIC_Locale_Resolver` for `/zh/` and `/zh-cn/` paths.
- Rank Math active → theme meta suppressed when plugin detected; rely on Rank Math for meta/OG/schema.
- CSS built from partials under `assets/css/parts/` per AGENTS.md; rebuild via `python3 scripts/build_css.py --theme svicloudtvbox-lumen` then `./scripts/sync_theme_container.sh`.
- Key templates: `front-page.php`, `page-compare.php`, `home.php`, `header.php`/`footer.php`, WooCommerce `woocommerce/archive-product.php`, `woocommerce/single-product.php`, static pages (faq/contact/guides/etc.).
- JS: `assets/js/theme.js` handles lang toggle, smooth scroll, animations; keep dependency-free.
- Blog rendering: `home.php` (index) and `single.php` use `svic_post_title()` to localize titles via `blog.posts.{slug}.title` keys and optional Polylang default-language mapping; content body is per-post in WP (no auto-translation). Categories localized via `blog.categories.*` keys.
- Locale/resolution: `SVIC_Locale_Resolver` handles cookies/query/path; `svic_url_with_lang()` should wrap internal links; route aliases defined in `svic_route_alias_definitions()`.

### Files to Reference

- `theme/svicloudtvbox-lumen/front-page.php` and `assets/css/parts/` for hero/sections.
- `theme/svicloudtvbox-lumen/page-compare.php` and `assets/css/parts/compare-*` for comparison content.
- `theme/svicloudtvbox-lumen/woocommerce/single-product.php` for 10P+ PDP content blocks.
- Blog: `theme/svicloudtvbox-lumen/home.php`, `single.php`, `inc/helpers-svic.php` (post titles/categories, lang-aware links), `lang/{en_US,zh_TW,zh_CN}.php` for `blog.*` keys.
- Locale/translation core: `inc/class-svic-translator.php`, `inc/class-svic-locale-resolver.php`, `inc/helpers-svic.php` (`svic_url_with_lang`, `svic_translate_*`).
- SEO/plugin: Rank Math settings (in WP admin), ensure theme doesn’t conflict with Rank Math output.

### Technical Decisions

- Keep meta/OG/schema under Rank Math; avoid theme-level overrides that conflict when plugin active.
- Use translator keys for UI copy; add blog title keys under `blog.posts.{slug}.title` for EN/zh-TW/zh-CN. Body translation remains per-post in WP (or via Polylang mapping) — ensure localized posts exist.
- Prefer internal links using localized URLs via `svic_url_with_lang` and existing nav helpers; respect `/zh/` vs `/zh-cn/` prefixes.
- Keep assets build chain intact; edit partials, rebuild bundles, sync container per AGENTS.md.

## Implementation Plan

### Tasks

- [x] Audit current 10P+ PDP, front page, compare page for multilingual copy gaps and internal links; list needed copy blocks (EN/zh-TW/zh-CN).
- [x] Add multilingual SEO copy blocks for 10P+: value props, US-based concierge, warranty, shipping, FAQ snippets; wire into front page + PDP + compare.
- [x] Strengthen internal linking: front page → 10P+/compare/FAQ/contact; PDP → FAQ/support/contact; compare → PDPs/contact; blog CTA → compare/support.
- [x] Add FAQ content section (Rank Math FAQ schema or markup-compatible) on PDP/FAQ page with EN/zh entries targeting search intents.
- [x] Blog translations: inventory posts/slugs, ensure EN/zh-TW/zh-CN bodies exist (per-post translations or Polylang mappings); add/update `blog.posts.{slug}.title` keys in all lang files; add zh_CN overrides where wording differs.
- [x] Verify categories/taxonomy labels localized via `blog.categories.*`; add missing keys as needed.
- [x] Ensure hreflang/canonical continue to use locale resolver; confirm Rank Math coexists (no duplicate meta output); verify `svic_url_with_lang` wrapping for blog links.
- [x] Performance hygiene: image alt text (already handled), hero preload only on home, keep JS dependency-free; avoid regressions.
- [x] Update language files with new strings; verify zh coverage for new blocks.
- [x] Build CSS bundles and sync to container after style changes.

#### Review Follow-ups (AI)
- [x] [AI-Review][High] Implement the multilingual 10P+ value props and internal links on front page, PDP, compare, FAQ/support/contact per ACs (touch `front-page.php`, `page-compare.php`, Woo PDP templates, nav links, translator keys).
- [x] [AI-Review][High] Localize blog single hero title/excerpt using post meta helpers (`svic_post_title`, `svic_post_locale_meta`) in `theme/svicloudtvbox-lumen/single.php` so zh visitors see translated titles/teasers.
- [x] [AI-Review][High] Emit FAQ content/schema on FAQ page and PDP using new translations; ensure Rank Math FAQ compatibility (e.g., `page-faq.php`, PDP FAQ block).
- [x] [AI-Review][Medium] Document actual modified/added files in Dev Agent File List/Change Log to match git changes (blog markdown exports, scripts, theme PHP/lang updates).
- [x] [AI-Review][Low] Rebuild CSS/JS bundles and sync theme; record the build/sync commands executed.

### Acceptance Criteria

- [ ] Multilingual (EN/zh-TW/zh-CN) copy added for 10P+ value props, FAQ snippets, CTA blocks on front page, PDP, compare, and relevant blog CTAs; visible and styled appropriately.
- [ ] Blog posts (target list) have EN/zh-TW/zh-CN bodies published or mapped; titles localized via `blog.posts.{slug}.title` keys; categories localized; zh_CN overrides applied where wording differs.
- [ ] Internal links present: front page → 10P+, compare, FAQ, contact; PDP/compare/blog → FAQ/contact/support; localized links resolve correctly with `svic_url_with_lang` and path prefixes.
- [ ] Rank Math remains the source of meta/OG/schema; no duplicate head tags from theme; canonical/hreflang intact via locale resolver.
- [ ] FAQ content eligible for Rank Math FAQ schema (structure compatible) with EN/zh entries targeting likely queries (shipping, warranty, setup, US support).
- [ ] CSS/JS build passes (bundled assets rebuilt) and theme synced to container; no console errors on home/PDP/compare/blog at desktop/mobile widths.

## Additional Context

### Dependencies

- Rank Math plugin active for SEO/meta/schema.
- Existing translator and locale resolver for bilingual routing.

### Testing Strategy

- Manual QA: front page, 10P+ PDP, compare page, blog index/single—verify EN/zh-TW/zh-CN toggles, internal links, FAQ visibility, no duplicate head meta, console clean (desktop + mobile widths).
- Validate localized URLs generated via `svic_url_with_lang` for new links.
- Spot-check zh_CN route prefix (`/zh-cn/`) and zh_TW (`/zh/`) paths; ensure translations present and readable.

### Notes

- Target audience: US-based Chinese/Asian customers; prioritize zh copy quality. If time-constrained, ship zh-first with clear EN equivalents.

### Dev Agent Record

**File List (current work)**
- theme/svicloudtvbox-lumen/single.php
- theme/svicloudtvbox-lumen/woocommerce/single-product.php
- theme/svicloudtvbox-lumen/lang/en_US.php
- theme/svicloudtvbox-lumen/lang/zh_TW.php
- theme/svicloudtvbox-lumen/lang/zh_CN.php
- theme/svicloudtvbox-lumen/home.php
- docs/blog/* (EN), docs/blog/zh/*, docs/blog/zh-cn/* (markdown exports)
- scripts/translate_and_update_posts.py, scripts/export_posts_markdown.py

**Change Log (current work)**
- Localize blog single titles/excerpts using meta helpers; add localized post content plumbing.
- Add FAQ translations (EN/zh-TW/zh-CN) and wire FAQ schema on PDP; ensure FAQ page uses localized Q/A with JSON-LD.
- Add localized blog index CTA text link to contact.
- Populate blog post translation meta/export markdown; add translation/update helper scripts.
