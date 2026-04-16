# AfterShip REST Audit Findings — 2026-04

**Date:** 2026-04-16  
**Branch:** `feat/recent-verified-deliveries-strip`  
**Audit path:** WordPress REST + Application Password using existing `.env` credentials (`WP_REST_ENDPOINT`, `WP_REST_USERNAME`, `WP_REST_PASSWORD`)  
**Method:** `scripts/aftership_rest_audit.py` plus targeted follow-up REST inspection  
**Scope:** determine whether the live site's existing AfterShip/WooCommerce shipment data is sufficient to power the Recent Verified Deliveries strip.

Related files:
- `scripts/aftership_rest_audit.py`
- `docs/bmad/implementation-artifacts/aftership-rest-audit-runbook-2026-04.md`
- `docs/bmad/implementation-artifacts/recent-verified-deliveries-plan-2026-04.md`
- `docs/bmad/implementation-artifacts/tech-spec-recent-verified-deliveries-strip.md`

---

## Executive Summary

### Result
**Partial green light, but not fully implementation-ready yet.**

The live site **does** have AfterShip installed and connected, and recent live WooCommerce orders clearly contain real shipment/tracking data.

However, the current REST-exposed order data does **not** expose a reliable **delivered timestamp** or **delivered event state**.

### Practical meaning
We now know this feature is much more real than the local Phase 0 environment suggested, because the live site has:
- a real AfterShip plugin
- a real WooCommerce Shipping workflow
- real USPS shipment/tracking metadata on recent orders
- recent orders with usable shipping city/state presence

But we still **cannot truthfully compute “delivered in X days”** from the currently accessible REST data alone.

---

## What was confirmed

## 1) Existing credentials for live REST access were already available

Repo-local `.env` already contained:
- `WP_REST_ENDPOINT`
- `WP_REST_USERNAME`
- `WP_REST_PASSWORD`

Those credentials were sufficient to authenticate to the live site's REST API and inspect WooCommerce data.

---

## 2) AfterShip is definitely active on the live site

Confirmed active plugin:
- **AfterShip Tracking - All-In-One WooCommerce Order Tracking (Free plan available)**
- Version: **1.18.1**

Also active and relevant:
- **WooCommerce Shipping** `2.2.8`

### Interpretation
The live shipping flow is not hypothetical. The site is actively using a real tracking/shipment stack.

---

## 3) Live REST namespaces confirm both AfterShip and WooCommerce Shipping integrations

Observed namespaces/routes include:
- `wc/aftership/v1`
- `wcshipping/v1`

### Important limitation
`wc/aftership/v1` only exposed:
- `/wc/aftership/v1`
- `/wc/aftership/v1/settings`

It did **not** expose a public/order-level tracking-events REST route that obviously returns delivered checkpoints or timestamps.

---

## 4) AfterShip appears connected and in active use

From the AfterShip settings REST response:
- `connected: true`
- `enable_import_tracking: 1`
- `use_track_button: true`

### Interpretation
This is not just an installed-but-idle plugin. The plugin appears connected and participating in the live tracking workflow.

---

## 5) Recent completed orders contain real shipment metadata

Recent completed orders exposed shipment-related meta such as:
- `_wcshipping-shipments`
- `wcshipping_labels`
- `_wcshipping_selected_rates`
- `_wcshipping_shipment_dates`
- `_wc_shipment_tracking_items`
- `_aftership_tracking_items`
- `_aftership_tracking_number`
- `_aftership_tracking_provider_name`

### Counts from the sampled REST audit
From the first 12 recent completed orders:
- `wcshipping` shipment/label meta appeared on **all 12**
- AfterShip meta appeared on **7 of 12**

### Interpretation
WooCommerce Shipping data is consistently present.
AfterShip data is present on many recent orders, though not necessarily all sampled orders.

---

## 6) Carrier mix in recent live orders looks strongly USPS-based

Across sampled recent completed orders:
- carrier/provider was consistently **USPS**
- service name appeared as **USPS - Ground Advantage**

### Interpretation
This is useful because it means:
- carrier mix is at least currently USPS-dominant in sampled live data
- the trust strip would likely not need complex multi-carrier UI wording at launch

---

## 7) Usable shipment-start-ish timestamps exist

Recent orders exposed values that can plausibly represent shipment start or label purchase timing:
- `_aftership_tracking_items[].additional_fields.ship_date`
- `wcshipping_labels[].created_date`
- `_aftership_tracking_items[].metrics.created_at`

### Best currently visible candidate
The strongest currently visible shipment-start proxy is:
- `additional_fields.ship_date` from `_aftership_tracking_items`

### Important caveat
This is **not yet the delivered timestamp**.
It is only evidence that shipment creation/start timing exists.

---

## 8) Recent completed orders do have usable public location data

In sampled recent completed orders:
- shipping city was often present
- shipping state was present
- billing city/state was also usually present

### Interpretation
If the feature becomes data-ready later, public display of:
- `City, State`
or
- `State only`
looks feasible from the live dataset.

This is a major improvement over the local/dev dataset.

---

## What is still missing

## 9) No reliable delivered timestamp was found in REST-exposed order data

We did **not** find a machine-readable delivered timestamp in the inspected order metadata.

Specifically, sampled shipment/tracking structures exposed:
- provider/carrier
- tracking presence
- ship date
- label purchase timing

But **not**:
- delivered timestamp
- delivered checkpoint timestamp
- out-for-delivery timestamp
- in-transit progression timeline

---

## 10) WooCommerce Shipping REST did not give delivered status either

The `wcshipping` label-status inspection returned label data like:
- carrier
- service name
- purchased label info
- label status = `PURCHASED`

It did **not** provide a delivered lifecycle state.

### Interpretation
WooCommerce Shipping appears to be a label/source-of-shipment layer, not the proof-of-delivery source we need.

---

## 11) AfterShip REST exposure appears too thin for this feature by itself

The existing AfterShip REST namespace exposed settings, but not a useful tracking-events endpoint for recent orders.

### Interpretation
AfterShip is present and connected, but the current REST-accessible data is not enough by itself to render:
- `Phoenix, AZ · delivered in 2 days`

with confidence.

---

## 12) Order notes did not surface meaningful delivery events

Shipment-related order notes were not a useful source of truth in the sampled data.

### Interpretation
We should not expect to derive delivered timing from notes.

---

## Key decision

## Decision: **Do not implement the real “delivered in X days” strip yet from current REST data alone**

### Why
We still do not have a dependable delivered timestamp exposed through the live site's current REST-accessible data model.

### What we *can* now say confidently
- AfterShip is installed and connected
- WooCommerce Shipping is active
- recent live orders have real USPS shipment metadata
- recent live orders have usable public location fields
- shipment start/ship-date proxies exist
- delivered proof is the remaining blocker

---

## Best next step

## Phase A.1 — obtain delivered-event access

We now need one of these to move forward:

### Option 1 — Preferred
Expose a **temporary authenticated custom REST endpoint** that, server-side, can inspect:
- deeper AfterShip plugin data
- plugin internals/classes if available
- order meta not surfaced through normal REST
- any saved AfterShip checkpoint/status payloads

### Option 2
Inspect the wp-admin AfterShip UI for one or more delivered orders and determine whether:
- delivered timestamps exist visibly in admin
- those timestamps are only remote/UI data versus locally stored in WordPress

### Option 3
Use direct AfterShip API/webhook integration if the site/account already has access and the plugin connection can be leveraged.

---

## What this means for the feature plan

### Good news
The feature is now much closer to feasible than it looked from local Phase 0.

### Remaining blocker
The blocker is no longer “is AfterShip installed?” or “is there real shipping data?”

The blocker is now much narrower:
- **Where do we get a reliable delivered timestamp/event from?**

---

## Current recommendation summary

### Confirmed yes
- live AfterShip plugin exists
- live WooCommerce Shipping data exists
- USPS shipment metadata exists on recent orders
- shipping city/state exists often enough for a public feed

### Confirmed no
- no reliable delivered timestamp found in currently accessible REST order data
- no obvious AfterShip REST tracking-events endpoint exposed
- no good delivered-event source in order notes

### Recommendation
Proceed to the **next audit layer**, not implementation:
- temporary authenticated custom REST endpoint
- or manual wp-admin AfterShip timeline audit

Do **not** yet implement the final “Recent Verified Deliveries” strip from the current REST data alone.
