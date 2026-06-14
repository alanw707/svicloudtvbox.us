# SVICLOUD Agent-Friendly Website Implementation Plan

Date: 2026-06-13
Status: Planning
Scope: Items 1-4 from the agent-friendly website roadmap

## Objective

Make svicloudtvbox.us easier for search engines, AI assistants, and future web agents to understand, cite, and route users through, while prioritizing traffic opportunities already visible in Google Search Console and GA4.

Recent validation showed the strongest organic opportunity is not generic AI discovery. It is the existing support, app, setup, troubleshooting, and Chinese-language search demand. The implementation should improve these high-intent pages first, then expose clean agent-readable resources around the same content.

## Traffic Evidence

Recent GSC signals show the highest-impact pages are:

- `/zh/guides-apps/`
- `/zh/guides-troubleshooting/`
- `/guides-troubleshooting/`
- `/zh/blog/`
- `/guides-apps/`
- `/zh/guides-setup/`

High-signal query themes include:

- `yogurt tv`
- `yogurt tv 下載`
- `yogurt tv 怎麼下載`
- `yogurt tv 不能看 2026`
- `小雲盒子安裝`
- `小雲遙控器沒反應`
- `8989c`
- `8989c.cc`
- `小雲盒子詐騙`
- `小雲盒子11`
- `10P vs 10S`

GA4 also shows a small early AI-assistant referral signal from `chatgpt.com / ai-assistant`, but current traffic is still dominated by Google organic and paid search.

## Implementation Order

1. Build answer hubs from existing high-traffic support pages.
2. Audit and tighten structured data.
3. Add agent-readable Markdown endpoints and `/llms.txt`.
4. Create product decision and upgrade pages that bridge support traffic into purchase intent.

## 1. Build Answer Hubs

### Goal

Turn existing high-traffic support pages into the best answer pages for Google, ChatGPT, Perplexity, and future agents.

### Target Pages

- `/zh/guides-apps/`
- `/zh/guides-troubleshooting/`
- `/guides-troubleshooting/`
- `/zh/guides-setup/`
- `/guides-apps/`
- `/zh/svicloud遙控器配對失敗-故障碼排查一次搞定/`

### Step-by-Step Plan

1. Pull updated GSC data for the last 90 days.
   - Export top queries by page.
   - Export top query/page pairs.
   - Identify pages with high impressions but weak CTR.

2. Group keywords by intent.
   - App download:
     - `yogurt tv 下載`
     - `yogurt tv 怎麼下載`
     - `yogurt tv 安裝`
     - `8989c`
     - `8989c.cc`
   - App failure:
     - `yogurt tv 不能看`
     - `yogurt tv 不能看 2026`
     - `小雲盒子yogurt tv啟動失敗`
   - Device setup:
     - `小雲盒子安裝`
     - `小雲盒子安裝網址`
   - Remote issues:
     - `小雲遙控器沒反應`
     - `遙控器配對失敗`
   - Scam/authenticity:
     - `小雲盒子詐騙`
     - official seller queries
   - Upgrade/buying:
     - `小雲盒子11`
     - `小雲11何時上市`
     - `10P vs 10S`

3. Rewrite each hub page around exact answer blocks.
   - Quick answer at the top.
   - Symptoms.
   - Step-by-step fix.
   - Common causes.
   - When to contact support.
   - Recommended product or upgrade path.
   - FAQ section.
   - Internal links to products, support, compare page, and return policy.

4. Make Traditional Chinese the first-class version.
   - Current traffic is strongest on `/zh/` pages.
   - English and Simplified Chinese should follow, but the Traditional Chinese pages should get the deepest polish first.

5. Add conversion bridges.
   - Add "Need help choosing a replacement or upgrade?" callout.
   - Add "Compare 10P+ vs 10S" links.
   - Add official US support phone and contact link.
   - Add product cards where the intent naturally suggests replacement or upgrade.

6. Improve internal linking.
   - App guide pages should link to troubleshooting, setup, compare, and product pages.
   - Troubleshooting pages should link back to app/setup pages and forward to support/product pages.
   - FAQ should link to high-intent answer hubs.

7. Validate the page experience.
   - Confirm mobile readability.
   - Confirm no sticky UI overlaps content.
   - Confirm support phone is `702-389-3416`.
   - Confirm all CTA URLs work.
   - Confirm pages are indexable and present in sitemap.

### Acceptance Criteria

- Top answer hub pages include clear quick-answer blocks.
- Traditional Chinese hub pages cover the major GSC query intents.
- Each hub page includes at least three relevant internal links.
- Each hub page includes a support/contact path.
- Each hub page includes a product/upgrade bridge where appropriate.
- No stale phone number or unsupported claim remains.

## 2. Structured Data Audit and Cleanup

### Goal

Make Google and AI systems parse SVICLOUD pages cleanly, without duplicate or misleading schema.

### Target Schema Types

- `Organization`
- `Product`
- `Offer`
- `FAQPage`
- `HowTo`
- `BreadcrumbList`

### Step-by-Step Plan

1. Crawl key live pages and extract JSON-LD.
   - Homepage.
   - 10P+ product page.
   - 10S product page.
   - Compare page.
   - FAQ page.
   - App guide pages.
   - Troubleshooting pages.
   - Shipping, returns, and contact pages.

2. Check for duplicate or conflicting schema.
   - Rank Math output vs custom theme output.
   - Duplicate `FAQPage`.
   - Duplicate `Product`.
   - Organization phone mismatch.
   - Product price mismatch.
   - Product availability mismatch.
   - Fake or hidden aggregate ratings.

3. Add or validate page-specific schema.
   - Support/app pages:
     - `FAQPage`
   - Setup/troubleshooting pages:
     - `HowTo` only when the page contains real visible steps.
     - `FAQPage` for visible FAQs.
   - Product pages:
     - `Product`
     - `Offer`
     - `Brand`
   - Site-wide:
     - `Organization`
   - Major navigable pages:
     - `BreadcrumbList`

4. Keep the schema defensible.
   - Do not add fake reviews.
   - Do not add `aggregateRating` unless real visible reviews exist.
   - Do not mark hidden text as FAQ content.
   - Product price and availability must match what the user sees.
   - Phone number must remain `702-389-3416`.

5. Add automated checks.
   - Fetch important pages.
   - Extract JSON-LD.
   - Confirm expected schema types exist.
   - Confirm known bad duplicates do not exist.
   - Confirm phone consistency.
   - Confirm product schema includes price, currency, and availability.

6. Validate externally after deployment.
   - Google Rich Results Test.
   - Search Console enhancement reports.
   - Manual page-source review.

### Acceptance Criteria

- Product pages output valid `Product` and `Offer` schema.
- Support pages output visible-content-safe `FAQPage` schema.
- Troubleshooting pages output `HowTo` only when steps are visible.
- Site-wide `Organization` schema has correct phone/support details.
- No fake review or hidden FAQ markup exists.
- Automated schema checks run in CI or a repeatable local script.

## 3. Agent Markdown and `/llms.txt` Layer

### Goal

Give AI agents clean source material instead of forcing them to scrape noisy WordPress templates.

### Resources to Add

- `/llms.txt`
- `/llms-full.txt`
- `/agent/products.md`
- `/agent/compare-10p-vs-10s.md`
- `/agent/apps.md`
- `/agent/troubleshooting.md`
- `/agent/setup.md`
- `/agent/shipping-returns.md`
- `/agent/contact.md`

### Step-by-Step Plan

1. Add `/llms.txt`.
   - Include site name.
   - Identify SVICLOUD TV Box US as the official US-focused storefront.
   - Link to core product pages.
   - Link to support and setup resources.
   - Link to shipping, returns, warranty, and contact pages.
   - Link to Markdown resources under `/agent/`.

2. Add `/llms-full.txt`.
   - Include a more complete agent briefing.
   - Explain product lineup.
   - Explain 10P+ vs 10S positioning.
   - Include official support phone.
   - Include shipping and return basics.
   - Include troubleshooting index.
   - Include authenticity and scam-avoidance guidance.

3. Add clean Markdown resources.
   - Products:
     - Product names.
     - Model differences.
     - Buy links.
     - Warranty/support notes.
   - Compare:
     - 10P+ vs 10S.
     - Who should buy each model.
     - Upgrade guidance.
   - Apps:
     - Yogurt TV and related app guidance.
     - Official cautions.
     - Troubleshooting links.
   - Troubleshooting:
     - Symptoms.
     - Fixes.
     - Support escalation.
   - Setup:
     - Basic setup steps.
     - Network/app/remote setup.
   - Shipping/returns:
     - Plain-language policy summary.
     - Links to canonical policy pages.
   - Contact:
     - Official phone.
     - Contact page.
     - Support expectations.

4. Prefer generated or reusable content.
   - Avoid hand-maintaining two conflicting versions.
   - Source the Markdown from stable theme translations or a small structured config when possible.

5. Add guardrails.
   - No private customer/order data.
   - No unsupported claims.
   - No scraped competitor text.
   - No old phone number.
   - No promises that conflict with policy pages.

6. Add checks.
   - `/llms.txt` returns HTTP 200.
   - `/llms-full.txt` returns HTTP 200.
   - All `/agent/*.md` resources return HTTP 200.
   - Required product names appear.
   - Required phone number appears.
   - Old phone number does not appear.
   - Markdown resources link to canonical public pages.

### Acceptance Criteria

- `/llms.txt` and `/llms-full.txt` are live and crawlable.
- Agent Markdown endpoints are live and crawlable.
- Markdown content is concise, accurate, and not template-noisy.
- CI or a script checks old-phone regressions and endpoint availability.

## 4. Product Decision and Upgrade Pages

### Goal

Turn support and app traffic into product research and purchase traffic.

### Target Pages

- `/compare/`
- `/svicloud-10p-vs-10s/`
- `/best-svicloud-box-for-chinese-tv-usa/`
- `/yogurt-tv-not-working-upgrade-guide/`
- `/svicloud-box-authenticity-guide/`

### Step-by-Step Plan

1. Map support intent to purchase intent.
   - App not working:
     - Troubleshooting first.
     - Upgrade path second.
   - Remote not working:
     - Pairing fix first.
     - Support/replacement/new box path second.
   - Scam/authenticity:
     - Official store trust page.
     - Product links.
   - Setup confusion:
     - Guided setup.
     - Support CTA.
   - Old model/new model queries:
     - Upgrade comparison.

2. Create a clear 10P+ vs 10S page.
   - Who should buy 10P+.
   - Who should buy 10S.
   - Performance differences.
   - Storage differences.
   - Wi-Fi/spec differences.
   - Warranty/support differences.
   - Best choice for parents or elderly users.
   - Best choice for heavy users.

3. Create a "best box for Chinese TV in USA" page.
   - Target US Chinese-language buyers.
   - Explain official US support.
   - Explain shipping and warranty.
   - Compare models.
   - Link to setup and app guidance.

4. Create a "Yogurt TV not working" upgrade guide.
   - Start with fixes, not a sales pitch.
   - Explain when the issue is app/network/configuration related.
   - Explain when an older box may be the bottleneck.
   - Link to product comparison and support.

5. Create or improve an authenticity guide.
   - Explain official purchase path.
   - Warn about scam/gray-market sellers without making legal overclaims.
   - Include support phone.
   - Link to product pages and warranty policy.

6. Add trust blocks.
   - US support.
   - US shipping.
   - Warranty.
   - Official contact phone.
   - Return policy.
   - Authenticity guidance.

7. Add internal links from high-traffic pages.
   - App guide to compare page.
   - Troubleshooting to product pages.
   - Remote issue page to support and product pages.
   - FAQ to decision pages.
   - Blog pages to relevant guides.

8. Add tracking.
   - GA4 events for comparison CTA clicks.
   - Product page clicks from support pages.
   - Add-to-cart from support pages.
   - Contact clicks from support pages.
   - AI referral sessions from ChatGPT, Perplexity, Claude, and other assistants.

### Acceptance Criteria

- Product decision pages are live, indexable, and internally linked.
- Support pages link naturally to decision pages.
- Decision pages include product CTAs without undermining the support-first intent.
- GA4 tracks decision-page CTA behavior.
- Pages include accurate support, shipping, warranty, and product information.

## Recommended PR Sequence

### PR 1: Flagship Answer Hub and Agent Index

Scope:

- Improve `/zh/guides-apps/`.
- Add `/llms.txt`.
- Add `/llms-full.txt`.
- Add `/agent/apps.md`.
- Add phone/content regression checks.

Reason:

`/zh/guides-apps/` has the strongest search signal and clear app-download intent.

### PR 2: Troubleshooting Hub and Schema

Scope:

- Improve `/zh/guides-troubleshooting/`.
- Improve `/guides-troubleshooting/`.
- Add or validate `FAQPage` and `HowTo`.
- Add `/agent/troubleshooting.md`.

### PR 3: Product Decision Layer

Scope:

- Improve or create 10P+ vs 10S comparison content.
- Add product decision CTA blocks to support pages.
- Add `/agent/compare-10p-vs-10s.md`.

### PR 4: Schema and Monitoring Hardening

Scope:

- Add schema validation script.
- Add endpoint availability checks.
- Add old-phone regression check.
- Add GA4 event tracking for decision CTAs.

## Measurement Plan

Track before and after:

- GSC clicks and impressions for top support pages.
- GSC CTR on high-impression queries.
- Rankings for app/setup/troubleshooting queries.
- Organic landing sessions to support hubs.
- Product clicks from support pages.
- Add-to-cart events from support paths.
- Contact clicks from support paths.
- AI-assistant referrals.
- `/llms.txt` and `/agent/*.md` crawl/access logs if available.

## Non-Goals

- Do not build a public write-enabled MCP server yet.
- Do not expose order/customer tools publicly.
- Do not add unsupported product claims.
- Do not add fake review schema.
- Do not rewrite every page in one large PR.

## Final Recommendation

Ship this in small PRs. Start with `/zh/guides-apps/`, `/llms.txt`, `/llms-full.txt`, and `/agent/apps.md`, because that page has the clearest traffic signal and the lowest risk.
