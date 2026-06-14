# Agent-Friendly Website Implementation Validation

Date: 2026-06-13

## Implemented artifacts

- `theme/svicloudtvbox-lumen/inc/agent-resources.php`
  - Serves `/llms.txt`, `/llms-full.txt`, and `/agent/*.md` resources.
  - Supports localized path prefixes such as `/zh/llms.txt` without duplicating content.
  - Uses `text/plain` for `.txt` and `text/markdown` for `.md`.
  - Emits `X-Robots-Tag: index, follow`.
- `theme/svicloudtvbox-lumen/inc/guide-routes.php`
  - Serves guide section fallback routes when local WordPress page rows are absent.
  - Normalizes localized prefixes such as `/zh/guides-apps/` to the same guide section renderer.
- `theme/svicloudtvbox-lumen/inc/agent-sitemap.php`
  - Serves `/agent-friendly-sitemap.xml` listing agent endpoints, guide fallback routes, remote issue route, and decision pages.
  - Appends the sitemap to `robots.txt` and Rank Math sitemap index output when available.
- `theme/svicloudtvbox-lumen/inc/policy-contact-routes.php`
  - Provides local fallback 200 routes for `/contact/`, `/shipping-policy/`, and `/return-policy/` so canonical CTA/policy links validate even when DB page rows are missing.
- `theme/svicloudtvbox-lumen/inc/decision-pages.php`
  - Serves indexable product-decision pages:
    - `/svicloud-10p-vs-10s/`
    - `/best-svicloud-box-for-chinese-tv-usa/`
    - `/yogurt-tv-not-working-upgrade-guide/`
    - `/svicloud-box-authenticity-guide/`
  - Keeps support-first intent before upgrade recommendations.
  - Adds CTA event attributes for decision behavior tracking.
- `theme/svicloudtvbox-lumen/page-guide-section.php`
  - Adds quick-answer blocks for app, troubleshooting, and setup guide pages.
  - Adds visible FAQ accordions for app and troubleshooting intent pages.
  - Emits `FAQPage` schema only from visible FAQ content.
  - Keeps existing setup `HowTo` schema tied to visible setup steps.
- `theme/svicloudtvbox-lumen/assets/css/parts/67-guides.css`
  - Styles answer hub, internal links, and FAQ blocks.
- `scripts/validate_agent_resources.py`
  - Static regression check for required endpoints, product names, decision slugs, guide fallback slugs, sitemap inclusion, phone consistency, answer hub/schema presence, and generated CSS.
- `scripts/validate_live_agent_friendly.py`
  - Live HTTP validation for endpoints, crawl headers, sitemap inclusion, JSON-LD parseability/types, Product/Offer/Organization schema presence, fake rating/review absence, CTA/internal link presence, and policy/contact URL availability.

## Acceptance criteria mapping

### Answer hubs

- Quick-answer blocks: implemented for app, troubleshooting, and setup guide sections.
- Traditional Chinese priority: answer copy switches to Chinese when current locale starts with `zh`.
- Internal links: answer hubs link to guide hub, compare page, contact page, and the new decision/upgrade pages; existing guide sidebar and CTAs remain.
- Support path: support phone `702-389-3416` and contact links are present.
- Product/upgrade bridge: compare/product CTAs remain in guide sections and new decision pages.
- Stale phone guard: validation script checks forbidden phone patterns.

### Structured data

- Product/Offer schema: live validation checks 10P+ and 10S product pages expose `Product`, nested `Offer`, and `Organization` schema, and do not expose `AggregateRating`/`Review` markup.
- FAQPage schema: guide FAQ schema is generated from visible FAQ content only.
- HowTo schema: setup page still emits `HowTo` only from visible setup steps.
- Organization phone: existing schema helpers remain unchanged; validation guards new content for `702-389-3416`.
- Fake reviews: no aggregate rating/review markup was added.
- Repeatable checks: `scripts/validate_agent_resources.py` added.

### Agent Markdown and `/llms.txt`

- `/llms.txt` and `/llms-full.txt`: routed by `svic_serve_agent_resource`.
- Markdown endpoints: all required `/agent/*.md` resources are in the resource map.
- Content: concise official-storefront, product, app, troubleshooting, setup, policy, and contact guidance.
- Guardrails: no private data, no competitor text, no fake guarantees, no old phone numbers.
- Checks: validation script covers required endpoints, required product names, canonical links, phone, and route hook.

### Product decision and upgrade layer

- Decision pages: four requested decision/upgrade/authenticity routes implemented; existing `/compare/` remains canonical comparison page; `/agent-friendly-sitemap.xml` includes them for index discovery.
- Support-first intent: Yogurt TV upgrade page says fixes first, upgrade second.
- Trust blocks: decision content includes official US support, policy links, and authenticity cautions.
- Internal links: decision pages link to compare, contact, app guide, and troubleshooting.
- Tracking: CTA links include `data-svic-event="svic_decision_cta_click"` and labels.

## Edge-case review

- Localized prefixes: `/zh/...` paths are normalized for agent and decision resources.
- Unsupported claims: decision content avoids legal overclaims and app guarantees.
- Hidden FAQ risk: schema uses the same `$answer_hubs` FAQ content rendered as visible `<details>` elements.
- Duplicate schema risk: no fake reviews or aggregate ratings added; setup `HowTo` remains scoped to setup only.
- PHP compatibility: avoids `str_ends_with`; uses `substr` for older PHP compatibility.
- Cache/noise: Markdown endpoints bypass WordPress template noise and set crawlable robot headers.
- Generated CSS: partial and generated `guides.css` both updated.

## Validation commands run

```bash
python3 scripts/validate_agent_resources.py
find theme/svicloudtvbox-lumen -name '*.php' -print0 | xargs -0 -n1 php -l
python3 scripts/build_css.py --theme svicloudtvbox-lumen
python3 scripts/validate_live_agent_friendly.py --base 'http://127.0.0.1?host=svicloud10p.svic.local'
```

Observed results:

- `OK: agent resources, answer hubs, schema, phone guards`
- `OK: php lint`
- CSS build completed for all theme bundles.
- `OK: live endpoints, JSON-LD types, CTAs, and sitemap`

## Remaining deployment validation

After syncing/deploying WordPress, verify live HTTP status and rendered HTML for:

- `/llms.txt`
- `/llms-full.txt`
- `/agent/products.md`
- `/agent/compare-10p-vs-10s.md`
- `/agent/apps.md`
- `/agent/troubleshooting.md`
- `/agent/setup.md`
- `/agent/shipping-returns.md`
- `/agent/contact.md`
- `/guides-apps/`
- `/zh/guides-apps/`
- `/guides-troubleshooting/`
- `/zh/guides-troubleshooting/`
- `/guides-setup/`
- `/zh/guides-setup/`
- `/svicloud-10p-vs-10s/`
- `/best-svicloud-box-for-chinese-tv-usa/`
- `/yogurt-tv-not-working-upgrade-guide/`
- `/svicloud-box-authenticity-guide/`
