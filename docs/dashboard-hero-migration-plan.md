# Dashboard Highlight Theme Migration Plan

Author: Codex (acting Sr. Front-End Architect & Visual Designer)
Date: 2025-09-16

## 1. Objective & Scope
- Stand up a new WordPress theme (`svicloudtvbox-lumen`) that encapsulates the dashboard-inspired visual system while preserving the current `svicloudtvbox` theme as a stable fallback.
- Reuse existing copy, CTAs, WooCommerce integrations, and static metrics; the redesign focuses on styling/markup, not real-time data feeds.
- Establish a scalable token + partial CSS architecture from day one so additional pages adopt the new aesthetic quickly.

## 2. Rationale for Creating a New Theme
- **Risk isolation:** Running the new design in a separate theme allows full QA and stakeholder demos without disrupting the production theme.
- **Simpler rollback:** Reverting only requires switching active themes rather than unwinding a large CSS/markup diff.
- **Parallel development:** Enables incremental porting of templates (homepage, compare page, PDPs) while legacy theme continues to receive hotfixes if needed.

## 3. Current State Snapshot (Legacy Theme)
- Theme root: `theme/svicloudtvbox/` with PHP templates (front page, WooCommerce overrides), assets under `assets/css`, `assets/js`.
- CSS pipeline: partials in `assets/css/parts/*.css`, bundled into `style.css` via `scripts/build_css.py`.
- JavaScript: `assets/js/theme.js` handles dark-mode toggle, carousel, concierge accordion.
- Hero markup uses `hero-modern` classes tightly coupled to existing CSS.

## 4. Target Architecture for New Theme
1. **Directory Setup**
   - Create `theme/svicloudtvbox-lumen/` mirroring WordPress expectations (`style.css`, `functions.php`, template files, `assets/` subdirectories).
   - Add `style.css` header metadata with unique `Theme Name` so WordPress recognises it separately.

2. **Design Tokens & CSS Pipeline**
   - Reuse partial workflow: `assets/css/parts/00-tokens.css`, `32-hero-dashboard.css`, etc., with the build script extended (or copied) to output `assets/css/style.css` for the new theme.
   - Tokens should introduce overlay surfaces, accent hues, radii, and shadow stacks that underpin the dashboard visuals.

3. **Template Strategy**
   - Start from copied templates (`header.php`, `footer.php`, `front-page.php`) and progressively refactor markup to the new component classes (`hero-dashboard`, glass cards, etc.).
   - Maintain PHP helpers (`svic_` functions) by requiring shared utilities or extracting them into a common include if necessary.

4. **Assets & Media**
   - Store redesigned hero imagery under `assets/images/hero/` (WebP + PNG fallback) sized for retina.
   - Keep SVG icon set consistent to avoid double maintenance.

5. **JavaScript Considerations**
   - Duplicate `assets/js/theme.js` as a base, pruning unused hero-specific logic and ensuring new selectors are referenced. Keep dependency-free philosophy.

## 5. Implementation Phases & Task List

### Phase 0 – Discovery & Alignment
- [ ] Catalogue templates from legacy theme that must ship in v1 (front page, header/footer, WooCommerce PDP overrides).
- [ ] Confirm copy, CTA targets, and imagery remain unchanged for launch.
- [ ] Inventory shared PHP helpers to determine if any should be extracted into a `includes/` directory consumable by both themes.

### Phase 1 – Theme Scaffolding
- [ ] Duplicate legacy theme into `svicloudtvbox-lumen` and update `style.css` metadata.
- [ ] Strip non-essential legacy CSS/JS from the new theme to reduce debt (retain tokens, utilities, layout primitives).
- [ ] Ensure build scripts (`scripts/build_css.py`) can target the new theme path or create a dedicated script variant.
- [ ] Register theme support features (menus, thumbnails, WooCommerce) in the new `functions.php`.

### Phase 2 – Dashboard Hero Integration
- [ ] Add new tokens to `assets/css/parts/00-tokens.css` and document them.
- [ ] Create `assets/css/parts/32-hero-dashboard.css` with responsive rules and reduced-motion handling.
- [ ] Replace hero markup in `front-page.php` with the dashboard layout (keeping existing copy/metrics/static data).
- [ ] Drop in optimised product photo assets and reference them via `get_template_directory_uri()`.

### Phase 3 – Page-Level Styling Alignment
- [x] Harmonise adjacent sections (credibility bar, feature panels, concierge split) with new spacing, gradients, and card treatments.
- [x] Update global utilities (`90-utilities.css`) to support glass/blur elements reused across sections.
- [x] Verify JavaScript hooks on the homepage reflect new IDs/classes (e.g., `#hero-dashboard`).

### Phase 4 – Cross-Template Porting (Optional for Initial Launch)
- [x] Port `/compare/` template into new theme using dashboard visual language.
- [x] Update WooCommerce PDP hero banners/cards for stylistic consistency.
- [x] Establish shared helpers/components if repetition grows (e.g., `shared/helpers-svic.php`).

### Phase 5 – Quality Assurance & Deployment
- [x] Regenerate CSS via updated build script; confirm only the new theme’s bundle changes.
- [ ] Smoke-test locally across key breakpoints (360, 768, 1280, 1600).
- [ ] Validate WooCommerce interactions on `/product/svicloud-10p-plus/` and `/product/svicloud-10s/` using the new theme.
- [ ] Capture before/after screenshots (desktop + mobile) and note manual QA steps for PR.
- [ ] Stage deployment: activate `svicloudtvbox-lumen` on staging, run Playwright suite post-deploy, gather stakeholder approvals, then schedule production go-live.

## 6. Risk & Mitigation
- **Theme Divergence:** Two themes can drift apart; mitigate by centralising shared helpers and documenting differences.
- **Build Script Duplication:** Ensure CSS/JS build tooling handles both themes to avoid manual sync errors.
- **Asset Weight:** Monitor hero imagery sizes; enforce WebP + lazy loading.
- **Plugin Compatibility:** Verify WooCommerce and other plugins load template overrides from the new theme correctly.

## 7. Deliverables Checklist
- [ ] New theme directory registered and installable in WordPress.
- [ ] Updated CSS token set + hero partial in the new theme.
- [ ] Dashboard hero implemented with static content parity.
- [ ] Updated supporting sections styled to match hero (at least for homepage launch slice).
- [ ] Build tooling updated to compile new theme assets.
- [ ] QA evidence captured; Playwright checks executed post-deploy.

## 8. Future Enhancements (Post-Migration)
- Extend the dashboard visual language to all remaining templates (compare page, PDPs, blog) within the new theme.
- Explore shared component library or pattern library for designers/developers to reuse hero/cta/card patterns.
- Revisit dark-mode animations and custom property toggles once the broader site has adopted the new aesthetic.
