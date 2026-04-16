# Phase 0 Discovery — Recent Verified Deliveries Strip

**Feature branch:** `feat/recent-verified-deliveries-strip`
**Date:** 2026-04-16
**Scope:** audit the current repo + local Docker WordPress environment to determine whether a recent-deliveries strip can be powered by real shipment/delivery data.

**Addendum:** after this local audit, the store owner clarified that AfterShip is installed on the live website. That does not change the findings in this document, but it does narrow the next production audit target to AfterShip first.

---

## Executive Summary

### Current verdict
**Not implementation-ready yet.**

The current local/dev environment does **not** contain reliable shipment-tracking or delivered-status data that can power a real “recent verified deliveries” strip.

### Why
- No active shipping/tracking plugin was found.
- No order meta for tracking numbers, tracking URLs, shipment providers, or delivered timestamps was found.
- WooCommerce’s native **fulfillments** feature exists in code but is **disabled** in the current environment.
- The local order dataset is mostly **test data**, not trustworthy production-like fulfillment data.
- Completed orders in the local DB do not include usable shipping location data for public city/state chips.

### Recommendation
Do **not** start Phase 1 implementation yet.

First run a **production data audit** to identify the real shipping/tracking source of truth. Once that is known, we can decide between:
1. using an existing provider/webhook,
2. enabling/using WooCommerce native fulfillments, or
3. building a custom normalized delivery meta layer.

---

## Questions We Needed to Answer

1. What system is the source of truth for tracking?
2. Is delivered status already stored anywhere?
3. What carrier mix is actually in use?
4. What timestamp can represent “shipment started”?
5. Is there enough recent real delivered-order volume to keep the strip fresh?

---

## Discovery Method

Audited:
- theme code in `theme/svicloudtvbox-lumen/`
- local site content in `sites/svicloud10p/wp-content/`
- local Docker WordPress environment (`svicloud10p-wp`)
- WooCommerce HPOS order tables via WordPress bootstrap / PHP queries
- planning docs that mention shipping/tracking integrations

---

## Findings

## 1) Tracking source of truth is **not identifiable** from the current local environment

### Observed active plugins
Active plugins in the local site:
- WooCommerce
- Stripe payment plugin
- WP Mail SMTP
- WPForms Lite
- Rank Math

### What is missing
No active plugin matching any obvious shipping/tracking provider was found, such as:
- Shippo
- Pirate Ship
- AfterShip
- ShipStation
- a dedicated shipment-tracking plugin

### Interpretation
The repo/planning docs mention Pirate Ship / Shippo and tracking emails, but the local running site does not expose an installed/active shipping integration that stores normalized shipment data.

### Answer
**Unknown.** The real tracking source of truth is not visible from the current local/dev environment.

---

## 2) Delivered status is **not stored** in the current local order data

### Checked order meta keys
Searched HPOS order meta and legacy post meta for:
- `_tracking_number`
- `_tracking_url`
- `_shipment_provider`
- `_svic_delivery_started_at_gmt`
- `_svic_delivery_delivered_at_gmt`
- `_svic_delivery_transit_seconds`

### Result
All of the above returned **0 rows**.

### Checked notes/content
- only **1** order note matched `tracking`
- that note was a manual-looking test note: `tracking 12345`
- no useful `delivered` notes were found

### Answer
**No.** There is no usable delivered timestamp or delivered status currently stored in local order data.

---

## 3) Carrier mix cannot be validated from local order data

### What the docs say
Planning docs mention:
- `Pirate Ship or Shippo (USPS/UPS)`
- marketing/site copy also mentions `USPS / UPS / FedEx`

### What the data says
Local order data contains:
- no `_shipment_provider`
- no tracking URLs
- no carrier-specific metadata

### Answer
**Unknown in practice.** The repo docs suggest USPS/UPS and some marketing copy includes FedEx, but the local data does not prove actual carrier usage.

---

## 4) No reliable “shipment started” timestamp exists today

### Available timestamps on orders
The local DB has order timestamps such as:
- `date_created_gmt`
- `date_updated_gmt`
- status transitions through order notes

### Problem
These timestamps are **not shipment timestamps**.
They represent checkout/order/payment/admin status changes, not carrier acceptance or shipment handoff.

### Important implication
If we built the feature today using available local data, we would be forced to use a bad proxy like:
- order created → completed
or
- order created → order updated

That would **not** support a truthful claim like “from warehouse to customer’s home”.

### Answer
**No reliable shipment-start timestamp exists yet.** It must be captured explicitly from a real fulfillment/tracking system.

---

## 5) Local dataset is not suitable for deciding whether the strip would stay fresh

### Local order status totals
Observed in local HPOS orders:
- `wc-processing`: 50
- `wc-completed`: 20
- `wc-checkout-draft`: 15

### Freshness checks
- `completed_last_90d`: **0**
- latest completed orders in local DB were from **2025-10-03**
- recent 2026 activity is mostly processing/draft test traffic

### Quality checks
- `stripe_test_orders`: **69**
- recent completed/processing orders are predominantly **test-mode Stripe** orders
- completed orders with usable shipping city+state: **0**

### Answer
**Unknown for production.** The local/dev dataset is too test-heavy and stale to evaluate real production order volume freshness.

---

## Critical Technical Finding

## WooCommerce native fulfillments are present in code but **disabled**

### Observed
- WooCommerce contains native fulfillments classes and REST controllers
- `FulfillmentsController` class exists
- but feature flag check returned: `fulfillments_feature=no`
- `wp_wc_order_fulfillments` table is **missing**
- `woocommerce_fulfillments_db_tables_created` is unset/null

### Why this matters
The theme already includes logic tied to fulfillment hooks such as:
- `woocommerce_fulfillment_before_create`
- `woocommerce_fulfillment_before_update`

But with fulfillments disabled, those hooks are **not currently enough** to power the deliveries strip in this environment.

### Impact
The initial Phase 1 plan should **not assume** native WooCommerce fulfillments are available unless we explicitly choose to enable and adopt that feature.

---

## Evidence Summary

| Check | Result | Implication |
|------|--------|-------------|
| Active tracking/shipping plugin | None found | Source of truth unresolved |
| `_tracking_number` / `_tracking_url` / `_shipment_provider` | 0 rows | No shipment metadata available |
| Delivered timestamp meta | 0 rows | Cannot compute real transit time |
| WooCommerce fulfillments feature | Disabled | Native fulfillment hooks are dormant |
| `wp_wc_order_fulfillments` table | Missing | No native fulfillment records stored |
| Completed orders in last 90 days | 0 | Local data not fresh enough |
| Stripe test-mode order meta | 69 orders | Local order set is test-heavy |
| Completed orders with shipping city/state | 0 | Cannot safely render city/state chips from local data |

---

## Answers to the Original Phase 0 Questions

| Question | Answer | Confidence |
|---------|--------|------------|
| 1. Source of truth for tracking? | Unknown in current local/dev environment | High |
| 2. Is delivered status already stored? | No | High |
| 3. Actual carrier mix? | Unknown from local data | Medium |
| 4. Shipment-start timestamp available? | No | High |
| 5. Enough recent delivered volume? | Unknown for production; local data says no | Medium |

---

## Final Phase 0 Decision

## Decision: **Hold Phase 1 until production audit is completed**

Based on the available local environment, the feature is **conceptually feasible** but **not data-ready**.

### Why we should hold
Implementing now would force us to use unreliable proxies and test data, which would create a misleading trust strip.

### What must happen next
Run a **production-side audit** to answer:
1. Which service currently creates shipping labels/tracking?
2. Where are tracking numbers stored in production?
3. Does production already store delivered timestamps or webhooks?
4. Are carriers mostly USPS, or mixed USPS/UPS/FedEx?
5. Do at least 10 recent delivered orders have usable location + timing data?

---

## Recommended Next Step (Phase 0.5)

Perform a short production audit with either:
- WordPress admin access,
- WP-CLI on production/staging,
- database access,
- or screenshots/export from the live fulfillment tool.

### Minimum production sample needed
Audit **10–20 recent delivered orders** and capture for each:
- order ID
- carrier/provider
- tracking number presence (not the actual number in docs)
- shipment start timestamp source
- delivered timestamp source
- shipping city/state availability
- whether the data is machine-readable or only visible in an external UI

---

## If Production Audit Fails

If production also lacks delivered timestamps, then the recommended fallback is:
- **do not build** the real deliveries strip yet
- instead build a simpler static trust strip, e.g.:
  - `Ships from Nevada warehouse`
  - `Recent orders typically arrive in 2–4 days`
  - `Tracking emailed automatically`

That would be safer than publishing pseudo-real delivery data.

---

## Recommendation Summary

### Safe conclusion right now
- **Feature concept:** good
- **Current local data readiness:** no
- **Implementation readiness:** no
- **Next move:** production audit required before coding

### Most important blocker
There is currently **no reliable delivered shipment dataset** in the local/dev environment.
