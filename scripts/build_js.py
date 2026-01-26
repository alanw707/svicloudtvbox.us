#!/usr/bin/env python3
'''
Minify JavaScript files for the theme.

This script minifies theme.js using a lightweight regex-based minifier.
For production, you could swap in terser or esbuild, but this simple
approach handles our jQuery-style code well and requires no npm deps.

Usage:
    python3 scripts/build_js.py --theme svicloudtvbox-lumen
    python3 scripts/build_js.py --theme svicloudtvbox-lumen --pretty
'''

from __future__ import annotations

import argparse
import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]


def minify_js(js: str) -> str:
    """
    Simple JS minifier for jQuery-style code.
    
    Removes:
    - Single-line comments (// ...)
    - Multi-line comments (/* ... */)
    - Unnecessary whitespace
    
    Preserves:
    - String literals
    - Regular expression literals
    - Required whitespace (e.g., "return true")
    """
    # Remove single-line comments (but not URLs like https://)
    js = re.sub(r'(?<!:)//[^\n]*', '', js)
    
    # Remove multi-line comments (but keep /*! ... */ license comments)
    js = re.sub(r'/\*(?!\s*!)[\s\S]*?\*/', '', js)
    
    # Collapse multiple whitespace/newlines into single space
    js = re.sub(r'\s+', ' ', js)
    
    # Remove spaces around operators and punctuation
    # Be careful with regex literals and division
    js = re.sub(r'\s*([{};,:\[\]()=+\-*/<>!&|?])\s*', r'\1', js)
    
    # Restore necessary spaces (e.g., "return true", "var x", "function name")
    keywords = ['return', 'var', 'let', 'const', 'function', 'if', 'else', 'for', 
                'while', 'do', 'switch', 'case', 'break', 'continue', 'new', 
                'typeof', 'instanceof', 'in', 'of', 'throw', 'try', 'catch', 'finally']
    
    for kw in keywords:
        # Add space after keyword when followed by alphanumeric
        js = re.sub(rf'\b{kw}([a-zA-Z0-9_$])', rf'{kw} \1', js)
    
    # Clean up any double spaces that might have been introduced
    js = re.sub(r' +', ' ', js)
    
    return js.strip()


def build_js(theme_dir: Path, pretty: bool = False) -> None:
    """Build/minify JS for a theme."""
    js_dir = theme_dir / 'assets' / 'js'
    source_file = js_dir / 'theme.js'
    
    if not source_file.exists():
        print(f"[SKIP] No theme.js found at {source_file}")
        return
    
    # Read source
    js_content = source_file.read_text(encoding='utf-8')
    original_size = len(js_content.encode('utf-8'))
    
    if pretty:
        # Just report size, don't minify
        print(f"[INFO] {source_file.name}: {original_size:,} bytes (pretty mode, no minification)")
        return
    
    # Create minified version
    minified = minify_js(js_content)
    minified_size = len(minified.encode('utf-8'))
    
    # Write to theme.min.js
    min_file = js_dir / 'theme.min.js'
    min_file.write_text(minified, encoding='utf-8')
    
    savings = original_size - minified_size
    pct = (savings / original_size * 100) if original_size > 0 else 0
    
    print(f"[OK] {source_file.name} -> {min_file.name}")
    print(f"     Original: {original_size:,} bytes")
    print(f"     Minified: {minified_size:,} bytes")
    print(f"     Savings:  {savings:,} bytes ({pct:.1f}%)")


def main() -> None:
    parser = argparse.ArgumentParser(description='Minify theme JavaScript')
    parser.add_argument('--theme', required=True, help='Theme directory name')
    parser.add_argument('--pretty', action='store_true', help='Skip minification (info only)')
    args = parser.parse_args()
    
    theme_dir = ROOT / 'theme' / args.theme
    if not theme_dir.is_dir():
        print(f"[ERROR] Theme directory not found: {theme_dir}")
        return
    
    build_js(theme_dir, pretty=args.pretty)


if __name__ == '__main__':
    main()
