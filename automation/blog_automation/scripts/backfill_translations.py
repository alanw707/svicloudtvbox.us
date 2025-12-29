#!/usr/bin/env python3
"""Backfill missing translation meta for existing WordPress posts."""

from __future__ import annotations

import os
import re
import time
from pathlib import Path
from typing import Dict, Iterable, List, Optional, Tuple

import requests
import yaml

try:
    from anthropic import Anthropic
except Exception:  # pragma: no cover
    Anthropic = None  # type: ignore


def load_env(env_path: Path) -> None:
    if not env_path.exists():
        return
    for line in env_path.read_text().splitlines():
        if not line or line.strip().startswith("#"):
            continue
        if "=" in line:
            key, value = line.split("=", 1)
            os.environ.setdefault(key, value)


def has_cjk(text: str) -> bool:
    return bool(re.search(r"[\u4e00-\u9fff]", text or ""))


def sanitize_html(html: str) -> str:
    if not html:
        return html
    cleaned = re.sub(r"<p>[^<]*chinese title[^<]*</p>", "", html, flags=re.IGNORECASE)
    cleaned = re.sub(r"^\s*<h1[^>]*>.*?</h1>\s*", "", cleaned, flags=re.IGNORECASE | re.DOTALL)
    cleaned = cleaned.replace("@@HERO::", "")
    return cleaned.strip()


def strip_html(html: str) -> str:
    text = re.sub(r"<[^>]+>", " ", html or "")
    return re.sub(r"\s+", " ", text).strip()


def build_excerpt(text: str, limit: int = 220) -> str:
    if not text:
        return ""
    text = re.sub(r"(?i)chinese title\s*[:：].*?", "", text).strip()
    sentences = [
        segment.strip()
        for segment in re.split(r"(?<=[。！？!?\.])\s+", text)
        if segment.strip()
    ]
    result: List[str] = []
    for sentence in sentences or [text]:
        if sentence in result:
            continue
        result.append(sentence)
        if len("".join(result)) >= limit:
            break
    return "".join(result)[:limit]


def resolve_posts_endpoint(config: Dict[str, object]) -> str:
    env_endpoint = os.getenv("WP_REST_ENDPOINT", "").strip()
    if env_endpoint:
        base = env_endpoint.rstrip("/")
        if base.endswith("/wp/v2/posts"):
            return base
        if base.endswith("/wp-json"):
            return f"{base}/wp/v2/posts"
        return base

    rest_cfg = config.get("wordpress", {}).get("rest", {})  # type: ignore[assignment]
    endpoint = rest_cfg.get("endpoint") if isinstance(rest_cfg, dict) else None
    if not endpoint:
        raise RuntimeError("WordPress REST endpoint missing")
    return str(endpoint).rstrip("/")


def locale_suffix(locale: str) -> Optional[str]:
    mapping = {
        "zh_TW": "zh_tw",
        "zh_CN": "zh_cn",
        "en_US": "en_us",
    }
    return mapping.get(locale)


def needs_translation(text: str, locale: str) -> bool:
    if not text or not text.strip():
        return True
    if locale.lower().startswith("en"):
        return has_cjk(text)
    if locale.lower().startswith("zh"):
        return not has_cjk(text)
    return False


def translate_title(client: Anthropic, model: str, title: str, target: str, enforce_no_cjk: bool = False) -> str:
    system = (
        "You are a precise localization assistant. Keep brand terms (SVICLOUD, 10P+, 10S) as-is. "
        "Return only the translated title text."
    )
    if enforce_no_cjk:
        system += " Output must be English only; do not include any Chinese characters."
    prompt = f"Target language/locale: {target}\nTranslate the following title:\n\nTITLE:\n{title}"
    completion = client.messages.create(
        model=model,
        max_tokens=200,
        temperature=0.1,
        system=system,
        messages=[{"role": "user", "content": prompt}],
    )
    text = completion.content[0].text if completion and completion.content else ""
    cleaned = text.strip().lstrip("#").strip()
    if cleaned.lower().startswith("title:"):
        cleaned = cleaned.split(":", 1)[1].strip()
    return cleaned.splitlines()[0].strip()


def translate_html(client: Anthropic, model: str, html: str, target: str, enforce_no_cjk: bool = False) -> str:
    system = (
        "You are a precise localization assistant. Translate the text within HTML while keeping tags and structure unchanged. "
        "Do not remove or reorder HTML tags. Return only HTML."
    )
    if enforce_no_cjk:
        system += " Output must be English only; do not include any Chinese characters."
    prompt = (
        f"Target language/locale: {target}\n"
        "Translate the following HTML content. Preserve all tags and links.\n\n"
        f"CONTENT:\n{html}"
    )
    completion = client.messages.create(
        model=model,
        max_tokens=4000,
        temperature=0.1,
        system=system,
        messages=[{"role": "user", "content": prompt}],
    )
    text = completion.content[0].text if completion and completion.content else ""
    return text.strip()


def verify_post_meta(meta: Dict[str, object], locales: List[str]) -> Tuple[bool, List[str]]:
    warnings: List[str] = []
    ok = True
    for locale in locales:
        suffix = locale_suffix(locale)
        if not suffix:
            continue
        content = str(meta.get(f"_svic_content_{suffix}", "") or "")
        title = str(meta.get(f"_svic_title_{suffix}", "") or "")
        desc = str(meta.get(f"_svic_description_{suffix}", "") or "")
        if not content:
            ok = False
            warnings.append(f"missing content {locale}")
        if not title:
            ok = False
            warnings.append(f"missing title {locale}")
        if not desc:
            ok = False
            warnings.append(f"missing description {locale}")
        if locale.lower().startswith("en") and has_cjk(title + content):
            ok = False
            warnings.append(f"english contains CJK {locale}")
        if locale.lower().startswith("zh") and not has_cjk(title + content):
            ok = False
            warnings.append(f"zh locale missing CJK {locale}")
        if re.search(r"chinese title\s*[:：]", content, re.IGNORECASE):
            ok = False
            warnings.append(f"chinese title marker in content {locale}")
    return ok, warnings


def main() -> None:
    root = Path(__file__).resolve().parents[3]
    load_env(root / ".env")

    config_path = root / "automation" / "blog_automation" / "config.yaml"
    config = yaml.safe_load(config_path.read_text()) or {}

    locales = list((config.get("publishing", {}) or {}).get("locales", []) or [])
    base_locale = (config.get("publishing", {}) or {}).get("base_locale", "")

    endpoint = resolve_posts_endpoint(config)
    username = (config.get("wordpress", {}) or {}).get("rest", {}).get("username") or os.getenv("WP_REST_USERNAME")
    password = os.getenv((config.get("wordpress", {}) or {}).get("rest", {}).get("password_env", "WP_REST_PASSWORD"))

    if not username or not password:
        raise RuntimeError("Missing WP REST credentials")

    if Anthropic is None or not os.getenv("CLAUDE_API_KEY"):
        raise RuntimeError("Anthropic client unavailable or CLAUDE_API_KEY missing")

    client = Anthropic(api_key=os.getenv("CLAUDE_API_KEY"))
    model = (config.get("apis", {}) or {}).get("claude_model_full") or "claude-sonnet-4-20250514"

    page = 1
    per_page = 20
    updated = 0
    checked = 0

    while True:
        params = {
            "per_page": per_page,
            "page": page,
            "status": "any",
            "context": "edit",
        }
        resp = requests.get(endpoint, params=params, auth=(username, password), timeout=30)
        if resp.status_code != 200:
            if resp.status_code == 400 and "rest_post_invalid_page_number" in resp.text:
                break
            raise RuntimeError(f"Failed to fetch posts page {page}: {resp.status_code} {resp.text[:200]}")
        posts = resp.json()
        if not posts:
            break

        for post in posts:
            checked += 1
            post_id = post.get("id")
            title_raw = (post.get("title") or {}).get("raw", "") or ""
            content_raw = (post.get("content") or {}).get("raw", "") or ""
            excerpt_raw = (post.get("excerpt") or {}).get("raw", "") or ""
            meta = post.get("meta", {}) or {}

            sanitized_html = sanitize_html(content_raw)
            excerpt_text = build_excerpt(strip_html(sanitized_html))
            if not excerpt_raw or re.search(r"chinese title\\s*[:：]", excerpt_raw, re.IGNORECASE):
                excerpt_raw = excerpt_text

            meta_updates: Dict[str, str] = {}
            for locale in locales:
                suffix = locale_suffix(locale)
                if not suffix:
                    continue
                target = {
                    "zh_TW": "Traditional Chinese (zh-TW)",
                    "zh_CN": "Simplified Chinese (zh-CN)",
                    "en_US": "English (en-US)",
                }.get(locale, locale)

                content_key = f"_svic_content_{suffix}"
                title_key = f"_svic_title_{suffix}"
                desc_key = f"_svic_description_{suffix}"

                existing_content = str(meta.get(content_key, "") or "")
                existing_title = str(meta.get(title_key, "") or "")
                existing_desc = str(meta.get(desc_key, "") or "")

                if locale == base_locale:
                    if needs_translation(existing_content, locale):
                        meta_updates[content_key] = sanitized_html
                    if needs_translation(existing_title, locale):
                        meta_updates[title_key] = title_raw.strip()
                    if needs_translation(existing_desc, locale):
                        meta_updates[desc_key] = excerpt_raw
                    continue

                if needs_translation(existing_title, locale):
                    meta_updates[title_key] = translate_title(
                        client,
                        model,
                        title_raw,
                        target,
                        enforce_no_cjk=locale.lower().startswith("en"),
                    )
                    time.sleep(0.4)

                if needs_translation(existing_content, locale):
                    translated_html = translate_html(
                        client,
                        model,
                        sanitized_html,
                        target,
                        enforce_no_cjk=locale.lower().startswith("en"),
                    )
                    if locale.lower().startswith("en") and has_cjk(translated_html):
                        translated_html = translate_html(
                            client,
                            model,
                            sanitized_html,
                            target,
                            enforce_no_cjk=True,
                        )
                    meta_updates[content_key] = sanitize_html(translated_html)
                    time.sleep(0.6)

                if needs_translation(existing_desc, locale):
                    source_html = meta_updates.get(content_key) or existing_content or sanitized_html
                    meta_updates[desc_key] = build_excerpt(strip_html(source_html))

            payload: Dict[str, object] = {"meta": meta_updates}
            if excerpt_raw:
                payload["excerpt"] = excerpt_raw

            if meta_updates or payload.get("excerpt"):
                resp = requests.post(f"{endpoint}/{post_id}", auth=(username, password), json=payload, timeout=30)
                if resp.status_code < 200 or resp.status_code >= 300:
                    print(f"Post {post_id}: update failed {resp.status_code} {resp.text[:200]}")
                    continue
                updated += 1

                verify = requests.get(f"{endpoint}/{post_id}", params={"context": "edit"}, auth=(username, password), timeout=30)
                if verify.status_code != 200:
                    print(f"Post {post_id}: verify failed {verify.status_code}")
                    continue
                vpost = verify.json()
                ok, warnings = verify_post_meta(vpost.get("meta", {}) or {}, locales)
                if not ok:
                    print(f"Post {post_id}: verify warnings -> {', '.join(warnings)}")
                else:
                    print(f"Post {post_id}: translations ok")

        page += 1

    print(f"Checked {checked} posts. Updated {updated} posts.")


if __name__ == "__main__":
    main()
