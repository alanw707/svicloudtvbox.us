# 15P delivery-banner branch rebase record

## Rebase

- Updated `origin/main`: `d4c2e2a358d1049099985916553b051bd1e5824d`
- Local `main` preserved: `b25fa8aa3ba892c221c11e1a885d31c5f30815cf`
- Branch rebased: `safety/15p-delivery-banner-20260818`
- Branch after rebase: `a935b867326da2b73e47826ab9511d1ea3eadd3d`
- `origin/main` is an ancestor of the rebased branch.
- Ahead/behind: `0` behind / `53` ahead.
- Conflicts: none.
- Worktree: clean.
- No push or rewrite of `main`/`origin/main`.

The approved banner implementation and cache-bust marker were preserved in rebased commits `6378799`, `700cdc9`, and `a935b86`.

## Post-rebase validation

- PHP lint across theme: pass.
- CSS rebuild from partials: pass.
- Targeted Chromium/WebKit launch and localized commerce tests: 16/16 pass.
- Local 15P storefront audit: 36/36 pass.
- Fixture security: 3/3 pass.
- Private-fixture preservation: pass.
- Local 15P state: `publish|visible|1|379|288|288|1|0|notify|onbackorder|5`.
- PDP schema: one BackOrder Product offer; no `deliveryTime`.

The full local SEO audit currently reports six duplicate Product `@id` findings on accessory-expanded Shop pages inherited from the updated upstream Shop schema commits. The 15P PDP schema and targeted launch checks pass; this unrelated upstream baseline finding is not modified by the delivery-banner rebase.

Raw command outputs and before/after rebase records remain in the external preflight directory:
`/home/alanw/.pi/backups/svicloudtvbox.us/2026-08-18-15p-delivery-banner-preflight`.
