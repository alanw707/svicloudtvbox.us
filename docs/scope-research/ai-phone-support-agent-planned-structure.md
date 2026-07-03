# AI Phone Support Agent Planned Structure

## Scope and Intent
- Add docs/config artifacts for an internal Vapi phone-support pilot.
- No WordPress theme, WooCommerce, JavaScript, CSS, or deployment changes.
- Preserve existing public phone/email behavior until pilot calls pass.

## Current Shape
- `docs/support-agent/` is absent.
- Existing support facts are spread across:
  - `docs/svicloudtvbox-faq.md`
  - `docs/svicloudtvbox-faq-zh.md`
  - `docs/how-to-set-up-svicloud-tv-box.md`
  - `theme/svicloudtvbox-lumen/inc/agent-resources.php`
- Contact page renders translated phone/email values from theme translations.

## Planned Shape
```text
docs/
  support-agent/
    support-faq.md
    vapi-assistant-prompt.md
    pilot-test-calls.md
```

## File List
| File | Status | Responsibility |
|---|---|---|
| `docs/support-agent/support-faq.md` | new | Curated FAQ source for phone assistant; derived from existing repo docs only. |
| `docs/support-agent/vapi-assistant-prompt.md` | new | Vapi assistant instructions, bilingual behavior, escalation collection, safety boundaries. |
| `docs/support-agent/pilot-test-calls.md` | new | Manual validation scenarios and owner pass/fail record. |
| `theme/svicloudtvbox-lumen/lang/en_US.php` | unchanged | Existing public phone/email/support-form labels. |
| `theme/svicloudtvbox-lumen/page-contact.php` | unchanged | Existing contact rendering. |
| `theme/svicloudtvbox-lumen/inc/agent-resources.php` | unchanged | Existing static web-agent resources; source reference only. |

## Responsibility Changes
- New docs become source of truth for the Vapi pilot setup.
- Website remains source of truth for public contact display.
- Vapi/Google Voice setup remains external/manual and is validated through recorded pilot calls.

## Dependency Notes
- `vapi-assistant-prompt.md` depends on `support-faq.md` content.
- `pilot-test-calls.md` depends on both the FAQ and prompt.
- Public website copy should depend on successful pilot-call evidence, not on this docs commit.

## Review Diff Basis
Review implementation against this planned file list:
- Expected added files: `docs/support-agent/support-faq.md`, `docs/support-agent/vapi-assistant-prompt.md`, `docs/support-agent/pilot-test-calls.md`.
- Unexpected changed files: any theme/PHP/CSS/JS file unless explicitly replanned.
- Diff base: current branch before implementing `ai-phone-support-agent-plan.md`.
