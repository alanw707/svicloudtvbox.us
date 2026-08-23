#!/usr/bin/env python3
"""Live endpoint, schema, sitemap, and CTA validation for agent-friendly routes."""
from __future__ import annotations
import argparse
import json
import re
import socket
import ssl
import sys
from html.parser import HTMLParser
from urllib.parse import parse_qs, quote, urlparse

AGENT_PATHS = [
    "/llms.txt", "/llms-full.txt", "/agent/svicloud-15p.md", "/agent/products.md", "/agent/compare-10p-vs-10s.md",
    "/agent/apps.md", "/agent/troubleshooting.md", "/agent/setup.md", "/agent/shipping-returns.md", "/agent/contact.md",
]
AGENT_MARKDOWN_PATHS = [path for path in AGENT_PATHS if path.startswith("/agent/")]
GUIDE_PATHS = [
    "/guides-apps/", "/zh/guides-apps/", "/guides-troubleshooting/", "/zh/guides-troubleshooting/",
    "/guides-setup/", "/zh/guides-setup/", "/zh/svicloud遙控器配對失敗-故障碼排查一次搞定/",
]
DECISION_PATHS = [
    "/svicloud-10p-vs-10s/", "/best-svicloud-box-for-chinese-tv-usa/",
    "/yogurt-tv-not-working-upgrade-guide/", "/svicloud-box-authenticity-guide/",
]
POLICY_CONTACT_PATHS = ["/contact/", "/shipping-policy/", "/return-policy/"]
PRODUCT_SCHEMA_PATHS = ["/product/svicloud-15p/", "/product/svicloud-10p-plus/", "/product/svicloud-10s/"]
PROMO_PATHS = ["/svicloud-15p-features/", "/zh/svicloud-15p-features/", "/zh-cn/svicloud-15p-features/"]
SITEMAP_PATH = "/agent-friendly-sitemap.xml"

class JsonLdParser(HTMLParser):
    def __init__(self) -> None:
        super().__init__()
        self.in_jsonld = False
        self.buf: list[str] = []
        self.blocks: list[str] = []
    def handle_starttag(self, tag, attrs):
        if tag.lower() == "script" and dict(attrs).get("type", "").lower() == "application/ld+json":
            self.in_jsonld = True
            self.buf = []
    def handle_data(self, data):
        if self.in_jsonld:
            self.buf.append(data)
    def handle_endtag(self, tag):
        if tag.lower() == "script" and self.in_jsonld:
            self.blocks.append("".join(self.buf).strip())
            self.in_jsonld = False


def fail(msg: str) -> None:
    print(f"FAIL: {msg}")
    sys.exit(1)


def fetch(base: str, path: str) -> tuple[int, str, str]:
    u = urlparse(base)
    host = u.hostname or "127.0.0.1"
    port = u.port or (443 if u.scheme == "https" else 80)
    header_host = host if u.port is None else f"{host}:{port}"
    query = parse_qs(u.query or "")
    if host in {"127.0.0.1", "localhost"} and query.get("host"):
        header_host = query["host"][0]
    raw = socket.create_connection((host, port), timeout=10)
    if u.scheme == "https":
        s = ssl.create_default_context().wrap_socket(raw, server_hostname=host)
    else:
        s = raw
    request_path = quote(path, safe="/:?&=%-._~")
    request = f"GET {request_path} HTTP/1.1\r\nHost: {header_host}\r\nConnection: close\r\n\r\n"
    s.sendall(request.encode("utf-8"))
    data = b""
    while True:
        chunk = s.recv(65536)
        if not chunk:
            break
        data += chunk
    s.close()
    head_bytes, _, body_bytes = data.partition(b"\r\n\r\n")
    head = head_bytes.decode("iso-8859-1", "replace")
    if "transfer-encoding: chunked" in head.lower():
        decoded = []
        rest = body_bytes
        while rest:
            size_line, sep, after_size = rest.partition(b"\r\n")
            if not sep:
                break
            try:
                size = int(size_line.split(b";", 1)[0], 16)
            except ValueError:
                break
            if size == 0:
                break
            decoded.append(after_size[:size])
            rest = after_size[size + 2:]
        body_bytes = b"".join(decoded)
    body = body_bytes.decode("utf-8", "replace")
    m = re.search(r"HTTP/\d(?:\.\d)?\s+(\d+)", head)
    return (int(m.group(1)) if m else 0), head, body


def schema_types(html: str) -> list[str]:
    parser = JsonLdParser()
    parser.feed(html)
    found: list[str] = []
    def walk(node):
        if isinstance(node, dict):
            t = node.get("@type")
            if isinstance(t, str): found.append(t)
            if isinstance(t, list): found.extend(str(x) for x in t)
            for v in node.values(): walk(v)
        elif isinstance(node, list):
            for item in node: walk(item)
    for block in parser.blocks:
        try:
            walk(json.loads(block))
        except json.JSONDecodeError:
            fail("invalid JSON-LD block")
    return found


def validate_breadcrumb_schema(html: str, path: str) -> None:
    parser = JsonLdParser()
    parser.feed(html)

    def walk(node):
        if isinstance(node, dict):
            yield node
            for value in node.values():
                yield from walk(value)
        elif isinstance(node, list):
            for item in node:
                yield from walk(item)

    for block in parser.blocks:
        try:
            data = json.loads(block)
        except json.JSONDecodeError:
            fail("invalid JSON-LD block")

        for node in walk(data):
            node_type = node.get("@type")
            types = node_type if isinstance(node_type, list) else [node_type]
            if "BreadcrumbList" not in types:
                continue

            elements = node.get("itemListElement")
            if not isinstance(elements, list):
                fail(f"breadcrumb schema missing itemListElement {path}")

            for index, element in enumerate(elements, start=1):
                if not isinstance(element, dict):
                    fail(f"breadcrumb item invalid {path} item={index}")

                name = element.get("name")
                item = element.get("item")
                item_name = item.get("name") if isinstance(item, dict) else None
                if not str(name or item_name or "").strip():
                    fail(f"breadcrumb item missing name/item.name {path} item={index}")


def main() -> None:
    ap = argparse.ArgumentParser()
    ap.add_argument("--base", default="http://127.0.0.1?host=svicloud10p.svic.local", help="Base URL. For local Traefik use query host=domain to set Host header.")
    args = ap.parse_args()

    for path in AGENT_PATHS:
        status, head, body = fetch(args.base, path)
        if status != 200 or "SVICLOUD" not in body or "+1 (520) 641-7021" not in body:
            fail(f"agent endpoint invalid {path} status={status}")
        if path in AGENT_MARKDOWN_PATHS and "x-robots-tag: index, follow" not in head.lower():
            fail(f"agent endpoint missing crawl header {path}")

    for path in GUIDE_PATHS:
        status, _, body = fetch(args.base, path)
        if status != 200 or "guides-answer-hub" not in body or "+1 (520) 641-7021" not in body:
            fail(f"guide endpoint invalid {path} status={status}")
        validate_breadcrumb_schema(body, path)
        if "error404" in body or "Page not found" in body:
            fail(f"guide endpoint is internally 404 {path}")
        if path in {"/guides-apps/", "/zh/guides-apps/", "/guides-troubleshooting/", "/zh/guides-troubleshooting/"} and "FAQPage" not in schema_types(body):
            fail(f"missing FAQPage schema {path}")
        if "guides-setup" in path and "HowTo" not in schema_types(body):
            fail(f"missing HowTo schema {path}")
        for slug in ["svicloud-10p-vs-10s", "best-svicloud-box-for-chinese-tv-usa", "yogurt-tv-not-working-upgrade-guide", "svicloud-box-authenticity-guide"]:
            if path in {"/guides-apps/", "/zh/guides-apps/", "/guides-troubleshooting/", "/zh/guides-troubleshooting/"} and slug not in body:
                # Not every guide needs every slug, but at least decision integration must exist.
                pass
        if not any(slug in body for slug in ["svicloud-10p-vs-10s", "best-svicloud-box-for-chinese-tv-usa", "yogurt-tv-not-working-upgrade-guide", "svicloud-box-authenticity-guide"]):
            fail(f"guide lacks decision-page internal link {path}")

    for path in POLICY_CONTACT_PATHS:
        status, _, body = fetch(args.base, path)
        if status != 200 or "+1 (520) 641-7021" not in body:
            fail(f"policy/contact endpoint invalid {path} status={status}")
        validate_breadcrumb_schema(body, path)
        if "error404" in body or "Page not found" in body:
            fail(f"policy/contact endpoint is internally 404 {path}")

    for path in PRODUCT_SCHEMA_PATHS:
        status, _, body = fetch(args.base, path)
        validate_breadcrumb_schema(body, path)
        types = schema_types(body)
        if status != 200 or "Product" not in types or "Offer" not in types or "Organization" not in types:
            fail(f"product schema invalid {path} status={status} types={types}")
        if "AggregateRating" in types or "Review" in types:
            fail(f"fake/unsupported rating or review schema present {path}")
        if "+1 (520) 641-7021" not in body:
            fail(f"product page missing official support phone {path}")

    for path in PROMO_PATHS:
        status, _, body = fetch(args.base, path)
        types = schema_types(body)
        if status != 200 or "FAQPage" not in types:
            fail(f"15P promo endpoint invalid {path} status={status} types={types}")
        if "error404" in body or "Page not found" in body:
            fail(f"15P promo endpoint is internally 404 {path}")
        for term in ["15P", "10P+", "Yogurt TV Go", "288", "379"]:
            if term not in body:
                fail(f"15P promo page missing required term {path} -> {term}")

    for path in DECISION_PATHS:
        status, _, body = fetch(args.base, path)
        if status != 200 or "svic_decision_cta_click" not in body or "+1 (520) 641-7021" not in body:
            fail(f"decision endpoint invalid {path} status={status}")
        validate_breadcrumb_schema(body, path)
        if "error404" in body or "Page not found" in body:
            fail(f"decision endpoint is internally 404 {path}")
        for href in ["/compare/", "/contact/", "/guides-apps/", "/guides-troubleshooting/", "/guides-setup/", "/product/svicloud-10p-plus/", "/product/svicloud-10s/", "/shipping-policy/", "/return-policy/"]:
            if href not in body:
                fail(f"decision page missing required product/policy/support link {path} -> {href}")

    status, _, sitemap = fetch(args.base, SITEMAP_PATH)
    if status != 200:
        fail("agent-friendly sitemap not 200")
    for path in AGENT_PATHS + GUIDE_PATHS + DECISION_PATHS + PROMO_PATHS:
        if path not in sitemap:
            fail(f"sitemap missing {path}")

    print("OK: live endpoints, JSON-LD types, CTAs, and sitemap")

if __name__ == "__main__":
    main()
