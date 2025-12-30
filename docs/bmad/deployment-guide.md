# Deployment Guide — svicloudtvbox.us

## Theme Deploy (Hostinger FTPS)
- Command: `./scripts/deploy-theme.sh [--dry-run] [--delete-remote/--no-delete-remote]`
- Reads `.env` for `FTP_HOST`, `FTP_USER`, `FTP_PASS`, optional `FTP_PROTOCOL` (default ftps), `LOCAL_THEME_DIR`, `REMOTE_THEME_DIR`.
- Runs PHP lint on theme files before upload when `php` is available.
- Calls `scripts/deploy_theme.py` under the hood; supports `--bust-cache` to write `.deploy-version`.
- Excludes git/node_modules/maps/etc.; uploads `theme/svicloudtvbox-lumen` to `public_html/wp-content/themes/svicloudtvbox-lumen` by default.

## Local Docker Sync
- `./scripts/sync_theme_container.sh [container]` finds a WordPress container (uses `.env` WORDPRESS_IMAGE/WORDPRESS_VERSION or ancestor filters), streams theme into `/var/www/html/wp-content/themes/svicloudtvbox-lumen`, bumps `.deploy-version`, flushes WP cache if WP-CLI exists.

## Packaging
- `python3 scripts/zip_theme.py` to build a distributable zip.

## CI/CD
- None configured (no GitHub Actions). Deploy is manual via the FTPS script.

## Secrets
- Keep FTP credentials in `.env` (gitignored). Do not commit secrets.
