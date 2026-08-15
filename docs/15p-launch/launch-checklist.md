# SVICloud 15P — Operational Launch Checklist

## Launch angle (pick one, keep it simple)

**Chosen angle: "New SVICloud 15P — now available in the USA" + free shipping.**
Optional add-on only if margin allows: accessory bonus (e.g., spare remote or HDMI) for first N orders. Skip discounting the launch price — new flagship shouldn't open with a markdown.

## T-minus: before launch day

### Product intel (blockers)
- [ ] Supplier spec sheet + press-kit photos received
- [ ] Final U.S. price + margin confirmed
- [ ] Warranty terms confirmed (match 1-year U.S. standard)
- [ ] Replace all [PLACEHOLDER]/[SPEC TBC]/[TBC] in: product page lang strings (en/zh_TW/zh_CN), intel doc, 3 blog drafts, ads brief, support FAQ

### Inventory & ops
- [ ] Inventory count confirmed and entered in WooCommerce (enable stock management on product)
- [ ] Supplier ETA for restock confirmed
- [ ] Real product photos uploaded (featured image + gallery) with descriptive alt text
- [ ] Final price set on WC product (replaces $299 placeholder)

### Website
- [ ] Create `svicloud-15p` WooCommerce preview in PRODUCTION as out of stock with no price; enable sales only after specs, price, inventory, images, and policies are confirmed
- [ ] Preserve or create `/product/svicloud-9p/` as a published, out-of-stock legacy reference page linking to 15P
- [ ] Add 15P menu item to production primary nav (local done via wp-cli)
- [ ] Deploy theme: `./scripts/deploy-theme.sh --dry-run` then real deploy
- [ ] Test checkout end-to-end on production: add-to-cart → shipping → taxes → payment → confirmation email
- [ ] Verify confirmation email renders 15P name/price correctly
- [ ] Validate schema on live 15P page (Google Rich Results test)
- [ ] Submit updated sitemap in Search Console; request indexing for /product/svicloud-15p/

### Content
- [ ] Publish 3 blog posts (flip status draft→publish, set dates): 15P vs 10P+, Best SVICloud for CJK+USA TV, Where to Buy 15P USA
- [ ] Update announcement bar message (currently "out of stock" message — switch to 15P launch message)

### Support
- [ ] Circulate docs/15p-launch/support-faq-15p.md to support team
- [ ] Update Vapi/support scripts with 15P Q&A
- [ ] Set up "notify me" list handling for spec questions

### Ads (after page is live + tracking verified)
- [ ] Build campaign per docs/15p-launch/google-ads-brief.md
- [ ] Confirm conversion tracking fires on 15P page
- [ ] Merchant Center: verify 15P product appears in feed

## Launch day
- [ ] Flip announcement bar to launch message
- [ ] Publish blog posts
- [ ] Enable ads campaign
- [ ] Monitor first orders + stock count

## Post-launch (week 1)
- [ ] Watch Search Console for 15P query impressions
- [ ] Collect first customer questions → extend FAQ
- [ ] Review ad search terms; add negatives
- [ ] Do NOT remove or de-rank 10P+/9P-era content — keep pages live and funneling to 15P
