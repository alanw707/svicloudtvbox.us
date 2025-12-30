# Tech-Spec: Local Docker Recovery + Resource Organization (svicloud)

**Created:** 2025-12-29
**Status:** Ready for Development

## Overview

### Problem Statement
Local svicloud WordPress is down after restart. WordPress reports DB connection issues, but the actual failure is a PHP parse error triggered by `WORDPRESS_CONFIG_EXTRA` and the container is not seeing expected bind mounts (core, site config, themes). The Docker resources are also hard to identify, making troubleshooting slow.

### Solution
Stabilize the local stack without introducing docker-compose: correct `WORDPRESS_CONFIG_EXTRA`, ensure bind mounts resolve to repo paths, and document/automate a minimal “svicloud stack” view so containers and networks are easy to identify. Preserve the existing DB volume.

### Scope (In/Out)

**In scope**
- Fix `WORDPRESS_CONFIG_EXTRA` so `wp-config.php` eval is valid.
- Restore working bind mounts for WordPress core + site config + wp-content + themes.
- Validate DB connectivity and site reachability at `http://svicloud10p.svic.local`.
- Add minimal documentation + helper script(s) to show the svicloud stack containers, networks, and ports.

**Out of scope**
- Introducing docker-compose (unless proven necessary later).
- Refactoring WordPress application code beyond config/mount stabilization.
- Migrating or resetting DB data.

## Context for Development

### Codebase Patterns
- WordPress core lives in `core/` and is bind-mounted into the container.
- Site-specific config is in `sites/svicloud10p/config/wp-config-site.php`.
- Theme source of truth is under `theme/svicloudtvbox-lumen/` and `theme/shared/`.
- Traefik routing config is in `infrastructure/docker/traefik/`.

### Files to Reference
- `core/wp-config.php`
- `sites/svicloud10p/config/wp-config-site.php`
- `infrastructure/docker/traefik/traefik.yml`
- `infrastructure/docker/traefik/dynamic/svicloud10p.yml`
- `.env` / `.env.example`
- `scripts/sync_theme_container.sh`

### Technical Decisions
- Do not add docker-compose unless a clean restart still fails.
- Keep containers grouped by naming + network filters and document clearly.
- Preserve `docker_db-data` volume for MariaDB.

## Implementation Plan

### Tasks

- [ ] Task 1: Locate how the current `svicloud10p-wp` container was started (inspect `docker ps`, `docker inspect`) and capture the exact bind mounts and env vars.
- [ ] Task 2: Fix `WORDPRESS_CONFIG_EXTRA` so it no longer contains literal `\n` and remove `@ini_set` if needed. Prefer a single-line value or pre-processing in `core/wp-config.php` to translate `\n` to real newlines before eval.
- [ ] Task 3: Ensure bind mounts resolve correctly inside the container by confirming `core/wp-config.php`, `sites/svicloud10p/config/wp-config-site.php`, and theme directories are visible from `/var/www/html` and `/var/www/html/wp-content`.
- [ ] Task 4: If mounts are still empty, recreate the `svicloud10p-wp` container with correct bind mounts and env vars (preserve DB volume). Keep container name and networks consistent.
- [ ] Task 5: Add a minimal helper script (e.g., `scripts/docker-stack-status.sh`) that lists containers on `svicloudtvbox_backend`/`svicloudtvbox_frontend` networks and labels their roles (wp/db/traefik), plus a short doc `docs/local-docker-stack.md` describing what each container does.
- [ ] Task 6: Validate site response and DB connectivity (`curl` or browser load to `http://svicloud10p.svic.local`, optional `wp db check`).

### Acceptance Criteria

- [ ] Visiting `http://svicloud10p.svic.local` returns the WordPress site (no 500 errors).
- [ ] `svicloud10p-wp` logs no longer show the `PHP Parse error: unexpected token "@"` from `wp-config.php`.
- [ ] `wp-config-site.php` is loaded from `sites/svicloud10p/config/` (confirmed by container file content or logs).
- [ ] The svicloud containers are discoverable via a single helper script and documented in a short guide.
- [ ] DB data in `docker_db-data` remains intact.

## Additional Context

### Dependencies
- Docker Desktop (current) with WSL integration.
- Traefik routing already configured via `infrastructure/docker/traefik/`.

### Testing Strategy
- `docker logs svicloud10p-wp --tail 50` shows no parse errors.
- `curl -I http://svicloud10p.svic.local` returns 200/302.
- Optional: `wp db check` in the container if WP-CLI is available.

### Notes
- Current failure appears to be `WORDPRESS_CONFIG_EXTRA` containing literal `\n`, which breaks `eval()` in the stock `wp-config.php`.
- Container bind mounts appear present in `docker inspect` but not visible inside the container; a recreate may be required if mount resolution is broken.
