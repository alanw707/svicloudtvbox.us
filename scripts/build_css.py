#!/usr/bin/env python3
"""
Concatenate CSS partials into the final assets/css/style.css file.

Rules:
- Reads all files in assets/css/parts/*.css sorted lexicographically.
- Reads the existing assets/css/style.css as a base, and strips content
  between optional section markers so we can migrate piecemeal:
    /* @section:tokens start */ ... /* @section:tokens end */
    /* @section:header start */ ... /* @section:header end */
- Writes the concatenation: banner + parts + stripped base -> style.css

Run directly or via the deploy script.
"""

from __future__ import annotations

import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
THEME_DIR = ROOT / "theme" / "svicloudtvbox"
CSS_DIR = THEME_DIR / "assets" / "css"
PARTS_DIR = CSS_DIR / "parts"
STYLE_PATH = CSS_DIR / "style.css"


def strip_marked_sections(text: str) -> str:
    patterns = [
        (r"/\*\s*@section:tokens start\s*\*/.*?/\*\s*@section:tokens end\s*\*/", "tokens"),
        (r"/\*\s*@section:header start\s*\*/.*?/\*\s*@section:header end\s*\*/", "header"),
        (r"/\*\s*@section:navigation start\s*\*/.*?/\*\s*@section:navigation end\s*\*/", "navigation"),
        (r"/\*\s*@section:hero start\s*\*/.*?/\*\s*@section:hero end\s*\*/", "hero"),
        (r"/\*\s*@section:cards start\s*\*/.*?/\*\s*@section:cards end\s*\*/", "cards"),
        (r"/\*\s*@section:woocommerce start\s*\*/.*?/\*\s*@section:woocommerce end\s*\*/", "woocommerce"),
        (r"/\*\s*@section:utilities start\s*\*/.*?/\*\s*@section:utilities end\s*\*/", "utilities"),
    ]
    for pat, _ in patterns:
        text = re.sub(pat, "", text, flags=re.DOTALL)
    return text


def build() -> int:
    if not PARTS_DIR.exists():
        print("No parts directory found; nothing to build.")
        return 0

    part_files = sorted(PARTS_DIR.glob("*.css"))
    if not part_files:
        print("No partials found; nothing to build.")
        return 0

    base_src = STYLE_PATH.read_text(encoding="utf-8") if STYLE_PATH.exists() else ""
    base_src = strip_marked_sections(base_src)

    banner = (
        "/*\n"
        "  GENERATED FILE — do not edit directly.\n"
        "  Built from assets/css/parts/*.css + base content by scripts/build_css.py\n"
        "*/\n\n"
    )

    parts_src = []
    for p in part_files:
        parts_src.append(f"/* == PART: {p.name} == */\n" + p.read_text(encoding="utf-8").rstrip() + "\n")

    # Final output includes only the partials. Base content is no longer appended
    # once the project has migrated sections to partials. This keeps the
    # generated file concise and avoids duplicates.
    out = banner + "\n".join(parts_src) + "\n"
    STYLE_PATH.write_text(out, encoding="utf-8")
    print(f"Built {len(part_files)} partial(s) into {STYLE_PATH} ({len(out)} bytes)")
    return 0


if __name__ == "__main__":
    raise SystemExit(build())
