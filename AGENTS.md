# Repository Guidelines

## Project Structure & Module Organization
The SVICLOUD WordPress theme lives in `theme/svicloudtvbox/` with front-end templates now migrated to PHP (`front-page.php`, `page-compare.php`, WooCommerce overrides inside `woocommerce/`). Shared header/footer partials are `header.php` and `footer.php`; fallback routing uses `index.php` and `404.php`. Static assets compile from `theme/svicloudtvbox/assets/` where `css/style.css` and `js/theme.js` power the hero carousel, dark-mode toggle, and concierge sections. Repo-level `assets/` contains marketing renders used in docs and PDP copy; `reference/` stores design inspiration, and `scripts/` houses packaging/deploy utilities.

## Build, Package & Deployment Commands
```
python3 scripts/zip_theme.py              # rebuilds the distributable zip
./scripts/deploy-theme.sh --dry-run       # show pending FTPS changes without pushing
./scripts/deploy-theme.sh --delete-remote # syncs live theme, removing retired files
```
Commands read `.env` for FTPS host/user/password—never hardcode credentials. Run the dry-run before touching production and keep Hostinger sessions FTPS+TLS.

## Coding Style & Naming Conventions
Follow WordPress PHP coding standards (four spaces, inline braces, `esc_html__`/`wp_kses_post` on output). Prefix custom hooks/helpers with `svic_`. Template markup should stay semantic and bilingual-ready via translation functions. CSS relies on utility-flavored class names (`hero-*`, `bundle-card-*`) with mobile variants in-place; store shared tokens in `:root` inside `assets/css/style.css`. JavaScript in `assets/js/theme.js` must remain dependency-free and guard against missing DOM nodes.

## Testing Guidelines
Spin up a local or staging WordPress with the theme active. Smoke-test homepage hero, pricing grid toggles, concierge accordion, and dark-mode switch on desktop + mobile widths. Open `/product/svicloud-10p-plus/` and `/product/svicloud-10s/` to confirm no fatal PHP errors (current production returns a critical error—fix before deploy). Validate `/compare/` table content, cart redirects, WooCommerce add-to-cart buttons, and ensure there are no missing images, 404 favicon hits, or console warnings. Document manual steps and include screenshots for visual shifts.

## Commit & Pull Request Guidelines
Use Conventional Commits (`feat(theme):`, `fix(woocommerce):`, `chore(scripts):`). Group related PHP, CSS, and asset updates; squash trivial fix-ups. PRs must summarize customer impact, reference Notion/Trello tickets, attach before/after captures (desktop + mobile), and list manual QA (including PDP load + checkout). Request review prior to running deploy scripts unless you are release PIC.

## Security & Configuration Tips
Secrets stay in `.env` and local `.ftppass`; `.gitignore` already excludes them. Inspect zip output before uploads to avoid committing raw PSDs or cache files. When adjusting preloads or SEO metadata, keep tags centralized in `header.php` to prevent duplicates and to ease schema/OG additions later.
