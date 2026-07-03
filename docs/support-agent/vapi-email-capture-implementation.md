# Vapi email follow-up capture implementation

This records the five applied changes from the phone support update.

1. Ask preferred follow-up method.
   - Implemented in `docs/support-agent/vapi-assistant-prompt.md` escalation workflow: "Would you prefer support to follow up by phone or email?"

2. Capture email only if the caller chooses email.
   - Implemented in `docs/support-agent/vapi-assistant-prompt.md`: email is optional and only requested when email follow-up is chosen.

3. Ask the caller to spell the email slowly and repeat it back in chunks.
   - Implemented in `docs/support-agent/vapi-assistant-prompt.md`: repeat-back example uses "alan dot wang at gmail dot com".

4. Include `caller_email` only after caller confirmation; otherwise fall back to phone.
   - Implemented in `docs/support-agent/vapi-assistant-prompt.md`.
   - `docs/support-agent/vapi-escalation-tool.schema.json` defines `caller_email` as optional; it is not in `required`.

5. Send confirmed email to support team when present.
   - Implemented in `theme/svicloudtvbox-lumen/vapi-escalation.php`: parses `caller_email` and adds `Email: ...` to the support email body.

Model update applied with the same change set:
- `setup-vapi.mjs` uses `gpt-4.1-mini` for new setup.
- `fix-vapi.mjs` uses `gpt-4.1-mini` and preserves both FAQ and escalation tools.
- `docs/support-agent/vapi-escalation-tool.schema.json` is the local Vapi `/tool` creation payload for `sendEscalation`.
