#!/usr/bin/env bash
# Sync the local svicloudtvbox theme into a running Dockerized WordPress container.
#
# Usage:
#   ./scripts/sync_theme_container.sh [container-name-or-fragment]
# If no name is provided, the script will try to discover a container based on WORDPRESS_IMAGE in .env
# and fall back to the first running container based on a wordpress:* image.

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"
THEME_SLUG="svicloudtvbox-lumen"
LOCAL_THEME_DIR="${REPO_ROOT}/theme/${THEME_SLUG}"
CONTAINER_THEME_DIR="/var/www/html/wp-content/themes/${THEME_SLUG}"
DEFAULT_USER="www-data:www-data"

if [[ ! -d "${LOCAL_THEME_DIR}" ]]; then
  echo "[sync-theme] Local theme directory not found: ${LOCAL_THEME_DIR}" >&2
  exit 1
fi

# Attempt to source .env for WORDPRESS_IMAGE, WORDPRESS_VERSION, etc.
ENV_FILE="${REPO_ROOT}/.env"
if [[ -f "${ENV_FILE}" ]]; then
  set -a
  # shellcheck disable=SC1090
  source "${ENV_FILE}"
  set +a
fi

REQUESTED_FILTER="${1:-}"
CONTAINER_NAME=""

resolve_container() {
  local filter="$1"
  if [[ -n "${filter}" ]]; then
    CONTAINER_NAME=$(docker ps --filter "name=${filter}" --format '{{.Names}}' | head -n1 || true)
  fi
  if [[ -z "${CONTAINER_NAME}" && -n "${WORDPRESS_IMAGE:-}" ]]; then
    CONTAINER_NAME=$(docker ps --filter "ancestor=${WORDPRESS_IMAGE}" --format '{{.Names}}' | head -n1 || true)
  fi
  if [[ -z "${CONTAINER_NAME}" && -n "${WORDPRESS_VERSION:-}" ]]; then
    CONTAINER_NAME=$(docker ps --filter "ancestor=wordpress:${WORDPRESS_VERSION}" --format '{{.Names}}' | head -n1 || true)
  fi
  if [[ -z "${CONTAINER_NAME}" ]]; then
    CONTAINER_NAME=$(docker ps --filter "ancestor=wordpress" --format '{{.Names}}' | head -n1 || true)
  fi
}

resolve_container "${REQUESTED_FILTER}"

if [[ -z "${CONTAINER_NAME}" ]]; then
  echo "[sync-theme] Unable to find a running WordPress container. Pass a container fragment, e.g. ./scripts/sync_theme_container.sh svicloud10p" >&2
  exit 1
fi

echo "[sync-theme] Syncing theme into container: ${CONTAINER_NAME}" >&2

docker exec "${CONTAINER_NAME}" bash -c "mkdir -p '${CONTAINER_THEME_DIR}'"

# Stream the theme contents into the container, replacing the existing files.
tar -C "${LOCAL_THEME_DIR}" -cf - . | \
  docker exec -i "${CONTAINER_NAME}" bash -c "rm -rf '${CONTAINER_THEME_DIR}'/* && tar -C '${CONTAINER_THEME_DIR}' -xf -"

# Ensure correct ownership (ignore failure if user does not exist).
docker exec "${CONTAINER_NAME}" bash -c "chown -R ${DEFAULT_USER} '${CONTAINER_THEME_DIR}'" >/dev/null 2>&1 || true

# Bump deploy version and flush WP cache if possible.
docker exec "${CONTAINER_NAME}" bash -c "date +%s > '${CONTAINER_THEME_DIR}/.deploy-version'" || true

docker exec "${CONTAINER_NAME}" bash -c "if command -v wp >/dev/null 2>&1; then wp cache flush; fi" || true

echo "[sync-theme] Done. Force refresh your browser (⌘⇧R / Ctrl+F5)."