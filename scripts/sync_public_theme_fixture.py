#!/usr/bin/env python3
"""Create a local WordPress/WooCommerce theme fixture from production public REST data.

Production access is read-only and uses the WP_REST_* application-password
variables. Local mutation happens only through WP-CLI in svicloud10p-wp, so
local users, orders, customer data, credentials, and infrastructure settings
are never requested or changed.
"""

from __future__ import annotations

import argparse
import base64
import hashlib
import html
import json
import os
import re
import shutil
import subprocess
import sys
import tempfile
import urllib.error
import urllib.parse
import urllib.request
from pathlib import Path
from typing import Any

REPO_ROOT = Path(__file__).resolve().parent.parent
IMPORTER = REPO_ROOT / "scripts" / "import_public_theme_fixture.php"
USER_AGENT = "svic-public-theme-fixture-sync/1.0"


class SameOriginRedirectHandler(urllib.request.HTTPRedirectHandler):
    """Allow redirects only when the scheme/host/port stays unchanged."""

    def redirect_request(self, req, fp, code, msg, headers, newurl):
        if url_origin(req.full_url) != url_origin(newurl):
            return None
        return super().redirect_request(req, fp, code, msg, headers, newurl)


def url_origin(url: str) -> tuple[str, str, int | None]:
    parsed = urllib.parse.urlsplit(url)
    scheme = parsed.scheme.lower()
    hostname = (parsed.hostname or "").lower()
    port = parsed.port
    if port is None:
        port = 443 if scheme == "https" else 80 if scheme == "http" else None
    return scheme, hostname, port


def load_env(path: Path) -> None:
    if not path.is_file():
        return
    for line in path.read_text(encoding="utf-8").splitlines():
        line = line.strip()
        if not line or line.startswith("#") or "=" not in line:
            continue
        key, value = line.split("=", 1)
        if key and key not in os.environ:
            os.environ[key] = value.strip().strip("'\"")


def site_root(endpoint: str) -> str:
    parsed = urllib.parse.urlsplit(endpoint)
    if parsed.scheme not in {"http", "https"} or not parsed.netloc:
        raise ValueError("REST endpoint must be an absolute http(s) URL")
    path = parsed.path.split("/wp-json", 1)[0].rstrip("/")
    return urllib.parse.urlunsplit((parsed.scheme, parsed.netloc, path, "", ""))


def text(value: Any) -> str:
    if isinstance(value, dict):
        return str(value.get("raw") or value.get("rendered") or "")
    return str(value or "")


def validate_endpoint_override(override: str | None, configured: str, option: str) -> None:
    if not override:
        return
    if not configured:
        raise ValueError(f"{option} requires a configured WP_REST_ENDPOINT when credentials are loaded")
    if site_root(override) != site_root(configured):
        raise ValueError(f"{option} must match the configured WP_REST_ENDPOINT when credentials are loaded")


class RestClient:
    def __init__(self, root: str, username: str | None = None, password: str | None = None) -> None:
        self.root = root.rstrip("/")
        token = base64.b64encode(f"{username}:{password}".encode("utf-8")).decode("ascii") if username and password else ""
        self.authorization = f"Basic {token}" if token else ""
        self.opener = urllib.request.build_opener(SameOriginRedirectHandler())

    def get(self, path: str) -> tuple[Any, dict[str, str]]:
        headers = {"Accept": "application/json", "User-Agent": USER_AGENT}
        if self.authorization:
            headers["Authorization"] = self.authorization
        request = urllib.request.Request(f"{self.root}{path}", headers=headers)
        try:
            with self.opener.open(request, timeout=60) as response:
                payload = json.loads(response.read().decode(response.headers.get_content_charset() or "utf-8"))
                return payload, {key.lower(): value for key, value in response.headers.items()}
        except urllib.error.HTTPError as error:
            raise RuntimeError(f"GET {path} failed with HTTP {error.code}") from error
        except urllib.error.URLError as error:
            raise RuntimeError(f"GET {path} failed: {error.reason}") from error

    def collection(self, path: str) -> list[dict[str, Any]]:
        separator = "&" if "?" in path else "?"
        first, headers = self.get(f"{path}{separator}per_page=100&page=1")
        if not isinstance(first, list):
            raise RuntimeError(f"GET {path} did not return a collection")
        pages = int(headers.get("x-wp-totalpages", "1"))
        items = [item for item in first if isinstance(item, dict)]
        for page in range(2, pages + 1):
            payload, _ = self.get(f"{path}{separator}per_page=100&page={page}")
            if not isinstance(payload, list):
                raise RuntimeError(f"GET {path} page {page} did not return a collection")
            items.extend(item for item in payload if isinstance(item, dict))
        return items


def normalize_term(item: dict[str, Any]) -> dict[str, Any]:
    return {key: item.get(key) for key in ("id", "name", "slug", "description", "parent")}


def normalize_post(item: dict[str, Any]) -> dict[str, Any]:
    return {
        "id": item.get("id"),
        "slug": item.get("slug"),
        "date": item.get("date"),
        "title": text(item.get("title")),
        "content": text(item.get("content")),
        "excerpt": text(item.get("excerpt")),
        "parent": item.get("parent", 0),
        "menu_order": item.get("menu_order", 0),
        "featured_media": item.get("featured_media", 0),
        "terms": {
            "category": item.get("categories", []),
            "post_tag": item.get("tags", []),
        },
    }


def normalize_media(item: dict[str, Any]) -> dict[str, Any]:
    return {
        "id": item.get("id"),
        "source_url": item.get("source_url"),
        "parent": item.get("post", 0),
        "status": item.get("status"),
        "alt_text": item.get("alt_text"),
        "title": text(item.get("title")),
        "caption": text(item.get("caption")),
        "description": text(item.get("description")),
    }


def normalize_product(item: dict[str, Any]) -> dict[str, Any]:
    return {
        "id": item.get("id"),
        "name": item.get("name"),
        "slug": item.get("slug"),
        "type": item.get("type"),
        "catalog_visibility": item.get("catalog_visibility"),
        "description": item.get("description"),
        "short_description": item.get("short_description"),
        "sku": item.get("sku"),
        "regular_price": item.get("regular_price"),
        "sale_price": item.get("sale_price"),
        "virtual": item.get("virtual"),
        "featured": item.get("featured"),
        "menu_order": item.get("menu_order"),
        "reviews_allowed": item.get("reviews_allowed"),
        "categories": [category.get("id") for category in item.get("categories", []) if isinstance(category, dict)],
        "tags": [tag.get("id") for tag in item.get("tags", []) if isinstance(tag, dict)],
        "images": [{key: image.get(key) for key in ("id", "src", "name", "alt")} for image in item.get("images", []) if isinstance(image, dict)],
        "attributes": [
            {key: attribute.get(key) for key in ("id", "name", "options", "position", "visible", "variation")}
            for attribute in item.get("attributes", [])
            if isinstance(attribute, dict)
        ],
    }


def normalize_variation(product_id: int, item: dict[str, Any]) -> dict[str, Any]:
    image = item.get("image") if isinstance(item.get("image"), dict) else {}
    return {
        "product_id": product_id,
        "sku": item.get("sku"),
        "regular_price": item.get("regular_price"),
        "sale_price": item.get("sale_price"),
        "price": item.get("price"),
        "description": item.get("description"),
        "attributes": {
            str(attribute.get("name") or ""): str(attribute.get("option") or "")
            for attribute in item.get("attributes", [])
            if isinstance(attribute, dict) and attribute.get("name")
        },
        "image": {key: image.get(key) for key in ("id", "src")},
    }


def normalize_menu(item: dict[str, Any]) -> dict[str, Any]:
    locations = item.get("locations", [])
    return {"id": item.get("id"), "name": item.get("name") or text(item.get("title")), "locations": locations if isinstance(locations, list) else [locations]}


def normalize_menu_item(item: dict[str, Any]) -> dict[str, Any]:
    menus = item.get("menus", [])
    return {
        "id": item.get("id"),
        "menus": menus if isinstance(menus, list) else [menus],
        "parent": item.get("parent", 0),
        "menu_order": item.get("menu_order", 0),
        "title": text(item.get("title")),
        "type": item.get("type", "custom"),
        "object": item.get("object", ""),
        "object_id": item.get("object_id", 0),
        "url": item.get("url", ""),
        "target": item.get("target", ""),
        "classes": item.get("classes", []),
        "xfn": item.get("xfn", ""),
        "description": item.get("description", ""),
    }


def normalize_navigation(item: dict[str, Any]) -> dict[str, Any]:
    return {"title": text(item.get("title")), "slug": item.get("slug"), "content": text(item.get("content"))}


def require_status(items: list[dict[str, Any]], expected: str, resource: str) -> None:
    if any(item.get("status") != expected for item in items):
        raise RuntimeError(f"Cannot verify public classification for {resource}")


def snapshot(client: RestClient, local_url: str) -> dict[str, Any]:
    pages_raw = client.collection("/wp-json/wp/v2/pages?context=edit&status=publish")
    posts_raw = client.collection("/wp-json/wp/v2/posts?context=edit&status=publish")
    products_raw = client.collection("/wp-json/wc/v3/products?status=publish")
    require_status(pages_raw, "publish", "pages")
    require_status(posts_raw, "publish", "posts")
    require_status(products_raw, "publish", "products")

    public_client = RestClient(client.root)
    media_raw = public_client.collection("/wp-json/wp/v2/media?context=view&status=inherit")
    require_status(media_raw, "inherit", "media")

    product_attributes = client.collection("/wp-json/wc/v3/products/attributes")
    attribute_terms: dict[str, list[dict[str, Any]]] = {}
    for attribute in product_attributes:
        attribute_id = int(attribute.get("id") or 0)
        if attribute_id:
            attribute_terms[str(attribute_id)] = [normalize_term(item) for item in client.collection(f"/wp-json/wc/v3/products/attributes/{attribute_id}/terms")]

    variations: list[dict[str, Any]] = []
    for product in products_raw:
        product_id = int(product.get("id") or 0)
        if product_id:
            variations.extend(normalize_variation(product_id, item) for item in client.collection(f"/wp-json/wc/v3/products/{product_id}/variations"))

    settings_raw, _ = client.get("/wp-json/wp/v2/settings?_fields=blogname,blogdescription,show_on_front,page_on_front,page_for_posts,site_icon")
    settings = settings_raw if isinstance(settings_raw, dict) else {}
    return {
        "source_url": client.root,
        "local_url": local_url,
        "pages": [normalize_post(item) for item in pages_raw],
        "posts": [normalize_post(item) for item in posts_raw],
        "media": [normalize_media(item) for item in media_raw],
        "categories": [normalize_term(item) for item in client.collection("/wp-json/wp/v2/categories?context=view")],
        "tags": [normalize_term(item) for item in client.collection("/wp-json/wp/v2/tags?context=view")],
        "products": [normalize_product(item) for item in products_raw],
        "variations": variations,
        "product_categories": [normalize_term(item) for item in client.collection("/wp-json/wc/v3/products/categories")],
        "product_tags": [normalize_term(item) for item in client.collection("/wp-json/wc/v3/products/tags")],
        "product_attributes": [{key: item.get(key) for key in ("id", "name", "slug", "type", "order_by", "has_archives")} for item in product_attributes],
        "attribute_terms": attribute_terms,
        "menus": [normalize_menu(item) for item in client.collection("/wp-json/wp/v2/menus?context=view")],
        "menu_items": [normalize_menu_item(item) for item in client.collection("/wp-json/wp/v2/menu-items?context=view")],
        "navigation": [normalize_navigation(item) for item in client.collection("/wp-json/wp/v2/navigation?context=view")],
        "settings": {key: settings.get(key) for key in ("blogname", "blogdescription", "show_on_front", "page_on_front", "page_for_posts", "site_icon")},
    }


def fixture_counts(fixture: dict[str, Any]) -> str:
    keys = ("pages", "posts", "media", "categories", "products", "variations", "product_categories", "menus", "menu_items", "navigation")
    return ", ".join(f"{key}={len(fixture.get(key, []))}" for key in keys)


def run(command: list[str], *, input_bytes: bytes | None = None) -> None:
    completed = subprocess.run(command, input=input_bytes, check=False, capture_output=True)
    if completed.returncode:
        message = completed.stderr.decode("utf-8", errors="replace").strip() or "command failed"
        raise RuntimeError(f"Local import failed: {message}")


def capture(command: list[str]) -> str:
    completed = subprocess.run(command, check=False, capture_output=True)
    if completed.returncode:
        message = completed.stderr.decode("utf-8", errors="replace").strip() or "command failed"
        raise RuntimeError(f"Local verification failed: {message}")
    return completed.stdout.decode("utf-8", errors="replace").strip()


def wp_eval(container: str, code: str) -> str:
    return capture(["docker", "exec", container, "wp", "eval", code, "--allow-root"])


def normalised_hash(value: str, *, title: bool = False) -> str:
    value = html.unescape(value)
    value = re.sub(r"https?://[^\s\"'<>()]+", "[url]", value)
    if title:
        value = re.sub(r"<[^>]*>", "", value).strip()
    return hashlib.sha256(value.encode("utf-8")).hexdigest()


def local_source_ids(container: str, post_type: str) -> set[int]:
    code = (
        "$ids = get_posts(array('post_type' => '" + post_type + "', 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids', 'meta_key' => '_svic_source_fixture_id')); "
        "$sources = array(); foreach ($ids as $id) { $sources[] = (int) get_post_meta((int) $id, '_svic_source_fixture_id', true); } sort($sources); echo implode(',', array_filter($sources));"
    )
    raw = wp_eval(container, code)
    return {int(value) for value in raw.split(',') if value}


def local_fixture_hash(container: str, source_id: int, field: str) -> str:
    parts = [
        "$posts = get_posts(array('post_type' => array('page', 'post', 'product', 'product_variation', 'attachment', 'nav_menu_item'), 'post_status' => 'any', 'meta_key' => '_svic_source_fixture_id', ",
        f"'meta_value' => {source_id}, 'numberposts' => 1)); ",
        "if (!$posts) { exit(3); } ",
        f"$value = get_post_type($posts[0]) === 'nav_menu_item' ? wp_setup_nav_menu_item($posts[0])->{ 'title' if field == 'post_title' else 'description' } : get_post_field('{field}', $posts[0]->ID); ",
        "$value = html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5); ",
    ]
    if field == "post_title":
        parts.append("$value = wp_strip_all_tags($value); ")
    parts.extend([
        "$value = preg_replace('~https?://[^\\s\"\\\'<>()]+~', '[url]', $value); ",
        "echo hash('sha256', $value);",
    ])
    return wp_eval(container, "".join(parts))


def verify_fixture(fixture: dict[str, Any], container: str) -> None:
    checks: list[tuple[str, str, str]] = []
    for resource, field, key in (("pages", "post_title", "title"), ("pages", "post_content", "content"), ("products", "post_title", "name"), ("products", "post_content", "description")):
        items = fixture.get(resource, [])
        if items:
            item = items[0]
            checks.append((resource, local_fixture_hash(container, int(item["id"]), field), normalised_hash(str(item.get(key, "")), title=field == "post_title")))
    media = fixture.get("media", [])
    if media:
        item = media[0]
        checks.append(("media", local_fixture_hash(container, int(item["id"]), "post_title"), normalised_hash(str(item.get("title", "")), title=True)))
    menu_items = fixture.get("menu_items", [])
    if menu_items:
        item = menu_items[0]
        checks.append(("menu item", local_fixture_hash(container, int(item["id"]), "post_title"), normalised_hash(str(item.get("title", "")), title=True)))
    failures = [resource for resource, actual, expected in checks if actual != expected]
    if failures:
        raise RuntimeError(f"Representative content verification failed: {', '.join(failures)}")

    complete_checks = (
        ('pages', 'page'),
        ('posts', 'post'),
        ('media', 'attachment'),
        ('products', 'product'),
        ('menu_items', 'nav_menu_item'),
    )
    complete_failures = []
    for resource, post_type in complete_checks:
        expected_ids = {int(item['id']) for item in fixture.get(resource, []) if int(item.get('id') or 0) > 0}
        actual_ids = local_source_ids(container, post_type)
        if actual_ids != expected_ids:
            complete_failures.append(f"{resource} expected={len(expected_ids)} actual={len(actual_ids)}")
    if complete_failures:
        raise RuntimeError(f"Complete fixture manifest verification failed: {', '.join(complete_failures)}")

    imported_menu_items = wp_eval(container, "echo count(get_posts(array('post_type' => 'nav_menu_item', 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids')));")
    if imported_menu_items != str(len(menu_items)):
        raise RuntimeError("Menu item count does not match the production fixture")

def private_counts(container: str) -> str:
    return wp_eval(container, "global $wpdb; $counts = array((int) count_users()['total_users']); foreach (array('shop_order', 'shop_order_refund') as $type) { $counts[] = (int) $wpdb->get_var($wpdb->prepare(\"SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s\", $type)); } foreach (array('wc_orders', 'wc_customer_lookup') as $suffix) { $table = $wpdb->prefix . $suffix; $exists = (int) $wpdb->get_var($wpdb->prepare(\"SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = %s\", $table)); $counts[] = $exists ? (int) $wpdb->get_var(\"SELECT COUNT(*) FROM `$table`\") : 0; } $private_statuses = \"'draft','pending','private','future','trash'\"; $counts[] = (int) $wpdb->get_var(\"SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type IN ('page','post','product','wp_navigation') AND post_status IN ({$private_statuses})\"); $counts[] = (int) $wpdb->get_var(\"SELECT COUNT(*) FROM {$wpdb->posts} child INNER JOIN {$wpdb->posts} parent ON parent.ID = child.post_parent WHERE child.post_type IN ('attachment','product_variation') AND parent.post_status IN ({$private_statuses})\"); echo implode(',', $counts);")


def copy_to_container(source: Path, container: str, destination: str) -> None:
    run(["docker", "exec", "-i", container, "sh", "-c", f"cat > {destination}"], input_bytes=source.read_bytes())


def apply_fixture(fixture: dict[str, Any], container: str) -> None:
    if not IMPORTER.is_file():
        raise RuntimeError(f"Importer is missing: {IMPORTER}")
    private_before = private_counts(container)
    remote_fixture = "/tmp/svic-public-theme-fixture.json"
    remote_importer = "/tmp/svic-import-public-theme-fixture.php"
    with tempfile.TemporaryDirectory(prefix="svic-public-fixture-") as temp_dir:
        fixture_path = Path(temp_dir) / "fixture.json"
        fixture_path.write_text(json.dumps(fixture, ensure_ascii=False), encoding="utf-8")
        fixture_path.chmod(0o600)
        try:
            copy_to_container(fixture_path, container, remote_fixture)
            copy_to_container(IMPORTER, container, remote_importer)
            run(["docker", "exec", container, "wp", "eval-file", remote_importer, "--allow-root", "--", remote_fixture])
            verify_fixture(fixture, container)
            if private_counts(container) != private_before:
                raise RuntimeError("Local private-record counts changed during the fixture sync")
        finally:
            subprocess.run(["docker", "exec", container, "rm", "-f", remote_fixture, remote_importer], check=False, capture_output=True)


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--env-file", default=".env", help="Credential file (default: .env)")
    parser.add_argument("--source-url", help="Override WP_REST_ENDPOINT")
    parser.add_argument("--local-url", help="Override WP_REST_LOCAL_ENDPOINT")
    parser.add_argument("--container", default="svicloud10p-wp", help="Local WordPress container")
    parser.add_argument("--apply", action="store_true", help="Replace local public fixture content after fetching it")
    args = parser.parse_args()

    load_env(Path(args.env_file))
    configured_endpoint = os.environ.get("WP_REST_ENDPOINT", "")
    endpoint = args.source_url or configured_endpoint
    username = os.environ.get("WP_REST_USERNAME", "")
    password = os.environ.get("WP_REST_PASSWORD", "")
    local_endpoint = args.local_url or os.environ.get("WP_REST_LOCAL_ENDPOINT", "")
    if not endpoint or not username or not password or not local_endpoint:
        print("Missing source REST credentials or local endpoint configuration.", file=sys.stderr)
        return 2

    try:
        validate_endpoint_override(args.source_url, configured_endpoint, "--source-url")
        client = RestClient(site_root(endpoint), username, password)
        fixture = snapshot(client, site_root(local_endpoint))
        print(f"Public production fixture fetched in memory: {fixture_counts(fixture)}")
        print("Requested resources: pages, posts, media, catalog, menus, navigation, and field-limited display settings.")
        print("Not requested: users, orders, customers, payment data, credentials, logs, or private plugin data.")
        if args.apply:
            apply_fixture(fixture, args.container)
            print("Local public theme fixture replaced. Local users and infrastructure were not modified.")
        else:
            print("Dry run only. Re-run with --apply to replace local public fixture content.")
        return 0
    except (RuntimeError, ValueError, OSError, json.JSONDecodeError) as error:
        print(f"Public fixture sync failed: {error}", file=sys.stderr)
        return 2


if __name__ == "__main__":
    raise SystemExit(main())
