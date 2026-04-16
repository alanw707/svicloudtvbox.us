# Recent Verified Deliveries Plan — svicloudtvbox.us

**Created:** 2026-04-16  
**Feature branch:** `feat/recent-verified-deliveries-strip`  
**Status:** Historical planning document. The branch later pivoted to the simpler WooCommerce Shipping implementation documented in `docs/bmad/implementation-artifacts/tech-spec-recent-us-shipments-strip.md`.  
**Related docs:**
- `docs/bmad/implementation-artifacts/tech-spec-recent-verified-deliveries-strip.md`
- `docs/bmad/implementation-artifacts/discovery-recent-verified-deliveries-phase-0-2026-04.md`
- `docs/bmad/implementation-artifacts/aftership-production-audit-runbook-2026-04.md`
- `docs/bmad/implementation-artifacts/aftership-rest-audit-runbook-2026-04.md`
- `docs/bmad/implementation-artifacts/aftership-rest-audit-findings-2026-04.md`

---

## 1) What we are building

A slim **Recent Verified Deliveries** trust strip that renders:
- **below the main header**
- **above breadcrumbs / page content**
- as a tasteful horizontal chip row or slow auto-scroll
- with anonymized delivery-time proof from real recent shipments

### Example public items
- `Phoenix, AZ · delivered in 2 days`
- `San Jose, CA · delivered in 1 day`
- `Dallas, TX · delivered in 3 days`

### What this is not
This is **not** a fake urgency ticker and **not** a raw “live last 10 customer orders” feed.

The public UI must never expose:
- names
- order numbers
- tracking numbers
- street addresses
- ZIP codes
- exact delivered timestamps

---

## 2) Why this is a bigger feature

This looks like a simple frontend strip, but it actually depends on four systems working together:

1. **Real shipment data**
   - we need a trustworthy shipment start timestamp and delivered timestamp
2. **Normalization**
   - carrier/provider data must be reduced into a safe, consistent internal format
3. **Caching + privacy filtering**
   - the site must build a sanitized feed ahead of render time
4. **Theme rendering**
   - the strip must fit the header, translation, CSS bundle, and page-hiding rules

So this should be treated as a **phased delivery feature**, not a quick UI patch.

---

## 3) Verified current state

### Confirmed from local Phase 0
The local/dev environment is **not sufficient** to power this feature yet.

Key findings from `discovery-recent-verified-deliveries-phase-0-2026-04.md`:
- no usable delivered timestamps in local WooCommerce data
- no usable tracking/provider order meta in local data
- WooCommerce native fulfillments are present in code but disabled locally
- local orders are test-heavy / stale and not suitable for real delivery analytics

### New business context
Store owner clarified that **AfterShip is installed on the live website**.

That changes the likely integration path:
- we should now treat **AfterShip as the primary candidate source of truth**
- the local Phase 0 findings still stand, because AfterShip was not present in the local environment we audited

### Repo constraint that still matters
Deploy/sync workflow is **theme-oriented**, so the safest first implementation shape remains:
- data normalization + feed-building logic inside the theme for Phase 1
- rendering inside the theme
- no new separate deployment pipeline unless truly necessary

---

## 4) Working product decision

### Recommended product version
Build a **Recent Verified Deliveries** strip, not a raw customer-order marquee.

### Placement
Render it:
- directly **below the header**
- above breadcrumbs/content

### Tone
The strip should feel like:
- shipping proof
- fulfillment confidence
- low-pressure trust

It should **not** feel like:
- casino ticker UI
- fake scarcity
- aggressive urgency software

### Motion rules
- slow, subtle horizontal movement on larger screens
- pause on hover/focus
- `prefers-reduced-motion` = static wrapped chips
- not sticky

### Hide on
- cart
- checkout
- account
- order tracking
- any other transactional pages where distraction hurts completion

---

## 5) Working technical decision

## Source of truth strategy: **AfterShip first**

Use **AfterShip** as the first-choice shipment data source, because it is more likely than WooCommerce status alone to provide:
- delivered status
- delivered timestamps
- carrier-normalized tracking data
- multi-carrier support beyond USPS-only flows

### Do not use as source of truth
- WooCommerce order status by itself
- public USPS tracking page scraping
- frontend API calls during page render

### Theme responsibility
The theme should own:
- feature flagging
- order-level normalized meta storage
- public feed eligibility rules
- cached sanitized feed generation
- below-header rendering

### Provider responsibility
AfterShip should ideally provide:
- shipment lifecycle state
- shipment start / first accepted / first in-transit timestamp
- delivered timestamp
- carrier/provider identity

---

## 6) Proposed architecture

### Layer A — Provider data
Preferred source:
- AfterShip plugin local data, tables, order meta, or plugin APIs/hooks

Fallback source:
- AfterShip API/webhook sync if plugin does not store enough machine-readable data locally

Last-resort fallback:
- other existing provider data if AfterShip is not actually the operational source of truth for live orders

### Layer B — Normalized private order data
Store only the minimum normalized fields needed for the trust strip, for example:
- `_svic_delivery_started_at_gmt`
- `_svic_delivery_delivered_at_gmt`
- `_svic_delivery_transit_seconds`
- `_svic_delivery_provider`
- `_svic_delivery_public_city`
- `_svic_delivery_public_state`
- `_svic_delivery_feed_eligible`
- `_svic_delivery_sync_status`

### Layer C — Sanitized public feed cache
Build a pre-sanitized feed in a transient or option, e.g.:
- `svic_verified_deliveries_feed_v1`

Each public item should contain only safe display fields such as:
- `city`
- `state`
- `time_label`
- optional `carrier`

### Layer D — Theme render
Render the cached feed below the header using server-side PHP.

No live provider lookup during request rendering.

---

## 7) Execution plan

## Phase A — AfterShip production audit
**Goal:** prove that real shipment data exists and is accessible enough to power the feature.

### Questions to answer
1. What exact AfterShip plugin is installed?
2. Does it store shipment data in:
   - order meta,
   - custom DB tables,
   - only remote API views,
   - or a mix?
3. For a delivered order, can we get:
   - shipment start timestamp,
   - delivered timestamp,
   - carrier,
   - city/state?
4. Are multiple tracking numbers per order common?
5. Are there at least 10 recent delivered orders with usable data?

### Deliverable
A short audit note answering:
- **go** / **hold** / **fallback**

---

## Phase B — Data normalization design
**Goal:** define exactly how shipment data becomes theme-safe internal data.

### Decide
- which timestamp represents “start”
- whether public location is `City, State` or `State only`
- what makes an order feed-eligible
- how to handle multi-shipment orders
- whether webhook sync, cron sync, or both are needed

### Deliverable
A final normalized data contract and sync flow.

---

## Phase C — Backend implementation
**Goal:** build the data pipeline inside the theme.

### Planned work
- add `theme/svicloudtvbox-lumen/inc/class-svic-verified-deliveries.php`
- bootstrap from `theme/svicloudtvbox-lumen/functions.php`
- read/normalize AfterShip-backed delivery data
- store minimal normalized meta
- rebuild the sanitized cached feed
- add a feature flag so rollout stays controlled

### Deliverable
A server-side method that returns up to 10 safe verified-delivery items.

---

## Phase D — Frontend implementation
**Goal:** render the below-header strip cleanly in the theme.

### Planned work
- render from `theme/svicloudtvbox-lumen/header.php`
- add CSS partial:
  - `theme/svicloudtvbox-lumen/assets/css/parts/06-verified-deliveries-strip.css`
- register it in:
  - `theme/svicloudtvbox-lumen/assets/css/bundles.json`
- add copy/translations in:
  - `theme/svicloudtvbox-lumen/lang/en_US.php`
  - `theme/svicloudtvbox-lumen/lang/zh_TW.php`
  - `theme/svicloudtvbox-lumen/lang/zh_CN.php`
- only touch `theme.js` if CSS-first motion is not enough

### Deliverable
A polished trust strip with reduced-motion support and safe hiding rules.

---

## Phase E — QA and rollout
**Goal:** make sure the feature is true, safe, and non-disruptive.

### Verify
- no live provider/API calls on page render
- no customer-identifiable info leaks
- mobile header spacing remains good
- strip hides on cart/checkout/account/order-tracking
- translations fit in EN / zh_TW / zh_CN
- feed gracefully disappears if not enough safe data exists

### Deliverable
Production-ready rollout behind a feature flag.

---

## 8) Decision gates

## Green light
Proceed if AfterShip audit shows that we can reliably obtain:
- delivered status
- delivered timestamp
- a valid start timestamp or acceptable shipment-start proxy
- enough recent delivered orders for a 10-item feed

## Yellow light
Proceed carefully if:
- delivered data exists but only through API/webhook sync
- city/state quality is poor and we must use `State only`
- multi-shipment orders need a simplifying rule

## Red light
Do **not** implement the real strip yet if:
- AfterShip cannot provide a dependable delivered timestamp
- the data is not accessible in a machine-readable way
- recent delivered volume is too low or too stale
- the only fallback is scraping carrier pages or faking timing with order statuses

---

## 9) Open decisions to resolve before coding

1. **Public location format**
   - `City, State`
   - or `State only`

2. **Multi-shipment rule**
   - first delivered shipment?
   - last delivered shipment?
   - first complete order-level delivery?

3. **Start timestamp definition**
   - label created
   - first carrier acceptance
   - first in-transit scan

4. **Cache refresh strategy**
   - webhook-driven
   - cron-assisted
   - both

5. **Pages to hide**
   - baseline: cart, checkout, account, order tracking
   - confirm if blog/guides should show it

6. **Carrier display**
   - no carrier shown
   - or optional tiny badge if it improves trust

---

## 10) File map for the theme phase

| File | Planned change |
|---|---|
| `theme/svicloudtvbox-lumen/functions.php` | feature flag, include/bootstrap class |
| `theme/svicloudtvbox-lumen/inc/class-svic-verified-deliveries.php` | normalization, cache, eligibility, render helpers |
| `theme/svicloudtvbox-lumen/header.php` | render strip below header |
| `theme/svicloudtvbox-lumen/assets/css/parts/06-verified-deliveries-strip.css` | new strip styles |
| `theme/svicloudtvbox-lumen/assets/css/bundles.json` | add CSS partial to `style` bundle |
| `theme/svicloudtvbox-lumen/lang/en_US.php` | translation keys |
| `theme/svicloudtvbox-lumen/lang/zh_TW.php` | translation keys |
| `theme/svicloudtvbox-lumen/lang/zh_CN.php` | translation keys |
| `theme/svicloudtvbox-lumen/assets/js/theme.js` | optional enhancement only |

---

## 11) Risks and failure modes

### Data truth risk
If timestamps are weak or ambiguous, the strip becomes misleading.

### Privacy risk
If location quality is poor, city-level display could reveal too much.

### Freshness risk
If delivered volume is low, the strip may look stale or repetitive.

### Technical coupling risk
If AfterShip data only exists remotely and not locally, sync plumbing may be more work than expected.

### UX risk
If motion or density is overdone, the strip could cheapen the brand instead of increasing trust.

---

## 12) Fallback plan if audit fails

If AfterShip cannot support a real verified-deliveries feed, do **not** ship a fake version.

Instead ship a simpler static trust strip, such as:
- `Ships from our Nevada warehouse`
- `Tracking emailed after fulfillment`
- `Most U.S. orders arrive in 2–4 business days`

That preserves trust without inventing shipment precision.

---

## 13) Recommended next step

Resume with **Phase A: AfterShip production audit**.

Minimum audit sample:
- 10–20 recent delivered orders
- carrier/provider observed
- start timestamp source
- delivered timestamp source
- public location availability
- whether data lives in WP locally or only in AfterShip/API responses

If Phase A passes, we can confidently move into implementation on this branch.
