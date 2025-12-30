# UI Component Inventory — root

## Component Groups (inferred from templates, CSS parts, and theme.js)
- **Header & Navigation**: Lumen header with sticky/transparent states, mobile nav toggle/submenus (`data-lumen-header`, `data-lumen-toggle`, `#lumen-mobile-nav`).
- **Hero & Marketing Sections**: Hero variants, dashboard visuals/animations, certification, metrics strip, feature grid, experience, pricing, blog highlights (CSS parts: `30-hero.css`, `32-34`, `44-49`, `50-lumen-section-utilities.css`, `51-frontpage-blog.css`).
- **Pages**: About (`52-59`), Guides (`67`), FAQ (`68`), Contact/Policy (`69*`), Blog (`70`), Support form (`70-support-form.css`), Return policy (`70a`), Checkout (`71*`), Order tracking/received (`72-75`).
- **WooCommerce UI**: Shop/product cards, cart quantity steppers, checkout summary panels, coupon relocation, Stripe saved card pills (`initStripeSavedCardPills`), cart feedback toasts, add-to-cart loading states.
- **Product Media**: Product hero gallery (thumb → stage swap), product card carousel.
- **Language UX**: Language toggle adding `lang-zh` class; cookie persist for locale.
- **Motion/Performance**: Animate-on-scroll, preload critical hero images, lazy background sections, header scroll states.
- **Contact/Engagement**: Contact buttons with optional `gtag` tracking, contact form enhancements.

## State/Interaction Patterns
- jQuery event-driven behaviors; no global state container. Interaction scoped to DOM components.
- WooCommerce AJAX hooks: add-to-cart, checkout events drive UI updates.

## Gaps / Follow-ups
- No documented design system tokens beyond `00-tokens.css`; consider a short token reference if desired.
- Component props/variants live in templates + CSS class names; no Storybook or component catalog present.
