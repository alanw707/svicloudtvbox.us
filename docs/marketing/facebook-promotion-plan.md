# Facebook Promotion Plan — svicloudtvbox.us

Version 1.0 — Last updated: 2025-10-06

Owner: Marketing
Scope: Facebook Page, Shop/Catalog, Organic + Paid Ads, Measurement

---

## Objectives

- Establish a compliant, conversion-ready Facebook Page and Shop
- Sync WooCommerce products for dynamic catalog/retargeting
- Build trust with educational content and social proof
- Drive revenue via Advantage+ Shopping and remarketing
- Measure ROAS with Pixel + Conversions API and UTMs
- Localize experience for US-based Traditional Chinese audiences

---

## Create The Page (New Pages Experience)

- Page name: “SVICLOUD 電視盒美國授權經銷店”
- Username: “@svicloudtvboxus” (簡潔易記；沿用英文便於搜尋)
- Category: “Shopping & retail” or “Electronics”
- Profile: square high‑res logo (≥1024×1024, 1:1)
- Cover: brand banner (16:9, ≥1920×1080)，加入繁體宣傳標語與明確 CTA
- CTA button: “Shop on Website”（可搭配繁體顯示「前往授權商城」）→ https://svicloudtvbox.us/
- About: 以繁體中文撰寫 1–2 句定位，強調授權經銷、保固與客服聯絡資訊（Email／電話／營業時間／網址）
- Tabs/template: Shopping template, enable “Shop” and “Reviews”
- Messaging: Turn on Messenger, set greeting + FAQ quick replies（繁體）
- Connect accounts: Link Instagram; optionally WhatsApp Business
- Page roles & security: Use Meta Business Manager, assign roles, require 2FA

Suggested Page bio/About snippet（繁體）：

> SVICLOUD 電視盒美國授權經銷店｜5 分鐘完成安裝，介面流暢、客服即時支援。提供 SVICLOUD 10P Plus 與 10S，原廠授權保固、繁體中文服務與安心退換貨。需要協助？歡迎隨時聯絡我們。

---

## Commerce & Catalog (WooCommerce Sync)

1) Install and configure “Facebook for WooCommerce” (Meta) plugin.
2) Connect Business Manager → select Page, Ad Account, Pixel, and create/attach a Catalog.
3) Choose “Checkout on your website”.
4) Map product fields (title, price, availability, GTIN/MPN if available). Upload images ≥1024 px.
5) Ensure policy compliance: no IP infringement; avoid implying unauthorized content access.
6) Approve Shop in Commerce Manager; set shipping/returns to match site，補充美國運費、配送時間與退貨條件。
7) Create product sets: “SVICLOUD 10P Plus”, “SVICLOUD 10S”, “Accessories/Bundles”.

---

## Pixel + Conversions API

- Use Facebook for WooCommerce to auto‑install Pixel + CAPI.
- Standard events: ViewContent, AddToCart, InitiateCheckout, Purchase (with value + currency).
- Verify in Events Manager (Test events) and debug any deduplication warnings.
- Configure Aggregated Event Measurement: Purchase > InitiateCheckout > AddToCart > ViewContent.
- Add UTMs to all Page links/posts/ads for source‑of‑truth analytics, e.g.:
  - `?utm_source=facebook&utm_medium=social&utm_campaign=page_post&utm_content=hero_image`

---

## Content Strategy (What To Post)

Pillars
- Product demos: unboxing, setup, UI walkthroughs (focus on legal content sources)
- Education: cord‑cutting basics, device comparisons, troubleshooting tips
- Social proof: customer testimonials, reviews, before/after cable bills (no personal attributes)
- Offers: bundles, seasonal promos, limited‑time discounts
- Support: FAQ clips, warranty and returns explainer, how to contact support
- Behind‑the‑scenes: QC process, packing, team intro
- UGC: repost customer photos/videos with consent

Formats
- Short video/Reels (15–45s), square or vertical
- Carousels (features, accessories, how‑to steps)
- Lives (Q&A, setup workshop)
- Stories (promo reminders, polls, quick tips)

Visual standards
- Consistent brand colors and overlays; add subtle logo bug
- On‑screen captions for silent autoplay; maximize readability
- 1–3 relevant hashtags; keep copy concise and helpful

Tone
- Trustworthy, utility-focused, non-technical where possible
- Avoid any language implying unauthorized content access
- Communicate primarily in Traditional Chinese (繁體); keep product 名稱 與 CTA 語氣一致

---

## 30‑Day Posting Cadence (Example)

- 3 posts/week + 3–5 stories/week（建議依美國時區（ET／PT）排程）
- Weekly template
  - Mon: Demo/feature video (focus: 10P Plus)
  - Wed: Education carousel (setup tips, compatibility)
  - Fri: Offer/testimonial rotation
  - Stories: customer Q&A, polls, quick tips, re‑shares
- Pin 1–2 best posts (demo + offer) for new visitors

---

## Community & Support

- Enable “Click to Message” on posts periodically; use quick replies (配送、保固、安裝)
- Create a private “SVICLOUD Owners 繁體社群” Group linked to Page for support/UGC
- Comment moderation: respond within 24h, hide spam, escalate issues to DM
- Review generation: after purchase, email/SMS ask to leave a Page Recommendation

---

## Ad Strategy (Meta)

Structure
- Prospecting: Advantage+ Shopping Campaign (ASC) with Catalog; objective: Sales (website)
- Retargeting: Sales objective; ad set for site visitors/ATC/engagers
- Content amplification: Engagement/Video Views to build audiences on best demos

Audiences
- Prospecting: Broad (Advantage+), optional interests (home entertainment, cord‑cutting)
- Lookalikes: 1–3% from Purchasers, ATC, high‑value customers (once volume exists)
- Retargeting: 7/14/30‑day site visitors, ATC 14 days, video viewers 25–95%, Page/IG engagers

Creatives
- Prospecting: value prop + 2–4 benefits; short demo video + carousel
- Retargeting: social proof, FAQs, risk reducers (warranty, returns), limited‑time incentive
- Dynamic Product Ads (DPA): pull from Catalog for browse/ATC remarketing

Budgets & bidding
- Start: $50–$150/day total (≈60% ASC, 30% retargeting, 10% engagement)
- Keep changes <20% day‑to‑day to respect Learning Phase
- Optimize for Purchase; allow Advantage+ placements

Testing framework
- Test one variable at a time (hook line, thumbnail, offer)
- 3–5 day learning read; 7–10 days per test round
- Kill <1.0 ROAS early in retargeting; give prospecting more runway

Compliance
- No personal attributes; no illegal content implications; no unrealistic claims
- Focus on legal, quality, ease of use; avoid “watch everything for free” language

---

## KPIs & Measurement

- Core: ROAS, CPA (Purchase), AOV, Conversion Rate, CTR, CPC, CPM, Frequency
- Directional benchmarks (hardware D2C vary):
  - CPM $8–$20, CTR 1–2.5%, CPC $0.5–$1.5, CVR 1–3%
- Reporting cadence
  - Weekly: account summary and cohort (7/14/30‑day)
  - Ads Manager columns: Purchases, Cost/Result, Value, ROAS, ATC, IC
  - Cross‑check with WooCommerce revenue using `utm_source=facebook`

---

## Compliance & Risk Management

- Ensure positioning never implies accessing unauthorized content
- Publish clear warranty, returns, and usage policies on Page and site
- Keep proof of licensed content sources if referenced
- Moderate comments that solicit illegal use; do not engage
- Business verification + 2FA; restrict Page roles (least privilege)

---

## 60‑Day Rollout

Weeks 0–1
- Create Page; connect BM, Pixel+CAPI, Catalog sync
- Prepare 12–16 assets (videos, carousels); finalize About/Policies
- Soft‑launch Page; post 3 intro pieces; enable Reviews

Weeks 2–4
- Launch ASC + Retargeting; boost best demo content for engagement
- Build Group; run a “Setup Live” session; collect first reviews
- Optimize product feed fields and imagery; refine UTMs

Weeks 5–8
- Add Lookalikes; expand creative angles; test bundle offers
- Scale budgets on winners; rotate new hooks; refresh retargeting creatives
- Start influencer seeding and creator ads if assets allow

---

## Asset & Setup Checklist

- Branding: logo (1:1), cover (16:9), story/reel templates
- Product media: 5–8 images/style, 2–3 demo videos per SKU
- Copy bank: 10 hooks, 10 CTAs, 10 benefits, 10 objections/answers（全繁體中文）
- Policies: shipping, returns, warranty, support
- Tech: Facebook for WooCommerce installed/connected, Pixel+CAPI verified, Catalog approved
- UTMs: standard templates for posts and ads
- Reviews: email/SMS ask + instructions

---

## Sample Organic Posts（繁體示例）

- 帖子1（品牌介紹）：搭配 1 分鐘直式影片，開場呈現 SVICLOUD 10P Plus 外觀與家庭使用場景，口白文案：「歡迎來到 SVICLOUD TV Box US 官方頁面，輕鬆用繁體中文打造流暢的觀影體驗。」CTA：「立即選購：https://svicloudtvbox.us/」
- 帖子2（產品亮點－10P Plus）：三頁輪播圖，分別展示處理器、雙頻 Wi‑Fi、語音遙控，配文：「為什麼選擇 SVICLOUD 10P Plus？效能強、連線穩、操作直覺。」CTA：「了解更多：https://svicloudtvbox.us/product/svicloud-10p-plus/」
- 帖子3（快速安裝指南）：15 秒短片示範插電、連網、登入三步驟，字幕：「三步完成安裝，5 分鐘開機就緒。」CTA：「查看詳細教學：https://svicloudtvbox.us/support/」
- 帖子4（機型比較）：圖文對比 10P Plus 與 10S，強調適用族群，配文：「10P Plus 適合追求旗艦性能，10S 更適合日常串流需求。你會選哪一台？」CTA：「比較詳情：https://svicloudtvbox.us/product-category/devices/」
- 帖子5（真實評價）：引用客戶繁體好評，附授權照片或評分截圖，配文：「用戶 Alice 回饋：『系統很順，客服回覆超快。』感謝支持！」CTA：「歡迎留言分享你的體驗。」
- 帖子6（限時優惠）：海報放大折扣與截止日期，配文：「本週限時：購買 SVICLOUD 10S 即送語音遙控器，數量有限。」CTA：「立即下單：https://svicloudtvbox.us/product/svicloud-10s/」
- 帖子7（物流與售後 FAQ）：圖文列出運送時間、保固政策、退換流程，配文：「美國訂單安心配送，30 天內提供無憂退換。」CTA：「查看政策：https://svicloudtvbox.us/policies/」
- 帖子8（直播預告）：靜態圖呈現直播時間與主題，配文：「週五晚間 8 點直播教學：如何在 SVICLOUD 上自訂常用頻道。歡迎預先留言提問。」CTA：「點擊設定提醒。」
- 帖子9（售後支援）：短片介紹客服團隊，配文：「有問題嗎？Messenger 與 WhatsApp 均可聯繫，我們提供繁體中文一對一支援。」CTA：「立即私訊獲得協助。」
- 帖子10（剪線小祕訣）：長條圖分享節省有線電視費用的三個步驟，配文：「想降低每月帳單？從網速檢測到內容訂閱，這三步幫你輕鬆轉向 SVICLOUD。」CTA：「更多攻略請見官方部落格。」

---

## Sample Ad Copy（繁體）

- 拓客廣告（影片）： 「用 SVICLOUD 10P Plus 升級你的觀影體驗——安裝快速、介面流暢、客服即時回應。立即於官方商城下單。」
- 重定向廣告（輪播）： 「還在比較嗎？立刻查看真實用戶評價、逐頁了解功能亮點，享受安心退換。」
- 限時優惠廣告： 「限時組合：SVICLOUD 10S + 配件禮包，本週下單享免運。」

---

## 初始十篇帖子發布計畫（繁體）

- 第 1 篇（頁面上線）：官方歡迎帖，強調品牌使命、產品線與售後保障，附上關注與商店連結。
- 第 2 篇（10P Plus 核心賣點）：橫式三圖輪播，展示效能、Wi‑Fi、語音遙控，凸顯旗艦定位。
- 第 3 篇（10S 入門首選）：對比價格與適用情境，強調日常娛樂、長輩也能輕鬆操作。
- 第 4 篇（安裝教學影片）：45 秒教學，字幕語音皆為繁體中文，附使用手冊下載連結。
- 第 5 篇（客戶故事）：圖文分享用戶從有線電視轉向 SVICLOUD 的過程，突出節費與客服支援。
- 第 6 篇（售後政策 FAQ）：圖表列出保固期、退換流程、客服管道，附 Messenger 一鍵聯絡按鈕。
- 第 7 篇（直播互動預告）：公告下週直播主題「如何自訂常用頻道」，鼓勵留言提問並設定提醒。
- 第 8 篇（限時促銷）：倒數海報說明折扣碼與截止日，提醒庫存有限。
- 第 9 篇（UGC 徵集）：邀請用戶分享裝置照片或客廳布置，提供最佳分享者優惠券。
- 第 10 篇（功能更新）：若有韌體／應用更新，以圖文或短影片介紹新功能與升級步驟。

---

## Recommendations Beyond Pages

- Instagram: Cross-post Reels; product tags via shared Catalog; Story Highlights for setup
- Creator Ads: Seed devices to micro-creators; run “Use existing post” ads under your account
- YouTube: 2–4 min setup/tutorials; link back to site; embed in product pages
- Email/SMS: Capture on site; send post-purchase review ask + tips; sync audiences back to Meta
- Google Ads: Brand + Shopping for high-intent searches; align with Meta retargeting
- SEO: Comparison guides (“SVICLOUD 10P Plus vs 10S”), troubleshooting pages
- Support ops: Add Facebook Chat Plugin to site; saved replies; response SLA <24h

---

## Common Pitfalls To Avoid

- Inaccurate/over‑promising claims; implying illegal content access
- Weak product feed (low‑res images, missing fields) hurting DPAs
- Not using CAPI; pixel‑only tracking under‑reports conversions
- Over‑editing during learning phase; let ads exit learning before judging
- Neglecting UTMs; breaks source‑of‑truth analysis

---

## Maintenance

- Review performance weekly; refresh creatives every 3–4 weeks
- Update Catalog imagery/titles quarterly or when SKUs change
- Keep Page messaging templates and FAQs aligned with current policies
