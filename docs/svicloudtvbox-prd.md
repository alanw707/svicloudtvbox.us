# PRD — SVICLOUDTVBOX.US (MVP)

**Doc version:** 1.0  
**Owner:** {{YOUR_NAME}}  
**Date:** {{TODAY}}  
**Status:** Draft (MVP scope approved upon sign‑off)

---

## 1. Background & Context
SVICLOUDTVBOX.US will sell the SVICLOUD 10P+ and 10S TV boxes as an **authorized U.S. dealer**. Many current sellers are global and ship from overseas; this project focuses on **U.S. buyers** who value fast domestic shipping, warranty clarity, and English/中文 support.

**Opportunity:** U.S. demand for connected TV devices is high; Chinese‑speaking communities seek reliable, genuine devices with local support. Differentiation = **authorized** + **U.S. fulfillment** + **clear support**.

---

## 2. Goals & Non‑Goals

### 2.1 Goals (MVP)
- Launch a high‑trust, bilingual (EN/中文) storefront and sell **1–2 SKUs**.
- Provide a clear **Compare** experience (10P+ vs 10S) and simple checkout.
- Offer **U.S. shipping**, **1‑year warranty**, and **English/中文 support**.
- Implement clean **SEO** (brand/model, setup/troubleshooting) and **paid search** basics.
- Track core **ecommerce analytics** (GA4) and set up remarketing pixels.

### 2.2 Non‑Goals (MVP)
- Custom account portal beyond order history.
- Wholesale/enterprise features.
- Complex ERP/WMS integrations.
- International shipping outside the U.S.

---

## 3. Users & Personas

1) **English‑dominant U.S. buyer**: wants a legit device, fast delivery, easy returns.  
2) **Chinese‑speaking U.S. buyer**: prefers 中文 pages and phone/WhatsApp support.  
3) **Tech‑savvy deal seeker**: compares 10P+ vs 10S; values specs and warranty.

**Key needs:** trust, clarity, fast checkout, setup help, responsive support.

---

## 4. Scope & Requirements (MVP)

### 4.1 Functional Requirements
- **F1. Catalog**: 2 products (10P+, 10S) with PDPs, images, specs, price, inventory flag.
- **F2. Compare page** with clear differences (RAM/storage, apps, 4K/AV1, remote, best‑for).
- **F3. Bilingual**: EN and 中文 content; `/zh/` path; hreflang.
- **F4. Checkout**: Stripe (cards, Apple/Google Pay) and PayPal.
- **F5. Shipping (U.S. only)**: free over threshold; flat rate otherwise; tracking emails.
- **F6. Warranty & Returns** page and RMA instructions.
- **F7. Support**: phone/SMS/WhatsApp/email; hours posted.
- **F8. Content**: Three posts (Compare, 3‑min Setup, Wi‑Fi Fixes).
- **F9. SEO & Schema**: Title/meta per page; Organization & Product JSON‑LD.
- **F10. Analytics**: GA4 ecommerce & conversions; Meta/TikTok pixels.

### 4.2 Non‑Functional Requirements
- **N1. Performance**: LCP < 2.5s on mobile; TTFB < 600ms; CLS < 0.1.
- **N2. Availability**: 99.9% monthly uptime target.
- **N3. Security**: HTTPS/HSTS; 2FA for admin; reCAPTCHA on checkout/login.
- **N4. Accessibility**: WCAG 2.1 AA for navigation, forms, contrast.
- **N5. Privacy/PII**: DMARC aligned; SMTP via provider; data minimization.
- **N6. SEO hygiene**: canonical URLs, XML sitemap, robots, clean slugs.

### 4.3 Out of Scope (MVP)
- Advanced RMA portal, wholesale pricing, subscriptions, loyalty program.

---

## 5. Information Architecture

**Header:** Home · Shop (10P+, 10S) · Compare · How It Works · Setup · Warranty/Returns · Support · Contact (EN/中文)  
**Footer:** Authorized Dealer notice · U.S. address/phone · Shipping · Privacy · Terms · DMCA · Sitemap

---

## 6. UX Flows (Acceptance Criteria)

### 6.1 Purchase Flow
1. User lands on PDP → clicks **Add to Cart** → **Cart** → **Checkout**.  
2. User chooses payment (Stripe card/Apple Pay/Google Pay or PayPal).  
3. On completion, user sees **Order Received**, receives email receipt, and is added to audiences.

**AC:** Transaction recorded in GA4; email receipt delivered; order visible in admin.

### 6.2 Language Toggle
- Toggle EN/中文 (TranslatePress) keeps user on the same page path (adds `/zh/`).
**AC:** `hreflang` present; Chinese text rendered without layout shift.

### 6.3 Shipping & Tracking
- At checkout, user selects shipping; after fulfillment, receives tracking via email/SMS.
**AC:** Tracking link resolves; delivery estimates shown in cart/checkout.

---

## 7. Content & SEO

- **Pages:** Home, 10P+, 10S, Compare, How It Works, Setup, Warranty/Returns, Support, Contact.  
- **Blog/Guides:** Compare (10P+ vs 10S), Setup in 3 Minutes (video), Wi‑Fi Fixes.  
- **Titles/Meta:** per provided examples; avoid clickbait; use brand/model terms.  
- **Schema:** Organization + Product JSON‑LD; review snippets once reviews accrue.

---

## 8. Analytics & Measurement

- **KPIs (MVP targets):** CR ≥ 1.5%, AOV ≥ $200, ROAS ≥ 2.5, Refunds ≤ 3%.
- **Dashboards:** GA4 ecommerce; ad platforms; weekly rollup in Sheets.  
- **Events:** view_item, add_to_cart, begin_checkout, purchase; language toggle; support clicks.

---

## 9. Integrations

- **Payments:** Stripe, PayPal (Apple/Google Pay via Stripe).  
- **Shipping/Labels:** Pirate Ship or Shippo (USPS/UPS).  
- **Email/SMS:** SMTP provider (SendGrid/Mailgun); optional SMS for tracking.  
- **Translation:** TranslatePress.  
- **SEO:** Yoast/RankMath.  
- **Caching:** LiteSpeed Cache.

---

## 10. Compliance & Legal

- Display **Authorized Dealer** status; avoid “Official site” phrasing.  
- Clear **Warranty/Returns**; domestic U.S. address.  
- `.US` WHOIS privacy not allowed—use business identity.  
- Device has no monthly fee; third‑party apps may require subscriptions.

---

## 11. Risks & Mitigations

- **R1: Perception of “generic Android boxes.”** → Emphasize authorized status, genuine firmware, U.S. warranty, support.  
- **R2: Shipping delays/stockouts.** → Buffer inventory; show accurate ETAs; proactive comms.  
- **R3: Policy/ads disapproval.** → Avoid piracy language; add store policies; keep creatives compliant.  
- **R4: Performance regressions.** → Keep plugins minimal; monitor Core Web Vitals.  
- **R5: Returns abuse.** → Clear conditions; restocking policy; serial capture.

---

## 12. Release Plan

### MVP (Weeks 1–2)
- Theme + plugins installed; PDPs + Compare + core policies live.
- GA4 + pixels; Search Console; XML sitemap.
- Paid search: brand/model exact + negatives.
- Content: 3 posts (EN + 中文).

### Post‑MVP (Weeks 3–8)
- Add video clips (unbox/setup).  
- Reviews flow (email/SMS).  
- Expand FAQs and troubleshooting.  
- Test YouTube/TikTok ads; optimize landing pages.

### Phase‑2 (Azure)
- Migrate to nopCommerce when custom workflows/scale require it; maintain URLs and SEO continuity.

---

## 13. Acceptance Criteria (Go/No‑Go)

- Checkout completes on live Stripe/PayPal (test $1 order).  
- GA4 purchase events fire; Meta/TikTok pixels verified.  
- Sitemap submitted; no major coverage errors.  
- Mobile LCP < 2.5s; CLS < 0.1.  
- EN and 中文 pages present with hreflang.  
- Support channels reachable during posted hours.

---

## 14. Open Questions

- Final prices: **10P+ = ${{PRICE_10P}}**, **10S = ${{PRICE_10S}}**.  
- Free shipping threshold: **${{FREE_SHIP_THRESHOLD}}**.  
- Support phone/WhatsApp: **{{PHONE}}**.  
- Business address for footer/policies: **{{ADDRESS}}**.  
- PDP image filenames/paths: **{{10P_IMAGE}}**, **{{10S_IMAGE}}**.

---

## 15. Appendices

### A. Sample Titles/Meta
- Home: “SVICLOUD TV Box – Authorized U.S. Dealer | Fast U.S. Shipping & 1‑Year Warranty”  
- 10P+: “SVICLOUD 10P+ – 4GB/64GB, 4K/AV1 | U.S. Authorized Dealer”  
- 10S: “SVICLOUD 10S – 4K/AV1 Value Model | U.S. Authorized Dealer”  
- Compare: “SVICLOUD 10P+ vs 10S – Which Model Should I Buy?”

### B. Event Spec (GA4)
- `view_item` (PDP), `add_to_cart`, `begin_checkout`, `purchase`, `language_toggle`, `support_click`.  
- Parameters: item_id (SKU), value, currency, language, page_type.

---

*End of PRD v1.0*
