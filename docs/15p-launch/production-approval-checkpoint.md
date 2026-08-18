# Corrective production approval checkpoint

- Release candidate: `692e48987b1189c488fb3d4df47f106687dd8fd4`
- Scope: corrected five-image gallery, customer-facing Pre-order / 預購 / 预订 copy, and production managed stock correction to `0`.
- Local validation: passed gallery roles, PHP lint, fixture/security, private preservation, SEO, and localized storefront checks.
- Backup verification: passed via `scripts/verify_release_backup.py`; raw private archives remain outside Git.
- Production write status: **PENDING EXPLICIT APPROVAL**.
- Required approval text: `I approve deploying the corrected pre-order gallery to production.`

No production write may occur until the approval text is received. After approval, append the approval timestamp, deployment command/result, and independent production probe artifacts here in a follow-up commit.
