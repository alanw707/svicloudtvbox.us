#!/usr/bin/env python3
"""
Deploy the custom WordPress theme to Hostinger over FTP/FTPS.

Defaults target the theme folder:
  local:  theme/svicloudtvbox-lumen
  remote: public_html/wp-content/themes/svicloudtvbox-lumen

Usage examples:
  # Using env vars (recommended)
  FTP_HOST=147.79.122.118 FTP_USER=username FTP_PASS=secret \
  python3 scripts/deploy_theme.py

  # Or with flags
  python3 scripts/deploy_theme.py \
    --host 147.79.122.118 --user uXXXX --password 'secret' \
    --protocol ftps --remote-root public_html/wp-content/themes/svicloudtvbox-lumen

Security note: Prefer passing secrets via environment variables or a local .env
file that is gitignored. Do not commit credentials.
"""

from __future__ import annotations

import argparse
import fnmatch
import os
import posixpath
import socket
import sys
import time
from io import BytesIO
from dataclasses import dataclass
from pathlib import Path
import subprocess

try:
    # ftplib is in the standard library
    from ftplib import FTP, FTP_TLS, error_perm
except Exception as e:  # pragma: no cover - environment issue
    print(f"Failed to import ftplib: {e}")
    sys.exit(2)


# Defaults
DEFAULT_LOCAL_DIR = Path("theme/svicloudtvbox-lumen").resolve()
DEFAULT_REMOTE_ROOT = "public_html/wp-content/themes/svicloudtvbox-lumen"

# Exclusion patterns (dirs/files)
EXCLUDE_DIRS = {
    ".git",
    ".github",
    "node_modules",
    "__pycache__",
    ".idea",
    ".vscode",
    "dist",
    "vendor",
}
EXCLUDE_FILES = {
    ".DS_Store",
    "Thumbs.db",
}
EXCLUDE_GLOBS = [
    "*.swp",
    "*.swo",
    "*.map",
]


@dataclass
class DeployConfig:
    host: str
    user: str
    password: str
    port: int = 21
    protocol: str = "ftps"  # "ftps" or "ftp"
    local_dir: Path = DEFAULT_LOCAL_DIR
    remote_root: str = DEFAULT_REMOTE_ROOT
    delete_remote: bool = True
    dry_run: bool = False
    verify_tls: bool = False  # Hostinger often uses self-signed; set True if valid cert
    connect_retries: int = 5
    connect_timeout: int = 45
    skip_same_size: bool = True


def parse_args() -> DeployConfig:
    env = os.environ
    p = argparse.ArgumentParser(description="Deploy WordPress theme over (F)TPS")
    p.add_argument("--host", default=env.get("FTP_HOST"), help="FTP host/IP")
    p.add_argument("--user", default=env.get("FTP_USER"), help="FTP username")
    p.add_argument("--password", default=env.get("FTP_PASS"), help="FTP password")
    p.add_argument("--port", type=int, default=int(env.get("FTP_PORT", 21)))
    p.add_argument("--protocol", choices=["ftps", "ftp"], default=env.get("FTP_PROTOCOL", "ftps"))
    p.add_argument("--local-dir", default=env.get("LOCAL_THEME_DIR", str(DEFAULT_LOCAL_DIR)))
    p.add_argument("--remote-root", default=env.get("REMOTE_THEME_DIR", DEFAULT_REMOTE_ROOT))
    p.add_argument("--delete-remote", dest="delete_remote", action="store_true", help="Delete remote files not present locally")
    p.add_argument("--no-delete-remote", dest="delete_remote", action="store_false", help="Skip deleting remote files")
    p.add_argument("--dry-run", action="store_true", help="Preview actions without uploading")
    p.add_argument("--bust-cache", action="store_true", help="Write .deploy-version (epoch) before upload")
    p.add_argument("--verify-tls", action="store_true", help="Verify TLS cert when using FTPS")
    p.add_argument("--connect-retries", type=int, default=int(env.get("FTP_CONNECT_RETRIES", 5)), help="FTP/FTPS connect/login retry count")
    p.add_argument("--connect-timeout", type=int, default=int(env.get("FTP_CONNECT_TIMEOUT", 45)), help="FTP/FTPS connect timeout in seconds")
    p.add_argument("--skip-same-size", action="store_true", default=env.get("FTP_SKIP_SAME_SIZE", "0").lower() in {"1", "true", "yes"}, help="Skip remote files only when byte sizes match")

    p.set_defaults(delete_remote=True)

    a = p.parse_args()
    if not a.host or not a.user or not a.password:
        p.error("FTP host, user, and password are required (via flags or env vars)")

    local_dir = Path(a.local_dir).resolve()
    if not local_dir.exists():
        p.error(f"Local theme directory not found: {local_dir}")

    cfg = DeployConfig(
        host=a.host,
        user=a.user,
        password=a.password,
        port=a.port,
        protocol=a.protocol,
        local_dir=local_dir,
        remote_root=a.remote_root.strip("/") or DEFAULT_REMOTE_ROOT,
        delete_remote=bool(a.delete_remote),
        dry_run=bool(a.dry_run),
        verify_tls=bool(a.verify_tls),
        connect_retries=max(1, int(a.connect_retries)),
        connect_timeout=max(10, int(a.connect_timeout)),
        skip_same_size=bool(a.skip_same_size),
    )

    # Side-effect: if requested, create/update .deploy-version locally (unless dry run)
    if a.bust_cache and not cfg.dry_run:
        try:
            ver_path = cfg.local_dir / '.deploy-version'
            epoch = str(int(__import__('time').time()))
            ver_path.write_text(epoch, encoding='utf-8')
            print(f"Wrote cache-bust marker: {ver_path} -> {epoch}")
        except Exception as e:
            print(f"WARN: failed to write .deploy-version: {e}")

    return cfg


def should_exclude(path: Path) -> bool:
    name = path.name
    if name in EXCLUDE_FILES or name in EXCLUDE_DIRS:
        return True
    for g in EXCLUDE_GLOBS:
        if fnmatch.fnmatch(name, g):
            return True
    return False


def _connect(cfg: DeployConfig):
    last_error = None
    for attempt in range(1, cfg.connect_retries + 1):
        ftp = None
        try:
            print(f"Connecting to FTP server (attempt {attempt}/{cfg.connect_retries}, timeout {cfg.connect_timeout}s)...")
            if cfg.protocol == "ftps":
                ftp = FTP_TLS()
                # Note: certificate verification is off by default for FTP_TLS in many environments
                # We expose a flag to enforce verification if desired.
                ftp.connect(host=cfg.host, port=cfg.port, timeout=cfg.connect_timeout)
                ftp.login(cfg.user, cfg.password)
                ftp.prot_p()  # Secure data connection
            else:
                ftp = FTP()
                ftp.connect(host=cfg.host, port=cfg.port, timeout=cfg.connect_timeout)
                ftp.login(cfg.user, cfg.password)
            ftp.set_pasv(True)
            return ftp
        except (socket.timeout, socket.error, TimeoutError, error_perm) as e:
            last_error = e
            try:
                if ftp is not None:
                    ftp.close()
            except Exception:
                pass
            if attempt < cfg.connect_retries:
                delay = min(30, 5 * attempt)
                print(f"WARN: connection/login attempt {attempt} failed: {e}; retrying in {delay}s...")
                time.sleep(delay)
            else:
                break
    print(f"Connection/login failed after {cfg.connect_retries} attempt(s): {last_error}")
    sys.exit(2)


def _remote_join(*parts: str) -> str:
    filtered = [p for p in parts if p is not None and p != ""]
    if not filtered:
        return ""

    is_absolute = bool(filtered[0].startswith("/"))
    joined = posixpath.join(*[p.strip("/") for p in filtered if p.strip("/")])
    if not joined:
        return "/" if is_absolute else ""
    return f"/{joined}" if is_absolute else joined



def resolve_remote_root(ftp, remote_root: str) -> str:
    """Normalize remote_root to an absolute FTP path.

    Supports both styles of configuration:
    - root-relative: public_html/wp-content/themes/...
    - login-relative: wp-content/themes/...
    """
    remote_clean = remote_root.strip("/")
    if remote_root.startswith("/"):
        return _with_leading_slash(remote_clean)

    login_dir = _with_leading_slash(ftp.pwd())
    login_leaf = posixpath.basename(login_dir.rstrip("/"))

    if not remote_clean:
        return login_dir

    if login_leaf and (remote_clean == login_leaf or remote_clean.startswith(login_leaf + "/")):
        return _with_leading_slash(remote_clean)

    return posixpath.join(login_dir.rstrip("/"), remote_clean)


def ensure_remote_dir(ftp, remote_dir: str, verbose: bool = False) -> None:
    """Create remote_dir (and parents) if missing, then cwd into it."""
    start = ftp.pwd()
    parts = [p for p in remote_dir.strip("/").split("/") if p]
    # Try starting from root; if that fails, start from current dir
    try:
        ftp.cwd("/")
    except Exception:
        pass
    for p in parts:
        try:
            ftp.cwd(p)
        except error_perm:
            if verbose:
                print(f"MKD {p}")
            ftp.mkd(p)
            ftp.cwd(p)
    # We remain in target dir; caller can restore
    # Caller is responsible for restoring cwd
    # No return value


def remote_size(ftp, remote_path: str) -> int | None:
    try:
        return ftp.size(remote_path)
    except Exception:
        return None


def remote_file_matches_local(ftp, remote_path: str, local_file: Path) -> bool:
    """Return True when the remote file bytes exactly match local_file."""
    remote_bytes = BytesIO()
    try:
        ftp.retrbinary(f"RETR {remote_path}", remote_bytes.write)
    except Exception:
        return False
    return remote_bytes.getvalue() == local_file.read_bytes()


def upload_file(ftp, local_file: Path, remote_dir: str, dry_run: bool = False, verbose: bool = True, skip_same_size: bool = False) -> bool:
    """Upload a single file, returns True if uploaded (not skipped)."""
    remote_path = _remote_join(remote_dir, local_file.name)
    rsize = remote_size(ftp, remote_path)
    lsize = local_file.stat().st_size
    if rsize is not None and rsize == lsize:
        if skip_same_size:
            if verbose:
                print(f"SKIP same-size: {remote_path}")
            return False
        if remote_file_matches_local(ftp, remote_path, local_file):
            if verbose:
                print(f"SKIP identical: {remote_path}")
            return False
    if verbose:
        print(("DRY-RUN " if dry_run else "") + f"PUT {local_file} -> {remote_path}")
    if dry_run:
        return False
    start = ftp.pwd()
    ensure_remote_dir(ftp, remote_dir)
    with local_file.open("rb") as f:
        ftp.storbinary(f"STOR {local_file.name}", f)
    ftp.cwd(start)
    return True


def walk_local_files(base: Path):
    for root, dirs, files in os.walk(base):
        # Filter dirs in-place to avoid walking excluded trees
        dirs[:] = [d for d in dirs if not should_exclude(Path(d))]
        for fname in files:
            p = Path(root) / fname
            if should_exclude(p):
                continue
            yield p


def _with_leading_slash(path: str) -> str:
    stripped = path.strip("/")
    return f"/{stripped}" if stripped else "/"


def list_remote_paths(ftp, base_remote: str) -> set[str] | None:
    """Return set of remote file paths under base_remote.

    Prefers MLSD; falls back to NLST traversal when MLSD is unavailable.
    Returns None if the listing fails entirely (e.g., permissions).
    """

    def _list_with_mlsd(root: str) -> set[str] | None:
        try:
            paths: set[str] = set()

            def _recurse(remote_dir: str) -> None:
                try:
                    entries = list(ftp.mlsd(remote_dir))
                except Exception:
                    raise
                for name, facts in entries:
                    if name in (".", ".."):
                        continue
                    rpath = _remote_join(remote_dir, name)
                    typ = facts.get("type")
                    if typ == "file":
                        paths.add(rpath)
                    elif typ in ("dir", "cdir", "pdir"):
                        _recurse(rpath)

            _recurse(root)
            return paths
        except Exception:
            return None

    def _list_with_nlst(root: str) -> set[str] | None:
        start = _with_leading_slash(ftp.pwd())
        files: set[str] = set()
        visited: set[str] = set()
        try:
            try:
                ftp.cwd(_with_leading_slash(root))
            except Exception:
                return None
            base_dir = _with_leading_slash(ftp.pwd())
            stack = [base_dir]
            while stack:
                current = stack.pop()
                if current in visited:
                    continue
                visited.add(current)
                try:
                    ftp.cwd(current)
                except Exception:
                    continue
                cur_dir = _with_leading_slash(ftp.pwd())
                try:
                    entries = ftp.nlst()
                except error_perm:
                    entries = []
                for entry in entries:
                    name = posixpath.basename(entry.rstrip("/"))
                    if name in (".", ".."):
                        continue
                    prefix = cur_dir.rstrip("/") + "/"
                    if entry.startswith("/") or entry.startswith(prefix):
                        remote_entry = _with_leading_slash(entry).rstrip("/")
                    elif entry.startswith(prefix.lstrip("/")):
                        remote_entry = _with_leading_slash(entry).rstrip("/")
                    else:
                        remote_entry = posixpath.join(cur_dir.rstrip("/"), entry).rstrip("/")
                    remote_abs = _with_leading_slash(remote_entry)
                    try:
                        ftp.cwd(remote_abs)
                    except error_perm:
                        files.add(remote_abs)
                    else:
                        stack.append(_with_leading_slash(ftp.pwd()))
                    finally:
                        ftp.cwd(cur_dir)
            return files
        finally:
            try:
                ftp.cwd(start)
            except Exception:
                pass

    root_path = _with_leading_slash(base_remote)

    # First attempt MLSD
    paths = _list_with_mlsd(root_path)
    if paths is not None:
        return paths

    # Fallback to NLST traversal
    return _list_with_nlst(root_path)


def delete_remote_extraneous(ftp, base_remote: str, local_base: Path, dry_run: bool = False) -> int:
    """Delete remote files that do not exist locally. Uses MLSD if available."""
    remote_files = list_remote_paths(ftp, base_remote)
    if remote_files is None:
        print("Remote listing not supported; skipping deletion.")
        return 0

    local_files = set()
    for lf in walk_local_files(local_base):
        rel = str(lf.relative_to(local_base)).replace(os.sep, "/")
        local_files.add(_remote_join(base_remote, rel))

    to_delete = sorted(r for r in remote_files if r not in local_files)
    count = 0
    for rpath in to_delete:
        if dry_run:
            print(f"DRY-RUN DEL {rpath}")
            continue
        try:
            print(f"DEL {rpath}")
            ftp.delete(rpath)
            count += 1
        except Exception as e:
            print(f"WARN: failed to delete {rpath}: {e}")
    return count


def main() -> int:
    cfg = parse_args()
    # Build CSS from partials if present, before uploading
    try:
        build_script = Path(__file__).resolve().parent / 'build_css.py'
        if build_script.exists():
            subprocess.run([sys.executable, str(build_script)], check=False)
    except Exception as e:
        print(f"WARN: CSS build step failed: {e}")
    print("== Theme Deploy ==")
    print(f"Host: {cfg.host}:{cfg.port}  Protocol: {cfg.protocol.upper()}  PASV: yes")
    print(f"Local: {cfg.local_dir}")
    if cfg.dry_run:
        print("[DRY RUN] No changes will be made")

    ftp = _connect(cfg)
    absolute_remote_root = resolve_remote_root(ftp, cfg.remote_root)
    print(f"Remote: {absolute_remote_root}")
    uploaded = 0
    try:
        for lf in walk_local_files(cfg.local_dir):
            rel_dir = str(lf.parent.relative_to(cfg.local_dir)).replace(os.sep, "/")
            remote_dir = _remote_join(absolute_remote_root, rel_dir)
            if upload_file(ftp, lf, remote_dir, dry_run=cfg.dry_run, skip_same_size=cfg.skip_same_size):
                uploaded += 1

        deleted = 0
        if cfg.delete_remote:
            deleted = delete_remote_extraneous(ftp, absolute_remote_root, cfg.local_dir, dry_run=cfg.dry_run)

        print(f"Done. Uploaded: {uploaded} file(s). Deleted: {deleted} file(s).")
        return 0
    finally:
        try:
            ftp.quit()
        except Exception:
            pass


if __name__ == "__main__":
    sys.exit(main())
