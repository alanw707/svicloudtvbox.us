#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
"$SCRIPT_DIR/run-local.sh" --config "$SCRIPT_DIR/config.yaml" --max-posts 1 "$@"
