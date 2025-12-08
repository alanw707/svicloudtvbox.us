# Google Snippet & Keyword Growth Plan — svicloudtvbox.us

Version 1.0 — Last updated: 2025-10-24  
Owner: Marketing × SEO × Web Ops  
Scope: Homepage thumbnails, organic CTR lifts, priority keyword growth (EN + zh‑Hant)

---

## 1. Objectives

- Earn image thumbnails for the homepage (and key posts) to raise CTR.  
- Improve rankings for our high-intent keyword clusters, including zh‑Hant queries (“小雲電視盒 美國”, “小雲電視盒 10P+ USA”).  
- Ship technical + content updates in 8 weeks, backed by measurable KPIs (CTR, avg position, clicks).

KPIs
- Homepage CTR +0.5–1.5 pp for branded/generic queries once thumbnails appear.  
- Top 10 rankings for: “SVICLOUD 10P+”, “Chinese TV box US”, “Chinese TV box with warranty”, “SVICLOUD US dealer”, “小雲電視盒 美國”, “小雲電視盒 10P+ USA”.  
- Publish 6 dual-language guides (EN/zh) and refresh 5 existing posts with inline imagery + schema.

---

## 2. Why Thumbnails Are Missing

- No explicit `max-image-preview: large` tag → Google cannot show large thumbnails reliably.  
- Missing OG/Twitter meta for homepage → no clear canonical image.  
- No WebPage JSON-LD with `primaryImageOfPage`.  
- Some posts rely on CSS background images; no inline `<img>` above the fold; featured images under 1200 px.  
- Article schema absent → Google lacks structured context.

Verification: Rendered HTML from Search Console shows hero image but lacks the meta signals above; Rich Results Test doesn’t detect WebPage/Product schema (pending SEO plugin rollout).

---

## 3. Technical Implementation (Homepage First)

| Task | Details | Owner | Timeline |
| --- | --- | --- | --- |
| `max-image-preview` | Add `<meta name="robots" content="max-image-preview:large">` (skip/adjust if SEO plugin handles it). Guard against duplicates. | Web Ops | Week 1 |
| OG/Twitter tags | Output `og:type`, `og:title`, `og:description`, `og:url`, `og:image` (>=1200 px), `twitter:card=summary_large_image`, with locale-aware alt text. Skip if Yoast/RankMath active. | Web Ops | Week 1 |
| WebPage JSON-LD | Add JSON-LD for homepage only: `@type: WebPage`, `name`, `description`, `url`, `primaryImageOfPage`. Guard against SEO-plugin duplication. | Web Ops | Week 1 |
| Hero image spec | Ensure `svicloud-hero-product.png` (or new `-1200x630` asset) is ≥1200×630, optimized, with descriptive filename/alt. Consider `loading="eager"`. | Design | Week 1 |
| Sitemap/image index | Confirm hero image is referenced in image sitemaps (WordPress default) and not blocked. | Web Ops | Week 1 |

Testing: Run Rich Results Test + URL Inspection on home after deployment; confirm `<img>` is visible and meta tags exist. Request recrawl.

---

## 4. Post / Article Enhancements

Editorial SOP
- Every post must have a Featured Image ≥1200 px wide (ratio 1.91:1 or 1:1).  
- Place at least one inline `<img>` near the top of the content (visible, not CSS background).  
- Add concise, keyword-aware alt text (EN + zh).  
- Keep file names descriptive (e.g., `svicloud-setup-step1-1200.jpg`).

Schema
- Install/enable Rank Math (Woo module) or Yoast Woo; configure Article schema with `image`, `headline`, `datePublished`, `author`.  
- If plugin unavailable, add custom Article JSON-LD generator for posts with valid images.

Validation
- Rich Results Test on top posts; fix warnings (missing author/date/image).  
- Use Search Console Enhancements → Article/Review to monitor errors.

---

## 5. Keyword Growth Strategy

### 5.1 Targets & Baseline

- Pull top 90-day GSC queries; focus on keywords with avg position 11–30 and impressions >50.  
- Priority clusters (EN + zh):
  - Product: “SVICLOUD 10P Plus”, “SVICLOUD 10S”, “SVICLOUD TV box US/USA”.  
  - Generic: “Chinese TV box US / USA / North America”, “Traditional Chinese TV box”, “華人 電視 盒 美國”, “中文 電視盒 北美”.  
  - Intent: shipping, warranty, setup, compare, troubleshooting.  
  - New additions (per Oct 24 request): “小雲電視盒 美國”, “小雲電視盒 10P+ USA”.

### 5.2 Page Mapping

| Keyword Cluster | Primary Page | Supporting Content |
| --- | --- | --- |
| Brand/Model (EN & zh) | Homepage + 10P/10S PDPs | Compare page, spec sheets |
| “Chinese TV box US/USA” | Homepage + forthcoming “Buyer’s Guide” pillar | Setup guide, warranty/shipping article |
| “小雲電視盒 美國”, “小雲電視盒 10P+ USA” | zh homepage + zh PDPs | zh compare page, zh guides |
| “Setup/Tutorial” | “How to set up SVICLOUD in 5 minutes” guide | Video shorts, FAQ |
| “Troubleshooting/Buffering” | Technical support article | Support CTA, Wi-Fi optimization post |

### 5.3 Content Roadmap (8 Weeks)

- **Week 1–2:** Refresh homepage copy (titles/meta/H1/intro) to include target keywords; add FAQ block with schema. Finish Product schema plugin setup.  
- **Week 3–4:** Publish EN + zh:
  1. “SVICLOUD 10P Plus vs 10S – Which one fits your home?”  
  2. “Chinese TV Box Setup Guide (5-minute SVICLOUD walkthrough)” (include video + screenshots).
- **Week 5–6:** Publish EN + zh:
  3. “Best Chinese TV Box for North America (Warranty, shipping, support)”  
  4. “Troubleshooting buffering on SVICLOUD (network, firmware, DNS tips)”.
- **Week 7–8:** Refresh existing posts with updated H1/meta + inline images + FAQ schema; publish “Shipping & Warranty Policy Explained” article (EN/zh). Add internal links from homepage and PDPs to all new guides.

### 5.4 On-Page Actions

- Titles/meta: include “Chinese TV box US/USA”, “SVICLOUD 10P Plus”, “小雲電視盒 美國” where relevant.  
- H1 + intro paragraphs reflect target keywords; zh versions localized.  
- Add FAQ sections with `FAQPage` schema for shipping, warranty, setup topics.  
- Add schema for Compare page (Product comparison or FAQ) to increase SERP features.

### 5.5 Internal Linking

- Homepage “Featured Guides” block linking to top 3 guides (English + zh).  
- Product pages → compare page, setup guide, troubleshooting article (contextual anchors).  
- Blog posts → relevant PDPs and other guides (≥3 contextual links/post).  
- Breadcrumbs sitewide (HTML + schema) to reinforce site structure.

### 5.6 Authority & Trust

- Creator program: seed devices to 3–5 NA Chinese tech reviewers; request backlinks to product or guide pages.  
- Diaspora media (Dealmoon, ChineseInLA, World Journal) sponsored content linking to guides/compare.  
- Collect Google/Store reviews (see `docs/marketing/google-review-stars-playbook.md`); embed snippets on PDPs to reinforce trust.

---

## 6. Measurement & QA

- Weekly: GSC → Performance → filter by Page to track home/PDP/guides impressions, positions, CTR.  
- Monthly: Manual ranking check for the six priority keywords; log in spreadsheet.  
- Search Console Enhancements: watch Product/Article/FAQ snippet reports for errors.  
- Core Web Vitals: ensure LCP ≤2.5s, CLS ≤0.1 on home/product/blog templates (LiteSpeed + QUIC.cloud).  
- CTR experiments: after thumbnails launch, compare CTR pre/post (3–4 weeks) for top queries.

---

## 7. Verification Checklist

- [ ] `max-image-preview:large` present on homepage and posts.  
- [ ] OG/Twitter tags outputting 1200+ px image, localized titles/descriptions.  
- [ ] WebPage JSON-LD with `primaryImageOfPage` detected in Rich Results Test.  
- [ ] Rank Math or Yoast outputs Product + Article schema without warnings.  
- [ ] Featured images ≥1200 px + inline image above the fold on each target post.  
- [ ] FAQ schema valid on homepage FAQ strip and new guides.  
- [ ] Internal links updated (homepage → guides; PDPs ↔ guides).  
- [ ] Search Console shows Product/Article/FAQ enhancements without errors.  
- [ ] Priority keywords tracked weekly with latest avg positions.

---

## 8. Dependencies & Risks

- Need SEO plugin (Rank Math or Yoast Woo) configured to avoid hand-written duplicates.  
- Hero/featured images must be compressed yet ≥1200 px; coordinate with design to avoid heavy load.  
- If content is duplicated via translations, ensure hreflang + canonical fixes are live to avoid cannibalization.  
- Google may still choose not to show thumbnails; mitigation: ensure all signals (structured data, inline images, meta) are strong and request recrawl.

---

## 9. Related References

- docs/marketing/google-review-stars-playbook.md  
- docs/seo-plan-svicloudtvbox-us.md  
- docs/svicloudtvbox-backlog.md (Epic F, Epic L tasks for SEO + reviews)

