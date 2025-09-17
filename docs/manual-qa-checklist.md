# Manual QA Checklist

Use this checklist when verifying staging or production after theme updates.

## Pre-Flight
- Confirm latest theme build deployed and caches cleared (Hostinger cache manager + CDN).
- Verify test accounts and payment sandbox credentials are available if needed.
- Ensure Playwright smoke suite ran cleanly (`npm run test:playwright`).

## Core Commerce Flow
- Homepage loads without PHP notices; hero CTA (“Shop 10P+”) and product carousel render hero imagery.
- Primary navigation links (Shop, Compare, Bundles, Support) resolve to live pages without 404s.
- `Shop 10P+` CTA opens the 10P+ PDP; gallery thumbnails and pricing display, add-to-cart form visible.
- Add to cart increments mini-cart summary; “View cart” link opens `/cart/` with accurate line items and totals.
- Cart page updates quantities, removes items, and exposes “Proceed to checkout” button.
- Checkout page displays billing/shipping form, order summary, and payment gateways; form validation fires for required fields.
- Completing checkout with test payment succeeds (when sandbox enabled) and emails trigger (order confirmation, admin notice).

## Content & Trust Elements
- Compare page table populates all spec rows and buy buttons link to correct PDPs.
- Support/Contact page renders bilingual copy, contact form submits, and success message or confirmation email arrives.
- Footer copyright/current year accurate; warranty badges, testimonials, and social proof sections render.

## Localization & Accessibility
- Toggle any bilingual elements to confirm Chinese copy appears where promised.
- Keyboard navigation reaches header menu, hero CTAs, product cards, and checkout buttons with visible focus state.
- Run fast accessibility scan (e.g., Lighthouse, axe) to confirm no critical violations.

## Performance & Analytics
- Lighthouse performance score ≥90 on homepage and PDP (desktop/mobile) with LCP <1.5s.
- GA4/Hotjar scripts present once per page and record hits without duplicate beacons.
- XML sitemap responds 200 and includes PDP URLs; noindex tags absent on primary commerce pages.
