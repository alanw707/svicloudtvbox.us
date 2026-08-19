# 15P delivery-banner post-rebase reverification

- Rebased branch: `safety/15p-delivery-banner-20260818`
- Rebase evidence commit: `b75590290b6c8230947322862522ed8a1d289c22`
- Final verification commit: `9407a92beca8ae66e84e550b21878769d32dfe85`
- Updated base: `origin/main` `d4c2e2a358d1049099985916553b051bd1e5824d`
- Local `main`: `b25fa8aa3ba892c221c11e1a885d31c5f30815cf` (unchanged)
- Explicit production approval, recorded before the guarded deployment: `I approve deploying the 2–3 week delivery banner to production.`
- Approval gate audit trail: the superseded preflight document now labels its pending/none fields as historical; this final record is authoritative.

## Production banner probe

The approved banner is live and passed a six-case Chromium/WebKit × English/Traditional Chinese/Simplified Chinese probe:

- PDP: banner visible with the correct locale copy.
- Cart: banner visible with the correct locale copy.
- Checkout: banner visible with the correct locale copy.
- First-party probe errors: `0`.
- Production Playwright: 16/16 Chromium/WebKit launch and localized commerce tests pass after replacing third-party-sensitive `networkidle` waits with DOM-ready waits in test commit `9407a92`.
- Local Playwright rerun: 16/16 pass.

Third-party Google Pay, PayPal, Clarity, and report-only CSP console noise was excluded from first-party error counting; it did not affect banner or commerce assertions. The unfiltered diagnostic output is retained as `production-delivery-banner-probe.out`; the final gate uses `production-delivery-banner-probe-filtered.out`, whose first-party error count is zero.

## Preserved release state

- Product `1204`: `publish|visible|purchasable|379|288|288|manage_stock|0|notify|onbackorder`.
- Gallery: five images; response/hash verification passes.
- Watermark coverage: pass.
- Product/surface references: pass.
- Active sitemap: pass.
- Production private-data probe: 0 matching orders/customers.
- Restored two upstream notifier/email theme files removed by the earlier stale-theme deployment; remote/local hashes match:
  - `inc/class-svic-15p-preorder-notifier.php`
  - `woocommerce/emails/customer-svic-15p-preorder.php`

## Notes

- The rebased branch was not force-pushed and `main` was not rewritten.
- The production banner was verified against the already-approved banner deployment; unrelated upstream Shop/accessory changes were not deployed as part of this rebase-only operation.
- The updated upstream local SEO audit reports six duplicate Product `@id` findings on accessory-expanded Shop pages. The 15P PDP schema remains one BackOrder Product offer with no `deliveryTime`; this upstream baseline issue is outside the delivery-banner scope.
