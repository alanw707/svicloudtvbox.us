# 15P troubleshooting refresh — September 19, 2026

## Scope
Existing English, Traditional Chinese and Simplified Chinese troubleshooting pages now prioritize 15P with 11 native symptom accordions, reversible checks, older-model applicability, and an email support checklist. Removed troubleshooting-only sales interruptions and unsupported pairing/recovery/firmware guarantees. No pricing, stock, order or product-removal changes.

Content commit: `32b9f3f` on main. Evidence source: internal troubleshooting KB and September 19 reference review. Older-model hotspot success is explicitly not presented as a confirmed 15P fix.

## Verification before release
- PHP syntax and git diff checks passed.
- Six new Playwright checks passed: all three locales in Chromium desktop and WebKit mobile. Preview uses the real PHP template inside the live page shell because the local Docker WordPress environment is unavailable.
- Six existing homepage, comparison and 10P+ product smoke checks passed across desktop/mobile.
- The guide regression isolates Google Translate and analytics; the third-party Translate widget emitted cross-origin-frame errors in WebKit before isolation. No fix to that widget is claimed.
- Desktop and mobile screenshots reviewed. Existing unrelated research files left untracked and untouched.

## FTPS certificate renewal blocker
The first deployment (Actions 35464377139) failed before uploads on certificate-pin mismatch. PHP syntax and CodeQL passed. The presented SHA-256 fingerprint was independently checked on the repository-documented Hostinger endpoint 147.79.122.118:21 and exactly matched the CI error:
`79a7c9aeaad8855c82f58598597b931b10429afec6234b621588f4e937b36dee`.

Certificate: CN/SAN `*.hstgr.io` and `hstgr.io`; issuer Let's Encrypt YE2; validity August 13–November 11, 2026. OpenSSL STARTTLS FTP verification with the system trust store, `-verify_hostname hstgr.io -verify_return_error`, returned Verification OK / code 0. Updated both deployment workflows to the independently verified fingerprint. TLS and control/data socket pin checks remain enabled. No credentials were sent during certificate inspection.

Production verification will follow the successful retry; this document does not itself assert deployment success.
