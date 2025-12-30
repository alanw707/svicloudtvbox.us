# Tech-Spec: Standardize Mobile Action Button Groups

**Created:** 2025-12-14
**Status:** Completed

## Overview

### Problem Statement
Several pages implement “action groups” (clusters of CTA buttons/links) using one-off container styles. Some containers explicitly set `.lumen-pill { width: 100%; }`, which looks fine in 2-column desktop/grid layouts but causes unintended full-bleed / oversized CTAs when the layout collapses to a single column on mobile.

This surfaced on the homepage `frontpage-traffic` section where the primary “Shop 10P+” CTA appeared overly wide.

### Solution
Create a reusable, theme-wide “action group” utility that standardizes:
- layout (row vs column stack on mobile),
- alignment (centered or left),
- button sizing (full-bleed vs capped width),
- and consistent spacing.

Then, update templates to opt into this shared utility class instead of relying on bespoke per-section rules.

### Scope (In/Out)
**In scope**
- Introduce new reusable CSS utility classes for action groups in global styles (bundled into `style.css`).
- Adopt the utility across key templates/sections using `.lumen-pill` CTAs.
- Remove or neutralize per-section “force full width” rules where they conflict with the new standard.
- Add Playwright regression checks for mobile CTA widths/alignment on a small set of critical URLs.

**Out of scope**
- Redesign of button visuals (colors, shadows) beyond layout/width/alignment.
- Refactoring non-CTA buttons (e.g., cart quantity steppers) unless they share the same containers.

## Context for Development

### Codebase Patterns
- CSS is authored in partials under `theme/svicloudtvbox-lumen/assets/css/parts/` and bundled via `scripts/build_css.py` into route-specific outputs.
- Global utilities and tokens live in `00-tokens.css` and `90-utilities.css` (bundled into `assets/css/style.css`).
- Route sections (homepage, compare, etc.) have their own partials; avoid duplicating action-group logic per section.

### Files to Reference
- Base button styles: `theme/svicloudtvbox-lumen/assets/css/parts/13-header-actions.css` (`.lumen-pill`).
- Homepage section that triggered the issue: `theme/svicloudtvbox-lumen/assets/css/parts/41-frontpage-helpers.css` (`.frontpage-traffic__links`).
- Other action-group containers (examples):
  - `theme/svicloudtvbox-lumen/page-compare.php` (`.compare-hero__actions`, `.compare-traffic__links`, `.compare-final-cta__actions`)
  - `theme/svicloudtvbox-lumen/page-faq.php` (`.faq-hero__actions`, `.faq-intent__links`)
  - `theme/svicloudtvbox-lumen/page-contact.php` (`.contact-hero__actions`, `.contact-intent__links`)
  - `theme/svicloudtvbox-lumen/page-support.php` (`.support-intent__links`)
  - `theme/svicloudtvbox-lumen/woocommerce/single-product.php` (`.product-traffic__links`)

### Technical Decisions
1. **Opt-in via a shared container class**, not blanket selectors:
   - Avoid applying rules to every `*__actions/*__links` container globally because some sections legitimately want full-bleed buttons on mobile.
2. **Add a single CSS variable for mobile CTA cap**:
   - `--lumen-action-max-width-mobile` (default: `312px`), configurable in one place.
3. **Default mobile behavior is capped + centered**:
   - Any container using `.lumen-action-group` will, by default, stack on mobile and cap CTA width.
4. **Provide explicit modifiers** so each section can choose behavior when needed:
   - `--fullbleed-mobile` for intentionally full-width mobile CTAs
   - row/wrap on desktop

## Implementation Plan

### Tasks
- [x] Task 1: Add global action-group utility classes
  - Add to `theme/svicloudtvbox-lumen/assets/css/parts/90-utilities.css`:
    - `:root { --lumen-action-max-width-mobile: 312px; }` (or place in `00-tokens.css` if preferred)
    - `.lumen-action-group` base layout (flex or grid-agnostic)
    - `.lumen-action-group--center` (center alignment)
    - `.lumen-action-group--stack-mobile` (mobile column stacking)
    - `.lumen-action-group--capped-mobile` (applies `width:100%` + `max-width: var(--lumen-action-max-width-mobile)` to direct children that are CTAs)
    - `.lumen-action-group--fullbleed-mobile` (explicitly keeps `width:100%` without max-width)
  - Ensure selectors target common CTA elements:
    - direct child `a.lumen-pill`, `button.lumen-pill`, and optional `.frontpage-traffic__textlink`-style CTAs.

- [x] Task 2: Adopt the utility in templates (markup-first, minimal CSS overrides)
  - Update the following containers to include action-group classes:
    - `theme/svicloudtvbox-lumen/front-page.php`: `.frontpage-traffic__links`
    - `theme/svicloudtvbox-lumen/page-compare.php`: `.compare-hero__actions`, `.compare-traffic__links`, `.compare-final-cta__actions`
    - `theme/svicloudtvbox-lumen/page-faq.php`: `.faq-hero__actions`, `.faq-intent__links`
    - `theme/svicloudtvbox-lumen/page-contact.php`: `.contact-hero__actions`, `.contact-intent__links`
    - `theme/svicloudtvbox-lumen/page-support.php`: `.support-intent__links`
    - `theme/svicloudtvbox-lumen/page-return-policy.php` + `theme/svicloudtvbox-lumen/page-legal-disclaimer.php`: `.policy-support__actions`
    - `theme/svicloudtvbox-lumen/woocommerce/single-product.php`: `.product-traffic__links`
  - Decide per container whether it should be capped or full-bleed on mobile by applying:
    - `lumen-action-group lumen-action-group--stack-mobile lumen-action-group--capped-mobile lumen-action-group--center`
    - or swap `--capped-mobile` with `--fullbleed-mobile`.

- [x] Task 3: Remove/adjust conflicting per-section width rules
  - Update page-specific partials so they no longer force `width: 100%` in a way that overrides the new standard.
  - Example: `theme/svicloudtvbox-lumen/assets/css/parts/41-frontpage-helpers.css` should stop being responsible for CTA sizing and defer to `.lumen-action-group` (keep only layout unique to the section).

- [x] Task 4: Add Playwright regression checks (recommended)
  - Add a test that loads key URLs at a mobile viewport and asserts that the primary CTA inside each action-group container is:
    - centered if `--center` is used,
    - and `<= --lumen-action-max-width-mobile` when `--capped-mobile` is used.
  - Suggested URLs:
    - `/` (frontpage traffic)
    - `/compare/`
    - `/faq/`
    - `/contact/`
    - `/product/svicloud-10p-plus/`

### Acceptance Criteria
- [x] AC 1: Given a mobile viewport (≤ 720px), when a container has `lumen-action-group--capped-mobile`, then CTA buttons inside it do not exceed `--lumen-action-max-width-mobile` and remain tappable without horizontal overflow.
- [x] AC 2: Given a mobile viewport (≤ 720px), when a container has `lumen-action-group--stack-mobile`, then multiple CTAs stack vertically with consistent spacing and alignment.
- [x] AC 3: Given desktop viewport (≥ 960px), action groups preserve their existing multi-column / inline layouts (no regressions).
- [x] AC 4: All updated templates rebuild cleanly via `python3 scripts/build_css.py --theme svicloudtvbox-lumen` and no generated CSS files are manually edited.

## Additional Context

### Dependencies
- None beyond existing bundling scripts and Playwright setup (if adding tests).

### Testing Strategy
- Manual QA: verify action groups on `/`, `/compare/`, `/faq/`, `/contact/`, and a PDP at common mobile widths (390/414) and desktop.
- Automated QA (recommended): Playwright width assertions for selected CTAs.

### Notes
- This spec intentionally prefers “opt-in” action-group classes to avoid accidentally changing header buttons, WooCommerce checkout buttons, or other places where full-width CTAs are desired.
