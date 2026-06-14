#!/usr/bin/env python3
"""Static regression checks for agent-readable resources and guide answer hubs."""
from pathlib import Path
import re
import sys

ROOT = Path(__file__).resolve().parents[1]
AGENT = ROOT / "theme/svicloudtvbox-lumen/inc/agent-resources.php"
GUIDE = ROOT / "theme/svicloudtvbox-lumen/page-guide-section.php"
CSS = ROOT / "theme/svicloudtvbox-lumen/assets/css/guides.css"
DECISION = ROOT / "theme/svicloudtvbox-lumen/inc/decision-pages.php"
GUIDE_ROUTES = ROOT / "theme/svicloudtvbox-lumen/inc/guide-routes.php"
SITEMAP = ROOT / "theme/svicloudtvbox-lumen/inc/agent-sitemap.php"

REQUIRED_ENDPOINTS = [
    "llms.txt",
    "llms-full.txt",
    "agent/products.md",
    "agent/compare-10p-vs-10s.md",
    "agent/apps.md",
    "agent/troubleshooting.md",
    "agent/setup.md",
    "agent/shipping-returns.md",
    "agent/contact.md",
]
REQUIRED_TERMS = ["SVICLOUD 10P+", "SVICLOUD 10S", "702-389-3416", "/compare/", "/contact/"]
FORBIDDEN_PHONE_PATTERNS = ["7023893416", "702-389-3415", "702-389-3417"]
REQUIRED_DECISION_SLUGS = [
    "svicloud-10p-vs-10s",
    "best-svicloud-box-for-chinese-tv-usa",
    "yogurt-tv-not-working-upgrade-guide",
    "svicloud-box-authenticity-guide",
]


def fail(message: str) -> None:
    print(f"FAIL: {message}")
    sys.exit(1)


def main() -> None:
    agent = AGENT.read_text(encoding="utf-8")
    guide = GUIDE.read_text(encoding="utf-8")
    css = CSS.read_text(encoding="utf-8")
    decision = DECISION.read_text(encoding="utf-8")
    guide_routes = GUIDE_ROUTES.read_text(encoding="utf-8")
    sitemap = SITEMAP.read_text(encoding="utf-8")

    for endpoint in REQUIRED_ENDPOINTS:
        if endpoint not in agent:
            fail(f"missing endpoint {endpoint}")

    for term in REQUIRED_TERMS:
        if term not in agent:
            fail(f"missing agent term {term}")

    for slug in REQUIRED_DECISION_SLUGS:
        if slug not in decision:
            fail(f"missing decision page slug {slug}")

    if "svic_render_decision_page" not in decision or "template_redirect" not in decision:
        fail("decision page route hook missing")

    for route_source, route_name in [(guide_routes, "guide"), (ROOT.joinpath("theme/svicloudtvbox-lumen/inc/policy-contact-routes.php").read_text(encoding="utf-8"), "policy/contact")]:
        if "get_page_by_path" not in route_source or "post_status === 'publish'" not in route_source:
            fail(f"{route_name} fallback route can hijack published pages")

    for slug in ["guides-apps", "guides-troubleshooting", "guides-setup", "svicloud遙控器配對失敗-故障碼排查一次搞定"]:
        if slug not in guide_routes:
            fail(f"missing guide fallback route {slug}")

    if "svic_render_guide_route" not in guide_routes or "template_redirect" not in guide_routes:
        fail("guide fallback route hook missing")

    for slug in REQUIRED_DECISION_SLUGS:
        if slug not in guide:
            fail(f"guide pages do not internally link decision slug {slug}")
        if slug not in sitemap:
            fail(f"sitemap missing decision slug {slug}")

    for required_link in ["/product/svicloud-10p-plus/", "/product/svicloud-10s/", "/shipping-policy/", "/return-policy/", "/guides-setup/"]:
        if required_link not in decision:
            fail(f"decision pages missing required product/policy/setup link {required_link}")

    for endpoint in REQUIRED_ENDPOINTS:
        if endpoint not in sitemap:
            fail(f"sitemap missing endpoint {endpoint}")

    if "agent-friendly-sitemap.xml" not in sitemap or "rank_math/sitemap/index" not in sitemap:
        fail("agent-friendly sitemap integration missing")

    if "svic_agent_friendly_sitemap_lastmod" not in sitemap or "is_numeric($marker)" not in sitemap:
        fail("agent-friendly sitemap lastmod does not guard non-numeric deploy markers")

    combined = agent + guide + decision + guide_routes + sitemap
    for bad in FORBIDDEN_PHONE_PATTERNS:
        if bad in combined:
            fail(f"forbidden phone pattern present: {bad}")

    if "svic_serve_agent_resource" not in agent or "template_redirect" not in agent:
        fail("agent resource route hook missing")

    if "guides-answer-hub" not in guide or "FAQPage" not in guide:
        fail("guide answer hub or FAQ schema missing")

    if not re.search(r"Yogurt TV|8989c", guide):
        fail("app intent answers missing")

    if "guides-answer-hub" not in css:
        fail("answer hub CSS missing from generated guides.css")

    print("OK: agent resources, answer hubs, schema, phone guards")


if __name__ == "__main__":
    main()
