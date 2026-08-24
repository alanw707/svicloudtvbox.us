# SVICLOUD 15P marketing image v4

**Method:** Codex built-in `image_gen` via OpenAI's official `imagegen` skill.

**2026-08-24 inventory correction:** After 15P stock arrived, the shipped WebP was patched to replace the original `COMING SOON` badge with `IN STOCK NOW`. Keep the current in-stock badge for any future regeneration.

**Reference roles:**

- Previous 15P marketing graphic: visual direction and redesign target (intermediate removed after v4 approval)
- `assets/images/products/svicloud-15p-front.webp`: authoritative watermarked product identity
- `assets/images/products/svicloud-15p-angle.webp`: supporting watermarked product geometry

## Generation instruction

```text
Create a 3:2 landscape ecommerce marketing graphic for the SVICLOUD 15P that fits a wide Shop-card and homepage-hero media frame without object cropping. Make “SVICLOUD 15P” the primary selling headline in a refined premium geometric sans-serif—not oversized—then place “ANDROID 14 TV BOX” as a clearly smaller secondary line. Keep a compact “IN STOCK NOW” badge. Show four restrained feature pills with these exact labels: “WI-FI 6”, “BLUETOOTH 5.4”, “4 GB + 64 GB”, “4K HDR + AV1”. Remove the Explore CTA completely; include no button or button-like element. Make the accurate silver-gray 15P product the main visual, smaller than before and fully visible from top edge through its entire glossy black bottom/front edge, with comfortable padding below and no clipping. Preserve the centered black SVI.小雲 badge, white status LED, glossy black front band, and embossed 15 mark. Arrange all essential content and the complete device inside the central 76% of the canvas, leaving the outer top and bottom 12% as expendable atmospheric background so CSS cropping cannot cut the product or text. Use restrained cinematic blue/violet energy light behind the device, high contrast, sophisticated selling-focused typography, clean spacing, and readable text at card size. Render each allowed text string exactly once and no other words. No CTA, Explore text, price, release date/year, Limited Edition, Ultra Series, performance adjectives, app/service logos, remote, cable, packaging, people, watermark, invented ports, duplicate product, or extra microtext.
```

## Exact final correction prompt

```text
Edit the immediately preceding generated marketing graphic with only this targeted composition correction. Preserve its exact 3:2 landscape canvas, overall midnight-navy/cyan/blue-violet premium design, photorealistic product identity, lighting, typography style, all seven text strings, exact spellings, single occurrence of each string, four feature pills, and absence of CTA or extra text.

Create truly empty crop-safe atmosphere bands: the entire outer top 12% of the canvas and the entire outer bottom 12% of the canvas must contain atmospheric background only. No text, badge, feature pill, icon, or any part of the product may enter those bands. Move the compact “IN STOCK NOW” badge downward so its top edge begins clearly below the 12% boundary. Uniformly compress and reposition the remaining layout with clean spacing. Scale the device down modestly and raise it so the full product—including its complete top, side contours, entire glossy black bottom/front band, LED, and embossed 15 mark—ends clearly above the 88% boundary, leaving visible navy floor/background beneath it. Keep all essential content inside the middle 76% height and retain generous breathing room. Do not crop anything.

Maintain exact permitted text, each exactly once and no other words: “SVICLOUD 15P”; “ANDROID 14 TV BOX”; “IN STOCK NOW”; “WI-FI 6”; “BLUETOOTH 5.4”; “4 GB + 64 GB”; “4K HDR + AV1”. No CTA, no button-like CTA, no Explore text, price, date/year, promotional adjectives, service/app logos, remote, cable, packaging, people, invented ports, duplicate product, or extra microtext. Apply the existing SVICLOUD wordmark plus `svicloudtvbox.us` as a visible semi-transparent bottom-right delivery watermark after generation.
```
