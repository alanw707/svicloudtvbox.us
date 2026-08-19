# Storefront frontend baseline and stylesheet diagnosis

Captured locally on 2026-08-15 with `scripts/capture_frontend_audit.mjs` at 1440×900 and 390×844. Raw route/style/DOM evidence is in `before/audit.json`; screenshots are in `before/`.

## Route inventory

| Route | Main purpose | Theme bundle observed | Desktop height | Mobile height | Initial diagnosis |
|---|---|---:|---:|---:|---|
| `/` | Launch/discovery homepage | `style.css`, `front-page.css` | 11,141 px | 17,374 px | Bundles load; journey is excessively long and full-page capture exposes `content-visibility` paint gaps/inaccurate intrinsic sizing. |
| `/shop/` | Product discovery | `style.css`, `woocommerce.css` | 3,984 px | 5,349 px | Bundle loads; desktop grid leaves the third product orphaned in a two-column layout. |
| `/compare/` | 10P+/10S comparison | `style.css`, `compare.css` | 7,580 px | 11,188 px | Bundle loads; dense repeated cards and small secondary copy make scanning difficult. |
| `/product/svicloud-15p/` | 15P prelaunch preview | `style.css`, `woocommerce.css` | 5,605 px | 8,549 px | Bundle loads; prelaunch content is safe, but hero/detail hierarchy is tall and repetitive. |
| `/product/svicloud-10p-plus/` | Purchasable PDP | `style.css`, `woocommerce.css` | 5,639 px | 7,815 px | Bundle loads; small quantity/category/utility targets and repeated confidence content. |
| `/product/svicloud-9p/` | Legacy funnel PDP | `style.css`, `woocommerce.css` | 5,372 px | 7,165 px | Bundle loads; small utility links and repeated confidence content. |
| `/cart/` | Cart review | `style.css`, `woocommerce.css`, Woo Blocks CSS | 1,204 px | 1,754 px | **Wrong rendering system:** Woo Cart Block bypasses the theme’s classic cart override and all `.lumen-cart*` component styles. No H1. |
| `/checkout/` | Billing/payment/order review | `style.css`, `woocommerce.css`, Woo Blocks CSS | 3,186 px | 4,776 px | Classic override renders, but notices/coupon DOM are duplicated and mobile order review appears after the Place order section. |

All 16 route/viewport requests returned HTTP 200, had one `<main>`, no horizontal overflow, no console errors, and all expected contextual theme stylesheets reported `link.sheet` loaded. The primary styling failure is therefore not a missing enqueue.

## Root-cause map

### Critical

1. **Cart markup/CSS contract is broken**
   - Local Cart page content is `<!-- wp:woocommerce/cart -->`.
   - `page.php` intentionally suppresses the normal page H1 for cart/checkout because the custom Woo template is expected to provide it.
   - Woo renders block classes such as `.wc-block-cart`, while `woocommerce/cart/cart.php` and `66-woocommerce-cart.css` target `.lumen-cart`, `.lumen-cart-table`, and `.lumen-cart-summary*`.
   - Result: the bespoke template and hundreds of component rules never apply; the page has no H1, bare links, undersized controls, weak grouping, and no branded shell.
   - Required repair: force the Cart page through the maintained classic cart override (or fully support blocks). Reusing the existing deep template is the smaller, safer seam.

2. **Checkout renders duplicate status feedback**
   - `woocommerce_before_checkout_form` emits the server-side Woo notice.
   - `displayInitialNotices()` in `assets/js/theme.js` reads the same notice and creates `.svic-cart-feedback` without removing or suppressing the original.
   - Result: one thin full-width notice plus a second floating toast, with the toast obscuring billing fields on mobile.
   - Required repair: preserve one semantic notice. Do not create a second toast when a visible server notice exists; style the inline checkout notice consistently.

3. **Checkout contains duplicate `id="coupon_code"` controls**
   - `form-coupon.php` creates the canonical input.
   - Checkout JavaScript clones the form into the order-summary display but does not replace the cloned input ID/label target.
   - Result: duplicate IDs and ambiguous label relationships.
   - Required repair: give the display clone a unique ID and update its label; continue mirroring its value into the canonical submitted form.

### High impact

4. **Mobile checkout asks for order placement before order review**
   - In `form-checkout.php`, payment and Place order live inside `.lumen-checkout__primary`; the summary `<aside>` follows that entire column.
   - The responsive stylesheet stacks primary before summary.
   - Result: at 390 px the customer encounters Place order before product/total review.
   - Required repair: change the mobile information order so review/total precedes final payment confirmation without duplicating controls.

5. **Homepage is too long and its paint optimization produces misleading blank full-page evidence**
   - The template renders ten major content sections after the hero, several repeating pricing, confidence, shipping, warranty, support, and FAQ concepts.
   - `50-lumen-section-utilities.css` applies `content-visibility:auto` with intrinsic heights as low as 360–900 px to sections whose final heights reach roughly 1,300–2,900 px.
   - Result: a 17,374 px mobile journey, large scroll distance, repeated decisions, and blank regions in full-page/browser-print style captures. Normal viewport scrolling paints the content, so this is not an enqueue failure.
   - Required repair: tighten the discovery journey and remove the fragile content-visibility optimization (or use realistic containment values only where proven beneficial).

6. **Shop desktop grid creates an orphan product card**
   - The product grid remains two columns at 1440 px despite three products.
   - Result: 15P/10P occupy row one and 10S sits alone on row two, weakening comparison and wasting space.
   - Required repair: use a balanced three-column large-screen grid and preserve one-column mobile readability.

7. **Secondary controls fall below comfortable target size**
   - Baseline DOM geometry identified 17–20 px-high quantity/category/compare/setup/privacy targets on PDP and checkout surfaces.
   - Result: weak touch/keyboard affordance despite no horizontal overflow.
   - Required repair: provide at least 24×24 CSS-pixel minimum targets (44×44 preferred for primary touch controls), with visible focus.

### Moderate

8. **Comparison and PDP pages are visually repetitive**
   - Product facts, confidence assurances, support links, FAQ, and final CTAs recur in multiple large panels.
   - Result: long pages with weak progressive disclosure and reduced scannability.
   - Root is template/content hierarchy, not missing CSS.

9. **Mobile navigation visually works but needs interaction validation**
   - The expanded menu has clear large links and language/cart actions.
   - The baseline does not yet prove focus containment, Escape close, focus return, current-page state, or background interaction lock.
   - These are accessibility-audit items rather than stylesheet-loading defects.

10. **Native/third-party form styling is inconsistent**
    - Checkout state/select UI briefly appears light against the dark theme, while Select2 and native states use different treatments.
    - Root is overlapping Woo/native/Select2 selectors rather than a missing bundle.

## Evidence files

- Reproducible capture: `scripts/capture_frontend_audit.mjs`
- Machine-readable baseline: `docs/frontend-audit/before/audit.json`
- Desktop/mobile route screenshots: `docs/frontend-audit/before/*-{desktop,mobile}.png`
- First-viewport screenshots: `docs/frontend-audit/before/*-{desktop,mobile}-viewport.png`
- Expanded mobile navigation: `docs/frontend-audit/before/navigation-mobile-open.png`

## Baseline invariants to preserve

- Every route returns HTTP 200.
- No horizontal overflow at 1440 px or 390 px.
- One main landmark on every route.
- One H1 on all routes after the cart mismatch is repaired.
- No console errors.
- 15P stays non-purchasable and visibly TBC.
- EN/zh route behavior and 9P/10P funnels remain intact.
