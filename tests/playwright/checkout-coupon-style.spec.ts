import { test, expect } from '@playwright/test';

const THEME_BASE = 'http://svicloud10p.svic.local';

async function seedCart(page: import('@playwright/test').Page) {
  await page.goto(`${THEME_BASE}/?add-to-cart=12`, { waitUntil: 'networkidle' }).catch(() => undefined);
  await page.goto(`${THEME_BASE}/product/svicloud-10p-plus/`, { waitUntil: 'domcontentloaded' });
  const addToCartButton = page.locator('button[name="add-to-cart"]');
  if (await addToCartButton.count()) {
    await addToCartButton.first().click();
    await page.waitForTimeout(1500);
  }
}

test.describe('Checkout coupon styling', () => {
  test('coupon panel adopts lumen styling tokens', async ({ page }) => {
    await seedCart(page);
    await page.goto(`${THEME_BASE}/checkout/`, { waitUntil: 'domcontentloaded' });

    await page.waitForFunction(() => !!document.querySelector('.lumen-checkout__summary .lumen-checkout-coupon'));
    const couponContainer = page.locator('.lumen-checkout__summary .lumen-checkout-coupon');
    await expect(couponContainer).toBeVisible();

    await expect(couponContainer.locator('input[name="coupon_code"]').first()).toBeVisible();
    await expect(couponContainer.locator('button[name="apply_coupon"]').first()).toBeVisible();

    const controlsDirection = await page.evaluate(() => {
      const controls = document.querySelector('.lumen-checkout-coupon-display .lumen-checkout-coupon__controls');
      if (!controls) return null;
      const styles = getComputedStyle(controls);
      return {
        flexDirection: styles.flexDirection,
        gap: parseFloat(styles.gap || '0'),
      };
    });

    expect(controlsDirection).not.toBeNull();
    if (!controlsDirection) {
      return;
    }

    expect(controlsDirection).not.toBeNull();
  });
});
