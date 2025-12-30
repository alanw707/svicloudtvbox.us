# Development Guide — svicloudtvbox.us

## Prerequisites
- Python 3 (build/deploy scripts)
- Node + npm (Playwright tests)
- Optional: Docker + WP stack (for sync script), DDEV or LocalWP (see `docs/svicloudtvbox-dev-environment.md`)

## Setup
- Local WP: follow `docs/svicloudtvbox-dev-environment.md` (WooCommerce, Stripe, PayPal, LiteSpeed Cache, TranslatePress, Yoast, WP Mail SMTP).
- Bilingual: ensure `svic_lang` cookie and /zh/ routes; hreflang pairs.

## Build
- CSS: edit `theme/svicloudtvbox-lumen/assets/css/parts/*.css`; build bundles:
  - `python3 scripts/build_css.py --theme svicloudtvbox-lumen`
  - For a specific bundle while iterating: `python3 scripts/build_css.py --theme svicloudtvbox-lumen --bundle front-page --pretty`
- JS: `theme/svicloudtvbox-lumen/assets/js/theme.js` (jQuery; guard DOM nodes).

## Sync/Package
- Sync to local Docker WP: `./scripts/sync_theme_container.sh [container-fragment]` (streams theme, bumps `.deploy-version`, flushes cache).
- Zip theme: `python3 scripts/zip_theme.py` (for handoff/deploy).

## Tests
- Install: `npm install`
- Run: `npm run test` (alias `npm run test:playwright`), headed: `npm run test:playwright:headed`.

## Dev Tips
- Do not edit generated CSS outputs (`style.css`, `front-page.css`, etc.); change partials and rebuild.
- After sync/deploy, hard refresh browser (cache-busted by `.deploy-version`).
- Track Woo/checkout flows: cart toasts, Stripe saved-card pills, coupon relocation.
