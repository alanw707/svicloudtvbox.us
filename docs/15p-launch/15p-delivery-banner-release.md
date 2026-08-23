# SVICLOUD 15P delivery-time banner release

Status: preflight snapshot superseded by the approved deployment and post-rebase reverification recorded in `15p-delivery-banner-reverification.md`.

## Scope

- Customer-facing 15P delivery banner on the product page, cart, and checkout.
- Localized copy:
  - English: `Pre-order delivery` / `Release window: 1 to 2 weeks.`
  - Traditional Chinese: `預購配送` / `上市時程：1 至 2 週。`
  - Simplified Chinese: `预订配送` / `上市时间：1 至 2 周。`
- Existing internal WooCommerce `BackOrder`, `notify`, `onbackorder`, pricing, managed stock, gallery, and purchasability remain unchanged.
- Product schema continues to omit `deliveryTime`; the estimate is customer-facing copy only.

## Release candidate

- Source implementation commit: `ea89e9abaeb45e46bbde53a9a934bcbf9acef81b`
- Release candidate: `8f4e4a5c4908f2280cf53fbaa117947376fb493f`
- Safety branch: `safety/15p-delivery-banner-20260818`
- Historical preflight write status: **none at snapshot creation**.
- Required approval text: **“I approve deploying the 2–3 week delivery banner to production.”**
- Approval subsequently recorded before deployment: **“I approve deploying the 2–3 week delivery banner to production.”**
- Final production writes and verification: recorded in `15p-delivery-banner-reverification.md` and the external preflight raw outputs.

## Local validation

- PHP lint: theme bootstrap, all three locale files, templates, fixture importer — pass.
- CSS build: all bundles rebuilt from partials; WooCommerce bundle includes the banner styles.
- Targeted Playwright: 16/16 Chromium/WebKit launch and localized commerce checks pass.
- Local storefront audit: 36/36 locale/viewport/route checks pass with zero errors.
- SEO audit: 24 pages, 77 links, 0 issues.
- Fixture security: 3/3 tests pass; private-fixture preservation passes.
- Local 15P state: `publish|visible|1|379|288|288|1|0|notify|onbackorder|5`.
- Local schema: BackOrder offer remains present and has no `deliveryTime`.

## Fresh preflight reference

A fresh read-only reference was created before any banner deployment write:

- External backup directory: `/home/alanw/.pi/backups/svicloudtvbox.us/2026-08-18-15p-delivery-banner-preflight`
- Remote theme: 212 members, archive SHA-256 `c0f8d22990cc9c8be39aa22a9a3876e573fb4cf549b4ba9b7a74252b986d05f3`
- Remote `.htaccess` SHA-256: `b1cc1a01c64a885fa34ecebc6d90a8b6df3d1235f6bae64194cd672d2557a4cf`
- Read-only product state: `publish|visible|1|379|288|288|1|0|notify|onbackorder`, five gallery images.
- Verification summary: external `preflight-verification.json`; private archives remain outside Git.

At this preflight snapshot, deployment was correctly blocked until the distinct approval above. That approval was later provided; the approved deployment, targeted notifier-file restoration, production probes, and final hashes are recorded in `15p-delivery-banner-reverification.md`. Do not restore DB/uploads archives.
