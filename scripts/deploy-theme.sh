#!/usr/bin/env bash
set -euo pipefail

# Load .env if present
if [ -f .env ]; then
  set -a
  # shellcheck disable=SC1091
  source .env
  set +a
fi

FTP_HOST=${FTP_HOST:-}
FTP_PORT=${FTP_PORT:-21}
FTP_USER=${FTP_USER:-}
FTP_PASS=${FTP_PASS:-}
FTP_BASE_DIR=${FTP_BASE_DIR:-public_html/wp-content/themes/svicloudtvbox}
LOCAL_THEME_DIR=${LOCAL_THEME_DIR:-theme/svicloudtvbox}
FTP_TLS=${FTP_TLS:-true}
FTP_PASSIVE=${FTP_PASSIVE:-true}
DRY_RUN=${DRY_RUN:-false}

if [ -z "$FTP_HOST" ] || [ -z "$FTP_USER" ] || [ -z "$FTP_PASS" ]; then
  echo "Missing FTP credentials. Set FTP_HOST, FTP_USER, FTP_PASS (via .env or env)." >&2
  exit 2
fi

python3 scripts/deploy_theme_ftp.py \
  --host "$FTP_HOST" \
  --port "$FTP_PORT" \
  --user "$FTP_USER" \
  --password "$FTP_PASS" \
  --remote-base "$FTP_BASE_DIR" \
  --local-dir "$LOCAL_THEME_DIR" \
  $( [ "$FTP_TLS" = "true" ] && echo "--tls" || echo "--no-tls" ) \
  $( [ "$FTP_PASSIVE" = "true" ] && echo "--passive" || echo "--no-passive" ) \
  $( [ "$DRY_RUN" = "true" ] && echo "--dry-run" )

echo "Done."

