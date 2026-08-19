# Storefront visual and UX review

Reviewed from the local desktop/mobile captures in `docs/frontend-audit/before/` using the existing dark/teal brand direction.

## Resolution status

| Finding | Resolution |
|---|---|
| D1 unfinished cart | Fixed with the maintained branded cart template, one clear checkout action, compact summary, responsive item card/table, and real product image fallback. |
| D2 overlong homepage | Fixed by removing duplicate metrics/feature/experience/inline-CTA blocks, reducing hero choices, and removing fragile content containment. Height fell 27% desktop and 22% mobile. |
| D3 checkout completion friction | Fixed: one inline notice, unique coupon UI, summary before billing/payment on mobile, themed Select2, and explicit no-payment state. |
| D4 orphan shop card | Fixed: balanced three-column catalog at wide desktop, two-column tablet, one-column mobile; prelaunch remains visually distinct. |
| D5 repetitive PDP controls/hierarchy | Improved with tighter traffic/FAQ/comparison rhythm, 44 px quantity control, readable functional labels, and consistent utility-link targets/focus. |
| D6 dense comparison | Improved with reduced section gaps, tighter traffic/FAQ modules, and larger functional model labels while retaining all buyer facts. |
| D7 inconsistent states/type | Fixed through shared 44 px controls, 0.8 rem label token, universal focus ring, reduced-motion state, current mobile-nav treatment, inline notices, and disabled payment state. |

All 16 before/after route screenshots have different SHA-256 hashes. Final evidence is in `docs/frontend-audit/after/`.

## Original top issues

### D1. Cart appears unfinished and breaks the brand/component system — critical

**Routes:** `/cart/` desktop and mobile

- No page title or introductory hierarchy.
- Product, quantity, removal, totals, coupon, and checkout action appear as bare Woo Block UI.
- The checkout link looks like an inline text link rather than the primary next step.
- Product imagery falls back to an unhelpful generic placeholder.
- Desktop wastes most of the canvas; mobile has large dead gaps between the item and totals.

**UX consequence:** The highest-intent step looks less trustworthy than the marketing pages.

**Acceptance:** Use the existing custom cart shell with a clear H1, grouped item card/table, visible quantity controls, explicit primary checkout button, compact coupon disclosure, responsive summary, and useful product fallback image.

### D2. Homepage is dramatically overlong and repeats the same decision — high

**Route:** `/`

- 11,141 px desktop / 17,374 px mobile.
- The hero exposes three competing actions plus another two actions inside the right panel.
- Product availability, box contents, certification, metrics, feature grid, experience, pricing, confidence, and FAQ repeat shipping/support/value messages.
- Later sections have weak novelty and no strong narrative reset.
- `content-visibility:auto` creates blank regions in full-page evidence because intrinsic sizes are far below final section heights.

**UX consequence:** The visitor must traverse many similar panels before reaching a decision, especially on mobile.

**Acceptance:** Establish one primary 15P preview action and one current-model action; preserve essential trust and product choice, but tighten section padding, merge duplicate confidence content, and remove fragile section containment.

### D3. Checkout feedback and mobile sequence undermine completion — critical

**Route:** `/checkout/`

- A thin server notice and floating duplicate toast show the same added-to-cart message.
- The toast obscures form fields on mobile.
- Desktop hierarchy is generally polished, but native/select states do not match consistently.
- On mobile, Payment & confirmation/Place order appear before Order summary and totals.
- Place order still appears actionable when the environment has no payment method.

**UX consequence:** Users can act before reviewing the order, receive duplicate feedback, and hit an avoidable payment error.

**Acceptance:** One non-obscuring notice; summary/total before final confirmation on mobile; consistent inputs/selects/errors; unavailable payment state explained before action.

### D4. Shop layout makes the range harder to compare — high

**Route:** `/shop/`

- Two columns at 1440 px leave 10S isolated on a second row.
- Cards are very tall, and feature/assurance lists visually compete with the title/price/CTA.
- The 15P preview is correctly safe but visually similar to purchasable cards despite being informational.
- Large empty right-side space makes the catalog look incomplete.

**Acceptance:** Three balanced columns on wide screens, clear prelaunch visual treatment, consistent card heights/CTA placement, tighter supporting detail, and one-column mobile flow.

### D5. Product pages repeat confidence modules and dilute the purchase/preview decision — high

**Routes:** 15P, 10P+, 9P PDPs

- Hero, description, launch/funnel panel, box contents, support panel, FAQ, reviews, and footer repeatedly restate support/warranty/shipping.
- Hero panels are tall; on desktop the media column leaves dead space when details are longer.
- On mobile, every panel becomes a full-width card with nearly identical radius/border treatment, flattening hierarchy.
- Utility links and quantity controls are visually too small.
- 15P’s non-sale state is accurate but could communicate “preview, no purchase” sooner and more compactly.

**Acceptance:** Strong single decision at top, smaller secondary utility links with adequate hit areas, less repetitive support copy, varied section hierarchy, and consistent purchasable vs preview states.

### D6. Comparison page is comprehensive but hard to scan — high

**Route:** `/compare/`

- 7,580 px desktop / 11,188 px mobile.
- Hero, decision strip, archetype cards, FAQ, two full product cards, feature lists, confidence grid, how-to-order, and final CTA create repeated choice prompts.
- On mobile, the two product cards become long consecutive documents rather than a quick comparison.
- Tiny model labels and dense all-caps metadata reduce readability.

**Acceptance:** Keep one concise recommendation area, make core differences scannable before long product cards, tighten duplicated CTA/support modules, and preserve mobile access to equivalent facts without a 11k-pixel decision path.

### D7. Functional typography and states are inconsistent — moderate

**Global**

- Functional labels repeatedly use 0.68–0.78 rem uppercase text.
- Primary actions vary between bright filled pills, outlined pills, bare text links, and Woo defaults.
- Current-page navigation state is not visually obvious.
- Some inline links have no visible keyboard focus change.
- Disabled, unavailable, validation, and loading states are not presented as one coherent system.

**Acceptance:** Define a functional type floor, shared focus ring, minimum target sizes, consistent primary/secondary/text action hierarchy, current nav state, and explicit unavailable/error states.

## Quick wins

1. Add skip link, shared focus ring, and global reduced-motion rule.
2. Restore the custom cart template instead of styling a second cart system.
3. Prevent duplicate checkout toast and duplicate coupon ID.
4. Use three shop columns above a wide-screen breakpoint.
5. Remove homepage `content-visibility` containment and tighten section spacing.
6. Increase inline utility hit areas and quantity input height.
7. Give current desktop/mobile nav links an `aria-current` state and visible treatment.
8. Raise functional labels to at least 0.8 rem and instructional text to 0.9 rem.

## Visual polish direction

- Keep the midnight/teal palette, but reduce the number of panels using the same bright-blue fill. Reserve brighter surfaces for the next decision.
- Use one border opacity/radius scale for cards, one for compact controls, and one shadow strength for elevated/sticky panels.
- Increase body copy line-height and constrain reading width; reduce excessive letter spacing on small uppercase labels.
- Use subtle section backgrounds and spacing changes to establish rhythm rather than wrapping every block in another outlined card.
- Align CTA baselines and card footers so comparison does not require eye zig-zagging.
- Keep high-salience teal for primary actions, focus, and selected states—not every decorative dot and border.

## Responsive fixes

### Navigation
- Treat expanded mobile navigation as a modal surface with scroll lock and focus containment.
- Preserve the large existing menu targets; add current-page state and safe-area/bottom spacing.

### Homepage
- Reduce mobile hero actions to the primary preview and one current-model alternative.
- Tighten mobile section padding and remove duplicate confidence/feature statements.
- Ensure full content remains available without paint containment artifacts.

### Shop
- Wide desktop: three columns.
- Tablet: two columns with centered final card if needed.
- Mobile: one column, reduced media height, consistent CTA position.

### Compare
- Put the concise “which model” answer before extensive detail.
- Use compact difference rows/cards on mobile rather than requiring full-card comparison first.
- Keep sticky/final CTAs from duplicating each other or obscuring content.

### PDP
- Avoid stretching the media column to the taller information column.
- Keep the decision panel, price/state, and CTA in the first viewport where practical.
- Make accordion and utility targets comfortably tappable.

### Cart
- Stack product details cleanly, keep quantity/remove adjacent to the product, then place totals and checkout without large dead gaps.
- Make checkout the unmistakable primary action.

### Checkout
- Put summary/total before final payment action on mobile.
- Avoid fixed feedback over fields.
- Keep field labels and errors readable without relying on placeholder text.

## Implementation notes

- Fix shared foundations in global partials instead of page-local overrides: skip link, focus, target size, reduced motion, nav state.
- Keep product/shop card fixes in `43-shop-product-card.css`; homepage rhythm in existing `44–50` partials; cart in `66-woocommerce-cart.css`; PDP in `70-lumen-woocommerce.css`/`70a-pdp-compare.css`; checkout in `71*` partials.
- Reuse the existing classic `woocommerce/cart/cart.php`; do not maintain both Block Cart and classic cart presentation.
- Preserve PHP template semantics and Woo hooks while changing layout.
- Add Playwright safeguards for information order, duplicate IDs/notices, menu keyboard behavior, focus visibility, H1 count, target size, 320 px overflow, reduced motion, and TBC safety.
