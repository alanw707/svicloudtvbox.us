# Project Documentation Index — svicloudtvbox.us

## Project Overview
- **Type:** monolith
- **Primary Language:** PHP/JS (WordPress + WooCommerce)
- **Architecture:** WordPress theme with WooCommerce overrides; CSS partial bundles; jQuery UX

## Quick Reference
- **Tech Stack:** WordPress, WooCommerce, PHP templates, CSS partials → bundles.json, jQuery in `assets/js/theme.js`
- **Entry Points:** `front-page.php`, `page-compare.php`, `page-about.php`, guides/FAQ/support/contact/policy templates; `woocommerce/` overrides
- **Architecture Pattern:** WordPress theme with WooCommerce overrides

## Generated Documentation
- [Project Overview](./project-overview.md)
- [Architecture](./architecture.md)
- [Source Tree Analysis](./source-tree-analysis.md)
- [Component Inventory](./component-inventory.md)
- [Development Guide](./development-guide.md)
- [Deployment Guide](./deployment-guide.md)
- [API Contracts](./api-contracts.md)
- [Data Models](./data-models.md)

## Existing Documentation
- [PRD](../svicloudtvbox-prd.md)
- [Dev Environment Guide](../svicloudtvbox-dev-environment.md)
- [WooCommerce Snippets](../svicloudtvbox-woocommerce-snippets.md)
- [Hostinger Implementation Plan](../svicloudtvbox-hostinger-implementation-plan.md)
- [Launch Plan](../svicloudtvbox-launch-plan.md)
- [Integration: Google Customer Reviews](../integration/google-customer-reviews.md)
- [FAQ (EN)](../svicloudtvbox-faq.md)
- [FAQ (ZH)](../svicloudtvbox-faq-zh.md)
- [I18N Plan](../svicloudtvbox-i18n-plan.md)
- [I18N String Audit](../svicloudtvbox-i18n-string-audit.md)
- [Backlog](../svicloudtvbox-backlog.md)
- [Guides Content Next Steps](../guides-content-next-steps.md)
- [SVICLOUDTVBOX Lumen Phase 0](../svicloudtvbox-lumen-phase0.md)
- [Stripe Integration Plan](../stripe-integration-plan.md)
- [Manual Posting](../manual-posting.md)
- [Dashboard Hero Migration Plan](../dashboard-hero-migration-plan.md)
- [How to Set Up (EN)](../how-to-set-up-svicloud-tv-box.md)
- [How to Set Up (ZH)](../how-to-set-up-svicloud-tv-box-zh.md)
- [Single Page Setup (ZH)](../svicloud-single-page-setup-zh.md)
- [Quick Reference Card (ZH)](../svicloud-quick-reference-card-zh.md)
- [Box Setup Print Guide (ZH)](../svicloud-box-setup-print-guide-zh.md)

## Archived Materials
- Marketing/SEO: `../archive/marketing/`, `../archive/seo/`
- Blog posts: `../archive/blog/`

## Getting Started
1) Edit CSS partials → `python3 scripts/build_css.py --theme svicloudtvbox-lumen`
2) JS updates in `theme/svicloudtvbox-lumen/assets/js/theme.js`
3) Sync to local Docker WP: `./scripts/sync_theme_container.sh`
4) Tests: `npm install && npm run test`
5) Deploy: `./scripts/deploy-theme.sh --dry-run` → remove `--dry-run` when ready (requires `.env` FTP_*).

## Notes
- Do not edit generated CSS outputs; rebuild from partials.
- Theme assets live in `theme/svicloudtvbox-lumen`; Woo overrides under `woocommerce/`.
- Use `python3 scripts/zip_theme.py` to package the theme if needed.
