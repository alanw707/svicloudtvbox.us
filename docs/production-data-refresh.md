# Public production theme-fixture sync

`scripts/sync_public_theme_fixture.py` refreshes local content from production WordPress REST APIs. It is for realistic theme development data—not a production clone.

## What it imports

The sync makes read-only production requests with the application password configured in `.env` and replaces these local public resources. Media is fetched through the unauthenticated `status=inherit` endpoint, which verifies that it is publicly readable before it can be imported:

- published pages and posts;
- media, including featured images and product images;
- WordPress categories and tags;
- published WooCommerce products, product categories/tags, global product attributes/terms, product display attributes, and variations;
- classic menus/menu items and block navigation posts;
- field-limited display settings: site title/description, front/posts page selection, and site icon.

Source URLs in imported post, product, navigation, menu, and media content are rewritten to the local site URL. Downloaded assets live in the ignored local `sites/` runtime directory. The existing local custom-logo/site-icon attachments are retained as local infrastructure so the active theme chrome remains valid.

After importing the production-derived catalog, the local importer recreates one documented supplemental product: the SVICLOUD 15P. Its verified specifications, five watermarked source/gallery images, and watermarked marketing artwork are tracked with the theme and documented in `docs/15p-source-traceability.md`. It is catalog-visible and purchasable for a $288 sale price ($379 regular) with managed zero stock and notified backorders. Its shipping date is not announced. This local-only supplement is never sent to production and its exact commerce/media state is verified after every applied refresh.

## What it never requests or changes

- production users, orders, refunds, customers, addresses, payment data, sessions, logs, API keys, credentials, or private plugin data;
- local users, orders, customer data, credentials, database settings, plugins, theme files, or draft/private/future/trash pages, posts, products, navigation, their attached media, and terms they use;
- production data or settings.

The production API account must have permission to read the listed public-content endpoints. It is never used to write to production. Local imports use WP-CLI because the local WooCommerce REST account is intentionally not granted catalog-write access.

## Required local configuration

`.env` must contain these existing values; do not paste them into documentation, chat, or Git:

```dotenv
WP_REST_ENDPOINT=https://production.example/wp-json/wp/v2
WP_REST_USERNAME=production-rest-reader
WP_REST_PASSWORD=application-password
WP_REST_LOCAL_ENDPOINT=http://svicloud10p.svic.local/wp-json/wp/v2
```

`WP_REST_ENDPOINT` may be the site root or a `/wp-json/...` endpoint. The scripts normalize it to the site root. `WP_REST_PASSWORD` is a WordPress **Application Password**, not the normal account password.

## Procedure

1. Verify approved source coverage without saving API response data:

   ```bash
   python3 scripts/audit_public_theme_fixture_rest.py --env-file .env
   ```

   The audit prints only endpoint status and resource counts. It performs GET requests for public fixture endpoints (including unauthenticated, `status=inherit` media) plus a field-limited display-settings request. It does not request users, orders, or customer records.

2. Fetch a fixture in memory and review counts. This is non-destructive:

   ```bash
   python3 scripts/sync_public_theme_fixture.py --env-file .env
   ```

3. Replace local public fixture content:

   ```bash
   python3 scripts/sync_public_theme_fixture.py --env-file .env --apply
   ```

   `--apply` deletes and recreates managed or published local pages/posts/products/media/categories/tags/product attributes/menu items/navigation, then recreates the documented local-only 15P product. Draft, pending, private, future, and trashed local content is retained with its attached media and terms. It compares representative local page, product, media, and menu records to the in-memory production fixture, verifies the 15P state and image count, and aborts if local user/order/customer counts change. Local private counts may be nonzero; the sync preserves them rather than requiring or making them zero. It does not delete local users or query/create private production records. Do not run it against a shared or production container.

## Validation

After an applied refresh, validate counts and local URLs:

```bash
docker exec svicloud10p-wp wp post list --post_type=page --post_status=publish --format=count --allow-root
docker exec svicloud10p-wp wp post list --post_type=post --post_status=publish --format=count --allow-root
docker exec svicloud10p-wp wp post list --post_type=product --post_status=publish --format=count --allow-root
docker exec svicloud10p-wp wp post list --post_type=attachment --format=count --allow-root
docker exec svicloud10p-wp wp menu list --format=count --allow-root
docker exec svicloud10p-wp wp post list --post_type=nav_menu_item --post_status=publish --format=count --allow-root
docker exec svicloud10p-wp wp option get home --allow-root
docker exec svicloud10p-wp wp option get siteurl --allow-root
docker exec svicloud10p-wp wp user list --format=count --allow-root
```

Open representative pages, products, menus, and media at `http://svicloud10p.svic.local`. To seed draft/private records, apply the fixture, verify their content/media/terms, and clean up the probe, run:

```bash
python3 scripts/verify_private_fixture_preservation.py --env-file .env
```

Verify every WordPress-generated imported product permalink returns 200, then run the smoke suite:

```bash
PLAYWRIGHT_BASE_URL=http://svicloud10p.svic.local node scripts/verify_public_fixture_routes.mjs
PLAYWRIGHT_BASE_URL=http://svicloud10p.svic.local npx playwright test tests/playwright/smoke.spec.ts --project=chromium-desktop -g 'loads / without console errors'
```

## Current endpoint limitations

The sync imports only data returned by the approved core WordPress and WooCommerce REST endpoints. It deliberately does not import customizer settings, plugin-specific options, private custom-post types, order data, customers, or plugin tables. If the theme needs a public configuration value that is absent from the endpoint list, add a narrowly field-limited, read-only endpoint and document its classification before extending the sync.
