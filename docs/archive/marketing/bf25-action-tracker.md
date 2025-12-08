# Black Friday 2025 Marketing Action Tracker

**Last updated**: November 2025  
**DRI**: Growth Marketing Lead  

| # | Workstream | Task | Owner | Status | Due | Dependencies / Notes |
|---|------------|------|-------|--------|-----|----------------------|
| 1 | Creative Production | Build Facebook asset suite per `facebook-black-friday-2025-creative-brief.md` (videos, carousel, stories, statics). Track checklist completion + upload paths. | Creative Director + Motion team | In progress | Nov 19 | Scripts locked Nov 12; deliver source files under `/assets/facebook/bf25`. |
| 2 | Creative QA & Localization | Produce EN+ZH captions, accessibility review, export UTMs sheet for upload. | Creative Ops | Blocked (await assets) | Nov 19 | Needs final renders from Task 1. |
| 3 | Landing Page Build | Implement `/black-friday` template + modules + hero countdown following `black-friday-landing-page-plan.md`. | Web Engineering | Not started | Nov 16 | Requires new CSS partial `55-black-friday.css`, template updates, Klaviyo form embed. |
| 4 | Promo Codes & Testing | Configure `BF25-20`, `BF25-10`, `BF25-HOLD` coupons, auto-apply query params, regression test cart totals. | Ecommerce Ops | Not started | Nov 17 | Coordinate with Landing Page QA (Task 5). |
| 5 | Landing Page QA | Run QA runbook (Lighthouse, device tests, countdown, translation). | QA Lead | Not started | Nov 18 | Depends on Tasks 3-4. Capture screenshots. |
| 6 | Ads Manager Build | Create campaign/ad sets/ads per `facebook-bf25-ads-structure.md` with UTMs + naming. | Paid Social Manager | Not started | Nov 17 | Requires creative assets + pixel QA (Task 7). |
| 7 | Pixel/CAPI & Audiences | Run pixel diagnostics, confirm Conversion API dedup + refresh audiences (CRM, LAL seeds). | Marketing Ops Engineer | Not started | Nov 15 | Follow `facebook-bf25-ops-checklist.md`. |
| 8 | Automation Rules | Implement Meta automated rules (boost winners, CPA guardrail, etc.) and Slack alerts. | Paid Social Manager | Not started | Nov 18 | Tasks 6 & 7 prerequisites. |
| 9 | Reporting Dashboard | Configure Looker Studio `BF25 Paid Social Performance` pulling Meta/GA4/Klaviyo data. | Growth Analyst | Not started | Nov 19 | See Ops checklist §4. |
|10 | Stakeholder Checkpoint | Daily stand-up (Nov 17–Dec 2) to review tracker + unblock issues; log decisions in Notion. | Growth Lead | Scheduled | Nov 17 kickoff | Use tracker to update statuses. |

> Update this tracker daily; move rows to “Complete” once ACs met. Reference supporting docs in `/docs/marketing/`.
