# Facebook BF25 Ops Checklist (Automation, Tracking, Reporting)

**Updated**: November 2025  
**Owner**: Paid Media Ops + Analytics  

---

## 1. Automation Rules (Meta Ads Manager)

| Rule Name | Scope | Condition | Action | Notes |
|-----------|-------|-----------|--------|-------|
| `BF25 – Boost Winners` | Campaign | ROAS (Purchase) > 5 **and** Spend > $200 in last 12h | Increase budget +20%, max 2 times in 24h | Helps scale high-performers quickly |
| `BF25 – CPA Guardrail` | Ad Set | CPA (Purchase) > $45 for 12h **and** Spend > $150 | Send Slack alert + pause ad set | Manual review required before re-enabling |
| `BF25 – Frequency Cap` | Ad | Frequency > 6 **and** CTR < 0.8% over 3 days | Pause ad | Prevent creative fatigue in retargeting pools |
| `BF25 – Budget Shift Final 48h` | Ad Sets AS4/AS5 | Time window: Nov 30–Dec 2 | Increase budget +50% at 00:01 PT Nov 30 | Emphasize last-chance retargeting |
| `BF25 – Emergency Stop` | Campaign | ROAS < 2 for 6h **and** spend > $500 | Pause campaign, notify ops | Safety kill switch |

All rules should push alerts to #ads-bf25 Slack via Meta notifications integration.

---

## 2. Pixel & Conversion API Refresh

1. **Verify Pixel Health**
   - Use Events Manager Diagnostics to ensure no `Purchase` dedup errors.
   - Confirm pixel ID `SVICloudMain` connected to Meta Business Manager.
2. **Conversion API**
   - Ensure CAPI via WooCommerce plugin is active; test server events (Purchase) using Test Events tool.
   - Deduplication: match `event_id` between browser + server.
3. **Event Prioritization**
   - Configure Aggregated Event Measurement with `Purchase` at highest priority, followed by `AddToCart`, `ViewContent`.
4. **QA Flow**
   - In staging + prod, run through `/black-friday` CTA → product page → checkout with coupon. Verify Pixel Helper shows events with correct value/currency.
5. **Offline/CRM Sync**
   - Update offline event set mapping (purchasers uploaded from Shopify/Woo) daily to feed retargeting lookbacks.

---

## 3. Audience Refresh Tasks

| Audience | Frequency | Source | Notes |
|----------|-----------|--------|-------|
| `BF25 – CRM Purchasers` | Daily | WooCommerce export auto-synced via Zapier/Make | Used for exclusion + loyalty ad set |
| `BF25 – High LTV Seed` | Weekly | SQL export from analytics warehouse | Feed into Lookalike 1%/2% |
| `BF25 – Lead Magnet` | Daily | Klaviyo list `BF25-WarmList` | Add to retargeting + email reminder flows |
| `BF25 – Site Engagers 90d` | Auto | Pixel (ViewContent) | Confirm 90d retention toggled on |
| `BF25 – Video Viewers 75%` | Auto | Pixel, update with latest creatives | Use to warm new sequences |

Document audience IDs in shared sheet `bf25-paid-social-tracking.xlsx`.

---

## 4. Reporting Template Outline

**Platform**: Looker Studio dashboard `BF25 Paid Social Performance`

Sections:
1. **Executive Summary**: ROAS, Spend, Revenue, CPA, CTR (with goal lines).
2. **Daily Trend**: stacked area chart of spend vs revenue.
3. **Audience Breakdown**: table by ad set (ROAS, CPA, CVR, Frequency).
4. **Creative Leaderboard**: top creatives by ROAS & thumb-stop rate with thumbnails.
5. **Funnel Metrics**: sessions, add-to-carts, purchases (GA4 data filtered for `utm_campaign=bf25-fb-20off`).
6. **Email Lead Contribution**: number of Klaviyo submissions tied to FB UTMs.

**Data Sources**:
- Meta Ads (connector)
- GA4 (filtered by `page_location` contains `/black-friday`)
- Klaviyo CSV (manual upload daily) or API.

**Update Cadence**: 
- Auto-refresh every 4h during Nov 21–Dec 2.
- Manual commentary added daily 10am PT in dashboard notes.

---

## 5. Ops Runbook
1. **Pre-launch (Nov 17)**
   - Test Events Manager, confirm dedup, review automation rules.
   - Validate Looker dashboard data sources.
2. **Daily During Campaign**
   - 9am PT: Check alerts, annotate dashboards, adjust budgets, log decisions in Notion doc.
   - 3pm PT: Review KPIs vs targets, trigger manual optimizations if needed.
3. **Post-campaign**
   - Export Meta data CSV, GA4 conversions, Klaviyo lead counts.
   - Prepare post-mortem deck with learnings per audience + creative.

---

## 6. Owners
- Automation Rules: Paid Social Manager (Alex)
- Pixel/CAPI + Audience Sync: Marketing Ops Engineer (Jamie)
- Reporting Dashboard: Growth Analyst (Priya)
- Daily QA & Alerts: On-call rotation (see Ops sheet).

