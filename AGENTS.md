# Repository Guidelines

## Project Structure & Module Organization
The WordPress theme lives in `theme/svicloudtvbox/`, with templates in PHP (`front-page.php`, `page-compare.php`, WooCommerce overrides inside `woocommerce/`). Shared layout pieces are `header.php` and `footer.php`; fallback routing depends on `index.php` and `404.php`. Static assets compile from `theme/svicloudtvbox/assets/`, where `css/style.css` stores utility tokens and `js/theme.js` powers interactive widgets. Repo-level `assets/` contains marketing renders, `reference/` archives design inspiration, and `scripts/` houses packaging and deploy utilities.

## Build, Test, and Development Commands
- `python3 scripts/zip_theme.py` — rebuilds the distributable ZIP before handoff or deployment.
- `./scripts/deploy-theme.sh --dry-run` — previews FTPS changes; always run before production sync.
- `./scripts/deploy-theme.sh --delete-remote` — publishes updates and prunes retired files. Both scripts read `.env` for FTPS credentials.
Spin up a local WordPress instance with the theme active for verification; no automated test harness exists yet.

## Coding Style & Naming Conventions
Follow WordPress PHP standards: four-space indentation, inline braces, and sanitized output via `esc_html__`, `wp_kses_post`, or `esc_url`. Prefix custom helpers and hooks with `svic_`. Template markup must stay semantic and translation-ready. CSS utilities follow `hero-*`, `bundle-card-*`, and mobile variants; shared variables live in the `:root` block of `assets/css/style.css`. JavaScript in `assets/js/theme.js` remains dependency-free and guards against missing DOM nodes.

## Testing Guidelines
Manually smoke-test the homepage hero carousel, pricing grid toggles, concierge accordion, and dark-mode switch across desktop and mobile widths. Load `/product/svicloud-10p-plus/` and `/product/svicloud-10s/` to ensure no fatal PHP errors, then confirm `/compare/` table content, WooCommerce add-to-cart flows, cart redirects, favicon responses, and console cleanliness. Document steps taken and capture before/after screenshots when styles shift.

## Commit & Pull Request Guidelines
Use Conventional Commits (e.g., `feat(theme): improve hero carousel autoplay`). Group related PHP, CSS, and asset changes, squashing incidental fix-ups. PRs must summarize customer impact, reference the Notion or Trello ticket, attach desktop and mobile screenshots, and list manual QA steps (including PDP load + checkout). Request review before running deployment scripts unless you are the release PIC.

## Security & Configuration Tips
Keep FTPS secrets inside `.env` and `.ftppass`; never hardcode credentials. Inspect ZIP artifacts before deploy to avoid shipping PSDs or cache files. Centralize SEO and preload edits in `header.php` to prevent duplicates and ease future schema expansions.