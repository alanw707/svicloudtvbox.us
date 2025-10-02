# Stripe Integration Plan

## Assess Requirements
- Confirm Stripe account status (API keys, webhook secrets, supported currencies).
- Decide on required payment methods (cards, Apple Pay, installments) and compliance needs (SCA, PCI scope).

## Environment Preparation
- Snapshot current site and database before changes.
- Ensure staging mirrors production and meets minimum WP/WooCommerce versions.

## Install Plugin
- Add/activate the "WooCommerce Stripe Payment Gateway" plugin in staging.
- Store plugin source in version control or document managed location.
- Enable test mode for initial configuration.

## Stripe Configuration
- Enter test API keys in WooCommerce settings.
- Enable required payment methods.
- Configure a webhook endpoint in the Stripe dashboard and add the secret in WooCommerce.

## Theme & UI Alignment
- Review checkout/payment templates for overrides.
- If customizations are needed, add templates under `theme/svicloudtvbox-lumen/woocommerce/checkout/`.
- Create CSS partials for any new styles, update `bundles.json`, rebuild via `python3 scripts/build_css.py --theme svicloudtvbox-lumen`.

## Custom Logic
- Implement hooks/filters (prefix with `svic_`) for order metadata, receipts, or subscription handling in a dedicated PHP module.
- Sanitize all outputs according to WordPress standards.

## Testing Cycle
- Run WooCommerce checkout flows in Stripe test mode (cards, 3DS/SCA, refunds, disputes).
- Validate mobile/desktop views, dark mode, and ensure browser console is clean on `/checkout/`.

## Sync & Review
- Execute `./scripts/sync_theme_container.sh` after rebuilding assets.
- Document manual QA with screenshots and notes.
- Prepare Conventional Commit messages and PR summary referencing customer impact.

## Go-Live Checklist
- Switch configuration to live API keys and confirm production webhook settings.
- Perform a live low-value test transaction.
- Monitor Stripe dashboard and site logs post-launch; have rollback steps ready (disable plugin or revert config).
