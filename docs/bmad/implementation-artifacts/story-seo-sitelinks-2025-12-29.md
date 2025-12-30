# Story: Strengthen Navigation Signals for Sitelinks

## Story
As a site owner, I want Google to clearly understand our primary navigation structure and priority pages, so sitelinks are more likely to appear for branded queries.

## Acceptance Criteria
- [ ] BreadcrumbList schema is emitted as JSON-LD for pages that render breadcrumbs.
- [ ] SiteNavigationElement fallback schema includes Shop, Compare, Guides, Blog, About, FAQ, and Contact with localized URLs.
- [ ] Blog index and single posts include a visible priority links module pointing to Shop, Compare, Guides, Blog, and About.
- [ ] Guides hub includes the priority links module.
- [ ] Translations include labels/copy for the priority links module (EN, zh_TW, zh_CN).
- [ ] Local sync script avoids deleting host theme files when the theme is bind-mounted in the container.

## Tasks / Subtasks
- [x] Add helper functions to generate localized page URLs and priority links list.
- [x] Render priority links on blog index, blog posts, and guides hub.
- [x] Add priority links CSS partial and wire into bundles.
- [x] Update fallback navigation items for schema and header fallback.
- [x] Add translation strings for priority links module.
- [x] Patch sync script to detect bind mounts and skip delete/copy.
- [x] Rebuild CSS bundles.

## Dev Agent Record
### File List
- theme/svicloudtvbox-lumen/functions.php
- theme/svicloudtvbox-lumen/header.php
- theme/svicloudtvbox-lumen/footer.php
- theme/svicloudtvbox-lumen/home.php
- theme/svicloudtvbox-lumen/single.php
- theme/svicloudtvbox-lumen/page-guides.php
- theme/svicloudtvbox-lumen/assets/css/parts/72-priority-links.css
- theme/svicloudtvbox-lumen/assets/css/bundles.json
- theme/svicloudtvbox-lumen/assets/css/blog.css (generated)
- theme/svicloudtvbox-lumen/assets/css/guides.css (generated)
- theme/svicloudtvbox-lumen/lang/en_US.php
- theme/svicloudtvbox-lumen/lang/zh_TW.php
- theme/svicloudtvbox-lumen/lang/zh_CN.php
- scripts/sync_theme_container.sh

### Change Log
- Added BreadcrumbList JSON-LD registration and dedupe logic for Rank Math.
- Added priority links module to blog/guides and translations.
- Updated fallback nav/schema items to prioritize Shop, Compare, Guides, Blog, About, FAQ, Contact.
- Added CSS partial and rebuilt bundles.
- Patched sync script to avoid deleting bind-mounted host files.

