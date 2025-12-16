#!/usr/bin/env python3
"""
Report blog translation coverage for post title registry keys.

This repo localizes blog titles via translation registry keys:
  blog.posts.{slug}.title

This script compares exported markdown slugs under:
  docs/blog/*.md
  docs/blog/zh/*.md
  docs/blog/zh-cn/*.md
against title entries in:
  theme/svicloudtvbox-lumen/lang/en_US.php
  theme/svicloudtvbox-lumen/lang/zh_TW.php
  theme/svicloudtvbox-lumen/lang/zh_CN.php

Usage:
  python3 scripts/report_blog_translation_coverage.py
  python3 scripts/report_blog_translation_coverage.py --fail
"""

from __future__ import annotations

import argparse
from dataclasses import dataclass
from pathlib import Path


@dataclass(frozen=True)
class LocaleConfig:
    label: str
    lang_file: Path
    docs_dir: Path


def list_slugs(markdown_dir: Path) -> list[str]:
    if not markdown_dir.exists():
        return []

    slugs: list[str] = []
    for file_path in sorted(markdown_dir.glob("*.md")):
        if file_path.name.lower() == "index.md":
            continue
        slugs.append(file_path.stem)
    return slugs


def has_title_entry(lang_content: str, slug: str) -> bool:
    # Heuristic: match the array key and ensure there's a title field nearby.
    candidates = [
        f"'{slug}' =>",
        f'"{slug}" =>',
    ]
    idx = -1
    for needle in candidates:
        idx = lang_content.find(needle)
        if idx != -1:
            break
    if idx == -1:
        return False

    window = lang_content[idx : idx + 1500]
    return ("'title'" in window) or ('"title"' in window)


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--fail", action="store_true", help="Exit non-zero if any missing titles are found.")
    args = parser.parse_args()

    root = Path(__file__).resolve().parent.parent

    locales = [
        LocaleConfig(
            label="en_US",
            lang_file=root / "theme/svicloudtvbox-lumen/lang/en_US.php",
            docs_dir=root / "docs/blog",
        ),
        LocaleConfig(
            label="zh_TW",
            lang_file=root / "theme/svicloudtvbox-lumen/lang/zh_TW.php",
            docs_dir=root / "docs/blog/zh",
        ),
        LocaleConfig(
            label="zh_CN",
            lang_file=root / "theme/svicloudtvbox-lumen/lang/zh_CN.php",
            docs_dir=root / "docs/blog/zh-cn",
        ),
    ]

    missing_any = False

    for locale in locales:
        slugs = list_slugs(locale.docs_dir)
        if not slugs:
            print(f"[{locale.label}] No markdown exports found in {locale.docs_dir}")
            continue

        if not locale.lang_file.exists():
            print(f"[{locale.label}] Missing lang file: {locale.lang_file}")
            missing_any = True
            continue

        content = locale.lang_file.read_text(encoding="utf-8")

        missing = [slug for slug in slugs if not has_title_entry(content, slug)]
        if missing:
            missing_any = True
            print(f"[{locale.label}] Missing blog.posts.*.title for {len(missing)} slugs in {locale.lang_file}:")
            for slug in missing:
                print(f"  - {slug}")
        else:
            print(f"[{locale.label}] OK ({len(slugs)} slugs covered)")

    if args.fail and missing_any:
        return 1
    return 0


if __name__ == "__main__":
    raise SystemExit(main())

