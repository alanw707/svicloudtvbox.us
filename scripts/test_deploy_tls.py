#!/usr/bin/env python3
"""Offline FTPS identity/renewal regression tests; no production credentials/network.

Uses temporary test CA/leaf certificates and local socket pairs. Requires openssl.
Run: python3 scripts/test_deploy_tls.py
"""
import ftplib
import hashlib
import socket
import ssl
import subprocess
import tempfile
import threading
import unittest
from pathlib import Path
from unittest.mock import MagicMock, patch

import deploy_theme as deploy

ROOT = Path(__file__).resolve().parents[1]
SERVER_NAME = "hstgr.io"


class DeploymentPolicyTests(unittest.TestCase):
    def test_all_deployment_steps_use_verified_hostname_not_leaf_pin(self):
        for name, count in [("deploy-theme.yml", 2), ("deploy.yml", 1)]:
            with self.subTest(workflow=name):
                text = (ROOT / ".github/workflows" / name).read_text()
                self.assertEqual(text.count("FTP_TLS_SERVER_NAME: hstgr.io"), count)
                self.assertEqual(text.count('FTP_VERIFY_TLS: "1"'), count)
                self.assertNotIn("FTP_TLS_CERT_SHA256:", text)
                self.assertNotIn("--no-verify-tls", text)
                self.assertIn("python3 scripts/test_deploy_tls.py", text)

    def test_connect_preserves_ca_hostname_and_encrypted_data(self):
        cfg = deploy.DeployConfig(host="127.0.0.1", user="fixture", password="fixture",
                                  tls_server_name=SERVER_NAME, connect_retries=1)
        with patch.object(deploy, "FTP_TLS") as factory:
            ftp = factory.return_value
            def login(*_):
                self.assertEqual(ftp.host, SERVER_NAME)
            ftp.login.side_effect = login
            self.assertIs(deploy._connect(cfg), ftp)
            context = factory.call_args.kwargs["context"]
            self.assertTrue(context.check_hostname)
            self.assertEqual(context.verify_mode, ssl.CERT_REQUIRED)
            ftp.connect.assert_called_once_with(host=cfg.host, port=21, timeout=45)
            ftp.prot_p.assert_called_once()

    def test_no_identity_fails_before_connection(self):
        cfg = deploy.DeployConfig(host="127.0.0.1", user="fixture", password="fixture")
        with patch.object(deploy, "FTP_TLS") as factory, self.assertRaises(SystemExit):
            deploy._connect(cfg)
        factory.assert_not_called()

    def test_data_channel_uses_same_verified_hostname(self):
        context = ssl.create_default_context()
        ftp = ftplib.FTP_TLS(context=context)
        ftp.host = SERVER_NAME
        setattr(ftp, "_prot_p", True)
        raw = MagicMock()
        with patch.object(ftplib.FTP, "ntransfercmd", return_value=(raw, 12)), \
             patch.object(context, "wrap_socket") as wrap:
            ftp.ntransfercmd("RETR fixture.txt")
            wrap.assert_called_once_with(raw, server_hostname=SERVER_NAME)
        self.assertTrue(context.check_hostname)
        self.assertEqual(context.verify_mode, ssl.CERT_REQUIRED)


class CertificateValidationTests(unittest.TestCase):
    @classmethod
    def setUpClass(cls):
        cls.temp = tempfile.TemporaryDirectory(prefix="ftps-test-")
        cls.addClassCleanup(cls.temp.cleanup)
        cls.dir = Path(cls.temp.name)
        def openssl(*args):
            subprocess.run(["openssl", *args], cwd=cls.dir, check=True,
                           stdout=subprocess.PIPE, stderr=subprocess.PIPE)
        openssl("req", "-x509", "-newkey", "rsa:2048", "-nodes", "-days", "2",
                "-keyout", "ca.key", "-out", "ca.pem", "-subj", "/CN=Test CA",
                "-addext", "basicConstraints=critical,CA:TRUE",
                "-addext", "keyUsage=critical,keyCertSign,cRLSign")
        (cls.dir / "leaf.ext").write_text(
            f"subjectAltName=DNS:{SERVER_NAME}\nbasicConstraints=critical,CA:FALSE\n"
            "keyUsage=critical,digitalSignature,keyEncipherment\nextendedKeyUsage=serverAuth\n")
        (cls.dir / "index.txt").touch()
        (cls.dir / "serial").write_text("03\n")
        (cls.dir / "ca.cnf").write_text(
            "[ca]\ndefault_ca=issuer\n[issuer]\ndatabase=index.txt\nnew_certs_dir=.\n"
            "certificate=ca.pem\nprivate_key=ca.key\nserial=serial\ndefault_md=sha256\n"
            "policy=names\n[names]\ncommonName=supplied\n")
        for serial, name in [(1, "before"), (2, "renewed"), (3, "expired")]:
            openssl("req", "-newkey", "rsa:2048", "-nodes", "-keyout", f"{name}.key",
                    "-out", f"{name}.csr", "-subj", f"/CN={SERVER_NAME}")
            if name == "expired":
                openssl("ca", "-batch", "-notext", "-config", "ca.cnf", "-in", "expired.csr",
                        "-out", "expired.pem", "-extfile", "leaf.ext",
                        "-startdate", "20000101000000Z", "-enddate", "20010101000000Z")
            else:
                openssl("x509", "-req", "-in", f"{name}.csr", "-CA", "ca.pem", "-CAkey", "ca.key",
                        "-set_serial", str(serial), "-days", "1", "-extfile", "leaf.ext",
                        "-out", f"{name}.pem")

    def handshake(self, name, *, hostname=SERVER_NAME, trusted=True):
        server = ssl.SSLContext(ssl.PROTOCOL_TLS_SERVER)
        server.load_cert_chain(self.dir / f"{name}.pem", self.dir / f"{name}.key")
        client = ssl.create_default_context(cafile=str(self.dir / "ca.pem") if trusted else None)
        self.assertTrue(client.check_hostname)
        self.assertEqual(client.verify_mode, ssl.CERT_REQUIRED)
        a, b = socket.socketpair()
        a.settimeout(5)
        b.settimeout(5)
        def serve():
            try:
                with server.wrap_socket(a, server_side=True):
                    pass
            except ssl.SSLError:
                pass  # Expected when the client rejects the certificate.
            finally:
                a.close()
        thread = threading.Thread(target=serve, daemon=True)
        thread.start()
        try:
            with client.wrap_socket(b, server_hostname=hostname) as conn:
                certificate = conn.getpeercert(binary_form=True)
                assert certificate is not None
                return hashlib.sha256(certificate).hexdigest()
        finally:
            b.close()
            thread.join(timeout=6)
            self.assertFalse(thread.is_alive(), "TLS fixture did not terminate")

    def test_certificate_renewal_needs_no_fingerprint_change(self):
        original = self.handshake("before")
        renewed = self.handshake("renewed")
        self.assertNotEqual(original, renewed)

    def test_wrong_hostname_is_rejected(self):
        with self.assertRaises(ssl.SSLCertVerificationError):
            self.handshake("renewed", hostname="unrelated.invalid")

    def test_untrusted_issuer_is_rejected(self):
        with self.assertRaises(ssl.SSLCertVerificationError):
            self.handshake("renewed", trusted=False)

    def test_expired_certificate_is_rejected(self):
        with self.assertRaises(ssl.SSLCertVerificationError):
            self.handshake("expired")


if __name__ == "__main__":
    unittest.main(verbosity=2)
