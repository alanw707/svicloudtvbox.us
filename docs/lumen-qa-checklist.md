# Lumen Theme QA Checklist (Draft)

Use this list once the theme is activated on staging or local WordPress.

## Preflight
- [ ] Switch site theme to `SVICLOUD TV Box Lumen`.
- [ ] Run `python3 scripts/build_css.py --theme svicloudtvbox-lumen` to ensure CSS bundle is current.

## Homepage (`/`)
- [ ] Validate hero renders with product badge, photo, and static metrics (desktop + mobile).
- [ ] Confirm navigation glass header sticks on scroll and mobile toggle animates correctly.
- [ ] Inspect credibility tiles, feature grid, concierge section, and pricing cards for spacing/alignment at 360, 768, 1280, and 1600 widths.

## Compare Page (`/compare/`)
- [ ] Ensure luminous header/description render; table and cards stay legible on mobile.
- [ ] Test bilingual spans (`EN`/`中文`) for correct visibility under `.hide-zh` / `.hide-en` classes.

## WooCommerce PDPs
- [ ] Open `/product/svicloud-10p-plus/` and `/product/svicloud-10s/`; hero gallery should swap images on thumbnail click.
- [ ] Confirm add-to-cart button styling + loading state, metadata block, and highlights match the Lumen aesthetic.

## Post-Deploy
- [ ] Execute Playwright smoke suite against staging once theme is active.
- [ ] Capture before/after screenshots (desktop + mobile) for homepage, compare, and PDP hero sections.
- [ ] Review browser console for warnings; verify no missing assets.
