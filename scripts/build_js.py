#!/usr/bin/env python3
'''
Minify JavaScript files for the theme using terser.

Usage:
    python3 scripts/build_js.py --theme svicloudtvbox-lumen
    python3 scripts/build_js.py --theme svicloudtvbox-lumen --pretty

Requires: npm install --save-dev terser
'''

from __future__ import annotations

import argparse
import subprocess
import shutil
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]


def find_terser() -> str | None:
    """Find terser binary - check local node_modules first, then global."""
    # Local node_modules (preferred)
    local_terser = ROOT / 'node_modules' / '.bin' / 'terser'
    if local_terser.exists():
        return str(local_terser)
    
    # Global install
    global_terser = shutil.which('terser')
    if global_terser:
        return global_terser
    
    return None


def build_js(theme_dir: Path, pretty: bool = False) -> bool:
    """Build/minify JS for a theme. Returns True on success."""
    js_dir = theme_dir / 'assets' / 'js'
    source_file = js_dir / 'theme.js'
    min_file = js_dir / 'theme.min.js'
    
    if not source_file.exists():
        print(f"[SKIP] No theme.js found at {source_file}")
        return True
    
    # Get original size
    original_size = source_file.stat().st_size
    
    if pretty:
        print(f"[INFO] {source_file.name}: {original_size:,} bytes (pretty mode, no minification)")
        return True
    
    # Find terser
    terser = find_terser()
    if not terser:
        print("[ERROR] terser not found. Install with: npm install --save-dev terser")
        return False
    
    # Run terser with optimal settings for our jQuery code
    # --compress: dead code elimination, optimize booleans, etc.
    # --mangle: shorten variable names (safe for our code)
    # --format: output formatting options
    cmd = [
        terser,
        str(source_file),
        '--compress',
        '--mangle',
        '--format', 'comments=false',
        '--output', str(min_file),
    ]
    
    try:
        result = subprocess.run(
            cmd,
            capture_output=True,
            text=True,
            cwd=ROOT,
        )
        
        if result.returncode != 0:
            print(f"[ERROR] terser failed:")
            print(result.stderr)
            return False
        
        # Report results
        minified_size = min_file.stat().st_size
        savings = original_size - minified_size
        pct = (savings / original_size * 100) if original_size > 0 else 0
        
        print(f"[OK] {source_file.name} -> {min_file.name}")
        print(f"     Original: {original_size:,} bytes")
        print(f"     Minified: {minified_size:,} bytes")
        print(f"     Savings:  {savings:,} bytes ({pct:.1f}%)")
        return True
        
    except Exception as e:
        print(f"[ERROR] Failed to run terser: {e}")
        return False


def main() -> None:
    parser = argparse.ArgumentParser(description='Minify theme JavaScript with terser')
    parser.add_argument('--theme', required=True, help='Theme directory name')
    parser.add_argument('--pretty', action='store_true', help='Skip minification (info only)')
    args = parser.parse_args()
    
    theme_dir = ROOT / 'theme' / args.theme
    if not theme_dir.is_dir():
        print(f"[ERROR] Theme directory not found: {theme_dir}")
        return
    
    success = build_js(theme_dir, pretty=args.pretty)
    exit(0 if success else 1)


if __name__ == '__main__':
    main()
