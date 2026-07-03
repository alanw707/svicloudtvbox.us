# SVICLOUD Phone Support Pilot Test Calls

_Internal pilot checklist. Owner runs each scenario, records pass/fail, and notes any issues._

## Preconditions
- Google Voice support number is forwarding to Vapi.
- Vapi assistant is configured with the current `support-faq.md` and `vapi-assistant-prompt.md`.
- Knowledge File (RAG) is loaded and up to date.
- Escalation delivery destination is `support@svicloudtvbox.us`.
- Test call originates from a phone not associated with the Vapi account.

## Pass rules
For each test call to pass:
- Assistant responds in the caller's chosen language (English or Mandarin).
- Answer stays within FAQ content.
- Risky or unknown topics are escalated, not guessed.
- Escalation collects: name, phone, order number (when relevant), and issue summary.
- No refund, warranty, shipping-exception, compatibility, or order-lookup promises are made.

## Test scenarios

### Scenario 1 — English FAQ answer
- **Dial:** Google Voice support number.
- **Prompt in English:** "How do I pair the remote control?"
- **Expected:** Assistant answers from FAQ: hold VOL- and VOL+ together after replacing batteries.
- **Pass if:** Answer is correct, concise, and in English.

| Date | Tester | Result | Notes |
|------|--------|--------|-------|
|      |        |        |       |

### Scenario 2 — Mandarin FAQ answer
- **Dial:** Google Voice support number.
- **Prompt in Mandarin:** "盒子怎麼設定語言？" (How to set language on the box?)
- **Expected:** Assistant switches to Mandarin and answers from FAQ.
- **Pass if:** Answer is correct, comprehensible, and in Mandarin.

| Date | Tester | Result | Notes |
|------|--------|--------|-------|
|      |        |        |       |

### Scenario 3 — Order status escalation with order number
- **Dial:** Google Voice support number.
- **Prompt in English:** "I have order number SV12345 and I need to know when it ships."
- **Expected:** Assistant declines order-lookup commitment, asks for name and phone, and escalates with order number.
- **Pass if:** Escalation captures name, phone, order number, and issue summary; no delivery date promised.

| Date | Tester | Result | Notes |
|------|--------|--------|-------|
|      |        |        |       |

### Scenario 4 — Refund/warranty escalation refusal
- **Dial:** Google Voice support number.
- **Prompt in Mandarin:** "我這台盒子有問題，要退款。" (My box has a problem, I want a refund.)
- **Expected:** Assistant refuses refund promise, explains that a specialist follows up, and collects escalation details.
- **Pass if:** No refund promise made; escalation fields collected.

| Date | Tester | Result | Notes |
|------|--------|--------|-------|
|      |        |        |       |

### Scenario 5 — Unknown question escalation
- **Dial:** Google Voice support number.
- **Prompt in English:** "Can SVICLOUD run Xbox Game Pass?"
- **Expected:** Assistant says the question is not covered, offers to escalate, and collects name, phone, and summary.
- **Pass if:** No invented answer; escalation offered and executed.

| Date | Tester | Result | Notes |
|------|--------|--------|-------|
|      |        |        |       |

## Forwarding and delivery notes

### Google Voice forwarding check
- Google Voice must forward inbound calls to the Vapi phone number endpoint.
- Verify: call the Google Voice number from a different phone. Confirm it rings Vapi's assistant, not voicemail or silence.
- Record outcome below.

### Vapi escalation delivery check
- After each escalation, confirm that `support@svicloudtvbox.us` receives the email or that the Vapi dashboard log captures enough data for follow-up.
- Record deliverability outcome below.

| Check | Date | Result | Notes |
|-------|------|--------|-------|
| Google Voice → Vapi forwarding |      | Pass / Fail |        |
| Escalation email delivery       |      | Pass / Fail |        |

## Pilot acceptance
The pilot passes when:
- All test scenarios have been run at least once.
- At least one English FAQ call and one Mandarin call record a Pass result.
- At least one escalation scenario records a Pass result.
- Google Voice forwarding and escalation delivery are confirmed.
- The owner reviews all call notes and approves the pilot for continuation.

## Owner approval

| Date | Owner | Approved | Notes |
|------|-------|----------|-------|
|      |       | Yes / No |       |
