# SVICLOUDTVBOX.US — Dev Environment Guide (Local + Staging)

Version: 1.0  
Status: Ready for use

---

## 0) TL;DR

- Use both: Local WP for theme/CSS/JS work; Hostinger staging for Stripe/Apple Pay, TranslatePress, LiteSpeed cache, and pixels.  
- Recommended local stack: DDEV (Docker) or LocalWP. Validate payments, caching, and bilingual paths on staging, not purely local.

---

## 1) Environment Matrix

- Local (dev):
  - URL: `https://svicloudtvbox.ddev.site` (example)  
  - PHP 8.2, MySQL/MariaDB, Xdebug; Stripe in Test mode; Apple Pay not fully testable  
  - GA4 in DebugView only; GTM preview enabled
- Staging (Hostinger):
  - URL: `https://staging.svicloudtvbox.us` with HTTP auth  
  - Stripe/PayPal in Test mode; Apple Pay domain association; TranslatePress `/zh/` path; LiteSpeed Cache on  
  - GA4 + pixels fire; cache/CDN behavior verified
- Production:
  - Live payment keys; reCAPTCHA on; 2FA enforced; performance budgets monitored

---

## 2) Option A — DDEV Quickstart (Recommended)

Prereqs: Docker Desktop + DDEV installed.

```
# From your chosen project directory
mkdir svicloudtvbox-wp && cd svicloudtvbox-wp

# Configure a WordPress project (docroot can be 'public' or 'web')
ddev config --project-type=wordpress --docroot=public --php-version=8.2 --create-docroot

ddev start

# Download and install WordPress into the docroot
ddev wp core download --path=public

ddev wp config create \
  --path=public \
  --dbname=db --dbuser=db --dbpass=db --dbhost=db --skip-check

ddev wp db create

ddev wp core install \
  --path=public \
  --url=https://svicloudtvbox.ddev.site \
  --title="SVICLOUDTVBOX Local" \
  --admin_user=admin --admin_password=admin \
  --admin_email=you@example.com

# Install baseline plugins (adjust as needed)
ddev wp plugin install \
  woocommerce \
  woocommerce-gateway-stripe \
  woocommerce-paypal-payments \
  litespeed-cache \
  translatepress-multilingual \
  wordpress-seo \
  wp-mail-smtp \
  --activate

# Helpful dev flags
ddev wp config set WP_DEBUG true --raw
```

Theme development location: `public/wp-content/themes/svicloudtvbox/`.

---

## 3) Option B — LocalWP (Click-and-Go)

- Install LocalWP → Create new site (PHP 8.2) → Add WordPress.  
- Open site shell (from LocalWP) and run the same `wp plugin install` commands as above.  
- Place the custom theme at: `app/public/wp-content/themes/svicloudtvbox/`.

---

## 4) Custom Theme Workflow

- Theme name: `svicloudtvbox` (block theme; lightweight, no builder).  
- Structure (high level):
  - `style.css` (theme header)  
  - `theme.json` (colors, fonts, spacing, typography)  
  - `functions.php` (enqueue, menus, image sizes, optional JSON-LD)  
  - `templates/` → `front-page.html`, `single-product.html`, `archive-product.html`, `page-compare.html`, policy pages  
  - `parts/` → header, footer  
  - `patterns/` → hero, trust bar, product tiles, FAQ, support CTA
- Fonts: Inter (EN) + Noto Sans SC (中文).  
- Accent: electric blue.  
- PDP: sticky Add to Cart; bullets/specs; warranty/returns; compare link.  
- Compare page: static table + “Who should buy which.”

Tip: Keep this repo as the theme source of truth, then copy/symlink into the local WP theme folder.

---

## 5) Syncing Data Between Staging and Local

- Database (pull from staging):
  - Export a dump from staging (Hostinger panel or `wp db export`).  
  - Import locally: `ddev import-db --src=staging.sql`  
  - Search/replace URLs: `ddev wp search-replace 'https://staging.svicloudtvbox.us' 'https://svicloudtvbox.ddev.site' --all-tables`
- Media:
  - Use SFTP/rsync from `/wp-content/uploads/` on staging to local `public/wp-content/uploads/`.  
  - Or install a safe migration plugin; avoid overwriting production data.

---

## 6) Payments, Apple Pay, and Webhooks

- Stripe: Use Test mode locally and on staging first. For local webhook testing:  
  - Install Stripe CLI → `stripe login` → `stripe listen --forward-to https://svicloudtvbox.ddev.site/wp-json/wc/v3/webhooks`  
- Apple Pay: Requires domain association at `/.well-known/apple-developer-merchantid-domain-association`. Validate on staging/prod where the real domain exists.  
- PayPal: Sandbox accounts on staging; confirm IPN/webhook delivery.

---

## 7) Caching, Bilingual, and Pixels

- LiteSpeed Cache: On staging, enable page/object cache (if Redis), image WebP/lazy-load; test minify/defers.  
- TranslatePress: Configure `/zh/` language; ensure `hreflang en-US` and `zh` pairs; validate toggle keeps same URL path.  
- GA4 & Ad Pixels: Use DebugView/preview locally; verify actual purchase events on staging.

---

## 8) Security & Settings by Environment

- Local: `WP_DEBUG=true`; disable reCAPTCHA; Stripe in Test; enable Xdebug if needed.  
- Staging: HTTP auth; Stripe/PayPal Test; reCAPTCHA enabled; `DISALLOW_FILE_EDIT=true`; 2FA for admin.  
- Production: Live keys; reCAPTCHA; 2FA mandatory; backups; HSTS; minimal plugins.

---

## 9) Suggested .gitignore (Theme Repo)

```
node_modules/
.DS_Store
*.log
/dist/
/vendor/
.env
# WordPress core/uploads are outside theme repo; do not commit
```

---

## 10) Common Commands (DDEV)

```
# Start/Stop
ddev start
ddev stop

# WP-CLI
ddev wp plugin list
ddev wp theme list

# Database
ddev export-db --file=local.sql
ddev import-db --src=staging.sql

# URL replacements (after DB import)
ddev wp search-replace 'https://staging.svicloudtvbox.us' 'https://svicloudtvbox.ddev.site' --all-tables
```

---

## 11) Acceptance Criteria for Dev Env

- Local site runs with custom theme active; WooCommerce installed.  
- Staging mirrors local with HTTP auth; TranslatePress `/zh/` present with `hreflang`.  
- Stripe/PayPal test purchases succeed on staging; emails delivered.  
- LiteSpeed cache settings verified; mobile LCP < 2.5s on staging.

---

End of Dev Environment Guide v1.0

