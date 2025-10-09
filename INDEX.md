# Repository Index

Structured map of the `svicloudtvbox.us` project. Use this to quickly locate theme code, automation scripts, and project documentation.

## Top Level
- `README.md` — project overview, stack, deployment guidance.
- `AGENTS.md` — catalog of functional agents across theme, JS, and deployment automation.
- `docs/` — planning and reference material (PRD, backlog, launch plan, environment setup, WooCommerce notes, vendor PDFs).
- `scripts/` — automation for theme deployment.
- `theme/` — custom WordPress theme source.

## docs/
- `svicloudtvbox-prd.md` — product requirements baseline.
- `svicloudtvbox-launch-plan.md` — launch checklist and sequencing.
- `svicloudtvbox-backlog.md` — implementation backlog with epics and status.
- `svicloudtvbox-dev-environment.md` — local/staging environment setup guide.
- `svicloudtvbox-hostinger-implementation-plan.md` — Hostinger configuration plan.
- `svicloudtvbox-woocommerce-snippets.md` — reference snippets for WooCommerce behaviors.
- `SviCloud 10p+ 新品发布会.pdf`, `SviCloud 10P+ 规格书.pdf` — vendor-provided collateral (Chinese).

## scripts/
- `deploy-theme.sh` — shell wrapper that loads `.env` credentials and delegates theme upload.
- `deploy_theme_ftp.py` — FTP/FTPS deployer using `ftplib`, supports TLS, passive mode, dry runs.

## theme/svicloudtvbox/
- `functions.php` — theme bootstrap: supports, asset enqueue, WooCommerce helpers.
- `header.php`, `footer.php` — global header/footer markup with menu hooks.
- `front-page.php` — marketing landing page (hero, trust, product tiles, pricing).
- `page-compare.php` — compare template showcasing 10P+ vs 10S.
- `index.php`, `page.php`, `404.php` — core loop, page layout, and error handling.
- `woocommerce/` — overrides for shop archive and single product layouts.
- `assets/css/` — compiled theme styles (primary bundle lives in `assets/css/style.css`).
- `assets/js/theme.js` — front-end behaviors: language toggle, animations, WooCommerce UX tweaks.
- `assets/images/` — static imagery (product renders, logos, hero art).
- `style.css` — theme header metadata pointing to bundled assets.

## Supporting Notes
- Git metadata lives under `.git/`; `.gitignore` excludes build/log artefacts.
- Theme deployment expects FTP credentials in `.env` (not committed).
- WordPress core files are out-of-repo; this repository tracks the custom theme and project docs.

