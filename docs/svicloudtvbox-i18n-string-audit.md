# I18N String Inventory — April 2025

## Overview
- **Helper in use:** `svic_bilingual_span()` renders paired EN/中文 spans hidden via CSS. Found in 6 templates (50 total calls). Existing helper wraps `esc_html__`, so strings are also registered against the theme text domain.
- **Hard-coded dictionaries:** Several templates define associative arrays with `['en' => ..., 'zh' => ...]`. These model table entries, hero bullets, and CTA copy.
- **Goal for migration:** Each unique message must receive a stable translation key before we replace helper usage with `svic_translate()` (planned in `docs/svicloudtvbox-i18n-plan.md`).

## Helper Usage by Template
| Template | Calls | Notes |
| --- | --- | --- |
| `footer.php` | 12 | Logo tagline, summary paragraph, trust badges, pillar labels + descriptions, copyright line. |
| `front-page.php` | 3 | Concierge bullet list in service card. |
| `page-compare.php` | 26 | Hero badge/title/subtitle, benefit cards, product bullet lists, CTA buttons, final CTA block, feature comparison headings and per-row content. Many calls are indirectly fed by arrays listed below. |
| `woocommerce/archive-product.php` | 5 | Category hero subtitle plus per-card titles, leads, CTA buttons, and feature bullets. |
| `woocommerce/single-product.php` | 3 | Subtitle, trust ribbon, and set of product highlights. |
| `inc/helpers-svic.php` | 1 | Helper definition only. |

## Array-Based EN/中文 Dictionaries
These arrays power structured content and will need keys when we move to the registry.

### `page-compare.php`
- `\$comparison_rows`: RAM/Storage, Video Quality, Voice Remote, Kids App, Karaoke Mode, Best For entries (with en/zh values for 10P+ and 10S). 
- `\$key_differences`: Premium Performance, Family Entertainment, Smart Value cards with descriptions.
- CTA copy at bottom is handled via helper calls (see table above).

### `woocommerce/archive-product.php`
- Product card definitions (`$product_cards`) with `title`, `lead`, `features[]`, `button` fields for both SKUs.

### `woocommerce/single-product.php`
- `$trust_highlights` array lists 3 feature badges with en/zh copy.

## Other Bilingual Touchpoints
- Navigation labels and footer links currently rely on English titles; Chinese variants will need to live in menu configuration or translation registry once the switcher ships.
- WooCommerce system strings (cart, checkout, emails) remain English-only; flagged for future locale hooking.
- JavaScript bundle (`assets/js/theme.js`) presently contains no user-facing copy, but localized data payload will be required if future interactions add text.

## Next Steps
1. Derive translation keys for each unique message (see draft taxonomy in upcoming doc).
2. Map array-driven data structures to registry namespaces (e.g., `compare.table.ram.storage.p10p`).
3. Identify WooCommerce/emails strings that require hooking once registry exists.

## Key Mapping Snapshot
- `footer.*` keys cover tagline, summary, trust badges, pillar labels/descriptions, and copyright (`footer.tagline`, `footer.benefits.shipping.label`, etc.).
- `frontpage.concierge.*` stores concierge service checklist bullets from the homepage.
- `shop.hero.*` and `shop.cards.{10p,10s}.*` replace bilingual arrays powering the archive cards.
- `compare.*` namespace encapsulates hero copy, difference cards, product bullets/CTAs, comparison table rows, and final CTA messaging.
- `product.hero.*` and `product.highlights.*` supply PDP hero subtitle/detail and highlight bullets.
- Core UI strings (`core.cart.adding`, `core.badges.*`) live under the `core` namespace for reuse in navigation and components.

### Next Refactor Notes
- Templates should swap `svic_bilingual_span()` calls for `svic_translate_html()` with the keys above (e.g., `footer.tagline`, `compare.hero.badge`).
- When rendering arrays (bullets/rows), iterate over the keyed arrays from `svic_translate('compare.products.10p.bullets')` or pull specific values.
