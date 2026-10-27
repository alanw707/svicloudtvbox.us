# FTPS deployment identity and certificate renewal

## Root cause

Both deployment workflows pinned a short-lived Hostinger leaf certificate. The
September 2026 repair replaced that fingerprint; the next certificate renewal
broke the October deployment before authentication/upload. A fingerprint update
would repeat the same maintenance problem at each renewal.

## Renewal-safe policy

Both `.github/workflows/deploy-theme.yml` (automatic release) and `deploy.yml`
(manual release) use:

```yaml
FTP_PROTOCOL: ftps
FTP_VERIFY_TLS: "1"
FTP_TLS_SERVER_NAME: hstgr.io
```

The connection address remains the existing `FTP_HOST` secret. `hstgr.io` is the
**TLS identity**, not a replacement FTP address or a new credential. It was
already recorded as the verified identity in
`docs/15p-launch/troubleshooting-release-2026-09-19.md` before this repair.

The deployment client uses `ssl.create_default_context()` and requires a trusted,
unexpired certificate whose SAN covers this hostname. It uses this same identity
and context for both control and protected passive data connections (`PROT P`).
The workflows no longer pin a particular leaf certificate. Normal renewals and
public-key changes under this verified identity do not need configuration edits.
Invalid, expired, wrong-hostname or untrusted certificates still fail closed.
This is conventional CA/hostname authentication, not a TLS-verification bypass.
The client retains optional pin support for other explicitly configured uses.

## Verification

Before changing the workflows, a credential-free STARTTLS handshake to the
previously documented endpoint `147.79.122.118:21`, with the system CA store and
expected hostname `hstgr.io`, succeeded. The certificate is issued by Let's
Encrypt YE1, covers `*.hstgr.io` and `hstgr.io`, and is valid October 12, 2026 to
January 10, 2027. An unrelated expected hostname was rejected. Its SHA-256 was
`e09df5ea7a0730280d0d898f18263905c0f980687dd90da5679837db40bdc493`;
this is diagnostic evidence, **not a new configured pin**.

Run the offline regression suite:

```sh
python3 scripts/test_deploy_tls.py
```

It creates an ephemeral test CA and two different valid server certificates,
proves both work without a pin update, and rejects expired, wrong-host and
untrusted certificates. It also checks workflow policy, missing identity failure,
and verified/encrypted control and data configuration. Tests run before uploads
in both deployment workflows and require Python's standard library plus OpenSSL.
Temporary test keys are destroyed; no production credentials/network are used.

For a credential-free real endpoint check:

```sh
openssl s_client -connect 147.79.122.118:21 -starttls ftp \
  -servername hstgr.io -verify_hostname hstgr.io -verify_return_error </dev/null
```

A future identity/CA failure still requires investigation. Do not learn a new
trusted hostname merely from a failing server's certificate, disable verification,
or blindly repin it. If Hostinger changes the endpoint or identity, establish the
new value through trusted provider/account information before changing config.
Deploy through reviewed GitHub Actions, not local FTP. Verify the exact release
run and canonical live pages separately; passing TLS tests is not a deployment.
