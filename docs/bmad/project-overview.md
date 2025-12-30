# Project Overview — svicloudtvbox.us

## Summary
- WordPress + WooCommerce monolith with custom theme `svicloudtvbox-lumen`.
- CSS authored in partials under `assets/css/parts/`, bundled via `scripts/build_css.py` → route-specific outputs.
- JS in `assets/js/theme.js` for navigation, hero/perf, product gallery/carousel, Woo cart/checkout UX, language toggle, Stripe saved-card pills.
- Deploy via FTPS script; local Docker sync helper.
- Docs consolidated under `docs/`; legacy marketing/SEO archived to `docs/archive/*`.

## Tech Stack
- Platform: WordPress, WooCommerce
- Theme: PHP templates + CSS partial bundles + jQuery UI behaviors
- Tooling: Python scripts (build_css, zip_theme, deploy-theme), Playwright tests

## Repository Structure (monolith)
- Theme: `theme/svicloudtvbox-lumen/` (templates, CSS/JS assets, Woo overrides, locales)
- Scripts: `scripts/` (build/sync/deploy, zip)
- Docs: `docs/` (product/ops docs); `docs/archive/` (marketing/SEO)
- Tests: Playwright config and tests under `tests/`

## Key Documents
- Architecture: `./architecture.md`
- Source Tree: `./source-tree-analysis.md`
- Component Inventory: `./component-inventory.md`
- Development Guide: `./development-guide.md`
- Deployment Guide: `./deployment-guide.md`
- API Contracts: `./api-contracts.md` (no custom endpoints)
- Data Models: `./data-models.md` (no repo-owned schemas)

## Getting Started
- Edit CSS partials → `python3 scripts/build_css.py --theme svicloudtvbox-lumen`
- Sync to local Docker WP: `./scripts/sync_theme_container.sh`
- Run Playwright tests: `npm install && npm run test`
- Deploy: `./scripts/deploy-theme.sh --dry-run` then without `--dry-run` when ready (requires `.env` with FTP_*).

## Archived
- Legacy marketing/SEO materials relocated to `docs/archive/marketing/` and `docs/archive/seo/`.
