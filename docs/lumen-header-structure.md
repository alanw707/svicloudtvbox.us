# Lumen Header / Navigation Outline

Goal: Align primary navigation with Lumen glassmorphism aesthetic while keeping bilingual support.

## Layout Structure
```
<header class="lumen-header">
  <div class="lumen-header__inner">
    <a class="lumen-header__brand" href="/">
      <span class="lumen-header__glyph" aria-hidden="true"></span>
      <span class="lumen-header__wordmark">SVICLOUD</span>
      <span class="lumen-header__tagline chinese-text">思維雲</span>
    </a>
    <nav class="lumen-nav" aria-label="Primary">
      <ul class="lumen-nav__list">
        <li><a href="/compare/">Compare</a></li>
        <li><a href="/product/svicloud-10p-plus/">10P+</a></li>
        <li><a href="/product/svicloud-10s/">10S</a></li>
        <li><a href="/contact/">Concierge</a></li>
      </ul>
    </nav>
    <div class="lumen-header__actions">
      <a class="lumen-pill lumen-pill--outline" href="/compare/">EN / 中文</a>
      <a class="lumen-pill lumen-pill--primary" href="/cart/">View Cart</a>
    </div>
    <button class="lumen-header__toggle" aria-expanded="false" aria-controls="lumen-mobile-nav">
      <span class="sr-only">Toggle navigation</span>
    </button>
  </div>
  <div class="lumen-mobile-nav" id="lumen-mobile-nav">
    <!-- collapsing bilingual links + CTA -->
  </div>
</header>
```

## Styling Notes
- Header background: translucent glass layer (`background: rgba(8, 18, 42, 0.55)`, `backdrop-filter: blur(24px) saturate(180%)`).
- Brand glyph: circular glow with teal → amber gradient, echoing hero badge.
- Navigation links: pill hover states with subtle glow, bilingual spans using `svic_bilingual_span`.
- Action buttons: `lumen-pill` helpers (primary = filled teal, outline = glass border).
- Mobile toggle: glass icon button that morphs into close icon when expanded.

## Next Steps
1. Implement `lumen-header` styles in new partial (planned `assets/css/parts/12-header.css`).
2. Update `header.php` within Lumen theme to match markup structure, leveraging existing PHP menu functions.
3. Add JS hook in Lumen `theme.js` for mobile menu toggle with reduced-motion handling.
