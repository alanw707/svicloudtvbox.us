# SVICLOUDTVBOX.US — Implementation Backlog

Version: 1.0  
Status: Active  
Note: Aligns with PRD and Launch Plan. Use this as the working checklist.

---

## Legend

- [ ] To do • [~] In progress • [x] Done  
- AC = Acceptance Criteria  
- DRI = Directly Responsible Individual

---

## Epic A — Environment & DNS (Hostinger)

- [x] Point DNS to Hostinger
  - AC: A/AAAA/CNAME update propagated; canonical host decided (`root` or `www`).
- [x] PHP 8.2 + memory/time limits set
  - AC: `phpinfo()` reflects target; Woocommerce status shows OK.
- [ ] Staging site created and protected
  - AC: `staging.svicloudtvbox.us` loads; HTTP auth on; sync script documented.
- [ ] Real cron configured; WP-cron disabled
  - AC: `DISABLE_WP_CRON` true; cron job runs every 5 min; no overdue events.
- [ ] Backups scheduled and restore tested
  - AC: Successful test restore in staging; retention ≥14 days.

---

## Epic B — WordPress Core & Plugins

- [x] Permalinks configured (`/%postname%/`, products `/shop/%product%`)
  - AC: Sample URLs resolve; no 404s for shop/cart/checkout.
- [ ] Install minimal plugins (see plan)
  - AC: Stripe, PayPal, LiteSpeed Cache, custom bilingual framework loader, SEO, WP Mail SMTP, reCAPTCHA.
- [ ] SMTP configured + DNS (SPF/DKIM/DMARC)
  - AC: Test emails deliver to inbox; DMARC reports received.
- [ ] Security baseline (no file edits; 2FA; least privilege)
  - AC: `DISALLOW_FILE_EDIT` true; 2FA for admins; Shop Manager role verified.

---

## Epic C — Custom Theme `svicloudtvbox`

- [x] Scaffold theme (style.css, theme.json, functions.php)
  - AC: Theme selectable; no PHP errors; passes basic theme check.
- [x] Header/Footer template parts
  - AC: Header nav and bilingual toggle; footer policies/contact.
- [x] Front page template (hero, trust, tiles, why-us, FAQ, support CTA)
  - AC: Matches wireframe; LCP image preloaded; CLS < 0.1.
- [x] Product archive (`archive-product.html`)
  - AC: 10P+/10S tiles; clear CTAs; breadcrumb.
- [x] PDP (`single-product.html`) with sticky CTA
  - AC: Bullets/specs; warranty/returns section; compare link; structured data present.
- [x] Compare page (`page-compare.html`)
  - AC: Table rows per Launch Plan; “Who should buy which” section; cross-links.
- [x] Patterns (hero, trust-bar, product-tiles, faq, support-cta)
  - AC: Insertable in block editor; responsive at 320–1200px.
- [ ] Performance & a11y pass
  - AC: Lighthouse mobile ≥90 perf/a11y on key pages.

---

## Epic D — WooCommerce Setup

- [ ] General/currency/tax settings (U.S.-only)
  - AC: USD; tax enabled; store address set; time zone correct.
- [ ] Shipping methods (Free over ${{FREE_SHIP_THRESHOLD}}, flat rate)
  - AC: Rates display in cart/checkout; ETAs visible; methods restricted to U.S.
- [ ] Payment gateways (Stripe + PayPal)
  - AC: Live keys configured; $1 test purchase successful on both.
- [ ] Emails branding (From, logo, copy)
  - AC: New/Processing/Completed customized; deliver to inbox.
- [x] Products (10P+, 10S) + media
  - AC: PDPs published with bullets/specs/price/SKUs/images; inventory toggle works.

---

## Epic E — Custom Bilingual Framework

- [x] Implement translation registry + helpers (`svic_translate`, `svic_rich_text`)
  - AC: Helper functions output correct locale strings across templates.
- [x] Replace legacy bilingual spans in templates and WooCommerce overrides
  - AC: All theme copy uses registry keys; QA confirms both locales render without layout issues.
- [ ] Localize fallback theme strings (menu fallback, 404 template, misc `esc_html__` text)
  - AC: Remaining English-only strings replaced with registry keys or localized via PO; both locales verified.
- [ ] Configure `/zh/` routing, hreflang, and language cookie
  - AC: Language switch retains current path, sets `svic_lang` cookie, and outputs `en-US`/`zh` hreflang tags.
- [ ] Author editor workflow docs + QA localized pages (Home, PDPs, Compare, policies, 3 posts)
  - AC: Editorial guide published; bilingual reviewer signs off content parity.

---

## Epic F — SEO & Analytics

- [ ] SEO plugin setup (titles/meta, sitemap, robots)
  - AC: Titles per samples; sitemap submitted; robots sane.
- [ ] JSON-LD (Organization + Product)
  - AC: Rich Results Test passes; no schema errors.
- [ ] Rank Math or Yoast (Woo module) configured for Product schema
  - AC: Product pages output valid `Product` JSON-LD; `aggregateRating` only appears on PDPs with real, visible reviews; Organization schema has no ratings.
- [ ] GA4 (enhanced ecommerce) + Meta/TikTok pixels
  - AC: `purchase` events fire with value/currency; audiences collecting.
- [ ] Search Console verification and coverage
  - AC: No critical errors/warnings; pages indexed.

---


---

## Epic H — Security & Compliance

- [ ] reCAPTCHA on login/checkout
  - AC: CAPTCHA visible/working; spam reduced.
- [ ] 2FA enforced for admins
  - AC: All admins have 2FA; emergency codes stored securely.
- [ ] Legal pages and disclosure
  - AC: Authorized dealer notice; no “official site” phrasing; `.US` whois policy followed.

---

## Epic I — Content & Media

- [ ] Copywriting (Home, How It Works, Setup, Support, Policies)
  - AC: Pages published; tone consistent; bilingual variants ready.
- [ ] Blog/Guides (3 pieces)
  - AC: Compare, Setup in 3 Minutes, Wi‑Fi Fixes live in EN + 中文.
- [ ] Media assets (product shots, lifestyle hero, 30–45s clip)
  - AC: Optimized assets uploaded; video hosted (YouTube unlisted or self-host w/ lazy-load).

---

## Epic J — Operations & Policies

- [ ] Shipping labels workflow (Pirate Ship/Shippo)
  - AC: Staff SOP written; tracking links auto-inserted.
- [ ] RMA & returns SOP
  - AC: RMA steps documented; email templates created.
- [ ] Support channels
  - AC: Phone/SMS/WhatsApp/Email live; hours posted; test contact flow.

---

## Epic K — QA & Launch

- [ ] Smoke tests (cart/checkout/emails/pixels)
  - AC: All pass on staging and prod; logs captured.
- [ ] Redirects/canonicals verified
  - AC: Single canonical; 301s correct; no 404s.
- [ ] Backup + go-live switch
  - AC: Pre-launch backup; caches purged; monitoring enabled.

---

## Epic L — Post-Launch Growth

- [ ] Review capture flow (email/SMS)
  - AC: Trigger after delivery; UGC policy set.
- [ ] Google Customer Reviews (GCR) integration
  - AC: GCR opt-in enabled on thank-you page; Merchant Center linked; begin collecting seller ratings toward eligibility; Seller Ratings eligible once country threshold met.
- [ ] KPI dashboard (weekly)
  - AC: CR, AOV, ROAS, refunds tracked; sheet auto-updates.
- [ ] Ads (Google brand/model) with negatives
  - AC: Live campaigns; QS ≥ 8; search terms clean.
- [ ] A/B tests (landing variants)
  - AC: Hypotheses logged; tests run with significance checks.

---

## Epic M — Black Friday 2025 Marketing Activation

- [ ] Facebook creative suite production
  - AC: Assets defined in `docs/marketing/facebook-black-friday-2025-creative-brief.md` (videos, carousel, stories, statics) exported with EN/zh captions, UTMs sheet, and delivered to `/assets/facebook/bf25`.
- [ ] `/black-friday` landing module build
  - AC: Page implements modules specified in `docs/marketing/black-friday-landing-page-plan.md`, hero countdown wired, Klaviyo form connected, CSS partial `55-black-friday.css` added to `front-page` bundle.
- [ ] Promo code configuration & QA
  - AC: Coupons `BF25-20`, `BF25-10`, `BF25-HOLD` live with auto-apply query params; regression tests captured per QA checklist.
- [ ] Landing page QA runbook
  - AC: Device tests, Lighthouse, screenshot set, translation toggle verified; issues logged/resolved.
- [ ] Ads Manager build-out
  - AC: Campaign/ad sets/ad names and automation rules implemented per `docs/marketing/facebook-bf25-ads-structure.md`; pixel event tests captured.
- [ ] Pixel/CAPI + audience refresh
  - AC: Conversion API dedup validated, CRM and LAL seeds refreshed, reporting in `docs/marketing/facebook-bf25-ops-checklist.md` noted complete.
- [ ] BF25 reporting dashboard
  - AC: Looker Studio dashboard pulling Meta/GA4/Klaviyo data with annotated daily updates; link stored in ops checklist.

---

## Fill-ins (Blockers to resolve)

- Final prices — 10P+: ${{PRICE_10P}} • 10S: ${{PRICE_10S}}
- Free shipping threshold — ${{FREE_SHIP_THRESHOLD}}
- Support phone/WhatsApp — {{PHONE}}
- Business address — {{ADDRESS}}
- PDP image paths — {{10P_IMAGE}}, {{10S_IMAGE}}

---

## Suggested Order of Execution

1) Epics A, B (env/core) → 2) Epic C (theme) → 3) Epic D/E (commerce + bilingual) → 4) Epic F/G (SEO/perf) → 5) Epic H/I/J (security/content/ops) → 6) Epic K (QA/launch) → 7) Epic L (growth)

---

End of Backlog v1.0
