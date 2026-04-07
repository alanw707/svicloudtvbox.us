# CRO Plan — svicloudtvbox.us

**Created:** 2026-04-06
**Source:** Homepage CRO audit (prior session, 2026-04-06) + verification pass against current code & live site
**Goal:** Move site from "doing OK" to next-level conversion rate.

---

## Shipped

Commit `5f94fc9` — `feat(cro): pricing section, inline CTAs, FAQPage + BreadcrumbList schema`

- [x] **#2** Show price + Add to Cart on homepage (pricing cards)
- [x] **#4** FAQPage JSON-LD schema on homepage
- [x] BreadcrumbList JSON-LD schema site-wide
- [x] **#5** Inline CTA strips after certification + feature grid
- [x] **#6/#13** Trust badges: free shipping, 48hr dispatch, 30-day returns
- [x] PostalAddress schema cleanup (city/state only)

---

## Verified status (2026-04-06)

### Theme-code items

| # | Item | Status | Notes |
|---|---|---|---|
| 1 | Customer reviews / social proof | **Scaffold only** | `functions.php:2109` has `AggregateRating` schema gated behind `svic_organization_aggregate_rating` filter — disabled by default. **No testimonial UI section in any template. No GBP fetcher.** Needs (a) testimonial UI, (b) GBP integration or manual data pipe, (c) flip filter on. |
| 8 | H1 rewrite | **Not done** | `lang/en_US.php:708` still `"Best Android TV box, global entertainment ready for any TV"`. Needs zh_TW + zh_CN parallel updates. |
| 7 | Email capture | **Not done** | No newsletter/subscribe code in theme. |
| 12 | `theme-color` meta | **Not done** | Not in `<head>`. Trivial add. |

### WP-admin / plugin items (NOT theme code)

| # | Item | Where | Notes |
|---|---|---|---|
| 3 | Remove AdSense from homepage | Site Kit plugin | Live page serves `pagead2.googlesyndication.com/.../adsbygoogle.js`. **AdSense ≠ Google Ads.** AdSense places third-party ads on YOUR site — clicks send your visitors to competitors and earn pennies. On a commerce homepage this directly cannibalizes conversions. Fix: Site Kit → AdSense → disable site-wide (or at minimum exclude front page + product pages). |
| 9 | Clean up navigation | Appearance → Menus | Two blog post titles are top-level menu items: `SVICLOUD 10P+ 2026新款上市` and `SVICLOUD 美國本地服務｜48小時快速出貨與雙語客服支援`. Move under Blog dropdown. |
| 10 | Consolidate Google scripts | Site Kit plugin | Site Kit injects GA4 + AdSense as separate tags. Lower priority once AdSense is gone. |

### Tracking — important correction

| # | Item | Status | Notes |
|---|---|---|---|
| 11 | GA4 / Meta Pixel | **Re-scoped — Meta Pixel dropped** | GA4 (`G-25RK4LK4DH`) is **already firing via Site Kit** on the live site → no gap. **Meta Pixel removed from plan** (user not selling/advertising on Meta). The empty `SVIC_GA4_MEASUREMENT_ID` and `SVIC_META_PIXEL_ID` constants are a **dead parallel tracking path** → delete them. |
| — | Google Ads conversion tag (`AW-17655850932`) | **Pending decision** | Hard-coded in `functions.php:63` AND injected by "Google for WooCommerce" plugin. **User not running Google Ads campaigns** → safe to remove from both places. Removal: (a) delete `SVIC_GOOGLE_ADS_CONVERSION_ID` constant + dependent code in theme; (b) WooCommerce → Google for WooCommerce → disconnect/remove conversion tracking. |

---

## Revised priority order

### High impact, theme-code

1. **#1 Testimonial UI section + flip on AggregateRating** (manual data first, GBP integration later). Biggest remaining gap. Two-phase:
   - Phase 1: Static testimonial section on homepage with 3-4 real customer quotes (name + city). Flip `svic_organization_aggregate_rating` filter on with hand-curated count.
   - Phase 2: GBP fetcher with caching (after GBP is verified).
2. **Tracking cleanup** — delete dead `SVIC_GA4_MEASUREMENT_ID`, `SVIC_META_PIXEL_ID`, and `SVIC_GOOGLE_ADS_CONVERSION_ID` constants and any code paths that depend on them. Pending: confirm no active Google Ads campaigns before removing `AW-…`.
3. **#7 Email capture** — footer signup form. Pick a provider (Mailchimp/Brevo/native).

### Quick wins, theme-code

4. **#8 H1 rewrite** — update `lang/en_US.php`, `lang/zh_TW.php`, `lang/zh_CN.php`.
5. **#12 `theme-color` meta** — one-line `wp_head` add.

### WP-admin (no code)

6. **#3 Disable AdSense on homepage** (Site Kit settings).
7. **#9 Move blog posts out of top-level nav** (Appearance → Menus).

### Deferred / needs decision

8. **#10 Consolidate Google scripts** — non-trivial; requires moving tracking out of Site Kit. Defer until #11 is decided.

---

## Suggested next sprint

**Sprint A (theme code, one PR):** #8 H1 rewrite + #12 theme-color meta. ~30 min.

**Sprint B (theme code, separate PR):** #1 Phase 1 — static testimonial section + flip on AggregateRating.

**Sprint C (admin work, no code):** #3 disable homepage AdSense + #9 nav cleanup. User does this in WP admin.

**Sprint D (decision + code):** #11 Meta Pixel direction.

**Sprint E:** #7 email capture.

---

## Notes / constraints

- Home-based business → GBP setup uses hidden home address; service-area business model. P.O. boxes and most virtual mailboxes get rejected/suspended by Google.
- Google Merchant Center merchant ID `5317978135` already wired into code → Google Customer Reviews opt-in is the fastest path to a seller-rating badge (separate from GBP reviews).
- Site Kit plugin currently owns GA4, Google Ads, and AdSense injection. Any tracking-consolidation work must account for this.
