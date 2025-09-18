'''
Concatenate CSS partials into the final assets/css/style.css file.

Rules:
- Reads all files in assets/css/parts/*.css sorted lexicographically.
- Reads the existing assets/css/style.css as a base, and strips content
  between optional section markers so we can migrate piecemeal:
    /* @section:tokens start */ ... /* @section:tokens end */
    /* @section:header start */ ... /* @section:header end */
- Writes the concatenation: banner + parts + stripped base -> style.css

Run directly or via the deploy script.
'''

from __future__ import annotations

import argparse
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]

SECTION_PATTERNS = [
    (r"/\*\s*@section:tokens start\s*\*/.*?/\*\s*@section:tokens end\s*\*/", "tokens"),
    (r"/\*\s*@section:header start\s*\*/.*?/\*\s*@section:header end\s*\*/", "header"),
    (r"/\*\s*@section:navigation start\s*\*/.*?/\*\s*@section:navigation end\s*\*/", "navigation"),
    (r"/\*\s*@section:hero start\s*\*/.*?/\*\s*@section:hero end\s*\*/", "hero"),
    (r"/\*\s*@section:cards start\s*\*/.*?/\*\s*@section:cards end\s*\*/", "cards"),
    (r"/\*\s*@section:woocommerce start\s*\*/.*?/\*\s*@section:woocommerce end\s*\*/", "woocommerce"),
    (r"/\*\s*@section:utilities start\s*\*/.*?/\*\s*@section:utilities end\s*\*/", "utilities"),
]

SPACE_AROUND = frozenset(" ({:;,>+~")
PUNCTUATION = frozenset(":;,>{}+~(){}")

def strip_marked_sections(text: str) -> str:
    for pattern, _ in SECTION_PATTERNS:
        text = re.sub(pattern, "", text, flags=re.DOTALL)
    return text

def minify_css(css: str) -> str:
    '''Very small CSS minifier suited to our generated bundle.'''
    css = re.sub(r"/\*(?!\s*!)[\s\S]*?\*/", "", css)

    out: list[str] = []
    in_string = False
    quote_char = ""
    prev_char = ""

    for ch in css:
        if in_string:
            out.append(ch)
            if ch == quote_char and prev_char != '\\':
                in_string = False
            prev_char = ch
            continue

        if ch in {'"', "'"}:
            in_string = True
            quote_char = ch
            out.append(ch)
            prev_char = ch
            continue

        if ch in "\n\r\t":
            ch = " "

        if ch == " ":
            if not out or out[-1] in SPACE_AROUND:
                prev_char = ch
                continue

        if ch in PUNCTUATION:
            while out and out[-1] == " ":
                out.pop()
            out.append(ch)
            prev_char = ch
            continue

        out.append(ch)
        prev_char = ch

    text = "".join(out)
    text = re.sub(r";\}", "}", text)
    text = re.sub(r"\s+", " ", text).strip()
    text = re.sub(r"}\s*", "}\n", text)
    text = re.sub(r"\n+", "\n", text)
    text = re.sub(r"\n(@|\.|#)", r"\n\1", text)
    return text.strip()

def gather_theme_slugs(theme_arg: str) -> list[str]:
    theme_root = ROOT / "theme"
    if theme_arg == "all":
        return sorted(
            p.name
            for p in theme_root.iterdir()
            if p.is_dir() and (p / "assets" / "css" / "parts").exists()
        )
    return [theme_arg]

def build(theme_slug: str, compact: bool, include_base: bool) -> int:
    theme_dir = ROOT / "theme" / theme_slug
    if not theme_dir.exists():
        print(f"Theme directory not found: {theme_dir}")
        return 1

    css_dir = theme_dir / "assets" / "css"
    parts_dir = css_dir / "parts"
    style_path = css_dir / "style.css"

    if not parts_dir.exists():
        print(f"No parts directory found for {theme_slug}; nothing to build.")
        return 0

    part_files = sorted(parts_dir.glob("*.css"))
    if not part_files:
        print(f"No partials found for {theme_slug}; nothing to build.")
        return 0

    base_src = style_path.read_text(encoding="utf-8") if style_path.exists() else ""
    base_tail = strip_marked_sections(base_src).strip() if include_base and base_src else ""

    part_bodies = []
    for partial in part_files:
        body = partial.read_text(encoding="utf-8").strip()
        part_bodies.append((partial.name, body))

    if compact:
        fragments = [body for _, body in part_bodies]
        if base_tail:
            fragments.append(base_tail)
        combined = minify_css("\n".join(fragments))
    else:
        segments = [f"/* == PART: {name} == */\n{body}" for name, body in part_bodies]
        if base_tail:
            segments.append(f"/* == PART: base == */\n{base_tail}")
        combined = "\n\n".join(segments)

    banner = (
        "/*\n"
        "  GENERATED FILE — do not edit directly.\n"
        "  Built from assets/css/parts/*.css + base content by scripts/build_css.py\n"
        f"  Theme: {theme_slug}\n"
        "*/\n"
    )

    output = banner + combined + "\n"
    style_path.write_text(output, encoding="utf-8")
    print(f"Built {len(part_files)} partial(s) into {style_path} ({len(output)} bytes)")
    return 0

def main() -> int:
    parser = argparse.ArgumentParser(description="Build CSS bundle for a theme.")
    parser.add_argument(
        "--theme",
        default="svicloudtvbox",
        help="Theme directory name under theme/. Defaults to svicloudtvbox.",
    )
    parser.add_argument(
        "--all",
        action="store_true",
        help="Build all themes that provide assets/css/parts partials.",
    )
    parser.add_argument(
        "--pretty",
        action="store_true",
        help="Disable CSS minification (outputs readable bundle).",
    )
    parser.add_argument(
        "--include-base",
        action="store_true",
        help="Append any remaining CSS from the existing style.css after section stripping.",
    )
    args = parser.parse_args()

    theme_arg = "all" if args.all else args.theme
    slugs = gather_theme_slugs(theme_arg)
    compact = not args.pretty
    include_base = args.include_base

    exit_code = 0
    for slug in slugs:
        result = build(slug, compact, include_base)
        if result != 0 and exit_code == 0:
            exit_code = result
    return exit_code

if __name__ == "__main__":
    raise SystemExit(main())
