# Production 15P gallery and pricing update evidence

Status: corrective pre-order/gallery deployment completed after the explicit approval below.

## Approval checkpoint

User approval recorded in the goal session:

> I approve the production gallery and $288 pricing update.

The approval covered the corrected five-image watermarked gallery, screenshot-derived AI primary image, customer-facing pre-order wording, `$379` regular / `$288` sale pricing, managed stock correction to `0`, production verification, and rollback on critical failure.

## Release history

- Preflight commit: `51fb067a11ca455856e674d8cb39fdf9d3757007`
- Corrected SEO theme commit: `afa372c85d73eb7463dc2eb2b446e4f76e09407f`
- Watermark/sitemap fix commit: `9ae0f847fdb7d1c91a7cf55c54b7e0671de026d5`
- Corrective gallery/copy commit: `e3c1c766d1cbd44cd1b86c7e434b65dea8508311`
- Approval checkpoint commit: `846e73e09d5ef552070c1b0cd5fc1a1f5bae5518`
- Gallery-fit test commit: `08938f8029836f74862d85f01e3d0a622ebf9e46`
- Deployment evidence: `docs/15p-launch/production-deployment-log.md` and `docs/15p-launch/production-evidence/`
- Initial retry failed the social-image gate; product/media/theme rollback completed. Evidence: external Pi backup `rollback-evidence.md`.
- Corrected retry added explicit 15P social-image metadata and deployed the fully watermarked fallback assets.

## Live state

- Product ID: `1204`, slug `svicloud-15p`
- State: published, visible, purchasable, regular `$379`, sale/effective `$288`, managed stock `0`, `notify`, `onbackorder`
- Gallery IDs: `1210`, `1211`, `1216`, `1217`, `1218`
- Gallery order: screenshot-derived AI primary, angle/rear ports, packaging mockup, clean AI lifestyle, clean second AI lifestyle
- All five production image response bodies match committed local watermarked WebP hashes; exact raw output is committed in `production-evidence/production-final-gallery-hashes.json`.
- Theme marketing/fallback surfaces use only watermarked assets; old `svicloud-15p-marketing-v4.webp` was removed from the repository and remote theme, and the remote `.htaccess` retires that legacy path with HTTP 410. Production homepage, Shop, Compare, PDP, and metadata surface probes found no old unwatermarked `marketing-v4` or `front` references.
- Theme remote verification: `212/212` files match.

## Verification

- Production critical localized flows: Chromium/WebKit × Traditional Chinese/Simplified Chinese, 4/4 passed with zero first-party errors; raw output is committed in `production-evidence/production-final-critical.json`.
- Production English flows: Chromium/WebKit PDP → add-to-cart → cart → checkout, 2/2 passed with zero first-party errors; raw output is committed in `production-evidence/production-final-english.json`.
- Storefront audit: 36/36 route/viewport checks passed.
- SEO audit: 24 pages, 77 internal links, 0 issues.
- Product schema: one `$288.00 USD` BackOrder offer, no delivery-time promise.
- Social metadata: one unique `og:image` and one unique `twitter:image`, both watermarked primary image.
- Active sitemap: `/sitemap_index.xml` and all four children HTTP 200; `/wp-sitemap.xml` resolves to the active Rank Math sitemap rather than the homepage via the pinned remote `.htaccess` redirect. The pre-update/final `.htaccess` hashes and retired legacy-image rule are recorded in the external preflight evidence.
- No production offline-verifier orders/customers; no unrelated/private data or infrastructure writes.

The pre-update DB/uploads/remote-theme backups remain external, private, and hash-verified through the committed `production-backup-manifest.md` and `scripts/verify_release_backup.py`. No DB/uploads restoration was performed.
