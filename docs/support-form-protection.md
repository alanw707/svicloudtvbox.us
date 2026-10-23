# Support form protection

## Scope and policy

The theme owns `/support/` and the `svic_support_form` admin-post handler; this is not a contact-form plugin setting. The owner approved spam reduction plus purchase verification for after-sales, while preserving presales and missing-order paths.

- After-sales requires a WooCommerce shop order ID plus its exact checkout email (case-insensitive). Accepts paid orders, including historically paid/refunded orders. Guests do not need an account. Accepts plain IDs, `#ID`, and `WC-ID`; arbitrary custom order-number formats need manual verification.
- Unpaid/unverifiable orders, missing details, or a different contact email go to manual verification instructions; mismatches never reveal whether the ID or email exists. No customer record is returned or modified.
- Presales has only product/payment, shipping and compatibility categories, a distinct `[SVICLOUD Presales]` subject, and an explicit unverified label. It is intentionally not purchase-gated.
- Other-seller requests show original-seller guidance rather than generating a technical-support email. This is not a blanket ban on contacting the store.
- The page and new notices support English, Traditional Chinese and Simplified Chinese.

## Spam controls

- Existing WordPress nonce retained; a nonce alone is not bot protection.
- Offscreen, non-focusable, screen-reader-hidden honeypot; filled traps silently generate no email.
- Five attempts per IP per fixed hour, for both guests and logged-in users. Five validated requests per email per hour. Email keys use HMAC, not plaintext addresses.
- Ten-minute identical-message suppression after a successful `wp_mail` return. Failed sends are not labeled delivered or duplicate.
- Per-email atomic `add_option` send lock serializes concurrent sends; a stale lock expires after 120 seconds and is reclaimed on the next request for that email. Normal paths remove it immediately. No recurring cleanup job or new credentials.
- Field type/length validation, allowlisted categories/devices, plain-text message body, email-only Reply-To. IP/email counters use ordinary WP transients, not a distributed atomic rate-limit service.

Limits: this reduces common bot/repeat noise; it cannot guarantee zero spam. A human or sophisticated bot can mislabel a message as presales. Direct-to-mailbox spam is unaffected. No CAPTCHA service/key or Cloudflare rules were added. Shared IPs share the IP budget. PHP `REMOTE_ADDR` must contain the true visitor IP restored by the trusted host; never trust arbitrary forwarded headers in application code. Verify this hosting behavior before release. External WAF protections remain independent.

## Verification

Server fixture uses the actual handler with **mocked** WordPress/Woo orders and a mail sink:

```sh
php tests/fixtures/assert-support-form.php
```

Local browser fixture renders the actual PHP page in all locales, then exercises routing, native validation, intercepted request fields, mobile/desktop overflow and JS errors. It does not claim a live WordPress/mail integration test:

```sh
npm ci
python3 scripts/build_css.py --theme svicloudtvbox-lumen --bundle support
# In a separately tracked terminal:
python3 -m http.server 8774 --bind 127.0.0.1
# In another terminal (Playwright Chromium must be installed):
node tests/support-form-browser.cjs
# Optional installed-browser override:
CHROME_PATH=/path/to/chromium node tests/support-form-browser.cjs
```

Generated screenshots/HTML remain under `artifacts/support-form/` and are not release assets. The browser suite refuses non-loopback fixture origins; all POSTs are intercepted. No real email, customer order, coupon or payment is created.

## Release gates

1. Review feature-branch diff and passing fixture/lint/build checks.
2. Check hosting visitor-IP restoration and shortcode/custom order-number assumptions. If unavailable, report those gates as unverified rather than claiming production protection.
3. Owner-approved merge to main via GitHub Actions only. Do not deploy locally or disable FTPS certificate verification. The previously failed main deployment reported a certificate-pin mismatch; resolve that through trusted host verification as a separate approved infrastructure change.
4. After a successful deployment, verify canonical EN/TW/CN forms and cache freshness, missing/wrong purchase rejection, presales routing and guest usability. Coordinate one clearly labeled test email with the owner if end-to-end delivery proof is needed. Never send customer test messages merely to prove a guard works.
5. Monitor actual form submissions separately from direct emails. Roll back through a reviewed source change if legitimate customers are blocked; no database migration is involved.
