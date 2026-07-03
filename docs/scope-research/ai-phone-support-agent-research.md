# AI Phone Support Agent Research

## Current State Assessment
- Source foundation exists and scopes an internal pilot Vapi phone assistant, not a custom app: `docs/scope-research/ai-phone-support-agent-foundation.md:36`.
- Existing public support phone is represented in theme translations as `+1 (520) 641-7021`: `theme/svicloudtvbox-lumen/lang/en_US.php:73`-`theme/svicloudtvbox-lumen/lang/en_US.php:76`.
- Existing support inbox is represented in theme translations as `support@svicloudtvbox.us`: `theme/svicloudtvbox-lumen/lang/en_US.php:78`-`theme/svicloudtvbox-lumen/lang/en_US.php:80`.
- Contact page builds `tel:` links from the translated phone value: `theme/svicloudtvbox-lumen/page-contact.php:10`, `theme/svicloudtvbox-lumen/page-contact.php:24`, `theme/svicloudtvbox-lumen/page-contact.php:33`.
- Support form already asks for phone and order number fields: `theme/svicloudtvbox-lumen/lang/en_US.php:666`-`theme/svicloudtvbox-lumen/lang/en_US.php:672`.
- Required `docs/support-agent/` artifacts are not present; only foundation artifact exists under `docs/scope-research/`.
- Existing FAQ/setup docs can seed knowledge, but no curated phone-agent FAQ exists: `docs/svicloudtvbox-faq.md:19`, `docs/svicloudtvbox-faq-zh.md:20`, `docs/how-to-set-up-svicloud-tv-box.md:19`.
- Build/test commands available: `npm test`, `npm run build:css`, `npm run build:js`: `package.json:9`-`package.json:13`.

## Workflow Trace
- Current website contact path: translated phone/email values -> `page-contact.php` builds phone/email/support links -> user contacts human support (`theme/svicloudtvbox-lumen/page-contact.php:10`, `theme/svicloudtvbox-lumen/page-contact.php:18`, `theme/svicloudtvbox-lumen/page-contact.php:24`).
- Intended pilot path from foundation: caller dials Google Voice support number -> Google Voice forwards to Vapi -> Vapi answers in English/Mandarin from curated FAQ -> Vapi escalates/logs email for support (`docs/scope-research/ai-phone-support-agent-foundation.md:69`-`docs/scope-research/ai-phone-support-agent-foundation.md:75`).
- Runtime trace beyond docs is not possible in repo: Vapi and Google Voice configuration are external/manual per foundation (`docs/scope-research/ai-phone-support-agent-foundation.md:82`-`docs/scope-research/ai-phone-support-agent-foundation.md:83`).

## Project Slice Code Map
- `theme/svicloudtvbox-lumen/lang/en_US.php` owns English contact phone/email copy and support-form field labels.
- `theme/svicloudtvbox-lumen/page-contact.php` consumes contact translations and renders phone/support entry points.
- `theme/svicloudtvbox-lumen/inc/agent-resources.php` exposes static agent-facing markdown resources with official phone and safety wording (`theme/svicloudtvbox-lumen/inc/agent-resources.php:18`, `theme/svicloudtvbox-lumen/inc/agent-resources.php:28`).
- `docs/svicloudtvbox-faq*.md`, setup guides, and troubleshooting KB are existing support knowledge inputs, not yet the curated phone-agent source.

## File Map
| File | Evidence | Role |
|---|---:|---|
| `docs/scope-research/ai-phone-support-agent-foundation.md` | `:19`-`:24`, `:61`-`:66`, `:94`-`:110` | Scope, stack, first pilot slice |
| `theme/svicloudtvbox-lumen/lang/en_US.php` | `:73`-`:80`, `:666`-`:672`, `:703`-`:705` | Current support phone/email/form copy |
| `theme/svicloudtvbox-lumen/page-contact.php` | `:10`, `:18`, `:24`, `:33` | Current contact entry point rendering |
| `theme/svicloudtvbox-lumen/inc/agent-resources.php` | `:18`, `:24`, `:28` | Existing static agent-resource pattern |
| `docs/svicloudtvbox-faq.md` | `:19`, `:23`, `:34`, `:45`, `:70`, `:79` | English FAQ seed content |
| `docs/svicloudtvbox-faq-zh.md` | `:20`, `:24`, `:35`, `:46`, `:71` | Chinese FAQ seed content |
| `docs/how-to-set-up-svicloud-tv-box.md` | `:19`, `:42`, `:79`, `:95`, `:97` | English setup/support seed content |
| `package.json` | `:9`-`:13` | Exact validation/build commands |

## Structure Outline
- Current repo slice is a WordPress theme plus docs; phone-agent pilot is currently documentation/config only.
- Proposed support-agent docs directory is absent; foundation names `docs/support-agent/support-faq.md`, `docs/support-agent/vapi-assistant-prompt.md`, and `docs/support-agent/pilot-test-calls.md` as bootstrap artifacts (`docs/scope-research/ai-phone-support-agent-foundation.md:77`-`docs/scope-research/ai-phone-support-agent-foundation.md:83`).
- Existing agent resources are generated PHP strings under `inc/agent-resources.php`, but foundation keeps day-one phone-agent knowledge as Markdown under `docs/support-agent/` (`docs/scope-research/ai-phone-support-agent-foundation.md:64`).

## Verified Facts
- Already true: public support phone and support email exist in site copy (`theme/svicloudtvbox-lumen/lang/en_US.php:73`-`theme/svicloudtvbox-lumen/lang/en_US.php:80`).
- Already true: bilingual support is represented in existing docs/site copy (`docs/svicloudtvbox-faq.md:79`, `docs/how-to-set-up-svicloud-tv-box.md:97`).
- Already true: order number and phone are current support-form concepts (`theme/svicloudtvbox-lumen/lang/en_US.php:666`-`theme/svicloudtvbox-lumen/lang/en_US.php:672`).
- Not true: curated `docs/support-agent/` FAQ/prompt/test-call docs do not exist.
- Not true in repo: Vapi assistant config, Google Voice forwarding config, and email-escalation verification evidence.
- Unknown: whether Google Voice can forward to the required Vapi endpoint/number (`docs/scope-research/ai-phone-support-agent-foundation.md:125`).
- Unknown: exact Vapi built-in email/escalation behavior (`docs/scope-research/ai-phone-support-agent-foundation.md:126`).

## Design Question Evidence
- Telephony evidence: foundation says keep Google Voice and forward to Vapi (`docs/scope-research/ai-phone-support-agent-foundation.md:19`-`docs/scope-research/ai-phone-support-agent-foundation.md:20`); repo exposes phone number but not Google Voice configuration.
- Platform evidence: foundation defines Vapi as hosted assistant and no custom Twilio/webhook unless built-ins fail (`docs/scope-research/ai-phone-support-agent-foundation.md:14`, `docs/scope-research/ai-phone-support-agent-foundation.md:33`, `docs/scope-research/ai-phone-support-agent-foundation.md:47`).
- Knowledge evidence: foundation requires one curated FAQ, no crawler (`docs/scope-research/ai-phone-support-agent-foundation.md:40`, `docs/scope-research/ai-phone-support-agent-foundation.md:55`); existing FAQ/setup/troubleshooting docs provide seed facts.
- Safety evidence: foundation prohibits refund/warranty/shipping-exception/compatibility commitments and calls for escalation instead (`docs/scope-research/ai-phone-support-agent-foundation.md:29`-`docs/scope-research/ai-phone-support-agent-foundation.md:32`, `docs/scope-research/ai-phone-support-agent-foundation.md:120`-`docs/scope-research/ai-phone-support-agent-foundation.md:122`).

## Open Unknowns
- Google Voice forwarding mechanics into Vapi.
- Vapi built-in escalation/email format and deliverability to `support@svicloudtvbox.us`.
- Which 10-20 FAQ entries owner wants in the first pilot source.
- Mandarin voice/model quality on real SVICLOUD product/setup vocabulary.

## Remaining Blocker
- No planning blocker for docs/config pilot. External setup spikes remain for Google Voice forwarding and Vapi escalation delivery.

## Plan Readiness
- Ready.
- Next plan can target documentation/config artifacts only, with validation through owner-approved pilot calls and exact local checks: `npm test` if website behavior changes; no deploy/build needed for docs-only changes unless theme files change.
