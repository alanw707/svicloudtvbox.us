# Cart quantity investigation — 2026-09-05

## Reproduced on production without creating orders

1. Add 15P quantity 2; reduce cart field to 1; proceed directly to checkout without Update cart. Checkout still shows ×2/$576. Browser regression failed with expected 1, actual ×2.
2. Add 15P quantity 1; reload success document. Browser trace shows POST product URL -> HTTP200 twice, cart field becomes 2. Missing successful-add redirect permits POST replay.
3. Existing 15P in cart; invoke site's Buy Now URL with quantity=1. Checkout shows ×2. Existing Buy Now hook removed other product keys but retained accumulated quantity of the selected key.

A clean cart and explicit Update cart can purchase one unit in all three locales. There is no demonstrated two-unit minimum. These are reproducible failure paths, not a claim to reconstruct any customer's exact clicks.

## Fixes

- Successful non-AJAX add redirects to clean localized cart URL, unless an explicit redirect exists; Buy Now retains localized checkout destination.
- Buy Now resets selected key to the validated quantity provided by Woo rather than previous quantity plus requested quantity. Ordinary Add to cart remains additive and intentionally requested quantity 2 remains supported.
- Checkout with dirty quantities runs Woo's existing cart update, checks returned quantities and errors, then navigates. Errors/timeouts stay on cart. Express-payment containers are hidden while quantities are unsaved so stale totals cannot be paid via those buttons.
- Localized notice explains the save behavior.

## Verification

- `php scripts/test_cart_request_safety.php`: tests registered Woo callbacks and quantity semantics, plus localized redirects. This is a hook harness, not a full Woo deployment test.
- `PLAYWRIGHT_BASE_URL=https://svicloudtvbox.us npx playwright test tests/playwright/quantity-integrity.spec.ts --workers=1`: actual cart/checkout browser regressions; no payments/order submission.
- `SVIC_LOCAL_ASSETS=1` intercepts theme JS/CSS only for predeploy testing against actual Woo cart persistence. Server redirect/Buy Now tests require the PHP deployment and are not falsely marked covered by JS injection.

## Address-change finding (separate from cart fixes)

The order1265 request is actionable under the mail classifier, appears in the watcher log, and is in seen state, not ignored. Seen/alerted is not fulfillment acknowledgement. Current support form sends email but does not create an authenticated order-change hold. A future change-control flow needs order/customer authentication, an outstanding request attached to the order, explicit operator resolution, and a pre-label check. Do not automatically change shipment addresses or hold orders from unverified email text. Customer now plans to retrieve the package at the old address; collection not confirmed. Intercept monitor disabled at Alan's request. No refund/replacement.
