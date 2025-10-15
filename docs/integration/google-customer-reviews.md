# Google Customer Reviews Opt-in Integration Plan

## Objective
Enable the Google Customer Reviews survey opt-in prompt on the order confirmation page while satisfying Google's integration requirements and keeping customer data accurate and secure.

## Current Implementation Snapshot
- **Merchant ID constant**: `SVIC_GOOGLE_CUSTOMER_REVIEWS_MERCHANT_ID` (default `5317978135`) declared in `theme/svicloudtvbox-lumen/functions.php`.
- **Payload builder**: `svic_get_google_customer_reviews_optin_payload()` in `inc/helpers-svic.php` prepares the JSON payload (order id, customer email, delivery country, estimated delivery date, optional GTIN list).
- **Renderer**: `svic_render_google_customer_reviews_optin()` injects the required `<script>` tags and is invoked from `woocommerce/checkout/thankyou.php` after successful order rendering.
- **Extensibility hooks**:
  - `svic_google_customer_reviews_enabled` – toggle per order.
  - `svic_google_customer_reviews_merchant_id` – override merchant id.
  - `svic_google_customer_reviews_estimated_transit_days` – control delivery ETA calculation (default 5 days from order creation).
  - `svic_google_customer_reviews_gtin_meta_keys` – declare product meta keys that hold GTINs.
  - `svic_google_customer_reviews_optin_payload` – last-chance payload override before render.

## Prerequisites
1. **Policy compliance** – confirm checkout, cart, and confirmation pages live on the same domain and every document declares `<!DOCTYPE HTML>`.
2. **Merchant verification** – ensure the Google Customer Reviews merchant account is verified and the merchant ID matches the property we intend to report against.
3. **Privacy review** – update the site's privacy/return policy (done via new return policy page) to mention participation in Google Customer Reviews if required by internal policy.

## Integration Steps
1. **Merchant ID audit**
   - Validate the default constant (`5317978135`) or override via `wp-config.php` or filter if a different merchant ID is issued.
   - Optionally hide the script in non-production environments by filtering `svic_google_customer_reviews_enabled`.
2. **Product GTIN data**
   - Store GTIN values on each WooCommerce product using one of the recognized meta keys (`_wc_gcr_gtin`, `_gtin`, `gtin`, or `svic_gtin`), or adjust the meta key list via the corresponding filter.
   - If GTINs are unavailable, the integration continues without the `products` block.
3. **Estimated delivery refinement**
   - Default ETA is order creation date + 5 days. Override to match logistics SLA using:
     ```php
     add_filter('svic_google_customer_reviews_estimated_transit_days', fn($days, $order) => 7);
     ```
   - For complex logic (e.g., per shipping method), use the `svic_google_customer_reviews_optin_payload` filter to set `estimated_delivery_date` directly.
4. **Environment gating**
   - In staging/sandboxes, short-circuit via:
     ```php
     add_filter('svic_google_customer_reviews_enabled', fn($enabled) => false);
     ```
5. **Template confirmation**
   - Verify `svic_render_google_customer_reviews_optin($order)` remains in `woocommerce/checkout/thankyou.php` after order markup and only runs when an order object exists.

## QA Checklist
1. Place a test order (Stripe test mode acceptable) and confirm:
   - `https://apis.google.com/js/platform.js` loads on the order-received page.
   - Network tab shows a request to `gapi` with the serialized payload.
2. Validate rendered payload in DevTools console:
   ```js
   window.svicGoogleCustomerReviewsConfig
   ```
   Ensure order id, customer email, country, and ETA are correct.
3. Confirm the opt-in modal displays when Google eligibility requirements are met (may require Google's review/activation).
4. Regression check WooCommerce thank-you page (mobile + desktop) to ensure no layout shifts and scripts run without console errors.
5. After beta verification, remove any filters that disable the script and redeploy.

## Rollout Notes
- Coordinate with operations to ensure return/fulfillment SLAs match the estimated delivery logic.
- Update customer-facing FAQ/return policy to mention that a post-purchase survey may arrive via Google.
- Capture before/after screenshots of the thank-you page and attach them in the deployment PR per team guidelines.
