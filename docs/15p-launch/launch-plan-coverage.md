# 15P Launch Plan — Coverage Map

Date: 2026-08-15. This maps the original launch plan to shipped local artifacts and explicit launch-day handoffs.

| Original plan item | Evidence / handoff |
|---|---|
| Product intel: specs, photos, price, release, warranty, shipping, 15P-vs-10P/9P reasons | `15p-product-intel.md`: official/retail research, sourced 10P+/9P baseline, all unpublished 15P facts marked `[PLACEHOLDER]`, supplier fill-in checklist. |
| Dedicated `/product/svicloud-15p/` | WooCommerce product slug `svicloud-15p` created in local Docker (ID 95); shared PDP template in `woocommerce/single-product.php`; production creation is explicit in `launch-checklist.md`. |
| Target phrases | 15P page EN/zh copy and all three blog drafts target SVICloud 15P, USA, Korean/Chinese/Japanese TV box, Asian IPTV box, no monthly fees. |
| 15P vs 10P / 9P / who should upgrade / shipping-support-warranty | Conditional PDP comparison block, `products.svicloud-15p.comparison` strings in en_US/zh_TW/zh_CN. Verified HTTP 200 in English and zh. |
| Keep existing models live and funnel buyers to 15P | 10P+, 10S, and a published-but-out-of-stock legacy 9P PDP remain live locally and link to the 15P preview. The 15P comparison also links back to both 10P+ and 9P. |
| Homepage title/meta / shop copy / schema / alt text | Rendered local `<title>` contains 15P; `functions.php` provides native + Rank Math metadata and image alt; shop copy is prelaunch/TBC-safe; 15P emits one Product node plus FAQPage and BreadcrumbList. |
| Permanent clean slug | `svicloud-15p`, URL `/product/svicloud-15p/`. |
| Launch offer | `launch-checklist.md`: selected simple angle, “New SVICloud 15P — now available in the USA” + free shipping; optional accessory only if margin permits. |
| Google Ads | `google-ads-brief.md`: standalone 15P campaign, product-page landing URL, required headlines, descriptions, keywords, negatives, assets, pre-flight list. Manual setup remains out of scope. |
| Supporting content | Drafts: `docs/blog/svicloud-15p-vs-10p-comparison.md`, `best-svicloud-box-korean-chinese-japanese-usa-tv.md`, `where-to-buy-svicloud-15p-usa.md`. |
| Operations / support | `support-faq-15p.md`, `warranty-return-wording.md`, `launch-checklist.md` cover inventory, supplier ETA, support, returns, checkout/tax/email checks. Manual operations remain out of scope. |
| Theme refresh | 15P-first hero composition, floating nav shell, three-model lineup, shared PDP comparison/cross-link treatment, and distinct before/after homepage + 10P PDP screenshots on desktop/mobile. |
| Verification | `verification-report.md`: CSS build, Docker sync, PHP lint, strict no-ignore console smoke tests, launch safeguards for rendered title/schema/9P/TBC copy, and full Playwright result (88 passed, 8 declared skips, 0 failed). |

## Launch-day deferrals (intentional)

- Replace all 15P specification, price, image, inventory, warranty, and release placeholders from the supplier press kit.
- Create the WooCommerce 15P preview and preserve/create the legacy 9P reference PDP in production manually; add official 15P images, price, and stock only after confirmation. No theme deployment is part of this goal.
- Publish the three posts and configure Google Ads manually after final specifications and tracking are confirmed.
