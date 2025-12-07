#!/usr/bin/env python3
"""
Translate published posts to zh-TW and zh-CN using OpenAI and update WP meta.
Also writes Markdown snapshots to docs/blog/zh and docs/blog/zh-cn.

Requires env: WP_REST_ENDPOINT, WP_REST_USERNAME, WP_REST_PASSWORD, OPENAI_API_KEY

Heuristics:
- If content already contains significant CJK characters, reuse content for zh-TW and zh-CN.
- Otherwise, translate title/description/content to target locale preserving HTML.
"""

from __future__ import annotations

import os
import sys
from pathlib import Path
from typing import Dict
from urllib.parse import urljoin

import requests

OPENAI_MODEL = "gpt-4o-mini"


def get_env(name: str) -> str:
    value = os.environ.get(name)
    if not value:
        raise SystemExit(f"Missing required env var: {name}")
    return value


def ensure_dir(path: Path) -> None:
    path.mkdir(parents=True, exist_ok=True)


def count_cjk(text: str) -> int:
    return sum(1 for ch in text if "\u4e00" <= ch <= "\u9fff")


def openai_translate(api_key: str, text: str, target: str) -> str:
    if not text.strip():
        return ""
    headers = {
        "Authorization": f"Bearer {api_key}",
        "Content-Type": "application/json",
    }
    payload = {
        "model": OPENAI_MODEL,
        "messages": [
            {
                "role": "system",
                "content": (
                    "You are a professional translator. Translate the user's HTML/string to the target language exactly, "
                    "preserving HTML tags and placeholders. Do not add commentary."
                ),
            },
            {
                "role": "user",
                "content": f"Target language: {target}.\nInput:\n{text}",
            },
        ],
        "temperature": 0.2,
    }
    last_err = None
    for _ in range(3):
        try:
            resp = requests.post("https://api.openai.com/v1/chat/completions", headers=headers, json=payload, timeout=60)
            resp.raise_for_status()
            data = resp.json()
            return data["choices"][0]["message"]["content"].strip()
        except Exception as exc:  # pragma: no cover - network variability
            last_err = exc
            continue
    raise last_err


def write_md(path: Path, slug: str, title: str, description: str, content: str, status: str, date: str) -> None:
    ensure_dir(path.parent)
    fm = ["---", f"slug: {slug}", f"title: {title}", f"description: {description}", f"status: {status}", f"date: {date}", "---", ""]
    path.write_text("\n".join(fm) + content.strip() + "\n", encoding="utf-8")


def main() -> int:
    endpoint = os.environ.get("WP_REST_ENDPOINT") or "https://svicloudtvbox.us/wp-json"
    username = get_env("WP_REST_USERNAME")
    password = get_env("WP_REST_PASSWORD")
    api_key = get_env("OPENAI_API_KEY")

    base = endpoint.rstrip("/")
    if not base.endswith("/wp-json"):
        base += "/wp-json"
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

    out_tw = Path("docs/blog/zh")
    out_cn = Path("docs/blog/zh-cn")

    updated = []

    for post in all_posts:
        pid = post.get("id")
        slug = post.get("slug", "").strip()
        if not pid or not slug:
            continue
        status = post.get("status", "")
        date = post.get("date", "")
        meta: Dict = post.get("meta") or {}
        title_en = meta.get("_svic_title_en_us") or post.get("title", {}).get("rendered", "")
        desc_en = meta.get("_svic_description_en_us") or post.get("excerpt", {}).get("rendered", "")
        content_en = meta.get("_svic_content_en_us") or post.get("content", {}).get("rendered", "")

        cjk_count = count_cjk(content_en)
        needs_translate = cjk_count < max(50, len(content_en) * 0.05)

        if needs_translate:
            print(f"Translating {slug} (titles/descriptions only) ...")
            title_tw = openai_translate(api_key, title_en, "Traditional Chinese (zh-TW)")
            desc_tw = openai_translate(api_key, desc_en, "Traditional Chinese (zh-TW)")
            title_cn = openai_translate(api_key, title_en, "Simplified Chinese (zh-CN)")
            desc_cn = openai_translate(api_key, desc_en, "Simplified Chinese (zh-CN)")
            content_tw = content_en
            content_cn = content_en
        else:
            # Already Chinese; reuse for both locales
            title_tw = title_en
            desc_tw = desc_en
            content_tw = content_en
            title_cn = title_en
            desc_cn = desc_en
            content_cn = content_en

        # Update WP meta
        meta_payload = {
            "_svic_title_zh_tw": title_tw,
            "_svic_description_zh_tw": desc_tw,
            "_svic_content_zh_tw": content_tw,
            "_svic_title_zh_cn": title_cn,
            "_svic_description_zh_cn": desc_cn,
            "_svic_content_zh_cn": content_cn,
        }
        resp = session.post(f"{posts_url}/{pid}", json={"meta": meta_payload}, timeout=30)
        if resp.status_code >= 400:
            print(f"Failed updating {slug}: {resp.status_code} {resp.text[:200]}", file=sys.stderr)
            continue

        updated.append(slug)

        write_md(out_tw / f"{slug}.md", slug, title_tw, desc_tw, content_tw, status, date)
        write_md(out_cn / f"{slug}.md", slug, title_cn, desc_cn, content_cn, status, date)

    print(f"Translated/updated {len(updated)} posts: {', '.join(updated)}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
