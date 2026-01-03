#!/usr/bin/env python3
"""Scan autoblog posts for oversized images and regenerate hero images."""

from __future__ import annotations

import argparse
import json
import logging
import mimetypes
import os
import sqlite3
import sys
from pathlib import Path
from typing import Any, Dict, Iterable, List, Tuple

import requests
import yaml
from bs4 import BeautifulSoup
from dotenv import load_dotenv

SCRIPT_DIR = Path(__file__).resolve().parent
PROJECT_ROOT = SCRIPT_DIR.parent
if str(PROJECT_ROOT) not in sys.path:
    sys.path.append(str(PROJECT_ROOT))

from src.clients.image_generator import HeroImageGenerator
from src.models import TopicCandidate


def load_config(path: Path) -> Dict[str, Any]:
    if not path.exists():
        raise FileNotFoundError(f"Config file not found: {path}")
    with path.open("r", encoding="utf-8") as fh:
        return yaml.safe_load(fh) or {}


def ensure_env(env_path: Path) -> None:
    if env_path.exists():
        load_dotenv(env_path)
    else:
        print(f"Warning: .env not found at {env_path}", file=sys.stderr)


def iter_autoblog_post_ids(db_path: Path) -> Iterable[Tuple[int, str, str]]:
    if not db_path.exists():
        raise FileNotFoundError(f"SQLite db missing: {db_path}")
    conn = sqlite3.connect(db_path)
    cursor = conn.execute("SELECT id, title, keyword, metadata FROM posts_history WHERE metadata IS NOT NULL")
    for _, title, keyword, metadata in cursor:
        try:
            meta = json.loads(metadata) if metadata else {}
        except json.JSONDecodeError:
            continue
        post_id = meta.get("wp_post_id")
        if not post_id:
            continue
        yield int(post_id), title, keyword or title
    conn.close()


def fetch_post_content(
    endpoint: str,
    post_id: int,
    auth: Tuple[str, str],
) -> Tuple[str, int | None] | None:
    url = f"{endpoint.rstrip('/')}/{post_id}"
    try:
        resp = requests.get(url, auth=auth, params={"context": "edit"}, timeout=30)
        if resp.status_code == 200:
            data = resp.json()
            content = data.get("content", {}).get("raw") or data.get("content", {}).get("rendered") or ""
            featured_media = data.get("featured_media") or None
            return content, int(featured_media) if featured_media else None
        print(f"Fetch failed {post_id}: {resp.status_code} {resp.text[:200]}", file=sys.stderr)
    except requests.RequestException as exc:
        print(f"Fetch error {post_id}: {exc}", file=sys.stderr)
    return None


def fetch_media_url(endpoint: str, media_id: int, auth: Tuple[str, str]) -> str | None:
    base = endpoint.rstrip("/")
    if base.endswith("/posts"):
        media_endpoint = base.rsplit("/posts", 1)[0] + "/media"
    else:
        media_endpoint = base + "/media"
    url = f"{media_endpoint}/{media_id}"
    try:
        resp = requests.get(url, auth=auth, timeout=30)
        if resp.status_code == 200:
            data = resp.json()
            return data.get("source_url")
        print(f"Media fetch failed {media_id}: {resp.status_code} {resp.text[:200]}", file=sys.stderr)
    except requests.RequestException as exc:
        print(f"Media fetch error {media_id}: {exc}", file=sys.stderr)
    return None


def image_is_large(url: str, threshold_bytes: int) -> bool:
    try:
        head = requests.head(url, allow_redirects=True, timeout=20)
        if head.status_code >= 200 and head.status_code < 300:
            size = head.headers.get("Content-Length")
            if size and size.isdigit():
                return int(size) > threshold_bytes
    except requests.RequestException:
        pass

    try:
        with requests.get(url, stream=True, timeout=30) as resp:
            if resp.status_code < 200 or resp.status_code >= 300:
                return False
            total = 0
            for chunk in resp.iter_content(chunk_size=65536):
                if not chunk:
                    continue
                total += len(chunk)
                if total > threshold_bytes:
                    return True
    except requests.RequestException:
        return False
    return False


def remove_image_tag(img) -> None:
    container = img.find_parent(["figure", "p"])
    if container:
        container.decompose()
    else:
        img.decompose()


def upload_media(path: Path, title: str, posts_endpoint: str, auth: Tuple[str, str]) -> Dict[str, Any] | None:
    posts_endpoint = posts_endpoint.rstrip("/")
    if posts_endpoint.endswith("/posts"):
        media_endpoint = posts_endpoint.rsplit("/posts", 1)[0] + "/media"
    else:
        media_endpoint = posts_endpoint + "/media"

    content_type, _ = mimetypes.guess_type(path.name)
    files = {"file": (path.name, path.read_bytes(), content_type or "application/octet-stream")}
    data = {"title": title}
    try:
        resp = requests.post(media_endpoint, auth=auth, files=files, data=data, timeout=60)
        if 200 <= resp.status_code < 300:
            return resp.json()
        print(f"Media upload failed: {resp.status_code} {resp.text[:200]}", file=sys.stderr)
    except requests.RequestException as exc:
        print(f"Media upload error: {exc}", file=sys.stderr)
    return None


def update_post_content(
    endpoint: str,
    post_id: int,
    auth: Tuple[str, str],
    new_html: str,
    featured_media: int,
) -> bool:
    url = f"{endpoint.rstrip('/')}/{post_id}"
    payload = {"content": new_html, "featured_media": featured_media}
    try:
        resp = requests.post(url, auth=auth, json=payload, timeout=30)
        if 200 <= resp.status_code < 300:
            return True
        print(f"Update failed {post_id}: {resp.status_code} {resp.text[:200]}", file=sys.stderr)
    except requests.RequestException as exc:
        print(f"Update error {post_id}: {exc}", file=sys.stderr)
    return False


def main() -> int:
    parser = argparse.ArgumentParser(description="Scan autoblog posts for large images and regenerate hero.")
    parser.add_argument("--config", type=Path, default=Path("config.yaml"))
    parser.add_argument("--env", type=Path, default=PROJECT_ROOT.parent.parent / ".env")
    parser.add_argument("--threshold-mb", type=float, default=1.0)
    parser.add_argument("--dry-run", action="store_true")
    parser.add_argument("--keep-non-hero", action="store_true", help="Do not remove non-hero images.")
    parser.add_argument("--limit", type=int, default=0, help="Limit number of posts processed.")

    args = parser.parse_args()

    ensure_env(args.env)
    config = load_config(args.config.resolve())
    hero_cfg = config.get("content_inputs", {}).get("hero_images", {})
    if not hero_cfg or hero_cfg.get("provider") != "openai":
        print("Hero image provider disabled; enable content_inputs.hero_images.provider='openai'.", file=sys.stderr)
        return 1

    rest_cfg = config.get("wordpress", {}).get("rest", {})
    endpoint = rest_cfg.get("endpoint")
    username = rest_cfg.get("username")
    password_env = rest_cfg.get("password_env")
    password = os.getenv(password_env) if password_env else rest_cfg.get("password")
    if not endpoint or not username or not password:
        print("WordPress REST credentials missing in config.yaml/.env", file=sys.stderr)
        return 1

    db_path = Path(config.get("storage", {}).get("db_path", "data/posts_history.db"))
    generator = HeroImageGenerator(hero_cfg, logging.getLogger("hero_scan"))
    threshold_bytes = int(args.threshold_mb * 1024 * 1024)
    strip_non_hero = not args.keep_non_hero

    processed = 0
    for post_id, title, keyword in iter_autoblog_post_ids(db_path):
        if args.limit and processed >= args.limit:
            break
        post_data = fetch_post_content(endpoint, post_id, (username, password))
        if post_data is None:
            continue
        content, featured_media_id = post_data
        soup = BeautifulSoup(content, "html.parser")
        images = list(soup.find_all("img"))

        large_srcs: List[str] = []
        if featured_media_id:
            media_url = fetch_media_url(endpoint, featured_media_id, (username, password))
            if media_url and image_is_large(media_url, threshold_bytes):
                large_srcs.append(media_url)

        for img in images:
            src = img.get("src")
            if not src:
                continue
            if image_is_large(src, threshold_bytes):
                large_srcs.append(src)

        if not large_srcs:
            continue

        print(f"Post {post_id}: {len(large_srcs)} oversized images", file=sys.stderr)
        if args.dry_run:
            processed += 1
            continue

        topic = TopicCandidate(keyword=keyword)
        hero = generator.generate(topic, brief="", outline="")
        if not hero:
            print(f"Hero generation failed for post {post_id}", file=sys.stderr)
            continue

        hero_path = Path(hero["path"])
        upload = upload_media(hero_path, hero.get("alt") or title, endpoint, (username, password))
        if not upload:
            continue

        hero_url = upload.get("source_url")
        media_id = upload.get("id")
        if not hero_url or not media_id:
            print(f"Hero upload missing url/id for post {post_id}", file=sys.stderr)
            continue

        for img in list(soup.find_all("img")):
            src = img.get("src") or ""
            if strip_non_hero or src in large_srcs:
                remove_image_tag(img)

        hero_block = f'<figure class="wp-block-image size-large"><img src="{hero_url}" alt="{hero.get("alt") or title}"/>'
        if hero.get("caption"):
            hero_block += f"<figcaption>{hero.get('caption')}</figcaption>"
        hero_block += "</figure>"

        cleaned_content = str(soup)
        if hero_url not in cleaned_content:
            updated_content = f"{hero_block}\n\n{cleaned_content}"
        else:
            updated_content = cleaned_content

        if update_post_content(endpoint, post_id, (username, password), updated_content, int(media_id)):
            processed += 1

    print(f"Processed {processed} posts", file=sys.stderr)
    return 0


if __name__ == "__main__":
    logging.basicConfig(level=logging.INFO)
    sys.exit(main())
