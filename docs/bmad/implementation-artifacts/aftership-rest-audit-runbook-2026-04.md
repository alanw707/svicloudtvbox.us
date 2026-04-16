# AfterShip REST Audit Runbook — Application Password Path

**Created:** 2026-04-16  
**Branch:** `feat/recent-verified-deliveries-strip`  
**Purpose:** audit the live site for AfterShip/tracking data **without server shell access**, using a WordPress Application Password over REST.

Related files:
- `scripts/aftership_rest_audit.py`
- `docs/bmad/implementation-artifacts/aftership-production-audit-runbook-2026-04.md`
- `docs/bmad/implementation-artifacts/recent-verified-deliveries-plan-2026-04.md`

---

## 1) When to use this path

Use this method if:
- you do **not** have shell access
- you are **not** using Docker
- you **can** create a WordPress Application Password for an admin/shop-manager account with enough permissions
- the site exposes the normal WordPress and WooCommerce REST APIs over HTTPS

This is the best **no-shell** audit path.

---

## 2) What it checks

The script:
- connects to `/wp-json/`
- looks for tracking-related namespaces/routes
- tries to list active plugins via `/wp-json/wp/v2/plugins`
- inspects recent WooCommerce orders via `/wp-json/wc/v3/orders`
- scans order `meta_data` for keys like:
  - `aftership_*`
  - `*_tracking_*`
  - `*_shipment_*`
  - `*_delivery_*`
  - `*_carrier_*`
- checks recent order notes for shipment-related wording
- redacts URLs, tracking-like tokens, and emails in output

---

## 3) Requirements

You need:
- Python 3 on your local machine
- the repo copy with this script
- the live site's HTTPS base URL
- a WordPress username
- a WordPress **Application Password** for that user

### Recommended account
Use an admin account or another account that can:
- view plugins
- access WooCommerce orders through REST

---

## 4) How to create the Application Password

In WordPress admin:
1. Go to **Users**
2. Edit your user profile
3. Scroll to **Application Passwords**
4. Create a new password with a label like:
   - `aftership-audit-2026-04`
5. Copy the generated password immediately

Use HTTPS for the live site. Application Password auth is intended for secure transport.

---

## 5) How to run the audit locally

From the repo root:

```bash
python3 scripts/aftership_rest_audit.py --base-url https://svicloudtvbox.us --username YOUR_WP_USERNAME
```

The script will securely prompt for the Application Password.

### Optional environment variable
Instead of typing the password interactively:

```bash
export WP_APP_PASSWORD='xxxx xxxx xxxx xxxx xxxx xxxx'
python3 scripts/aftership_rest_audit.py --base-url https://svicloudtvbox.us --username YOUR_WP_USERNAME
```

---

## 6) Save the output to a file

Recommended:

```bash
python3 scripts/aftership_rest_audit.py --base-url https://svicloudtvbox.us --username YOUR_WP_USERNAME > aftership-rest-audit.txt
```

Then send back the resulting text file.

---

## 7) What a successful result looks like

Best case, the output shows:
- an active AfterShip plugin
- REST namespaces or routes related to shipment/tracking
- recent completed orders with candidate shipment meta keys
- some keys/values that imply:
  - carrier/provider
  - delivered status
  - delivered timestamp
  - shipment/transit timestamps

That is enough to decide whether the Recent Verified Deliveries strip can use the live site's data without shell access.

---

## 8) Limitations of this method

This method may still miss data if:
- the AfterShip plugin stores data only in custom tables not exposed by REST
- the plugin data is visible only in wp-admin UI, not in order `meta_data`
- the account lacks permission for plugin or WooCommerce REST endpoints
- the host/site blocks Basic Auth headers

If that happens, the next fallback is:
1. **manual wp-admin screenshot audit**, or
2. a **temporary authenticated custom REST endpoint** added via snippet/plugin/theme edit

---

## 9) Decision guide

### Green light
Proceed if REST output confirms:
- AfterShip active
- delivered/timestamp data visible in recent orders
- enough eligible recent delivered orders exist

### Yellow light
Proceed carefully if:
- plugin is visible but shipment data is sparse
- only carrier/tracking fields are visible, but no delivered timestamp
- data quality suggests `State only` instead of `City, State`

### Red light
Hold implementation if:
- no AfterShip/tracking signals appear in REST
- WooCommerce order meta does not expose shipment data
- delivered timing still cannot be proven

---

## 10) If this path does not work

If the script fails due to permissions or missing data, I can prepare the next no-shell option:
- a **temporary custom REST audit endpoint** protected by Application Password auth

That approach requires adding a small temporary snippet in WordPress admin, but still does **not** require server shell access.
