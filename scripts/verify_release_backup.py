#!/usr/bin/env python3
"""Verify a release backup directory without exposing its private contents."""
from __future__ import annotations

import argparse
import gzip
import hashlib
import json
import tarfile
from pathlib import Path


def sha256(path: Path) -> str:
    digest = hashlib.sha256()
    with path.open("rb") as handle:
        for chunk in iter(lambda: handle.read(1024 * 1024), b""):
            digest.update(chunk)
    return digest.hexdigest()


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("backup_dir", type=Path)
    args = parser.parse_args()
    root = args.backup_dir.resolve()
    required = {"local-wordpress.sql.gz", "uploads.tar.gz", "remote-theme-current.tar.gz", "remote-final.htaccess"}
    missing = sorted(name for name in required if not (root / name).is_file())
    if missing:
        raise SystemExit(f"missing backup artifacts: {', '.join(missing)}")

    checks = []
    for line in (root / "backup.sha256").read_text().splitlines():
        digest, _, raw_path = line.partition("  ")
        path = Path(raw_path)
        if not path.is_absolute():
            path = root / path
        actual = sha256(path)
        checks.append({"file": path.name, "expected": digest, "actual": actual, "match": digest == actual})
    if not checks or not all(item["match"] for item in checks):
        raise SystemExit(json.dumps({"checks": checks}, indent=2))

    with gzip.open(root / "local-wordpress.sql.gz", "rb") as stream:
        stream.read(1)
    with tarfile.open(root / "uploads.tar.gz", "r:gz") as archive:
        uploads_members = len(archive.getmembers())
    with tarfile.open(root / "remote-theme-current.tar.gz", "r:gz") as archive:
        theme_files = sum(1 for member in archive.getmembers() if member.isfile())
    manifest = json.loads((root / "backup-manifest.json").read_text())
    print(json.dumps({
        "backup_dir": str(root),
        "hashes": checks,
        "gzip_readable": True,
        "uploads_members": uploads_members,
        "remote_theme_files": theme_files,
        "manifest_artifacts": len(manifest.get("artifacts", [])),
        "pass": theme_files == 212,
    }, indent=2))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
