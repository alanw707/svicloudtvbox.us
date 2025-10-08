'''
Build CSS bundle(s) for a theme from assets/css/parts/*.css partials.

Features:
- By default concatenates every partial into assets/css/style.css.
- When assets/css/bundles.json is present, builds the named bundles
  into their respective output files (e.g. style.css, front-page.css).
- Supports pretty (non-minified) output via --pretty.

Run directly or via the deploy script.
'''

from __future__ import annotations

import argparse
import json
import re
from pathlib import Path
from typing import Iterable

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
    """Very small CSS minifier suited to our generated bundle."""
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


def ensure_css_suffix(name: str) -> str:
    return name if name.endswith(".css") else f"{name}.css"


def load_manifest(css_dir: Path) -> dict[str, dict[str, object]]:
    manifest_path = css_dir / "bundles.json"
    if not manifest_path.exists():
        return {}

    data = json.loads(manifest_path.read_text(encoding="utf-8"))
    bundles: dict[str, dict[str, object]] = {}

    for key, entry in data.items():
        if isinstance(entry, dict):
            parts = entry.get("parts") or []
            output = ensure_css_suffix(entry.get("output") or f"{key}.css")
            label = entry.get("label") or key
        elif isinstance(entry, list):
            parts = entry
            output = ensure_css_suffix(key)
            label = key
        else:
            raise ValueError(f"Unsupported manifest entry for bundle {key!r}.")

        if not parts:
            raise ValueError(f"Bundle {key!r} has no parts defined.")

        bundles[str(key)] = {
            "parts": [str(part) for part in parts],
            "output": output,
            "label": label,
        }

    return bundles


def read_partials(parts_dir: Path, part_names: Iterable[str]) -> list[tuple[str, str]]:
    bodies: list[tuple[str, str]] = []
    missing: list[str] = []

    for name in part_names:
        partial_path = parts_dir / name
        if not partial_path.exists():
            missing.append(name)
            continue
        body = partial_path.read_text(encoding="utf-8").strip()
        bodies.append((name, body))

    if missing:
        missing_list = ", ".join(missing)
        raise FileNotFoundError(f"Missing partial(s): {missing_list}")

    return bodies


def render_bundle(
    theme_slug: str,
    output_path: Path,
    part_bodies: list[tuple[str, str]],
    compact: bool,
    label: str,
    base_tail: str | None = None,
) -> None:
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
        "  Built by scripts/build_css.py\n"
        f"  Theme: {theme_slug}\n"
        f"  Bundle: {label}\n"
        "*/\n"
    )

    output = banner + combined + "\n"
    output_path.write_text(output, encoding="utf-8")
    print(f"Built {len(part_bodies)} partial(s) into {output_path} ({len(output)} bytes)")


def build(theme_slug: str, compact: bool, include_base: bool, requested_bundles: set[str] | None) -> int:
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

    manifest = load_manifest(css_dir)

    if manifest:
        bundles_to_build: list[str]
        if requested_bundles:
            unknown = requested_bundles.difference(manifest.keys())
            if unknown:
                unknown_str = ", ".join(sorted(unknown))
                print(f"Bundle(s) not defined in manifest: {unknown_str}")
                return 1
            bundles_to_build = sorted(requested_bundles)
        else:
            bundles_to_build = sorted(manifest.keys())

        for bundle_key in bundles_to_build:
            spec = manifest[bundle_key]
            part_bodies = read_partials(parts_dir, spec["parts"])
            output_path = css_dir / str(spec["output"])
            label = str(spec.get("label", bundle_key))
            render_bundle(theme_slug, output_path, part_bodies, compact, label)

        return 0

    if requested_bundles:
        print("Warning: --bundle specified but no bundles.json found; building default style.css")

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

    render_bundle(theme_slug, style_path, part_bodies, compact, "style.css", base_tail or None)
    return 0


def main() -> int:
    parser = argparse.ArgumentParser(description="Build CSS bundle for a theme.")
    parser.add_argument(
        "--theme",
        default="svicloudtvbox-lumen",
        help="Theme directory name under theme/. Defaults to svicloudtvbox-lumen.",
    )
    parser.add_argument(
        "--all",
        action="store_true",
        help="Build all themes that provide assets/css/parts partials.",
    )
    parser.add_argument(
        "--bundle",
        action="append",
        help="Only build the specified bundle(s) when bundles.json is present.",
    )
    parser.add_argument(
        "--pretty",
        action="store_true",
        help="Disable CSS minification (outputs readable bundle).",
    )
    parser.add_argument(
        "--include-base",
        action="store_true",
        help="Append any remaining CSS from the existing style.css after section stripping (legacy mode).",
    )
    args = parser.parse_args()

    theme_arg = "all" if args.all else args.theme
    slugs = gather_theme_slugs(theme_arg)
    compact = not args.pretty
    include_base = args.include_base
    requested_bundles = set(args.bundle or [])

    exit_code = 0
    for slug in slugs:
        result = build(slug, compact, include_base, requested_bundles if requested_bundles else None)
        if result != 0 and exit_code == 0:
            exit_code = result
    return exit_code


if __name__ == "__main__":
    raise SystemExit(main())
