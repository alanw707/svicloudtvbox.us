# Development Instructions — root

## Prerequisites
- Python 3 (for CSS build and deploy scripts)
- Node + npm (Playwright tests)
- Docker + WP stack optional (sync script targets running WP container)
- Optional: DDEV or LocalWP for full WP stack (see docs/svicloudtvbox-dev-environment.md)

## Environment Setup (from dev guide)
- Local WP via DDEV or LocalWP; install WooCommerce, Stripe, PayPal, LiteSpeed Cache, TranslatePress, Yoast, WP Mail SMTP.
- Bilingual: ensure `svic_lang` cookie and /zh/ paths; hreflang pairs.

## Build/Assets
- CSS: `python3 scripts/build_css.py --theme svicloudtvbox-lumen` (use `--bundle front-page --pretty` to iterate)
- Bundles defined in `theme/svicloudtvbox-lumen/assets/css/bundles.json`; edit partials under `assets/css/parts/`, never the generated outputs.
- JS entry: `theme/svicloudtvbox-lumen/assets/js/theme.js` (jQuery-driven UI + Woo UX).

## Sync/Packaging
- Sync to local Docker WP: `./scripts/sync_theme_container.sh [container-fragment]` (streams theme into `/var/www/html/wp-content/themes/svicloudtvbox-lumen`, bumps `.deploy-version`, flushes cache).
- Zip theme: `python3 scripts/zip_theme.py` (for handoff/deploy).

## Tests
- Install: `npm install`
- Run Playwright: `npm run test` (alias `npm run test:playwright`), headed: `npm run test:playwright:headed`.

## References
- Full dev environment guide: `docs/svicloudtvbox-dev-environment.md`.
