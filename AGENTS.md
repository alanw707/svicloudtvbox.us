# Repository Guidelines

## Project Structure & Assets
- Primary theme lives in `theme/svicloudtvbox-lumen/`; legacy assets remain under `theme/shared/` if needed.
- PHP entry points: `front-page.php`, `page-compare.php`, `page-about.php`, WooCommerce overrides in `woocommerce/`, and shared layout via `header.php` / `footer.php`.
- CSS source lives in `theme/svicloudtvbox-lumen/assets/css/parts/`. Partials are grouped by numeric prefixes (e.g., `30-hero.css`, `40-lumen-sections.css`) and bundled via `bundles.json` into multiple outputs:
  - `style.css` → global/base styles (tokens, header/nav, utilities).
  - `front-page.css` → homepage + marketing sections.
  - `compare.css` → compare table experience.
  - `woocommerce.css` → PDP, catalog, cart/checkout styling.
- JavaScript for interactive behaviour is in `assets/js/theme.js`; keep it dependency-free and guard against missing DOM nodes.
- Generated artifacts (`style.css`, `front-page.css`, etc.) should never be edited directly—always adjust the partials, rebuild, then sync.

## Build & Sync Workflow
1. Rebuild CSS bundles whenever partials change:
   - `python3 scripts/build_css.py --theme svicloudtvbox-lumen` (minified default).
   - Use `--bundle front-page --pretty` while iterating on a specific bundle.
2. Refresh the distributable before handoff or deployment: `python3 scripts/zip_theme.py`.
3. **Always** push changes into the local Docker WordPress instance after rebuilding: `./scripts/sync_theme_container.sh`.
4. Deployment preview/publish (FTPS) remains via:
   - `./scripts/deploy-theme.sh --dry-run`
   - `./scripts/deploy-theme.sh --delete-remote`
   Both scripts read `.env` for credentials.

## Coding Standards
- Follow WordPress PHP standards (4-space indent, inline braces). Sanitize output with `esc_html__`, `esc_url`, `wp_kses_post`, etc. Prefix helper functions/hooks with `svic_`.
- Ensure new styles land in dedicated partials; update `bundles.json` so no one drops large edits directly into generated `style.css`.
- CSS naming mirrors section context (`hero-*`, `lumen-certification__*`, `bundle-card-*`). Keep shared variables in the `:root` token block. Document new partials in `bundles.json` so they bundle correctly.
- When introducing route-specific styles, prefer creating a new partial and adding it to an existing bundle (or defining a new bundle) rather than expanding global CSS.

## Testing Checklist
- Homepage: hero carousel, certification section, metrics strip, pricing toggles, concierge accordion, dark-mode switch.
- Compare flow: `/compare/` table + mobile cards.
- WooCommerce: `/product/svicloud-10p-plus/`, `/product/svicloud-10s/`, add-to-cart/cart/checkout redirects.
- General: favicon responses, console cleanliness (desktop + mobile widths).
- Capture before/after screenshots when UI shifts; note manual QA steps in PRs.

## Git & Collaboration
- Use Conventional Commits (`feat(theme): …`, `fix(css): …`). Group related PHP/CSS/assets in single commits.
- Keep `main` clean—branch for feature work (e.g., `feat/css-bundles`) and open PRs summarising customer impact, linking to Notion/Trello tickets, attaching desktop/mobile screenshots, and listing manual QA steps.
- Inspect the ZIP artifact prior to deploy to avoid shipping PSDs/cache files; secrets stay in `.env` / `.ftppass`.
## Custom AI Instructions
This section contains the instructions that should be pasted on the AI custom instructions, either for Cline, Claude or Cursor, or any other MCP client. You should copy and paste these rules. For reference, see `custom-instructions.md` which contains these rules.

### Development
Basic development commands:

```
# Install dependencies
npm install

# Build the project
npm run build

# Run tests
npm run test

# Run tests in watch mode
npm run test:watch

# Run the server directly with ts-node for quick testing
npm run dev
```

### Running with Docker
Build the Docker image:

```
docker build -t memory-bank-mcp:local .
```

Run the Docker container for testing:

```
docker run -i --rm \
  -e MEMORY_BANK_ROOT="/mnt/memory_bank" \
  -v /path/to/memory-bank:/mnt/memory_bank \
  --entrypoint /bin/sh \
  memory-bank-mcp:local \
  -c "ls -la /mnt/memory_bank"
```

Add MCP configuration, example for Roo Code:

```
"allpepper-memory-bank": {
  "command": "docker",
  "args": [
    "run", "-i", "--rm",
    "-e", 
    "MEMORY_BANK_ROOT",
    "-v", 
    "/path/to/memory-bank:/mnt/memory_bank",
    "memory-bank-mcp:local"
  ],
  "env": {
    "MEMORY_BANK_ROOT": "/mnt/memory_bank"
  },
  "disabled": false,
  "alwaysAllow": [
    "list_projects",
    "list_project_files",
    "memory_bank_read",
    "memory_bank_update",
    "memory_bank_write"
  ]
}
```
