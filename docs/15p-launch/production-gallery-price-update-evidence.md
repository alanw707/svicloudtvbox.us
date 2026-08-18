# Production 15P gallery and pricing update evidence

Status: corrected retry deployed after the explicit approval below.

## Approval checkpoint

User approval recorded in the goal session:

> I approve the production gallery and $288 pricing update.

The approval covered the five-image watermarked gallery, screenshot-derived AI primary image, `$379` regular / `$288` sale pricing, production verification, and rollback on critical failure.

## Release history

- Preflight commit: `51fb067a11ca455856e674d8cb39fdf9d3757007`
- Corrected SEO theme commit: `afa372c85d73eb7463dc2eb2b446e4f76e09407f`
- Watermark/sitemap fix commit: `9ae0f847fdb7d1c91a7cf55c54b7e0671de026d5`
- Final repository commit: `9ae0f847fdb7d1c91a7cf55c54b7e0671de026d5`
- Initial retry failed the social-image gate; product/media/theme rollback completed. Evidence: external Pi backup `rollback-evidence.md`.
- Corrected retry added explicit 15P social-image metadata and deployed the fully watermarked fallback assets.

## Live state

- Product ID: `1204`, slug `svicloud-15p`
- State: published, visible, purchasable, regular `$379`, sale/effective `$288`, managed stock `0`, `notify`, `onbackorder`
- Gallery IDs: `1210`, `1211`, `1212`, `1213`, `1214`
- Gallery order: screenshot-derived AI primary, angle/rear ports, packaging, AI lifestyle, second AI lifestyle
- All five production image response bodies match committed local watermarked WebP hashes.
- Theme marketing/fallback surfaces use only watermarked assets; old `svicloud-15p-marketing-v4.webp` was removed from the repository and remote theme, and the remote `.htaccess` retires that legacy path with HTTP 410. Production homepage, Shop, Compare, PDP, and metadata surface probes found no old unwatermarked `marketing-v4` or `front` references.
- Theme remote verification: `212/212` files match.

## Verification

- Production critical localized flows: Chromium/WebKit × Traditional Chinese/Simplified Chinese, 4/4 passed.
- Production English flows: Chromium/WebKit PDP → add-to-cart → cart → checkout, 2/2 passed.
- Storefront audit: 36/36 route/viewport checks passed.
- SEO audit: 24 pages, 77 internal links, 0 issues.
- Product schema: one `$288.00 USD` BackOrder offer, no delivery-time promise.
- Social metadata: one unique `og:image` and one unique `twitter:image`, both watermarked primary image.
- Active sitemap: `/sitemap_index.xml` and all four children HTTP 200; `/wp-sitemap.xml` resolves to the active Rank Math sitemap rather than the homepage via the pinned remote `.htaccess` redirect. The pre-update/final `.htaccess` hashes and retired legacy-image rule are recorded in the external preflight evidence.
- No production offline-verifier orders/customers; no unrelated/private data or infrastructure writes.

The pre-update DB/uploads/remote-theme backups and rollback procedure remain external and hash-verified. No DB/uploads restoration was performed.
