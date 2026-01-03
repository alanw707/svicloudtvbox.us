#!/usr/bin/env python3
"""Generate and attach a hero image to an existing WordPress post."""

from __future__ import annotations

import argparse
import logging
import os
import sys
import mimetypes
from pathlib import Path
from typing import Dict, Any

import requests
import yaml
from dotenv import load_dotenv

SCRIPT_DIR = Path(__file__).resolve().parent
PROJECT_ROOT = SCRIPT_DIR.parent
if str(PROJECT_ROOT) not in sys.path:
    sys.path.append(str(PROJECT_ROOT))

from bs4 import BeautifulSoup

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


def main() -> int:
    parser = argparse.ArgumentParser(description="Regenerate hero image for an existing WP post.")
    parser.add_argument("--config", type=Path, default=Path("config.yaml"))
    parser.add_argument("--env", type=Path, default=Path(__file__).resolve().parent.parent.parent / ".env")
    parser.add_argument("--post-id", type=int, required=True)
    parser.add_argument("--keyword", required=True, help="Keyword or title context for the hero prompt")
    parser.add_argument("--geo", help="Geo target to feed into the prompt")
    parser.add_argument("--topic-type", help="Topic type (pillar/comparison/etc.)")
    parser.add_argument("--alt", help="Override alt text")
    parser.add_argument("--caption", help="Override caption text")

    args = parser.parse_args()

    ensure_env(args.env)
    config = load_config(args.config.resolve())

    hero_cfg = config.get("content_inputs", {}).get("hero_images", {})
    if not hero_cfg or hero_cfg.get("provider") != "openai":
        print("Hero image provider is disabled; enable content_inputs.hero_images.provider='openai' first.", file=sys.stderr)
        return 1

    publisher_cfg = config.get("wordpress", {}).get("rest", {})
    endpoint = publisher_cfg.get("endpoint")
    username = publisher_cfg.get("username")
    password_env = publisher_cfg.get("password_env")
    password = None
    if password_env:
        password = os.getenv(password_env)
    if not password:
        password = publisher_cfg.get("password")

    if not endpoint or not username or not password:
        print("WordPress REST credentials missing in config.yaml", file=sys.stderr)
        return 1

    generator = HeroImageGenerator(hero_cfg, logging.getLogger("hero_update"))
    metadata = {}
    if args.geo:
        metadata["geo_target"] = args.geo
    if args.topic_type:
        metadata["topic_type"] = args.topic_type

    topic = TopicCandidate(keyword=args.keyword, metadata=metadata)
    hero = generator.generate(topic, brief="", outline="")
    if not hero:
        print("Failed to generate hero image; check OpenAI credentials.", file=sys.stderr)
        return 1

    alt_text = args.alt or hero.get("alt") or f"{args.keyword} 英文示意圖"
    caption = args.caption or hero.get("caption") or ""

    upload = _upload_media(hero["path"], alt_text, endpoint, username, password)
    if not upload:
        return 1

    source_url = upload.get("source_url")
    media_id = upload.get("id")
    if not source_url or not media_id:
        print("Media upload succeeded but lacked source_url/id", file=sys.stderr)
        return 1

    success = _update_post(endpoint, args.post_id, username, password, source_url, media_id, alt_text, caption)
    if not success:
        return 1

    print(f"Updated post {args.post_id} with hero {source_url}")
    return 0


def _upload_media(path_str: str, title: str, posts_endpoint: str, username: str, password: str) -> Dict[str, Any] | None:
    posts_endpoint = posts_endpoint.rstrip("/")
    if posts_endpoint.endswith("/posts"):
        media_endpoint = posts_endpoint.rsplit("/posts", 1)[0] + "/media"
    else:
        media_endpoint = posts_endpoint + "/media"

    path = Path(path_str)
    if not path.exists():
        print(f"Image file missing: {path}", file=sys.stderr)
        return None

    content_type, _ = mimetypes.guess_type(path.name)
    files = {"file": (path.name, path.read_bytes(), content_type or "application/octet-stream")}
    data = {"title": title}
    try:
        response = requests.post(
            media_endpoint,
            auth=(username, password),
            files=files,
            data=data,
            timeout=60,
        )
        if 200 <= response.status_code < 300:
            return response.json()
        print(f"Media upload failed {response.status_code}: {response.text[:200]}", file=sys.stderr)
    except requests.RequestException as exc:
        print(f"Media upload error: {exc}", file=sys.stderr)
    return None


def _update_post(
    posts_endpoint: str,
    post_id: int,
    username: str,
    password: str,
    hero_url: str,
    media_id: int,
    alt: str,
    caption: str,
) -> bool:
    base = posts_endpoint.rstrip("/")
    post_url = f"{base}/{post_id}"
    params = {"context": "edit"}
    try:
        current = requests.get(post_url, auth=(username, password), params=params, timeout=30)
        current.raise_for_status()
    except requests.RequestException as exc:
        print(f"Failed to fetch post {post_id}: {exc}", file=sys.stderr)
        return False

    payload = current.json()
    content = payload.get("content", {}).get("raw") or payload.get("content", {}).get("rendered") or ""
    soup = BeautifulSoup(content, "html.parser")
    first_img = soup.find("img")
    if first_img:
        container = first_img.find_parent(["figure", "p"])
        if container:
            container.decompose()
        else:
            first_img.decompose()
    cleaned_content = str(soup)
    hero_block = f'<figure class="wp-block-image size-large"><img src="{hero_url}" alt="{alt}"/>'
    if caption:
        hero_block += f"<figcaption>{caption}</figcaption>"
    hero_block += "</figure>"

    if hero_url in cleaned_content:
        updated_content = cleaned_content
    else:
        updated_content = f"{hero_block}\n\n{cleaned_content}"

    try:
        resp = requests.post(
            post_url,
            auth=(username, password),
            json={"content": updated_content, "featured_media": media_id},
            timeout=30,
        )
        if 200 <= resp.status_code < 300:
            return True
        print(f"Failed to update post {post_id}: {resp.status_code} {resp.text[:200]}", file=sys.stderr)
    except requests.RequestException as exc:
        print(f"Error updating post {post_id}: {exc}", file=sys.stderr)
    return False


if __name__ == "__main__":
    logging.basicConfig(level=logging.INFO)
    sys.exit(main())
