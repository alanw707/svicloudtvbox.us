# Google Review Stars Playbook — svicloudtvbox.us

Version 1.0 — Last updated: 2025-10-24  
Owner: Marketing × Web Ops  
Scope: Organic review snippets, Google Business Profile reviews, Ads seller ratings

---

## Outcomes We Want

- Stars under eligible product URLs in organic results (review snippets)
- Visible Google reviews for brand queries via Google Business Profile (GBP)
- Seller Ratings stars on Google Ads/Shopping (once thresholds met)

Note: Google no longer shows “self‑serving” stars for Organization/LocalBusiness schema on your own site. Product pages remain eligible when real reviews are present and visible.

---

## Where Stars Appear

- Organic (Review Snippets): On product detail pages with valid Product structured data + real, on‑page reviews.
- Local/Knowledge Panel (Maps/GBP): Reviews left on our Google Business Profile.
- Ads Seller Ratings (Text Ads/Shopping): Aggregated store‑level ratings from Google Customer Reviews or approved partners.

---

## Organic Review Snippets (Product)

Requirements
- Real reviews visible on the page (do not fabricate or hide).  
- Product JSON‑LD includes: `name`, `image`, `brand`, `sku`, `offers` (`price`, `priceCurrency`, `availability`, `url`), and either `aggregateRating` or individual `review` objects.
- Apply only on product pages; do not add reviews/rating to the homepage or Organization schema.

Implementation (WooCommerce)
1) Enable product reviews: WooCommerce → Settings → Products → “Enable product reviews”.
2) Recommended: install an SEO plugin that outputs Product schema correctly:
   - Rank Math (WooCommerce module) or Yoast SEO + WooCommerce SEO.  
   - Pair with a reviews app (CusRev/Judge.me/Yotpo) to collect/display reviews.
3) Only surface `aggregateRating` after ≥1 real review exists on the page.

Custom JSON‑LD example (use only on PDPs with real reviews)
```
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "SVICLOUD 10P Plus",
  "image": ["https://svicloudtvbox.us/wp-content/uploads/10p-plus.jpg"],
  "brand": {"@type": "Brand", "name": "SVICLOUD"},
  "sku": "10P-PLUS",
  "offers": {
    "@type": "Offer",
    "priceCurrency": "USD",
    "price": "248.99",
    "availability": "https://schema.org/InStock",
    "url": "https://svicloudtvbox.us/product/svicloud-10p-plus/"
  },
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "4.8",
    "reviewCount": "37"
  }
}
```

Validate
- Use Google Rich Results Test and Search Console URL Inspection on 10P/10S pages.  
- Monitor Search Console → Enhancements → Product/Review snippets for warnings.

---

## Google Business Profile (Local Reviews)

Goals
- Show stars on brand/name and map results; improve trust and conversions.

Steps
1) Claim/verify GBP; add categories (e.g., “Electronics store”), hours, phone, photos.  
2) Enable Messaging; add FAQs.  
3) Start a post‑purchase review ask (email/SMS) linking directly to the GBP review form.  
4) Respond to every review; use Q&A for common pre‑purchase questions.

Notes
- GBP stars do not appear as review snippets under organic listings; they appear in Maps/Knowledge Panel.

---

## Ads Seller Ratings (Text Ads/Shopping)

What is required
- Google Customer Reviews (or approved aggregator) collecting seller‑level reviews.  
- Typically 100+ unique reviews in the past 12 months per country with ≥3.5 rating.

Setup
1) In Merchant Center, enable Google Customer Reviews, add the opt‑in module to the order confirmation page.  
2) Or integrate an approved partner (Yotpo/Trustpilot/Stamped) tied to Merchant Center and Ads.  
3) After thresholds are met, seller ratings can show on text ads and Shopping/free listings.

---

## 7‑Day Checklist

- Day 1–2: Enable WooCommerce reviews; install SEO plugin (Rank Math or Yoast Woo); configure Product schema.  
- Day 2–3: Draft and enable review request email/SMS; publish first 3–5 real product reviews.  
- Day 3–4: Validate PDPs in Rich Results Test; fix any missing fields/warnings.  
- Day 4–5: Claim/verify GBP; add categories, photos, Messaging; publish hours.  
- Day 5–7: Enable Google Customer Reviews in Merchant Center (or connect partner).  
- Ongoing: Reply to GBP reviews; monitor Search Console Enhancements; refresh PDP media/copy to improve CTR.

---

## Compliance & Pitfalls

- Do not mark up Organization/LocalBusiness with ratings to get stars—disallowed.  
- Do not include reviews in schema unless they are visible on the page.  
- Keep Product `offers` price/currency synchronized with visible page content.  
- Avoid scraped/3rd‑party review markup (e.g., embedding Google Maps reviews with schema)—Google ignores/penalizes.

---

## Monitoring & Owners

- Marketing: Review rate, CTR, impressions of review snippets, GBP review velocity.  
- Web Ops: Schema validation, Merchant Center GCR integration, PDP parity (content vs. markup).  
- Weekly: Check Search Console Enhancements and Merchant Center diagnostics; log changes in channel reports.

---

## Related Docs

- docs/seo-plan-svicloudtvbox-us.md (Structured Data section)  
- docs/svicloudtvbox-woocommerce-snippets.md (JSON‑LD examples)

