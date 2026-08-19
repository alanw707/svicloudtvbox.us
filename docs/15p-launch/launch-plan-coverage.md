# SVICLOUD 15P backorder-launch coverage

| Requirement | Evidence |
|---|---|
| Authoritative hardware facts and exclusions | `docs/15p-source-traceability.md` |
| Approved commerce decision | `$379` regular / `$288` sale, customer-facing pre-order copy backed by notified backorders, and no shipping date recorded in source traceability and RPI spec |
| Existing source-folder media inventory | `docs/15p-parent-media-inventory.md` (85 images + 4 videos, each with disposition) |
| Embedded PPTX media inventory | `docs/15p-pptx-media-inventory.md` (27 rows, all excluded with reasons) |
| Optimized assets | Five watermarked gallery derivatives plus watermarked v4 marketing artwork under `assets/images/products/` |
| Repeatable local product | `scripts/import_public_theme_fixture.php`; exact post-apply invariant in `scripts/sync_public_theme_fixture.py` |
| Private local content preservation | `scripts/verify_private_fixture_preservation.py` plus verified external recovery point |
| Homepage / Shop / Compare / PDP | Theme templates and EN/繁/简 locale registries |
| Cart / checkout | Standard WooCommerce flow plus localized notified-backorder notices |
| Structured data | One `$288` Product Offer with `BackOrder`; no 15P delivery-time estimate |
| SEO preservation/audit | RPI SEO baseline and `scripts/audit_storefront_seo.mjs` after implementation |
| Dedicated regression | `tests/playwright/launch-15p.spec.ts` |
| Locale/responsive/accessibility audit | `scripts/audit_15p_storefront.mjs` |
| Final screenshots | `docs/15p-launch/screenshots/after-home-*`, `final-shop-*`, and `final-pdp-15p-*` |

## Deliberately excluded

Search Console submission, a release or shipping date, 15P-specific shipping-speed/dispatch/delivery/warranty promises, unsupported performance comparisons, and paid-ad activation remain outside this integration. Production deployment and rollback evidence are recorded in `docs/15p-launch/production-gallery-price-update-evidence.md`.
