# Corrective production approval checkpoint

- Release candidate: `08938f8029836f74862d85f01e3d0a622ebf9e46`
- Scope: corrected five-image gallery, customer-facing Pre-order / 預購 / 预订 copy, and production managed stock correction to `0`.
- Local validation: passed gallery roles, PHP lint, fixture/security, private preservation, SEO, and localized storefront checks.
- Backup verification: passed via `scripts/verify_release_backup.py`; raw private archives remain outside Git.
- Approval timestamp (UTC): `2026-08-18T21:48:18Z`
- User approval: `I approve deploying the corrected pre-order gallery to production.`
- Production write status: **APPROVED — DEPLOY NOW**.

Deployment must append the deployment command/result and independent production probe artifacts here in a follow-up commit. Roll back product/media/theme on any critical gate failure; do not restore DB/uploads archives without separate approval.
