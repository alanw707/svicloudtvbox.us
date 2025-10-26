# SEO Plan for svicloudtvbox.us (Q4 2025 – Q2 2026)

This plan is tailored to your WordPress + WooCommerce site hosted on LiteSpeed (Hostinger). It focuses on crawlability, technical best practices, on-page optimization, content strategy, and authority building. It includes an execution timeline, owners, and measurable KPIs.

## Goals & KPIs

- Organic traffic: +60–100% non-brand organic sessions in 90–120 days.
- Indexation: 100% of key pages (home, products, categories, compare, about) indexed within 30 days.
- Rankings: Top 10 for target terms like “SVICLOUD 10P+”, “Chinese TV box US”, “Chinese TV box with warranty”, “SVICLOUD US dealer”, plus zh-Hant variants such as “小雲電視盒 美國” and “小雲電視盒 10P+ USA”.
- Conversions: +30–50% organic revenue; +20% organic CVR.
- Core Web Vitals (field): LCP ≤ 2.5s, CLS ≤ 0.10, INP ≤ 200ms.

Primary measurement: Google Search Console (GSC), GA4, and Core Web Vitals field data (CrUX). Secondary: Bing WMT.

## Immediate Fixes (Week 0–1)

1) Fix zh canonical and hreflang
- Current: `/zh/` outputs canonical to `/` (English). Set canonical to `https://svicloudtvbox.us/zh/`.
- Ensure 2-way hreflang between EN and zh for every paired page (home, product, compare, about, etc.).
- If using Yoast/RankMath + WPML/Polylang, configure per-language canonical and `rel="alternate" hreflang` tags; ensure each language page references itself in canonical and each other in hreflang.

2) Crawlability & Sitemaps
- robots.txt is fine (admin/logs disallowed). Keep it. Consider also disallowing `/cart/`, `/checkout/` (already noindex via meta; optional duplicate block in robots for crawl budget).
- Submit `https://svicloudtvbox.us/wp-sitemap.xml` in GSC and Bing WMT; ensure products, pages, categories are included.
- Verify clean 200/301 responses for canonical URLs (http→https, www→non-www already good).

3) Coverage & Indexing
- Use GSC URL Inspection to request indexing for: home, /zh/, top products, compare, about, contact, category pages.
- Ensure all 404s return proper 404 and are linked to helpful navigation.

## Technical SEO (Weeks 1–3)

Performance (LiteSpeed + WordPress)
- Enable/verify LiteSpeed Cache features: HTML minify, CSS/JS combine only if stable, defer non-critical JS, load CSS asynchronously with critical CSS, lazy-load images/iframes, QUIC.cloud image optimization to WebP/AVIF.
- Fonts: host fonts locally if possible; add `font-display: swap`; preconnect to `https://fonts.googleapis.com` and `https://fonts.gstatic.com` (already partially present); preload key font files.
- Images: ensure hero/product media are compressed (WebP/AVIF), properly sized, width/height set, and lazy-loaded below the fold.
- Third-parties: audit GTM and remove unused tags; delay non-essential scripts until interaction.

Canonicalization & Parameters
- One canonical per page; no cross-language canonicals.
- Trailing slash policy: keep consistent (current WordPress default is fine).
- In GSC, define parameter handling to avoid duplicate crawl from query params (e.g., tracking, filters, `?v=`).

Structured Data (JSON-LD)
- Site-wide: `Organization`, `WebSite` with `SearchAction`, `BreadcrumbList`.
- Products: `Product` with `brand`, `sku`, `mpn` (if available), `offers` (price, currency, availability), `aggregateRating` and `review` (when you have real reviews), `gtin` if applicable.
- Categories: `ItemList` (list of products) with item position and URL.
- Content/FAQ: `FAQPage` on relevant landing pages (shipping, warranty, setup, compare).
 - Reference: See `docs/marketing/google-review-stars-playbook.md` for how to earn organic review snippets, GBP stars, and Ads Seller Ratings safely/compliantly.

Internationalization
- For each EN page, ensure a zh counterpart where relevant, with unique content (not machine-only). Add hreflang: `en-US` ↔ `zh-TW` and `x-default` pointing to EN home.
- Validate hreflang with GSC International Targeting (legacy) or third-party validators.

Internal Linking
- Enable breadcrumb navigation (visible and in schema).
- Link from homepage and head nav to key categories and compare page; add related products and “popular bundles” cross-links.
- Create an HTML sitemap page for users (and bots) listing top categories and products.

Security/UX
- Maintain HSTS; keep CSP `upgrade-insecure-requests` (already present). Add `referrer-policy: strict-origin-when-cross-origin` if not present.

## On-Page Optimization (Weeks 2–6)

Titles & Meta Descriptions
- Craft unique, benefit-led titles per page (60–65 chars) and meta descriptions (140–160 chars) featuring primary keywords and USPs (ships from USA, 1-year U.S. warranty, no monthly fees, bilingual support).
- Example (Home):
  - Title: “SVICLOUD TV Box US – 10P+ Chinese TV Box | Ships from USA”
  - Description: “Authorized U.S. dealer. SVICLOUD 10P+ streams Chinese & global channels. 1-year U.S. warranty, no monthly fees, fast shipping, bilingual concierge.”

Headers & Copy
- Ensure 1 H1 per page; use H2s/H3s for features, specs, FAQs.
- Product pages: Expand content beyond specs—add use-cases, channel lineup overview, what’s in the box, shipping/returns, warranty, setup guide.
- Add “Compare Models” tables with schema, internal links to product pages.
- zh pages: Human-edited Chinese copy; align USPs and localize shipping/returns language.

Media & Alt Text
- Add descriptive alt text to all key images (e.g., “SVICLOUD 10P+ streaming device with remote”).
- Use caption/nearby text to reinforce keywords naturally.

Conversion Enhancements
- Prominent trust signals: warranty badge, shipping speed, US-based support.
- Clear CTAs above the fold; sticky cart for product pages.

## Content Strategy (Weeks 3–12)

Content Hubs & Topics
- Hub: “Chinese TV Boxes in the USA – Buyer’s Guide”.
- Clusters (publish 2–3 per week):
  1) “SVICLOUD 10P+ vs 9P: What changed in performance, AV1, Wi‑Fi 6?”
  2) “How to set up SVICLOUD on any TV (step-by-step, images)”
  3) “Best Chinese TV box for seniors: Remote, voice search, support options”
  4) “AV1 decoding and 4K HDR: Why it matters for streaming in 2025”
  5) “Karaoke on SVICLOUD: microphone setup, song libraries, tips”
  6) “Wi‑Fi 6 vs Ethernet for streaming boxes: Real-world tests”
  7) “SVICLOUD channels overview: news, drama, kids (with updates)”
  8) “Troubleshooting buffering: network, DNS, firmware updates”
  9) “SVICLOUD 10P+ accessories: USB 3.0 storage, remotes, mounts”
  10) “Shipping from USA: delivery times, returns, and warranty explained”
  11) “Compare SVICLOUD to other Chinese TV boxes: features and support”
  12) “Parental controls & safe kids content on SVICLOUD”

Editorial
- Each article: 900–1,600 words; include 2–3 internal links to products/categories; 1–2 FAQ schema items; original images/screenshots.
- Publish in EN and zh with localized screenshots and examples.

## Authority & Off-Page (Weeks 4–12)

Google Merchant Center (Free Listings)
- Create WooCommerce product feed (price, availability, condition, GTIN/MPN if available) and submit to GMC for free product listings; link to GA4 and GSC.

Reviews & UGC
- Implement Woo review collection by post-purchase email; surface aggregateRating schema only when you have real reviews.
- Encourage reviews on Trustpilot/Google Business Profile (if you have a physical address or service office).

Digital PR / Partnerships
- Seed review units to niche tech/streaming blogs and YouTube creators; target 5–10 pieces with dofollow links.
- Publish data-driven posts (e.g., streaming performance tests) and pitch to industry newsletters.

Profiles & Consistency
- Create/optimize brand profiles (YouTube, X/Twitter, Facebook) and link via `sameAs` in Organization schema.

## Monitoring & Reporting (Ongoing)

Weekly
- GSC: Search results (queries, pages), Coverage, Enhancements; check for hreflang errors.
- GA4: Organic sessions, CVR, revenue; top landing pages; site search queries.
- CWV: CrUX trends; PageSpeed Insights sampling for templates (home, product, category, blog post).

Monthly
- Report on KPIs vs goals; annotate deployments/content releases; identify new keyword opportunities and pages to build.

Alerting
- Set up uptime monitoring and 5xx alerts; track sudden drops in organic clicks (possible indexing or robots issues).

## Compliance & Risk

- Avoid unsubstantiated claims about channels/availability; include disclaimers where needed.
- Ensure terms, returns, and warranty pages are accessible and indexable.
- E‑E‑A‑T: Add “About” with team/service info, US warranty details, and support contact.

## Implementation Notes (WordPress)

- SEO plugin: Yoast or RankMath to manage titles, meta, canonicals, breadcrumbs, schema basics.
- Internationalization: WPML/Polylang with language-specific canonicals and hreflang.
- Performance: LiteSpeed Cache + QUIC.cloud image optimization; ensure critical CSS generation.
- Schema: Validate with Rich Results Test. For custom needs, add theme-level JSON‑LD or use a lightweight schema plugin.

Example filter (Yoast) to force zh canonical on homepage:

```php
add_filter('wpseo_canonical', function ($url) {
  if (is_front_page() && (get_locale() === 'zh_TW')) {
    return home_url('/zh/');
  }
  return $url;
});
```

## Roadmap & Owners

Week 0–1 (Owner: Dev + SEO)
- Fix canonical on `/zh/`; validate hreflang sitewide.
- Submit sitemaps; request indexing of key pages in GSC/Bing.
- Baseline report (GSC, GA4, CWV).

Weeks 1–3 (Owner: Dev)
- Implement performance improvements; schema site-wide; breadcrumbs; internal linking enhancements.

Weeks 2–6 (Owner: Content + SEO)
- Rewrite titles/meta; expand product copy; publish first 6 articles (EN+zh).

Weeks 4–12 (Owner: Marketing/PR)
- Launch reviews/UGC program, outreach to creators; set up Merchant Center feed.

Ongoing (Owner: SEO)
- Monitor, iterate, and grow the content hub; monthly reporting.

### Current Action Items (Oct 26, 2025)

1. **Structured data hardening**
   - ✅ Deploy enhanced Organization + ItemList schema on homepage (Rank Math hook + theme JSON-LD).
   - ✅ Extend Product/ItemList JSON-LD to PDPs, shop archive, and /compare so every shopping page passes Rich Results (Oct 26 deploy).
   - ➡ Prepare `aggregateRating` / `review` fields (placeholder structure ready; populate once authentic data is available) to clear remaining warnings.
   - ✅ Oct 27 2025 — Ran Rich Results “Test live URL” for home, /zh/, 10P+, 10S, /shop, and /compare after deploy + LiteSpeed purge; keep weekly cadence going.
2. **Thumbnail & social preview parity**
   - ➡ Mirror the homepage’s `max-image-preview:large`, Open Graph, and Twitter image tags on PDPs and top blog posts so SERP thumbnails appear consistently.
3. **Keyword execution**
   - ➡ Ship next blog briefs targeting “小雲電視盒 美國” and “小雲電視盒 10P+ USA”, including zh-Hant sections and internal links from homepage/compare.
4. **Monitoring & documentation**
   - ➡ Capture deploy + Rich Results outcomes in this plan (date, issue resolved) and keep LiteSpeed purge noted for future schema releases.

## Success Criteria

- Indexed: All key URLs visible in GSC Coverage and appearing on “site:” searches.
- Performance: Field CWV pass on key templates.
- Visibility: Growing impressions and CTR for non-brand queries.
- Revenue: Measurable lift from organic traffic with improved CVR on product pages.
