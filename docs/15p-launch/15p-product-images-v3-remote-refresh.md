# 15P Product Images v3 Remote Refresh

Generated on 2026-08-24 after visual review found the v2 product images did not show the included remote.

## Selected assets

- `theme/svicloudtvbox-lumen/assets/images/products/svicloud-15p-primary-studio-v4-bilingual-remote-watermarked.webp`
- `theme/svicloudtvbox-lumen/assets/images/products/svicloud-15p-angle-studio-v3-remote-watermarked.webp`
- `theme/svicloudtvbox-lumen/assets/images/products/svicloud-15p-lifestyle-studio-v3-remote-watermarked.webp`
- `theme/svicloudtvbox-lumen/assets/images/products/svicloud-15p-detail-studio-v3-remote-watermarked.webp`
- `theme/svicloudtvbox-lumen/assets/images/products/svicloud-15p-marketing-v7-bilingual-remote-watermarked.webp`

The v4 primary and v7 marketing replacements add exact `SVICLOUD 小雲盒子` / `小雲盒子` branding and rebalance the product group so the 15P no longer reads too far right on mobile product surfaces.

## Direction

Keep the approved clean 15P studio styling, but show the included Bluetooth voice remote in every selected product-facing asset. Maintain consistent 1200x900 gallery dimensions, keep the 1536x1024 feature graphic format, and preserve the existing `svicloudtvbox.us` watermark.

## Source workflow

- Base product images: v2 clean studio 15P WebP assets generated for the in-stock launch.
- Remote source: `theme/svicloudtvbox-lumen/assets/images/products/svicloud-remote-control-source.svg`.
- Rendering/compositing: Playwright rendered the remote source to transparent PNG; `ffmpeg` composited it into the v2 assets and encoded optimized WebP outputs.

## Constraints

- No preorder, coming soon, or shipping-date language.
- No app/channel/service screenshots inside the product image.
- The remote must be visible in each selected 15P product-facing image.
- The box shape, lighting, watermark treatment, and ecommerce framing should stay consistent across Shop, Compare, PDP, feature page, and Merchant feed surfaces.
