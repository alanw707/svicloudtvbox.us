# Architecture — svicloudtvbox.us (root)

## Executive Summary
WordPress monolith with a custom theme (`svicloudtvbox-lumen`) and WooCommerce overrides. Styling is authored in CSS partials and bundled via `scripts/build_css.py`. Frontend interactions live in a single JS entry (`assets/js/theme.js`) to enhance navigation, hero/performance, product media, and WooCommerce UX. Deployments use FTPS via `scripts/deploy-theme.sh` → `scripts/deploy_theme.py`; local sync to Docker WP via `scripts/sync_theme_container.sh`.

## Technology Stack
- Platform: WordPress + WooCommerce
- Theme: `svicloudtvbox-lumen` (PHP templates + CSS partial bundles + jQuery-based JS)
- CSS: Partial files under `assets/css/parts/`, bundled per `bundles.json` → `style.css`, `front-page.css`, `about.css`, `compare.css`, `woocommerce.css`, etc. (generated outputs; do not edit directly)
- JS: `assets/js/theme.js` (jQuery; nav, smooth scroll, hero/gallery, cart/checkout UX, Stripe saved-card pills, lazy perf)
- Tooling: Python scripts (`build_css.py`, `sync_theme_container.sh`, `zip_theme.py`, `deploy-theme.sh` / `deploy_theme.py`)
- Tests: Playwright (`npm run test`, headed variant)

## Architecture Pattern
- WordPress theme with shared layout (`header.php`, `footer.php`) and page templates (`front-page.php`, `page-compare.php`, `page-about.php`, guides/support/contact/FAQ/policy pages).
- WooCommerce template overrides: `woocommerce/archive-product.php`, `woocommerce/single-product.php` for catalog/PDP experience.
- CSS authored in parts; composed into multiple bundle outputs per route/context (global, front-page, about, compare, WooCommerce, etc.).
- JS is DOM-guarded, dependency-free, and layered on WooCommerce events (cart/checkout) plus site-wide UX (nav, language toggle, smooth scroll, gallery/carousel, contact tracking).

## Data Architecture
- No repo-owned schemas or migrations. Relies on standard WordPress/WooCommerce DB tables. If schema docs are required, export from the live DB or WooCommerce reference.

## API Design
- No custom REST/GraphQL endpoints in this repo. Theme consumes WordPress/WooCommerce built-in AJAX/REST (e.g., `wc-ajax=add_to_cart`, `admin-ajax.php`).

## Component Overview (from UI inventory)
- Header & Nav: sticky/transparent header, mobile nav toggle with submenu controls.
- Hero/Marketing: hero variants, dashboard visuals/animations, certification, metrics strip, feature grid, experience, pricing, blog highlights.
- Pages: About, Guides, FAQ, Contact/Policy, Support form, Return policy, Blog, Checkout/Order tracking/Received.
- Woo UX: shop/product cards, cart quantity steppers, checkout coupon relocation, Stripe saved-card pills, cart feedback toasts, add-to-cart loading states.
- Product Media: hero gallery (thumb swap), product card carousel.
- Language: locale toggle with `svic_lang` cookie, `lang-zh` class.
- Motion/Perf: animate-on-scroll, preload critical images, lazy backgrounds, header scroll state.
- Contact/Engagement: contact buttons with optional `gtag` tracking.
(See `./ui-component-inventory-root.md` for details.)

## Source Tree
- Annotated tree: `./source-tree-analysis.md`.
- Critical folders: `assets/css/parts/`, `assets/js/theme.js`, `woocommerce/` overrides, `scripts/` (build/sync/deploy), `docs/` (canonical docs; marketing/SEO archived under `docs/archive/*`).

## Development Workflow
- CSS: edit partials in `assets/css/parts/`; build via `python3 scripts/build_css.py --theme svicloudtvbox-lumen` (use `--bundle front-page --pretty` when iterating).
- JS: `assets/js/theme.js` (jQuery; guard DOM existence).
- Sync to local Docker WP: `./scripts/sync_theme_container.sh [container]` (streams theme, bumps `.deploy-version`, flushes cache).
- Zip: `python3 scripts/zip_theme.py`.
- Playwright tests: `npm install` then `npm run test` (or `npm run test:playwright:headed`).
- Full env guide: `docs/svicloudtvbox-dev-environment.md` (DDEV/LocalWP, plugins, bilingual, Stripe/PayPal notes).

## Deployment Architecture
- FTPS deploy to Hostinger via `./scripts/deploy-theme.sh` → `scripts/deploy_theme.py` (reads `.env` for FTP_HOST/FTP_USER/FTP_PASS). Defaults: protocol `ftps`, remote `public_html/wp-content/themes/svicloudtvbox-lumen`. PHP lint runs before upload; optional cache-bust via `.deploy-version`.
- Manual CI/CD (no GitHub Actions present). Package via `python3 scripts/zip_theme.py` if needed.

## Testing Strategy
- Automated: Playwright smoke tests (`npm run test`).
- Manual (recommend): homepage hero/metrics/pricing toggles, compare page, Woo flows (PDP/cart/checkout with coupon relocation, Stripe saved-card pills), language toggle (`svic_lang`), header/nav/responsive states, performance lazy-loading, and cart feedback toasts.

## Risks / Follow-ups
- No repo-level schema/API contracts; depends entirely on WP/Woo configuration.
- No CI pipeline; deployments are manual—consider adding GitHub Actions for lint/build/package.
- Ensure CSS bundles are rebuilt before deploy; avoid editing generated `.css` outputs directly.
