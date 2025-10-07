import { test, expect } from '@playwright/test';

const THEME_BASE = 'http://svicloud10p.svic.local';

async function seedCart(page: import("@playwright/test").Page) {
  await page.goto(`${THEME_BASE}/?add-to-cart=12`, { waitUntil: 'networkidle' });
}

test.describe('Checkout layout spacing', () => {
  test('billing inputs have compact height and generous gutters', async ({ page }) => {
    await seedCart(page);
    await page.goto(`${THEME_BASE}/checkout/`, { waitUntil: 'domcontentloaded' });

    const firstName = page.locator('#billing_first_name');
    await expect(firstName).toBeVisible();

    const panel = page.locator('.lumen-checkout__panel-inner').first();
    const inputBox = await firstName.boundingBox();
    const panelBox = await panel.boundingBox();
    expect(inputBox).toBeTruthy();
    expect(panelBox).toBeTruthy();

    if (!inputBox || !panelBox) {
      test.fail(true, 'Could not compute bounding boxes for layout assertions.');
      return;
    }

    const horizontalPadding = panelBox.width - inputBox.width;
    const leftInset = inputBox.x - panelBox.x;
    const rightInset = panelBox.x + panelBox.width - (inputBox.x + inputBox.width);

    expect(inputBox.height).toBeLessThan(52);
    expect(horizontalPadding).toBeGreaterThan(48);
    expect(leftInset).toBeGreaterThan(20);
    expect(rightInset).toBeGreaterThan(16);

    // Validate coupon form sizing
    const couponToggle = page.locator('.woocommerce-form-coupon-toggle a').first();
    if (await couponToggle.count()) {
      if (await couponToggle.isVisible()) {
        await couponToggle.click();
      } else {
        await couponToggle.click({ force: true });
      }
      const couponInput = page.locator('form.checkout_coupon input[name="coupon_code"]');
      await expect(couponInput).toBeVisible();
      const couponPanel = page.locator('form.checkout_coupon');
      const couponInputBox = await couponInput.boundingBox();
      const couponPanelBox = await couponPanel.boundingBox();
      if (!couponInputBox || !couponPanelBox) {
        test.fail(true, 'Could not compute coupon form bounds.');
        return;
      }
      const couponLeftInset = couponInputBox.x - couponPanelBox.x;
      const couponRightInset = couponPanelBox.x + couponPanelBox.width - (couponInputBox.x + couponInputBox.width);
      expect(couponInputBox.height).toBeLessThan(64);
      expect(couponLeftInset).toBeGreaterThan(18);
      expect(couponRightInset).toBeGreaterThan(14);
    } else {
      test.skip(true, 'Coupon toggle not present on checkout page.');
    }
  });
});
