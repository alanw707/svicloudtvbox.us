# 15P Launch Build — Local Verification Report

Date: 2026-08-15

## Build and code quality

- `python3 scripts/build_css.py --theme svicloudtvbox-lumen` — clean; generated CSS rebuilt from registered partials.
- `./scripts/sync_theme_container.sh svicloud10p` — complete; bind-mounted theme live in local Docker.
- PHP lint — clean for `functions.php`, homepage, compare page, WooCommerce templates, and all three locale files.
- `git diff --check` — clean.
- Work recorded with Conventional Commit message `feat(theme): prepare SVICloud 15P launch` (verify with `git log`).

## Rendered SEO and schema

- Local `/` → HTTP 200 and rendered title: `New SVICLOUD 15P Coming Soon | Authorized SVICLOUD TV Box US Dealer`.
- Local `/product/svicloud-15p/` → HTTP 200.
- 15P JSON-LD contains exactly **one** `Product` node (one unique `@id`), one `FAQPage`, and one `BreadcrumbList`.
- Prelaunch Product node has **no Offer** because price and inventory are unconfirmed.
- 15P FAQ schema asks whether specs, availability, and policies are confirmed; answers explicitly say TBC.

## Product state and claim safety

- Local 15P product ID 95 is published as an **out-of-stock, no-price, non-purchasable preview**.
- No 15P add-to-cart control renders.
- Official imagery placeholder replaces the generic old-model product image.
- Hardware, features, fee terms, inventory, shipping, warranty, returns, support, and in-box contents are visibly marked SPEC/FEATURE/POLICY/TBC.
- Unsupported performance phrases are absent from rendered 15P comparison copy.
- Draft articles and support scripts include prepublication gates and TBC fields.

## 9P preservation and funnel

- Local `/product/svicloud-9p/` and `/zh/product/svicloud-9p/` → HTTP 200.
- 9P is published but out of stock as a legacy support/reference page.
- 9P PDP links to `/product/svicloud-15p/`; 15P comparison links back to both 9P and 10P+.
- 10P+ and 10S PDPs also link to the 15P preview.

## Screenshots

Homepage evidence:
- `screenshots/before-home-desktop.png`
- `screenshots/before-home-mobile.png`
- `screenshots/redesign-home-desktop.png`
- `screenshots/redesign-home-mobile.png`

Genuine same-URL PDP evidence (`/product/svicloud-10p-plus/`):
- `screenshots/before-pdp-10p-desktop.png` SHA-256 `477d3dde...`
- `screenshots/after-pdp-10p-desktop.png` SHA-256 `4eb66af1...`
- `screenshots/before-pdp-10p-mobile.png` SHA-256 `9cb76d1c...`
- `screenshots/after-pdp-10p-mobile.png` SHA-256 `43764117...`

The corresponding before/after hashes differ. Final prelaunch PDP screenshots:
- `screenshots/final-pdp-15p-desktop.png`
- `screenshots/final-pdp-15p-mobile.png`

## Playwright

`npm test`: **88 passed, 8 declared skips, 0 failed** (exit 0).

Additional launch safeguards in `tests/playwright/launch-15p.spec.ts` verify:

- rendered homepage title contains 15P;
- exactly one Product schema node and no Offer;
- no 15P add-to-cart control;
- unsupported comparison claims are absent and TBC copy is present;
- legacy 9P URL is live and links to 15P.

`tests/playwright/smoke.spec.ts` collects every console error without an ignore list. Homepage, compare, shop, current PDPs, 15P, and account pass on Chromium desktop and WebKit mobile with zero console errors. The CSP response is amended at the shared header seam to allow only the Google Customer Reviews sources required by the enabled badge.

## Deployment boundary

No production theme deployment is included. Product creation, final price/inventory, official images, spec replacement, publication, advertising, and launch activation remain manual launch-day steps.
