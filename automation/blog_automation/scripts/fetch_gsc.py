#!/usr/bin/env python3
"""Fetch Google Search Console query data and save to the autoblog JSON format."""
from __future__ import annotations

import argparse
import json
import os
from datetime import date, timedelta
from pathlib import Path
from typing import Any, List

from dotenv import load_dotenv
from google.oauth2 import service_account
from googleapiclient.discovery import build

SCOPES = ["https://www.googleapis.com/auth/webmasters.readonly"]


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="Export GSC query data for the autoblog pipeline")
    parser.add_argument("--site", required=True, help="Site URL as registered in GSC (e.g., https://svicloudtvbox.us)")
    parser.add_argument(
        "--credentials",
        default=os.getenv("GSC_CREDENTIALS_PATH"),
        help="Path to service-account JSON (default: GSC_CREDENTIALS_PATH env)",
    )
    parser.add_argument(
        "--output",
        default="automation/blog_automation/data/gsc_topics.json",
        help="Output JSON file path",
    )
    parser.add_argument("--days", type=int, default=30, help="Lookback window in days (default: 30)")
    parser.add_argument("--limit", type=int, default=100, help="Max queries to export (default: 100)")
    parser.add_argument(
        "--min-impressions", type=float, default=50, dest="min_impressions", help="Minimum impressions filter"
    )
    parser.add_argument(
        "--max-position", type=float, default=20, dest="max_position", help="Maximum average position filter"
    )
    parser.add_argument(
        "--env-file",
        help="Optional .env file to load before reading env vars",
    )
    return parser.parse_args()


def load_credentials(path: str | None):
    if not path:
        raise SystemExit("Missing credentials path. Set --credentials or GSC_CREDENTIALS_PATH.")
    cred_path = Path(path).expanduser()
    if not cred_path.exists():
        raise SystemExit(f"Credentials file not found: {cred_path}")
    return service_account.Credentials.from_service_account_file(str(cred_path), scopes=SCOPES)


def main() -> None:
    args = parse_args()
    if args.env_file:
        env_path = Path(args.env_file)
        if env_path.exists():
            load_dotenv(env_path)
    else:
        load_dotenv()  # load default .env if present

    creds = load_credentials(args.credentials)
    service = build("searchconsole", "v1", credentials=creds, cache_discovery=False)

    end_date = date.today() - timedelta(days=1)
    start_date = end_date - timedelta(days=args.days)

    request_body = {
        "startDate": start_date.isoformat(),
        "endDate": end_date.isoformat(),
        "dimensions": ["query"],
        "rowLimit": args.limit,
        "type": "web",
        "dataState": "all",
    }

    response = (
        service.searchanalytics()
        .query(siteUrl=args.site, body=request_body)
        .execute()
    )

    rows: List[Any] = response.get("rows", [])
    filtered = []
    for row in rows:
        query = row.get("keys", [""])[0]
        impressions = row.get("impressions", 0)
        position = row.get("position", 99)
        ctr = row.get("ctr", 0)
        if impressions < args.min_impressions:
            continue
        if position > args.max_position:
            continue
        filtered.append(
            {
                "query": query,
                "impressions": impressions,
                "ctr": ctr,
                "position": position,
            }
        )

    output_path = Path(args.output)
    output_path.parent.mkdir(parents=True, exist_ok=True)
    output_path.write_text(json.dumps(filtered, ensure_ascii=False, indent=2), encoding="utf-8")
    print(f"Saved {len(filtered)} queries to {output_path}")


if __name__ == "__main__":
    main()
