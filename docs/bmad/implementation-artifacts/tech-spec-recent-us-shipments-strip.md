# Tech Spec — Recent U.S. Shipments Strip

**Date:** 2026-04-16  
**Branch:** `feat/recent-verified-deliveries-strip`  
**Status:** Implemented in theme branch  
**Pivot:** replaces the heavier “verified delivered in X days” approach with a lighter **Recent U.S. shipments + estimated delivery days** strip powered by WooCommerce Shipping metadata.

---

## Goal

Render a slim trust strip below the site header that shows recent U.S. shipment destinations and estimated carrier delivery windows, using data already stored on WooCommerce orders.

### Example items
- `California · estimated 2 days`
- `Minnesota · estimated 4 days`
- `Nevada · estimated 2 days`

---

## Why this version

The live AfterShip / WooCommerce audit confirmed:
- real recent shipment data exists on orders
- WooCommerce Shipping metadata exposes recent labels, carrier/service, and estimated delivery days
- but the currently accessible data does **not** expose a reliable delivered timestamp

So this version preserves a real shipping trust signal **without** claiming exact delivered timing.

---

## Data source

Primary source:
- WooCommerce order meta written by **WooCommerce Shipping**

Relevant meta keys:
- `_wcshipping_selected_rates`
- `_wcshipping_shipment_dates`
- `wcshipping_labels`

Optional supporting meta exists from AfterShip, but this implementation does **not** depend on AfterShip.

---

## Public display rules

Each public chip shows only:
- destination **state**
- estimated carrier delivery window in days

The strip does **not** show:
- names
- order numbers
- tracking numbers
- street addresses
- ZIP codes
- exact shipment timestamps
- exact delivered timestamps

---

## Eligibility rules

Include only orders that:
- are `completed`
- ship to `US`
- have a shipping state
- contain WooCommerce Shipping rate metadata
- expose `delivery_days`
- expose a shipment/label timestamp usable for sorting recency

Skip orders when:
- estimated days are missing
- estimated days are outside a sane range
- shipping state is blank
- shipment metadata is malformed

---

## Rendering behavior

Placement:
- below `header`
- above breadcrumbs

Hide on:
- cart
- checkout
- account
- order-tracking

Render only when:
- feature flag is enabled
- at least 3 safe items exist

Animation:
- slow horizontal marquee when enough items exist
- pauses on hover/focus
- reduced-motion falls back to wrapped static chips

---

## Theme files

Implemented files:
- `theme/svicloudtvbox-lumen/inc/class-svic-recent-shipments.php`
- `theme/svicloudtvbox-lumen/functions.php`
- `theme/svicloudtvbox-lumen/header.php`
- `theme/svicloudtvbox-lumen/assets/css/parts/06-recent-shipments-strip.css`
- `theme/svicloudtvbox-lumen/assets/css/bundles.json`
- `theme/svicloudtvbox-lumen/lang/en_US.php`
- `theme/svicloudtvbox-lumen/lang/zh_TW.php`
- `theme/svicloudtvbox-lumen/lang/zh_CN.php`

---

## Implementation notes

### Feature flag
- `SVIC_RECENT_SHIPMENTS_ENABLED`

### Cache
- transient-backed feed cache in the theme
- short TTL
- cache cleared on checkout/order status events

### Feed shape
Internal feed items store only:
- state
- estimated days
- service name
- shipped-at timestamp for sorting

Public markup only uses the safe subset needed for display.

---

## Acceptance criteria

- Strip renders below the header.
- Strip uses WooCommerce Shipping order metadata, not live page-load API calls.
- Strip shows recent U.S. shipment states and estimated delivery windows.
- Strip does not claim actual delivered timing.
- Strip hides automatically when not enough safe data exists.
- Strip stays hidden on transactional pages.

---

## Follow-up option

If a later audit exposes reliable delivered timestamps, this strip can be upgraded into a true verified-deliveries strip. Until then, this simpler version is the truthful low-plumbing implementation.
