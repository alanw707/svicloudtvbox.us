# SVICLOUDTVBOX.US

WordPress + WooCommerce storefront for SVICLOUD TV boxes (10P+ / 10S), hosted on Hostinger. Bilingual (EN/中文) with TranslatePress, custom lightweight block theme, Stripe/PayPal payments, and LiteSpeed performance.

## Quick Links
- PRD: `docs/svicloudtvbox-prd.md`
- Launch Plan: `docs/svicloudtvbox-launch-plan.md`
- Hostinger Implementation Plan: `docs/svicloudtvbox-hostinger-implementation-plan.md`
- Implementation Backlog: `docs/svicloudtvbox-backlog.md`
- Dev Environment Guide: `docs/svicloudtvbox-dev-environment.md`
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

