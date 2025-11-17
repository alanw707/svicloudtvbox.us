#!/usr/bin/env bash
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "$0")"/../.. && pwd)"
cd "$REPO_ROOT/automation/blog_automation"

docker build -t svicloud/autoblog:local .

docker run --rm \
  --env-file .env \
  -v "$PWD/data:/app/data" \
  -v "$PWD/logs:/app/logs" \
  -v "$PWD/drafts:/app/drafts" \
  -v "$REPO_ROOT/claudedocs:/claudedocs" \
  -v "$REPO_ROOT/secrets:/secrets" \
  svicloud/autoblog:local "$@"
