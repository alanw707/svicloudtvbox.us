import { test, expect } from '@playwright/test';

const paths = [
  '/',
  '/compare/',
  '/shop/',
  '/product/svicloud-10p-plus/',
  '/product/svicloud-10s/',
  '/product/svicloud-15p/',
  '/my-account/',
  '/guides/',
];

test.describe('SVICLOUD site smoke', () => {
  for (const path of paths) {
    test(`loads ${path} without console errors`, async ({ page, baseURL }) => {
      const errors: string[] = [];
      page.on('console', (msg) => {
        const location = msg.location();
        if (msg.type() === 'error' && !location.url.startsWith('https://fonts.gstatic.com/')) {
          errors.push(msg.text());
        }
      });

      const urlObj = new URL(path, baseURL);
      urlObj.searchParams.set('cb', Date.now().toString());
      const resp = await page.goto(urlObj.toString(), { waitUntil: 'domcontentloaded' });
      expect(resp?.ok()).toBeTruthy();

      // Basic header checks
      await expect(page.locator('header.lumen-header')).toBeVisible();
      await expect(page.locator('.lumen-header__logo-image')).toHaveCount(1, { timeout: 5000 });

      // Page specific probes
      if (path.startsWith('/product/')) {
        const hero = page.locator('.lumen-product .product-hero, .product-hero').first();
        await expect(hero).toBeVisible();
        const isBackorder15p = path === '/product/svicloud-15p/';
        await expect(page.locator('.product-hero-price, .lumen-product .product-hero-price')).toBeVisible();
        const thumbCount = await page.locator('.product-thumb').count();
        if (thumbCount > 1) {
          const thumbLocator = page.locator('.product-thumb').nth(thumbCount - 1);
          const firstSrc = await page.locator('.product-hero-image').first().getAttribute('src');
          const targetSrc = await thumbLocator.getAttribute('data-image');
          await thumbLocator.click();
          await expect(thumbLocator).toHaveAttribute('aria-pressed', 'true');
          if (firstSrc && targetSrc && targetSrc !== firstSrc) {
            await expect(page.locator('.product-hero-image').first()).toHaveAttribute('src', targetSrc);
          }
        }
        const addBtn = page.locator('.single_add_to_cart_button');
        await expect(addBtn).toBeVisible();
        await expect(addBtn).toHaveText(isBackorder15p ? /backorder 15p/i : /add to cart/i);
        if (isBackorder15p) {
          await expect(page.locator('.pdp-compare')).toBeVisible();
          await expect(page.locator('.stock.available-on-backorder')).toBeVisible();
        }
        await page.evaluate(() => {
          const form = document.querySelector('form.cart');
          if (!form) return;
          form.addEventListener('submit', (event) => event.preventDefault(), { once: true });
        });
        await addBtn.click();
        await page.waitForTimeout(150);
        const { hasLoading, ariaBusy, disabled } = await addBtn.evaluate((btn) => ({
          hasLoading: btn.classList.contains('is-loading'),
          ariaBusy: btn.getAttribute('aria-busy'),
          disabled: btn.hasAttribute('disabled'),
        }));
        if (hasLoading || ariaBusy === 'true' || disabled) {
          await expect(addBtn).toHaveAttribute('aria-busy', 'true');
          await page.evaluate(() => {
            if (window.jQuery) {
              const $btn = window.jQuery('.single_add_to_cart_button');
              if ($btn && $btn.length) {
                window.jQuery(document.body).trigger('added_to_cart', [$btn]);
              }
            }
          });
          await expect(addBtn).not.toHaveClass(/is-loading/, { timeout: 5000 });
          await expect(addBtn).toHaveAttribute('aria-busy', 'false');
        } else {
          await expect(addBtn).toBeEnabled();
        }
      }

      if (path === '/') {
        await expect(page.locator('.hero-dashboard')).toBeVisible();
        await expect(page.locator('.hero-15p')).toBeVisible();
        await expect(page.locator('.lumen-metric')).toHaveCount(0);
        await expect(page.locator('.lumen-feature-card')).toHaveCount(0);
        await expect(page.locator('.frontpage-traffic')).toBeVisible();
        await expect(page.locator('.lumen-inbox')).toBeVisible();
        await expect(page.locator('.lumen-certification')).toBeVisible();
        await expect(page.locator('.lumen-pricing')).toBeVisible();
        await expect(page.locator('.lumen-confidence')).toBeVisible();
        await expect(page.locator('.lumen-faq')).toBeVisible();

        await expect(page.locator('.footer-brand__badge')).toHaveCount(3);

        const firstBenefitIcon = page.locator('.footer-benefits__icon').first();
        await expect(firstBenefitIcon).toBeVisible();
        const iconStyles = await firstBenefitIcon.evaluate((el) => {
          const computed = window.getComputedStyle(el);
          return {
            backgroundImage: computed.backgroundImage,
            boxShadow: computed.boxShadow,
          };
        });
        expect(iconStyles.backgroundImage).toContain('gradient');
        expect(iconStyles.boxShadow).not.toBe('none');
      }

      if (path === '/compare/') {
        const modernCardCount = await page.locator('.compare-product-card').count();
        const tableLocator = page.locator('.comparison-table table');

        if (modernCardCount > 0) {
          expect(modernCardCount).toBeGreaterThanOrEqual(2);
          const differenceCount = await page.locator('.compare-difference-card').count();
          expect(differenceCount).toBeGreaterThan(0);
          const comparisonItems = await page.locator('.compare-product-card__comparison-item').count();
          expect(comparisonItems).toBeGreaterThan(0);
        } else if (await tableLocator.isVisible()) {
          const rowHeaders = await page.locator('.comparison-table tbody th').count();
          expect(rowHeaders).toBeGreaterThan(0);
        } else {
          // legacy mobile cards fallback
          const cardCount = await page.locator('.comparison-card').count();
          expect(cardCount).toBeGreaterThan(0);
        }
      }

      if (path === '/guides/') {
        // Hero redesign guard: no giant oval, de-pilled highlights, single framed media
        await expect(page.locator('.guides-hero__pill')).toHaveCount(0);
        await expect(page.locator('.guides-hero__frame')).toHaveCount(1);
        await expect(page.locator('.guides-hero img')).toHaveCount(1);

        const highlights = page.locator('.guides-hero__highlights li');
        expect(await highlights.count()).toBeGreaterThan(0);
        const highlightStyles = await highlights.evaluateAll((items) =>
          items.map((el) => {
            const style = window.getComputedStyle(el);
            return {
              radius: parseFloat(style.borderTopLeftRadius) || 0,
              background: style.backgroundColor,
              borderWidth: parseFloat(style.borderTopWidth) || 0,
            };
          })
        );
        for (const style of highlightStyles) {
          expect(style.radius).toBe(0);
          expect(style.borderWidth).toBe(0);
          expect(style.background).toBe('rgba(0, 0, 0, 0)');
        }

        // Only the eyebrow badge + 2 CTAs may be capsule-shaped inside the hero
        const capsuleCount = await page.locator('.guides-hero').evaluate((hero) =>
          Array.from(hero.querySelectorAll('*')).filter((el) => {
            const style = window.getComputedStyle(el);
            const box = el.getBoundingClientRect();
            if (box.width < 40 || box.height < 18) return false;
            const radius = parseFloat(style.borderTopLeftRadius) || 0;
            const isCapsule = radius >= box.height / 2 - 1;
            const hasChrome =
              style.backgroundColor !== 'rgba(0, 0, 0, 0)' || (parseFloat(style.borderTopWidth) || 0) > 0;
            return isCapsule && hasChrome;
          }).length
        );
        expect(capsuleCount).toBe(3);

        const overflows = await page.evaluate(
          () => document.documentElement.scrollWidth > window.innerWidth + 1
        );
        expect(overflows).toBe(false);
      }

      if (path === '/my-account/') {
        const form = page.locator('form.login, form.woocommerce-form-login').first();
        const username = page.locator('input[name="username"]').first();
        const loginButton = page.locator('button[name="login"], input[name="login"]').first();

        await expect(form).toBeVisible();
        const formBox = await form.boundingBox();
        const inputBox = await username.boundingBox();
        const buttonBox = await loginButton.boundingBox();
        if (!formBox || !inputBox || !buttonBox) {
          throw new Error('Account login form is missing layout boxes');
        }

        const inputStyle = await username.evaluate((el) => {
          const style = window.getComputedStyle(el);
          return {
            backgroundColor: style.backgroundColor,
            borderRadius: style.borderRadius,
          };
        });

        // Original bug was a flush-left (0px) raw form; mobile uses a smaller gutter than desktop
        expect(formBox.x).toBeGreaterThanOrEqual(12);
        expect(inputBox.height).toBeGreaterThanOrEqual(44);
        expect(inputStyle.backgroundColor).not.toBe('rgb(255, 255, 255)');
        expect(parseFloat(inputStyle.borderRadius)).toBeGreaterThanOrEqual(8);
        expect(buttonBox.width).toBeGreaterThanOrEqual(120);
        expect(buttonBox.height).toBeGreaterThanOrEqual(44);
      }

      // No console errors
      expect(errors, errors.join('\n')).toHaveLength(0);
    });
  }
});
