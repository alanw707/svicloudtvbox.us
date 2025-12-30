# API Contracts — root

## Scope
Deep scan of theme for custom APIs or route handlers.

## Findings
- No custom REST endpoints or bespoke API handlers present in the theme. Frontend JS is purely UI/UX: navigation, smooth scrolling, hero/gallery, cart UX, checkout coupon relocation, Stripe saved-card pills, contact click tracking, and product grid toggles.
- Theme relies on WordPress/WooCommerce core AJAX endpoints (e.g., `wc-ajax=add_to_cart`, `admin-ajax.php`) but does not define new endpoints in this repo.

## Notes
- Any API integration is inherited from WooCommerce/WordPress; no OpenAPI/GraphQL specs or custom routes were found in the codebase.
