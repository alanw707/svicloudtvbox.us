# Corrective 15P production deployment log

- Release commit before approval: `08938f8029836f74862d85f01e3d0a622ebf9e46`
- Approval recorded in commit `846e73e09d5ef552070c1b0cd5fc1a1f5bae5518` at `2026-08-18T21:48:18Z`.
- Approved text: `I approve deploying the corrected pre-order gallery to production.`
- Preflight backup: `docs/15p-launch/production-backup-manifest.md`; verifier returned `pass=true` before writes.

## Ordered writes

1. FTPS theme dry-run completed with certificate pin `47e1b5dc5d6102e8ed8c012161af8b225370ab5563b00caf146cbd7a9651e95e`.
2. Pinned FTPS theme deployment completed at `2026-08-18T21:51:59Z`: 9 uploads, 3 obsolete gallery asset deletions, no unapproved remote paths.
3. WooCommerce REST product update completed at `2026-08-18T21:52:17Z`: product `1204`, gallery media `[1210, 1211, 1216, 1217, 1218]`, state verified as `publish|visible|purchasable|379|288|288|manage_stock|0|notify|onbackorder`.
4. Temporary pinned cache/opcache purge completed at `2026-08-18T21:53:25Z`; temporary MU plugin deleted.

## Independent production evidence

The raw, sanitized production probe outputs are committed under `docs/15p-launch/production-evidence/` with `SHA256SUMS`:

- Gallery exact local/remote hashes: pass, 5/5.
- Theme delivered-asset coverage: pass, 9/9 exact hashes.
- Homepage/Shop/Compare/PDP surface references: pass, no obsolete unwatermarked references.
- Gallery geometry: pass, 5/5 selected images inside stage with `objectFit=contain`.
- Localized critical commerce: Chromium/WebKit Traditional Chinese and Simplified Chinese pass, no first-party errors.
- English commerce: Chromium/WebKit pass, no first-party errors.
- Broad production launch Playwright: 12/12 pass.
- Storefront: all configured locale/viewport routes pass with zero first-party errors/overflow.
- SEO: 24 pages, 77 links, 0 issues; active `/sitemap_index.xml` and 4 children pass.
- Private-data check: 0 matching production orders and 0 matching production customers.

No DB/uploads archive was restored. Old media remains preserved for rollback; only the displayed product gallery changed.
