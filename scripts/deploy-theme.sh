#!/usr/bin/env bash
set -euo pipefail

# Wrapper for scripts/deploy_theme.py that loads .env if present and
# applies sensible defaults for this repository.

ROOT_DIR="$(cd "$(dirname "$0")"/.. && pwd)"

# Load environment variables from .env if available (gitignored)
if [[ -f "$ROOT_DIR/.env" ]]; then
  # shellcheck disable=SC1090
  set -a; source "$ROOT_DIR/.env"; set +a
fi

# Defaults for this repo
FTP_PROTOCOL="${FTP_PROTOCOL:-ftps}"
LOCAL_THEME_DIR="${LOCAL_THEME_DIR:-$ROOT_DIR/theme/svicloudtvbox-lumen}"
REMOTE_THEME_DIR="${REMOTE_THEME_DIR:-public_html/wp-content/themes/svicloudtvbox-lumen}"

if [[ ! -d "$LOCAL_THEME_DIR" ]]; then
  echo "Local theme directory not found: $LOCAL_THEME_DIR" >&2
  exit 1
fi

PY="${PYTHON:-python3}"

exec "$PY" "$ROOT_DIR/scripts/deploy_theme.py" \
  --protocol "$FTP_PROTOCOL" \
  --local-dir "$LOCAL_THEME_DIR" \
  --remote-root "$REMOTE_THEME_DIR" \
  "$@"

