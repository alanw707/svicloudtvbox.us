# SVICLOUD Storefront

The SVICLOUD storefront context describes the customer-facing commerce language for promotions, products, and purchase flows.

## Language

**Coupon Promotion**:
A limited-time offer redeemed by entering a coupon code during checkout.
_Avoid_: Sale, discount, deal

**Product Model**:
A named SVICLOUD device variant sold in the storefront.
_Avoid_: SKU, item, box

**Promotion Window**:
The calendar period during which a Coupon Promotion can be redeemed.
_Avoid_: Weekend, campaign dates, sale period

**Promotion Surface**:
A customer-facing storefront location where a Coupon Promotion is advertised.
_Avoid_: Ad slot, promo area, placement

**Merchant Promotion**:
A Google Merchant Center listing that advertises a Coupon Promotion in shopping surfaces.
_Avoid_: Google ad, feed discount, GMC deal

**Localized Promotion Copy**:
Customer-facing Coupon Promotion wording written for each storefront language.
_Avoid_: Translation string, copy variant, banner text

## Relationships

- A **Coupon Promotion** has exactly one public coupon code.
- A **Coupon Promotion** may apply different discount rates to different **Product Models**.
- A **Coupon Promotion** can discount multiple eligible **Product Models** in the same customer cart.
- A **Coupon Promotion** may be limited to one redemption per customer.
- A **Coupon Promotion** has exactly one **Promotion Window**.
- A **Coupon Promotion** appears on one or more **Promotion Surfaces** and is redeemed during checkout.
- A **Merchant Promotion** may advertise only one featured **Product Model** even when the related **Coupon Promotion** covers more than one model.
- A **Coupon Promotion** has **Localized Promotion Copy** for each supported storefront language.

## Example dialogue

> **Dev:** "Should the Father's Day offer change product prices everywhere?"
> **Domain expert:** "No — it is a **Coupon Promotion**, so shoppers use the code at checkout."

## Flagged ambiguities

- "Father's Day promotion" was used broadly; resolved: it means a **Coupon Promotion**, not a permanent price change.
- "One code" was clarified to mean one public coupon code with model-specific discount rates, not separate model-specific codes.
- "Father's Day weekend" was clarified to include Monday, not only Saturday and Sunday.
- "Advertise it on the website" was clarified to require a sitewide top banner as the primary **Promotion Surface**.
- "Add the promotion in Google Merchant Center" was clarified to mean one **Merchant Promotion**; if one discount must be featured, feature the 10S discount.
