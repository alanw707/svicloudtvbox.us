# SVICLOUD Live UI/UX Review – 16 Sep 2025

**Methodology.** Conducted a live crawl of https://svicloudtvbox.us with cache-busting parameters using Playwright (chromium + webkit projects) and cross-checked layouts against repo reference materials (`reference/porto-agency-one-page.md`) and Unblock Tech PDP (https://unblocktech.com/upadpros/47.html). Verified interactions with `npm run test:playwright -- --project=chromium-desktop` and `--project=webkit-mobile` after deployment.

## Findings & Recommendations

1. **Hero Readability & Depth**  
   - *Observation:* The gradient hero now uses brighter body tokens (#1f2937 fallback) but the subtitle still renders at `rgba(255,255,255,0.82)` against the luminous cyan portion, which is less legible than Porto’s solid charcoal hero.  
   - *Recommendation:* Add a subtle linear overlay (e.g., `linear-gradient(180deg, rgba(11,18,32,0.88), rgba(11,18,32,0.65))`) behind the copy block and raise subtitle opacity to 0.9 to match Porto’s high-contrast hero copy.

2. **Hero CTA Hierarchy**  
   - *Observation:* Primary and secondary CTAs both use filled treatments (gradient vs translucent white). Porto references show a clear primary pill vs outline.  
   - *Recommendation:* Reintroduce the outline style for the secondary “View Bundles” CTA and add an inline arrow icon per Porto to reinforce hierarchy/scannability.

3. **Hero Supporting Points**  
   - *Observation:* Point list now uses teal pills, yet the content remains generic checkmarks without bilingual pairs. Unblock’s PDP mixes icons + bilingual microcopy for trust.  
   - *Recommendation:* Replace the plain text spans with bilingual icon rows (e.g., shipping truck + label) mirroring the new credibility bar for stronger scannability.

4. **Spacing Between Sections**  
   - *Observation:* Hero bottom padding (4rem) and credibility strip top padding (2.75rem) compress on large screens, unlike Porto’s generous 6–8rem gutters.  
   - *Recommendation:* Increase vertical rhythm between primary sections to 6rem desktop/4rem mobile to prevent crowding at 1440px+.

5. **Credibility Cards Icon Color**  
   - *Observation:* Icons inherit `currentColor` (navy) while supporting text uses dark grey. On hover the tint barely shifts.  
   - *Recommendation:* Tint the SVG stroke/fill to the gradient blue (`#1b5cff`) and add a subtle glow on hover to align with the teal cues used in hero/product card.

6. **Feature Panels Typography**  
   - *Observation:* Feature headings remain at default `h3` size (≈1.3rem) with 0 margin adjustments, yielding cramped copy compared to Porto’s 1.5rem/32px spacing.  
   - *Recommendation:* Upscale panel headings to 1.5rem with 0.75rem spacing, and lighten paragraph color to `var(--text-muted)` for better hierarchy.

7. **Shop Grid Toggles**  
   - *Observation:* Grid toggle buttons are text-only with minimal states; active indicator is light cyan background that feels non-interactive.  
   - *Recommendation:* Adopt Porto’s segmented control pattern with iconography (grid/list icons), `aria-pressed` states, and focus outlines for accessibility.

8. **Product Cards Highlight Blurbs**  
   - *Observation:* Blurbs still pull trimmed WooCommerce descriptions; the new text color helps, but content density remains high vs Porto’s short 2-line synopsis.  
   - *Recommendation:* Audit copy length to max 18 words and add micro-tag rows (e.g., “4K / Wi-Fi 6 / Karaoke”) using the new chip styling for parity with hero card.

9. **Compare Table Mobile Experience**  
   - *Observation:* Zebra striping improves desktop readability, yet on mobile the three-column table still requires horizontal scroll.  
   - *Recommendation:* Introduce stacked cards below 768px (feature label + two spec rows) mirroring Unblock’s spec layout to avoid pinch-zoom.

10. **Playwright Toggle Verification**  
    - *Observation:* Automated runs confirmed product thumbnail swapping and grid toggle states, but add-to-cart tests only check DOM; no visual spinner assertion.  
    - *Recommendation:* Extend Playwright to assert `.is-loading` class and `aria-busy` transitions on Woo buttons to ensure UX feedback stays intact.

11. **PDP Gallery Thumbnails**  
    - *Observation:* Thumbnail buttons now exist but default to identical imagery (many products only supply a single image).  
    - *Recommendation:* Provide at least three unique lifestyle shots (product-in-use, remote close-up, packaging) to emulate Unblock’s gallery depth and fully showcase the new lightbox behavior.

12. **Footer & Secondary Pages**  
    - *Observation:* Footer still inherits legacy font weights and lacks the refreshed iconography. Secondary pages (support, account) do not pull the new tokens.  
    - *Recommendation:* Back-port `var(--text-body)` + button palette to `footer.php` and enqueue `style.css` across secondary templates to maintain the unified theme.

---

**Next Steps:** Prioritize items 1, 4, 7, and 9 for immediate iteration (readability and responsiveness); schedule separate content refresh for item 11 with marketing. Add follow-up Playwright visual assertions once imagery updates land.
