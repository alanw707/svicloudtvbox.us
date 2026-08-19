# SVICLOUD 15P delivery-time banner release

Status: local validation complete; production approval pending.

## Scope

- Customer-facing 15P delivery banner on the product page, cart, and checkout.
- Localized copy:
  - English: `Pre-order delivery` / `Expected delivery: 2–3 weeks after ordering.`
  - Traditional Chinese: `預購配送` / `預計下單後 2–3 週送達。`
  - Simplified Chinese: `预订配送` / `预计下单后 2–3 周送达。`
- Existing internal WooCommerce `BackOrder`, `notify`, `onbackorder`, pricing, managed stock, gallery, and purchasability remain unchanged.
- Product schema continues to omit `deliveryTime`; the estimate is customer-facing copy only.

## Release candidate

- Source implementation commit: `ea89e9abaeb45e46bbde53a9a934bcbf9acef81b`
- Release candidate: `8f4e4a5c4908f2280cf53fbaa117947376fb493f`
- Safety branch: `safety/15p-delivery-banner-20260818`
- Production writes: **none**
- Required approval text: **“I approve deploying the 2–3 week delivery banner to production.”**

## Local validation

- PHP lint: theme bootstrap, all three locale files, templates, fixture importer — pass.
- CSS build: all bundles rebuilt from partials; WooCommerce bundle includes the banner styles.
- Targeted Playwright: 16/16 Chromium/WebKit launch and localized commerce checks pass.
- Local storefront audit: 36/36 locale/viewport/route checks pass with zero errors.
- SEO audit: 24 pages, 77 links, 0 issues.
- Fixture security: 3/3 tests pass; private-fixture preservation passes.
- Local 15P state: `publish|visible|1|379|288|288|1|0|notify|onbackorder|5`.
- Local schema: BackOrder offer remains present and has no `deliveryTime`.

Before deployment, create a fresh read-only production safety reference and independently verify it. Deploy only after the distinct approval above. Append production banner probes, regression checks, and hashes after deployment. Do not restore DB/uploads archives.
