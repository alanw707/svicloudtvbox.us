import { test, expect, Page } from '@playwright/test';

const legalDisclaimerZhUrl = '/legal-disclaimer/?lang=zh';
const cardSelector = '.policy-sections__inner .policy-card';

async function collectCardGutters(page: Page) {
  return page.$$eval(cardSelector, (nodes) =>
    nodes.map((node) => {
      const rect = node.getBoundingClientRect();
      const vw = window.innerWidth;
      return {
        left: rect.left,
        right: vw - rect.right,
        width: rect.width,
      };
    }),
  );
}

test.describe('Legal disclaimer layout (zh)', () => {
  test('desktop cards remain centered', async ({ page }) => {
    await page.goto(legalDisclaimerZhUrl, { waitUntil: 'networkidle' });

    const cardCount = await page.locator(cardSelector).count();
    expect(cardCount).toBeGreaterThan(0);

    const gutters = await collectCardGutters(page);
    for (const gutter of gutters) {
      expect(gutter.left).toBeGreaterThanOrEqual(20);
      expect(gutter.right).toBeGreaterThanOrEqual(20);
      expect(Math.abs(gutter.left - gutter.right)).toBeLessThan(8);
      expect(gutter.width).toBeLessThanOrEqual(820);
    }
  });

  test('mobile cards keep balanced gutters', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 812 });
    await page.goto(legalDisclaimerZhUrl, { waitUntil: 'networkidle' });

    const cardCount = await page.locator(cardSelector).count();
    expect(cardCount).toBeGreaterThan(0);

    const gutters = await collectCardGutters(page);
    for (const gutter of gutters) {
      expect(gutter.left).toBeGreaterThanOrEqual(12);
      expect(gutter.right).toBeGreaterThanOrEqual(12);
      expect(Math.abs(gutter.left - gutter.right)).toBeLessThan(6);
    }

    const overflow = await page.evaluate(() => {
      return {
        sw: document.documentElement.scrollWidth,
        vw: window.innerWidth,
      };
    });
    expect(overflow.sw).toBeLessThanOrEqual(overflow.vw + 1);
  });
});
