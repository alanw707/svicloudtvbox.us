#!/usr/bin/env python3
"""
Update one WordPress post's localized SVICLOUD meta from local Markdown files.

This is intentionally narrower than import_blog_posts.py: it does not modify
English content, categories, status, author, or featured media.

Usage:
  WP_REST_PASSWORD=... python3 scripts/update_post_translation_meta.py \
    --slug svicloud-tv-box-usa-guide-2026
"""

from __future__ import annotations

import argparse
import base64
import os
from pathlib import Path
from typing import Dict, Tuple

import requests
import yaml


DEFAULT_ENDPOINT = "https://svicloudtvbox.us/wp-json"
DEFAULT_USERNAME = "content-bot@svicloudtvbox.us"


def load_env_file(file_path: str = ".env") -> None:
    path = Path(file_path)
    if not path.exists():
        return
    for raw_line in path.read_text(encoding="utf-8").splitlines():
        line = raw_line.strip()
        if not line or line.startswith("#") or "=" not in line:
            continue
        key, value = line.split("=", 1)
        if key and key not in os.environ:
            os.environ[key.strip()] = value.strip().strip('"').strip("'")


def parse_markdown(path: Path) -> Tuple[Dict, str]:
    raw = path.read_text(encoding="utf-8-sig")
    if not raw.startswith("---"):
        raise ValueError(f"{path} missing YAML front matter")
    _, front_raw, body = raw.split("---", 2)
    return yaml.safe_load(front_raw) or {}, body.lstrip("\r\n").strip()


def auth_headers(username: str, password: str) -> Dict[str, str]:
    credential = f"{username}:{password}".encode("utf-8")
    token = base64.b64encode(credential).decode("utf-8")
    return {
        "Authorization": f"Basic {token}",
        "Content-Type": "application/json",
    }


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--slug", required=True)
    parser.add_argument("--docs-dir", default="docs/blog")
    parser.add_argument("--endpoint", default=os.environ.get("WP_REST_ENDPOINT", DEFAULT_ENDPOINT))
    parser.add_argument("--username", default=os.environ.get("WP_REST_USERNAME", DEFAULT_USERNAME))
    parser.add_argument("--password", default=os.environ.get("WP_REST_PASSWORD"))
    parser.add_argument("--env-file", default=".env")
    parser.add_argument("--dry-run", action="store_true")
    args = parser.parse_args()

    load_env_file(args.env_file)
    if args.password is None:
        args.password = os.environ.get("WP_REST_PASSWORD")
    if not args.password:
        raise SystemExit("Missing WP_REST_PASSWORD. Export it or pass --password.")

    docs_dir = Path(args.docs_dir)
    zh_tw_path = docs_dir / "zh" / f"{args.slug}.md"
    zh_cn_path = docs_dir / "zh-cn" / f"{args.slug}.md"
    if not zh_tw_path.exists():
        raise SystemExit(f"Missing zh-TW file: {zh_tw_path}")
    if not zh_cn_path.exists():
        raise SystemExit(f"Missing zh-CN file: {zh_cn_path}")

    tw_meta, tw_body = parse_markdown(zh_tw_path)
    cn_meta, cn_body = parse_markdown(zh_cn_path)
    meta_payload = {
        "_svic_title_zh_tw": str(tw_meta.get("title") or ""),
        "_svic_description_zh_tw": str(tw_meta.get("description") or ""),
        "_svic_content_zh_tw": tw_body,
        "_svic_title_zh_cn": str(cn_meta.get("title") or ""),
        "_svic_description_zh_cn": str(cn_meta.get("description") or ""),
        "_svic_content_zh_cn": cn_body,
    }

    endpoint = args.endpoint.rstrip("/")
    if not endpoint.endswith("/wp-json"):
        endpoint = f"{endpoint}/wp-json"
    posts_url = f"{endpoint}/wp/v2/posts"

    session = requests.Session()
    session.headers.update(auth_headers(args.username, args.password))
    lookup = session.get(
        posts_url,
        params={"slug": args.slug, "status": "any", "context": "edit", "_fields": "id,slug,status"},
        timeout=20,
    )
    lookup.raise_for_status()
    posts = lookup.json()
    if not posts:
        raise SystemExit(f"No WordPress post found for slug: {args.slug}")
    post_id = int(posts[0]["id"])

    if args.dry_run:
        print(f"[dry-run] Would update localized meta for post ID {post_id}, slug={args.slug}")
        return 0

    response = session.post(f"{posts_url}/{post_id}", json={"meta": meta_payload}, timeout=30)
    response.raise_for_status()
    print(f"Updated localized meta for post ID {post_id}, slug={args.slug}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
