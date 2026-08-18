#!/usr/bin/env python3
"""Seed private local content, apply the public fixture, and prove it survives."""

from __future__ import annotations

import argparse
import json
import subprocess
import sys
import uuid
from pathlib import Path


def wp_eval(container: str, code: str) -> str:
    completed = subprocess.run(
        ["docker", "exec", container, "wp", "eval", code, "--allow-root"],
        check=False,
        capture_output=True,
        text=True,
    )
    if completed.returncode:
        raise RuntimeError(completed.stderr.strip() or "WP-CLI command failed")
    return completed.stdout.strip()


def private_snapshot(container: str) -> dict[str, int]:
    code = """
    global $wpdb;
    $statuses = "'draft','pending','private','future','trash'";
    echo wp_json_encode(array(
        'content' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type IN ('page','post','product','wp_navigation') AND post_status IN ({$statuses})"),
        'children' => (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} child INNER JOIN {$wpdb->posts} parent ON parent.ID = child.post_parent WHERE child.post_type IN ('attachment','product_variation') AND parent.post_status IN ({$statuses})")
    ));
    """
    return json.loads(wp_eval(container, code))


def seed(container: str, token: str) -> dict[str, int]:
    code = f"""
    require_once ABSPATH . 'wp-admin/includes/image.php';
    $token = '{token}';
    $page_id = wp_insert_post(array('post_type' => 'page', 'post_status' => 'draft', 'post_title' => $token . '-page', 'post_content' => $token . '-page-content'));
    $post_id = wp_insert_post(array('post_type' => 'post', 'post_status' => 'private', 'post_title' => $token . '-post', 'post_content' => $token . '-post-content'));
    $navigation_id = wp_insert_post(array('post_type' => 'wp_navigation', 'post_status' => 'draft', 'post_title' => $token . '-navigation', 'post_content' => '<!-- wp:navigation-link {{"label":"Private probe","url":"#"}} /-->'));
    $term = wp_insert_term($token . '-category', 'product_cat', array('slug' => $token . '-category'));
    $term_id = is_wp_error($term) ? (int) $term->get_error_data('term_exists') : (int) $term['term_id'];
    $product = new WC_Product_Simple();
    $product->set_name($token . '-product');
    $product->set_slug($token . '-product');
    $product->set_status('draft');
    $product->set_regular_price('99.99');
    $product->set_category_ids(array($term_id));
    $product_id = $product->save();
    $source = get_stylesheet_directory() . '/assets/images/svicloud-hero-product.webp';
    $uploads = wp_upload_dir();
    $filename = wp_unique_filename($uploads['path'], $token . '.webp');
    $destination = trailingslashit($uploads['path']) . $filename;
    if (!copy($source, $destination)) {{ WP_CLI::error('Could not create preservation attachment'); }}
    $attachment_id = wp_insert_attachment(array('post_mime_type' => 'image/webp', 'post_title' => $token . '-attachment', 'post_status' => 'inherit', 'post_parent' => $page_id), $destination, $page_id);
    wp_update_attachment_metadata($attachment_id, wp_generate_attachment_metadata($attachment_id, $destination));
    foreach (array($page_id, $post_id, $navigation_id, $product_id, $attachment_id) as $id) {{ update_post_meta($id, '_svic_preservation_probe', $token); }}
    echo wp_json_encode(array('page' => $page_id, 'post' => $post_id, 'navigation' => $navigation_id, 'product' => $product_id, 'attachment' => $attachment_id, 'term' => $term_id));
    """
    return json.loads(wp_eval(container, code))


def verify(container: str, token: str, ids: dict[str, int]) -> dict[str, object]:
    encoded_ids = json.dumps(ids)
    code = f"""
    $token = '{token}';
    $ids = json_decode('{encoded_ids}', true);
    $term = get_term((int) $ids['term'], 'product_cat');
    $product = wc_get_product((int) $ids['product']);
    $attachment_path = get_attached_file((int) $ids['attachment']);
    echo wp_json_encode(array(
        'page' => get_post_status((int) $ids['page']),
        'page_content' => get_post_field('post_content', (int) $ids['page']),
        'post' => get_post_status((int) $ids['post']),
        'navigation' => get_post_status((int) $ids['navigation']),
        'product' => $product ? $product->get_status() : '',
        'product_price' => $product ? $product->get_regular_price() : '',
        'product_categories' => $product ? $product->get_category_ids() : array(),
        'term' => $term instanceof WP_Term ? $term->slug : '',
        'attachment' => get_post_field('post_status', (int) $ids['attachment']),
        'attachment_parent' => (int) wp_get_post_parent_id((int) $ids['attachment']),
        'attachment_file' => is_string($attachment_path) && is_file($attachment_path),
        'probe_count' => count(get_posts(array('post_type' => array('page', 'post', 'product', 'attachment', 'wp_navigation'), 'post_status' => 'any', 'numberposts' => -1, 'meta_key' => '_svic_preservation_probe', 'meta_value' => $token, 'fields' => 'ids')))
    ));
    """
    result = json.loads(wp_eval(container, code))
    expected = {
        "page": "draft",
        "page_content": f"{token}-page-content",
        "post": "private",
        "navigation": "draft",
        "product": "draft",
        "product_price": "99.99",
        "term": f"{token}-category",
        "attachment": "inherit",
        "attachment_parent": ids["page"],
        "attachment_file": True,
        "probe_count": 5,
    }
    for key, value in expected.items():
        if result.get(key) != value:
            raise RuntimeError(f"Preservation check failed for {key}: {result.get(key)!r} != {value!r}")
    if ids["term"] not in result.get("product_categories", []):
        raise RuntimeError("Preserved draft product lost its category")
    return result


def cleanup(container: str, token: str) -> None:
    code = f"""
    $token = '{token}';
    $ids = get_posts(array('post_type' => array('page', 'post', 'product', 'attachment', 'wp_navigation'), 'post_status' => 'any', 'numberposts' => -1, 'meta_key' => '_svic_preservation_probe', 'meta_value' => $token, 'fields' => 'ids'));
    foreach ($ids as $id) {{ wp_delete_post((int) $id, true); }}
    $term = get_term_by('slug', $token . '-category', 'product_cat');
    if ($term instanceof WP_Term) {{ wp_delete_term((int) $term->term_id, 'product_cat'); }}
    """
    wp_eval(container, code)


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--env-file", default=".env")
    parser.add_argument("--container", default="svicloud10p-wp")
    args = parser.parse_args()
    token = "svic-preserve-" + uuid.uuid4().hex[:10]
    baseline = private_snapshot(args.container)
    ids: dict[str, int] = {}
    try:
        ids = seed(args.container, token)
        subprocess.run(
            [sys.executable, "scripts/sync_public_theme_fixture.py", "--env-file", args.env_file, "--container", args.container, "--apply"],
            check=True,
            stdout=subprocess.DEVNULL,
        )
        result = verify(args.container, token, ids)
        print(json.dumps({"preserved": result, "baseline": baseline}, ensure_ascii=False))
    finally:
        if ids:
            cleanup(args.container, token)
    if private_snapshot(args.container) != baseline:
        raise RuntimeError("Private-content counts did not return to baseline after probe cleanup")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
