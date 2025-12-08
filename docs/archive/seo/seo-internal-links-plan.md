# Internal Link Plan for Auto-Generated Posts (svicloudtvbox.us)

## Goals
- Raise internal link density in generated posts without sounding spammy.
- Push PageRank toward money pages (shop, compare, guides, FAQ, contact).
- Keep anchors natural, varied, and context-aligned.

## Link sources (current config)
- Defined in `automation/blog_automation/config.yaml` under `internal_links` and `internal_link_targets`:
  - Compare: `https://svicloudtvbox.us/compare/`
  - Shop/Home: `https://svicloudtvbox.us/`
  - Contact/Support (ZH): `https://svicloudtvbox.us/zh/contact/`
  - FAQ (ZH): `https://svicloudtvbox.us/zh/blog/svicloud-faq/`

## Rules of thumb
- Count: 3–5 internal links per 1,000–1,200 words (cap at 6 per post).
- Placement: spread links—early (after intro), mid (after 1–2 body sections), late (before conclusion/CTA).
- Avoid self-linking to the same URL as the post slug. Deduplicate per URL per post.
- Anchors: use descriptive, sentence-integrated anchors; avoid naked URLs.
- Language: match page language (EN post → EN anchors; ZH post → ZH anchors/targets).
- Relevance: pick target based on post intent (comparison → `/compare/`; setup/tips → FAQ/support; buyer intent → home/shop).

## Anchor examples
- Compare: “SVICLOUD 10P+ vs 10S comparison table”, “SVICLOUD 方案比較”
- Shop/Home: “SVICLOUD 美國官網”, “現貨與保固資訊”
- Contact/Support: “聯絡中文客服”, “美國客服支援”
- FAQ: “SVICLOUD 常見問題”, “安裝與保固 FAQ”

## Implementation outline (automation)
1) Add a linker utility in `automation/blog_automation/blog_automation.py` that:
   - Accepts: post language, intent/theme, desired link count.
   - Filters `internal_link_targets` for language/intent.
   - Samples 3–5 targets, dedupes, and returns anchor+URL pairs.
2) Injection points in render phase:
   - After intro paragraph.
   - After first or second H2 block.
   - Before final CTA/conclusion.
3) Safety checks:
   - Skip if target URL == canonical of the post.
   - Ensure anchors are inserted once per target.
   - Keep link text < 60 chars; avoid exact same anchor twice.
4) Config tweaks (if needed):
   - Add EN/ZH variants to `internal_link_targets` with `lang` and `intent` fields.
   - Add `max_internal_links` (default 5) and `min_word_count_for_links` (e.g., 600) to config.

## QA checklist
- Links render in the final Markdown/HTML with correct URLs.
- No duplicate internal links to the same target per post.
- Anchors read naturally in-sentence; no keyword stuffing.
- Language of anchor matches page language.
- Post still passes quality thresholds and length minimums.
