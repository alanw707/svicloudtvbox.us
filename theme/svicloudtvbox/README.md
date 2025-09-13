# SVICLOUDTVBOX Block Theme (Neon Tech)

Custom lightweight block theme for SVICLOUDTVBOX.US.

- Fast: minimal PHP, Site Editor templates, theme.json tokens.
- Modern: Neon gradients, glow accents, glass cards.
- Bilingual-ready: header includes a `/zh/` link (use TranslatePress for real toggle).

## Structure

- `style.css` — theme header + neon utilities
- `theme.json` — colors, typography, spacing, block styles
- `functions.php` — font enqueue + pattern category
- `parts/` — `header.html`, `footer.html`
- `templates/` — `index.html`, `front-page.html`, `page-compare.html`
- `patterns/` — `hero-neon.html`, `trust-bar.html`, `product-tiles.html`, `faq.html`, `support-cta.html`
- `assets/images/hero-device.png` — add your product hero image here

## Install

1) Copy folder to your WordPress site: `wp-content/themes/svicloudtvbox/`  
2) In WP Admin → Appearance → Themes → Activate “SVICLOUDTVBOX”  
3) Site Editor → set `front-page` to use the included sections/patterns  
4) Create a page named “Compare” and assign the “page-compare” template

## Notes

- WooCommerce: this theme doesn’t override Woo templates; use core/Woo blocks on PDPs.  
- Fonts: Inter + Noto Sans SC are loaded from Google Fonts (swap to self-host later if desired).  
- Replace `/wp-content/themes/svicloudtvbox/assets/images/hero-device.png` with your image.

