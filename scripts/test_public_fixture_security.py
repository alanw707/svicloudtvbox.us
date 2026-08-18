#!/usr/bin/env python3
"""Regression tests for public-fixture credential and redirect boundaries."""

from __future__ import annotations

import importlib.util
import sys
import threading
import urllib.error
from http.server import BaseHTTPRequestHandler, HTTPServer
from pathlib import Path
import unittest


ROOT = Path(__file__).resolve().parent


def load_module(name: str, path: Path):
    spec = importlib.util.spec_from_file_location(name, path)
    if spec is None or spec.loader is None:
        raise RuntimeError(f"Could not load {path}")
    module = importlib.util.module_from_spec(spec)
    sys.modules[name] = module
    spec.loader.exec_module(module)
    return module


SYNC = load_module("svic_sync_fixture", ROOT / "sync_public_theme_fixture.py")
AUDIT = load_module("svic_audit_fixture", ROOT / "audit_public_theme_fixture_rest.py")


class _TargetHandler(BaseHTTPRequestHandler):
    authorization: str | None = None

    def do_GET(self):  # noqa: N802 - stdlib handler API
        type(self).authorization = self.headers.get("Authorization")
        self.send_response(200)
        self.send_header("Content-Type", "application/json")
        self.end_headers()
        self.wfile.write(b"{}")

    def log_message(self, *_args):
        return


class _RedirectHandler(BaseHTTPRequestHandler):
    target_port: int

    def do_GET(self):  # noqa: N802 - stdlib handler API
        self.send_response(302)
        self.send_header("Location", f"http://127.0.0.1:{self.target_port}/target")
        self.end_headers()

    def log_message(self, *_args):
        return


class PublicFixtureSecurityTests(unittest.TestCase):
    def setUp(self):
        _TargetHandler.authorization = None
        self.target = HTTPServer(("127.0.0.1", 0), _TargetHandler)
        _RedirectHandler.target_port = self.target.server_port
        self.redirect = HTTPServer(("127.0.0.1", 0), _RedirectHandler)
        for server in (self.target, self.redirect):
            threading.Thread(target=server.serve_forever, daemon=True).start()

    def tearDown(self):
        self.redirect.shutdown()
        self.target.shutdown()
        self.redirect.server_close()
        self.target.server_close()

    def test_sync_client_rejects_cross_origin_redirect_before_forwarding_auth(self):
        client = SYNC.RestClient(f"http://127.0.0.1:{self.redirect.server_port}", "dummy", "secret")
        with self.assertRaises(RuntimeError):
            client.get("/start")
        self.assertIsNone(_TargetHandler.authorization)

    def test_audit_request_rejects_cross_origin_redirect_before_forwarding_auth(self):
        authorization = AUDIT.auth_header("dummy", "secret")
        status, _, error = AUDIT.request(
            f"http://127.0.0.1:{self.redirect.server_port}/start", authorization
        )
        self.assertEqual(status, 302)
        self.assertIsNone(error)
        self.assertIsNone(_TargetHandler.authorization)

    def test_endpoint_overrides_must_match_configured_source(self):
        with self.assertRaises(ValueError):
            SYNC.validate_endpoint_override(
                "https://attacker.invalid/wp-json", "https://production.invalid/wp-json", "--source-url"
            )
        with self.assertRaises(ValueError):
            AUDIT.validate_endpoint_override(
                "https://attacker.invalid/wp-json", "https://production.invalid/wp-json", "--base-url"
            )
        with self.assertRaises(ValueError):
            SYNC.validate_endpoint_override("https://attacker.invalid/wp-json", "", "--source-url")
        with self.assertRaises(ValueError):
            AUDIT.validate_endpoint_override("https://attacker.invalid/wp-json", "", "--base-url")


if __name__ == "__main__":
    unittest.main()
