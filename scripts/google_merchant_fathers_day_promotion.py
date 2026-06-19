#!/usr/bin/env python3
"""Create/update the SVICLOUD Father's Day promotion in Google Merchant Center.

Uses Merchant API REST endpoints with a service-account JSON key.
Secret file default: secrets/google-merchant-service-account.json
"""

from __future__ import annotations

import argparse
import json
import os
import sys
from datetime import datetime
from pathlib import Path
from typing import Any
from zoneinfo import ZoneInfo

try:
    from google.oauth2 import service_account
    from google.auth.transport.requests import AuthorizedSession
except ImportError as exc:  # pragma: no cover - operator guidance
    raise SystemExit(
        "Missing Google auth libraries. Install with: "
        "python3 -m pip install google-auth requests"
    ) from exc


SCOPE = "https://www.googleapis.com/auth/content"
ACCOUNT_ID = "5317978135"
PROMOTION_ID = "dad2026-svic-10s"
PROMOTION_CODE = "DAD2026"
TITLE = "Father's Day: 10% off SVICLOUD 10S"
DESCRIPTION = "Celebrate Dad with a better way to stream. Save 10% on SVICLOUD 10S with code DAD2026."
DEFAULT_KEY_PATH = "secrets/google-merchant-service-account.json"
DEFAULT_TIMEZONE = "America/Los_Angeles"


class MerchantApiError(RuntimeError):
    pass


def api_request(session: AuthorizedSession, method: str, url: str, **kwargs: Any) -> dict[str, Any]:
    response = session.request(method, url, timeout=45, **kwargs)
    if response.status_code >= 400:
        body = response.text[:2000]
        raise MerchantApiError(f"{method} {url} failed: HTTP {response.status_code}\n{body}")
    if not response.text.strip():
        return {}
    return response.json()


def rfc3339_local(value: str, timezone_name: str) -> str:
    tz = ZoneInfo(timezone_name)
    dt = datetime.strptime(value, "%Y-%m-%d %H:%M:%S").replace(tzinfo=tz)
    return dt.isoformat()


def get_session(key_path: str) -> AuthorizedSession:
    credentials = service_account.Credentials.from_service_account_file(key_path, scopes=[SCOPE])
    return AuthorizedSession(credentials)


def list_data_sources(session: AuthorizedSession, account_id: str) -> list[dict[str, Any]]:
    url = f"https://merchantapi.googleapis.com/datasources/v1/accounts/{account_id}/dataSources?pageSize=1000"
    data = api_request(session, "GET", url)
    return data.get("dataSources", [])


def find_or_create_promotion_data_source(
    session: AuthorizedSession,
    account_id: str,
    *,
    content_language: str,
    target_country: str,
    explicit_data_source_id: str | None,
    create_if_missing: bool,
) -> str:
    if explicit_data_source_id:
        if explicit_data_source_id.startswith("accounts/"):
            return explicit_data_source_id
        return f"accounts/{account_id}/dataSources/{explicit_data_source_id}"

    for source in list_data_sources(session, account_id):
        promo_source = source.get("promotionDataSource") or {}
        if (
            promo_source.get("contentLanguage") == content_language
            and promo_source.get("targetCountry") == target_country
        ):
            name = source.get("name")
            if name:
                return name

    if not create_if_missing:
        raise MerchantApiError("No en/US promotion data source found. Pass --data-source-id or allow creation.")

    url = f"https://merchantapi.googleapis.com/datasources/v1/accounts/{account_id}/dataSources"
    body = {
        "displayName": "SVICLOUD Promotions API",
        "promotionDataSource": {
            "contentLanguage": content_language,
            "targetCountry": target_country,
        },
    }
    created = api_request(session, "POST", url, json=body)
    name = created.get("name")
    if not name:
        raise MerchantApiError(f"Promotion data source created without a name: {json.dumps(created, indent=2)}")
    return name


def list_products(session: AuthorizedSession, account_id: str) -> list[dict[str, Any]]:
    products: list[dict[str, Any]] = []
    page_token = ""
    while True:
        url = f"https://merchantapi.googleapis.com/products/v1/accounts/{account_id}/products?pageSize=1000"
        if page_token:
            url += f"&pageToken={page_token}"
        data = api_request(session, "GET", url)
        products.extend(data.get("products", []))
        page_token = data.get("nextPageToken", "")
        if not page_token:
            return products


def product_search_text(product: dict[str, Any]) -> str:
    attrs = product.get("productAttributes") or {}
    pieces = [
        product.get("offerId", ""),
        product.get("name", ""),
        attrs.get("title", ""),
        attrs.get("link", ""),
        attrs.get("canonicalLink", ""),
    ]
    return " ".join(str(piece) for piece in pieces if piece).lower()


def find_10s_offer_id(session: AuthorizedSession, account_id: str) -> str | None:
    products = list_products(session, account_id)
    for product in products:
        text = product_search_text(product)
        if "10s" in text or "svicloud-10s" in text:
            offer_id = product.get("offerId")
            if offer_id:
                return str(offer_id)
    return None


def promotion_body(
    account_id: str,
    data_source: str,
    *,
    offer_id: str | None,
    start_time: str,
    end_time: str,
) -> dict[str, Any]:
    attributes: dict[str, Any] = {
        "productApplicability": "SPECIFIC_PRODUCTS" if offer_id else "ALL_PRODUCTS",
        "offerType": "GENERIC_CODE",
        "genericRedemptionCode": PROMOTION_CODE,
        "longTitle": TITLE,
        "couponValueType": "PERCENT_OFF",
        "percentOff": "10",
        "promotionDestinations": ["SHOPPING_ADS", "FREE_LISTINGS"],
        "promotionEffectiveTimePeriod": {
            "startTime": start_time,
            "endTime": end_time,
        },
        "promotionDisplayTimePeriod": {
            "startTime": start_time,
            "endTime": end_time,
        },
        "promotionUrl": "https://svicloudtvbox.us/shop/",
    }
    if offer_id:
        attributes["itemIdInclusion"] = [offer_id]

    return {
        "dataSource": data_source,
        "promotion": {
            "promotionId": PROMOTION_ID,
            "contentLanguage": "en",
            "targetCountry": "US",
            "redemptionChannel": ["ONLINE"],
            "attributes": attributes,
            "customAttributes": [
                {"name": "svic_description", "value": DESCRIPTION},
                {"name": "svic_coupon_code", "value": PROMOTION_CODE},
            ],
        },
    }


def insert_promotion(session: AuthorizedSession, account_id: str, body: dict[str, Any]) -> dict[str, Any]:
    url = f"https://merchantapi.googleapis.com/promotions/v1/accounts/{account_id}/promotions:insert"
    return api_request(session, "POST", url, json=body)


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="Create/update the Father's Day Merchant Center promotion.")
    parser.add_argument("--account-id", default=os.getenv("GOOGLE_MERCHANT_ACCOUNT_ID", ACCOUNT_ID))
    parser.add_argument("--key-file", default=os.getenv("GOOGLE_APPLICATION_CREDENTIALS", DEFAULT_KEY_PATH))
    parser.add_argument("--data-source-id", default=os.getenv("GOOGLE_MERCHANT_PROMOTION_DATA_SOURCE_ID"))
    parser.add_argument("--timezone", default=os.getenv("SVIC_PROMO_TIMEZONE", DEFAULT_TIMEZONE))
    parser.add_argument("--start", default="2026-06-19 00:00:00")
    parser.add_argument("--end", default="2026-06-22 23:59:59")
    parser.add_argument("--no-create-data-source", action="store_true")
    parser.add_argument("--dry-run", action="store_true")
    return parser.parse_args()


def main() -> int:
    args = parse_args()
    key_path = Path(args.key_file)
    if not key_path.exists():
        raise SystemExit(f"Service account JSON not found: {key_path}")

    session = get_session(str(key_path))
    data_source = find_or_create_promotion_data_source(
        session,
        args.account_id,
        content_language="en",
        target_country="US",
        explicit_data_source_id=args.data_source_id,
        create_if_missing=not args.no_create_data_source,
    )
    offer_id = find_10s_offer_id(session, args.account_id)
    start_time = rfc3339_local(args.start, args.timezone)
    end_time = rfc3339_local(args.end, args.timezone)
    body = promotion_body(args.account_id, data_source, offer_id=offer_id, start_time=start_time, end_time=end_time)

    if args.dry_run:
        safe_body = json.loads(json.dumps(body))
        print(json.dumps({"dataSource": data_source, "matched10sOfferId": offer_id, "request": safe_body}, indent=2))
        return 0

    response = insert_promotion(session, args.account_id, body)
    print(json.dumps({
        "status": "submitted",
        "accountId": args.account_id,
        "dataSource": data_source,
        "matched10sOfferId": offer_id,
        "promotionId": PROMOTION_ID,
        "promotionName": response.get("name"),
    }, indent=2))
    return 0


if __name__ == "__main__":
    try:
        raise SystemExit(main())
    except MerchantApiError as exc:
        print(str(exc), file=sys.stderr)
        raise SystemExit(1) from exc
