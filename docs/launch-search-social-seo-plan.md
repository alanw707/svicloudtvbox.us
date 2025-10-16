# Prioritized Launch Plan – Google Search + Facebook (Meta) + Google SEO

Last updated: 2025-02-14  
Owner: Growth/Performance

## 0) Goal & Guardrails

- Objective: Launch compliant, high-intent acquisition via Google Search; build demand and retarget via Facebook; improve organic discoverability via Google SEO.  
- Guardrails: Position SviCloud as an Android-based streaming media player with voice-enabled remote (10P+) and home karaoke features. Do not imply access to unauthorized/pirated content. Avoid risky terms (e.g., "free channels").

---

## 1) Google Search – Campaign Blueprint

### 1.1 Structure
- Campaigns:  
  - Brand (exact/phrase)  
  - Features (voice remote, karaoke, kids mode)  
  - Problem/Solution (setup, pairing, Wi‑Fi, Ethernet)  
  - Compare/Alternatives (vs Chromecast/Shield/Fire TV/Apple TV; 10P+ vs 10S)  
- Ad Groups: Granular by intent/theme; 3–5 RSA ads per group; 3–6 sitelinks at campaign level.

### 1.2 Keywords (starter sets)
- Brand:  
  - [exact] sviCloud  
  - [exact] sviCloud tv box  
  - [phrase] sviCloud box setup  
- Features:  
  - [phrase] android streaming box with voice remote  
  - [phrase] android tv box karaoke  
  - [phrase] family streaming box kids mode  
- Problem/Solution:  
  - [phrase] tv box remote not pairing  
  - [phrase] android tv box setup guide  
  - [phrase] improve tv box wifi streaming  
- Compare/Alternatives:  
  - [phrase] android tv box vs chromecast  
  - [phrase] nvidia shield alternative  
  - [phrase] roku vs android tv box  

ZH-TW mirrors (policy-safe phrasing):  
- [phrase] 小雲 電視盒 安裝教學  
- [phrase] android 串流 電視盒 語音 遙控  
- [phrase] 家用 卡拉OK 電視盒  

### 1.3 Negatives (seed list)
- free, cracked, illegal, pirate, m3u, iptv list, kodi build, torrent, hack, jailbroken, no subscription free, scraper, porn (exclude from non-18+), adult channels (for generic campaigns), download movies free.

### 1.4 RSA Assets (templates)
- Headlines:  
  - Family Streaming + Karaoke in One Box  
  - SviCloud 10P+ Voice Remote  
  - Easy 7‑Step Setup (EN/中文)  
  - Kid-Friendly Streaming Modes  
  - Smooth Playback. Wi‑Fi or Ethernet  
  - Android Streaming Box – Simple Setup  
  - Bilingual Support: English + 繁中  
- Descriptions:  
  - Upgrade your living room with voice search, kids mode, and in‑home karaoke. Simple setup, secure checkout.  
  - Android-based streaming box with a family focus. Get started in minutes with our step‑by‑step guide.  

### 1.5 Sitelinks
- Setup Guide (EN) → /guides/how-to-set-up (map to actual route)  
- 安裝教學 (繁中) → /zh/guides/how-to-set-up  
- Compare 10P+ vs 10S → /compare/  
- FAQ → /faq/

### 1.6 Targeting, Bids, Budgets
- Geos: US (refine via performance).  
- Languages: EN; separate ZH-TW campaign if landing exists.  
- Bidding: Start Maximize Conversions (no tCPA) for learning; add tCPA after 30–50 conversions.  
- Daily budget: Brand $50–$150; Features/Problem $150–$400 combined; Compare $100–$250.

### 1.7 Tracking & UTMs
- UTM template: `utm_source=google&utm_medium=paid_search&utm_campaign={{campaign.name}}&utm_content={{adgroup.name}}&utm_term={keyword}`.  
- Verify GA4, Enhanced Conversions, server events where feasible.

### 1.8 KPIs
- CTR ≥ 6–10% brand, 3–6% non-brand; CVR ≥ 3–5% landing; CAC ≤ target; QS ≥ 7.

---

## 2) Facebook (Meta) – Prospecting & Retargeting

### 2.1 Prereqs
- Pixel events: ViewContent, AddToCart, InitiateCheckout, Purchase mapped. Domains verified.  
- Product catalog optional (for Advantage+ Shopping-like flows) or standard Conversion objective.

### 2.2 Structure
- Campaign 1 – Prospecting (Sales/Conversions)  
  - Ad Set A: Broad (18–65+, ENG)  
  - Ad Set B: Interests (family entertainment, karaoke, Android TV, smart home)  
  - Ad Set C: ZH-TW (if running Chinese ads + landings)  
- Campaign 2 – Retargeting (30‑day site engagers; video viewers 50%+)

### 2.3 Creatives (matrix)
- Hooks:  
  - “Family Streaming + Karaoke—One Box”  
  - “Pair Your Voice Remote in Seconds (VOL‑/VOL+)”  
  - “Kid Mode + Multilingual Subtitles”  
- Formats: 15–30s video, 1:1 + 9:16; carousel (features: voice/kids/karaoke/setup).  
- Copy (Primary Text – EN):  
  - “Upgrade movie night with voice search, Kid Mode, and in‑home karaoke. SviCloud 10P+ is the Android streaming box built for families—simple 7‑step setup.”  
- Copy (Primary Text – ZH-TW):  
  - 「親子串流 + 居家卡拉OK，一機搞定！小雲 10P+ 支援語音操控、兒童模式與簡單 7 步驟安裝。」  
- Headlines:  
  - “Voice Remote + Karaoke” / 「語音遙控 + 卡拉OK」  
- CTAs: Shop Now / Learn More (map to PDP vs Guide as test).

### 2.4 Targeting & Budgets
- Start ABO: $50–$150/ad set/day; expand to CBO post‑signal.  
- Geos: US; exclude low‑intent placements as needed; keep Advantage Placements on initially.  
- Brand safety: no risky content claims.

### 2.5 Measurement
- KPIs: Thumb‑stop rate, 3‑sec views, CTR ≥ 1.5–2.5%, LP CVR ≥ 3%; blended CAC vs target.  
- Creative iteration every 10–14 days; winners scaled 20–30% at a time.

---

## 3) Google SEO – High-Impact Actions (30 Days)

### 3.1 Technical
- Hreflang between EN/ZH-TW pages (Guides/FAQ/PDPs).  
- Add Schema:  
  - HowTo for setup guides, FAQPage for FAQs, Product for PDPs, BreadcrumbList sitewide.  
- Sitemaps: ensure XML up to date; submit to GSC + Bing.  
- Performance: compress images to WebP, lazy‑load, preconnect fonts/CDNs.

### 3.2 On‑page
- Titles, meta descriptions with target phrases.  
- Internal links: cross‑link Guides ↔ FAQ ↔ Compare ↔ PDP; add “related links” section.  
- Alt text for new screenshots; file names descriptive.

### 3.3 Content
- Publish 4–6 articles (EN/ZH) on: voice remote pairing, karaoke setup, kids mode tips, Ethernet vs Wi‑Fi, compare vs Chromecast/Shield/Fire TV.  
- Add compare tables with clear CTAs; ensure compliance language.

### 3.4 Measurement
- GSC: track top queries, CTR changes, position by cluster.  
- KPIs: non‑brand clicks +30% MoM, time on page ≥ 90s, assisted conversions.

---

## 4) Compliance & Risk
- Avoid terms implying unauthorized access to paid content.  
- Ads and landing copy highlight features (voice, karaoke, kids, Android).  
- Influencer briefs to include policy‑safe talking points.

---

## 5) UTM & Naming Conventions
- Search: `utm_source=google&utm_medium=paid_search&utm_campaign={{campaign}}&utm_content={{adgroup}}&utm_term={keyword}`  
- Meta: `utm_source=facebook&utm_medium=paid_social&utm_campaign={{campaign}}&utm_content={{adset}}&utm_creative={{ad.id}}`
- Name schemas: `US|EN|Search|Features|Exact` / `US|EN|FB|Prospecting|Broad` etc.

---

## 6) 30‑Day Timeline & Owners

Week 1  
- Finalize keywords, negatives, RSAs, sitelinks. (Growth)  
- Prep 3 video creatives + 2 static variants (EN/ZH). (Creative)  
- Implement hreflang + HowTo/FAQ schema on Guides/FAQs. (Dev/SEO)

Week 2  
- Launch Search (Brand + Features + Problem/Solution). (Growth)  
- Launch FB Prospecting + Retargeting; set rules for spend scaling. (Growth)  
- Publish 2 articles (EN/ZH). (Content)

Week 3  
- Add Compare/Alternatives Search campaign; test PDP vs Guide landings. (Growth)  
- Expand creatives; rotate hooks. (Creative)  
- Publish 2 more articles. (Content)

Week 4  
- Optimize bids, negatives, placements; scale winners. (Growth)  
- Add internal links; push FAQs into schema. (SEO)  
- Report: CAC/ROAS/CVR + organic gains; plan next month. (Growth/SEO)

---

## 7) Asset Checklist
- RSAs (EN/ZH), sitelinks, callouts.  
- 3× 15–30s videos (voice search, karaoke, kids mode).  
- 2× carousels (features).  
- 2× static hero images.  
- Landing page mapping and UTMs.  
- Hreflang + schema implemented.

---

## 8) Launch Readiness QA
- Pixels and GA4 events firing correctly.  
- Payment trust (Apple Pay/Google Pay via Stripe) messaging present.  
- Page speed acceptable (LCP < 2.5s on landing pages).  
- All ad copy passes policy; screenshots are feature‑focused.
