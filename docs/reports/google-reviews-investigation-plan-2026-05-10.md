# Google Reviews investigation + implementation plan

Date: 2026-05-10

## Scope

Goal: let customers leave real reviews and show trustworthy review proof on the SVICLOUD site without fake stars/placeholders.

## Current repo findings

- Google Customer Reviews merchant ID is configured in `theme/svicloudtvbox-lumen/functions.php` as `SVIC_GOOGLE_CUSTOMER_REVIEWS_MERCHANT_ID = 5317978135`.
- Google Customer Reviews opt-in payload/rendering exists in `theme/svicloudtvbox-lumen/inc/helpers-svic.php`:
  - `svic_get_google_customer_reviews_optin_payload()` builds payload from WooCommerce order data.
  - `svic_render_google_customer_reviews_optin()` loads `https://apis.google.com/js/platform.js` and calls `gapi.load('surveyoptin')`.
- Thank-you page calls the opt-in renderer in `theme/svicloudtvbox-lumen/woocommerce/checkout/thankyou.php`.
- Homepage testimonials are intentionally disabled in `theme/svicloudtvbox-lumen/functions.php` via `SVIC_TESTIMONIALS_ENABLED = false`.
- Testimonial translations still contain placeholders in `theme/svicloudtvbox-lumen/lang/en_US.php`; do not enable until real quotes replace placeholders.
- Organization aggregate rating schema is disabled in `theme/svicloudtvbox-lumen/functions.php` until real review count exists.
- No Google Customer Reviews rating badge implementation found (`ratingbadge` not present).
- Support page only explains the review flow; it does not link to a Google review destination.

## Likely root causes

1. No reviews showing on site because current code only implements the post-purchase Google Customer Reviews opt-in survey, not the visible rating badge or a reviews/testimonials page.
2. Opt-in popup may fail intermittently because `platform.js?onload=svicRenderGoogleCustomerReviews` is printed before `window.svicRenderGoogleCustomerReviews` is defined. Async loading can race and call the onload before the function exists.
3. Google Customer Reviews will not show public seller rating/badge until Google has enough eligible survey responses and merchant settings are approved in Merchant Center.
4. If the expectation is Google Business Profile reviews, this repo is wired for Google Customer Reviews/Merchant Center, not Google Business Profile review display.

## Recommended implementation plan

### Phase 1 — Fix opt-in reliability

Files:
- `theme/svicloudtvbox-lumen/inc/helpers-svic.php`
- `theme/svicloudtvbox-lumen/woocommerce/checkout/thankyou.php`

Tasks:
1. Move inline callback/config definition before loading `platform.js`.
2. Add safe retry if `gapi` is not ready yet.
3. Add optional debug mode gated by query string, e.g. `?svic_gcr_debug=1`, logging only payload readiness and render attempts.
4. Add Playwright or PHP-level assertion that thank-you page includes:
   - `svicGoogleCustomerReviewsConfig`
   - `surveyoptin`
   - Merchant ID
   - Estimated delivery date in `YYYY-MM-DD`

### Phase 2 — Add visible Google Customer Reviews badge

Files:
- `theme/svicloudtvbox-lumen/functions.php` or new helper in `inc/helpers-svic.php`
- `theme/svicloudtvbox-lumen/assets/css/parts/` if custom badge container styling needed
- `theme/svicloudtvbox-lumen/assets/css/bundles.json` if adding CSS partial

Tasks:
1. Add feature flag, default false until confirmed in production:
   - `SVIC_GOOGLE_CUSTOMER_REVIEWS_BADGE_ENABLED`
2. Render Google rating badge site-wide in footer when enabled:
   - Use `gapi.load('ratingbadge')`.
   - Render with merchant ID `5317978135`.
   - Prefer Google-controlled badge position, e.g. bottom-right, unless it conflicts with WhatsApp support chat.
3. Resolve overlap with floating WhatsApp chat if both are enabled.
4. Validate on desktop and mobile.

### Phase 3 — Add a customer-facing “Leave a review” path

Choose one path depending on business goal:

A. Google Customer Reviews only:
- Customers cannot directly leave a Google Customer Reviews review from an arbitrary public link; they opt in after checkout and Google emails them after delivery.
- Add clearer support/account copy explaining that only eligible buyers get Google’s email survey.

B. Google Business Profile reviews:
- Add direct CTA to Google Business Profile review link if business profile exists.
- Add button to support page and order emails: “Leave a Google review”.
- This is separate from Merchant Center Customer Reviews.

C. WooCommerce product reviews:
- Enable product reviews and add post-delivery email CTA to product review form.
- Show reviews on product pages; optionally curate snippets on homepage.

Recommended: use B + current A. Google Customer Reviews improves seller ratings in Google surfaces; Google Business Profile gives direct customer review link and visible local trust.

### Phase 4 — Show real reviews/testimonials onsite

Files:
- `theme/svicloudtvbox-lumen/front-page.php`
- `theme/svicloudtvbox-lumen/lang/en_US.php`
- `theme/svicloudtvbox-lumen/lang/zh_TW.php`
- `theme/svicloudtvbox-lumen/lang/zh_CN.php`
- CSS partial containing `.lumen-testimonials` styles, if missing or stale

Tasks:
1. Replace placeholder testimonial quotes with real customer-approved quotes.
2. Set `SVIC_TESTIMONIALS_ENABLED` to true only after real quotes exist.
3. Add source labels like “Verified buyer”, “Google review”, or “Support message” only when accurate.
4. Keep FTC-safe: no fake reviews, no invented names/cities, no aggregate rating schema unless public source and count are real.

### Phase 5 — Enable review schema only when defensible

Files:
- `theme/svicloudtvbox-lumen/functions.php`

Tasks:
1. If using Google Business Profile aggregate rating, enable `svic_organization_aggregate_rating` only with real `ratingValue` and `reviewCount`.
2. Do not use Google Customer Reviews rating unless badge/account reports provide public seller rating data.
3. Keep schema values synced manually or via trusted source.

## Implementation status

Completed locally:

- Fixed Google Customer Reviews opt-in loader ordering/race.
- Added official Google Customer Reviews badge renderer in footer.
- Added configurable Google Business Profile review CTA for support and thank-you pages.
- Added Playwright coverage in `tests/playwright/google-reviews.spec.ts`.
- Updated checkout-flow test to assert Google Customer Reviews opt-in markup.
- Added WooCommerce product reviews section to the custom product page template.
- Added thank-you page product review CTA linking buyers to the purchased product `#reviews` section.

Merchant Center API verification:

- Merchant ID `5317978135` matches code.
- Account name: `SvicloudTVbox.us`.
- Website claimed: true.
- Products: 3 active, 0 disapproved across US/CA Shopping and Free listings.
- Business customer service email: `support@svicloudtvbox.us`.
- Google Customer Reviews program/badge status is not exposed by checked Content API endpoints; requires Merchant Center UI screenshot.

## Validation checklist

- Complete a test order in local/staging.
- Confirm thank-you page contains GCR opt-in script and valid payload.
- Confirm no JS console errors on thank-you page.
- Confirm Merchant Center Customer Reviews program is enabled and domain/checkout URLs match.
- Confirm badge appears only after Google has seller rating available.
- Confirm support/review CTA points to correct review system.
- Confirm homepage has no placeholder review text before enabling testimonials.

## Open questions

1. Do you mean Google Customer Reviews in Merchant Center, Google Business Profile reviews, or both?
2. Do you already have a Google Business Profile review link?
3. Should the visible trust element be Google’s official rating badge, curated customer quotes, or both?
