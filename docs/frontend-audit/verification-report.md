# Storefront accessibility and UX verification

## Outcome

The scoped homepage, navigation, shop, compare, 15P/10P+/9P product pages, cart, and checkout were audited and improved locally. Production was not deployed.

## Before/after dimensions

Heights are rendered CSS pixels from `before/audit.json` and `after/audit.json`. A taller commerce page is intentional where bare Woo Blocks were replaced by complete accessible UI.

| Route | Desktop before → after | Mobile before → after | Result |
|---|---:|---:|---|
| Homepage | 11,141 → 8,153 | 17,374 → 13,567 | Duplicate marketing blocks removed; no containment paint gaps. |
| Shop | 3,984 → 2,787 | 5,349 → 5,368 | Three balanced desktop cards; mobile type/targets improved. |
| Compare | 7,580 → 7,358 | 11,188 → 10,963 | Tighter rhythm and readable functional labels. |
| 15P PDP | 5,605 → 5,454 | 8,549 → 8,439 | Tighter comparison/support rhythm; TBC state preserved. |
| 10P+ PDP | 5,639 → 5,517 | 7,815 → 7,687 | 44 px quantity and improved utility/FAQ targets. |
| 9P PDP | 5,372 → 5,244 | 7,165 → 7,016 | Legacy funnel preserved with improved targets/rhythm. |
| Cart | 1,172 → 2,118 | 1,734 → 3,320 | Bare Block Cart replaced by complete semantic/branded cart UI. |
| Checkout | 3,186 → 3,218 | 4,776 → 4,799 | Summary-first mobile flow, unique coupon UI, consistent notices/payment state. |

Every one of the 16 full-page before/after screenshot pairs has a different SHA-256 hash. Viewport and expanded-navigation evidence is stored alongside the full-page captures.

## Accessibility evidence

Final `accessibility-audit.json` covers eight routes at 1440, 390, and 320 CSS pixels:

- 24/24 have exactly one H1 and one main landmark.
- 24/24 have no horizontal overflow.
- Zero duplicate IDs.
- Zero visible unlabeled inputs.
- Zero nonessential active animation/transition under reduced-motion emulation.
- Mobile menu locks scroll, makes the background inert, loops focus, closes with Escape, and returns focus.
- Skip link is first in tab order and focuses `#main-content`.
- Cart quantity/remove controls and primary actions meet 44 px sizing.
- Checkout coupon controls have unique IDs; simulated Woo errors receive `role="alert"` and field `aria-invalid` state.
- No-gateway checkout provides an explanatory disabled `Payment unavailable` action.
- `contrast-audit.json` reports zero conservative solid-background candidates; gradient controls were manually reviewed in final screenshots.

`tests/playwright/frontend-quality.spec.ts` guards these results in Chromium and WebKit, including EN/zh reflow at 320 px.

`/zh/`, `/zh-cn/`, and their cart routes expose localized skip-link, mobile-dialog, open/close, and quantity-control accessible names through `svic_translate`.

## Build and source checks

- All 56 theme PHP files pass `php -l`.
- `git diff --check` passes.
- `python3 scripts/build_css.py --theme svicloudtvbox-lumen` succeeds.
- `python3 scripts/build_js.py --theme svicloudtvbox-lumen` succeeds.
- Generated CSS is reproducible from registered partials.
- Local and container SHA-256 hashes match for `style.css`, `front-page.css`, `compare.css`, and `woocommerce.css`.
- `sync_theme_container.sh svicloud10p` completes with the bind-mounted local theme.

## Browser verification

Final full command: `npm test`

Final result after bilingual accessibility safeguards: **108 passed, 8 declared skips, 0 failed**.

Declared skips are pre-existing environment/content gates:
- Stripe purchase/widget checks require `PLAYWRIGHT_STRIPE_E2E=1` and configured Stripe test credentials.
- Checkout coupon-toggle continuation is absent because the theme exposes the coupon form directly in the order summary.
- One optional blog anchor test skips when its local fixture route is absent.

Strict smoke coverage continues to collect console errors without an ignore list across desktop and mobile routes.

## Finding reconciliation

- Accessibility findings A1–A10: resolved or, for inline prose links, retained under the WCAG inline-target exception. See `accessibility-review.md`.
- Design findings D1–D7: fixed or materially improved within the existing brand/content boundaries. See `design-ux-review.md`.
- 15P remains non-purchasable with visible SPEC/FEATURE/AVAILABILITY/POLICY TBC language; no unconfirmed offer was introduced.
- EN/zh rendering, 9P/10P funnels, WooCommerce behavior, and existing launch safeguards remain covered.

## Commits

- `0ffa9ea fix(theme): establish accessible storefront foundations`
- `c556687 fix(theme): streamline storefront discovery UX`
- `251128c fix(woocommerce): repair cart and checkout UX`
- `a554611 test(theme): verify storefront accessibility UX`
- Bilingual accessibility correction commit follows this report.
