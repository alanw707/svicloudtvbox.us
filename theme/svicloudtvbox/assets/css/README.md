CSS authoring guide

- Edit partials in `assets/css/parts/*.css`.
- Do NOT edit `assets/css/style.css` directly; it is generated on deploy by `scripts/build_css.py`.
- The generator strips sections between markers inside `style.css` and prepends partials, allowing an incremental migration.
- Current sections moved to partials:
  - tokens/base/typography (`00-tokens.css`)
  - header (`10-header.css`)

Local build
- Run `python3 scripts/build_css.py` to regenerate `style.css`.

