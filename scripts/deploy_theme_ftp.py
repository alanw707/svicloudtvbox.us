#!/usr/bin/env python3
"""
Deploy the custom theme to Hostinger via FTP/FTPS.

Defaults assume WordPress is installed under `public_html` and the theme
should live at `public_html/wp-content/themes/svicloudtvbox`.

Usage (env vars):
  FTP_HOST=147.79.122.118 \
  FTP_PORT=21 \
  FTP_USER=uXXXXXXXXX.svicloudtvbox.us \
  FTP_PASS=... \
  FTP_BASE_DIR=public_html/wp-content/themes/svicloudtvbox \
  LOCAL_THEME_DIR=theme/svicloudtvbox \
  python3 scripts/deploy_theme_ftp.py

Or via flags:
  python3 scripts/deploy_theme_ftp.py \
    --host 147.79.122.118 --port 21 \
    --user uXXXXXXXXX.svicloudtvbox.us --password '...' \
    --remote-base public_html/wp-content/themes/svicloudtvbox \
    --local-dir theme/svicloudtvbox --no-tls
"""
from __future__ import annotations

import argparse
import os
import posixpath
import sys
from ftplib import FTP, FTP_TLS, error_perm


def parse_args() -> argparse.Namespace:
    p = argparse.ArgumentParser(description="Deploy WP theme to Hostinger via FTP/FTPS")
    p.add_argument("--host", default=os.getenv("FTP_HOST", ""), help="FTP hostname/IP")
    p.add_argument("--port", type=int, default=int(os.getenv("FTP_PORT", "21")), help="FTP port")
    p.add_argument("--user", default=os.getenv("FTP_USER", ""), help="FTP username")
    p.add_argument("--password", default=os.getenv("FTP_PASS", ""), help="FTP password")
    p.add_argument("--remote-base", default=os.getenv("FTP_BASE_DIR", "public_html/wp-content/themes/svicloudtvbox"), help="Remote base dir for theme")
    p.add_argument("--local-dir", default=os.getenv("LOCAL_THEME_DIR", "theme/svicloudtvbox"), help="Local theme directory")
    p.add_argument("--tls", dest="tls", action=argparse.BooleanOptionalAction, default=os.getenv("FTP_TLS", "true").lower() not in {"0","false","no"}, help="Use explicit FTPS (AUTH TLS)")
    p.add_argument("--passive", dest="passive", action=argparse.BooleanOptionalAction, default=os.getenv("FTP_PASSIVE", "true").lower() not in {"0","false","no"}, help="Use passive mode")
    p.add_argument("--dry-run", action="store_true", help="Show actions, don't upload")
    return p.parse_args()


def ensure_dirs(ftp: FTP, remote_dir: str):
    parts = [p for p in remote_dir.split("/") if p and p != "."]
    path = ""
    for part in parts:
        path = f"{path}/{part}" if path else part
        try:
            ftp.mkd(path)
        except error_perm as e:
            # 550 directory exists or permission denied
            if not str(e).startswith("550"):
                raise


def upload_file(ftp: FTP, local_path: str, remote_path: str, dry_run=False):
    with open(local_path, "rb") as f:
        if dry_run:
            print(f"DRY STOR {remote_path}")
            return
        ftp.storbinary(f"STOR {remote_path}", f)


def upload_tree(ftp: FTP, local_dir: str, remote_base: str, dry_run=False):
    for root, dirs, files in os.walk(local_dir):
        # skip hidden dirs
        dirs[:] = [d for d in dirs if not d.startswith('.') and d != '__pycache__']
        rel = os.path.relpath(root, local_dir)
        rel = "" if rel == "." else rel
        remote_dir = remote_base if not rel else posixpath.join(remote_base, rel.replace(os.sep, "/"))
        ensure_dirs(ftp, remote_dir)
        for name in files:
            if name.startswith('.'):
                continue
            lp = os.path.join(root, name)
            rp = posixpath.join(remote_dir, name)
            print(f"PUT {lp} -> {rp}")
            upload_file(ftp, lp, rp, dry_run=dry_run)


def main() -> int:
    a = parse_args()
    if not a.host or not a.user or not a.password:
        print("Missing required credentials. Set FTP_HOST, FTP_USER, FTP_PASS or pass flags.", file=sys.stderr)
        return 2
    if not os.path.isdir(a.local_dir):
        print(f"Local theme dir not found: {a.local_dir}", file=sys.stderr)
        return 2

    # Connect
    ftp: FTP
    try:
        if a.tls:
            ftps = FTP_TLS()
            ftps.connect(a.host, a.port, timeout=30)
            ftps.auth()  # explicit TLS
            ftps.prot_p()  # secure data connection
            ftp = ftps
        else:
            ftp = FTP()
            ftp.connect(a.host, a.port, timeout=30)
        ftp.set_pasv(a.passive)
        ftp.login(a.user, a.password)
    except Exception as e:
        print(f"FTP connect/login failed: {e}", file=sys.stderr)
        return 1

    try:
        ensure_dirs(ftp, a.remote_base)
        upload_tree(ftp, a.local_dir, a.remote_base, dry_run=a.dry_run)
        print("\nDeploy complete.")
    finally:
        try:
            ftp.quit()
        except Exception:
            pass
    return 0


if __name__ == "__main__":
    raise SystemExit(main())

