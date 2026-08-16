#!/usr/bin/env python3
"""
Audit live localized SVICLOUD pages for untranslated body content.

The older blog coverage report checks local title registry keys. This script
checks what visitors and search engines see on live zh/zh-cn URLs.

Usage:
  python3 scripts/audit_live_translation_coverage.py
  python3 scripts/audit_live_translation_coverage.py --fail
  python3 scripts/audit_live_translation_coverage.py --kind posts --limit 20
"""

from __future__ import annotations

import argparse
import re
import sys
import time
import xml.etree.ElementTree as ET
from dataclasses import dataclass
from html import unescape
from typing import Iterable
from urllib.parse import urlparse, urlunparse

import requests
from bs4 import BeautifulSoup


DEFAULT_SITEMAP = "https://svicloudtvbox.us/sitemap_index.xml"
LOCALES = ("zh", "zh-cn")
ALLOW_LATIN_TERMS = {
    "svicloud",
    "tv",
    "box",
    "usa",
    "us",
    "android",
    "wifi",
    "wi",
    "fi",
    "4k",
    "hdr",
    "hdmi",
    "usb",
    "ram",
    "gb",
    "app",
    "apps",
    "google",
    "youtube",
    "netflix",
    "evpad",
    "ubox",
    "pro",
    "plus",
    "10p",
    "10s",
}


@dataclass(frozen=True)
class PageAudit:
    url: str
    status: int
    cjk_chars: int
    latin_words: int
    latin_ratio: float
    verdict: str
    snippets: tuple[str, ...]


def fetch_text(url: str, session: requests.Session) -> str:
    response = session.get(url, timeout=20)
    response.raise_for_status()
    return response.text


def sitemap_locs(xml_text: str) -> list[str]:
    root = ET.fromstring(xml_text)
    ns = {"sm": "http://www.sitemaps.org/schemas/sitemap/0.9"}
    return [loc.text.strip() for loc in root.findall(".//sm:loc", ns) if loc.text]


def collect_urls(sitemap_url: str, session: requests.Session, kind: str) -> list[str]:
    root_locs = sitemap_locs(fetch_text(sitemap_url, session))
    sitemap_urls = [url for url in root_locs if url.endswith("-sitemap.xml")]
    if kind != "all":
        sitemap_name = {
            "posts": "post-sitemap.xml",
            "pages": "page-sitemap.xml",
            "products": "product-sitemap.xml",
            "categories": "category-sitemap.xml",
        }[kind]
        sitemap_urls = [url for url in sitemap_urls if url.endswith(sitemap_name)]

    urls: list[str] = []
    seen: set[str] = set()
    for child_sitemap in sitemap_urls:
        for url in sitemap_locs(fetch_text(child_sitemap, session)):
            if url in seen:
                continue
            seen.add(url)
            urls.append(url)
    return urls


def localized_url(base_url: str, locale: str) -> str:
    parsed = urlparse(base_url)
    path = parsed.path
    if path.startswith(f"/{locale}/"):
        return base_url
    if path in ("", "/"):
        localized_path = f"/{locale}/"
    else:
        localized_path = f"/{locale}{path}"
    return urlunparse(parsed._replace(path=localized_path))


def visible_text(html: str) -> str:
    soup = BeautifulSoup(html, "html.parser")
    for tag in soup(["script", "style", "noscript", "svg", "header", "footer", "nav"]):
        tag.decompose()
    body = soup.select_one("main") or soup.select_one("article") or soup.body or soup
    text = unescape(body.get_text(" ", strip=True))
    return re.sub(r"\s+", " ", text).strip()


def strip_allowed_latin_terms(text: str) -> str:
    def repl(match: re.Match[str]) -> str:
        word = match.group(0).lower().strip("-_")
        compact = word.replace("-", "").replace("_", "")
        if word in ALLOW_LATIN_TERMS or compact in ALLOW_LATIN_TERMS:
            return " "
        if re.fullmatch(r"[0-9a-z]*[0-9][0-9a-z]*", word):
            return " "
        return match.group(0)

    return re.sub(r"[A-Za-z0-9][A-Za-z0-9+._-]*", repl, text)


def english_snippets(text: str) -> tuple[str, ...]:
    snippets: list[str] = []
    for sentence in re.split(r"(?<=[.!?])\s+|\s+[•|]\s+|\n+", text):
        cleaned = strip_allowed_latin_terms(sentence)
        words = re.findall(r"[A-Za-z][A-Za-z'-]+", cleaned)
        if len(words) >= 6:
            snippets.append(sentence.strip()[:180])
        if len(snippets) >= 3:
            break
    return tuple(snippets)


def audit_page(url: str, session: requests.Session) -> PageAudit:
    response = session.get(url, timeout=20)
    status = response.status_code
    if status >= 400:
        return PageAudit(url, status, 0, 0, 1.0, "missing", ())

    text = visible_text(response.text)
    cjk_chars = sum(1 for ch in text if "\u4e00" <= ch <= "\u9fff")
    latin_scan = strip_allowed_latin_terms(text)
    latin_words = len(re.findall(r"[A-Za-z][A-Za-z'-]+", latin_scan))
    latin_ratio = latin_words / max(1, latin_words + cjk_chars)

    snippets = english_snippets(text)
    if cjk_chars < 80 and latin_words > 80:
        verdict = "mostly-english"
    elif latin_ratio > 0.35 and latin_words > 120 and snippets:
        verdict = "partial-english"
    else:
        verdict = "ok"

    return PageAudit(url, status, cjk_chars, latin_words, latin_ratio, verdict, snippets)


def print_report(results: Iterable[PageAudit]) -> int:
    failures = [result for result in results if result.verdict != "ok"]
    for result in failures:
        print(
            f"[{result.verdict}] {result.url} "
            f"(status={result.status}, cjk={result.cjk_chars}, "
            f"latin_words={result.latin_words}, latin_ratio={result.latin_ratio:.2f})"
        )
        for snippet in result.snippets:
            print(f"  - {snippet}")

    print(f"\nChecked {len(list(results)) if not isinstance(results, list) else len(results)} localized URLs; flagged {len(failures)}.")
    return len(failures)


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--sitemap", default=DEFAULT_SITEMAP)
    parser.add_argument("--kind", choices=["all", "posts", "pages", "products", "categories"], default="all")
    parser.add_argument("--limit", type=int, default=0)
    parser.add_argument("--sleep", type=float, default=0.05)
    parser.add_argument("--fail", action="store_true")
    args = parser.parse_args()

    session = requests.Session()
    session.headers.update({"User-Agent": "SVICLOUD translation audit/1.0"})
    base_urls = collect_urls(args.sitemap, session, args.kind)
    if args.limit:
        base_urls = base_urls[: args.limit]

    results: list[PageAudit] = []
    for base_url in base_urls:
        for locale in LOCALES:
            url = localized_url(base_url, locale)
            try:
                results.append(audit_page(url, session))
            except Exception as exc:
                print(f"[error] {url}: {exc}", file=sys.stderr)
                results.append(PageAudit(url, 0, 0, 0, 1.0, "error", ()))
            if args.sleep:
                time.sleep(args.sleep)

    failures = print_report(results)
    return 1 if args.fail and failures else 0


if __name__ == "__main__":
    raise SystemExit(main())
