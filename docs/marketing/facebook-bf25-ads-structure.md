# Facebook Ads Manager Build – Black Friday 2025

**Last updated**: November 2025  
**Owner**: Paid Social Manager  
**Objective**: Sales (Conversion) using Advantage Campaign Budget (ACB)  

---

## 1. Campaign Overview
- **Campaign Name**: `BF25 | SVICLOUD | FB+IG | Sales`
- **Objective**: Sales (Website) with Conversion location set to website.
- **Pixel Event**: `Purchase` (optimize for value). Backup A/B with `AddToCart` if limited volume early.
- **Budget**: $3,500/day (adjustable) with ACB on.
- **Attribution**: 7-day click / 1-day view.
- **A/B Tests**: 
  - Test 1: Percentage vs Dollar-off messaging (split by ad set duplication).
  - Test 2: Countdown urgency vs social proof angle in retargeting.

---

## 2. Ad Set Structure

| Ad Set | Audience | Budget Split | Placement | Bid Strategy | Notes |
|--------|----------|--------------|-----------|--------------|-------|
| AS1 – Prospecting Broad | Broad (US/CA, ages 28-64, English/Chinese). Exclude purchasers last 30d. | 30% | Advantage+ placements | Lowest cost | Use Advantage+ creative if possible; hero videos + carousels. |
| AS2 – LAL 1% LTV | 1% lookalike from top 10% LTV + concierge users. | 20% | Advantage+ placements | Lowest cost w/ cost cap $40 fallback | Duplicate for 2% LAL if volume good. |
| AS3 – Interest Stack | Interests: “Cord-cutting”, “IPTV”, “International TV”, “Expats”, “Streaming media player”. Layer with engaged shoppers. | 10% | FB Feed, IG Feed, Reels, Stories | Lowest cost | Use static proof + carousel mix. |
| AS4 – Retargeting 30d | ViewContent + AddToCart + 75% video viewers (30d). Exclude purchasers. | 20% | FB Feed, IG Feed, Stories, Reels, Audience Network off | Lowest cost with bid cap $40 | Creative: testimonials, countdown stinger. Frequency cap 2/day. |
| AS5 – Cart Abandon 7d | Initiate Checkout/AddToCart past 7d. | 10% | FB + IG Feed, Messenger placements | Cost cap $35 | Use DPA templates if product catalog ready; otherwise static with code reminder. |
| AS6 – Loyalty / CRM | Custom list of past buyers + email leads (exclude recent purchasers <30d). | 10% | Feed + Stories | Lowest cost | Promote gifting bundles, accessories, referral CTA. |

Total adds up 100%. Adjust retargeting share up to 40% during final 48 hours.

---

## 3. Creative Mapping

| Creative ID | Asset | Assigned Ad Sets |
|-------------|-------|------------------|
| `VID15-A` | 15s feature montage | AS1, AS2 |
| `VID10-T` | 10s testimonial | AS1, AS4 |
| `STNGR6-U` | 6s urgency stinger | AS4, AS5 |
| `CAR-20` | Carousel (4 cards) | AS1, AS2, AS3 |
| `STATIC-SP` | Social proof static | AS3, AS4 |
| `STORY-SEQ` | Story 3-frame | AS1, AS4 |
| `LOYAL-GIFT` | Loyalty static (gift angle) | AS6 |

Each creative tagged with UTMs: `utm_campaign=bf25-fb-20off&utm_content=<creative-id>`.

---

## 4. Bidding & Budget Automation
- Start with lowest-cost; if CPA exceeds $40 for 12 consecutive hours, trigger rule to duplicate ad set with cost cap `$40`.
- Set automated rule: if ROAS >5 for 12h and spend >$200, increase ad set budget 20% (max 2 times/day).
- Pause rule: if frequency >6 and CTR <0.8% over last 3 days, pause affected creative.

---

## 5. Naming Convention
`<Campaign> | <Objective> | <Audience> | <Offer>`  
Example ad set: `BF25 | Sales | LAL1% LTV | UpTo20`.  
Ad names include creative + copy angle: `VID15-A | Authorized Dealer | EN`.

---

## 6. Measurement & Reporting
- Daily pivots by: audience, placement, creative ID, language.
- Compare ROAS vs GA4 eCommerce revenue for `/black-friday`.
- Track email sign-ups attributed to ads via UTMs + Klaviyo hidden fields (`utm_campaign`, `utm_source`).
- Document findings in shared Looker Studio template (see Reporting doc).

---

## 7. Launch Checklist
1. Upload all creatives with captions + thumbnails.
2. Set dynamic UTMs via “URL Parameters” field:  
   `utm_source=facebook&utm_medium=paid_social&utm_campaign=bf25-fb-20off&utm_content={{ad.name}}`
3. Verify pixel firing (Purchase / AddToCart) via Test Events.
4. QA ads in preview links across placements (Feed/Reels/Stories).
5. Submit for Meta review by Nov 17 to allow buffer.

