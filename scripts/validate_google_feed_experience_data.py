#!/usr/bin/env python3
"""Validate Google Merchant shopping-experience fields in a product feed."""

from __future__ import annotations

import argparse
import sys
import xml.etree.ElementTree as ET
from pathlib import Path


G_NS = "{http://base.google.com/ns/1.0}"
REQUIRED_IMAGE_LINKS = {
    "1204": "svicloud-15p-marketing-v7-bilingual-remote-watermarked.webp",
}


def text_for(item: ET.Element, tag: str) -> str:
    node = item.find(f"{G_NS}{tag}")
    return (node.text or "").strip() if node is not None else ""


def fail(message: str) -> None:
    print(f"FAIL: {message}")
    raise SystemExit(1)


def item_id(item: ET.Element) -> str:
    return text_for(item, "id") or "(missing id)"


def offer_key(offer_id: str) -> str:
    if offer_id in REQUIRED_IMAGE_LINKS:
        return offer_id
    digits = "".join(ch if ch.isdigit() else " " for ch in offer_id).split()
    for candidate in digits:
        if candidate in REQUIRED_IMAGE_LINKS:
            return candidate
    return offer_id


def validate_item(item: ET.Element, min_additional_images: int) -> None:
    offer_id = item_id(item)
    shipping_blocks = item.findall(f"{G_NS}shipping")
    us_shipping = [
        block for block in shipping_blocks
        if ((block.find(f"{G_NS}country").text or "").strip().upper() if block.find(f"{G_NS}country") is not None else "") == "US"
    ]
    if not us_shipping:
        fail(f"{offer_id}: missing US g:shipping block")

    for block in us_shipping:
        service = ((block.find(f"{G_NS}service").text or "").strip() if block.find(f"{G_NS}service") is not None else "")
        price = ((block.find(f"{G_NS}price").text or "").strip() if block.find(f"{G_NS}price") is not None else "")
        min_transit = ((block.find(f"{G_NS}min_transit_time").text or "").strip() if block.find(f"{G_NS}min_transit_time") is not None else "")
        max_transit = ((block.find(f"{G_NS}max_transit_time").text or "").strip() if block.find(f"{G_NS}max_transit_time") is not None else "")
        if not service:
            fail(f"{offer_id}: US shipping missing service")
        if price != "USD 0.00":
            fail(f"{offer_id}: US shipping price should be USD 0.00, got {price or '(missing)'}")
        if not min_transit or not max_transit:
            fail(f"{offer_id}: US shipping missing transit time")

    if not text_for(item, "min_handling_time") or not text_for(item, "max_handling_time"):
        fail(f"{offer_id}: missing handling time")
    if not text_for(item, "return_policy_label"):
        fail(f"{offer_id}: missing return_policy_label")
    if not text_for(item, "image_link"):
        fail(f"{offer_id}: missing image_link")
    required_image_link = REQUIRED_IMAGE_LINKS.get(offer_key(offer_id))
    if required_image_link and required_image_link not in text_for(item, "image_link"):
        fail(f"{offer_id}: image_link must use {required_image_link}")

    additional_images = [
        (node.text or "").strip()
        for node in item.findall(f"{G_NS}additional_image_link")
        if (node.text or "").strip()
    ]
    if len(set(additional_images)) < min_additional_images:
        fail(f"{offer_id}: needs at least {min_additional_images} unique additional images, got {len(set(additional_images))}")


def validate_feed(path: Path, min_additional_images: int) -> None:
    try:
        root = ET.parse(path).getroot()
    except ET.ParseError as error:
        fail(f"invalid XML: {error}")

    items = root.findall(".//item")
    if not items:
        fail("feed contains no item nodes")

    for item in items:
        validate_item(item, min_additional_images)

    print(f"PASS: {len(items)} feed items include shipping, handling, return-policy, and image signals")


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("feed", type=Path, help="Path to Google Merchant XML feed")
    parser.add_argument("--min-additional-images", type=int, default=3)
    args = parser.parse_args()

    validate_feed(args.feed, args.min_additional_images)
    return 0


if __name__ == "__main__":
    sys.exit(main())
