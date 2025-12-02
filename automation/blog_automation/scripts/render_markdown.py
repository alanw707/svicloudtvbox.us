#!/usr/bin/env python3
"""
Render Markdown to HTML for manual posting.

Usage:
  python3 render_markdown.py input.md > output.html
"""

import sys
from pathlib import Path

import markdown


def main() -> int:
    if len(sys.argv) != 2:
        sys.stderr.write("Usage: python3 render_markdown.py input.md > output.html\n")
        return 1

    path = Path(sys.argv[1])
    if not path.exists():
        sys.stderr.write(f"Not found: {path}\n")
        return 1

    text = path.read_text(encoding="utf-8")
    html = markdown.markdown(text, extensions=["extra", "sane_lists"])
    sys.stdout.write(html)
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
