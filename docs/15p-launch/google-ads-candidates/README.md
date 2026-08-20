# 15P Google Ads image candidates — approval required

These are **derived read-only campaign candidates**, not production replacements. They were generated from existing approved/watermarked 15P theme assets by crop/scale/pad only. No AI-generated image was needed for this first candidate set, and no source asset was overwritten.

## Candidate manifest

| Candidate | Type / target | Source | Method | Status | Review notes | SHA-256 |
|---|---|---|---|---|---|---|
| `APPROVAL-landscape-lifestyle-01.webp` | Reuse-derived, 1200×628, 1.91:1 | `theme/svicloudtvbox-lumen/assets/images/products/svicloud-15p-lifestyle-clean-2-watermarked.webp` | Lanczos scale to 1231×628, center crop to 1200×628 | **Approval required** | Single product/lifestyle composition; no embedded sales copy; watermark/domain remains visible. | `fb79bab877e472a1fd4c146b20cb04a7c6156c86a6101cfe9af6cf669f2504da` |
| `APPROVAL-landscape-front-01.webp` | Reuse-derived, 1200×628, 1.91:1 | `theme/svicloudtvbox-lumen/assets/images/products/svicloud-15p-front.webp` | Center crop from 1200×738 to 1200×628 | **Approval required** | Product-focused; no embedded claim copy; watermark/domain remains visible. | `d1f1261918d371f028bb646f0be422a22eac81cb2b3bb6228ddc11476fbc86a7` |
| `APPROVAL-square-front-01.webp` | Reuse-derived, 1200×1200, 1:1 | `theme/svicloudtvbox-lumen/assets/images/products/svicloud-15p-front.webp` | Preserve full product; scale to 1200×738 and pad with white to 1200×1200 | **Approval required** | Full product remains visible; white padding may need policy/performance review; watermark/domain remains visible. | `f41cd0b67cecad03d8c01dca887cd43b813b826bae0e01aacb411dffcd369a7d` |
| `APPROVAL-square-packaging-01.webp` | Reuse-derived, 1200×1200, 1:1 | `theme/svicloudtvbox-lumen/assets/images/products/svicloud-15p-packaging-mockup-watermarked.webp` | Center crop from 1200×900 to 900×900 and scale to 1200×1200 | **Approval required** | Packaging/product identity; inspect for visual-composite preference before use; watermark/domain remains visible. | `911f9384c1003097d7f33c441708dd7ecdadb76dc3d1ffc62354b4c5cee2865e` |
| `APPROVAL-vertical-package-01.webp` | Reuse-derived, 900×1600, 9:16 | `theme/svicloudtvbox-lumen/assets/images/products/svicloud-15p-package.webp` | Scale to 900×1348 and pad top/bottom to 900×1600 with brand-dark background | **Approval required** | Full vertical package/product composition; padded background and watermark/domain need policy review. | `fbe376aff69ba8c89a3385b5b25db97df777e70615f4ccdb908bdb0d2be8f15a` |

## Shared validation

- File type: WebP; all candidates remain below the 5 MB Google Ads image limit.
- Dimensions: exact Google responsive-display target dimensions.
- Localization: no new embedded EN/zh-TW/zh-CN claims were introduced. Campaign headlines/descriptions must be localized outside the images and reviewed separately.
- Brand: existing SVICLOUD/SVI 小雲 product identity and watermark are preserved; no legacy `SVI.studio` asset was reused.
- Claim safety: candidates contain no new price, availability, shipping, warranty, compatibility, speed, subscription, catalogue, or superiority claim. `COMING SOON` exists only in the original v4 artwork, which is not used in this derived set.
- Duplication: the duplicate `svicloud-15p-angle.webp` / `svicloud-15p-angle-watermarked.webp` pair was not used.
- Approval gate: do not upload, publish, or replace existing campaign assets until the user approves the candidates and Google Ads account mapping is available.

## Performance Max candidate package

The enabled WordPress campaign inventory contains 4 `marketing_image`, 6 `square_marketing_image`, and 4 `portrait_marketing_image` slots. The original source set has no exact square or 4:5 portrait family, so the following **approval-required** crops are limited to that documented ratio gap. They are derived from existing approved 15P source media only; no source or WordPress Media Library record was changed.

| Candidate | Target | Approved source | Method | Size | SHA-256 | Review status |
|---|---|---|---|---:|---|---|
| `APPROVAL-pmax-landscape-primary-01.webp` | marketing image | `svicloud-15p-primary-ai-watermarked.webp` | Center crop to 1.91:1 | 32 KB | `87f5adcc740cb51a45fc1b76045c9e4731d04d395688c3092467964ae774f09b` | Approval required; product identity clear |
| `APPROVAL-pmax-landscape-packaging-01.webp` | marketing image | `svicloud-15p-packaging-mockup-watermarked.webp` | Center crop to 1.91:1 | 9 KB | `95a696ef83f634054a9e5d140b138bc5fb8fe2ecf8e8f00f08964edde57c7123` | Approval required; 15P packaging visible |
| `APPROVAL-pmax-square-primary-01.webp` | square marketing image | `svicloud-15p-primary-ai-watermarked.webp` | Preserve full product; white pad to 1:1 | 38 KB | `0d0640665d1b172ee32488110f718b713fb3ccdd6af6326cfc3a94b6b798c9d8` | Approval required; padding review |
| `APPROVAL-pmax-square-angle-01.webp` | square marketing image | `svicloud-15p-angle-watermarked.webp` | Preserve full product; white pad to 1:1 | 41 KB | `e404cbf649a269ed7e97937e8e36e078ac8a46bb46283b32ebfd54649f2dc5f9` | Approval required; hardware identity clear |
| `APPROVAL-pmax-square-lifestyle-01.webp` | square marketing image | `svicloud-15p-lifestyle-clean-2-watermarked.webp` | Preserve full composition; blue pad to 1:1 | 20 KB | `077365f64d2a3e28f9bcfb84df9874b1f8d1a06bb3e13e9940787fbcbcb4832a` | Approval required; large pad review |
| `APPROVAL-pmax-square-package-01.webp` | square marketing image | `svicloud-15p-package.webp` | Preserve package/product; dark pad to 1:1 | 17 KB | `033a29e6047a9737f11a717f7edd78ae9f8f4bbb7b4c50a598d5e52fb8bf0e51` | Approval required; packaging text review |
| `APPROVAL-pmax-portrait-package-01.webp` | portrait marketing image | `svicloud-15p-package.webp` | Preserve package/product; dark pad to 4:5 | 16 KB | `16d30333042b4f3a0005f8b145e5a41a133d543fb3497905cbc67d14b463e3c4` | Approval required; packaging text review |
| `APPROVAL-pmax-portrait-packaging-01.webp` | portrait marketing image | `svicloud-15p-packaging-mockup-watermarked.webp` | Preserve package/product; dark pad to 4:5 | 13 KB | `d25001dc633f98b3f46b8f1113349193c1ee775fd9076f6eab79228504fd0581` | Approval required; composite/padding review |
| `APPROVAL-pmax-portrait-angle-01.webp` | portrait marketing image | `svicloud-15p-angle-watermarked.webp` | Preserve full product; white pad to 4:5 | 28 KB | `1bfeeb77ffd46745ff6e76354afbde873c48361e08069078347d0f740c649d7c` | Approval required; hardware identity clear |
| `APPROVAL-pmax-portrait-primary-01.webp` | portrait marketing image | `svicloud-15p-primary-ai-watermarked.webp` | Preserve full product; white pad to 4:5 | 26 KB | `75a62f5f3058d0f82abd9464ade8291d70d85f3c00756163f90abc99a00256e4` | Approval required; product identity clear |

All ten PMax candidates are WebP, exact `1200×628`, `1200×1200`, or `960×1200`, and below 5 MB. Visual review confirms the product/package remains readable, SVICLOUD/domain watermarks remain intact, and no new text or claims were introduced. Embedded `15P`/brand text comes only from the approved source artwork. No EN/zh-TW/zh-CN localization is embedded; campaign text remains untouched.

The earlier `APPROVAL-vertical-package-01.webp` is a 9:16 responsive-display candidate and is **not** part of the PMax 4:5 replacement set.
