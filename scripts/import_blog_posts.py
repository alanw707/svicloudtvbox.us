#!/usr/bin/env python3
"""
Import Markdown blog drafts from docs/blog/ into WordPress posts via WP-CLI or REST.

Usage:
    # WP-CLI (local container)
    python3 scripts/import_blog_posts.py \
        --wp-cli php tmp/wp-cli.phar \
        --wp-path core \
        --status draft

    # REST (remote site with Application Password)
    python3 scripts/import_blog_posts.py \
        --rest-endpoint https://example.com/wp-json \
        --rest-username autoblog \
        --rest-password 'xxxxxxxxxxxxxxxx' \
        --status draft

By default the script picks up English Markdown files from docs/blog/*.md,
looks for a matching Traditional Chinese translation under docs/blog/zh/,
and maps metadata + content into a single WordPress post. Chinese content is
stored in custom meta fields (_svic_*_zh_tw) to align with the planned
localization framework.
"""

from __future__ import annotations

import argparse
import base64
import json
import shlex
import subprocess
import sys
from pathlib import Path
from typing import Dict, List, Optional, Tuple

try:
    import requests
except ImportError:  # pragma: no cover
    requests = None
import yaml

try:
    import markdown as markdown_lib
except ImportError:  # pragma: no cover - optional dependency
    markdown_lib = None


DEFAULT_DOCS_DIR = Path("docs/blog")


class ImportError(Exception):
    """Lightweight wrapper for import failures."""


def run_wp(
    wp_cli: List[str],
    wp_path: Path,
    args: list[str],
    *,
    capture_output: bool = True,
    input_value: Optional[str] = None,
) -> subprocess.CompletedProcess[str]:
    """Execute a WP-CLI command and return the completed process."""

    cmd = wp_cli + [f"--path={wp_path}"]
    cmd.extend(args)

    result = subprocess.run(
        cmd,
        text=True,
        capture_output=capture_output,
        input=input_value,
        check=False,
    )

    return result


def parse_markdown(path: Path) -> Tuple[Dict, str]:
    """Return (front_matter, body) from a Markdown file with YAML front matter."""

    raw = path.read_text(encoding="utf-8-sig")
    if not raw.startswith("---"):
        raise ImportError(f"{path} missing YAML front matter delimiter '---'.")

    try:
        _, front_raw, body = raw.split("---", 2)
    except ValueError as exc:
        raise ImportError(f"{path} has malformed front matter blocks.") from exc

    try:
        meta = yaml.safe_load(front_raw) or {}
    except yaml.YAMLError as exc:
        raise ImportError(f"Unable to parse front matter for {path}") from exc

    content = body.lstrip("\r\n")
    return meta, content


def markdown_to_html(markdown_text: str) -> str:
    """Convert Markdown to HTML, falling back to the raw text if markdown package missing."""

    if markdown_lib is None:
        return markdown_text

    return markdown_lib.markdown(markdown_text, extensions=["extra", "tables"])


def ensure_category(
    wp_cli: List[str],
    wp_path: Path,
    category_name: str,
    category_slug: Optional[str],
) -> Optional[str]:
    """Return a category term ID, creating the category if it does not exist."""

    if not category_name:
        return None

    query_args = [
        "term",
        "list",
        "category",
        f"--name={category_name}",
        "--field=term_id",
        "--format=ids",
    ]
    result = run_wp(wp_cli, wp_path, query_args)
    if result.returncode == 0 and result.stdout.strip():
        return result.stdout.strip()

    create_args = [
        "term",
        "create",
        "category",
        category_name,
        "--porcelain",
    ]
    if category_slug:
        create_args.append(f"--slug={category_slug}")

    created = run_wp(wp_cli, wp_path, create_args)
    if created.returncode != 0:
        raise ImportError(
            f"Failed to create category '{category_name}': {created.stderr.strip()}"
        )

    return created.stdout.strip()


def upsert_post(
    wp_cli: List[str],
    wp_path: Path,
    status: str,
    category_id: Optional[str],
    english_path: Path,
    zh_path: Optional[Path],
    dry_run: bool = False,
) -> None:
    """Create or update a WordPress post from Markdown sources."""

    en_meta, en_content = parse_markdown(english_path)
    en_html = markdown_to_html(en_content)
    if "slug" not in en_meta:
        en_meta["slug"] = english_path.stem

    slug = str(en_meta["slug"]).strip()
    if not slug:
        raise ImportError(f"{english_path} missing slug value in front matter.")

    zh_meta = {}
    zh_content = ""
    zh_html = ""
    if zh_path and zh_path.exists():
        zh_meta, zh_content = parse_markdown(zh_path)
        zh_html = markdown_to_html(zh_content)

    post_title = en_meta.get("title", slug.replace("-", " ").title())
    post_excerpt = en_meta.get("description", "")
    keywords_en = en_meta.get("keywords", [])

    # Lookup existing post by slug.
    lookup_args = [
        "post",
        "list",
        "--post_type=post",
        "--post_status=any",
        "--fields=ID,post_name",
        "--posts_per_page=-1",
        "--format=json",
    ]
    lookup = run_wp(wp_cli, wp_path, lookup_args)
    post_id = ""
    if lookup.returncode == 0 and lookup.stdout.strip():
        try:
            records = json.loads(lookup.stdout)
        except json.JSONDecodeError:
            records = []
        for record in records:
            if str(record.get("post_name", "")).strip() == slug:
                post_id = str(record.get("ID", "")).strip()
                if post_id:
                    break

    meta_input: Dict[str, str] = {}
    if zh_html:
        meta_input["_svic_title_zh_tw"] = zh_meta.get("title", "")
        meta_input["_svic_description_zh_tw"] = zh_meta.get("description", "")
        meta_input["_svic_content_zh_tw"] = zh_html
        zh_keywords = zh_meta.get("keywords", [])
        if zh_keywords:
            meta_input["_svic_keywords_zh_tw"] = ", ".join(map(str, zh_keywords))

    if keywords_en:
        meta_input["_svic_keywords_en_us"] = ", ".join(map(str, keywords_en))

    meta_arg = ""
    if meta_input:
        meta_arg = json.dumps(meta_input, ensure_ascii=False)

    if dry_run:
        action = "update" if post_id else "create"
        print(f"[dry-run] Would {action} post slug='{slug}' title='{post_title}'")
        if zh_html:
            print(f"          ✓ zh-TW translation found ({zh_path.name})")
        else:
            print("          ⚠️  no zh-TW translation paired")
        return

    if post_id:
        cmd_args: List[str] = [
            "post",
            "update",
            post_id,
        ]
    else:
        cmd_args = [
            "post",
            "create",
            f"--post_name={slug}",
            "--porcelain",
        ]

    cmd_args.extend(
        [
            f"--post_title={post_title}",
            f"--post_status={status}",
            f"--post_content={en_html}",
        ]
    )

    if post_excerpt:
        cmd_args.append(f"--post_excerpt={post_excerpt}")

    if meta_arg:
        cmd_args.append(f"--meta_input={meta_arg}")

    if category_id:
        cmd_args.append(f"--post_category={category_id}")

    result = run_wp(wp_cli, wp_path, cmd_args)
    if result.returncode != 0:
        raise ImportError(
            f"WP-CLI post {'update' if post_id else 'create'} failed for {slug}: "
            f"{result.stderr.strip()}"
        )

    if not post_id:
        post_id = result.stdout.strip()
        print(f"Created post ID {post_id} for slug '{slug}'")
    else:
        print(f"Updated post ID {post_id} for slug '{slug}'")

    # Update zh meta fields after create/update to avoid losing complex
    # characters from JSON escaping.
    if zh_html:
        meta_pairs = {
            "_svic_content_zh_tw": zh_html,
            "_svic_title_zh_tw": meta_input.get("_svic_title_zh_tw", ""),
            "_svic_description_zh_tw": meta_input.get("_svic_description_zh_tw", ""),
        }
        for key, value in meta_pairs.items():
            meta_args = [
                "post",
                "meta",
                "update",
                post_id,
                key,
                "--format=plaintext",
            ]
            meta_result = run_wp(
                wp_cli,
                wp_path,
                meta_args,
                input_value=value,
            )
            if meta_result.returncode != 0:
                raise ImportError(
                    f"Failed to update meta {key} for post {post_id}: "
                    f"{meta_result.stderr.strip()}"
                )


def discover_pairs(docs_dir: Path, zh_dir: Path) -> Dict[Path, Optional[Path]]:
    """Return mapping of English docs to optional zh translations."""

    mapping: Dict[Path, Optional[Path]] = {}
    for path in sorted(docs_dir.glob("*.md")):
        if path.parent == zh_dir:
            continue
        zh_path = zh_dir / path.name
        mapping[path] = zh_path if zh_path.exists() else None

    return mapping


class RestImporter:
    """Push posts via the WordPress REST API using an Application Password."""

    def __init__(
        self,
        base_url: str,
        username: str,
        password: str,
        timeout: float = 10.0,
    ) -> None:
        if not base_url:
            raise ValueError("REST base URL cannot be empty.")

        self.base_url = base_url.rstrip("/")
        if not self.base_url.endswith("/wp-json"):
            # Allow passing site root, append /wp-json automatically.
            self.base_url = f"{self.base_url}/wp-json"

        self.posts_endpoint = f"{self.base_url}/wp/v2/posts"
        self.categories_endpoint = f"{self.base_url}/wp/v2/categories"
        credential = f"{username}:{password}".encode("utf-8")
        token = base64.b64encode(credential).decode("utf-8")
        self.headers = {
            "Authorization": f"Basic {token}",
            "Content-Type": "application/json",
        }
        self.timeout = timeout

    def _request(self, method: str, url: str, *, params=None, json_body=None):
        response = requests.request(
            method,
            url,
            headers=self.headers,
            params=params,
            json=json_body,
            timeout=self.timeout,
        )
        if response.status_code >= 400:
            raise ImportError(
                f"REST {method} {url} failed ({response.status_code}): {response.text}"
            )
        return response.json()

    def ensure_category(self, name: str, slug: Optional[str]) -> Optional[int]:
        if not name:
            return None

        params = {"slug": slug or name}
        existing = self._request("GET", self.categories_endpoint, params=params)
        if isinstance(existing, list) and existing:
            return int(existing[0]["id"])

        payload = {"name": name}
        if slug:
            payload["slug"] = slug
        created = self._request("POST", self.categories_endpoint, json_body=payload)
        return int(created["id"])

    def get_post_by_slug(self, slug: str) -> Optional[int]:
        params = {
            "slug": slug,
            "status": "any",
            "context": "edit",
            "_fields": "id,slug,status",
        }
        data = self._request("GET", self.posts_endpoint, params=params)
        if isinstance(data, list) and data:
            return int(data[0]["id"])
        return None

    def upsert_post(
        self,
        slug: str,
        status: str,
        title: str,
        excerpt: str,
        content_html: str,
        meta: Dict[str, str],
        category_id: Optional[int],
        existing_id: Optional[int] = None,
    ) -> Tuple[int, bool]:
        if existing_id is None:
            existing_id = self.get_post_by_slug(slug)
        payload = {
            "title": title,
            "slug": slug,
            "status": status,
            "content": content_html,
            "excerpt": excerpt,
        }
        if meta:
            payload["meta"] = meta
        if category_id:
            payload["categories"] = [category_id]

        if existing_id:
            endpoint = f"{self.posts_endpoint}/{existing_id}"
            result = self._request("POST", endpoint, json_body=payload)
            return int(result["id"]), False
        else:
            result = self._request("POST", self.posts_endpoint, json_body=payload)
            return int(result["id"]), True


def main() -> int:
    parser = argparse.ArgumentParser(description="Sync docs/blog content into WordPress.")
    parser.add_argument(
        "--wp-cli",
        default=f"php {Path('tmp/wp-cli.phar')}",
        help="Command used to invoke WP-CLI (default: php tmp/wp-cli.phar)",
    )
    parser.add_argument(
        "--wp-path",
        default="core",
        help="Path to the WordPress installation root (default: core)",
    )
    parser.add_argument(
        "--docs-dir",
        default=str(DEFAULT_DOCS_DIR),
        help="Path to the English Markdown docs directory (default: docs/blog)",
    )
    parser.add_argument(
        "--status",
        default="draft",
        choices=["draft", "pending", "publish"],
        help="Post status to apply to imported posts (default: draft)",
    )
    parser.add_argument(
        "--category",
        default="Comparisons",
        help="Category name to assign (created if missing). Empty string to skip.",
    )
    parser.add_argument(
        "--category-slug",
        default="comparisons",
        help="Slug to use when creating the category (default: comparisons).",
    )
    parser.add_argument(
        "--dry-run",
        action="store_true",
        help="Preview actions without invoking WP-CLI modifications.",
    )
    parser.add_argument(
        "--rest-endpoint",
        help="Base REST endpoint (e.g. https://example.com/wp-json). When provided, REST mode is used.",
    )
    parser.add_argument(
        "--rest-username",
        help="REST authentication username (Application Password account).",
    )
    parser.add_argument(
        "--rest-password",
        help="REST authentication password (Application Password).",
    )
    parser.add_argument(
        "--rest-timeout",
        type=float,
        default=10.0,
        help="REST request timeout in seconds (default: 10).",
    )

    args = parser.parse_args()

    docs_dir = Path(args.docs_dir).resolve()
    zh_dir = (docs_dir / "zh") if "zh" not in args.docs_dir else Path(args.docs_dir).resolve()

    if not docs_dir.exists():
        print(f"❌ Docs directory '{docs_dir}' not found.", file=sys.stderr)
        return 1

    use_rest = bool(args.rest_endpoint)
    rest_client: Optional[RestImporter] = None

    if use_rest:
        if not args.rest_username or not args.rest_password:
            print("❌ REST mode requires --rest-username and --rest-password.", file=sys.stderr)
            return 1
        if requests is None:
            print("❌ REST mode requires the 'requests' package. Install it via pip.", file=sys.stderr)
            return 1
        if markdown_lib is None:
            print(
                "⚠️ python-markdown package not found. Sending raw Markdown via REST.",
                file=sys.stderr,
            )
        rest_client = RestImporter(
            base_url=args.rest_endpoint.strip(),
            username=args.rest_username.strip(),
            password=args.rest_password.strip(),
            timeout=args.rest_timeout,
        )
    else:
        wp_cli_cmd = args.wp_cli.strip()
        wp_path = Path(args.wp_path).resolve()

        if not wp_path.exists():
            print(f"❌ WordPress path '{wp_path}' does not exist.", file=sys.stderr)
            return 1

        wp_cli_parts = shlex.split(wp_cli_cmd)
        if not wp_cli_parts:
            print("❌ --wp-cli command is empty.", file=sys.stderr)
            return 1

        if len(wp_cli_parts) >= 2 and "wp-cli.phar" in Path(wp_cli_parts[1]).name:
            phar_path = Path(wp_cli_parts[1]).resolve()
            if not phar_path.exists():
                print(
                    f"❌ WP-CLI phar not found at '{phar_path}'. Download it or adjust --wp-cli.",
                    file=sys.stderr,
                )
                return 1
            wp_cli_parts[1] = str(phar_path)

    pairs = discover_pairs(docs_dir, zh_dir)
    if not pairs:
        print("No Markdown posts found to import.")
        return 0

    category_id: Optional[str] = None
    rest_category_id: Optional[int] = None
    if args.category:
        if args.dry_run:
            print(
                f"[dry-run] Would ensure category '{args.category}' "
                f"(slug '{args.category_slug}') exists."
            )
            category_id = "dry-run"
        else:
            if use_rest and rest_client:
                rest_category_id = rest_client.ensure_category(
                    args.category, args.category_slug
                )
            else:
                category_id = ensure_category(
                    wp_cli_parts,
                    wp_path,
                    args.category,
                    args.category_slug,
                )

    for en_path, zh_path in pairs.items():
        try:
            if use_rest and rest_client:
                en_meta, en_content = parse_markdown(en_path)
                if "slug" not in en_meta:
                    en_meta["slug"] = en_path.stem
                slug = str(en_meta["slug"]).strip()
                if not slug:
                    raise ImportError(f"{en_path} missing slug value in front matter.")

                zh_meta: Dict = {}
                zh_content = ""
                zh_html = ""
                if zh_path and zh_path.exists():
                    zh_meta, zh_content = parse_markdown(zh_path)
                    zh_html = markdown_to_html(zh_content)

                post_title = en_meta.get("title", slug.replace("-", " ").title())
                post_excerpt = en_meta.get("description", "")
                keywords_en = en_meta.get("keywords", [])

                meta_payload: Dict[str, str] = {}
                if zh_html:
                    meta_payload["_svic_content_zh_tw"] = zh_html
                    meta_payload["_svic_title_zh_tw"] = zh_meta.get("title") or ""
                    meta_payload["_svic_description_zh_tw"] = zh_meta.get("description") or ""
                    zh_keywords = zh_meta.get("keywords", [])
                    if zh_keywords:
                        meta_payload["_svic_keywords_zh_tw"] = ", ".join(map(str, zh_keywords))

                if keywords_en:
                    meta_payload["_svic_keywords_en_us"] = ", ".join(map(str, keywords_en))

                if args.dry_run:
                    print(
                        f"[dry-run] Would sync (REST) slug='{slug}' title='{post_title}' "
                        f"status='{args.status}'"
                    )
                    continue

                category_value = rest_category_id if rest_category_id else None
                existing_id = rest_client.get_post_by_slug(slug)
                post_id, created = rest_client.upsert_post(
                    slug=slug,
                    status=args.status,
                    title=post_title,
                    excerpt=post_excerpt,
                    content_html=markdown_to_html(en_content),
                    meta=meta_payload,
                    category_id=category_value,
                    existing_id=existing_id,
                )
                action = "Created" if created or not existing_id else "Updated"
                print(f"{action} post ID {post_id} for slug '{slug}'")
            else:
                upsert_post(
                    wp_cli_parts,
                    wp_path,
                    args.status,
                    None if category_id in (None, "dry-run") else category_id,
                    en_path,
                    zh_path,
                    dry_run=args.dry_run,
                )
        except ImportError as exc:
            print(f"❌ {exc}", file=sys.stderr)
            if args.dry_run:
                continue
            return 1

    return 0


if __name__ == "__main__":
    sys.exit(main())
