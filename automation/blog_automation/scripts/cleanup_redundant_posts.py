#!/usr/bin/env python3
from __future__ import annotations

import argparse
import json
import os
import sqlite3
from dataclasses import dataclass
from datetime import datetime, timedelta, timezone
from pathlib import Path
from typing import Any, Dict, Iterable, List, Optional, Sequence, Tuple

import requests
import yaml
from dotenv import load_dotenv


@dataclass(frozen=True)
class PostSummary:
    id: int
    date_gmt: datetime
    title: str
    categories: List[int]


def _parse_wp_datetime(value: str) -> datetime:
    normalized = (value or "").strip().replace("Z", "+00:00")
    dt = datetime.fromisoformat(normalized)
    if dt.tzinfo is None:
        dt = dt.replace(tzinfo=timezone.utc)
    return dt.astimezone(timezone.utc)


def _load_config(path: Path) -> Dict[str, Any]:
    return yaml.safe_load(path.read_text(encoding="utf-8")) or {}


def _get_rest_auth(config: Dict[str, Any]) -> Tuple[str, str, str]:
    rest_cfg = (config.get("wordpress", {}) or {}).get("rest", {}) or {}
    endpoint = rest_cfg.get("endpoint")
    username = rest_cfg.get("username")
    password_env = rest_cfg.get("password_env")
    password = os.getenv(password_env) if password_env else rest_cfg.get("password")

    if not endpoint or not username or not password:
        raise RuntimeError("REST API not configured (endpoint/username/password missing)")
    return str(endpoint), str(username), str(password)


def _fetch_recent_posts(
    endpoint: str,
    auth: Tuple[str, str],
    *,
    per_page: int = 30,
    page: int = 1,
    author: Optional[int] = None,
) -> List[PostSummary]:
    params: Dict[str, Any] = {
        "per_page": per_page,
        "page": page,
        "orderby": "date",
        "order": "desc",
        "status": "publish",
        "_fields": "id,date_gmt,title,categories",
    }
    if author is not None:
        params["author"] = author

    resp = requests.get(endpoint, auth=auth, params=params, timeout=30)
    resp.raise_for_status()
    rows = resp.json() or []

    posts: List[PostSummary] = []
    for row in rows:
        title_obj = row.get("title") or {}
        title = title_obj.get("rendered") or ""
        date_gmt = _parse_wp_datetime(row.get("date_gmt") or row.get("date") or "")
        posts.append(
            PostSummary(
                id=int(row.get("id")),
                date_gmt=date_gmt,
                title=str(title),
                categories=[int(v) for v in (row.get("categories") or [])],
            )
        )
    return posts


def _cluster_posts(
    posts: Sequence[PostSummary],
    *,
    max_gap_minutes: int,
) -> List[List[PostSummary]]:
    if not posts:
        return []
    ordered = sorted(posts, key=lambda p: p.date_gmt)
    clusters: List[List[PostSummary]] = [[ordered[0]]]
    gap = timedelta(minutes=max_gap_minutes)
    for post in ordered[1:]:
        if (post.date_gmt - clusters[-1][-1].date_gmt) <= gap:
            clusters[-1].append(post)
        else:
            clusters.append([post])
    return clusters


def _choose_keep_post(posts: Sequence[PostSummary], *, prefer_category: int) -> PostSummary:
    preferred = [p for p in posts if prefer_category in p.categories]
    if preferred:
        return sorted(preferred, key=lambda p: p.date_gmt)[0]
    return sorted(posts, key=lambda p: p.date_gmt)[0]


def _trash_post(endpoint: str, auth: Tuple[str, str], post_id: int) -> None:
    url = f"{endpoint.rstrip('/')}/{post_id}"
    resp = requests.delete(url, auth=auth, timeout=30)
    resp.raise_for_status()


def _update_local_db_status(db_path: Path, wp_post_ids: Iterable[int], new_status: str) -> int:
    ids = {int(v) for v in wp_post_ids}
    if not ids or not db_path.exists():
        return 0

    conn = sqlite3.connect(db_path)
    updated = 0
    try:
        cursor = conn.execute(
            "SELECT id, metadata, status FROM posts_history WHERE status IN ('published', 'staged')"
        )
        for row_id, meta_raw, status in cursor.fetchall():
            try:
                meta = json.loads(meta_raw) if meta_raw else {}
            except Exception:
                meta = {}
            wp_id = meta.get("wp_post_id")
            if wp_id is None:
                continue
            try:
                wp_id_int = int(wp_id)
            except Exception:
                continue
            if wp_id_int not in ids:
                continue
            if status == new_status:
                continue
            conn.execute("UPDATE posts_history SET status = ? WHERE id = ?", (new_status, row_id))
            updated += 1
        conn.commit()
        return updated
    finally:
        conn.close()


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(
        description="Trash redundant autoblog posts (and update local posts_history.db status)."
    )
    parser.add_argument("--config", type=Path, default=Path("config.yaml"), help="Path to config.yaml")
    parser.add_argument("--env", type=Path, default=Path(".env"), help="Path to .env file (default: .env)")
    parser.add_argument("--dry-run", action="store_true", help="Show what would be trashed without changing anything")
    parser.add_argument("--post-id", type=int, action="append", help="Specific WP post ID to trash (repeatable)")
    parser.add_argument("--since-hours", type=int, default=24, help="Scan window for clustering (default: 24)")
    parser.add_argument("--cluster-minutes", type=int, default=20, help="Max gap to treat as one burst (default: 20)")
    parser.add_argument(
        "--prefer-keep-category",
        type=int,
        default=22,
        help="Prefer keeping a post that has this category ID when bursting (default: 22 = Comparisons)",
    )
    return parser.parse_args()


def main() -> int:
    args = parse_args()

    if args.env and args.env.exists():
        load_dotenv(args.env)

    config_path = args.config.resolve()
    config = _load_config(config_path)

    endpoint, username, password = _get_rest_auth(config)
    auth = (username, password)

    rest_cfg = (config.get("wordpress", {}) or {}).get("rest", {}) or {}
    author = rest_cfg.get("author")
    author_id = int(author) if author is not None else None

    target_ids = [int(v) for v in (args.post_id or [])]

    if not target_ids:
        recent = _fetch_recent_posts(endpoint, auth, per_page=60, author=author_id)
        cutoff = datetime.now(timezone.utc) - timedelta(hours=int(args.since_hours))
        windowed = [p for p in recent if p.date_gmt >= cutoff]
        clusters = _cluster_posts(windowed, max_gap_minutes=int(args.cluster_minutes))
        bursts = [c for c in clusters if len(c) > 1]

        for burst in bursts:
            keep = _choose_keep_post(burst, prefer_category=int(args.prefer_keep_category))
            to_trash = [p for p in burst if p.id != keep.id]
            target_ids.extend([p.id for p in to_trash])

    target_ids = sorted(set(target_ids))
    if not target_ids:
        print("No redundant posts found to trash.")
        return 0

    # Summarize targets
    lookup = {p.id: p for p in _fetch_recent_posts(endpoint, auth, per_page=60, author=author_id)}
    print("Will trash posts:")
    for pid in target_ids:
        post = lookup.get(pid)
        if post:
            print(f"- {pid} {post.date_gmt.isoformat()} {post.title} categories={post.categories}")
        else:
            print(f"- {pid} (not found in recent listing)")

    if args.dry_run:
        print("Dry run: no changes made.")
        return 0

    for pid in target_ids:
        _trash_post(endpoint, auth, pid)
        print(f"Trashed WP post {pid}")

    db_path = (config.get("storage", {}) or {}).get("db_path") or "data/posts_history.db"
    resolved_db = (config_path.parent / Path(str(db_path))).resolve() if not Path(str(db_path)).is_absolute() else Path(str(db_path))
    updated = _update_local_db_status(resolved_db, target_ids, new_status="trashed")
    print(f"Updated local DB statuses: {updated} row(s) -> trashed ({resolved_db})")

    return 0


if __name__ == "__main__":
    raise SystemExit(main())

