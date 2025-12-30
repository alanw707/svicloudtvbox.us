# Tech-Spec: Traffic & Conversion Lift for 10P+ — Phase 2 (Schema Hygiene + Content Ops)

**Created:** 2025-12-15
**Status:** Implemented (Pending QA)

## Overview

### Problem Statement

Phase 1 shipped multilingual (EN/zh-TW/zh-CN) conversion copy + internal links + FAQ content across core templates (front page, compare, FAQ, and the 10P+ PDP). However, the current implementation still has two operational risks that can blunt organic gains:

1) **Schema duplication/conflicts**: templates emit `FAQPage` JSON-LD directly, while Rank Math is also active and already used as the canonical schema/meta source. This can lead to duplicate/competing structured data output, inconsistent schema graphs, and harder troubleshooting.
2) **Content ops drift**: the blog localization system relies on a mix of (a) translation registry keys (`blog.posts.{slug}.title`) and (b) per-post localized meta (`_svic_*`) for excerpts/bodies. As new posts ship, it’s easy to forget required keys/meta, causing zh pages to silently fall back to English titles/snippets and weakening SEO intent.

### Solution

Ship a Phase 2 hardening pass focused on:

- **Centralizing JSON-LD emission** (starting with FAQPage) so the site has a single structured-data pipeline:
  - If Rank Math is active, inject nodes into Rank Math’s JSON-LD graph via `rank_math/json_ld`.
  - If Rank Math is not active, emit JSON-LD once via theme hooks (no duplicates).
- **Adding a “translation coverage” guardrail** for blog posts so missing locale titles/snippets are detectable before deploy:
  - Provide a lightweight script that reports missing `blog.posts.{slug}.title` keys and missing `_svic_*` localized meta (where expected) based on exported markdown and/or WP content.

### Scope (In/Out)

**In scope**
- Implement a theme-level JSON-LD registry and a single emitter (Rank Math filter when present, otherwise `wp_head`).
- Migrate FAQ JSON-LD generation in:
  - `theme/svicloudtvbox-lumen/front-page.php`
  - `theme/svicloudtvbox-lumen/page-faq.php`
  - `theme/svicloudtvbox-lumen/woocommerce/single-product.php`
  to the centralized registry/emitter.
- Add a reporting script for blog translation coverage (titles + optional meta fields) and document the workflow.

**Out of scope**
- Rewriting all schema output site-wide (Product/Organization/ItemList graphs are already in place and can remain as-is for now).
- Generating new blog content or translating post bodies automatically.
- Changing Rank Math settings in WP admin (this spec stays code-only; admin configuration can be documented as follow-up).

## Context for Development

### Codebase Patterns

- **Current implementation status (as of 2025-12-15)**
  - FAQPage JSON-LD is currently echoed directly from templates:
    - `theme/svicloudtvbox-lumen/front-page.php`
    - `theme/svicloudtvbox-lumen/page-faq.php`
    - `theme/svicloudtvbox-lumen/woocommerce/single-product.php`
  - Rank Math integration already exists via `rank_math/json_ld` filters in `theme/svicloudtvbox-lumen/functions.php` (e.g., site navigation schema injection and Organization node enhancements), but FAQ nodes are not currently injected into Rank Math’s graph.
  - Blog localization runtime is already wired:
    - `svic_post_title()` → `blog.posts.{slug}.title` fallback
    - `svic_post_locale_meta()` → `_svic_{field}_{locale}` meta fields
  - Blog tooling already exists for content ops, but not coverage reporting:
    - Export: `scripts/export_posts_markdown.py`
    - Translate + update meta: `scripts/translate_and_update_posts.py`

- Theme: `theme/svicloudtvbox-lumen/`
- Locale routing:
  - `/zh/` → `zh_TW`
  - `/zh-cn/` → `zh_CN`
  - handled by `theme/svicloudtvbox-lumen/inc/class-svic-locale-resolver.php`
- Translation system:
  - `SVIC_Translator` loads registries from `theme/svicloudtvbox-lumen/lang/en_US.php`, `zh_TW.php`, `zh_CN.php`
  - `zh_CN.php` includes `zh_TW.php` and applies overrides
- Localized URL helper: `svic_url_with_lang()` in `theme/svicloudtvbox-lumen/inc/helpers-svic.php`
- Blog localization:
  - `svic_post_title()` falls back to `blog.posts.{slug}.title`
  - `svic_post_locale_meta()` reads `_svic_{field}_{locale}` post meta (title/description/content)
  - Blog templates already consume these helpers:
    - index: `theme/svicloudtvbox-lumen/home.php`
    - single: `theme/svicloudtvbox-lumen/single.php`
- Rank Math integration already exists in `theme/svicloudtvbox-lumen/functions.php` (canonical/hreflang filters + JSON-LD graph filters).

### Files to Reference

- FAQ JSON-LD currently emitted inline:
  - `theme/svicloudtvbox-lumen/front-page.php`
  - `theme/svicloudtvbox-lumen/page-faq.php`
  - `theme/svicloudtvbox-lumen/woocommerce/single-product.php`
- Rank Math JSON-LD integration points:
  - `theme/svicloudtvbox-lumen/functions.php` (look for `rank_math/json_ld` filters)
- Helpers/localization:
  - `theme/svicloudtvbox-lumen/inc/helpers-svic.php`
  - `theme/svicloudtvbox-lumen/inc/class-svic-translator.php`
  - `theme/svicloudtvbox-lumen/inc/class-svic-locale-resolver.php`
- Blog export/update tooling:
  - `scripts/export_posts_markdown.py`
  - `scripts/translate_and_update_posts.py`
  - `docs/blog/`, `docs/blog/zh/`, `docs/blog/zh-cn/` (export targets)

### Technical Decisions

1) **Single source of truth for structured data**
   - If Rank Math is active, schema nodes should be injected into Rank Math’s graph (avoid template `echo '<script …>'` duplication).
2) **Keep template code simple**
   - Templates should register schema “facts” (e.g., FAQ Q/A pairs) and let the emitter decide where/how to output.
3) **Do not introduce dependencies**
   - Use existing WordPress hooks + existing theme patterns.
4) **Guardrails over magic**
   - Translation coverage reporting should be non-blocking by default (report-only), with an option to fail CI/deploy later if desired.

## Implementation Plan

### Tasks

- [x] Task 1: Add a JSON-LD registry + emitter
  - Add helper functions to register schema nodes during template render, e.g.:
    - `svic_schema_register(array $node): void`
    - `svic_schema_all(): array`
  - Implement a single output path:
    - If Rank Math is active: append registered nodes to the graph in a `rank_math/json_ld` filter.
    - Else: emit a single `<script type="application/ld+json">` in `wp_head` containing either an `@graph` or a single node (depending on count).

- [x] Task 2: Migrate FAQPage schema to the registry (no more inline scripts)
  - Update templates to stop echoing JSON-LD directly and instead register:
    - `@type: FAQPage`
    - `mainEntity`: list of Question/Answer derived from the same translation keys already used for rendering.
  - Target files:
    - `theme/svicloudtvbox-lumen/front-page.php`
    - `theme/svicloudtvbox-lumen/page-faq.php`
    - `theme/svicloudtvbox-lumen/woocommerce/single-product.php`
  - Ensure each page registers at most one FAQPage node (even if multiple FAQ sections exist).

- [x] Task 3: Add a blog translation coverage report
  - Add a script (Python or PHP) that outputs:
    - posts present in `docs/blog/*.md` that are missing `blog.posts.{slug}.title` in `en_US.php`
    - posts present in `docs/blog/zh/*.md` and `docs/blog/zh-cn/*.md` that are missing `blog.posts.{slug}.title` in `zh_TW.php` / `zh_CN.php`
    - optional: report missing `_svic_description_*` and `_svic_content_*` meta if a local WP DB is available (otherwise skip)
  - Document a recommended workflow:
    1) export markdown (`scripts/export_posts_markdown.py`)
    2) run translation coverage report
    3) patch lang registries / post meta as needed

- [ ] Task 4: Manual QA pass on schema output (desktop + mobile widths)
  - Confirm exactly one FAQPage JSON-LD per relevant page.
  - Confirm Rank Math remains the canonical schema source (Product schema, OG/meta) and no duplicate tags appear.

### Acceptance Criteria

- [ ] AC 1: Given Rank Math active, when loading `/product/svicloud-10p-plus/`, `/faq/`, and `/`, then FAQ structured data is present exactly once per page and is part of Rank Math’s emitted JSON-LD graph.
- [x] AC 2: Given Rank Math inactive, when loading the same pages, then FAQ structured data is still present exactly once per page (theme emits it) and passes JSON validation.
- [x] AC 3: Given a published blog post in EN/zh/zh-cn exports, when running the translation coverage report, then missing title keys are reported with enough detail to patch the correct lang file.
- [ ] AC 4: No console errors are introduced on `/`, `/compare/`, `/faq/`, `/contact/`, `/blog/`, and `/product/svicloud-10p-plus/` at common desktop/mobile widths.

## Additional Context

### Dependencies

- WordPress + WooCommerce
- Rank Math (optional but expected active in production)

### Testing Strategy

- Manual:
  - View-source or DevTools Elements: verify JSON-LD count and content.
  - Check Rank Math schema output remains intact and canonical.
- Scripted:
  - Run translation coverage report locally.

### Notes

- Follow repo workflow for CSS changes (if any emerge during implementation):
  - edit partials under `theme/svicloudtvbox-lumen/assets/css/parts/`
  - rebuild via `python3 scripts/build_css.py --theme svicloudtvbox-lumen`
  - sync via `./scripts/sync_theme_container.sh`
