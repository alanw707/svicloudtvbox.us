# Storefront accessibility review

Scope: homepage, global navigation, shop, comparison, 15P/10P+/9P product pages, cart, and checkout. Tested locally at 1440, 390, and 320 CSS pixels with keyboard-only interaction and `prefers-reduced-motion: reduce`.

Evidence:
- `accessibility-audit.json`
- `contrast-audit.json`
- `before/audit.json`
- `scripts/audit_frontend_accessibility.mjs`
- `scripts/audit_frontend_contrast.mjs`

This is an implementation audit toward WCAG 2.2 AA expectations, not a certification.

## Critical issues

### A1. Mobile navigation is not modal when expanded

- The menu opens with `aria-expanded="true"` and is keyboard reachable.
- Body overflow remains `visible`; page content is not inert.
- After the menu's 11 controls, Tab moves into homepage content behind the open menu.
- Escape closes the menu, but focus is not returned to the menu button.

**Impact:** Keyboard and screen-magnifier users can lose context and interact with obscured content.

**Required change:** Lock background scroll, contain Tab/Shift+Tab within the menu while open, close on Escape, restore focus to the toggle, and update the toggle's accessible name between Open/Close navigation.

### A2. Cart has no page H1 or coherent semantic UI

- All widths report `h1Count: 0`.
- The first headings are H2s emitted by the Cart Block.
- The Cart Block bypasses the maintained classic template, producing an unnamed linked image and 20–22 px-high product/remove controls.

**Impact:** Screen-reader users lack a page heading; touch and motor-impaired users receive undersized controls and weak grouping.

**Required change:** Render the maintained `.lumen-cart` template, which supplies an H1, explicit table labels, named quantity controls, and semantic summary grouping; then verify its responsive table transformation.

### A3. Checkout feedback is duplicated and can obscure fields

- A server notice appears at the page top.
- JavaScript copies the same text into `.svic-cart-feedback`.
- On mobile the floating duplicate overlays billing controls.

**Impact:** Repeated announcements and visual obstruction increase cognitive load and can interfere with form completion.

**Required change:** Keep the inline server notice as the single source when present; only use the live-region toast for AJAX/pending feedback without an inline notice.

### A4. Checkout has duplicate `coupon_code` IDs

- Both the canonical hidden/submission form and the visible summary clone use `id="coupon_code"` at all tested widths.

**Impact:** Label targeting and DOM relationships are ambiguous.

**Required change:** Rename the visible clone to a unique ID and point its cloned label at that ID while synchronizing values to the canonical field.

## Moderate issues

### A5. No skip link

The first Tab stop on the homepage is the logo. There is no “Skip to main content” control.

**Change:** Add a translated skip link as the first focusable body control, target a stable `#main-content`, and reveal it visibly on focus.

### A6. Several PDP controls are below the 24×24 WCAG 2.2 target minimum

- 10P+ quantity input: 64×17 px and no visible focus change.
- Product category link: 117×20 px.
- Compare/setup utility links: approximately 82–102×19 px with no visible focus change.
- 9P comparison/setup links repeat the issue.

**Change:** Increase control line boxes/hit areas, style quantity focus, and add a consistent `:focus-visible` treatment to inline utility links.

### A7. Checkout error behavior needs stronger semantics

Submitting an empty checkout moves focus to the Woo error `<ul>`, and fields receive invalid classes, which is useful. However:

- The error container has no `role="alert"` or asserted live-region semantics.
- Individual controls do not consistently expose `aria-invalid="true"`.
- The Place order button remains enabled even when no payment methods are available; the user only learns through “Invalid payment method” after submit.

**Change:** Add alert semantics to the error summary, associate/reflect invalid states where Woo permits, and disable or replace Place order with an explanatory unavailable state when no gateway exists.

### A8. Reduced-motion mode still runs global motion

With reduced motion requested, the audit still finds the body entry animation (`0.35s`) and many 0.15–0.3s transitions. Existing media queries cover selected components only.

**Change:** Add one global reduced-motion safety rule that removes nonessential animation/transition and smooth scrolling, while preserving state changes without motion.

### A9. Many secondary controls are below the preferred 44×44 touch size

No homepage/shop/compare control is below 24 px, but many header, breadcrumb, inline utility, FAQ, and footer links are under 44 px in at least one viewport.

**Change:** Prioritize 44 px for primary controls, menu actions, quantity controls, checkout actions, and icon-only controls. Use padded inline link groups where a 44 px line box is practical without harming reading flow.

### A10. Small typography weakens readability

Shop, compare, PDP, and checkout CSS contains repeated 0.68–0.78 rem labels. They are often uppercase with tracking, increasing recognition cost on mobile.

**Change:** Establish a 0.8 rem floor for functional labels and 0.9–1 rem for instructional/transactional text; retain smaller type only for genuinely tertiary metadata.

## Passed checks

- All scoped routes reflow at 320 CSS px without horizontal scrolling.
- All route variants expose one main landmark.
- Non-cart routes have one H1 and no heading-level skips in the automated baseline.
- Visible form inputs have programmatic labels.
- Images have alt attributes.
- No route emits console errors during the audit.
- Mobile navigation links themselves have generous target sizes and readable labels.
- Conservative computed-color checks found no simple solid-background contrast failure. Gradient-backed controls require visual/manual review after changes because computed CSS alone cannot derive the rendered gradient pixel behind text.
- 15P preview remains non-purchasable and visibly TBC.

## Manual verification after implementation

1. Tab from the address bar; confirm Skip to main is first and moves focus to the main landmark.
2. At 390 px, open the menu with Enter and Space; Tab/Shift+Tab through it; confirm no background focus; close with Escape; confirm toggle focus returns.
3. Navigate all product and commerce controls with keyboard; verify every focus indicator remains visible and unobscured.
4. Change cart quantity and remove an item using keyboard only; confirm status feedback is announced once.
5. Submit checkout empty; confirm one alert summary, visible field errors, logical focus, and no overlay obscures a field.
6. Emulate reduced motion; verify no entrance/scroll animation while all state changes still work.
7. Test at 200% browser zoom and 320 CSS px; confirm no two-dimensional scrolling or clipped labels.
8. Review text/button contrast on rendered gradients and disabled/error states.
