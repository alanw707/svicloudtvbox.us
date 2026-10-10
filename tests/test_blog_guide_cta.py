#!/usr/bin/env python3
"""Render the actual single.php CTA with WP stubs; test guide-only scope and locales."""
from html.parser import HTMLParser
from pathlib import Path
import subprocess
import unittest

ROOT = Path(__file__).resolve().parents[1]
FIXTURE = ROOT / "tests/fixtures/render-blog-cta.php"


class CtaParser(HTMLParser):
    def __init__(self):
        super().__init__()
        self.in_actions = False
        self.links = []
        self.anchor = None

    def handle_starttag(self, tag, attrs):
        attributes = dict(attrs)
        if tag == "div" and attributes.get("class") == "blog-article__cta-actions":
            self.in_actions = True
        elif self.in_actions and tag == "a":
            self.anchor = {"href": attributes.get("href", ""),
                           "class": attributes.get("class", ""), "text": ""}

    def handle_data(self, data):
        if self.anchor is not None:
            self.anchor["text"] += data

    def handle_endtag(self, tag):
        if tag == "a" and self.anchor is not None:
            self.anchor["text"] = self.anchor["text"].strip()
            self.links.append(self.anchor)
            self.anchor = None


def render(locale, slug):
    result = subprocess.run(["php", str(FIXTURE), locale, slug],
                            capture_output=True, text=True, check=True, cwd=ROOT)
    parser = CtaParser()
    parser.feed(result.stdout)
    return parser.links


class GuideCtaTest(unittest.TestCase):
    def test_guide_is_15p_first_in_every_locale(self):
        expected = {"en_US": ("", "Buy SVICLOUD 15P", "Compare current models"),
                    "zh_TW": ("/zh", "購買小雲 15P", "比較目前機型"),
                    "zh_CN": ("/zh-cn", "购买小云 15P", "比较目前机型")}
        for locale, (prefix, buy, compare) in expected.items():
            with self.subTest(locale=locale):
                links = render(locale, "best-chinese-tv-box-north-america")
                self.assertEqual(4, len(links))
                self.assertEqual(buy, links[0]["text"])
                self.assertEqual("https://svicloudtvbox.us" + prefix + "/product/svicloud-15p/", links[0]["href"])
                self.assertIn("btn-primary", links[0]["class"])
                self.assertEqual(compare, links[1]["text"])
                self.assertEqual("https://svicloudtvbox.us" + prefix + "/compare/", links[1]["href"])
                self.assertIn("btn-outline", links[1]["class"])
                self.assertFalse(any("10p-plus" in link["href"] for link in links))

    def test_unrelated_post_keeps_original_ctas(self):
        links = render("en_US", "svicloud-tv-box-usa-guide-2026")
        self.assertEqual(5, len(links))
        self.assertIn("svicloud-10p-plus", links[0]["href"])
        self.assertIn("svicloud-10s", links[1]["href"])
        self.assertEqual("Compare 10P+ vs 10S", links[2]["text"])
        self.assertIn("btn-primary", links[2]["class"])
        self.assertFalse(any("svicloud-15p" in link["href"] for link in links))


if __name__ == "__main__":
    unittest.main()
