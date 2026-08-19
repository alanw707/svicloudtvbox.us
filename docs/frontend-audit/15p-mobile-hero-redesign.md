# SVICLOUD 15P mobile hero redesign

## Scope

The 15P homepage visual card keeps its product splash image visible on mobile, but the image is now a separate media block instead of an absolutely positioned layer behind the card copy. Desktop behavior remains unchanged. No production deployment was performed for this redesign.

## Sources

- Markup: `theme/svicloudtvbox-lumen/front-page.php` (`.hero-15p`, `.hero-15p__image`, `.hero-15p__topline`, `.hero-15p__content`)
- Responsive CSS: `theme/svicloudtvbox-lumen/assets/css/parts/32b-15p-launch-redesign.css`
- Bundle: `theme/svicloudtvbox-lumen/assets/css/bundles.json` (`front-page` includes `32b-15p-launch-redesign.css`)
- Generated output rebuilt with `python3 scripts/build_css.py --bundle front-page`

## Behavior evidence

- Baseline: `test-results/15p-mobile-hero/before-report.json`, `before-mobile-390.png`, `before-desktop-1512.png`
- Final: `test-results/15p-mobile-hero/after-report.json`, `after-hero-mobile.png`, `after-hero-desktop.png`
- Locale screenshots: `after-hero-en.png`, `after-hero-zh-TW.png`, `after-hero-zh-CN.png`
- Asset hash unchanged: `svicloud-15p-marketing-v4-watermarked.webp` SHA-256 `ae6c59234b39303a465bf862a787cc33953b9cf806071addde85929f18074a70`

At 390px:

- Image position changes from `absolute` to `relative`.
- Image is visible at `301.22 × 169.42px` with `16:9` aspect ratio.
- Content begins after the image; no overlap.
- Title, copy, feature points, CTA, and comparison link remain visible.
- Document width remains `390px`; no horizontal overflow.

At 1512px:

- Hero geometry remains `630 × 690px`.
- Image remains `position: absolute` with the existing desktop treatment.
- Content geometry remains unchanged.

## Verification

`/tmp/verify_15p_mobile_hero.mjs` passed **12/12** Chromium/WebKit × English/Traditional Chinese/Simplified Chinese × mobile/desktop cases:

- 0 failures
- 0 filtered console/page errors
- Mobile image visible and non-absolute
- Desktop image treatment preserved
- Text, CTA, comparison link, and feature content present
- No horizontal overflow
