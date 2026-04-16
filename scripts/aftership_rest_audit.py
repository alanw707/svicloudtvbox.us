#!/usr/bin/env python3
"""Audit a live WordPress/WooCommerce site for AfterShip/tracking data using REST + Application Password auth.

Usage:
  python3 scripts/aftership_rest_audit.py --base-url https://example.com --username admin

Auth:
  Set WP_APP_PASSWORD in the environment, or the script will prompt securely.

What it checks:
  - REST namespaces/routes that look tracking-related
  - active plugins via /wp/v2/plugins (if permitted)
  - recent WooCommerce orders via /wc/v3/orders (if permitted)
  - candidate tracking/delivery meta keys in order meta_data
  - candidate shipment-related order notes

This script uses only Python's standard library.
"""

from __future__ import annotations

import argparse
import base64
import getpass
import json
import os
import re
import sys
import urllib.error
import urllib.parse
import urllib.request
from collections import Counter
from typing import Any, Dict, Iterable, List, Optional, Tuple

KEYWORD_RE = re.compile(r"after|track|shipment|deliver|fulfill|carrier|transit|provider|ship", re.I)
SAFE_VALUE_RE = re.compile(
    r"^(aftership|after_ship|track123|trackingmore|shippo|pirateship|pirate_ship|usps|ups|fedex|dhl|ontrac|lasership|in_transit|delivered|out_for_delivery|exception|pending|unknown|shipment|tracking|fulfilled|complete|completed|yes|no|true|false|manual|api|webhook|test|live|sandbox)$",
    re.I,
)
STATE_RE = re.compile(r"^[A-Z]{2}$")


def build_auth_header(username: str, app_password: str) -> str:
    token = base64.b64encode(f"{username}:{app_password}".encode("utf-8")).decode("ascii")
    return f"Basic {token}"


def api_get(base_url: str, path: str, auth_header: str) -> Tuple[Optional[Any], Optional[str], Optional[int]]:
    url = urllib.parse.urljoin(base_url.rstrip("/") + "/", path.lstrip("/"))
    request = urllib.request.Request(url)
    request.add_header("Authorization", auth_header)
    request.add_header("Accept", "application/json")
    request.add_header("User-Agent", "svic-aftership-audit/1.0")

    try:
        with urllib.request.urlopen(request, timeout=30) as response:
            charset = response.headers.get_content_charset() or "utf-8"
            body = response.read().decode(charset)
            return json.loads(body), None, response.status
    except urllib.error.HTTPError as exc:
        body = exc.read().decode("utf-8", errors="replace")
        return None, body, exc.code
    except urllib.error.URLError as exc:
        return None, str(exc), None


def redact_text(text: str) -> str:
    text = re.sub(r"https?://\S+", "[redacted-url]", text, flags=re.I)
    text = re.sub(r"[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}", "[redacted-email]", text, flags=re.I)
    text = re.sub(r"\b(?:\d[ -]?){10,}\b", "[redacted-number]", text)
    text = re.sub(r"\b[A-Z]{2}\d{9}[A-Z]{2}\b", "[redacted-tracking]", text, flags=re.I)
    text = re.sub(r"\b[A-Z0-9]{10,}\b", "[redacted-token]", text)
    text = text.strip()
    if len(text) > 180:
        text = text[:177] + "..."
    return text


def format_meta_value(meta_key: str, value: Any) -> str:
    if value is None:
        return "(null)"
    if isinstance(value, bool):
        return "true" if value else "false"
    if isinstance(value, (int, float)):
        if 946684800 <= int(value) <= 2147483647:
            return f"{int(value)} (unix-like)"
        return str(value)
    if isinstance(value, dict):
        return f"[object keys={len(value)}]"
    if isinstance(value, list):
        return f"[array count={len(value)}]"

    raw = str(value).strip()
    if not raw:
        return "(empty)"
    if SAFE_VALUE_RE.match(raw):
        return raw.lower()
    if STATE_RE.match(raw):
        return raw.upper()
    if re.match(r"^\d{4}-\d{2}-\d{2}(?:[ T]\d{2}:\d{2}:\d{2})?$", raw):
        return raw
    if re.match(r"^[a-z0-9 _:\-]{1,40}$", raw, flags=re.I) and not re.search(r"\d{5,}", raw):
        return raw
    return f"[redacted len={len(raw)}]"


def print_section(title: str) -> None:
    print(f"\n## {title}")


def plugin_matches(plugin: Dict[str, Any]) -> bool:
    haystack = " ".join(str(plugin.get(key, "")) for key in ("plugin", "name", "description", "author", "status"))
    return bool(KEYWORD_RE.search(haystack))


def summarize_plugins(payload: Any) -> None:
    print_section("Active shipping / tracking candidate plugins")
    if not isinstance(payload, list):
        print("- plugins endpoint unavailable or unexpected response")
        return

    matches = [plugin for plugin in payload if isinstance(plugin, dict) and plugin_matches(plugin)]
    if not matches:
        print("- none matched after|track|shipment|deliver|fulfill|carrier|ship")
        return

    for plugin in matches:
        name = str(plugin.get("name") or plugin.get("plugin") or "unknown")
        version = str(plugin.get("version") or "unknown")
        plugin_file = str(plugin.get("plugin") or plugin.get("slug") or "unknown")
        status = str(plugin.get("status") or "unknown")
        print(f"- {name} ({version}) :: {plugin_file} :: status={status}")


def summarize_rest_index(payload: Any) -> None:
    print_section("Candidate REST namespaces / routes")
    if not isinstance(payload, dict):
        print("- REST index unavailable or unexpected response")
        return

    namespaces = payload.get("namespaces")
    routes = payload.get("routes")

    namespace_matches: List[str] = []
    route_matches: List[str] = []

    if isinstance(namespaces, list):
        namespace_matches = [str(ns) for ns in namespaces if isinstance(ns, str) and KEYWORD_RE.search(ns)]

    if isinstance(routes, dict):
        route_matches = [str(route) for route in routes.keys() if KEYWORD_RE.search(str(route))]

    if not namespace_matches and not route_matches:
        print("- none")
        return

    if namespace_matches:
        print("- namespaces:")
        for ns in namespace_matches[:40]:
            print(f"  - {ns}")
    if route_matches:
        print("- routes:")
        for route in route_matches[:40]:
            print(f"  - {route}")


def get_meta_candidates(order: Dict[str, Any]) -> List[Tuple[str, Any]]:
    result: List[Tuple[str, Any]] = []
    meta_data = order.get("meta_data", [])
    if not isinstance(meta_data, list):
        return result

    for meta in meta_data:
        if not isinstance(meta, dict):
            continue
        key = str(meta.get("key") or "")
        if not KEYWORD_RE.search(key):
            continue
        result.append((key, meta.get("value")))
    return result


def fetch_order_notes(base_url: str, auth_header: str, order_id: int) -> List[Dict[str, Any]]:
    payload, error, code = api_get(base_url, f"/wp-json/wc/v3/orders/{order_id}/notes?per_page=20", auth_header)
    if payload is None:
        return [{"error": f"HTTP {code}: {redact_text(error or 'unknown error')}"}]
    if not isinstance(payload, list):
        return []
    return [note for note in payload if isinstance(note, dict)]


def summarize_orders(base_url: str, auth_header: str, orders: Any, title: str) -> None:
    print_section(title)
    if not isinstance(orders, list):
        print("- WooCommerce orders endpoint unavailable or unexpected response")
        return
    if not orders:
        print("- no orders returned")
        return

    meta_key_counter: Counter[str] = Counter()
    candidate_order_count = 0

    for order in orders:
        if not isinstance(order, dict):
            continue
        candidates = get_meta_candidates(order)
        for key, _ in candidates:
            meta_key_counter[key] += 1
        if candidates:
            candidate_order_count += 1

    print(f"- orders_returned: {len(orders)}")
    print(f"- orders_with_candidate_meta: {candidate_order_count}")
    if meta_key_counter:
        print("- candidate_meta_keys:")
        for key, count in meta_key_counter.most_common(40):
            print(f"  - {key}: {count}")
    else:
        print("- candidate_meta_keys: none")

    for order in orders[:12]:
        if not isinstance(order, dict):
            continue
        order_id = int(order.get("id") or 0)
        status = str(order.get("status") or "unknown")
        created = str(order.get("date_created") or "")
        completed = str(order.get("date_completed") or "") or "(none)"
        shipping = order.get("shipping") if isinstance(order.get("shipping"), dict) else {}
        billing = order.get("billing") if isinstance(order.get("billing"), dict) else {}
        shipping_city = str(shipping.get("city") or "")
        shipping_state = str(shipping.get("state") or "")
        billing_city = str(billing.get("city") or "")
        billing_state = str(billing.get("state") or "")

        print(f"\n### Order {order_id}")
        print(f"- status: {status}")
        print(f"- created: {created or '(none)'}")
        print(f"- completed: {completed}")
        print(f"- shipping_city_present: {'yes' if shipping_city else 'no'}")
        print(f"- shipping_state: {shipping_state if STATE_RE.match(shipping_state) else ('(empty)' if not shipping_state else '[redacted]')}")
        print(f"- billing_city_present: {'yes' if billing_city else 'no'}")
        print(f"- billing_state: {billing_state if STATE_RE.match(billing_state) else ('(empty)' if not billing_state else '[redacted]')}")

        candidates = get_meta_candidates(order)
        if not candidates:
            print("- candidate_meta: none")
        else:
            print("- candidate_meta:")
            for key, value in candidates:
                print(f"  - {key} = {format_meta_value(key, value)}")

        if order_id <= 0:
            print("- candidate_notes: skipped")
            continue

        notes = fetch_order_notes(base_url, auth_header, order_id)
        candidate_note_lines: List[str] = []
        for note in notes:
            if "error" in note:
                candidate_note_lines.append(str(note["error"]))
                continue
            content = str(note.get("note") or "")
            if not KEYWORD_RE.search(content):
                continue
            date_created = str(note.get("date_created") or "")
            candidate_note_lines.append(f"{date_created} :: {redact_text(content)}")

        if not candidate_note_lines:
            print("- candidate_notes: none")
        else:
            print("- candidate_notes:")
            for line in candidate_note_lines[:8]:
                print(f"  - {line}")


def main() -> int:
    parser = argparse.ArgumentParser(description="Audit a live WordPress site for AfterShip/tracking data using REST + Application Password auth.")
    parser.add_argument("--base-url", required=True, help="WordPress site base URL, e.g. https://svicloudtvbox.us")
    parser.add_argument("--username", required=True, help="WordPress username for the Application Password")
    parser.add_argument("--order-count", type=int, default=12, help="How many recent completed orders to inspect (default: 12)")
    args = parser.parse_args()

    app_password = os.environ.get("WP_APP_PASSWORD") or getpass.getpass("WordPress Application Password: ").strip()
    if not app_password:
        print("No application password provided.", file=sys.stderr)
        return 1

    auth_header = build_auth_header(args.username, app_password)
    base_url = args.base_url.rstrip("/")

    print_section("Environment")
    print(f"- base_url: {base_url}")
    print(f"- username: {args.username}")
    print(f"- order_count: {args.order_count}")

    rest_index, error, code = api_get(base_url, "/wp-json/", auth_header)
    if rest_index is None:
        print_section("REST bootstrap")
        print(f"- failed to load /wp-json/: HTTP {code or 'n/a'} :: {redact_text(error or 'unknown error')}")
        return 1

    summarize_rest_index(rest_index)

    plugins_payload, plugins_error, plugins_code = api_get(base_url, "/wp-json/wp/v2/plugins?per_page=100&status=active", auth_header)
    if plugins_payload is None:
        print_section("Active shipping / tracking candidate plugins")
        print(f"- plugins endpoint unavailable: HTTP {plugins_code or 'n/a'} :: {redact_text(plugins_error or 'unknown error')}")
    else:
        summarize_plugins(plugins_payload)

    orders_path = f"/wp-json/wc/v3/orders?status=completed&per_page={max(1, min(args.order_count, 20))}&orderby=date&order=desc"
    orders_payload, orders_error, orders_code = api_get(base_url, orders_path, auth_header)
    if orders_payload is None:
        print_section("Recent completed orders")
        print(f"- WooCommerce orders endpoint unavailable: HTTP {orders_code or 'n/a'} :: {redact_text(orders_error or 'unknown error')}")
    else:
        summarize_orders(base_url, auth_header, orders_payload, "Recent completed orders")

    processing_path = f"/wp-json/wc/v3/orders?status=processing&per_page={max(1, min(args.order_count, 10))}&orderby=date&order=desc"
    processing_payload, processing_error, processing_code = api_get(base_url, processing_path, auth_header)
    if processing_payload is None:
        print_section("Recent processing orders")
        print(f"- WooCommerce processing orders endpoint unavailable: HTTP {processing_code or 'n/a'} :: {redact_text(processing_error or 'unknown error')}")
    else:
        summarize_orders(base_url, auth_header, processing_payload, "Recent processing orders")

    print("\n## Audit complete")
    print("Review this output for: active AfterShip plugin, candidate REST namespaces, order meta keys, delivered/status fields, and whether recent orders expose usable shipment data through REST.")
    print("If plugin/order data is still not visible, the next no-shell fallback is a temporary authenticated custom REST endpoint or manual wp-admin audit.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
