#!/usr/bin/env python3
"""
Clean up existing "bad" WordPress posts (typically off-topic / blocked keywords).

Default behavior is non-destructive: it reports candidates only.
Use --apply to change post status (default: draft).

Sources:
- automation/blog_automation/data/posts_history.db (tracks automation outputs + wp_post_id)
- WordPress REST API (optional scan of all published posts)

Requires (loaded automatically from .env if present):
- WP_REST_ENDPOINT (e.g. https://svicloudtvbox.us/wp-json)
- WP_REST_USERNAME
- WP_REST_PASSWORD
"""

from __future__ import annotations

import argparse
import json
import os
import re
import sqlite3
from dataclasses import dataclass
from pathlib import Path
from typing import Iterable, Optional
from urllib.parse import urljoin

import requests
import yaml


ROOT = Path(__file__).resolve().parent.parent


def load_dotenv_file(path: Path) -> None:
    if not path.exists():
        return
    for raw_line in path.read_text(encoding="utf-8", errors="ignore").splitlines():
        line = raw_line.strip()
        if not line or line.startswith("#") or "=" not in line:
            continue
        key, value = line.split("=", 1)
        key = key.strip()
        value = value.strip()
        if not key:
            continue
        if (value.startswith('"') and value.endswith('"')) or (value.startswith("'") and value.endswith("'")):
            value = value[1:-1]
        os.environ.setdefault(key, value)


def load_env() -> None:
    # Prefer repo root .env first, then the automation one.
    load_dotenv_file(ROOT / ".env")
    load_dotenv_file(ROOT / "automation/blog_automation/.env")


def get_env(name: str) -> str:
    value = os.environ.get(name)
    if not value:
        raise SystemExit(f"Missing required env var: {name} (set it or add to .env)")
    return value


def load_block_patterns() -> list[re.Pattern[str]]:
    config_path = ROOT / "automation/blog_automation/config.yaml"
    if not config_path.exists():
        # Safe fallback: only known off-topic patterns that previously leaked.
        raw_patterns = [r"(?i)sound\s*bar", r"音響條", r"音響条", r"聲霸"]
        return [re.compile(p) for p in raw_patterns]

    cfg = yaml.safe_load(config_path.read_text(encoding="utf-8"))
    raw_patterns: list[str] = []
    try:
        raw_patterns = cfg["content_inputs"]["keyword_filters"]["block_patterns"] or []
    except Exception:
        raw_patterns = []

    if not raw_patterns:
        raw_patterns = [r"(?i)sound\s*bar", r"音響條", r"音響条", r"聲霸"]

    patterns: list[re.Pattern[str]] = []
    for pattern in raw_patterns:
        try:
            patterns.append(re.compile(pattern))
        except re.error:
            patterns.append(re.compile(re.escape(pattern)))
    return patterns


@dataclass(frozen=True)
class Candidate:
    wp_id: int
    slug: str
    title: str
    reason: str


def match_reason(patterns: list[re.Pattern[str]], *fields: str) -> Optional[str]:
    combined = " | ".join([f for f in fields if f])
    for pattern in patterns:
        if pattern.search(combined):
            return f"matches block pattern: {pattern.pattern}"
    return None


def candidates_from_db(patterns: list[re.Pattern[str]]) -> list[Candidate]:
    db_path = ROOT / "automation/blog_automation/data/posts_history.db"
    if not db_path.exists():
        return []

    con = sqlite3.connect(str(db_path))
    cur = con.cursor()
    rows = cur.execute(
        "select title,slug,metadata from posts_history where status='published' order by datetime(created_at) desc"
    ).fetchall()
    con.close()

    candidates: list[Candidate] = []
    for title, slug, metadata in rows:
        wp_id = None
        try:
            meta = json.loads(metadata) if metadata else {}
            wp_id = meta.get("wp_post_id")
        except Exception:
            wp_id = None

        if not wp_id or not isinstance(wp_id, int):
            continue

        reason = match_reason(patterns, str(title or ""), str(slug or ""))
        if not reason:
            continue

        candidates.append(
            Candidate(
                wp_id=wp_id,
                slug=str(slug or ""),
                title=str(title or ""),
                reason=reason,
            )
        )

    # Deduplicate by wp_id.
    unique: dict[int, Candidate] = {c.wp_id: c for c in candidates}
    return list(unique.values())


def wp_session() -> tuple[requests.Session, str]:
    load_env()
    endpoint = os.environ.get("WP_REST_ENDPOINT") or get_env("WP_REST_ENDPOINT")
    username = os.environ.get("WP_REST_USERNAME") or get_env("WP_REST_USERNAME")
    password = os.environ.get("WP_REST_PASSWORD") or get_env("WP_REST_PASSWORD")

    base = endpoint.rstrip("/")
    if not base.endswith("/wp-json"):
        base += "/wp-json"

    session = requests.Session()
    session.auth = (username, password)
    return session, base


def wp_list_published_posts(session: requests.Session, base: str) -> Iterable[dict]:
    posts_url = urljoin(base + "/", "wp/v2/posts")
    page = 1
    while True:
        resp = session.get(
            posts_url,
            params={"per_page": 100, "page": page, "status": "publish", "context": "edit"},
            timeout=30,
        )
        if resp.status_code == 400 and "rest_post_invalid_page_number" in resp.text:
            break
        resp.raise_for_status()
        batch = resp.json()
        if not batch:
            break
        for post in batch:
            yield post
        page += 1


def candidates_from_wp(patterns: list[re.Pattern[str]]) -> list[Candidate]:
    session, base = wp_session()
    candidates: list[Candidate] = []

    for post in wp_list_published_posts(session, base):
        pid = post.get("id")
        title = (post.get("title") or {}).get("rendered") or ""
        slug = post.get("slug") or ""
        if not pid or not slug:
            continue
        reason = match_reason(patterns, str(title), str(slug))
        if not reason:
            continue
        candidates.append(Candidate(wp_id=int(pid), slug=str(slug), title=str(title), reason=reason))

    unique: dict[int, Candidate] = {c.wp_id: c for c in candidates}
    return list(unique.values())


def wp_get_post(session: requests.Session, base: str, wp_id: int) -> dict:
    url = urljoin(base + "/", f"wp/v2/posts/{wp_id}")
    resp = session.get(url, params={"context": "edit"}, timeout=30)
    resp.raise_for_status()
    return resp.json()


def wp_update_post_status(session: requests.Session, base: str, wp_id: int, status: str) -> dict:
    url = urljoin(base + "/", f"wp/v2/posts/{wp_id}")
    resp = session.post(url, json={"status": status}, timeout=30)
    resp.raise_for_status()
    return resp.json()


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--source", choices=["db", "wp", "both"], default="db")
    parser.add_argument(
        "--ids",
        default="",
        help="Comma-separated WordPress post IDs to target explicitly (bypasses source scanning).",
    )
    parser.add_argument("--apply", action="store_true", help="Actually change post status.")
    parser.add_argument(
        "--status",
        default="draft",
        choices=["draft", "private", "trash"],
        help="Target status when applying changes (default: draft).",
    )
    args = parser.parse_args()

    patterns = load_block_patterns()

    candidates: list[Candidate] = []
    explicit_ids = [s.strip() for s in (args.ids or "").split(",") if s.strip()]
    if explicit_ids:
        for token in explicit_ids:
            try:
                wp_id = int(token)
            except ValueError:
                raise SystemExit(f"Invalid --ids entry (expected int): {token!r}")
            candidates.append(Candidate(wp_id=wp_id, slug="", title="", reason="manual id target"))
    else:
        if args.source in ("db", "both"):
            candidates.extend(candidates_from_db(patterns))
        if args.source in ("wp", "both"):
            candidates.extend(candidates_from_wp(patterns))

    unique: dict[int, Candidate] = {c.wp_id: c for c in candidates}
    candidates = sorted(unique.values(), key=lambda c: c.wp_id)

    if not candidates:
        print("No candidates found.")
        return 0

    session, base = wp_session()

    print(f"Found {len(candidates)} candidate post(s):")
    for c in candidates:
        print(f"- wp_id={c.wp_id} slug={c.slug!r} reason={c.reason} title={c.title!r}")

    if not args.apply:
        print("\nDry run only. Re-run with --apply to update statuses.")
        return 0

    print(f"\nApplying status={args.status} ...")
    updated = 0
    skipped_missing = 0
    skipped_errors = 0
    for c in candidates:
        try:
            post = wp_get_post(session, base, c.wp_id)
        except requests.HTTPError as exc:
            resp = getattr(exc, "response", None)
            code = getattr(resp, "status_code", None)
            if code == 404:
                skipped_missing += 1
                print(f"- Skipped wp_id={c.wp_id}: not found (already deleted?)")
                continue
            skipped_errors += 1
            print(f"- Skipped wp_id={c.wp_id}: fetch failed ({code})")
            continue

        current = post.get("status")
        if current == args.status:
            continue

        try:
            wp_update_post_status(session, base, c.wp_id, args.status)
        except requests.HTTPError as exc:
            resp = getattr(exc, "response", None)
            code = getattr(resp, "status_code", None)
            skipped_errors += 1
            print(f"- Skipped wp_id={c.wp_id}: update failed ({code})")
            continue

        updated += 1
        print(f"- Updated wp_id={c.wp_id}: {current} -> {args.status}")

    print(f"\nDone. Updated {updated} post(s). Skipped missing: {skipped_missing}. Skipped errors: {skipped_errors}.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
