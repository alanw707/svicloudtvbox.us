# AI Phone Support Agent Plan

## Plan Status
- Status: Approved for implementation.
- Source artifact: `docs/scope-research/ai-phone-support-agent-research.md`.
- Scope: documentation/config pilot only; no website/theme behavior changes.
- Blocking ambiguity: none.

## Preconditions
- Vapi account exists: `docs/scope-research/ai-phone-support-agent-foundation.md:44`.
- Support inbox is `support@svicloudtvbox.us`: `docs/scope-research/ai-phone-support-agent-foundation.md:45`.
- Public support phone already exists in site copy: `theme/svicloudtvbox-lumen/lang/en_US.php:73`-`:75`.
- Vapi forwarding and escalation delivery remain manual/external validation items: `docs/scope-research/ai-phone-support-agent-foundation.md:46`-`:47`.

## Clarifications Resolved
- FAQ source: existing repo docs only for first pilot; no invented owner-only facts.
- Launch mode: internal pilot only; no contact page or public copy changes.
- Escalation: prompt collects caller name, phone, order number when relevant, and issue summary; Vapi delivery is proven by pilot calls before public use.

## Design Summary
- Keep the pilot as Markdown artifacts under `docs/support-agent/`.
- Use Vapi as hosted assistant configuration, not custom Twilio/webhook code.
- Keep Google Voice as public support-number source and forward manually to Vapi.
- Support English and Mandarin on day one; Cantonese remains out of scope.
- Escalate risky issues instead of making refund, warranty, shipping-exception, compatibility, or order-status commitments.

## Structure Summary
Current:
- `docs/support-agent/` does not exist.
- Existing support knowledge lives in FAQ/setup docs and static agent-resource PHP strings.

Planned:
- `docs/support-agent/support-faq.md`: curated phone-agent FAQ seed from existing repo docs.
- `docs/support-agent/vapi-assistant-prompt.md`: Vapi prompt, safety rules, bilingual behavior, escalation collection.
- `docs/support-agent/pilot-test-calls.md`: owner test-call checklist and pass/fail log.

Dependencies:
- `vapi-assistant-prompt.md` depends on `support-faq.md`.
- `pilot-test-calls.md` depends on the prompt and manual Vapi/Google Voice setup.

## Solution Path
1. Create the support-agent docs directory and curate a small FAQ from existing English/Chinese/setup/support materials.
2. Write a Vapi prompt that constrains answers to the curated FAQ and escalates unsafe/unknown cases.
3. Define pilot calls that prove English, Mandarin, escalation capture, and safety refusals.
4. Validate Markdown and run pilot calls manually; do not run website build/test unless theme files change.

## Task Breakdown

### T1. Create phone-agent FAQ source
- Files: `docs/support-agent/support-faq.md`
- Action: Create 10-20 concise English-first FAQ entries from existing repo docs, with Mandarin-friendly wording notes where source docs already support it.
- Depends on: none
- Rollback: delete `docs/support-agent/support-faq.md`
- Parallel: yes
- Risk: low
- Review required: yes
- Verify: `test -f docs/support-agent/support-faq.md && grep -E "^## |^### " docs/support-agent/support-faq.md`

### T2. Create Vapi assistant prompt
- Files: `docs/support-agent/vapi-assistant-prompt.md`
- Action: Define assistant role, answer boundaries, English/Mandarin behavior, escalation collection fields, and safety refusal rules.
- Depends on: T1
- Rollback: delete `docs/support-agent/vapi-assistant-prompt.md`
- Parallel: no
- Risk: medium
- Review required: yes
- Verify: `grep -E "Vapi|Mandarin|escalat|refund|warranty|shipping|compatibility" docs/support-agent/vapi-assistant-prompt.md`

### T3. Create pilot test-call checklist
- Files: `docs/support-agent/pilot-test-calls.md`
- Action: Add test scenarios for English FAQ, Mandarin FAQ, unknown issue escalation, risky policy escalation, and failed forwarding/delivery notes.
- Depends on: T2
- Rollback: delete `docs/support-agent/pilot-test-calls.md`
- Parallel: no
- Risk: medium
- Review required: yes
- Verify: `grep -E "English|Mandarin|escalation|Google Voice|Vapi|Pass|Fail" docs/support-agent/pilot-test-calls.md`

### T4. Run local docs sanity checks
- Files: `docs/support-agent/support-faq.md`, `docs/support-agent/vapi-assistant-prompt.md`, `docs/support-agent/pilot-test-calls.md`
- Action: Confirm expected files exist and contain no old phone number, no direct order lookup promise, and no unsafe policy commitment.
- Depends on: T1, T2, T3
- Rollback: fix or revert offending doc lines
- Parallel: no
- Risk: low
- Review required: no
- Verify: `test -f docs/support-agent/support-faq.md && test -f docs/support-agent/vapi-assistant-prompt.md && test -f docs/support-agent/pilot-test-calls.md && ! grep -R "WooCommerce/order lookup\|guarantee.*refund\|guarantee.*warranty" docs/support-agent`

### T5. Manual pilot validation
- Files: `docs/support-agent/pilot-test-calls.md`
- Action: Owner runs and records English/Mandarin/support-escalation calls after manual Vapi and Google Voice setup.
- Depends on: T4
- Rollback: keep pilot internal and revise prompt/FAQ; do not update public site copy
- Parallel: no
- Risk: high
- Review required: yes
- Verify: owner-approved pass/fail notes in `docs/support-agent/pilot-test-calls.md`

## Requirements Traceability
- Keep Google Voice public number: T3, T5; source `docs/scope-research/ai-phone-support-agent-foundation.md:19`-`:20`.
- Use Vapi hosted assistant: T2, T5; source `docs/scope-research/ai-phone-support-agent-foundation.md:14`, `:36`.
- English and Mandarin day one: T1, T2, T3, T5; source `docs/scope-research/ai-phone-support-agent-foundation.md:8`, `:21`, `:39`.
- Answer from curated FAQ only: T1, T2; source `docs/scope-research/ai-phone-support-agent-foundation.md:22`, `:40`.
- Escalate hard/risky issues to support: T2, T3, T5; source `docs/scope-research/ai-phone-support-agent-foundation.md:23`-`:24`.
- No custom app/webhook unless Vapi built-ins fail: T2, T5; source `docs/scope-research/ai-phone-support-agent-foundation.md:33`, `:47`.

## Constraints
- Website contact phone is already rendered from translations; no public copy change in this plan: `theme/svicloudtvbox-lumen/page-contact.php:10`-`:13`, `:24`, `:33`.
- Existing phone value is `+1 (520) 641-7021`; do not introduce older numbers: `theme/svicloudtvbox-lumen/lang/en_US.php:73`-`:75`, `theme/svicloudtvbox-lumen/inc/agent-resources.php:18`.
- Support email is `support@svicloudtvbox.us`: `theme/svicloudtvbox-lumen/lang/en_US.php:80`.
- Support form already uses order number and phone concepts: `theme/svicloudtvbox-lumen/lang/en_US.php:87`, `:666`-`:672`.
- No WooCommerce/order lookup in day one: `docs/scope-research/ai-phone-support-agent-foundation.md:26`.
- No refund, warranty, shipping-exception, or compatibility commitments: `docs/scope-research/ai-phone-support-agent-foundation.md:29`.
- Docs-only changes need no CSS/JS build; if theme files change later, use package commands: `package.json:9`-`:13`.

## Validation
| Check | Task | Command / Evidence |
|---|---|---|
| FAQ exists and is structured | T1 | `test -f docs/support-agent/support-faq.md && grep -E "^## |^### " docs/support-agent/support-faq.md` |
| Prompt includes required boundaries | T2 | `grep -E "Vapi|Mandarin|escalat|refund|warranty|shipping|compatibility" docs/support-agent/vapi-assistant-prompt.md` |
| Pilot checklist covers core scenarios | T3 | `grep -E "English|Mandarin|escalation|Google Voice|Vapi|Pass|Fail" docs/support-agent/pilot-test-calls.md` |
| No unsafe docs promises | T4 | `test -f docs/support-agent/support-faq.md && test -f docs/support-agent/vapi-assistant-prompt.md && test -f docs/support-agent/pilot-test-calls.md && ! grep -R "WooCommerce/order lookup\|guarantee.*refund\|guarantee.*warranty" docs/support-agent` |
| Real call approval | T5 | Owner-approved notes in `docs/support-agent/pilot-test-calls.md` |
| Website regression, only if theme files change | Later only | `npm test` |

## Replan Triggers
- Google Voice cannot forward to a Vapi-usable number/endpoint.
- Vapi built-in escalation cannot send/log enough detail for support follow-up.
- Owner wants public website copy changes before pilot calls pass.
- Owner supplies FAQ content that contradicts existing policy/source docs.
- Mandarin call quality fails product/setup vocabulary enough to require a different voice/model/provider.
- Implementation touches theme/PHP files not listed in this plan.
