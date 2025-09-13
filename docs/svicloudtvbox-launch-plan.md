# SVICLOUDTVBOX.US — Lean Launch Plan (WooCommerce on Hostinger)  

**Status:** Authorized U.S. dealer • 1–2 SKUs (10P+ / 10S) • U.S.-focused  
**Primary domain:** `svicloudtvbox.us` (canonical)  
**Alt domains:** (optional) `.com`, `小雲盒子.com` → 301 to primary  
**Goal:** Go live fast with a clean, high‑trust storefront; add depth over 4–8 weeks.

---

## 0) TL;DR

- **Stack:** Hostinger + WordPress + WooCommerce (fastest to revenue); Azure remains Phase‑2.  
- **Positioning:** *Authorized U.S. dealer* • *Ships from USA* • *1‑Year U.S. warranty* • *English/中文 support*.  
- **SKUs at launch:** **SVICLOUD 10P+** (flagship), **SVICLOUD 10S** (value).  
- **Pages:** Home, Shop (10P+, 10S), Compare, How It Works, Setup, Warranty/Returns, Support, Contact (EN/中文).  
- **Day‑1 growth:** Brand/model paid search + setup/troubleshooting content + reviews capture.

---

## 1) Project Checklist (print & check)

- [ ] Domain DNS to Hostinger (A/AAAA/CNAME) + **Cloudflare** (optional)  
- [ ] TLS (HTTPS) issued, **HSTS** on  
- [ ] WordPress + WooCommerce installed (US locale, USD)  
- [ ] Theme **Astra** (or Blocksy/Kadence) installed  
- [ ] Plugins: WooCommerce, **Woo Stripe Gateway** (Apple/Google Pay), **PayPal**, **LiteSpeed Cache**, **TranslatePress**, **Yoast/RankMath**, **WP Mail SMTP**, **reCAPTCHA**  
- [ ] Permalinks set to `/shop/%product%`  
- [ ] Products: 10P+ and 10S created with images, bullets, specs, price, inventory toggle  
- [ ] **Compare** page table done; cross-links from PDPs  
- [ ] Policies: Shipping, Warranty/Returns, Privacy, Terms, DMCA  
- [ ] **Authorized Dealer** badge + footer disclosure (“Not the manufacturer’s official site”)  
- [ ] GA4 + Search Console + Meta + TikTok pixels + conversions  
- [ ] XML sitemap submitted; robots.txt present  
- [ ] Email deliverability: **SPF/DKIM/DMARC** + tested order emails  
- [ ] Performance: LiteSpeed cache on, WebP, lazy-load, minify (tested)  
- [ ] Security: strong admin creds, 2FA, reCAPTCHA on checkout/login  
- [ ] Launch ads: Google Search (brand/model), negative keywords set  
- [ ] Publish 3 content pieces (EN + 中文): *10P+ vs 10S*, *Setup in 3 minutes*, *Wi‑Fi fixes*  
- [ ] Post-purchase review email/SMS flows live

---

## 2) Site Architecture

**Header:** Home · **Shop** (10P+, 10S) · **Compare** · How It Works · Setup · Warranty/Returns · Support · Contact (EN/中文)  
**Footer:** Authorized Dealer notice · U.S. address/phone · Shipping · Privacy · Terms · DMCA · Sitemap

**Homepage (wireframe):**  
1. **Hero:** “SVICLOUD TV Box — Authorized U.S. Dealer”  
   Sub: *Ships from USA • 1‑Year Warranty • English/中文 Support*  
   CTA: **Shop 10P+** | **Shop 10S**  
2. **Trust bar:** Authorized • USA shipping • No monthly fees • Secure checkout  
3. **Product tiles** (10P+ vs 10S) + link: **Compare models**  
4. **Why buy from us:** U.S. fulfillment, Warranty, Returns, English/中文 support  
5. **Feature strip:** 4K HDR / AV1 • Voice Remote • (Kids & Karaoke on 10P+)  
6. **FAQ:** top 6 questions (shipping, setup, warranty, language)  
7. **Support CTA:** Chat / Call / WhatsApp

**Compare page — key rows:**  
- RAM/Storage (10P+ **4GB/64GB** vs 10S **2GB/32GB**)  
- Apps (Kids/Karaoke on **10P+** only)  
- Video (both 4K HDR / AV1)  
- Voice Remote (both)  
- Ports + In‑box items  
- “Best for” (families/sports/4K vs entry/value)

---

## 3) Theme, Style, and UX

- **Colors:** black/white + single accent (electric blue).  
- **Fonts:** Inter (EN) + Noto Sans SC/TC (中文).  
- **Buttons/CTAs:** high contrast; primary “Add to Cart” always visible on PDP.  
- **Images:** clean device shots on white; 1 lifestyle hero; a 30–45s unbox/plug‑in clip.  
- **Bilingual:** **TranslatePress** with `/zh/` path; manual copy (no auto garble).  
- **Speed:** avoid heavy page builders; use block editor and native sections.

---

## 4) WooCommerce Settings (quick reference)

### Payments
- Stripe (Cards + **Apple Pay/Google Pay**).  
- PayPal (PayPal + Pay Later).  
- Test mode → Live keys; test a $1 sandbox product before launch.

### Shipping (U.S. only)
- Methods: **Free shipping over ${{FREE_SHIP_THRESHOLD}}**; **Flat rate** (e.g., $7.95).  
- Labels via **Pirate Ship** or **Shippo** (USPS/UPS).  
- Set 1–3 business day handling; show carrier ETAs.

### Taxes
- WooCommerce Tax (automated). Display tax at checkout.

### Emails
- From: `orders@svicloudtvbox.us`; brand header/footer.  
- SMTP: SendGrid/Mailgun via **WP Mail SMTP** (SPF/DKIM/DMARC set).  
- Test **new order**, **processing**, **completed**.

### Performance (LiteSpeed)
- Page & object cache **ON**, ESI as needed.  
- Image optimization + WebP + lazy‑load.  
- CSS/JS minify/merge (test for conflicts).  
- Defer non-critical scripts; preload hero image; limit plugins.

### Security
- reCAPTCHA on login/checkout.  
- Strong admin usernames, 2FA.  
- Least-privilege roles for fulfillment staff.  
- Backups scheduled; one-click restore tested.

---

## 5) Product Pages (copy skeleton)

### SVICLOUD 10P+ — PDP
**H1:** SVICLOUD 10P+ (Authorized U.S. Dealer)  
**Price:** $248.99 *(adjust as needed)*  
**Bullets:**  
- 4GB RAM / 64GB storage • 4K HDR • AV1 decode  
- Kids & Karaoke apps (family‑friendly)  
- Voice remote • Dual‑band Wi‑Fi • Google Play  
- Ships from USA • 1‑Year U.S. Warranty  
**Sections:** What’s in the Box · Setup in 3 Minutes · Warranty/Returns · Reviews  
**CTA:** Add to Cart (primary) · Compare (secondary)

### SVICLOUD 10S — PDP
**H1:** SVICLOUD 10S (Authorized U.S. Dealer)  
**Price:** $183.99 *(adjust as needed)*  
**Bullets:**  
- 2GB RAM / 32GB storage • 4K HDR • AV1 decode  
- Voice remote • Dual‑band Wi‑Fi • Google Play  
- Ships from USA • 1‑Year U.S. Warranty

---

## 6) SEO & Content (day‑one)

### Technical
- Canonical to chosen host (root or `www`), 301 the other.  
- XML sitemap → Search Console; robots.txt set.  
- Clean slugs: `svicloud-10p-plus`, `svicloud-10s`, `compare-10p-plus-vs-10s`.  
- `hreflang`: `en-US` and `zh` for `/zh/` paths.

### Titles & Meta (examples)
- **Home (EN):** *SVICLOUD TV Box – Authorized U.S. Dealer | Fast U.S. Shipping & 1‑Year Warranty*  
  **Meta:** *Buy genuine SVICLOUD 10P+ & 10S in the USA. Authorized dealer, fast U.S. shipping, English/中文 support. No monthly fees.*  
- **10P+:** *SVICLOUD 10P+ – 4GB/64GB, 4K/AV1 | U.S. Authorized Dealer*  
- **10S:** *SVICLOUD 10S – 4K/AV1 Value Model | U.S. Authorized Dealer*  
- **Compare:** *SVICLOUD 10P+ vs 10S – Which Model Should I Buy?*

### Keyword clusters
- Brand: `svicloud tv box`, `svicloud 10p`, `svicloud 10s`, `小雲 盒子 國際版`, `小云 盒子`.  
- Purchase: `buy svicloud usa`, `svicloud authorized dealer`, `svicloud warranty usa`.  
- Help: `svicloud setup`, `svicloud wifi not connecting`, `svicloud voice remote`.  
- 中文: `美国 小云 电视盒`, `小云 10P 购买 美国`, `svicloud 美国 保修`.

### Content to publish (EN + 中文)
1. **10P+ vs 10S — Which one is right for me?**  
2. **Setup in 3 Minutes** (short video + steps)  
3. **Wi‑Fi Fixes & Troubleshooting** (clear, scannable)

---

## 7) Structured Data (drop‑in JSON‑LD)

> Replace URLs/SKUs/prices to match your final site.

```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "SVICLOUDTVBOX.US",
  "url": "https://svicloudtvbox.us/",
  "logo": "https://svicloudtvbox.us/media/logo.png",
  "contactPoint": [{
    "@type": "ContactPoint",
    "telephone": "+1-000-000-0000",
    "contactType": "customer support",
    "areaServed": "US",
    "availableLanguage": ["en","zh"]
  }],
  "sameAs": [
    "https://www.facebook.com/yourpage",
    "https://www.youtube.com/@yourchannel"
  ]
}
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "SVICLOUD 10P+ TV Box",
  "brand": {"@type": "Brand", "name": "SVICLOUD"},
  "sku": "SVI-10P-64",
  "description": "Authorized U.S. dealer. 4GB RAM / 64GB storage, 4K HDR, AV1 decode, voice remote, Kids & Karaoke apps.",
  "image": ["https://svicloudtvbox.us/media/10pplus-front.jpg"],
  "offers": {
    "@type": "Offer",
    "priceCurrency": "USD",
    "price": "248.99",
    "availability": "https://schema.org/InStock",
    "url": "https://svicloudtvbox.us/shop/svicloud-10p-plus"
  }
}
</script>
```

---

## 8) Compare Table (Markdown)

| Feature | SVICLOUD 10P+ | SVICLOUD 10S |
|---|---|---|
| RAM / Storage | **4GB / 64GB** | **2GB / 32GB** |
| Video | 4K HDR, AV1 | 4K HDR, AV1 |
| Voice Remote | Yes | Yes |
| Kids App | **Yes** | — |
| Karaoke App | **Yes** | — |
| Best For | Families, Sports, 4K | Entry/value, secondary TV |

Add a “**Who should buy which**” section under the table with plain‑English guidance.

---

## 9) Ads & Demand Capture

### Google Search (start here)
- **Campaign A – Brand exact:** `svicloud tv box`, `svicloud 10p`, `svicloud 10s`, `小雲 盒子`.  
- **Campaign B – Buy intent:** `svicloud 10p buy`, `svicloud usa`, `svicloud warranty`.  
- **Ad assets:** sitelinks (10P+, 10S, Compare, Warranty), callouts (Ships from USA), structured snippets (Features).  
- **Negative keywords:** `free movies`, `kodi`, `pirated`, `cracked`, `torrent`.

### YouTube/TikTok (15–30s)
- Unbox → plug → 3 benefits → price → CTA. EN + 中文 captions.

### Meta (FB/IG)
- Target Chinese‑speaking communities + CTV/streaming interests.  
- Use bilingual creatives; drive to model pages.

---

## 10) Operations & Policy Templates

### Shipping (U.S. only)
- Handling: **1–3** business days; Carriers: USPS/UPS.  
- Free shipping over **${{FREE_SHIP_THRESHOLD}}**; otherwise flat rate.  
- Tracking via email/SMS; delivery estimates shown in cart/checkout.

### Warranty & Returns
- **1‑Year U.S. warranty** against defects.  
- **30‑day returns** (unopened/full refund; opened/working may incur restock).  
- RMA steps: contact → RMA # → send to U.S. address → inspect → refund/replace.

### Support
- Hours: **10am–7pm (Pacific)**, Mon–Sat; **English/中文**.  
- Channels: Phone/SMS/WhatsApp/Email; remote assist script ready.

### Authenticity
- Serial capture at fulfillment; include “Authorized Dealer” card in box.  
- Footer disclosure: “Authorized SVI CLOUD Dealer. Not the manufacturer’s official website.”

---

## 11) Analytics & KPIs

- **Core:** Sessions, CR%, AOV, CAC, ROAS, Refund rate, Ticket volume.  
- **Targets (starter):** CR% ≥ 1.5%, AOV ≥ $200, ROAS ≥ 2.5, Refunds ≤ 3%.  
- **Dashboards:** GA4 ecommerce; ad platform dashboards; weekly sheet snapshot.

---

## 12) QA / Launch-Day Smoke Test

- [ ] Home loads < 2.0s on mobile (LCP green)  
- [ ] PDP adds to cart; checkout completes with Stripe test → Live transaction $1  
- [ ] Emails: order / reset password arrive (no spam)  
- [ ] Pixels fire (GA4 purchase event, Meta, TikTok)  
- [ ] Sitemap valid in Search Console  
- [ ] 404s → 301s; canonical correct  
- [ ] Chinese `/zh/` pages live with `hreflang`

---

## 13) Phase‑2 (Azure / nopCommerce roadmap)

- Keep URLs where possible; set **301s** for any changes.  
- Migrate products, orders, reviews.  
- Add RMA portal, wholesale pricing, dealer/ERP hooks, advanced analytics, and custom dashboards in .NET.  
- Performance via Azure Front Door/CDN; secrets in Key Vault; logs in App Insights.

---

## 14) Copy Blocks (paste-ready)

### Hero (EN)
**SVICLOUD TV Box — Authorized U.S. Dealer**  
Ships from the USA • 1‑Year Warranty • English/中文 Support  
**[Shop 10P+]**  **[Shop 10S]**

### Hero (中文)
**SVICLOUD 小云电视盒 — 美国官方授权经销商**  
美国本土发货 • 一年保修 • 英文/中文客服  
**【购买 10P+】**  **【购买 10S】**

### Why Buy From Us (bullets)
- Ships from the USA — no customs delays  
- Genuine hardware & firmware（授权经销）  
- 1‑Year U.S. warranty & easy returns  
- English/中文 technical support

---

## 15) Legal & .US Notes

- Use brand name under **authorized reseller** terms; avoid “Official site” phrasing.  
- `.US` Nexus = you’re fine (U.S. resident & business).  
- `.US` WHOIS privacy is **not** allowed; register under your LLC (use business address).

---

### Fill‑ins before launch

- **FREE_SHIP_THRESHOLD**: $_____ (e.g., $99)  
- **Support phone/WhatsApp**: +1‑___‑___‑____  
- **Business address** (for footer & policies): _______________________  
- **Logo & PDP images**: `/media/...` paths finalized  
- **Final prices**: 10P+ $_____ · 10S $_____  

---

*Version 1.0 — generated for svicloudtvbox.us*
