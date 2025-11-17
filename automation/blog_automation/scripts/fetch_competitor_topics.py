#!/usr/bin/env python3
"""Extract competitor keywords by scraping target URLs."""
from __future__ import annotations

import argparse
import json
import re
from pathlib import Path
from typing import Dict, List

import requests
from bs4 import BeautifulSoup

COMPETITOR_CONFIG: Dict[str, List[str]] = {
    "techunblock.com": ["https://techunblock.com/blog/"],
    "svicloudtw.com": ["https://svicloudtw.com/blog/"],
    "safeprosys.com": ["https://safeprosys.com/category/svi-cloud/"],
}

KEYWORD_REGEX = re.compile(r"[\u4e00-\u9fffA-Za-z0-9+\- ]{2,40}")


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="Scrape competitor pages for keyword ideas")
    parser.add_argument(
        "--output",
        default="automation/blog_automation/data/competitor_topics.json",
        help="Output JSON file path",
    )
    parser.add_argument(
        "--sites",
        nargs="*",
        help="Override competitor sites list (format: domain=url1,url2)"
    )
    parser.add_argument("--min-length", type=int, default=4, dest="min_length")
    return parser.parse_args()


def load_sites(args: argparse.Namespace) -> Dict[str, List[str]]:
    sites = COMPETITOR_CONFIG.copy()
    if args.sites:
        for entry in args.sites:
            domain, urls = entry.split("=", 1)
            sites[domain.strip()] = [u.strip() for u in urls.split(",") if u.strip()]
    return sites


def fetch_keywords(url: str, min_length: int) -> List[str]:
    try:
        resp = requests.get(url, timeout=15)
        resp.raise_for_status()
    except requests.RequestException as exc:
        print(f"Failed to fetch {url}: {exc}")
        return []
    soup = BeautifulSoup(resp.text, "html.parser")
    text_candidates = []
    for tag in soup.find_all(["h1", "h2", "h3", "h4", "strong"]):
        text = tag.get_text(strip=True)
        if not text:
            continue
        text_candidates.extend(KEYWORD_REGEX.findall(text))
    keywords = []
    for token in text_candidates:
        token = token.strip()
        if len(token) < min_length:
            continue
        if token not in keywords:
            keywords.append(token)
    return keywords


def main() -> None:
    args = parse_args()
    sites = load_sites(args)
    results = []
    for domain, urls in sites.items():
        for url in urls:
            keywords = fetch_keywords(url, args.min_length)
            for keyword in keywords:
                results.append(
                    {
                        "query": keyword,
                        "source": domain,
                        "impressions": 100,
                        "position": 15,
                        "ctr": 0.01,
                    }
                )
    output_path = Path(args.output)
    output_path.parent.mkdir(parents=True, exist_ok=True)
    output_path.write_text(json.dumps(results, ensure_ascii=False, indent=2), encoding="utf-8")
    print(f"Saved {len(results)} competitor keywords to {output_path}")


if __name__ == "__main__":
    main()
