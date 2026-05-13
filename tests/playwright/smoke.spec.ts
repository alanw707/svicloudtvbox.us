import { test, expect } from '@playwright/test';

const paths = [
  '/',
  '/compare/',
  '/shop/',
  '/product/svicloud-10p-plus/',
  '/product/svicloud-10s/',
  '/my-account/',
];

const ignoredConsoleErrorPatterns = [
  // Third-party wallet iframes can emit CSP report/noise from their own frames.
  // These are outside theme control and do not indicate a page regression.
  /\[Report Only\] Refused to frame 'https:\/\/pay\.google\.com\/' because an ancestor violates the following Content Security Policy directive: "frame-ancestors 'self'"\./,
  /Refused to execute inline script because it violates the following Content Security Policy directive: .*https:\/\/\*\.paypal\.com.*https:\/\/\*\.paypalobjects\.com/,
];

function isIgnoredConsoleError(text: string): boolean {
  return ignoredConsoleErrorPatterns.some((pattern) => pattern.test(text));
}

test.describe('SVICLOUD site smoke', () => {
  for (const path of paths) {
    test(`loads ${path} without console errors`, async ({ page, baseURL }) => {
      const errors: string[] = [];
      page.on('console', (msg) => {
        if (msg.type() === 'error' && !isIgnoredConsoleError(msg.text())) errors.push(msg.text());
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
        await expect(addBtn).toHaveText(/add to cart/i);
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
        await expect(page.locator('.hero-dashboard__card')).toBeVisible();
        await expect(page.locator('.lumen-metric')).toHaveCount(4);
        await expect(page.locator('.lumen-feature-card')).toHaveCount(3);

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

      // No console errors
      expect(errors, errors.join('\n')).toHaveLength(0);
    });
  }
});
