# `/black-friday` Landing Page & Promo QA Plan (2025)

**Last updated**: November 2025  
**Owner**: Growth & Web Experience  
**URL**: `/black-friday` (WordPress page template)  

---

## 1. Page Objectives
- Convert paid social + email traffic during Nov 21–Dec 2 window.
- Communicate “Up to 20% Off” hero offer, highlighting 10P Plus discount + concierge benefits.
- Capture hesitant visitors via modal/form (10% voucher) for December remarketing.
- Reinforce trust with testimonials, comparison tables, FAQ, and guarantee badges.

---

## 2. Content & Module Requirements

| Order | Module | Key Requirements |
|-------|--------|------------------|
| 01 | **Hero** | Headline: “Black Friday: Up to 20% Off SVICLOUD Bundles.” Subhead: “Save on SVICLOUD 10P Plus + get lifetime concierge support through Cyber Monday.” CTA buttons (“Shop 10P Plus,” “See 10S Offer”). Include countdown timer synced to promo end (Dec 2 23:59 PT). |
| 02 | **Offer Tiles** | Two cards: (a) 10P Plus 20% off + free 3-month premium channel pack/free shipping. (b) 10S 10% off positioned as “From $165 with bonus accessories.” Each shows strike-through price, savings badge, CTA linking to WooCommerce product with querystring `?coupon=BF25-20` or `?coupon=BF25-10`. |
| 03 | **Value Stack** | 3-column icons: “1,000+ Channels,” “4K Ready,” “Lifetime Concierge.” Keep copy under 16 words per column. |
| 04 | **Comparison Table** | Highlight differences between 10P Plus vs 10S vs competitors (Evpad/Unblock) focusing on warranty, support, and price after discount. |
| 05 | **Testimonials** | Carousel or 3 cards with 4.8⭐ badge, quotes, names/cities. Call-out: “Verified US Customers.” |
| 06 | **FAQ Accordion** | Questions: shipping timelines, how to redeem concierge, when sale ends, return policy. Include schema markup if feasible. |
| 07 | **Lead Capture Section** | Gradient block with copy “Not ready yet? Join for a 10% voucher + early shipping updates.” Form should post to Klaviyo list `BF25-WarmList`. Auto-send coupon code `BF25-HOLD`. |
| 08 | **Guarantee / Support Strip** | Icons for “Authorized SVICLOUD Dealer,” “1-Year Warranty,” “US-Based Concierge.” |
| 09 | **Final CTA** | Sticky footer CTA on mobile: “Claim Up to 20% Off” linking back to hero anchor to encourage conversions. |

Design notes: reuse `front-page` typography, but shift palette to dark background (#0B0B0E) with neon red (#F93822) accents; integrate subtle animated particles behind countdown for premium feel.

---

## 3. Technical Implementation Notes
- **Template**: extend `page-front-page.php` pattern or create `page-black-friday.php` using existing section partials under `theme/svicloudtvbox-lumen/template-parts/`.
- **CSS**: add new partial `assets/css/parts/55-black-friday.css` with hero, timer, offer tiles, etc., and register in `bundles.json` under `front-page.css`.
- **Countdown**: vanilla JS module hooking into data attribute `data-sale-end="2025-12-02T23:59:59-08:00"`. Fallback static copy if JS disabled.
- **Forms**: use existing gravity/CF7 integration or embed Klaviyo form; ensure double opt-in toggle.
- **UTMs**: Append UTMs when linking to WooCommerce products (inherit `window.location.search` when CTA clicked to preserve source).
- **Localization**: Provide optional zh-hans translation toggle if `?lang=zh` present (copy stored in JSON for quick swap).

---

## 4. Promo Code & Pricing QA

| Code | Applies To | Discount | Notes |
|------|------------|----------|-------|
| `BF25-20` | SVICLOUD 10P Plus + bundles | 20% off + free premium channel pack (auto-added bonus product) | Ensure rule scoped to 10P/10P bundle skus only |
| `BF25-10` | SVICLOUD 10S | 10% off | Validate it cannot stack with subscription coupons |
| `BF25-HOLD` | Email capture follow-up | 10% off any device (single-use) | Tied to Klaviyo automation |

**QA Checklist**
1. Confirm WooCommerce coupons set with correct usage limits (per user + total) and validity dates (Nov 21–Dec 2 PT).
2. Test `?coupon=` query parameter auto-applying code when user lands on PDP.
3. Validate cart summary shows both percentage discount and free premium pack line item.
4. Regression test shipping/tax calculations after applying discounts.
5. Verify translation/RTL display for hero copy (if zh toggle enabled).
6. Smoke test on top devices (iPhone 14, Pixel 8, iPad, Desktop Safari/Chrome).
7. Confirm countdown reaches zero and triggers final-state copy (“Sale has ended—join waitlist”).

---

## 5. QA Runbook
1. Deploy staging build to Docker WP instance.
2. Run `python3 scripts/build_css.py --theme svicloudtvbox-lumen --bundle front-page --pretty` then `./scripts/sync_theme_container.sh`.
3. Visit staging `/black-friday?utm_source=qa&utm_medium=internal` and record Lighthouse + Web Vitals.
4. Capture before/after screenshots (desktop + mobile) for hero, offer tiles, FAQ, and CTA.
5. Log results + issues in Notion “BF25 Landing QA” board; assign fixes.

---

## 6. Content Owners & Deadlines
| Item | Owner | Due |
|------|-------|-----|
| Final copy deck (EN) | Growth copywriter | Nov 12 |
| zh translation | Localization partner | Nov 14 |
| Hero/offer art direction | Creative director | Nov 13 |
| Dev implementation | Web engineering | Nov 16 |
| QA pass + bugfix | QA lead | Nov 18 |

---

## 7. Open Questions
- Do we allow Klarna/affirm callouts on this page? (Legal review pending)
- Should returning customers see personalized pricing via cookie? evaluate scope.
- Need final decision on email form incentive (10% voucher vs early shipping).

