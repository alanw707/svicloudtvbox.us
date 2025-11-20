#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
# Don't pass --config since config.yaml is copied into Docker container
"$SCRIPT_DIR/run-local.sh" --max-posts 1 "$@"
