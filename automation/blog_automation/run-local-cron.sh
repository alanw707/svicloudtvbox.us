#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
# If a long-running scheduler container is active, avoid duplicate runs.
if command -v docker >/dev/null 2>&1; then
  if docker ps --format '{{.Names}}' | grep -q '^autoblog-scheduler$'; then
    echo "autoblog-scheduler is running; skipping cron-triggered run."
    exit 0
  fi
fi
# Don't pass --config since config.yaml is copied into Docker container
"$SCRIPT_DIR/run-local.sh" --max-posts 1 "$@"
