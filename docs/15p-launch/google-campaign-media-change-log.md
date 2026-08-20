# WordPress Marketing → Google Campaigns — 15P Media Change Log

## Scope and access

- Target: WordPress REST representation of **Marketing → Google Campaigns** (wc/gla/ads/*), authenticated as WP user ID 2.
- Capture: 2026-08-20T02:49:54.166Z.
- Operation: **GET-only before-state inventory; no write request made.**
- Connected Google Ads account: 8488975309 — SvicloudTVbox.us Google Ads account; REST reports connected and has_access: true.
- Other account visible: 3645865182 — 3W Distributing LLC; not touched.

## Campaign before-state

| Campaign ID | Name | Status | Type | Amount field | Country | Start date | Targeted locations | Asset group state |
|---:|---|---|---|---:|---|---|---|---|
| 23167444956 | SviCloud TvBox | removed | search | 5 |  | 2025-10-23 | US | none |
| 23405072796 | Campaign 2025-12-30 03:30:18 | enabled | performance_max | 10 | US | 2025-12-29 | US | 6652700113 |

The only enabled campaign is Performance Max '23405072796' (Campaign 2025-12-30 03:30:18). Its asset group is '6652700113', with final URL 'https://svicloudtvbox.us/product/svicloud-10p-plus/' and display path 'product / 10p-plus'. The final URL is the 10P+ product page, not the 15P page.

## Media-slot before-state

The API exposes no human-readable asset names or locale fields for these slots. Exact identifiers are therefore recorded as field type + Google asset ID + content URL. Locale/status interpretation: campaign country/target is US; no locale-specific media variants are exposed; each content URL returned HTTP 200 during capture; approval status was not supplied by the API.

| Slot | Field type | Asset ID | Content URL / value | Retrieval | Format | Dimensions | SHA-256 |
|---:|---|---:|---|---:|---|---:|---|
| 1 | marketing_image | 319737354296 | https://tpc.googlesyndication.com/simgad/1220387759834189413 | 200 | image/jpeg | 1920x1004 | baba8c7d6ad303e3a3cd6b52c83cecfd2b94f81742db988bce15b13ec88eb8f4 |
| 2 | marketing_image | 319821075442 | https://tpc.googlesyndication.com/simgad/2288451649640054486 | 200 | image/png | 1605x837 | b5e261a12d033dee6edffe2a2a14ada8a4d982ce24c91c91f89717fdf8ceffd9 |
| 3 | marketing_image | 319863498987 | https://tpc.googlesyndication.com/simgad/842237743274211825 | 200 | image/png | 1601x835 | dc7891f120a1f20799cfcfb68418576253c36efe27a5c2cae77f3cf23150271b |
| 4 | marketing_image | 319863552900 | https://tpc.googlesyndication.com/simgad/3415367161799774919 | 200 | image/jpeg | 3840x2009 | 08f4e941b8ac25b80d3b6c00d3be80144c1eaef229f2c3f2b6ecae9b41b3cd54 |
| 5 | square_marketing_image | 319821075550 | https://tpc.googlesyndication.com/simgad/4234018302966857589 | 200 | image/jpeg | 2160x2160 | c2687211e42469bec7920f1cb365f1a5201a5da12e651288b7e21ca31bb5965d |
| 6 | square_marketing_image | 319861574169 | https://tpc.googlesyndication.com/simgad/1210192345753587850 | 200 | image/png | 1024x1024 | 5053516096e8170208ce3779a4a1bcc08355edf1c2719031ae5395bea1c807ff |
| 7 | square_marketing_image | 319863498885 | https://tpc.googlesyndication.com/simgad/9348936224264725852 | 200 | image/jpeg | 1075x1080 | 17e46ec0d11e2d27cc16f9671b847a020d2f842e1251646e2d76a71e51262398 |
| 8 | square_marketing_image | 319863498918 | https://tpc.googlesyndication.com/simgad/14355712376652179045 | 200 | image/png | 900x900 | d092d0076b53630d601f96ad845cd6b1e052db9cde4a423e6380b4c8a4a59f18 |
| 9 | square_marketing_image | 319863499065 | https://tpc.googlesyndication.com/simgad/5976843453915853508 | 200 | image/jpeg | 2160x2160 | 175a896f1001eaa752015a9ff3e437f36477569d5f5310a2c9c36489180bb3dc |
| 10 | square_marketing_image | 319863552765 | https://tpc.googlesyndication.com/simgad/17181115981823753011 | 200 | image/png | 897x898 | 045ca52f6e56644fa1282028bffd691d9a3cc79b03b928fd0e6ea0c098df1ae3 |
| 11 | portrait_marketing_image | 319863498900 | https://tpc.googlesyndication.com/simgad/9617637448164695674 | 200 | image/jpeg | 864x1080 | 628410c8c152372ae5bccfb57656c5cc2faf5080a59772114dfd1778eddc95d7 |
| 12 | portrait_marketing_image | 319863498981 | https://tpc.googlesyndication.com/simgad/2041321646211686433 | 200 | image/png | 721x898 | b3cb83062c208b787aa4d8b7f1cfa8d00730bbdb953eba71b681cb08a508bb24 |
| 13 | portrait_marketing_image | 319863570324 | https://tpc.googlesyndication.com/simgad/12419596345874421717 | 200 | image/png | 720x900 | f3066e82179b2b6c754c9b72b1e4d0848b6b9aab48e61770ffe7fb3ea705d290 |
| 14 | portrait_marketing_image | 319863571674 | https://tpc.googlesyndication.com/simgad/6349957929529685924 | 200 | image/jpeg | 1728x2160 | b09e8330a74afe31145effb669a741cae250bd13753908b495d5fec1bc5f3ea0 |
| 15 | youtube_video | 320553491838 | Qj-TtrB_-Pg | present | text/value | n/a | n/a |
| 16 | headline | 346941188858 | Free US Shipping | present | text/value | n/a | n/a |
| 17 | headline | 346941188870 | No Monthly Fees | present | text/value | n/a | n/a |
| 18 | headline | 405360999389 | US Seller Support | present | text/value | n/a | n/a |
| 19 | headline | 405361002740 | SVICloud TV Box US | present | text/value | n/a | n/a |
| 20 | headline | 405361002746 | Chinese TV Box USA | present | text/value | n/a | n/a |
| 21 | long_headline | 405461285677 | SVICloud TV boxes with Korean, Chinese, Japanese and USA content, shipped fast from the US | present | text/value | n/a | n/a |
| 22 | description | 405461285680 | SVICloud 9P and 10P boxes in stock with access to popular Asian and US content apps. | present | text/value | n/a | n/a |
| 23 | description | 405484660595 | Fast US delivery, secure checkout, trusted support | present | text/value | n/a | n/a |
| 24 | description | 405531691566 | Shop SVICloud TV boxes with Korean, Chinese, Japanese and USA apps. Free US shipping. | present | text/value | n/a | n/a |
| 25 | business_name | 319695985640 | SvicloudTVbox.us | present | text/value | n/a | n/a |
| 26 | logo | 319736868125 | https://tpc.googlesyndication.com/simgad/8025828284151045361 | 200 | image/png | 160x160 | a6a3d6709460d047d9a44dd8fe1b64d69d198cf3df694522be5401bcc058328e |
| 27 | logo | 319861552419 | https://tpc.googlesyndication.com/simgad/9221213205531827908 | 200 | image/png | 1024x1024 | be1d301d1e3adbfacb0065a9494bd00509201c1470c367fcf7c80f200cc4cf29 |
| 28 | logo | 319861574169 | https://tpc.googlesyndication.com/simgad/1210192345753587850 | 200 | image/png | 1024x1024 | 5053516096e8170208ce3779a4a1bcc08355edf1c2719031ae5395bea1c807ff |
| 29 | logo | 334074662513 | https://tpc.googlesyndication.com/simgad/10386174255467216054 | 200 | image/png | 32x32 | b9d4158502b56460610d403f776623c468403aa6d00e63fddf2210f6743e2877 |

### Media counts

| Field type | Count | Current status |
|---|---:|---|
| marketing_image | 4 | Present in enabled asset group; no 15P identity observed in before-state review |
| square_marketing_image | 6 | Present in enabled asset group; no 15P identity observed in before-state review |
| portrait_marketing_image | 4 | Present in enabled asset group; no 15P identity observed in before-state review |
| logo | 4 | Present in enabled asset group; no 15P identity observed in before-state review |

## Non-media fields preserved as protected before-state

These values were read and are outside the permitted change scope. They must remain byte/semantic-equivalent after save:

| Field type | Asset IDs / current values |
|---|---|
| headline | ID 346941188858: Free US Shipping<br>ID 346941188870: No Monthly Fees<br>ID 405360999389: US Seller Support<br>ID 405361002740: SVICloud TV Box US<br>ID 405361002746: Chinese TV Box USA |
| long_headline | ID 405461285677: SVICloud TV boxes with Korean, Chinese, Japanese and USA content, shipped fast from the US |
| description | ID 405461285680: SVICloud 9P and 10P boxes in stock with access to popular Asian and US content apps.<br>ID 405484660595: Fast US delivery, secure checkout, trusted support<br>ID 405531691566: Shop SVICloud TV boxes with Korean, Chinese, Japanese and USA apps. Free US shipping. |
| business_name | ID 319695985640: SvicloudTVbox.us |
| youtube_video | ID 320553491838: Qj-TtrB_-Pg |

## Approved 15P source inventory for candidate selection

These approved 15P media records already exist in the WordPress Media Library and were read through REST; no new Media Library record was created.

| WP media ID | Existing title | Source URL | Dimensions | Intended use |
|---:|---|---|---:|---|
| 1201 | SVICLOUD 15P TV box front view | https://svicloudtvbox.us/wp-content/uploads/2026/08/svicloud-15p-front.webp | 1280×788 | Product identity / landscape source |
| 1202 | SVICLOUD 15P angled view | https://svicloudtvbox.us/wp-content/uploads/2026/08/svicloud-15p-angle.webp | 1280×872 | Product identity / secondary source |
| 1203 | SVICLOUD 15P packaging | https://svicloudtvbox.us/wp-content/uploads/2026/08/svicloud-15p-package.webp | 854×1280 | Vertical packaging source |
| 1210 | SVICLOUD 15P primary AI product image | https://svicloudtvbox.us/wp-content/uploads/2026/08/svicloud-15p-primary-ai-watermarked.webp | 1200×740 | Product identity / landscape source |
| 1211 | SVICLOUD 15P angled watermarked view | https://svicloudtvbox.us/wp-content/uploads/2026/08/svicloud-15p-angle-watermarked.webp | 1200×818 | Product identity / secondary source |
| 1212 | SVICLOUD 15P watermarked packaging | https://svicloudtvbox.us/wp-content/uploads/2026/08/svicloud-15p-package-watermarked.webp | 1200×1798 | Vertical packaging source |
| 1216 | SVICLOUD 15P watermarked packaging mockup | https://svicloudtvbox.us/wp-content/uploads/2026/08/svicloud-15p-packaging-mockup-watermarked.webp | 1200×900 | Packaging source |
| 1217 | SVICLOUD 15P clean AI lifestyle image | https://svicloudtvbox.us/wp-content/uploads/2026/08/svicloud-15p-lifestyle-clean-watermarked.webp | 1200×444 | Landscape lifestyle source |
| 1218 | SVICLOUD 15P second clean AI lifestyle image | https://svicloudtvbox.us/wp-content/uploads/2026/08/svicloud-15p-lifestyle-clean-2-watermarked.webp | 1200×612 | Landscape lifestyle source |

Existing approved source files were checked for format, dimensions, readability, visible product identity, watermark/brand consistency, and source-traceability claim boundaries. No localized image-text variants are present; the current Google asset group is US-targeted and exposes no locale-specific slots.

## Provisional replacement mapping — not applied

The following mapping preserves field type and slot count for asset group `6652700113`. Existing asset IDs are delete targets; candidate files are create payloads. The REST controller deletes only the supplied asset IDs and creates only supplied `content` assets. No payload has been sent.

| Original field type | Original asset ID | Proposed candidate | Approved source WP media | Rationale / validation |
|---|---:|---|---:|---|
| marketing_image | 319737354296 | `APPROVAL-landscape-lifestyle-01.png` | 1218 | Replace legacy landscape with clean 15P lifestyle; exact 1200×628 candidate. |
| marketing_image | 319821075442 | `APPROVAL-landscape-front-01.png` | 1201 | Replace legacy landscape with clear 15P front view; exact 1200×628 candidate. |
| marketing_image | 319863498987 | `APPROVAL-pmax-landscape-primary-01.png` | 1210 | Documented crop gap; approved primary source cropped to exact 1200×628. |
| marketing_image | 319863552900 | `APPROVAL-pmax-landscape-packaging-01.png` | 1216 | Documented crop gap; approved 15P package mockup cropped to exact 1200×628. |
| square_marketing_image | 319821075550 | `APPROVAL-square-front-01.png` | 1201 | Replace legacy square with full 15P front view; exact 1200×1200 candidate. |
| square_marketing_image | 319861574169 | `APPROVAL-square-packaging-01.png` | 1216 | Replace legacy square with 15P package/product identity; exact 1200×1200 candidate. |
| square_marketing_image | 319863498885 | `APPROVAL-pmax-square-primary-01.png` | 1210 | Documented square-ratio gap; full approved primary source padded to exact 1200×1200. |
| square_marketing_image | 319863498918 | `APPROVAL-pmax-square-angle-01.png` | 1211 | Documented square-ratio gap; full approved angle source padded to exact 1200×1200. |
| square_marketing_image | 319863499065 | `APPROVAL-pmax-square-lifestyle-01.png` | 1218 | Documented square-ratio gap; clean approved lifestyle source padded to exact 1200×1200. |
| square_marketing_image | 319863552765 | `APPROVAL-pmax-square-package-01.png` | 1203 | Documented square-ratio gap; approved vertical package source padded to exact 1200×1200. |
| portrait_marketing_image | 319863498900 | `APPROVAL-pmax-portrait-package-01.png` | 1203 | Documented PMax 4:5 gap; approved package source padded to exact 960×1200. |
| portrait_marketing_image | 319863498981 | `APPROVAL-pmax-portrait-packaging-01.png` | 1216 | Documented PMax 4:5 gap; approved package mockup padded to exact 960×1200. |
| portrait_marketing_image | 319863570324 | `APPROVAL-pmax-portrait-angle-01.png` | 1211 | Documented PMax 4:5 gap; approved angle source padded to exact 960×1200. |
| portrait_marketing_image | 319863571674 | `APPROVAL-pmax-portrait-primary-01.png` | 1210 | Documented PMax 4:5 gap; approved primary source padded to exact 960×1200. |

Candidate validation is recorded in [`google-ads-candidates/README.md`](google-ads-candidates/README.md). All applied candidates are approval-labeled, Google Ads-compatible PNG, below 5 MB, exact PMax dimensions, readable, brand-consistent, free of new localized copy and unsupported claims. The scoped goal authorized the media-only save; no non-media field was included.

## Initial media assessment

- The enabled asset group points to 10P+ and contains legacy/current-model visuals; no approved 15P image URL is present.
- The image slots are US-targeted with no locale-specific variants exposed.
- The enabled group currently has 4 landscape, 6 square, and 4 portrait marketing-image slots, plus 4 logo slots.
- Current text assets mention 9P/10P, “in stock,” fast delivery, free shipping, apps/content, and no monthly fees. These are protected non-media fields and will not be changed by this task.
- **Recorded scope caveat:** the only enabled asset group (6652700113) still finalizes to the 10P+ product page. The final URL was deliberately preserved because this goal authorizes media fields only; no campaign or landing-page setting was changed.
- The scoped media write and fresh GET verification are recorded below.

## Scoped write record

- Write endpoint: `PUT /wp-json/wc/gla/ads/campaigns/asset-groups/6652700113`
- Write result: HTTP 200, `Successfully edited asset group.`
- Payload: 28 entries — 14 original image IDs for deletion and 14 PNG candidate URLs for creation.
- Changed field types only: `marketing_image`, `square_marketing_image`, `portrait_marketing_image`.
- Not sent: `final_url`, `path1`, `path2`, headlines, descriptions, business name, YouTube video, logos, budgets, bids, targeting, campaign settings, Merchant Center fields, or direct Google Ads settings.
- Candidate hosting: public, approval-labeled raw URLs on the feature branch `campaign/15p-google-media-assets`; Google stored the uploaded image bytes as new asset IDs. No WordPress Media Library record was created.

## After-state verification

Captured: 2026-08-20T03:16:46.243Z. Each returned Google asset URL fetched HTTP 200 and matched the candidate SHA-256, proving the intended bytes were saved.

| Field type | After slot | Original asset ID | After Google asset ID | Candidate | Google asset URL | HTTP | Format | Dimensions | SHA-256 | Source WP media |
|---|---:|---:|---:|---|---|---:|---|---:|---|---:|
| marketing_image | 1 | 319863498987 | 409998451523 | `APPROVAL-pmax-landscape-primary-01.png` | https://tpc.googlesyndication.com/simgad/147402117716147518 | 200 | image/png | 1200×628 | `ac94489f16d1bc22aec87a0e2b7a43b2c2f55298de61296f74c7aedceba00ceb` | 1210 |
| marketing_image | 2 | 319863552900 | 409998457178 | `APPROVAL-pmax-landscape-packaging-01.png` | https://tpc.googlesyndication.com/simgad/18252902111834578345 | 200 | image/png | 1200×628 | `e466881273ccffcc5fce9c05cda3a8dace00be1a177a31d3dc5ca6225c4909fd` | 1216 |
| marketing_image | 3 | 319821075442 | 410100472963 | `APPROVAL-landscape-front-01.png` | https://tpc.googlesyndication.com/simgad/5467959077247392664 | 200 | image/png | 1200×628 | `611963c20dcb9473a30639c8a1f7a406d9d5d0d67f4dcceab03442ffa735c0b9` | 1201 |
| marketing_image | 4 | 319737354296 | 410172265887 | `APPROVAL-landscape-lifestyle-01.png` | https://tpc.googlesyndication.com/simgad/11737234310690382294 | 200 | image/png | 1200×628 | `a39f52fb8dd1b7170e1bacaaf58d7b7a0507da17c03d314e54e967f3aae4ec82` | 1218 |
| square_marketing_image | 1 | 319821075550 | 410100451066 | `APPROVAL-square-front-01.png` | https://tpc.googlesyndication.com/simgad/13184373693000771228 | 200 | image/png | 1200×1200 | `f3721ffe362dbfc465208ab3f48846e350e414080da139933ca38e000216019a` | 1201 |
| square_marketing_image | 2 | 319863498918 | 410100451075 | `APPROVAL-pmax-square-angle-01.png` | https://tpc.googlesyndication.com/simgad/11093795369921765269 | 200 | image/png | 1200×1200 | `48d2b18a6a8bf2d61c8d9444af5e416d95b24d678849c6db976f657fdd015ba3` | 1211 |
| square_marketing_image | 3 | 319861574169 | 410100472972 | `APPROVAL-square-packaging-01.png` | https://tpc.googlesyndication.com/simgad/5321362270589123776 | 200 | image/png | 1200×1200 | `3903abf3da47b7c303801901a4180299458aa8c670634a8a1fd92b6331d468ec` | 1216 |
| square_marketing_image | 4 | 319863498885 | 410100472975 | `APPROVAL-pmax-square-primary-01.png` | https://tpc.googlesyndication.com/simgad/16071042488408537355 | 200 | image/png | 1200×1200 | `6f386c868dfc56b9f76c56893475b4a4bcb5afff667613ff9692946fdd7a2306` | 1210 |
| square_marketing_image | 5 | 319863499065 | 410100472981 | `APPROVAL-pmax-square-lifestyle-01.png` | https://tpc.googlesyndication.com/simgad/9592477914156833955 | 200 | image/png | 1200×1200 | `bc6f44dc60979b655164deb13e60b7a024c358be2ae8029362d8a36a7633daaf` | 1218 |
| square_marketing_image | 6 | 319863552765 | 410172265896 | `APPROVAL-pmax-square-package-01.png` | https://tpc.googlesyndication.com/simgad/4844918780074997388 | 200 | image/png | 1200×1200 | `ad386c3ef4239ea32a9ceb488b69e3784381d8081c9cb29e5d0a68904dc3b30c` | 1203 |
| portrait_marketing_image | 1 | 319863571674 | 409998451535 | `APPROVAL-pmax-portrait-primary-01.png` | https://tpc.googlesyndication.com/simgad/2959277340611706629 | 200 | image/png | 960×1200 | `c8ea1051476558be285e641a5b4bf3666e6913f02966ee8f0b62a07624525200` | 1210 |
| portrait_marketing_image | 2 | 319863498900 | 409998457187 | `APPROVAL-pmax-portrait-package-01.png` | https://tpc.googlesyndication.com/simgad/14084286220006177816 | 200 | image/png | 960×1200 | `e2ccb860c9f0302419db2d8afedc01ac80b8c58e2733094c8ee3121b84c21673` | 1203 |
| portrait_marketing_image | 3 | 319863570324 | 410100451087 | `APPROVAL-pmax-portrait-angle-01.png` | https://tpc.googlesyndication.com/simgad/3830136228050604165 | 200 | image/png | 960×1200 | `6768e532cf285c892b864971fd2feee77a36976d05e7d159f8bed37e79247d41` | 1211 |
| portrait_marketing_image | 4 | 319863498981 | 410100472993 | `APPROVAL-pmax-portrait-packaging-01.png` | https://tpc.googlesyndication.com/simgad/13531701507650271214 | 200 | image/png | 960×1200 | `541445975e70d8424ea8c0f712343c25aea7db962c8d2d15c20c3d28ec7d89f3` | 1216 |

### Scope checks

- Final URL remained `https://svicloudtvbox.us/product/svicloud-10p-plus/`; display path remained `product / 10p-plus`.
- Protected fields matched the before-state exactly: 5 headlines, 1 long headline, 3 descriptions, business name, YouTube video, and all 4 logos.
- Counts remained 4 landscape, 6 square, 4 portrait, and 4 logos; all 14 original image IDs are absent from their respective media fields.
- No WordPress site implementation, template, landing-page, translation, production asset, or general Media Library record was changed by the scoped REST operation.
- The active PMax campaign's name, status, type, amount, country, start date, and targeted locations matched the before-state on after-read. The removed Search campaign was omitted by the after-read collection response and was not part of the PUT request. No budget, bid, targeting, campaign setting, Merchant Center, or unrelated Google operation was requested.
