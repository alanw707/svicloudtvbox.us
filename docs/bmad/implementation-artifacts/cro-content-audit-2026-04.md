# Homepage Content Audit — svicloudtvbox.us

**Date:** 2026-04-06
**Source:** Live page (`https://svicloudtvbox.us/`) + theme code review
**Companion to:** `cro-plan-2026-04.md`

---

## 🔴 Critical bugs (broken right now)

### B1. `sr_sale_announcement` literal in screen-reader text (a11y bug, not visible)
- **Status:** ✅ Fixed in `inc/helpers-svic.php:1056` (2026-04-06)
- **Where:** Pricing section, both 10P+ and 10S cards (and any sale price rendered via `svic_price_html()`)
- **Symptom:** Screen-reader users heard `"sr sale announcement"` instead of `"Sale price $269.00, original price $369.99"`. Sighted users were unaffected — the leak was inside `<span class="screen-reader-text">` (visually hidden via CSS).
- **Root cause:** `svic_translate('pricing.sr_sale_announcement')` queried the wrong key path. Actual key lives at `frontpage.pricing.sr_sale_announcement` (`en_US.php:880-883`). Lookup miss → translator returned the unmatched key tail → rendered literally inside the `screen-reader-text` span. The `sprintf` logic itself was correct.
- **Fix:** Changed lookup to `frontpage.pricing.sr_sale_announcement`. Single-line edit.
- **Note:** Initial audit framed this as a visible CRO bug — incorrect. It is an accessibility-only bug. Reclassified accordingly.

### B2. Pricing card CTA is "View 10P+" instead of "Add to Cart"
- **Where:** Pricing section CTAs
- **Symptom:** Visitor sees price → clicks button → lands on PDP → must click again to add to cart
- **Conflict with intent:** Original CRO recommendation #2 was "price + Add to Cart" specifically to remove the second click. Currently only half-shipped.
- **Fix options:**
  - (a) Replace with Ajax add-to-cart button using WC's `?add-to-cart=ID`
  - (b) Relabel "Buy Now" and link to `/checkout/?add-to-cart=ID` for one-click intent
- **Impact:** Every extra click loses ~20% of intent

### B3. Sale prices shown without savings callout
- **Symptom:** `$269.00` next to `$369.99` — no "$100 off" or "27% off" badge
- **Fix:** Compute savings server-side, render as a third element ("Save $100" or "27% off")
- **Impact:** Crossed-out price + explicit savings significantly outperforms crossed-out alone

---

## 🟠 High-impact content gaps

### C1. Zero social proof on the homepage
- **Missing:** customer count, star ratings, testimonials, customer logos, press mentions
- **Why this matters:** $269 product, brand most U.S. buyers have never heard of → without proof every visitor takes you on faith
- **Fix (Phase 1, no GBP needed):** 3-4 hand-curated quotes with first name + city in a `testimonials` section
- **Fix (Phase 2):** GBP review fetcher + flip on `svic_organization_aggregate_rating` filter (`functions.php:2112`)

### C2. H1 is generic and keyword-stuffed
- **Current:** *"Best Android TV box, global entertainment ready for any TV"*
- **Problems:** 2012-era SEO; doesn't say *who* it's for or *why* different
- **Options:**
  - Benefit-first: *"Watch 1000+ Chinese & Asian Channels in 4K — Ships from Nevada, Bilingual Support, 1-Year Warranty"*
  - Audience-first: *"The Streaming Box Built for U.S. Chinese Families — Ships in 48 Hours from Nevada"*
- **Files:** `lang/en_US.php:708`, `lang/zh_TW.php`, `lang/zh_CN.php`

### C3. Sub-headline H2 is descriptive, not promise-driven
- **Current:** *"Bilingual, U.S.-stock 10P+ with concierge and warranty"*
- **Suggested:** *"Skip the import hassle and language barriers. Get a fully-set-up SVICLOUD shipped from Nevada with English & 中文 help on speed-dial."*

### C4. FAQ is too short — only 4 questions in 2 groups
**Missing high-intent questions:**
- "What channels / apps come pre-loaded?" (highest search-intent for this category)
- "How is SVICLOUD different from EVPAD / UnblockTech / UBOX?" (you have a comparison page — pull excerpts)
- "What payment methods do you accept?"
- "Do I need a VPN?"
- "What happens after the 1-year warranty?"
- "Can I get an invoice / receipt for resale?"
- **Bonus:** more FAQ entries = more SERP real estate (FAQPage schema already wired)

---

## 🟡 Medium impact

### M1. No "What's in the box" section
Buyers want tangibility. Data already exists in `hero.card.specs`. One image + 6 bullets.

### M2. Hero specs panel is too cold / enthusiast-coded
"Amlogic S928X · Octa-core / Wi-Fi 6 / AV1 decode" — accurate but speaks to spec geeks. Most buyers care about outcomes (no buffering, works with my TV/apps). Either translate to plain English or move down-page and lead with an outcomes panel.

### M3. Only 2 above-the-fold CTAs, both soft
- "Shop 10P+" + "Compare Models"
- "Compare Models" delays purchase. Replace with **price anchor**: "From $209" → jump to pricing section. Or "See Pricing" anchor link.

### M4. No urgency / scarcity
- "Ships from Nevada within 48 hours" exists as a feature bullet, not a deadline
- Options: live "order in next 4 hours, ships today" countdown; "Last 12 of December batch" (only if true); louder "Most Popular" badge on 10P+

### M5. Authorization certificate section is visually heavy
The SVI.STUDIO Authorized Dealer block dedicates a lot of real estate to a single trust signal. Compress to small badge + "View Certificate" link; reclaim space for testimonials.

### M6. "Talk to concierge" / "Visit support & FAQ" CTAs are escape hatches
Both pull visitors *away* from purchase. They have nearly equal visual weight to buy CTAs. Useful for hesitant buyers but should be visually demoted.

---

## 🟢 Quick polish

### P1. Hero card timestamp is stale
> *"Latest U.S. batch · July 2024"* — today is 2026-04-06 → looks like 21-month-old inventory. Update or remove.
- **File:** `lang/en_US.php:726`

### P2. `&#36; 269.00` has literal space between `$` and number
- HTML entity + whitespace renders as `$ 269.00`. Should be `$269.00`.

### P3. Mobile sticky buy bar?
PDP has one (`single-product.html`). Homepage probably doesn't — mobile users scrolling past hero would benefit. Need to verify.

---

## Suggested execution order

**Sprint A — Stop the bleeding (theme bugs)**
- B1. Fix `sr_sale_announcement` rendering
- B2. Pricing card CTA → real Add to Cart / Buy Now
- B3. Add savings callout
- P1. Fix stale July 2024 timestamp
- P2. Fix `$ 269.00` spacing

**Sprint B — Copy rewrites**
- C2. H1 (en_US, zh_TW, zh_CN)
- C3. H2 sub-headline
- M2. Hero specs panel → outcome-led copy

**Sprint C — Trust + content depth**
- C1 Phase 1. Testimonial section with 3-4 hand-curated quotes
- C4. Expand FAQ from 4 → 10 questions
- M1. "What's in the box" section
- M5. Compress certificate block

**Sprint D — Funnel sharpening**
- M3. Replace "Compare Models" CTA with price anchor
- M4. Urgency / scarcity copy
- M6. Demote escape-hatch CTAs visually
- P3. Verify/add mobile sticky buy bar

**Sprint E — Reviews integration (depends on GBP)**
- C1 Phase 2. GBP fetcher + flip on `AggregateRating`
