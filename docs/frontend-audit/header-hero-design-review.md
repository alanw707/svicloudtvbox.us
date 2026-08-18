# Header + hero design & accessibility review — homepage above-the-fold

Reviewed at `http://svicloud10p.svic.local/` (local fixture with the imported
production menu: 15 top-level items).

Repeatable checks:

```bash
node scripts/audit_header_hero_layout.mjs --label after     # overlap/overflow at 5 viewports
node scripts/audit_header_hero_a11y.mjs --width 1512        # names, focus, contrast, headings
node scripts/audit_header_hero_a11y.mjs --width 1512 --scrolled
node scripts/audit_header_hero_a11y.mjs --width 390
```

Evidence screenshots (gitignored): `.playwright/header-audit/{before,after}-{viewport,header}-<width>.png`
plus `after-header-scrolled-<width>.png`, produced by
`node scripts/capture_header_hero_before_after.mjs`.

## 1. Layout defect (reported screenshot)

| Item | Detail |
| --- | --- |
| Symptom | Logo overlapped `Shop`/`Blog`; `View Cart` overlapped `Order Tracking`; nav row overflowed the viewport (240px at 1280) |
| Cause | `.lumen-nav` (`12-header-base.css`) centred a non-wrapping `inline-flex` list inside a `minmax(0,1fr)` grid track, so content spilled over both neighbouring columns |
| Trigger | Bar was designed for ~7 links; production menu has 15 |
| Fix | Nav list wraps (`12-header-base.css`); `initHeaderNavFit()` measures rows and applies `lumen-header--nav-tiered` (full-width second tier, max 2 rows) or `lumen-header--nav-collapsed` (hamburger + existing dialog); scrolled sticky bar always collapses |

Measured after the fix (`audit_header_hero_layout.mjs`): 0 overlaps, 0 overflow,
no horizontal scrollbar at 1920 / 1512 / 1280 / 1024 / 390.

## 2. Accessibility checklist

Measured at 1512 (top + scrolled), 1280, 1024, 390. Contrast failures flagged by
the CSS heuristic are re-measured pixel-accurately (text hidden, element
screenshot decoded, worst painted pixel behind the glyphs).

| Check | Result | Notes |
| --- | --- | --- |
| Accessible names | PASS | 25 controls at desktop, 7 at mobile, 0 unnamed (logo uses `aria-label` + `.screen-reader-text`; hamburger and lang group are labelled) |
| Keyboard focus visible | PASS | Indicator changes on all 25 controls; first 12 Tab stops all have outline/box-shadow |
| Contrast (WCAG AA) | PASS | 41 text nodes desktop / 24 mobile; lowest measured 4.84:1 (`2026 release`, 11px bold, needs 4.5) |
| Decorative graphics hidden | PASS | `15P` monogram, orbits, hero globes are `aria-hidden`; logo image carries alt text |
| Heading order | PASS | `h1` hero title then `h2` card title; exactly one `h1` |
| Nav dialog | PASS | Opens at desktop widths, 19 links, focus moves into dialog, Esc closes and restores focus to the toggle |

Fixed during the pass:

- `.hero-15p__topline` was `rgba(212,231,251,0.62)` → 4.18:1 over the card glow at
  390px. Now `rgba(226,240,255,0.88)`.
- `.hero-15p__cta` / `.hero-15p__compare` had no hover or focus-visible styling.
  Added hover treatments plus an explicit 2px focus ring.

## 3. Hero + flagship card design review

| # | Observation | Status |
| --- | --- | --- |
| 1 | Wrapped nav rows were centred, leaving an orphan row that read as accidental | Fixed — tier rows are left-aligned, so row 2 continues the list and the first item aligns with the logo |
| 2 | Sticky header reached 168px tall with the tier visible | Fixed — the tier is dropped once scrolled; sticky bar settles at 75px |
| 3 | Card topline (absolute) slid under `SVICLOUD` eyebrow at ≤600px | Fixed — topline type tightened and `.hero-15p__content` padding-top raised to 60px; regression covered by `heroOverlaps` in the layout audit |
| 4 | Card CTAs had no hover/focus affordance | Fixed — see above |
| 5 | Hero left column spacing rhythm | No change needed — uniform 20px desktop / 18px mobile between eyebrow, badge, h1, copy, list, CTA; rating strip intentionally ordered last |
| 6 | Touch/pointer target sizes | No change needed — nav links 39px, lang chips 38px, hero CTAs 52px, card CTA 48px; smallest is the `Compare the lineup` text link at 26px, above the 24px AA minimum |
| 7 | Header container (1422px) is wider than the hero container (1320px), so the logo sits 51px left of the hero content | Deferred — section containers on this page already vary (900–1320px); narrowing the header would shrink the nav row and re-trigger wrapping. Needs a site-wide container decision, out of scope here |
| 8 | Cart is hidden from the mobile top bar (only inside the dialog) | Deferred — pre-existing information architecture, not a regression from this defect; changing mobile commerce IA is out of scope |
| 9 | `[TBC]` chips read as disabled pills | No change — they intentionally mark unconfirmed launch data; copy changes are out of scope |

## 4. Regression status

```bash
python3 scripts/build_css.py --theme svicloudtvbox-lumen
npm run build:js
./scripts/sync_theme_container.sh
PLAYWRIGHT_BASE_URL=http://svicloud10p.svic.local npx playwright test \
  tests/playwright/smoke.spec.ts --project=chromium-desktop -g 'loads / without console errors'
```

- Required homepage smoke test: 1 passed, 0 failures, no console errors.
- Full `chromium-desktop` smoke run: 6 passed / 2 failed. Both failures are
  pre-existing and unrelated to this change — `/product/svicloud-15p/` (product is
  absent from the current local fixture) and `/my-account/` (account form not
  rendered locally). The same suite fails on those specs with this change stashed.
- JavaScript must be rebuilt after editing `assets/js/theme.js`: the site enqueues
  `theme.min.js`, so `npm run build:js` is required alongside the CSS build.
