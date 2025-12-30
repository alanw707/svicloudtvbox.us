# Deployment Configuration — root

## Theme Deploy (Hostinger FTPS)
- Command: `./scripts/deploy-theme.sh [--dry-run] [--delete-remote/--no-delete-remote]`
- Script loads `.env` if present; defaults:
  - protocol: `ftps`
  - local dir: `theme/svicloudtvbox-lumen`
  - remote root: `public_html/wp-content/themes/svicloudtvbox-lumen`
- Internally calls `scripts/deploy_theme.py`; supports `--host/--user/--password/--protocol/--remote-root/--bust-cache/--dry-run`.
- PHP lint check runs before upload when `php` available.
- Excludes: .git, node_modules, maps, etc. Cache-bust via `.deploy-version` when `--bust-cache`.

## Sync to Local Docker WP
- `./scripts/sync_theme_container.sh [container]` finds WordPress container (uses `.env` WORDPRESS_IMAGE/WORDPRESS_VERSION fallback), streams theme into `/var/www/html/wp-content/themes/svicloudtvbox-lumen`, bumps `.deploy-version`, runs `wp cache flush` if available.

## CI/CD
- No GitHub Actions/CI pipelines present. Deploy is manual via FTPS script.

## Notes
- `.env` holds FTP credentials (gitignored); do not commit secrets.
- Use `python3 scripts/zip_theme.py` to package theme before deploy if needed.
