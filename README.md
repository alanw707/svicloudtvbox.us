# SVICLOUDTVBOX.US

WordPress + WooCommerce storefront for SVICLOUD TV boxes (10P+ / 10S), hosted on Hostinger. Bilingual (EN/中文) with TranslatePress, custom lightweight block theme, Stripe/PayPal payments, and LiteSpeed performance.

## Quick Links
- PRD: `docs/svicloudtvbox-prd.md`
- Launch Plan: `docs/svicloudtvbox-launch-plan.md`
- Hostinger Implementation Plan: `docs/svicloudtvbox-hostinger-implementation-plan.md`
- Implementation Backlog: `docs/svicloudtvbox-backlog.md`
- Dev Environment Guide: `docs/svicloudtvbox-dev-environment.md`
- Contributor Guide: `AGENTS.md`
- WooCommerce Snippets: `docs/svicloudtvbox-woocommerce-snippets.md`

## Stack
- Platform: Hostinger (LiteSpeed)
- CMS: WordPress + WooCommerce (U.S.-only)
- Payments: Stripe (Apple/Google Pay), PayPal
- Bilingual: TranslatePress (`/zh/` paths, hreflang)
- SEO: Yoast/RankMath + JSON-LD (Org/Product)
- Performance: LiteSpeed Cache, WebP, lazy-load, minify (tested)

## Development
- Local + Staging workflow recommended.
- See `docs/svicloudtvbox-dev-environment.md` for DDEV/LocalWP setup, syncing DB/media, Stripe webhooks, and caching/pixels validation.

### Local WordPress (DDEV)
1. Install Docker Desktop and [DDEV](https://ddev.readthedocs.io/en/latest/).
2. From the repo root run `./scripts/setup-local-ddev.sh` to provision WordPress, install WooCommerce + required plugins, and symlink this theme into `public/wp-content/themes/svicloudtvbox/`.
3. Visit `https://svicloudtvbox.ddev.site` (admin at `/wp-admin`, user `admin`, password `admin`) to activate and iterate on the theme locally.
4. After importing staging data run `ddev wp search-replace 'https://staging.svicloudtvbox.us' 'https://svicloudtvbox.ddev.site' --all-tables` to normalize URLs.

## Theme
- Custom lightweight block theme `svicloudtvbox` (planned):
  - theme.json design tokens (colors, fonts, spacing)
  - Header/Footer parts, front-page, PDP, shop archive, compare page
  - Patterns: hero, trust bar, product tiles, FAQ, support CTA

## Backlog & Launch
- Work is tracked in `docs/svicloudtvbox-backlog.md`.
- Hostinger setup, WooCommerce config, translations, SEO/analytics, and QA steps in `docs/svicloudtvbox-hostinger-implementation-plan.md`.

## Contributing
- Default branch: `main`
- Open PRs to `main`; keep changes focused and reference backlog items.

Status: MVP in progress.

## Deployment

- Theme-only FTP deploy (Hostinger):
  - Copy `.env.example` to `.env` and fill `FTP_HOST`, `FTP_USER`, `FTP_PASS` (do not commit `.env`).
  - Ensure WordPress is under `public_html` on Hostinger (default). Adjust `FTP_BASE_DIR` if different.
  - Run: `bash scripts/deploy-theme.sh` (uses FTPS by default). Set `DRY_RUN=true` to preview.
