#!/usr/bin/env bash
# Show containers attached to the local svicloud Docker networks.

set -euo pipefail

NETWORKS=("svicloudtvbox_backend" "svicloudtvbox_frontend")

if ! command -v docker >/dev/null 2>&1; then
  echo "[stack-status] Docker is not installed or not on PATH." >&2
  exit 1
fi

role_for_container() {
  local name="$1"
  local image="$2"

  if [[ "$name" == *traefik* || "$image" == *traefik* ]]; then
    echo "traefik"
  elif [[ "$name" == *mariadb* || "$name" == *mysql* || "$name" == *db* || "$image" == *mariadb* || "$image" == *mysql* ]]; then
    echo "db"
  elif [[ "$name" == *wp* || "$name" == *wordpress* || "$image" == *wordpress* ]]; then
    echo "wp"
  else
    echo "unknown"
  fi
}

printf "SVICLOUD local Docker stack\n"
for network in "${NETWORKS[@]}"; do
  if ! docker network inspect "$network" >/dev/null 2>&1; then
    printf "\nNetwork: %s (missing)\n" "$network"
    continue
  fi

  printf "\nNetwork: %s\n" "$network"
  docker ps --filter "network=$network" --format '{{.Names}}	{{.Image}}	{{.Ports}}' | \
    while IFS=$'\t' read -r name image ports; do
      if [[ -z "$name" ]]; then
        continue
      fi
      role=$(role_for_container "$name" "$image")
      ports_display=${ports:-"-"}
      printf "  %-24s %-8s %s\n" "$name" "$role" "$ports_display"
    done

done
