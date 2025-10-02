# CSS Bundle & Build System Cleanup Report

Date: 2025-10-02
Author: Automated analysis (GitHub Copilot CLI)

## Executive Summary
The modular CSS refactor is internally consistent: every partial referenced in `bundles.json` exists, and there are no orphan or missing files. The primary cleanup opportunity is removal of the legacy `10-header.css`, which is no longer referenced by the current header markup (now using the `lumen-*` namespace). Keeping it inflates the global `style.css` bundle with unused rules.

## Findings Detail

### 1. Bundle ↔ Part Consistency
- Referenced parts (bundles.json): **38**
- Existing partial files: **38**
- Missing referenced parts: **0**
- Orphan (unreferenced) parts: **0**

Conclusion: `bundles.json` accurately mirrors the `assets/css/parts/` directory.

### 2. Legacy vs. New Header System
- New header partials: `12-header-base.css`, `13-header-actions.css`, `14-header-mobile.css`, `15-header-motion.css`.
- Current markup (`header.php`) uses classes: `lumen-header`, `lumen-nav`, `lumen-mobile-nav`, etc.
- Legacy partial `10-header.css` defines `.site-header`, `.primary-nav`, `.mobile-menu-toggle`, `.site-logo`, and `body.nav-open` toggles.
- No PHP templates or JS reference these legacy selectors.
- JavaScript toggles a body class `lumen-nav-open`; the legacy `nav-open` variant is obsolete.

Risk of removing `10-header.css`: **Low** (pending confirmation Playwright tests do not target legacy selectors).

### 3. Dead CSS Impact
While small in size, `10-header.css` adds unused rules to `style.css` (post-minification overhead). Removing it trims CSS payload and reduces mental surface area.

### 4. JavaScript Consistency
- Duplicate definition line of `const bodyClass = 'lumen-nav-open';` appears twice in `theme.js` (line ~239). Not harmful, but can be consolidated for clarity.
- No references to `nav-open` (legacy) found—confirms divergence.

### 5. Ordering & Namespacing
- Numeric ordering cleanly layers tokens → header → navigation → hero → sections → footer → utilities.
- After removing `10-header.css`, the header block (12–15) remains contiguous and conceptually grouped.

### 6. build_css.py Health Check
- Script intact; `render_bundle` present and functional.
- No contamination with deploy/zip code.
- Minifier strategy acceptable for current scope.

### 7. Additional Minor Opportunities
- Consider documenting the deprecation of legacy header in existing `css-refactor-plan.md` or linking to this report.
- Potential future consolidation: shared certification/badge/list styles across about + front page (already noted in prior plan).

## Recommended Actions (Phased)
| Phase | Action | Rationale | Risk |
|-------|--------|-----------|------|
| 1 | Remove `10-header.css` from `style` bundle (comment out or delete line in `bundles.json`) | Stops shipping dead CSS | Low |
| 2 | Rebuild CSS (`python3 scripts/build_css.py --theme svicloudtvbox-lumen`) & run smoke + Playwright tests | Validate no regression | Low |
| 3 | Delete `assets/css/parts/10-header.css` after validation | Permanently shrinks codebase | Low |
| 4 | Remove duplicate `bodyClass` line in `theme.js` | Minor clarity improvement | Very low |
| 5 | Update docs (`css-refactor-plan.md`) noting legacy header removal | Knowledge preservation | None |

## Rollback Plan
If unexpected styling regressions appear after removal:
1. Re-add `10-header.css` entry in `bundles.json`.
2. Restore file from Git history (`git checkout HEAD~1 -- path/to/10-header.css`).
3. Rebuild bundles and redeploy.

## Verification Checklist After Removal
- Header renders with correct gradient, transparency, and scroll state.
- Mobile toggle (hamburger) opens/closes with `lumen-nav-open` class behavior.
- No console errors or missing class references.
- Lighthouse / performance diff shows slight CSS reduction (optional).
- Playwright scripts (if asserting header elements) still pass.

## Decision Needed
Proceed with Phase 1 (removal from bundle) now, or schedule with other CSS changes? Provide confirmation and the change can be committed in a minimal diff.

---
Generated automatically. Modify or extend as needed for PR inclusion.
