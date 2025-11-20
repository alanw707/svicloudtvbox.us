#!/usr/bin/env python3
"""Normalize shipping messaging in existing WordPress posts."""
from __future__ import annotations

import argparse
import os
import re
from dataclasses import dataclass
from typing import Dict, Iterable, List, Tuple
from urllib.parse import quote

import requests
import yaml
from dotenv import load_dotenv


@dataclass
class WPConfig:
    endpoint: str
    username: str
    password: str


def load_wp_config(config_path: str, env_path: str | None = None) -> WPConfig:
    if env_path:
        if os.path.exists(env_path):
            load_dotenv(env_path)
        else:
            print(f"Warning: .env not found at {env_path}")

    with open(config_path, "r", encoding="utf-8") as handle:
        config = yaml.safe_load(handle) or {}

    rest_cfg = config.get("wordpress", {}).get("rest", {})
    endpoint = rest_cfg.get("endpoint")
    username = rest_cfg.get("username")
    password_env = rest_cfg.get("password_env") or "WP_REST_PASSWORD"
    password = os.getenv(password_env)

    if not (endpoint and username and password):
        raise ValueError("Missing WordPress REST configuration or credentials")

    return WPConfig(endpoint=endpoint, username=username, password=password)


def fetch_posts(config: WPConfig, per_page: int = 50) -> Iterable[Dict]:
    page = 1
    while True:
        resp = requests.get(
            config.endpoint,
            params={"per_page": per_page, "page": page, "context": "edit"},
            auth=(config.username, config.password),
            timeout=30,
        )
        if resp.status_code == 400 and "rest_post_invalid_page_number" in resp.text:
            break
        resp.raise_for_status()
        data = resp.json()
        if not data:
            break
        for post in data:
            yield post
        if len(data) < per_page:
            break
        page += 1



CONTENT_REPLACEMENTS: List[Tuple[re.Pattern, str]] = [
    (
        re.compile(r"內華達倉庫\s*48小時[\w\s]*?(?:配送|到貨|送達)"),
        "由美國本地團隊提供客服與保固支援，物流時程依承運商為準",
    ),
    (
        re.compile(r"48 ?小時(?:極速)?配送"),
        "提供標準配送並附追蹤碼，實際到貨依地區而定",
    ),
    (
        re.compile(r"48 ?小時(?:內)?(?:快速)?(?:送達|到貨)"),
        "出貨後可追蹤包裹進度，請以承運商更新為準",
    ),
    (
        re.compile(r"48 ?小時(?:極速)?(?:配達|到貨|送到家)"),
        "使用標準配送服務並提供追蹤碼",
    ),
    (
        re.compile(r"[—\\-].*?[0-9０-９]+(?:\s*[-–]\s*[0-9０-９]+)?\s*小時內(?:送達|配送)"),
        "— 提供標準配送服務（附追蹤碼）",
    ),
    (
        re.compile(r"[0-9０-９]+\s*(?:[-–]\s*[0-9０-９]+)?\s*小時內(?:送達|配送)", re.IGNORECASE),
        "採用標準配送服務，依地區約需數日",
    ),
]

TITLE_REPLACEMENTS: List[Tuple[re.Pattern, str]] = [
    (re.compile(r"48小時[^｜]*"), ""),
    (re.compile(r"48小時(?:極速)?(?:配送|送到家|到貨)?"), ""),
    (re.compile(r"快速出貨(?:指南)?"), ""),
    (re.compile(r"出貨指南"), ""),
    (re.compile(r"影音升級指南"), ""),
]

DISCLAIMER = (
    "提醒：配送時效依承運商為準，請以結帳時顯示的預估時程與追蹤碼更新為準。"
)


def normalize_content(html: str) -> str:
    updated = html
    for pattern, replacement in CONTENT_REPLACEMENTS:
        updated = pattern.sub(replacement, updated)

    question_pattern = re.compile(
        r"<h3>Q1：配送到加州需要多久？</h3>.*?</p>",
        re.DOTALL,
    )
    updated = question_pattern.sub(
        "<h3>Q1：如何聯繫客服？</h3>"
        "<p><strong>A：</strong> 可透過官網表單、電話或 Email 聯繫，客服會提供設定、保固與內容建議。</p>",
        updated,
    )

    if any(token in updated for token in ("配送", "送達", "到貨")):
        updated += f"<p>{DISCLAIMER}</p>"

    return updated


def normalize_title(title: str) -> str:
    updated = title
    for pattern, replacement in TITLE_REPLACEMENTS:
        updated = pattern.sub(replacement, updated)
    updated = re.sub(r"[|｜]+", "｜", updated)
    segments = [segment.strip() for segment in updated.split("｜") if segment.strip()]
    cleaned: List[str] = []
    for segment in segments:
        piece = re.sub(r"\s*\+\s*", "+", segment)
        piece = re.sub(r"^(?:\+)+", "", piece).strip(" +-")
        if piece and len(piece) > 1:
            cleaned.append(piece)
    updated = "｜".join(cleaned) if cleaned else updated.strip(" ｜+-")
    if not updated:
        return title.strip()
    return updated


def slugify_title(title: str) -> str:
    cleaned = title.strip()
    cleaned = re.sub(r"[？?！!，,。.:：]+", "-", cleaned)
    cleaned = re.sub(r"\s+", "-", cleaned)
    cleaned = re.sub(r"-+", "-", cleaned).strip("-")
    if not cleaned:
        cleaned = "svicloud"
    return quote(cleaned, safe="-").lower()


def update_post(config: WPConfig, post_id: int, payload: Dict[str, str]) -> None:
    resp = requests.post(
        f"{config.endpoint}/{post_id}",
        json=payload,
        auth=(config.username, config.password),
        timeout=30,
    )
    resp.raise_for_status()


def main() -> None:
    parser = argparse.ArgumentParser(description="Normalize shipping claims in WP posts")
    parser.add_argument("--config", default="automation/blog_automation/config.yaml")
    parser.add_argument("--env", default=".env")
    parser.add_argument("--dry-run", action="store_true")
    args = parser.parse_args()

    wp = load_wp_config(args.config, args.env)

    changed = 0
    for post in fetch_posts(wp):
        content = post.get("content", {}).get("raw") or ""
        title = post.get("title", {}).get("rendered", "")
        slug = post.get("slug", "")

        new_content = normalize_content(content)
        new_title = normalize_title(title)
        payload: Dict[str, str] = {}

        if new_content != content:
            payload["content"] = new_content

        if new_title != title:
            payload["title"] = new_title
            payload["slug"] = slugify_title(new_title)
        elif any(token in slug for token in ("48", "影音", "%e5%bd%b1%e9%9f%b3")):
            payload["slug"] = slugify_title(new_title or title)

        if not payload:
            continue

        changed += 1
        print(f"Updating post {post['id']}: {title[:60]}")
        if not args.dry_run:
            update_post(wp, post["id"], payload)

    print(f"Posts updated: {changed}")


if __name__ == "__main__":
    main()
