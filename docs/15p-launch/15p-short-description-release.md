# SVICLOUD 15P short-description release

## Approved change

Production approval was explicitly provided:

> I approve updating the 15P short description in production.

Product `1204` now uses:

- English: `Everything in 10P+, plus support for downloading mobile apps.`
- Traditional Chinese: `具備 10P+ 的全部功能，並支援下載手機 App。`
- Simplified Chinese: `具备 10P+ 的全部功能，并支持下载手机 App。`

## Changed storage

- WooCommerce REST product `1204`: `short_description` updated with a single-field `PUT`.
- Pinned FTPS theme files uploaded only:
  - `lang/en_US.php`
  - `lang/zh_TW.php`
  - `lang/zh_CN.php`

The localized source values and remote SHA-256 hashes are verified in the external preflight artifact:
`/home/alanw/.pi/backups/svicloudtvbox.us/2026-08-18-15p-short-description-preflight/localized-short-description-theme-final-verification.json`.

## Verification

- Default product content and raw short-description hashes: `production-product-short-description-final-verification.json`.
- Long-description hash matches the captured pre-write hash.
- All stable WooCommerce/product invariants match: price `$288` / regular `$379`, stock `0`, `notify`, `onbackorder`, five-image gallery, categories, metadata, and product flags.
- PDP routes pass in Chromium/WebKit for English, Traditional Chinese, and Simplified Chinese: `production-short-description-pdp-verification-final.json` (6/6, 0 errors), with locale markers, status, and Product schema present.
- The custom PDP template does not render WooCommerce `short_description` as a visible excerpt; localized copy is therefore verified from the pinned remote translation source, while PDP route/locale/schema output is verified separately. The earlier strict visible-copy probe is retained as diagnostic, not a failed release gate.
- Production private-data probe: 0 matching orders and 0 matching customers.

The immediate REST `PUT` response represented the unchanged long description with formatter-normalized whitespace, so the write was rechecked using a fresh `GET`; the fresh response matched the original long-description hash and stable invariants. No long-description write was requested.
