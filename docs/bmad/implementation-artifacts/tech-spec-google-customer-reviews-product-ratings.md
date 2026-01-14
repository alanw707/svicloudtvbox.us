---
title: 'Google Customer Reviews & Product Ratings Integration'
slug: 'google-customer-reviews-product-ratings'
created: '2026-01-13'
status: 'ready-for-dev'
stepsCompleted: [1, 2, 3, 4]
tech_stack:
  - PHP (WordPress/WooCommerce)
  - JavaScript (vanilla)
  - JSON-LD structured data
files_to_modify:
  - theme/svicloudtvbox-lumen/inc/helpers-svic.php
  - theme/svicloudtvbox-lumen/woocommerce/single-product.php
  - theme/svicloudtvbox-lumen/functions.php
  - theme/svicloudtvbox-lumen/footer.php
  - theme/svicloudtvbox-lumen/lang/en_US.php
  - theme/svicloudtvbox-lumen/lang/zh_TW.php
  - theme/svicloudtvbox-lumen/lang/zh_CN.php
code_patterns:
  - WooCommerce hooks and filters
  - Schema.org JSON-LD markup
  - Google APIs integration
test_patterns:
  - Manual order flow testing
  - Schema validation via Google Rich Results Test
  - Browser console debugging
---

# Google Customer Reviews & Product Ratings Integration

## Overview

### Problem Statement

Google Customer Reviews opt-in hasn't fired in 30+ days despite code existing in the codebase. The Google Merchant Center shows warning: "Google Customer Reviews hasn't displayed an opt-in notification to your customers in more than 30 days."

Additionally:
- No Google Customer Reviews seller badge is displayed on the site
- Product reviews are not shown on single product pages (reviews section missing from template)
- Product schema lacks `aggregateRating` property, preventing star ratings from appearing in Google search results

### Solution

Implement a complete Google reviews integration:
1. Debug and fix the existing GCR opt-in script on the order confirmation page
2. Add the GCR seller badge using merchantwidget.js
3. Add WooCommerce reviews section to single product pages
4. Extend product schema to include `aggregateRating` when reviews exist
5. Configure WooCommerce follow-up email for post-purchase review collection

### Scope

**In Scope:**
- Fix GCR opt-in script + add debug mode for troubleshooting
- Add GCR seller badge to site footer (inline placement)
- Add reviews UI section to `single-product.php`
- Extend `svic_build_product_schema_from_wc_product()` with `aggregateRating`
- Document WooCommerce review email settings (admin configuration)

**Out of Scope:**
- Third-party review platforms (Judge.me, Yotpo, Trustpilot)
- Google Product Ratings XML feed export
- Review incentive/discount programs
- Review moderation workflows
- Fake review detection

---

## Context for Development

### Existing Integration Points

**Google Customer Reviews Opt-in (exists but not working):**
- Merchant ID: `5317978135` (defined in `functions.php:14`)
- Payload builder: `svic_get_google_customer_reviews_optin_payload()` in `helpers-svic.php:1318`
- Renderer: `svic_render_google_customer_reviews_optin()` in `helpers-svic.php:1476`
- Called from: `thankyou.php:117`

**Product Schema (partial implementation):**
- Builder: `svic_build_product_schema_from_wc_product()` in `functions.php:1922`
- Output hook: `svic_output_single_product_schema` in `functions.php:3131` (hooked to `wp_head`)
- ✅ Already outputs on single product pages
- ❌ Missing: `aggregateRating` property (no review data included)

**WooCommerce Reviews:**
- Native WooCommerce reviews enabled but not displayed in custom `single-product.php` template
- Reviews tab/section was removed from product page layout

### Technical Constraints

- Must use vanilla JavaScript (no jQuery dependency for new code)
- Must maintain existing translation system (`svic_translate()`)
- Schema must pass Google Rich Results Test validation
- GCR badge position should not interfere with existing UI elements

---

## Technical Investigation Results

### GCR Opt-in Code Analysis

**Location**: `helpers-svic.php:1476-1515`

The GCR opt-in renderer exists and is called from `thankyou.php:117`. The code:

```php
function svic_render_google_customer_reviews_optin($order): void
{
    static $rendered = false;
    if ($rendered) { return; }
    $payload = svic_get_google_customer_reviews_optin_payload($order);
    if (!$payload) { return; }  // ← Silent failure point
    // ... renders script with gapi.surveyoptin.render()
}
```

**Potential Issues Identified**:
1. Silent failure if `$payload` returns null/false - no debugging output
2. No console logging to verify script execution
3. Payload builder at `helpers-svic.php:1318` may be returning null due to:
   - Missing order data
   - Invalid email format
   - Estimated delivery date calculation issues

**Required Fix**: Add debug mode with console logging to identify why opt-in isn't displaying.

### GCR Badge (merchantwidget.js)

**Current State**: NOT IMPLEMENTED

No `merchantwidget.js` integration exists anywhere in the codebase. The badge needs to be added to `footer.php` (ideal location: after existing footer badges around line 60-70).

### Product Schema Analysis

**Location**: `functions.php:1922-2026`

The `svic_build_product_schema_from_wc_product()` function builds Product schema with:
- ✅ @type, @id, url, name
- ✅ itemCondition, brand, category
- ✅ description, sku, image
- ✅ offers (price, availability, seller)
- ❌ **MISSING: aggregateRating**

**Schema Output**: Already hooked to `wp_head` via `svic_output_single_product_schema` at line 3148, so schema IS being output on single product pages.

**WooCommerce Review Methods Available** (not currently used):
- `$product->get_review_count()` - returns int
- `$product->get_average_rating()` - returns string (e.g., "4.5")

### WooCommerce Reviews UI

**Current State**: NOT DISPLAYED

The `single-product.php` template (245 lines) contains:
- Hero section with product image and pricing
- Description section
- Traffic CTA section
- FAQ section
- **NO reviews section** - WooCommerce reviews completely removed from template

Standard WooCommerce reviews use:
- `comments_template()` function - NOT called in this template
- `woocommerce_product_reviews` hook - NOT implemented

### Files Reference Table

| File | Lines | Purpose | Action Needed |
|------|-------|---------|---------------|
| `helpers-svic.php` | 1318-1515 | GCR payload + renderer | Add debug mode |
| `functions.php` | 1922-2026 | Product schema builder | Add aggregateRating |
| `single-product.php` | ~245 | Product page template | Add reviews section |
| `footer.php` | ~100 | Site footer | Add GCR badge |
| `thankyou.php` | 117 | Order confirmation | Verify GCR call |

### Technical Decisions

1. **Reviews UI Placement**: Add reviews section after FAQ section in `single-product.php` (before closing `</main>` tag)
2. **Badge Position**: Add GCR badge in footer, after existing trust badges
3. **aggregateRating Logic**: Only include in schema when `$product->get_review_count() > 0`
4. **Debug Mode**: Use URL parameter `?gcr_debug=1` to enable console logging

---

## Implementation Stories

### Task 1: Add Debug Mode to GCR Opt-in Renderer

- [ ] **File**: `theme/svicloudtvbox-lumen/inc/helpers-svic.php`
- [ ] **Action**: Modify `svic_render_google_customer_reviews_optin()` (line ~1476)
- [ ] **Changes**:
  1. Add sanitized debug mode check:
     ```php
     $debug = filter_input(INPUT_GET, 'gcr_debug', FILTER_SANITIZE_STRING) === '1';
     ```
  2. Add console.log statements to track:
     - When function is called
     - Payload contents (JSON stringified)
     - gapi.load callback execution
     - gapi.surveyoptin.render call
  3. Add PHP error logging when `$payload` is null:
     ```php
     if ($debug) { error_log('GCR: Payload is null - check payload builder'); }
     ```
- [ ] **Notes**: Debug output only when `?gcr_debug=1` is in URL. Uses sanitized input.

### Task 2: Add Debug Mode to GCR Payload Builder

- [ ] **File**: `theme/svicloudtvbox-lumen/inc/helpers-svic.php`
- [ ] **Action**: Modify `svic_get_google_customer_reviews_optin_payload()` (line ~1318)
- [ ] **Changes**:
  1. Add debug logging at each validation point:
     - Order object validation
     - Email extraction and validation
     - Estimated delivery date calculation
     - Country code validation
  2. Return detailed error info in debug mode instead of silent null
- [ ] **Notes**: Helps identify exactly where payload generation fails

### Task 3: Add GCR Seller Badge to Footer

- [ ] **File**: `theme/svicloudtvbox-lumen/footer.php`
- [ ] **Action**: Add GCR badge inline after existing footer trust badges (around line 60-70)
- [ ] **Changes**:
  1. Add badge container div in the footer badges area:
     ```html
     <div id="gcr-badge-container" class="gcr-badge-inline"></div>
     ```
  2. Add Google badge rendering script (before `</body>`):
     ```html
     <script src="https://apis.google.com/js/platform.js?onload=renderGcrBadge" async defer></script>
     <script>
       window.renderGcrBadge = function() {
         if (window.gapi && window.gapi.load) {
           window.gapi.load('ratingbadge', function() {
             if (window.gapi.ratingbadge) {
               window.gapi.ratingbadge.render(
                 document.getElementById('gcr-badge-container'),
                 { merchant_id: 5317978135, position: 'INLINE' }
               );
             }
           });
         }
       };
     </script>
     ```
  3. Add CSS for inline badge placement:
     ```css
     .gcr-badge-inline { display: inline-block; vertical-align: middle; margin-left: 10px; }
     ```
- [ ] **Notes**: Using INLINE position to place badge alongside existing trust badges. Merchant ID: `5317978135`

### Task 4: Add aggregateRating to Product Schema

- [ ] **File**: `theme/svicloudtvbox-lumen/functions.php`
- [ ] **Action**: Modify `svic_build_product_schema_from_wc_product()` (line ~2020, before `return $product_node;`)
- [ ] **Changes**:
  1. Get review data from product with proper type casting:
     ```php
     $review_count = (int) $product->get_review_count();
     $average_rating = (float) $product->get_average_rating();
     ```
  2. Add aggregateRating only when valid reviews exist:
     ```php
     if ($review_count > 0 && $average_rating > 0.0) {
         $product_node['aggregateRating'] = [
             '@type' => 'AggregateRating',
             'ratingValue' => number_format($average_rating, 1, '.', ''),
             'reviewCount' => $review_count,
             'bestRating' => '5',
             'worstRating' => '1',
         ];
     }
     ```
- [ ] **Notes**: Uses explicit type casting to handle edge cases where WooCommerce returns empty strings. `number_format()` ensures consistent decimal formatting for schema.

### Task 5: Add Reviews Section to Single Product Page

- [ ] **File**: `theme/svicloudtvbox-lumen/woocommerce/single-product.php`
- [ ] **Action**: Add reviews section after FAQ section (before closing `</main>`)
- [ ] **Changes**:
  1. Create new section with consistent styling:
     ```php
     <!-- Reviews Section -->
     <section class="product-reviews-section">
         <div class="container">
             <h2><?php echo svic_translate_html('products.reviews.title') ?: 'Customer Reviews'; ?></h2>
             <?php comments_template(); ?>
         </div>
     </section>
     ```
  2. Add CSS styling to match existing sections
  3. Ensure translation key exists or falls back gracefully
- [ ] **Notes**: WooCommerce's `comments_template()` renders the native reviews form and list

### Task 6: Add Reviews Section CSS

- [ ] **File**: `theme/svicloudtvbox-lumen/woocommerce/single-product.php` (inline `<style>` block) OR create `theme/svicloudtvbox-lumen/assets/css/parts/product-reviews.css`
- [ ] **Action**: Add styles for reviews section
- [ ] **Changes**:
  1. Style `.product-reviews-section` container (padding, background consistent with FAQ section)
  2. Style WooCommerce review elements:
     - `.woocommerce-Reviews` - main container
     - `.comment-form` - review submission form
     - `.commentlist` - reviews list
     - `.star-rating` - star display
  3. Ensure responsive design with mobile breakpoints
  4. Match existing theme typography (font-family, sizes from FAQ section)
- [ ] **Notes**: Check `theme/svicloudtvbox-lumen/assets/css/parts/` for existing pattern. If adding new file, import in main CSS.

### Task 7: Add Translation Keys for Reviews

- [ ] **File**: `theme/svicloudtvbox-lumen/lang/en_US.php`, `zh_TW.php`, `zh_CN.php`
- [ ] **Action**: Add translation keys for reviews section heading only
- [ ] **Changes**:
  1. Add `products.reviews.title` key:
     - EN: "Customer Reviews"
     - zh_TW: "顧客評價"
     - zh_CN: "顾客评价"
- [ ] **Notes**: Only the section heading needs custom translation. WooCommerce's native review form/list strings use WordPress i18n and are already translated via WooCommerce language packs. No need to override those.

### Task 8: Fix GCR Opt-in Based on Debug Findings

- [ ] **File**: `theme/svicloudtvbox-lumen/inc/helpers-svic.php`
- [ ] **Action**: Apply fix based on debug output from Tasks 1-2
- [ ] **Changes**: (To be determined after debug analysis)
  - Common fixes to check:
    1. **Estimated delivery date**: Ensure `delivery_date` is in correct format (YYYY-MM-DD)
    2. **Email validation**: Ensure email is extracted correctly from order
    3. **Country code**: Verify order billing country is supported (US, etc.)
    4. **Script timing**: Ensure gapi loads before render is called
- [ ] **Notes**: This task executes AFTER running debug mode and identifying the root cause. If debug reveals the issue is external (Google-side), document findings and close.

### Task 9: Document WooCommerce Review Email Settings

- [ ] **File**: N/A (Admin configuration)
- [ ] **Action**: Configure WooCommerce review request follow-up
- [ ] **Changes**:
  1. Navigate to WooCommerce → Settings → Emails
  2. Locate "Customer Review Reminder" email (if plugin needed, note which one)
  3. Configure timing: 7-14 days after order completion
  4. Document settings in this spec's Notes section
- [ ] **Notes**: WooCommerce core doesn't include review reminder emails. May require plugin like "Review Reminder for WooCommerce" or custom implementation. If plugin required, add to Dependencies.

---

## Acceptance Criteria

### AC1: GCR Debug Mode
- [ ] Given I am on the order confirmation page with `?gcr_debug=1`, when the page loads, then I see console.log output showing GCR initialization steps and payload data

### AC2: GCR Opt-in Display
- [ ] Given I complete a purchase with a valid email, when I reach the order confirmation page, then the Google Customer Reviews opt-in survey modal appears

### AC3: GCR Badge Visibility
- [ ] Given I am on any page of the site, when I scroll to the footer, then I see the Google Customer Reviews badge displayed

### AC4: Product Schema with Reviews
- [ ] Given a product has at least one review, when I view page source or run Rich Results Test, then the Product schema includes `aggregateRating` with correct `ratingValue` and `reviewCount`

### AC5: Product Schema without Reviews
- [ ] Given a product has zero reviews, when I view page source, then the Product schema does NOT include `aggregateRating` property

### AC6: Reviews Section Display
- [ ] Given I am on a single product page, when I scroll past the FAQ section, then I see a "Customer Reviews" section with the WooCommerce reviews form

### AC7: Review Submission
- [ ] Given I am logged in on a product page, when I fill out and submit the review form, then my review appears in the reviews list

### AC8: Schema Validation
- [ ] Given the product page with reviews, when I test with Google Rich Results Test, then the schema passes validation with no errors

### AC9: Translation Support
- [ ] Given the site language is set to Chinese, when I view the reviews section, then headings display in Chinese

### AC10: Guest Review Submission (if enabled)
- [ ] Given I am NOT logged in and guest reviews are enabled, when I fill out the review form with name/email, then my review is submitted successfully

### AC11: GCR Root Cause Identified
- [ ] Given debug mode is enabled, when I complete a test order, then the console output reveals why opt-in was not displaying (or confirms it now works)

---

## Dependencies

- **Google Merchant Center**: Merchant ID `5317978135` must remain active
- **WooCommerce Reviews**: Must be enabled in WooCommerce settings (currently enabled)
- **Google APIs**: `apis.google.com/js/platform.js` must be accessible
- **Review Email Plugin** (optional): WooCommerce core lacks review reminder emails. If Task 9 requires automated emails, consider "JEEB Review Reminder" or "Follow-Ups" plugin

---

## Testing Strategy

### Manual Testing
1. **GCR Debug**: Add `?gcr_debug=1` to thankyou page URL, check console
2. **Place Test Order**: Complete checkout flow, verify opt-in appears
3. **Badge Check**: Verify badge appears in footer on all pages
4. **Schema Validation**: Use Google Rich Results Test on product pages
5. **Review Flow**: Submit a test review, verify it displays

### Validation Tools
- Google Rich Results Test: https://search.google.com/test/rich-results
- Google Merchant Center: Check GCR status after implementation
- Browser DevTools: Console for debug output, Network for script loading

---

## Notes

### High-Risk Items
1. **GCR Script Loading**: Google's platform.js can fail silently if blocked by ad blockers
2. **Estimated Delivery Date**: Payload requires valid delivery date; may need adjustment for shipping calculation
3. **Reviews Section Layout**: New section may affect page layout/performance - test on mobile

### Rollback Plan
If issues arise after deployment:
1. **GCR Debug Code**: Safe to leave in place (only activates with URL param)
2. **GCR Badge**: Remove the `<div id="gcr-badge-container">` and associated script from footer.php
3. **Reviews Section**: Remove the `<section class="product-reviews-section">` block from single-product.php
4. **Schema Changes**: Remove the aggregateRating block from `svic_build_product_schema_from_wc_product()`
5. **Git**: All changes should be in a single commit for easy `git revert`

### Known Limitations
- Star ratings in Google Search only appear after Google re-crawls pages with reviews
- GCR surveys only sent to US customers with valid email addresses
- Badge may take 24-48 hours to reflect accurate seller rating

### Future Considerations (Out of Scope)
- Google Product Ratings XML feed for enhanced product ratings
- Review import from other platforms
- Automated review request emails via WooCommerce
