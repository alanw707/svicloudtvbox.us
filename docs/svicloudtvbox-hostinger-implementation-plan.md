# SVICLOUDTVBOX.US — Hostinger Implementation Plan (WordPress + WooCommerce)

Doc version: 1.0
Owner: {{YOUR_NAME}}
Date: {{TODAY}}
Status: Ready for execution

---

## 1) Overview & Assumptions

- Objective: Production-ready WooCommerce storefront on Hostinger matching PRD and Launch Plan.
- Scope: U.S.-only store, bilingual EN/中文, 1–2 SKUs (10P+ / 10S), custom lightweight theme, privacy/security/SEO/performance per PRD.
- Assumptions: Domain on Hostinger DNS (optionally Cloudflare), WordPress created, WooCommerce installed, no legacy data.

---

## 2) Hostinger Environment Setup

- PHP/runtime: PHP 8.2; `memory_limit` 512M; `max_execution_time` 120; timezone US/Pacific.
- SSL/TLS: Issue Let’s Encrypt; force HTTPS; enable HSTS (6–12 months).
- Staging: Create staging subdomain (e.g., `staging.svicloudtvbox.us`) with copy tool; protect via auth.
- Object cache: If available on plan, enable Redis + LiteSpeed object cache; otherwise page cache only.
- Cron: Disable WP-cron (`DISABLE_WP_CRON` true); add real cron every 5 min: `wp cron event run --due-now`.
- Backups: Daily + pre-deploy manual snapshot; retention ≥14 days; test one-click restore.
- Email: Use SMTP provider (SendGrid/Mailgun). Set SPF, DKIM, DMARC (`p=quarantine` initially).
- File perms: Enforce `0644` files/`0755` dirs; disallow file editing in dashboard.

---

## 3) WordPress Setup

- General: Site language EN (secondary 中文 via TranslatePress), US locale, timezone, week starts Monday.
- Permalinks: Post name; WooCommerce product permalinks `/shop/%product%`.
- Users: Separate owner vs. fulfillment role (Shop Manager); enforce 2FA for Admins/Editors.
- Media: WebP on upload; consistent PDP filenames under `/wp-content/uploads/products/*`.
- SMTP: WP Mail SMTP with provider API; test order and password reset emails.
- Privacy: Publish Privacy, Terms, Shipping, Warranty/Returns, DMCA; link from footer.

---

## 4) Theme Strategy (Custom Lightweight Block Theme)

- Approach: Custom block theme `svicloudtvbox` to stay fast and clean; avoid heavy builders; use theme.json for design tokens.
- Design system: Colors (black/white + electric blue accent), Fonts: Inter (EN) + Noto Sans SC (中文). Accessible contrast.
- Structure:
  - `wp-content/themes/svicloudtvbox/`
    - `style.css` (theme header)
    - `theme.json` (colors, fonts, spacing, typography)
    - `functions.php` (enqueue, menus, image sizes, JSON-LD hook)
    - `templates/`
      - `front-page.html` (Hero, Trust bar, Tiles, Why us, FAQ, Support CTA)
      - `page-compare.html` (compare table + recommendations)
      - `page-how-it-works.html`, `page-setup.html`, `page-warranty-returns.html`, `page-support.html`, `page-contact.html`
      - `single-product.html` (PDP), `archive-product.html` (Shop)
    - `parts/` (header, footer)
    - `patterns/` (hero, trust-bar, product-tiles, faq, support-cta)
- Compare page: Static table (Markdown/blocks) with cross-links to PDPs; “Who should buy which” guidance.
- Performance: Minimal CSS, no jQuery dependence, preload hero image, defer non-critical scripts.
- Accessibility: Landmarks, focus outlines, skip links, alt text conventions.

---

## 5) Plugins & Config (Minimal Set)

- WooCommerce (core)
- Payments: WooCommerce Stripe Gateway (Apple/Google Pay), WooCommerce PayPal
- Performance: LiteSpeed Cache (page + object cache if Redis), WebP, lazy-load; test minify/merge
- SEO: Yoast or RankMath (one only)
- Translation: TranslatePress (EN ↔ 中文, `/zh/` path, manual copy)
- Analytics: Site Kit (GA4/Search Console) or GTM; Meta/TikTok pixel plugin
- Email: WP Mail SMTP (API transport)
- Security: reCAPTCHA for login/checkout (e.g., Advanced noCaptcha & invisible reCaptcha); optional 2FA plugin

---

## 6) WooCommerce Configuration

- General: Currency USD; selling & shipping: U.S. only; measurements imperial; enable taxes.
- Products: Create 10P+ and 10S with bullets/specs/prices/images; define SKUs; stock management toggles.
- Shipping: Free shipping over ${{FREE_SHIP_THRESHOLD}}; Flat rate (e.g., $7.95) otherwise; handling 1–3 business days; USPS/UPS via Pirate Ship/Shippo.
- Payments: Stripe (Cards + Apple/Google Pay) and PayPal live credentials; test $1 product then hide.
- Taxes: WooCommerce Tax automated; display at checkout; verify rates.
- Emails: From `orders@svicloudtvbox.us`; brand header/footer and logo; test New/Processing/Completed.
- Pages: Shop, Cart, Checkout, My Account; policy pages mapped in WooCommerce settings.

---

## 7) Bilingual (TranslatePress)

- Paths: `/` EN, `/zh/` 中文; ensure canonical/hreflang pairs (`en-US` and `zh`).
- Content coverage: Homepage, PDPs, Compare, policies, key posts (3 content pieces).
- Exclusions: Admin pages; avoid translated slugs for critical commerce URLs.
- QA: Toggle keeps current page; no layout shift; Chinese typography rules applied.

---

## 8) SEO & Structured Data

- Technical: Canonical root vs. www; XML sitemap; robots.txt; clean slugs per Launch Plan.
- Titles/meta: Configure plugin per samples; avoid clickbait; include authorized/U.S. framing.
- JSON-LD: Organization + Product; prefer SEO plugin’s blocks; otherwise add via theme `wp_head`.
- Search Console: Verify domain; submit sitemap; fix coverage errors pre-launch.

---

## 9) Analytics & Pixels

- GA4: Site Kit or GTM; enable enhanced ecommerce; verify `purchase` fires with value/currency.
- Ad pixels: Meta + TikTok with purchase events; define key audiences (view/add/begin/purchase).
- Events (GA4): `view_item`, `add_to_cart`, `begin_checkout`, `purchase`, `language_toggle`, `support_click` per PRD.

---

## 10) Performance Tuning (LiteSpeed Cache)

- Cache: ON; object cache if Redis; ESI if needed; test logged-in behavior.
- Media: WebP, compression ~80–85, responsive sizes; lazy-load.
- CSS/JS: Minify; combine only if safe; defer non-critical; preload hero; remove unused CSS where possible.
- Measurement: Target LCP < 2.5s, TTFB < 600ms, CLS < 0.1 on mobile.

---

## 11) Security & Compliance

- reCAPTCHA on login/checkout; strong admin creds; restrict dashboard file editing.
- 2FA for admins; least-privilege for Shop Manager.
- Email auth: SPF/DKIM/DMARC aligned; DMARC aggregate reports enabled.
- Legal: Authorized dealer disclosure; no “official site” language; `.US` WHOIS uses business identity.

---

## 12) Ops & Fulfillment

- Labels: Pirate Ship/Shippo; USPS/UPS; tracking injected into order complete emails.
- Serial capture: Optional order note/meta at fulfillment; consider barcode workflow later.
- Hours: 10am–7pm PT Mon–Sat; English/中文 support; publish in Support page.

---

## 13) QA Checklist (Pre-Launch)

- Checkout live test ($1) succeeds on Stripe and PayPal; emails deliver to inbox.
- GA4 purchase and ad pixels verified; language toggle events captured.
- Sitemap submitted; no major Search Console errors.
- Mobile performance green (Core Web Vitals); product pages cached.
- `/zh/` translations live with proper hreflang.
- Policies accessible; footer contact details accurate.

---

## 14) Launch & Post-Launch

- Freeze content; take backup; switch Domain/Cloudflare if applicable; purge caches/CDN.
- Enable production ads (brand/model); set negative keywords; monitor KPIs.
- Post-purchase review flow live (email/SMS); weekly KPI rollup sheet.

---

## 15) Useful Snippets (Optional)

### 15.1 wp-config.php hardening
```
define('DISALLOW_FILE_EDIT', true);
define('DISABLE_WP_CRON', true);
```

### 15.2 Theme JSON-LD hook (Organization)
Add to `functions.php` if not using SEO plugin for schema:
```
add_action('wp_head', function () {
  if (is_admin()) return;
  $org = [
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => 'SVICLOUDTVBOX.US',
    'url' => home_url('/'),
    'logo' => home_url('/media/logo.png'),
    'contactPoint' => [[
      '@type' => 'ContactPoint',
      'telephone' => '+1-000-000-0000',
      'contactType' => 'customer support',
      'areaServed' => 'US',
      'availableLanguage' => ['en','zh']
    ]]
  ];
  echo '<script type="application/ld+json">' . wp_json_encode($org) . '</script>';
});
```

### 15.3 WP-CLI quick config (run in staging)
```
wp option update blogdescription 'Authorized U.S. Dealer — Fast U.S. Shipping'
wp rewrite structure '/%postname%/' --hard
wp wc tool run install_pages
```

---

End of Hostinger Implementation Plan v1.0

