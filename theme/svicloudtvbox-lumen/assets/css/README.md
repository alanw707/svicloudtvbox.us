CSS authoring guide (Lumen theme)

- Edit partials in `assets/css/parts/*.css`. Naming mirrors the legacy theme but will be reworked during redesign.
- Do NOT edit `assets/css/style.css` directly; regenerate via `python3 scripts/build_css.py --theme svicloudtvbox-lumen`.
- The generator concatenates partials in lexicographical order with a banner noting this theme.

Local build example
- `python3 scripts/build_css.py --theme svicloudtvbox-lumen`
