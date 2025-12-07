#!/usr/bin/env python3
"""
Export published WordPress posts to Markdown files for en, zh-TW, and zh-CN
using existing REST content/meta. Generates front matter with slug/title/
description/date/status and writes HTML content as the body.

Usage:
  WP_REST_ENDPOINT=... WP_REST_USERNAME=... WP_REST_PASSWORD=... \
  python3 scripts/export_posts_markdown.py

Outputs:
  docs/blog/{slug}.md
  docs/blog/zh/{slug}.md
  docs/blog/zh-cn/{slug}.md

Notes:
- Uses existing localized meta fields if present:
  _svic_title_{en_us,zh_tw,zh_cn}, _svic_description_{...}, _svic_content_{...}
- Falls back zh_CN -> zh_TW -> en_us.
- Content is written as HTML inside Markdown; adjust if you prefer Markdown conversion.
"""

from __future__ import annotations

import os
import sys
from pathlib import Path
from typing import Dict
from urllib.parse import urljoin

import requests

def get_env(name: str) -> str:
    value = os.environ.get(name)
    if not value:
        raise SystemExit(f"Missing required env var: {name}")
    return value

def ensure_dir(path: Path) -> None:
    path.mkdir(parents=True, exist_ok=True)

def choose_meta(meta: Dict, key_base: str, lang: str, fallback_lang: str, default: str) -> str:
    primary = meta.get(f"{key_base}_{lang}")
    if isinstance(primary, str) and primary.strip():
        return primary.strip()
    fallback = meta.get(f"{key_base}_{fallback_lang}")
    if isinstance(fallback, str) and fallback.strip():
        return fallback.strip()
    default_val = meta.get(f"{key_base}_en_us")
    if isinstance(default_val, str) and default_val.strip():
        return default_val.strip()
    return default

def write_markdown(path: Path, slug: str, title: str, description: str, content_html: str, status: str, date: str) -> None:
    ensure_dir(path.parent)
    front_matter = ["---", f"slug: {slug}", f"title: {title}", f"description: {description}", f"status: {status}", f"date: {date}", "---", ""]
    body = content_html.strip()
    path.write_text("\n".join(front_matter) + body + "\n", encoding="utf-8")

def main() -> int:
    endpoint = os.environ.get("WP_REST_ENDPOINT") or "https://svicloudtvbox.us/wp-json"
    username = get_env("WP_REST_USERNAME")
    password = get_env("WP_REST_PASSWORD")

    base = endpoint.rstrip("/")
    if not base.endswith("/wp-json"):
        base = base + "/wp-json"
    posts_url = urljoin(base + "/", "wp/v2/posts")

    session = requests.Session()
    session.auth = (username, password)

    all_posts = []
    page = 1
    while True:
        resp = session.get(posts_url, params={"per_page": 100, "page": page, "status": "publish", "context": "edit"}, timeout=20)
        if resp.status_code == 400 and "rest_post_invalid_page_number" in resp.text:
            break
        resp.raise_for_status()
        batch = resp.json()
        if not batch:
            break
        all_posts.extend(batch)
        page += 1

    out_en = Path("docs/blog")
    out_zh_tw = out_en / "zh"
    out_zh_cn = out_en / "zh-cn"

    for post in all_posts:
        slug = post.get("slug", "").strip()
        if not slug:
            continue
        date = post.get("date", "")
        status = post.get("status", "")
        meta = post.get("meta") or {}
        title_en = meta.get("_svic_title_en_us") or post.get("title", {}).get("rendered", "")
        desc_en = meta.get("_svic_description_en_us") or post.get("excerpt", {}).get("rendered", "")
        content_en = meta.get("_svic_content_en_us") or post.get("content", {}).get("rendered", "")

        # English
        write_markdown(out_en / f"{slug}.md", slug, title_en or slug, desc_en or "", content_en or "", status, date)

        # zh-TW
        title_tw = choose_meta(meta, "_svic_title", "zh_tw", "zh_cn", title_en or slug)
        desc_tw = choose_meta(meta, "_svic_description", "zh_tw", "zh_cn", desc_en or "")
        content_tw = choose_meta(meta, "_svic_content", "zh_tw", "zh_cn", content_en or "")
        write_markdown(out_zh_tw / f"{slug}.md", slug, title_tw, desc_tw, content_tw, status, date)

        # zh-CN
        title_cn = choose_meta(meta, "_svic_title", "zh_cn", "zh_tw", title_en or slug)
        desc_cn = choose_meta(meta, "_svic_description", "zh_cn", "zh_tw", desc_en or "")
        content_cn = choose_meta(meta, "_svic_content", "zh_cn", "zh_tw", content_en or "")
        write_markdown(out_zh_cn / f"{slug}.md", slug, title_cn, desc_cn, content_cn, status, date)

    print(f"Exported {len(all_posts)} posts to docs/blog (en/zh/zh-cn)")
    return 0

if __name__ == "__main__":
    raise SystemExit(main())
