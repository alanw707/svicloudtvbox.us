# AI Phone Support Agent Design Discussion

## Context Summary
- Goal: internal pilot for an AI phone support agent for `svicloudtvbox.us`.
- Platform choice is already constrained: Vapi hosted assistant plus existing Google Voice number.
- Repo work is docs/config only unless Vapi built-ins cannot handle escalation.
- Existing website already exposes phone/email; public site changes are not needed for first pilot.

## Design Goals
- Answer common support questions in English and Mandarin.
- Use curated static knowledge, not crawling or live order lookup.
- Escalate unresolved or risky issues to `support@svicloudtvbox.us`.
- Keep setup small enough to validate with real pilot calls.
- Avoid custom code until hosted tools fail.

## Proposed Solution Shape
- Add `docs/support-agent/support-faq.md` from existing FAQ/setup/support docs.
- Add `docs/support-agent/vapi-assistant-prompt.md` for assistant behavior and boundaries.
- Add `docs/support-agent/pilot-test-calls.md` for manual validation.
- Keep Vapi and Google Voice configuration manual.

## Intended Placement
- Put pilot docs under `docs/support-agent/` because this is not theme runtime behavior.
- Keep planning artifacts under `docs/scope-research/`.
- Do not alter `theme/svicloudtvbox-lumen/` until public launch copy is explicitly requested.

## Architecture Patterns
- Static Markdown config handoff, not application code.
- Manual external setup with repo-tracked instructions/checklists.
- Safety by explicit refusal/escalation rules in the prompt.
- Validation by owner-approved real calls, not simulated unit tests.

## Design Questions and Answers

### Q1. First FAQ source
- Decision: use existing repo docs only.
- Rationale: fastest safe pilot; avoids invented support facts.
- Consequence: owner review remains required before public use.

### Q2. Public copy changes
- Decision: internal-only pilot; no website/contact changes.
- Rationale: forwarding, escalation, and Mandarin quality are unproven.
- Consequence: no build/deploy required for docs-only implementation.

### Q3. Escalation representation
- Decision: prompt tells assistant to collect name, phone, order number when relevant, and issue summary; Vapi delivery verified manually.
- Rationale: Vapi email/log format is unknown, so the plan should not prescribe a brittle template.
- Consequence: add exact email template later only if Vapi supports it cleanly.

## Tradeoffs and Rejected Options
- Rejected custom Twilio/webhook flow: unnecessary unless Vapi built-ins fail.
- Rejected automated website crawler: foundation requires one curated FAQ source.
- Rejected public website copy update: creates support risk before pilot calls pass.
- Rejected live order lookup: out of day-one scope and higher privacy/security risk.

## Follow-Up Decisions
- If Google Voice forwarding fails, decide whether to use a Vapi-provided number publicly or choose another telephony bridge.
- If Vapi escalation delivery is inadequate, decide between a tiny webhook and manual dashboard review.
- If Mandarin quality is weak, decide whether to change Vapi voice/model settings or narrow Mandarin support wording.
- After successful pilot calls, decide whether to update website copy to announce AI-assisted phone support.
