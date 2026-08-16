import { expect, test } from '@playwright/test';

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
