# Tech-Spec: Checkout Payment Options Investigation and Fix

**Created:** 2025-12-27
**Status:** Ready for Development

## Overview

### Problem Statement
Checkout payment options are failing for live users. PayPal flickers on selection and does not progress, and Apple Pay may be unavailable. Only credit card appears to work. Woo Payments was disabled recently due to redundancy, and Stripe payment plugins were installed. The site is live and the issue impacts all users.

### Solution
Investigate and resolve payment method availability and UX for checkout. Ensure Stripe card + Link, Apple Pay, Google Pay, Klarna, and PayPal all appear and function correctly. Remove conflicts and redundant gateways, and confirm frontend behavior with WooCommerce checkout updates.

### Scope (In/Out)
- In: Investigate checkout payment gateway configuration, plugin conflicts, and frontend behavior. Fix availability and selection flow for Stripe card + Link, Apple Pay, Google Pay, Klarna, and PayPal.
- Out: Broad checkout redesign, pricing/tax logic, shipping or cart changes unrelated to payments.

## Context for Development

### Codebase Patterns
- Checkout is rendered via a theme override: `theme/svicloudtvbox-lumen/woocommerce/checkout/form-checkout.php`.
- The checkout block is replaced with the template via `render_block` filter in `theme/svicloudtvbox-lumen/functions.php`.
- Stripe saved-card UI enhancements live in `theme/svicloudtvbox-lumen/assets/js/theme.js`.
- Payment method styling is concentrated in `theme/svicloudtvbox-lumen/assets/css/parts/71d-checkout-payment.css` and `theme/svicloudtvbox-lumen/assets/css/parts/71z-checkout-responsive.css`.

### Files to Reference
- `theme/svicloudtvbox-lumen/woocommerce/checkout/form-checkout.php`
- `theme/svicloudtvbox-lumen/functions.php`
- `theme/svicloudtvbox-lumen/assets/js/theme.js`
- `theme/svicloudtvbox-lumen/assets/css/parts/71d-checkout-payment.css`
- `theme/svicloudtvbox-lumen/assets/css/parts/71z-checkout-responsive.css`

### Technical Decisions
- Prefer a single Stripe gateway plugin to avoid conflicts (configure via WooCommerce settings).
- Ensure PayPal is provided by a single plugin (WooCommerce PayPal or Stripe PayPal if enabled) to avoid duplicate or broken options.
- Preserve existing checkout template and styling; only adjust if UX issues originate from theme JS/CSS.

## Implementation Plan

### Tasks
- [ ] Inventory active payment plugins and gateways (Woo Payments disabled). Confirm which Stripe plugin(s) and PayPal plugin(s) are active.
- [ ] Audit WooCommerce payment settings for Stripe and PayPal: enable Link, Apple Pay, Google Pay, Klarna; verify domain registration and required keys.
- [ ] Reproduce checkout issue locally/staging: select PayPal, observe flicker, capture console/network errors.
- [ ] Inspect `theme.js` for checkout event handlers that may interfere with payment method selection or PayPal modal/redirect.
- [ ] Review checkout CSS for overlays/positioning that could block PayPal/Apple Pay frames or buttons.
- [ ] Resolve conflicts by disabling redundant gateways and ensuring only one gateway per method.
- [ ] Verify each payment method end-to-end on checkout: card + Link, Apple Pay, Google Pay, Klarna, PayPal.

### Acceptance Criteria
- [ ] PayPal selection leads to the expected modal/redirect without flicker or failure.
- [ ] Stripe card + Link is available and functional at checkout.
- [ ] Apple Pay and Google Pay buttons appear when eligible and complete checkout.
- [ ] Klarna appears and completes checkout when enabled for the cart currency/region.
- [ ] No console errors on checkout page for payment selection.
- [ ] Only the intended payment methods appear (no duplicate gateways).

## Additional Context

### Dependencies
- WooCommerce payment gateway plugins (Stripe, PayPal).
- Stripe dashboard settings for wallets (Apple Pay/Google Pay), Link, and Klarna.

### Testing Strategy
- Manual checkout flow for each payment method on `/checkout/` with a valid cart.
- Confirm behavior on desktop and mobile widths.
- Verify no regression in checkout layout or order placement.

### Notes
- Live site impact; prioritize investigation and safe fixes.
- If the issue is plugin-conflict-related, document which plugin remains and why.
