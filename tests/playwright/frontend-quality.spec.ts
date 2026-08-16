import { expect, test, type Page } from '@playwright/test';

async function seedCart(page: Page) {
  await page.goto('/?add-to-cart=12', { waitUntil: 'domcontentloaded' });
}

test.describe('storefront frontend quality safeguards', () => {
  test('skip link is first and moves focus to main content', async ({ page }) => {
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await page.keyboard.press('Tab');

    const skipLink = page.locator('.svic-skip-link');
    await expect(skipLink).toBeFocused();
    await expect(skipLink).toBeVisible();

    await page.keyboard.press('Enter');
    await expect(page.locator('#main-content')).toBeFocused();
  });

  test('mobile navigation contains focus, locks the page, and restores focus', async ({ page }) => {
    await page.setViewportSize({ width: 390, height: 844 });
    await page.goto('/', { waitUntil: 'domcontentloaded' });

    const toggle = page.locator('[data-lumen-toggle]');
    const mobileNav = page.locator('#lumen-mobile-nav');
    await toggle.focus();
    await page.keyboard.press('Enter');

    await expect(toggle).toHaveAttribute('aria-expanded', 'true');
    await expect(mobileNav).toBeVisible();
    await expect(page.locator('body')).toHaveCSS('overflow', 'hidden');
    await expect(page.locator('#main-content')).toHaveAttribute('inert', '');

    for (let index = 0; index < 16; index += 1) {
      await page.keyboard.press('Tab');
      const focusInsideHeader = await page.evaluate(() => {
        const header = document.querySelector('[data-lumen-header]');
        return Boolean(header && document.activeElement && header.contains(document.activeElement));
      });
      expect(focusInsideHeader).toBe(true);
    }

    await page.keyboard.press('Escape');
    await expect(toggle).toHaveAttribute('aria-expanded', 'false');
    await expect(mobileNav).toBeHidden();
    await expect(toggle).toBeFocused();
    await expect(page.locator('#main-content')).not.toHaveAttribute('inert', '');
  });

  test('discovery pages keep a concise, balanced responsive layout', async ({ page }) => {
    await page.setViewportSize({ width: 1440, height: 900 });
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('.lumen-metric, .lumen-feature-card, .lumen-experience')).toHaveCount(0);
    const homepageSections = await page.locator('#main-content > section').count();
    expect(homepageSections).toBeLessThanOrEqual(7);
    await expect(page.locator('.lumen-pricing')).toHaveCSS('content-visibility', 'visible');

    await page.goto('/shop/', { waitUntil: 'domcontentloaded' });
    const desktopColumns = await page.locator('.shop-products__grid').evaluate((element) =>
      getComputedStyle(element).gridTemplateColumns.split(' ').length,
    );
    expect(desktopColumns).toBe(3);

    await page.setViewportSize({ width: 390, height: 844 });
    const mobileColumns = await page.locator('.shop-products__grid').evaluate((element) =>
      getComputedStyle(element).gridTemplateColumns.split(' ').length,
    );
    expect(mobileColumns).toBe(1);
  });

  test('product decision controls meet minimum target sizing', async ({ page }) => {
    await page.setViewportSize({ width: 390, height: 844 });
    await page.goto('/product/svicloud-10p-plus/', { waitUntil: 'domcontentloaded' });

    const quantity = page.locator('.product-hero-cta input.qty');
    await expect(quantity).toBeVisible();
    const quantityBox = await quantity.boundingBox();
    expect(quantityBox?.height ?? 0).toBeGreaterThanOrEqual(44);

    const utilityLinks = page.locator('.product-faq__answer a:visible');
    for (let index = 0; index < await utilityLinks.count(); index += 1) {
      const box = await utilityLinks.nth(index).boundingBox();
      expect(box?.height ?? 0).toBeGreaterThanOrEqual(24);
    }
  });

  test('cart uses the branded semantic template with one checkout action', async ({ page }) => {
    await seedCart(page);
    await page.goto('/cart/', { waitUntil: 'domcontentloaded' });

    await expect(page.locator('[data-cart-page]')).toBeVisible();
    await expect(page.locator('.wc-block-cart')).toHaveCount(0);
    await expect(page.locator('h1.lumen-cart__title')).toHaveCount(1);
    await expect(page.locator('.lumen-cart-summary__totals .checkout-button')).toHaveCount(1);
    await expect(page.locator('.lumen-cart-summary__links')).toHaveCount(0);

    const increase = page.locator('[data-qty-control="increase"]');
    await expect(increase).toHaveAccessibleName(/increase quantity/i);
    const target = await increase.boundingBox();
    expect(target?.width ?? 0).toBeGreaterThanOrEqual(44);
    expect(target?.height ?? 0).toBeGreaterThanOrEqual(44);
    const quantity = page.locator('.lumen-cart-qty__field');
    await expect(quantity).toHaveValue('1');
    await increase.click();
    await expect(quantity).toHaveValue('2');
    await expect(page.locator('.lumen-cart-update')).toBeEnabled();

    const noticePosition = await page.locator('.woocommerce-notices-wrapper').first().evaluate((element) => getComputedStyle(element).position);
    expect(noticePosition).not.toBe('fixed');
    await expect(page.locator('.svic-cart-feedback.is-visible')).toHaveCount(0);
  });

  test('checkout has unique controls, one notice, and reviews the order first on mobile', async ({ page }) => {
    await page.setViewportSize({ width: 390, height: 844 });
    await seedCart(page);
    await page.goto('/checkout/', { waitUntil: 'domcontentloaded' });

    await expect(page.locator('#coupon_code')).toHaveCount(1);
    await expect(page.locator('#checkout_coupon_code')).toHaveCount(1);
    await expect(page.locator('.svic-cart-feedback.is-visible')).toHaveCount(0);

    const summaryBox = await page.locator('.lumen-checkout__summary').boundingBox();
    const primaryBox = await page.locator('.lumen-checkout__primary').boundingBox();
    expect(summaryBox).toBeTruthy();
    expect(primaryBox).toBeTruthy();
    expect(summaryBox!.y).toBeLessThan(primaryBox!.y);

    const paymentMethods = await page.locator('input[name="payment_method"]').count();
    const placeOrder = page.locator('#place_order');
    if (paymentMethods === 0) {
      await expect(placeOrder).toBeDisabled();
      await expect(placeOrder).toHaveText(/payment unavailable/i);
    } else {
      await expect(placeOrder).toBeEnabled();
    }

    await page.evaluate(() => {
      const error = document.createElement('ul');
      error.className = 'woocommerce-error';
      error.innerHTML = '<li>Test validation error</li>';
      document.querySelector('.lumen-checkout')?.prepend(error);
      document.querySelector('#billing_first_name_field')?.classList.add('woocommerce-invalid-required-field');
      const pageWindow = window as Window & {
        jQuery?: (target: EventTarget) => { trigger: (eventName: string) => void };
      };
      pageWindow.jQuery?.(document.body).trigger('checkout_error');
    });
    await expect(page.locator('.woocommerce-error')).toHaveAttribute('role', 'alert');
    await expect(page.locator('#billing_first_name')).toHaveAttribute('aria-invalid', 'true');
  });

  test('all scoped EN and zh discovery routes reflow at 320px', async ({ page }) => {
    await page.setViewportSize({ width: 320, height: 844 });
    const routes = [
      '/', '/shop/', '/compare/', '/product/svicloud-15p/', '/product/svicloud-10p-plus/', '/product/svicloud-9p/',
      '/zh/', '/zh/shop/', '/zh/compare/', '/zh/product/svicloud-15p/',
    ];

    for (const route of routes) {
      const response = await page.goto(route, { waitUntil: 'domcontentloaded' });
      expect(response?.ok(), route).toBeTruthy();
      await expect(page.locator('h1'), `${route} should have one H1`).toHaveCount(1);
      const hasOverflow = await page.evaluate(() => document.documentElement.scrollWidth > window.innerWidth + 1);
      expect(hasOverflow, `${route} should reflow without horizontal scrolling`).toBe(false);
    }
  });

  test('reduced motion disables nonessential page animation and transitions', async ({ browser }) => {
    const context = await browser.newContext({ reducedMotion: 'reduce' });
    const page = await context.newPage();
    await page.goto('/', { waitUntil: 'domcontentloaded' });

    const activeMotion = await page.evaluate(() =>
      [...document.querySelectorAll('*')].filter((element) => {
        const style = getComputedStyle(element);
        const animations = style.animationDuration.split(',').some((value) => parseFloat(value) > 0);
        const transitions = style.transitionDuration.split(',').some((value) => parseFloat(value) > 0);
        return animations || transitions;
      }).length,
    );

    expect(activeMotion).toBe(0);
    await context.close();
  });
});
