#!/usr/bin/env python3
"""Verify the safe production REST resources needed for a local theme fixture.

Reads WP_REST_ENDPOINT, WP_REST_USERNAME, and WP_REST_PASSWORD from the
process environment or a local .env file. It sends only GET requests for public
content endpoints and prints endpoint metadata/counts, never response content.
"""

from __future__ import annotations

import argparse
import base64
import json
import os
import subprocess
import sys
import urllib.error
import urllib.parse
import urllib.request
from dataclasses import dataclass
from pathlib import Path
from typing import Any


@dataclass(frozen=True)
class Endpoint:
    name: str
    path: str
    required: bool = True
    public_only: bool = False


class SameOriginRedirectHandler(urllib.request.HTTPRedirectHandler):
    """Allow redirects only when the scheme/host/port stays unchanged."""

    def redirect_request(self, req, fp, code, msg, headers, newurl):
        if url_origin(req.full_url) != url_origin(newurl):
            return None
        return super().redirect_request(req, fp, code, msg, headers, newurl)


def url_origin(url: str) -> tuple[str, str, int | None]:
    parsed = urllib.parse.urlsplit(url)
    scheme = parsed.scheme.lower()
    hostname = (parsed.hostname or "").lower()
    port = parsed.port
    if port is None:
        port = 443 if scheme == "https" else 80 if scheme == "http" else None
    return scheme, hostname, port


SAFE_OPENER = urllib.request.build_opener(SameOriginRedirectHandler())


ENDPOINTS = (
    Endpoint("pages", "/wp-json/wp/v2/pages?context=view&status=publish&per_page=1"),
    Endpoint("posts", "/wp-json/wp/v2/posts?context=view&status=publish&per_page=1"),
    Endpoint("media", "/wp-json/wp/v2/media?context=view&status=inherit&per_page=1", public_only=True),
    Endpoint("categories", "/wp-json/wp/v2/categories?context=view&per_page=1"),
    Endpoint("tags", "/wp-json/wp/v2/tags?context=view&per_page=1"),
    Endpoint("products", "/wp-json/wc/v3/products?status=publish&per_page=1"),
    Endpoint("product categories", "/wp-json/wc/v3/products/categories?per_page=1"),
    Endpoint("product tags", "/wp-json/wc/v3/products/tags?per_page=1"),
    Endpoint("product attributes", "/wp-json/wc/v3/products/attributes?per_page=1"),
    Endpoint("classic menus", "/wp-json/wp/v2/menus?context=view&per_page=1", required=False),
    Endpoint("classic menu items", "/wp-json/wp/v2/menu-items?context=view&per_page=1", required=False),
    Endpoint("block navigation",  "/wp-json/wp/v2/navigation?context=view&per_page=1", required=False),
    Endpoint("public display settings", "/wp-json/wp/v2/settings?_fields=blogname,blogdescription,show_on_front,page_on_front,page_for_posts,site_icon", required=False),
)


def load_env(path: Path) -> None:
    if not path.is_file():
        return
    for line in path.read_text(encoding="utf-8").splitlines():
        line = line.strip()
        if not line or line.startswith("#") or "=" not in line:
            continue
        key, value = line.split("=", 1)
        if key and key not in os.environ:
            os.environ[key] = value.strip().strip("'\"")


def site_root(endpoint: str) -> str:
    parsed = urllib.parse.urlsplit(endpoint)
    if parsed.scheme not in {"http", "https"} or not parsed.netloc:
        raise ValueError("WP_REST_ENDPOINT must be an absolute http(s) URL")
    marker = "/wp-json"
    path = parsed.path.split(marker, 1)[0] if marker in parsed.path else parsed.path.rstrip("/")
    return urllib.parse.urlunsplit((parsed.scheme, parsed.netloc, path.rstrip("/"), "", ""))


def auth_header(username: str, password: str) -> str:
    token = base64.b64encode(f"{username}:{password}".encode("utf-8")).decode("ascii")
    return f"Basic {token}"


def validate_endpoint_override(override: str | None, configured: str, option: str) -> None:
    if not override:
        return
    if not configured:
        raise ValueError(f"{option} requires a configured WP_REST_ENDPOINT when credentials are loaded")
    if site_root(override) != site_root(configured):
        raise ValueError(f"{option} must match the configured WP_REST_ENDPOINT when credentials are loaded")


def request(url: str, authorization: str) -> tuple[int | None, dict[str, str], str | None]:
    headers = {"Accept": "application/json", "User-Agent": "svic-public-theme-fixture-audit/1.0"}
    if authorization:
        headers["Authorization"] = authorization
    req = urllib.request.Request(url, headers=headers)
    try:
        with SAFE_OPENER.open(req, timeout=30) as response:
            return response.status, {key.lower(): value for key, value in response.headers.items()}, None
    except urllib.error.HTTPError as error:
        return error.code, {key.lower(): value for key, value in error.headers.items()}, None
    except urllib.error.URLError as error:
        return None, {}, str(error.reason)


def route_names(root: str, authorization: str) -> set[str]:
    status, _, error = request(f"{root}/wp-json/", authorization)
    if status != 200:
        raise RuntimeError(f"REST index unavailable (HTTP {status or 'network error'}{': ' + error if error else ''})")

    req = urllib.request.Request(f"{root}/wp-json/", headers={"Authorization": authorization, "Accept": "application/json", "User-Agent": "svic-public-theme-fixture-audit/1.0"})
    with SAFE_OPENER.open(req, timeout=30) as response:
        payload: Any = json.loads(response.read().decode(response.headers.get_content_charset() or "utf-8"))
    routes = payload.get("routes", {}) if isinstance(payload, dict) else {}
    return set(routes) if isinstance(routes, dict) else set()


def local_count(post_type: str) -> str:
    status = "inherit" if post_type == "attachment" else "publish"
    command = ["docker", "exec", "svicloud10p-wp", "wp", "post", "list", f"--post_type={post_type}", f"--post_status={status}", "--format=count", "--allow-root"]
    completed = subprocess.run(command, capture_output=True, text=True, check=False)
    return completed.stdout.strip() if completed.returncode == 0 else "unavailable"


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--env-file", default=".env", help="Credential environment file (default: .env)")
    parser.add_argument("--base-url", help="Override WP_REST_ENDPOINT")
    args = parser.parse_args()

    load_env(Path(args.env_file))
    configured_endpoint = os.environ.get("WP_REST_ENDPOINT", "")
    endpoint = args.base_url or configured_endpoint
    username = os.environ.get("WP_REST_USERNAME", "")
    password = os.environ.get("WP_REST_PASSWORD", "")
    if not endpoint or not username or not password:
        print("Missing WP_REST_ENDPOINT, WP_REST_USERNAME, or WP_REST_PASSWORD.", file=sys.stderr)
        return 2

    try:
        validate_endpoint_override(args.base_url, configured_endpoint, "--base-url")
        root = site_root(endpoint)
        authorization = auth_header(username, password)
        routes = route_names(root, authorization)
    except (RuntimeError, ValueError, urllib.error.URLError, json.JSONDecodeError) as error:
        print(f"REST audit failed: {error}", file=sys.stderr)
        return 2

    print("Production REST public-fixture audit")
    print(f"- site: {root}")
    print("- private records requested: none; settings request is field-limited to display fields")
    print("- endpoint results:")
    failures: list[str] = []
    for endpoint_spec in ENDPOINTS:
        status, headers, error = request(f"{root}{endpoint_spec.path}", "" if endpoint_spec.public_only else authorization)
        total = headers.get("x-wp-total", "n/a")
        route_path = endpoint_spec.path.split("?", 1)[0].removeprefix("/wp-json")
        route_available = "yes" if route_path in routes else "no"
        suffix = f"network={error}" if error else f"http={status} total={total} route={route_available}"
        print(f"  - {endpoint_spec.name}: {suffix}")
        if endpoint_spec.required and status != 200:
            failures.append(endpoint_spec.name)

    print("- local public-content counts:")
    for post_type in ("page", "post", "product", "attachment"):
        print(f"  - {post_type}: {local_count(post_type)}")
    menu = subprocess.run(["docker", "exec", "svicloud10p-wp", "wp", "menu", "list", "--format=count", "--allow-root"], capture_output=True, text=True, check=False)
    print(f"  - menus: {menu.stdout.strip() if menu.returncode == 0 else 'unavailable'}")

    if failures:
        print(f"Required public fixture endpoints unavailable: {', '.join(failures)}", file=sys.stderr)
        return 2
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
