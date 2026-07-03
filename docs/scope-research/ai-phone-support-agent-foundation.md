# AI Phone Support Agent Foundation

## Vision Summary
- Build an AI customer support phone agent for `svicloudtvbox.us`.
- The agent answers the website support phone line currently served by Google Voice.
- First launch is an internal pilot, not full public rollout.
- Primary purpose: support triage for existing customers.
- Success means the agent answers common support questions in English and Mandarin, safely escalates unresolved issues, and passes owner-approved test calls.

## Actor Model
- Caller/customer: asks product, setup, shipping, return, warranty, compatibility, or order-status questions.
- AI phone agent: answers only from curated static support knowledge; collects escalation details when needed.
- Site owner/support operator: maintains FAQ source, reviews escalation emails, approves pilot calls.
- Vapi: hosted voice-agent platform handling call flow, speech, assistant behavior, and built-in escalation/logging.
- Google Voice: public support number source; forwards calls to Vapi for the pilot.

## Goals and Non-Goals Alignment
- Goals:
  - Keep the existing Google Voice public support number.
  - Forward calls from Google Voice to Vapi.
  - Support English and Mandarin on day one.
  - Answer common FAQ/product/setup/support questions from a curated source.
  - Escalate hard or risky issues by collecting caller name, phone, issue summary, and order number when relevant.
  - Send escalations to `support@svicloudtvbox.us`.
- Non-goals for day one:
  - No WooCommerce/order lookup.
  - No payment handling.
  - No sales-close workflow.
  - No refund, warranty, shipping-exception, or compatibility commitments.
  - No warm transfer to a live human.
  - No website crawler.
  - No WordPress admin UI.
  - No custom Twilio app or webhook unless Vapi built-ins fail.

## Terminology Decisions
- "AI customer support Agent" means a hosted Vapi voice assistant, not a custom app service.
- "Support phone number" means the existing Google Voice number exposed by the website.
- "Escalation" means async email to support, not live transfer.
- "Chinese support" means Mandarin first; Cantonese is out of scope until call demand proves it.
- "Knowledge" means one curated FAQ source, not automated website crawling.
- "Done" means internal pilot acceptance, not public launch.

## Constraints and Assumptions
- User already has a Vapi account.
- Existing support inbox is `support@svicloudtvbox.us`.
- Google Voice can forward calls to a number/endpoint usable by Vapi or a Vapi-provided phone number; this must be verified during setup.
- Vapi built-ins are assumed sufficient for first escalation/logging; if not, add a tiny webhook later.
- FAQ owner is the site owner; updates are manual.
- Existing repo is `svicloudtvbox.us`; foundation/docs live here.
- RPI pipeline contract loaded from `rpi-pipeline.yml` and treated as authoritative.

## Decision Surface
- Telephony: keep Google Voice; forward to Vapi.
- Voice platform: Vapi, because account already exists and hosted voice avoids custom Twilio code.
- Knowledge source: curated FAQ doc, because controlled answers beat broad stale scraping for phone support.
- Escalation: email only, because simplest reliable pilot path.
- Data access: static knowledge only; no customer/order system access.
- Safety policy: no commitments on refunds, warranty disputes, shipping exceptions, or uncertain compatibility.
- Repo boundary: documentation/config artifacts only unless Vapi built-ins cannot email escalations.

## Recommended Stack
- Vapi hosted assistant for voice AI, call handling, multilingual conversation, and built-in tools/logging.
- Google Voice forwarding for preserving the public support number.
- Markdown support FAQ stored under `docs/support-agent/` for source-controlled pilot knowledge.
- Vapi dashboard/config for assistant prompt, language behavior, and escalation handling.
- Email destination: `support@svicloudtvbox.us`.
- No app runtime for day one.

## Architecture Shape
- Caller dials existing Google Voice support number.
- Google Voice forwards call to Vapi-controlled phone flow.
- Vapi assistant answers, detects/uses English or Mandarin, and responds from curated FAQ.
- If question is outside FAQ or high-risk, assistant gathers required contact/context fields.
- Vapi sends or logs escalation for `support@svicloudtvbox.us` using built-in capability.
- Owner reviews escalation and follows up manually.

## Bootstrap Shape
- Add docs only in existing repo:
  - `docs/support-agent/support-faq.md`
  - `docs/support-agent/vapi-assistant-prompt.md`
  - `docs/support-agent/pilot-test-calls.md`
- Configure Vapi manually from those docs.
- Configure Google Voice forwarding manually.
- No deployment pipeline needed unless custom webhook is introduced.

## Bootstrap Commands
```bash
mkdir -p docs/support-agent
$EDITOR docs/support-agent/support-faq.md
$EDITOR docs/support-agent/vapi-assistant-prompt.md
$EDITOR docs/support-agent/pilot-test-calls.md
```

## First Vertical Slice
- Create a minimal FAQ with 10-20 high-frequency support answers.
- Create a Vapi assistant prompt that:
  - greets callers,
  - supports English and Mandarin,
  - answers only from FAQ,
  - refuses commitments on risky topics,
  - collects escalation fields,
  - sends/escalates to `support@svicloudtvbox.us`.
- Forward Google Voice to Vapi.
- Run pilot calls:
  - English FAQ answer,
  - Mandarin FAQ answer,
  - order-status escalation with order number,
  - refund/warranty escalation refusal,
  - unknown question escalation.
- Pilot passes only when owner approves call behavior and escalation email delivery.

## Risks and Spikes
- Google Voice forwarding compatibility with Vapi may fail or add friction.
  - Spike: verify forwarding path before writing extra code.
- Vapi built-in email escalation may not support the exact email format needed.
  - Spike: test built-in escalation; add webhook only if required.
- Mandarin quality may vary by voice/model.
  - Spike: run Mandarin pilot calls with real product/setup vocabulary.
- FAQ gaps may cause over-escalation.
  - Spike: review pilot call transcripts and add only missing high-frequency answers.
- Agent may overpromise despite guardrails.
  - Spike: test adversarial refund/warranty/shipping/compatibility calls.

## Open Unknowns
- Exact Google Voice forwarding mechanics into Vapi are unverified.
- Exact Vapi built-in email/escalation mechanism is unverified.
- Current FAQ content does not yet exist.
- Public launch monitoring and call review cadence are intentionally deferred.

## Plan Readiness
- Ready for planning the internal pilot.
- Remaining unknowns are isolated setup spikes, not blockers to a pilot plan.
