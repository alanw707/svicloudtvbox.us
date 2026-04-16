# Recent Verified Deliveries Strip — Feasibility + Implementation Plan

**Feature branch:** `feat/recent-verified-deliveries-strip`

**Status note:** this document captures the original delivered-times concept. The branch implementation has since pivoted to the simpler WooCommerce Shipping version documented in `docs/bmad/implementation-artifacts/tech-spec-recent-us-shipments-strip.md`.

**Phase 0 findings:** see `docs/bmad/implementation-artifacts/discovery-recent-verified-deliveries-phase-0-2026-04.md`
**Execution plan:** see `docs/bmad/implementation-artifacts/recent-verified-deliveries-plan-2026-04.md`
**AfterShip audit runbook:** see `docs/bmad/implementation-artifacts/aftership-production-audit-runbook-2026-04.md`
**AfterShip REST audit runbook:** see `docs/bmad/implementation-artifacts/aftership-rest-audit-runbook-2026-04.md`
**AfterShip REST audit findings:** see `docs/bmad/implementation-artifacts/aftership-rest-audit-findings-2026-04.md`
**Live-site note:** store owner confirmed AfterShip is installed on the live website; treat AfterShip as the primary audit target until production data is verified.

## Recommendation

**Recommended:** build a **Recent Verified Deliveries** strip below the main header.

**Not recommended:** build a literal "last 10 real customer orders" marquee with exact timestamps.

### Why
- The trust signal is strong: recent real delivery speed can reinforce the Nevada/U.S. fulfillment message.
- A raw order ticker can look scammy, expose too much about real customers, and feel like fake urgency software.
- The cleaner version is a branded trust strip that shows **anonymized, verified, rounded transit times**.

---

## Final Product Direction

Render a slim strip **below the sticky header** and **above breadcrumbs/content**.

Example items:
- `Phoenix, AZ · delivered in 2 days`
- `San Jose, CA · delivered in 1 day`
- `Dallas, TX · delivered in 3 days`

With a small disclaimer such as:
- `Based on recent delivered U.S. orders. Times vary by carrier, destination, weekends, and holidays.`

### UX rules
- Not sticky
- Not a traditional HTML marquee
- Slow, tasteful horizontal chip scroll on desktop
- Pause on hover/focus
- `prefers-reduced-motion` => static wrapped chips, no motion
- Hide on checkout/cart/account/order-tracking views to avoid distraction

---

## Go / No-Go Criteria

### Green light
Proceed if at least one of these is true:
1. The live fulfillment/tracking system already stores a delivered timestamp or delivered status in order/fulfillment meta.
2. The live shipping/tracking provider offers API/webhook access for delivered events.
3. USPS is the dominant carrier and direct USPS API access is available.

### Yellow light
Proceed carefully if:
- Carrier mix is USPS/UPS/FedEx and no normalized provider exists.
- The only shipment metadata currently stored is tracking number + provider.
- Delivery confirmation must be polled rather than pushed.

### Red light
Do **not** build this feature yet if:
- There is no reliable way to know when an order was delivered.
- The only source is scraping the public USPS tracking page.
- Order volume is too low to keep the strip fresh.
- Legal/privacy concerns outweigh the conversion upside.

---

## Repo-Specific Constraints

## 1) Deployment workflow is theme-only
Current local sync + deploy scripts only handle the theme:
- `scripts/sync_theme_container.sh`
- `scripts/deploy-theme.sh`

### Implication
For **Phase 1**, keep the implementation **inside the theme**, not a separate plugin, so it fits the existing deploy workflow.

### Preferred code organization
Add a new include file instead of bloating `functions.php` further:
- `theme/svicloudtvbox-lumen/inc/class-svic-verified-deliveries.php`

Then bootstrap it from:
- `theme/svicloudtvbox-lumen/functions.php`

---

## 2) Existing code already shows tracking integration signals
Current theme code already references fulfillment metadata:
- `_tracking_number`
- `_tracking_url`
- `_shipment_provider`
- AfterShip/USPS tracking URL correction logic in `functions.php`

### Implication
The site is already close enough to shipping metadata that this feature is feasible.
The missing piece is **delivered timestamp capture + safe public rendering**.

---

## Recommended Architecture

## Phase 0 — Discovery Spike (required before implementation)
Before writing the feature, inspect recent real orders in the live environment and answer:

1. Which system is the source of truth for tracking?
   - Pirate Ship
   - Shippo
   - AfterShip
   - WooCommerce extension
   - other
2. Is delivered status already stored anywhere in order/fulfillment meta?
3. What carrier mix do we actually use?
   - USPS only
   - USPS + UPS
   - USPS + UPS + FedEx
4. What timestamp best represents "shipment started"?
   - fulfillment created
   - label created
   - carrier acceptance / first scan
5. Do we have enough volume for a last-10 feed to stay fresh?

### Discovery outcome decision tree
- **If delivered timestamp already exists in meta:** build from stored data.
- **If only tracking number/provider exist, but provider has webhook/API:** add sync job.
- **If neither exists:** do not build the live strip; use static shipping proof instead.

---

## Backend Plan

## Option A — Preferred: reuse existing provider data
If the current shipping/tracking provider can return delivered timestamps:
- read delivered timestamp from existing meta or webhook payload
- store normalized values on the order
- rebuild a sanitized public feed cache

This is best because it works across USPS/UPS/FedEx and avoids carrier-specific logic.

## Option B — Acceptable fallback: USPS direct
Use USPS directly **only if**:
- most shipments are USPS
- reliable USPS API credentials exist
- there is no better provider integration available

Do **not** scrape the public USPS tracking page.

## Option C — Not recommended for Phase 1
Do not start with a new custom database table unless volume/query performance proves it necessary.
For this site, **order meta + cached feed option** is the simplest practical approach.

---

## Theme-Level Structure

### New file
- `theme/svicloudtvbox-lumen/inc/class-svic-verified-deliveries.php`

### Bootstrap from
- `theme/svicloudtvbox-lumen/functions.php`

### Render from
- `theme/svicloudtvbox-lumen/header.php`

### Style in
- `theme/svicloudtvbox-lumen/assets/css/parts/06-verified-deliveries-strip.css`
- add partial to `theme/svicloudtvbox-lumen/assets/css/bundles.json`

### Translations in
- `theme/svicloudtvbox-lumen/lang/en_US.php`
- `theme/svicloudtvbox-lumen/lang/zh_TW.php`
- `theme/svicloudtvbox-lumen/lang/zh_CN.php`

### Optional JS
Only if needed for enhanced pause/duplication behavior:
- `theme/svicloudtvbox-lumen/assets/js/theme.js`

Prefer CSS-driven motion first.

---

## Proposed Data Model

Store normalized private order meta using a `svic_` prefix.

### Suggested order meta keys
- `_svic_delivery_started_at_gmt`
- `_svic_delivery_delivered_at_gmt`
- `_svic_delivery_transit_seconds`
- `_svic_delivery_provider`
- `_svic_delivery_public_city`
- `_svic_delivery_public_state`
- `_svic_delivery_feed_eligible`
- `_svic_delivery_sync_status`

### Notes
- Do **not** store tracking numbers in the public feed cache.
- Reuse existing `_tracking_number` / `_shipment_provider` when available instead of duplicating them.
- `feed_eligible` should be false for canceled, refunded, test, replacement, local pickup, or malformed records.

### Cached public feed
Store a prebuilt sanitized array in a single option or transient, e.g.:
- `svic_verified_deliveries_feed_v1`

Each public feed item should contain only:
- `city`
- `state`
- `time_label`
- `carrier` (optional)
- `delivered_at` (optional internal use, not required to render)

---

## Start/End Timestamp Policy

This feature should **not** claim more precision than the data supports.

### Preferred calculation
`carrier acceptance / first scan` → `delivered`

### Acceptable fallback
`fulfillment created` → `delivered`

### Public wording
Use wording like:
- `delivered in 2 days`
- `recent verified deliveries`
- `delivery time after shipment`

Avoid wording like:
- `warehouse to home in 46 hours 12 minutes`
- `exact live last orders`

---

## Feed Eligibility Rules

Only include orders that are:
- real customer orders
- paid
- delivered
- U.S. domestic shipments
- within the last 60–90 days
- not canceled / refunded / failed
- not test / admin / internal / replacement orders

### Additional sanity filters
Reject items when:
- transit time is less than 2 hours
- transit time is greater than 14 days for the public ticker
- city/state are blank
- provider/tracking data is clearly malformed

### Privacy rules
Public feed must never expose:
- customer name
- order number
- tracking number
- street address
- ZIP code
- exact delivered timestamp

If small-town volume is too low, fall back to:
- `State only`
instead of `City, State`

---

## Cache + Sync Strategy

## Rendering rule
The frontend must never call USPS/provider APIs during page render.

### Update flow
1. Tracking is created/updated on an order.
2. Capture start timestamp if absent.
3. Schedule background status sync.
4. When shipment is confirmed delivered, store normalized delivery meta.
5. Rebuild the sanitized public feed cache.
6. Render the cache server-side in the theme.

### Job runner
Use **Action Scheduler** if available through WooCommerce.
Fallback to WP-Cron if needed.

### Refresh cadence
- On fulfillment create/update: schedule immediate sync
- Pending shipments: retry on interval (e.g. hourly)
- Feed cache: rebuild on successful delivery update and optionally daily cleanup

---

## Provider Integration Strategy

Because the active shipping plugin/provider is not tracked in this repo, the implementation should be adapter-friendly.

### Preferred internal contract
The theme class should normalize provider responses into something like:

```php
[
    'status'       => 'delivered',
    'started_at'   => '2026-04-15 18:12:00',
    'delivered_at' => '2026-04-17 14:41:00',
    'provider'     => 'usps',
]
```

### Priority order
1. Existing fulfillment/tracking plugin meta
2. Existing tracking provider API/webhook
3. USPS direct API fallback

### Avoid
- carrier page scraping
- page-load polling
- client-side fetches of sensitive shipment data

---

## Frontend Rendering Plan

## Placement
In `theme/svicloudtvbox-lumen/header.php`, render the strip:
- **after** `</header>`
- **before** `svic_render_breadcrumbs();`

This matches the requested placement: **right below the header**.

## Feature flag
Add a constant in `functions.php`:
- `SVIC_VERIFIED_DELIVERIES_ENABLED`

Default it to `false` until real data is validated.

## Display logic
Hide the strip when:
- feature flag is off
- fewer than 3 eligible feed items exist
- current page is cart/checkout/account/order-tracking

## Visual treatment
- dark glass / trust-strip styling aligned with header palette
- chip-based horizontal layout
- optional tiny carrier badge only if it helps, otherwise omit
- short disclaimer beneath or within the strip

---

## Suggested Translation Keys

Add a new translation block, e.g.:

```php
'verified_deliveries' => [
    'badge'       => 'Recent verified deliveries',
    'aria_label'  => 'Recent verified deliveries',
    'disclaimer'  => 'Based on recent delivered U.S. orders. Times vary by carrier, destination, weekends, and holidays.',
    'item_city_state' => '{{city}}, {{state}} · delivered in {{time}}',
    'item_state_only' => '{{state}} · delivered in {{time}}',
],
```

Mirror the same structure in:
- `zh_TW.php`
- `zh_CN.php`

---

## File Change Plan

| File | Change |
|------|--------|
| `theme/svicloudtvbox-lumen/functions.php` | add feature flag, require new class, bootstrap feature |
| `theme/svicloudtvbox-lumen/inc/class-svic-verified-deliveries.php` | new class for sync, cache, formatting, rendering |
| `theme/svicloudtvbox-lumen/header.php` | render strip below header |
| `theme/svicloudtvbox-lumen/assets/css/parts/06-verified-deliveries-strip.css` | new strip styles |
| `theme/svicloudtvbox-lumen/assets/css/bundles.json` | include new CSS partial in `style` bundle |
| `theme/svicloudtvbox-lumen/lang/en_US.php` | add English translations |
| `theme/svicloudtvbox-lumen/lang/zh_TW.php` | add Traditional Chinese translations |
| `theme/svicloudtvbox-lumen/lang/zh_CN.php` | add Simplified Chinese translations |
| `theme/svicloudtvbox-lumen/assets/js/theme.js` | optional only if CSS-first approach needs enhancement |

---

## Phased Delivery Plan

## Phase 0 — Discovery + Data Audit
- inspect live recent delivered orders
- identify provider and available meta
- confirm carrier mix
- confirm usable start/delivered timestamps
- confirm enough order volume

### Deliverable
Short findings note with a yes/no answer on data readiness.

## Phase 1 — Backend normalization
- add theme class
- normalize shipment/delivery timestamps onto orders
- schedule sync jobs
- build sanitized cached feed

### Deliverable
Server-side function that can return up to 10 eligible public items.

## Phase 2 — Frontend strip
- add render call in `header.php`
- add CSS partial and bundle entry
- add translations
- implement motion + reduced-motion behavior

### Deliverable
Tasteful below-header trust strip on marketing/product pages.

## Phase 3 — QA + rollout
- verify no page-load HTTP calls
- verify EN / zh_TW / zh_CN rendering
- verify cache rebuilds correctly
- confirm privacy rules
- measure CTR / conversion effect

### Deliverable
Production-ready rollout behind feature flag.

---

## Acceptance Criteria

### Functional
- Strip renders below header and above breadcrumbs/content.
- Strip shows at most 10 real delivered orders.
- Each item is anonymized and shows only rounded transit time.
- Strip is hidden when no safe dataset exists.
- Strip never triggers live carrier/provider API calls during page render.

### UX
- Motion is slow and non-intrusive.
- Animation pauses on hover/focus.
- `prefers-reduced-motion` disables motion.
- Strip is hidden on checkout/cart/account/order-tracking.

### Privacy / trust
- No names, order numbers, tracking numbers, ZIPs, or exact timestamps are shown.
- Copy clearly frames the strip as recent verified deliveries, not fake urgency.

### Engineering
- Feature can be toggled off via constant.
- CSS builds through existing workflow.
- Theme sync/deploy scripts remain unchanged.

---

## QA Checklist

- [ ] Orders with missing city/state do not render broken chips
- [ ] USPS/UPS/FedEx data does not cause provider-specific layout breakage
- [ ] Strip hides completely when feed cache is empty
- [ ] Mobile view remains readable under the header
- [ ] No console errors
- [ ] No PHP warnings when provider data is unavailable
- [ ] Cache updates survive normal site traffic and cron behavior
- [ ] Chinese translations fit without overflow

---

## Build / Deploy Notes

After CSS implementation:

```bash
python3 scripts/build_css.py --theme svicloudtvbox-lumen
./scripts/sync_theme_container.sh
```

For deployment:

```bash
./scripts/deploy-theme.sh --dry-run
```

---

## Time Estimate

## If delivered timestamps already exist
- Discovery: 2–4 hours
- Build: 1–2 days
- QA: 0.5–1 day

**Total:** ~2–3 days

## If provider API/webhook is needed
- Discovery: 0.5–1 day
- Integration + sync: 1–2 days
- Frontend + QA: 1–2 days

**Total:** ~3–5 days

## If USPS direct must be built from scratch
- Discovery/API validation: 1 day
- Integration: 1–2 days
- Frontend + QA: 1–2 days

**Total:** ~4–6 days

---

## Recommendation Summary

### Build this if
- real delivery timestamps are available or can be synced reliably
- the strip is anonymized and tasteful
- it is framed as shipping proof, not fake urgency

### Do not build this if
- data reliability is weak
- only public carrier-page scraping is possible
- the result would expose too much customer/order detail

### Best version for this site
A **Recent Verified Deliveries** trust strip below the header, powered by cached real delivery data, implemented inside the theme to fit the current deploy workflow.

---

## Immediate Next Steps

1. Audit 10–20 recent delivered orders in production.
2. Confirm the active tracking provider and whether delivered timestamps already exist.
3. Decide whether public items should show `City, State` or `State only`.
4. Confirm pages where the strip should be hidden.
5. If discovery passes, implement Phase 1 in this branch.
