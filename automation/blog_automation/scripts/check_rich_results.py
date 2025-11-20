#!/usr/bin/env python3
"""Run Google Rich Results checks for one or more URLs.

This helper wraps the Search Console URL Inspection API so we can batch-verify
structured data status for sitemap entries or ad-hoc URLs during QA.
"""

from __future__ import annotations

import argparse
import json
import sys
from pathlib import Path
from typing import Dict, Iterable, List, Optional, Tuple
from urllib.parse import urlparse
import xml.etree.ElementTree as ET

import requests
import yaml
try:
    from google.oauth2 import service_account
    from googleapiclient.discovery import build
    from googleapiclient.errors import HttpError
    GOOGLE_IMPORT_ERROR: Optional[Exception] = None
except ImportError as exc:  # pragma: no cover - optional dependency
    service_account = None  # type: ignore[assignment]
    build = None  # type: ignore[assignment]
    HttpError = Exception  # type: ignore[assignment]
    GOOGLE_IMPORT_ERROR = exc

SCOPES = ["https://www.googleapis.com/auth/webmasters.readonly"]


def load_config(path: Path) -> Dict[str, object]:
    if not path.exists():
        raise FileNotFoundError(f"Config file not found: {path}")
    with path.open("r", encoding="utf-8") as fh:
        return yaml.safe_load(fh) or {}


def resolve_path(base: Path, candidate: str) -> Path:
    p = Path(candidate)
    return p if p.is_absolute() else (base / p).resolve()


def load_urls(
    explicit: List[str],
    urls_file: Optional[Path],
    sitemap: Optional[str],
) -> List[str]:
    urls: List[str] = list(explicit)

    if urls_file:
        if not urls_file.exists():
            raise FileNotFoundError(f"URLs file not found: {urls_file}")
        with urls_file.open("r", encoding="utf-8") as fh:
            urls.extend(line.strip() for line in fh if line.strip())

    if sitemap:
        urls.extend(parse_sitemap(sitemap))

    deduped: List[str] = []
    seen = set()
    for url in urls:
        if not url:
            continue
        normalized = url.strip()
        if normalized not in seen:
            seen.add(normalized)
            deduped.append(normalized)

    return deduped


def parse_sitemap(target: str, visited: Optional[set[str]] = None) -> List[str]:
    """Return <loc> entries from a sitemap or sitemap index."""
    visited = visited or set()
    if target in visited:
        return []
    visited.add(target)

    parsed = urlparse(target)
    if parsed.scheme in {"http", "https"}:
        resp = requests.get(target, timeout=30)
        resp.raise_for_status()
        xml_text = resp.text
    else:
        path = Path(target)
        if not path.exists():
            raise FileNotFoundError(f"Sitemap not found: {path}")
        xml_text = path.read_text(encoding="utf-8")

    root = ET.fromstring(xml_text)
    ns = {"sm": "http://www.sitemaps.org/schemas/sitemap/0.9"}
    tag_name = root.tag.lower()

    if tag_name.endswith("sitemapindex"):
        urls: List[str] = []
        for loc in root.findall(".//sm:loc", ns):
            if loc.text:
                urls.extend(parse_sitemap(loc.text.strip(), visited))
        return urls

    urls = []
    for loc in root.findall(".//sm:loc", ns):
        if loc.text:
            urls.append(loc.text.strip())
    return urls


def inspect_url(
    service,
    site_url: str,
    target_url: str,
) -> Tuple[str, Dict[str, object]]:
    """Call URL Inspection API for a single URL."""
    request_body = {
        "inspectionUrl": target_url,
        "siteUrl": site_url,
    }
    result = (
        service.urlInspection()
        .index()
        .inspect(body=request_body)
        .execute()
    )

    inspection = result.get("inspectionResult", {})
    rich_results = inspection.get("richResults") or {}
    verdict = rich_results.get("verdict", inspection.get("verdict", "UNKNOWN"))
    detected_items = rich_results.get("detectedItems") or []

    issues: List[Dict[str, object]] = []
    for entry in detected_items:
        rr_type = entry.get("richResultType")
        for item in entry.get("items", []):
            for issue in item.get("issues", []):
                issues.append(
                    {
                        "type": rr_type,
                        "severity": issue.get("severity"),
                        "message": issue.get("message"),
                        "documentation": issue.get("documentation"),
                    }
                )

    summary = {
        "verdict": verdict,
        "detected_types": [entry.get("richResultType") for entry in detected_items],
        "issues": issues,
        "last_crawl_time": inspection.get("lastCrawlTime"),
        "index_status": inspection.get("indexStatusResult", {}).get("verdict"),
    }
    return verdict, summary


def main() -> int:
    if GOOGLE_IMPORT_ERROR:
        print(
            "Error: google-api-python-client is required. Install dependencies via "
            "`pip install -r automation/blog_automation/requirements.txt`.",
            file=sys.stderr,
        )
        return 1

    parser = argparse.ArgumentParser(
        description="Run Google Rich Results validations through the URL Inspection API."
    )
    parser.add_argument(
        "--config",
        type=Path,
        default=Path("config.yaml"),
        help="Path to automation config (default: config.yaml)",
    )
    parser.add_argument(
        "--credentials",
        type=str,
        help="Override path to the Google service-account JSON (defaults to config content_inputs.gsc.credentials)",
    )
    parser.add_argument(
        "--site",
        type=str,
        help="Override site URL (defaults to content_inputs.gsc.site)",
    )
    parser.add_argument(
        "--url",
        action="append",
        dest="urls",
        default=[],
        help="Individual URL to check (can be provided multiple times)",
    )
    parser.add_argument(
        "--urls-file",
        type=Path,
        help="File containing URLs to inspect (one per line)",
    )
    parser.add_argument(
        "--sitemap",
        type=str,
        help="Path or URL to a sitemap whose entries should be validated",
    )
    parser.add_argument(
        "--json",
        type=Path,
        help="Optional path to write JSON output of the inspection results",
    )
    parser.add_argument(
        "--limit",
        type=int,
        help="Inspect at most N URLs (useful when sampling a large sitemap)",
    )

    args = parser.parse_args()
    config = load_config(args.config.resolve())

    gsc_cfg = config.get("content_inputs", {}).get("gsc", {})  # type: ignore[assignment]
    site_url = args.site or gsc_cfg.get("site")
    if not site_url:
        print("Error: site URL missing (set content_inputs.gsc.site or pass --site)", file=sys.stderr)
        return 1

    credentials_path = args.credentials or gsc_cfg.get("credentials")
    if not credentials_path:
        print("Error: credentials path missing (set content_inputs.gsc.credentials or pass --credentials)", file=sys.stderr)
        return 1

    creds_file = resolve_path(args.config.parent, str(credentials_path))
    urls = load_urls(args.urls, args.urls_file, args.sitemap)
    if args.limit:
        urls = urls[: args.limit]
    if not urls:
        print("Error: no URLs provided. Use --url, --urls-file, or --sitemap.", file=sys.stderr)
        return 1

    creds = service_account.Credentials.from_service_account_file(
        str(creds_file),
        scopes=SCOPES,
    )
    service = build("searchconsole", "v1", credentials=creds, cache_discovery=False)

    results: Dict[str, Dict[str, object]] = {}
    failures = 0

    for url in urls:
        try:
            verdict, summary = inspect_url(service, site_url, url)
            results[url] = summary
            status_icon = "✅" if verdict == "PASS" else "⚠️"
            print(f"{status_icon} {url} — verdict: {verdict}, types: {', '.join(summary['detected_types'] or ['none'])}")
            if summary["issues"]:
                failures += 1
                for issue in summary["issues"]:
                    print(
                        f"    - {issue.get('type')}: {issue.get('severity')} — {issue.get('message')}"
                        f"{' (' + issue.get('documentation') + ')' if issue.get('documentation') else ''}"
                    )
        except HttpError as exc:
            failures += 1
            error_content = exc.content.decode("utf-8") if exc.content else str(exc)
            print(f"❌ {url} — API error: {error_content}", file=sys.stderr)
        except Exception as exc:
            failures += 1
            print(f"❌ {url} — Unexpected error: {exc}", file=sys.stderr)

    if args.json:
        args.json.parent.mkdir(parents=True, exist_ok=True)
        args.json.write_text(json.dumps(results, indent=2, ensure_ascii=False), encoding="utf-8")

    if failures:
        print(f"Completed with {failures} failing URLs out of {len(urls)} total.")
        return 2

    print(f"All {len(urls)} URLs passed rich results validation.")
    return 0


if __name__ == "__main__":
    sys.exit(main())
