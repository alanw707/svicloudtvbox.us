# Repository Guidelines

## Project Overview
WordPress/WooCommerce e-commerce theme for SVICLOUD TV Box. Primary theme is `svicloudtvbox-lumen` with bilingual support (English/Chinese).

## Project Structure
```
theme/svicloudtvbox-lumen/     # Primary WordPress theme
  assets/css/parts/            # CSS partials (numbered: 00-90)
  assets/css/bundles.json      # Bundle configuration
  assets/js/theme.js           # Main JavaScript (jQuery, no bundler)
  inc/                         # PHP class files and helpers
  woocommerce/                 # WooCommerce template overrides
  lang/                        # Translation files (en_US.php, zh_TW.php, zh_CN.php)
scripts/                       # Python build/deploy scripts
tests/playwright/              # End-to-end tests
infrastructure/docker/         # Local WordPress Docker setup
```

## Build Commands

### CSS Build
```bash
# Build all bundles (minified)
python3 scripts/build_css.py --theme svicloudtvbox-lumen

# Build specific bundle, pretty-printed (for debugging)
python3 scripts/build_css.py --bundle front-page --pretty

# Build all themes
python3 scripts/build_css.py --all
```

### Sync to Local Docker
```bash
# Always sync after CSS changes
./scripts/sync_theme_container.sh
```

### Deployment
```bash
# Dry run (preview changes)
./scripts/deploy-theme.sh --dry-run

# Deploy with remote cleanup
./scripts/deploy-theme.sh --delete-remote
```

### Theme Distribution
```bash
python3 scripts/zip_theme.py
```

## Test Commands

### Playwright E2E Tests
```bash
# Run all tests
npm test

# Run single test file
npx playwright test tests/playwright/smoke.spec.ts

# Run specific test by name
npx playwright test -g "loads /compare/ without console errors"

# Run in headed mode (visible browser)
npm run test:playwright:headed

# Run specific project (chromium-desktop, webkit-mobile)
npx playwright test --project=chromium-desktop
```

Base URL defaults to `http://svicloud10p.svic.local`. Override with:
```bash
PLAYWRIGHT_BASE_URL=https://example.com npx playwright test
```

## Code Style Guidelines

### PHP
- Follow WordPress PHP Coding Standards (4-space indent, inline braces)
- Prefix all functions/hooks with `svic_` (e.g., `svic_get_localized_url()`)
- Use `declare(strict_types=1);` in helper files
- Guard against undefined with `if (!function_exists('svic_...'))` wrappers
- Escape all output: `esc_html()`, `esc_url()`, `esc_attr()`, `wp_kses_post()`
- Early exit pattern: `if (!defined('ABSPATH')) { exit; }`

```php
if (!function_exists('svic_example_function')) {
    function svic_example_function(string $param): string {
        if ($param === '') {
            return '';
        }
        return esc_html($param);
    }
}
```

### CSS
- Partials live in `assets/css/parts/` with numeric prefixes (e.g., `30-hero.css`)
- **Never edit generated files** (`style.css`, `front-page.css`, etc.) directly
- CSS custom properties in `:root` block of `00-tokens.css`
- BEM-style naming following section context: `hero-*`, `lumen-*`, `compare-*`
- Update `bundles.json` when adding new partials

```css
/* Good: Section-scoped naming */
.lumen-certification__badge { }
.compare-product-card__title { }
.hero-dashboard__card { }

/* Bad: Generic names */
.card { }
.title { }
```

### JavaScript
- IIFE pattern with jQuery: `(function($) { 'use strict'; ... })(jQuery);`
- Guard against missing DOM nodes before operating
- No external dependencies beyond jQuery (WP-bundled)
- Use `$` for jQuery, avoid global namespace pollution

```javascript
function initFeature() {
    const $element = $('.my-element');
    if (!$element.length) {
        return; // Guard against missing elements
    }
    // ...
}
```

### Naming Conventions
| Type | Convention | Example |
|------|------------|---------|
| PHP functions | `svic_snake_case` | `svic_get_current_locale()` |
| PHP classes | `SVIC_Pascal_Case` | `SVIC_Translator` |
| CSS classes | `kebab-case` (BEM) | `lumen-header__logo-image` |
| CSS variables | `--kebab-case` | `--lumen-accent-teal` |
| JS functions | `camelCase` | `initLanguageSwitcher()` |

### Error Handling
- PHP: Use strict type hints; return early on invalid input
- JavaScript: Guard DOM operations; use try/catch for storage APIs
- Never suppress errors in production code

## CSS Bundle System
Bundles are defined in `theme/svicloudtvbox-lumen/assets/css/bundles.json`:

| Bundle | Output File | Purpose |
|--------|-------------|---------|
| style | `style.css` | Global/base (header, footer, tokens) |
| front-page | `front-page.css` | Homepage marketing sections |
| compare | `compare.css` | Product comparison page |
| woocommerce | `woocommerce.css` | Shop, cart, checkout, PDP |
| about | `about.css` | About page |
| guides | `guides.css` | Help/documentation pages |

**Workflow**: Edit partials -> run `build_css.py` -> run `sync_theme_container.sh`

## Testing Checklist
Before deploying, verify:
- [ ] Homepage: hero, metrics strip, pricing toggles, dark-mode switch
- [ ] Compare: `/compare/` table + mobile cards
- [ ] WooCommerce: `/product/svicloud-10p-plus/`, add-to-cart, checkout
- [ ] Console: No JS errors (desktop + mobile viewports)
- [ ] Capture before/after screenshots for UI changes

## Git Workflow
- Use Conventional Commits: `feat(theme):`, `fix(css):`, `docs:`, `refactor:`
- Group related PHP/CSS/assets in single commits
- Never commit generated CSS files without corresponding partial changes
- Secrets stay in `.env` / `.ftppass` (gitignored)

## Important Files
- `functions.php` - Theme bootstrap, hooks, meta registry
- `inc/class-svic-translator.php` - Bilingual translation system
- `inc/class-svic-locale-resolver.php` - URL/locale routing
- `inc/helpers-svic.php` - Utility functions
- `header.php` / `footer.php` - Site chrome
- `front-page.php` - Homepage template
- `page-compare.php` - Comparison page template

## Environment Variables
Set in `.env` for local development:
```
WORDPRESS_IMAGE=wordpress:6.x
FTP_HOST=ftp.example.com
FTP_USER=username
FTP_PROTOCOL=ftps
```

## Common Tasks

### Add new CSS section
1. Create `assets/css/parts/XX-section-name.css`
2. Add to appropriate bundle in `bundles.json`
3. Run `python3 scripts/build_css.py && ./scripts/sync_theme_container.sh`

### Add new translation string
1. Add key to `lang/en_US.php` and `lang/zh_TW.php`
2. Use `svic_translate('key.path')` in PHP templates

### Debug CSS issues
```bash
python3 scripts/build_css.py --bundle front-page --pretty
```
Check the unminified output in `assets/css/front-page.css`
