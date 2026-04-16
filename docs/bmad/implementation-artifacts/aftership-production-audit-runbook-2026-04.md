# AfterShip Production Audit Runbook — Recent Verified Deliveries

**Created:** 2026-04-16  
**Branch:** `feat/recent-verified-deliveries-strip`  
**Purpose:** verify whether the live site's AfterShip data can power the Recent Verified Deliveries strip.

Related files:
- `scripts/aftership_production_audit.php`
- `docs/bmad/implementation-artifacts/recent-verified-deliveries-plan-2026-04.md`
- `docs/bmad/implementation-artifacts/tech-spec-recent-verified-deliveries-strip.md`

---

## 1) What this audit must answer

We need to confirm on the live site or staging clone:

1. Is an **AfterShip plugin** actually active?
2. Does it store useful shipment data in WordPress locally?
3. Can we retrieve, for real recent orders:
   - carrier/provider
   - shipment start timestamp or acceptable proxy
   - delivered timestamp
   - enough location data for `City, State` or at least `State only`
4. Are there at least **10 recent delivered orders** with usable safe display data?
5. Is the data source:
   - local plugin meta/tables,
   - local plugin + API sync,
   - or remote-only?

---

## 2) Audit output we want

The desired evidence is:
- active plugin slug/name/version
- any AfterShip-related custom tables
- any AfterShip/tracking/delivery meta keys
- whether delivered timestamps exist
- whether recent orders show machine-readable shipment signals
- whether shipping city/state is present often enough for public display

We do **not** need full tracking numbers, customer emails, or full addresses in the audit report.

---

## 3) Audit tool included in this branch

Use:
- `scripts/aftership_production_audit.php`

What it does:
- bootstraps WordPress from `wp-load.php`
- lists active shipping/tracking candidate plugins
- lists candidate custom tables and options
- scans HPOS + legacy postmeta for AfterShip/tracking/delivery keys
- counts candidate order notes
- prints a recent sample of orders with **redacted** candidate shipment metadata

### Safety notes
- intended for **CLI use only**
- redacts tracking-like tokens, URLs, and emails in output
- should be run on **production or staging shell**, not exposed publicly over the web
- remove the temporary uploaded copy after running if you place it on the server

---

## 4) How to run it

## Option A — server shell / staging shell

If you have shell access and can upload the script temporarily:

```bash
php /path/to/aftership_production_audit.php /absolute/path/to/wp-load.php > aftership-audit-$(date +%F).txt
```

Example paths:

```bash
php /tmp/aftership_production_audit.php /home/USER/public_html/wp-load.php > aftership-audit-$(date +%F).txt
```

If WordPress core lives in a subdirectory, point to that site's actual `wp-load.php`.

---

## Option B — Dockerized WordPress container

If the environment is Dockerized, copy the script in temporarily and run it inside the container.

Example:

```bash
docker cp scripts/aftership_production_audit.php <wordpress-container>:/tmp/aftership_production_audit.php
docker exec <wordpress-container> php /tmp/aftership_production_audit.php /var/www/html/wp-load.php > aftership-audit-$(date +%F).txt
docker exec <wordpress-container> rm -f /tmp/aftership_production_audit.php
```

Local example used in this repo:

```bash
docker cp scripts/aftership_production_audit.php svicloud10p-wp:/tmp/aftership_production_audit.php
docker exec svicloud10p-wp php /tmp/aftership_production_audit.php /var/www/html/wp-load.php > aftership-audit-local.txt
docker exec svicloud10p-wp rm -f /tmp/aftership_production_audit.php
```

---

## Option C — no shell access

If you do not have shell access, ask the host/admin/dev to run the script and send back:
- the text output
- screenshots of the active AfterShip plugin page
- screenshots of one delivered order's shipment data in WooCommerce / AfterShip admin

If needed, we can also fall back to a **manual admin checklist**.

---

## 5) How to interpret the results

## Strong green light
Proceed if the audit shows:
- an active AfterShip plugin
- identifiable AfterShip or tracking meta/tables
- real delivered timestamps
- recent delivered orders with usable city/state or state-only data
- enough sample volume for a 10-item feed

## Yellow light
Proceed carefully if:
- AfterShip is active but delivered data is not stored locally
- only remote/API-backed data is available
- location quality is weak, forcing `State only`
- multi-shipment orders appear common

## Red light
Do **not** implement the real feed yet if:
- no delivered timestamps exist
- no machine-readable shipment data is available locally or via accessible API/webhooks
- the plugin is installed but not actually used operationally
- recent usable delivered volume is too low

---

## 6) Specific fields to look for in the output

### Good signals
- plugin name/version containing `AfterShip`
- tables containing `aftership`, `tracking`, `shipment`, `deliver`, `fulfill`
- meta keys resembling:
  - `aftership_*`
  - `*_tracking_*`
  - `*_shipment_*`
  - `*_delivered_*`
  - `*_delivery_*`
- status values like:
  - `delivered`
  - `in_transit`
  - `out_for_delivery`
- timestamps that look like:
  - `YYYY-MM-DD HH:MM:SS`
  - unix timestamps converted by the script

### Weak signals
- only order notes saying tracking was added
- only generic WooCommerce `completed` statuses
- only tracking URLs with no delivered event timestamp

---

## 7) What to send back after running it

Please return:
1. the audit output text file
2. any screenshot of the active AfterShip plugin in wp-admin
3. one or two screenshots of a delivered order's tracking timeline if visible in admin
4. notes on whether you ship mostly via USPS, or mixed carriers

---

## 8) Decision template

Use this template when reviewing the output:

- **AfterShip active?** yes / no
- **Version / plugin slug:**
- **Custom tables found?** yes / no
- **Relevant meta keys found?** yes / no
- **Delivered timestamp found?** yes / no
- **Usable start timestamp found?** yes / no
- **Carrier field found?** yes / no
- **Usable public location available?** city+state / state only / no
- **At least 10 recent eligible delivered orders?** yes / no
- **Recommended next step:** proceed / hold / use static fallback

---

## 9) Current recommendation

Start with this audit before writing any production feature code.

If the output confirms AfterShip has the needed data, we can move into:
- normalized order meta design
- cache rebuild strategy
- theme render implementation

If the output does not confirm that, we should switch to:
- a static shipping-proof strip
- or a webhook/API sync project before UI work
