# Source Tree Analysis — svicloudtvbox.us

```
svicloudtvbox.us/
├── theme/
│   ├── svicloudtvbox-lumen/          # Active WordPress theme
│   │   ├── assets/
│   │   │   ├── css/
│   │   │   │   ├── parts/            # CSS partials (00-tokens, header/nav, hero, marketing, Woo, checkout, FAQ/support, etc.)
│   │   │   │   ├── bundles.json      # Bundles map → outputs: style.css, front-page.css, about.css, compare.css, woocommerce.css, etc.
│   │   │   │   └── *.css             # Generated outputs (do not edit directly)
│   │   │   ├── js/theme.js           # All frontend behaviors (nav, hero/perf, scroll, product gallery, cart/checkout UX, Stripe pills)
│   │   │   ├── images/, svg/         # Theme assets
│   │   ├── lang/                     # Locale files (en_US, zh_CN, zh_TW)
│   │   ├── woocommerce/              # Template overrides (archive-product.php, single-product.php)
│   │   ├── front-page.php            # Homepage entry
│   │   ├── page-compare.php          # Compare page
│   │   ├── page-about.php            # About page
│   │   ├── page-faq.php, page-guides*.php, page-support.php, page-contact.php, page-return-policy.php, page-legal-disclaimer.php
│   │   ├── header.php / footer.php   # Shared layout
│   │   ├── functions.php             # Theme bootstrap/hooks
│   │   └── index.php, home.php, single.php, 404.php
│   └── shared/                       # Legacy/shared assets (unused unless referenced)
├── scripts/                          # Tooling
│   ├── build_css.py                  # Build CSS bundles from partials
│   ├── sync_theme_container.sh       # Sync theme to local Docker WP
│   ├── zip_theme.py                  # Package theme for deploy
│   ├── deploy-theme.sh               # FTPS deploy helper
│   └── content helpers (import/export/translate blog posts, palettes)
├── docs/                             # Existing documentation (marketing plans, SEO audits, PRD, FAQs, i18n, guides, blog markdown)
├── tests/ + playwright.config.ts     # Playwright setup (dev/tests)
├── automation/                       # Blog automation README and scripts
├── assets/, data/, reference/        # Misc. repo assets/supporting files
├── package.json                      # Playwright dependency, test scripts
└── docs/bmad/                        # BMM workflow artifacts (status, scan report, outputs)
```

## Critical Folders Summary
- `theme/svicloudtvbox-lumen/assets/css/parts/`: Author CSS here; rebuild via `python3 scripts/build_css.py --theme svicloudtvbox-lumen`.
- `theme/svicloudtvbox-lumen/assets/js/theme.js`: Single JS entry for UI/UX + Woo enhancements.
- `theme/svicloudtvbox-lumen/woocommerce/`: WooCommerce template overrides.
- `scripts/`: Build/sync/deploy utilities (local Docker sync + FTPS deploy).
- `docs/`: All existing documentation to consolidate (marketing/SEO/PRD/FAQ/i18n/guides/blogs).
