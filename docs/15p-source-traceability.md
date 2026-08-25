# SVICLOUD 15P source traceability

## Authority and extraction

The 15P hardware and media content is derived from the two read-only source files below. Included-app status is supplemented by direct supplier confirmation relayed by the site owner.

| Source | SHA-256 | Extracted evidence |
|---|---|---|
| `D:\168mediagroup\SVICloudTVBox.us\15P\15P Specs最新.pdf` | `76e421663e5d45076c53f13b12a6cc24cef98639ef3d1c50b87ccc31d6e41108` | 2 pages; page 1 contains the specification table and four embedded product/manual images; page 2 is blank. |
| `D:\168mediagroup\SVICloudTVBox.us\15P\15P PDF.pptx` | `3388cab020d0f9f9a3bc4797241c2ad57b30972109d89588d7575fe9bc0dd85b` | 27 slides; each slide is one 1422×800 raster marketing image. |
| Direct supplier confirmation relayed by site owner, 2026-08-17 | N/A | Confirms that the 15P includes the Kids and Karaoke apps. |
| Site-owner commerce decision, 2026-08-17; stock update relayed by site owner, 2026-08-24 | N/A | Sets U.S. regular price `$379.00`, sale price `$288.00`, and current in-stock ordering. |
| Public SVICLOUD 15P listing fetched 2026-08-23 | `https://us.svicloudnet.com/products/svicloud-15p?shpxid=32df5470-b562-4243-a46e-105a1d00f6e4` | Publicly describes Android 14, S905Y5, Wi-Fi 6, Bluetooth 5.4, HDR10+/HDR10/HLG, AV1 up to 4K@60fps, Filmax local playback, NAS-style local sources, and air-mouse remote. |
| Site-owner launch/app positioning, 2026-08-23 | N/A | Sets the 15P promotional page angle around app-download flexibility and Yogurt TV Go guidance; do not frame this as a guaranteed app catalogue or content promise. |

Extraction used the official Anthropic `pdf` and `pptx` skills, PyMuPDF/PDFPlumber, OOXML inspection, and image hashing. The source files were not modified. The parent folder contained 89 media files (85 images and four MP4 videos), but none had an authoritative 15P identity; no image had an exact hash match with the PDF/PPTX assets. The complete parent-folder path/dimension/duration/size/hash/disposition inventory is inspectable in [`docs/15p-parent-media-inventory.md`](15p-parent-media-inventory.md), and all 27 embedded PPTX media parts—with slide mapping, hashes, content risks, and disposition—are listed in [`docs/15p-pptx-media-inventory.md`](15p-pptx-media-inventory.md). Therefore, five watermarked 15P gallery derivatives are delivered under descriptive theme filenames; no unwatermarked source image is delivered, and no unmodified flattened PPTX slide is published. The Shop/pricing marketing creative is an AI-generated derivative grounded in approved product references and carries the same watermark.

## Canonical product state

- **Product:** SVICLOUD 15P TV Box / 小雲 15P 電視盒 / 小云 15P 电视盒.
- **Slug:** `svicloud-15p`.
- **Catalog state:** Published and catalog-visible.
- **Commerce state:** Regular `$379.00`, sale/effective `$288.00`, in stock, normal ordering enabled, and purchasable.
- **Claim boundary:** The PDF/PPTX do not state price, release timing, inventory, shipping, warranty, or returns. Price and in-stock state are site-owner business decisions; no 15P-specific shipping-speed, delivery-date, or warranty claim is added.
- **Primary positioning:** Android 14 TV box with Amlogic S905Y5, 4 GB DDR3 memory, 64 GB eMMC storage, dual-band Wi-Fi 6, Bluetooth 5.4, and 4K HDR/AV1 playback.

## Accepted claims

| Published claim | Source evidence | Usage notes |
|---|---|---|
| Amlogic S905Y5 quad-core ARM Cortex-A55 CPU | PDF p.1, Hardware → CPU; PPTX slide 4 | Use the exact chip/family. Do not publish performance comparisons. |
| ARM Mali-G31 MP2 GPU | PDF p.1, Hardware → GPU | Technical specification only. |
| 4 GB DDR3 RAM | PDF p.1, Hardware → RAM; PPTX slide 27 says `4GB+64GB` | Normalize source `4G DDR3` to standard capacity notation. |
| 64 GB eMMC storage | PDF p.1, Hardware → ROM; PPTX slide 27 says `64` and `4GB+64GB` | Do not imply usable capacity after system files. |
| Dual-band 2.4/5 GHz Wi-Fi 6, 2T2R | PDF p.1, Hardware → Wifi; PPTX slides 5 and 27 | Avoid reproducing the PDF typo `802.1.1`; slides independently support the normalized claim. |
| Bluetooth 5.4 | PDF p.1, Hardware → BT; PPTX slide 21 | Product connectivity claim. |
| Android 14 | PDF p.1, Software → O.S.; PPTX slide 27 | No upgrade-policy claim. |
| HDR10+, HDR10, and HLG processing | PDF p.1, Software → HDR; PPTX slide 6 supports HDR10 | Detailed specification can list all three; short copy uses `4K HDR`. |
| AV1, VP9, H.265/HEVC, and H.264 decoding up to the source-listed limits | PDF p.1, Software → Video/Picture CODEC; PPTX slide 7 supports AV1 | Short copy says `AV1 decoding`; detailed table retains source-specific limits. |
| Up to 4K × 2K at 60 fps for AV1, VP9, and H.265/HEVC | PDF p.1, Software → Video/Picture CODEC | Do not generalize this limit to every codec. |
| HDMI 2.1, two USB 2.0 ports, RJ45 Ethernet, optical audio, and Type-C 5V/2A power | PDF p.1, Ports; PPTX slide 3 supports Type-C | Use in specification table and ports gallery caption. |
| Standby LED red; working LED white | PDF p.1, Hardware → LED | Detailed specification only. |
| Bluetooth voice air-mouse remote | PDF p.1 lists one BT remote; PPTX slide 14 names the Bluetooth voice air-mouse remote | Do not imply compatibility with other models. |
| Filmax local playback for owned files and NAS-style sources | Public SVICLOUD 15P listing fetched 2026-08-23 | Use for the 15P promotional page as a local-player feature only. Do not imply streaming-service content is included. |
| App-download / Yogurt TV Go positioning | Site-owner launch/app positioning, 2026-08-23 | Use as guidance and sales/SEO positioning. Route setup, search terms, passwords, and content-section questions to the app guide/support; do not guarantee specific content or continuing app availability. |
| HDMI CEC TV control | PPTX slide 19 | Describe as HDMI CEC control, not universal compatibility with every TV. |
| Included gift box, AC adapter, HDMI cable, BT remote, and user manual | PDF p.1, Accessories | PPTX slide 26 labels pictured adapters as optional; do not promise a particular plug variant or extra adapter. |
| Full-format playback including BDMV/ISO | PPTX slide 10 | Secondary PDP feature only; do not claim DRM bypass or content entitlement. |
| Subtitle/audio-track/downmix/3D/audio-offset settings | PPTX slide 12 | Group as playback controls rather than five headline claims. |
| Small-screen casting to TV | PPTX slide 13 | Describe generically; no named protocol is stated. |
| Kids app included | Direct supplier confirmation relayed by site owner, 2026-08-17 | Inclusion only; do not promise a catalogue, subscription, region, or continuing service availability. |
| Karaoke app included | Direct supplier confirmation relayed by site owner, 2026-08-17; karaoke use is also shown on PPTX slide 22 | Inclusion only; no included microphone, song catalogue, subscription, region, or continuing service-availability claim. |

## Excluded or qualified source material

| Source material | Decision |
|---|---|
| `25S` boot claim on PPTX slide 8 | Excluded. It is a benchmark-like claim with no conditions or independent specification-table support. |
| App/service logos and compatibility on slides 9, 23, and 24 | Excluded from product promises. Logos do not establish included subscriptions, regional availability, licensing, or continuing compatibility. |
| Smart poster wall and UI screenshots on slide 11 | Excluded from core claims because firmware/version behavior is not specified. |
| Voice-assistant languages on slides 16–17 | Qualified to `voice remote` only; language and service availability are not guaranteed. |
| Network-function list (`Skype`, `Picasa`, `Flicker`, `Facebook`, etc.) in PDF p.1 | Excluded as outdated and unsupported by release/region details. |
| `Professional player-grade` and `premium` on PPTX slides | Excluded as unmeasured marketing language. |
| Qualitative/temporal paraphrases (`S905Y5 power`, `Android 14 performance`, `current playback formats`, `current wireless`, `current platform`, `practical connections`) | Excluded from published copy. The site lists the underlying processor, OS, codec, wireless, and port facts directly without qualitative framing. |
| Wi-Fi/HDR/remote slide artwork (slides 5, 6, and 14) | Excluded from storefront media. Embedded copy adds unverified coverage, transmission, color-performance, restoration, and operating-experience claims; the remote slide also includes service/content imagery. |
| Remaining PPTX raster slide artwork | Not published as storefront media. Even when a technical label is accepted, the flattened image may combine it with marketing, app/service, lifestyle, or availability implications that cannot be separated reliably. |
| Accessories image on slide 26 | Not used because pictured adapter variants are labeled optional. The PDF package list controls included-item copy. |
| User-manual cover embedded in PDF p.1 | Not used as product artwork; it mentions 15P, 15PLUS, and 15 PRO rather than this SKU alone. |

## Selected image map

All output images are optimized WebP files; source PDF/PPTX files remain external and untracked. No flattened PPTX marketing slide is shipped. The generated marketing creative uses only accepted Android 14, Wi-Fi 6, Bluetooth 5.4, 4 GB + 64 GB, 4K HDR, AV1, and in-stock claims. It contains no embedded CTA; interactive actions remain accessible HTML controls outside the image.

| Theme asset | Source | Purpose and alt text |
|---|---|---|
| `assets/images/products/svicloud-15p-marketing-v7-bilingual-remote-watermarked.webp` | Bilingual image-generation edit of the approved v6 15P feature graphic, keeping the remote visible and adding exact `小雲盒子` branding. | Watermarked homepage hero, Shop card, pricing card, feature page hero, and social metadata. Alt: `SVICLOUD 小雲盒子 15P Android 14 feature graphic with Bluetooth voice remote`. |
| `assets/images/products/svicloud-15p-primary-studio-v4-bilingual-remote-watermarked.webp` | Bilingual image-generation edit of the approved v3 15P primary studio image, rebalanced for mobile PDP/card framing and adding exact `SVICLOUD 小雲盒子` branding. | Primary WooCommerce/PDP/Compare/Google Merchant image. Alt: `SVICLOUD 小雲盒子 15P TV Box front studio view with Bluetooth voice remote`. |
| `assets/images/products/svicloud-15p-angle-studio-v3-remote-watermarked.webp` | Local composite from the approved v2 15P angle studio image plus rendered remote source; refresh notes in `docs/15p-launch/15p-product-images-v3-remote-refresh.md` | Gallery angle/detail image. Alt: `SVICLOUD 15P TV box angled view with Bluetooth voice remote`. |
| `assets/images/products/svicloud-15p-lifestyle-studio-v3-remote-watermarked.webp` | Local composite from the approved v2 15P lifestyle studio image plus rendered remote source; refresh notes in `docs/15p-launch/15p-product-images-v3-remote-refresh.md` | Gallery media-console studio image. Alt: `SVICLOUD 15P TV Box on media console with Bluetooth voice remote`. |
| `assets/images/products/svicloud-15p-detail-studio-v3-remote-watermarked.webp` | Local composite from the approved v2 15P detail studio image plus rendered remote source; refresh notes in `docs/15p-launch/15p-product-images-v3-remote-refresh.md` | Gallery close product detail image. Alt: `SVICLOUD 15P TV Box front detail view with Bluetooth voice remote`. |
| `assets/images/products/svicloud-15p-primary-ai-watermarked.webp` | Supplied screenshot-derived AI primary image | Legacy watermarked primary/front image retained for history and rollback. Alt: `SVICLOUD 15P TV Box front view with watermark`. |
| `assets/images/products/svicloud-15p-front.webp` | PDF p.1 embedded front product photo, watermark derivative | Watermarked fallback/identity image. Alt: `SVICLOUD 15P TV box front view`. |
| `assets/images/products/svicloud-15p-angle.webp` | PDF p.1 embedded angled product photo, watermark derivative | Watermarked gallery/fallback image. Alt: `SVICLOUD 15P TV box angled view showing rear ports`. |

## Localized content contract

### English

- **Title:** SVICLOUD 15P TV Box
- **Badge/status:** In stock now
- **Lead:** SVICLOUD 15P runs Android 14 on an Amlogic S905Y5 processor with 4 GB DDR3 memory, 64 GB eMMC storage, dual-band Wi-Fi 6, Bluetooth 5.4, and 4K HDR playback.
- **Feature labels:** `Amlogic S905Y5 + Android 14`; `4 GB DDR3 + 64 GB eMMC`; `Wi-Fi 6 + Bluetooth 5.4`; `4K HDR + AV1 decoding`.
- **CTA:** Buy 15P
- **Availability:** `$288.00` sale / `$379.00` regular; in stock now.

### Traditional Chinese

- **Title:** 小雲 15P 電視盒
- **Badge/status:** 現貨供應
- **Lead:** 全新小雲 15P 搭載 Android 14、Amlogic S905Y5 處理器、4 GB DDR3 記憶體、64 GB eMMC 儲存空間、雙頻 Wi-Fi 6、藍牙 5.4 與 4K HDR 播放。
- **Feature labels:** `Amlogic S905Y5 + Android 14`; `4 GB DDR3 + 64 GB eMMC`; `Wi-Fi 6 + 藍牙 5.4`; `4K HDR + AV1 解碼`.
- **CTA:** 購買 15P
- **Availability:** 特價 `US$288` / 原價 `US$379`；現貨供應。

### Simplified Chinese

- **Title:** 小云 15P 电视盒
- **Badge/status:** 现货供应
- **Lead:** 全新小云 15P 搭载 Android 14、Amlogic S905Y5 处理器、4 GB DDR3 内存、64 GB eMMC 存储空间、双频 Wi-Fi 6、蓝牙 5.4 与 4K HDR 播放。
- **Feature labels:** `Amlogic S905Y5 + Android 14`; `4 GB DDR3 + 64 GB eMMC`; `Wi-Fi 6 + 蓝牙 5.4`; `4K HDR + AV1 解码`.
- **CTA:** 购买 15P
- **Availability:** 特价 `US$288` / 原价 `US$379`；现货供应。

## Rendered-copy map

Every 15P-facing theme key is restricted to a source-backed fact or the confirmed commerce state:

| Surface / key family | Published content | Evidence above |
|---|---|---|
| Homepage `frontpage.hero` and `frontpage.pricing.cards.15p` | Accepted hardware facts; `$288/$379`; In stock now; Buy 15P | Accepted claims; site-owner commerce decision; Canonical product state |
| Shop `shop.cards.15p` | Same hardware/commerce facts with v4 artwork and aligned card | Accepted claims; Selected image map; Canonical product state |
| PDP `products.svicloud-15p` | Detailed hardware/package list; sale/regular price; normal in-stock ordering; no delivery-date or warranty promise | Accepted claims; site-owner commerce decision; excluded/qualified material |
| 15P PDP FAQ header and product-specific footer | Confirmed platform plus price and in-stock state | Canonical product state and implementation guardrail 3 |
| Compare `compare.products.15p` and `compare.comparison.rows.*.p15p` | Direct specification values and `$288/$379` in-stock state; no speed or superiority claim | Accepted claims; comparison qualification; commerce decision |
| Header/meta | Localized EN/繁/简 Shop, Compare, PDP, and homepage metadata with authorized-dealer intent, `$288/$379`, and InStock state | Canonical product state, approved SEO decision, and accepted claims |

Labels such as `Confirmed platform`, `Core specifications`, and `At a glance` organize evidence; they do not assert performance or suitability. The site makes no “newest,” “best,” “faster,” everyday-use, room-fit, or model-superiority claim for 15P.

## Implementation guardrails

1. A local fixture refresh must recreate the supplemental 15P product with regular `379`, sale/effective `288`, unmanaged stock, backorders `no`, stock status `instock`, and five watermarked source/gallery images.
2. Homepage, Shop, Compare, PDP, cart/checkout, metadata, and Product/Offer JSON-LD must agree on the price and InStock state.
3. Normal checkout/payment/shipping-rate/cancellation/return behavior applies, but no 15P-specific shipping speed, dispatch date, delivery date, or warranty promise may be added.
4. The v4 artwork must not say “Coming Soon”; the shipped version uses `IN STOCK NOW` and surrounding UI uses localized in-stock action/status copy.
5. Every hardware/app storefront claim must map to the accepted-claims table above; otherwise remove it or ask for clarification.
