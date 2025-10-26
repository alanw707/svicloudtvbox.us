import { test, expect } from '@playwright/test';

const compareZhUrl = 'http://svicloud10p.svic.local/compare/?lang=zh';

test.describe('Compare hero experience (zh)', () => {
  test('desktop layout, spacing, and contrast', async ({ page }) => {
    await page.goto(compareZhUrl, { waitUntil: 'networkidle' });

    const hero = page.locator('.compare-hero');
    await expect(hero).toBeVisible();

    const paddingTop = await hero.evaluate((el) => parseFloat(getComputedStyle(el).paddingTop));
    expect(paddingTop).toBeGreaterThanOrEqual(0);
    expect(paddingTop).toBeLessThanOrEqual(60);

    const desktopHeroBox = await hero.boundingBox();
    expect(desktopHeroBox?.height ?? 0).toBeGreaterThan(420);

    const backgroundImage = await page
      .locator('.compare-hero__photo--primary')
      .evaluate((el) => getComputedStyle(el).backgroundImage);
    expect(backgroundImage).toContain('svicloud-10p-plus-lifestyle-2');

    const highlights = page.locator('.compare-hero__highlight');
    const desktopHighlightCount = await highlights.count();
    expect(desktopHighlightCount).toBeGreaterThan(0);

    const highlightTexts = await highlights.evaluateAll((nodes) => nodes.map((node) => node.textContent?.trim() ?? ''));
    expect(highlightTexts.filter((text) => text.length > 0).length).toBeGreaterThan(0);

    const primaryCtaColor = await page
      .locator('.compare-hero__actions .lumen-pill--primary')
      .first()
      .evaluate((el) => getComputedStyle(el).color);
    expect(primaryCtaColor).toBe('rgb(4, 19, 39)');

    const viewport = page.viewportSize();
    const wordBreak = await page
      .locator('.compare-hero__title')
      .evaluate((el) => getComputedStyle(el).wordBreak);
    if (viewport && viewport.width >= 960) {
      expect(wordBreak).toBe('keep-all');
    }
  });

  test('mobile hero maintains coverage and tap targets', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 812 });
    await page.goto(compareZhUrl, { waitUntil: 'networkidle' });

    const hero = page.locator('.compare-hero');
    const paddingTop = await hero.evaluate((el) => parseFloat(getComputedStyle(el).paddingTop));
    expect(paddingTop).toBeGreaterThanOrEqual(0);

    const heroBox = await hero.boundingBox();
    expect(heroBox?.height ?? 0).toBeGreaterThan(360);

    const photoOpacity = await page
      .locator('.compare-hero__photo--primary')
      .evaluate((el) => parseFloat(getComputedStyle(el).opacity));
    expect(photoOpacity).toBeGreaterThan(0.3);

    const highlightCount = await page.locator('.compare-hero__highlight').count();
    expect(highlightCount).toBeGreaterThan(0);

    const buttonWidth = await page
      .locator('.compare-hero__action')
      .first()
      .evaluate((el) => el.getBoundingClientRect().width);
    expect(buttonWidth).toBeGreaterThan(140);

    // Ensure no horizontal overflow
    const overflow = await page.evaluate(() => {
      const vw = window.innerWidth;
      const sw = document.documentElement.scrollWidth;
      return { vw, sw };
    });
    expect(overflow.sw).toBeLessThanOrEqual(overflow.vw + 1);
  });
});
