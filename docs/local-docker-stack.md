# Local Docker Stack (svicloud)

## Purpose
Use this guide to quickly identify the local svicloud containers, networks, and key mounts when the stack is unhealthy or after a restart.

## Expected networks
- `svicloudtvbox_backend`: WordPress + database network.
- `svicloudtvbox_frontend`: Traefik + WordPress network for routing `svicloud10p.svic.local`.

## Helper script
Run the stack status helper to list containers on the svicloud networks and their roles:

```bash
./scripts/docker-stack-status.sh
```

## Compose grouping
To keep the svicloud containers grouped in Docker Desktop, run the stack with the compose files below (from the repo root so `.env` is loaded):

```bash
docker compose -f infrastructure/docker/docker-compose.yml -f infrastructure/docker/sites/svicloud10p.yml up -d
```

## Typical container roles
- WordPress: `svicloud10p-wp` (bind mounts `core/`, `sites/svicloud10p/config/`, and theme directories).
- Database: `svicloud10p-db` or a `mariadb` container (uses `docker_db-data`).
- Proxy: `traefik` for local routing (see `infrastructure/docker/traefik/`).

## Mount expectations (inside the WordPress container)
- `/var/www/html` -> repo `core/`
- `/var/www/html/wp-config-site/wp-config-site.php` -> repo `sites/svicloud10p/config/wp-config-site.php`
- `/var/www/html/wp-content/themes/svicloudtvbox-lumen` -> repo `theme/svicloudtvbox-lumen/`

## Config extras
`WORDPRESS_CONFIG_EXTRA` is evaluated inside `core/wp-config.php`. If you use multiline values in `.env` or `docker run`, pass literal `\n` or `\r\n`; they are translated to real newlines before `eval()`.

## Validation checks
- `docker logs svicloud10p-wp --tail 50` (no parse errors)
- `curl -I http://svicloud10p.svic.local` (200/302)
- Optional: `docker exec -it svicloud10p-wp wp db check`
