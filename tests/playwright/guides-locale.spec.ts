import { test, expect } from '@playwright/test';

test.describe('Localized Guides hub routing', () => {
  for (const locale of [
    { prefix: '/zh', label: '使用指南', lang: 'zh-TW' },
    { prefix: '/zh-cn', label: '使用指南', lang: 'zh-CN' },
  ]) {
    test(`routes the ${locale.prefix} header Guides link to the localized hub`, async ({ page }) => {
      await page.goto(`${locale.prefix}/`, { waitUntil: 'networkidle' });
      let link = page.locator('header a').filter({ hasText: locale.label }).first();
      if (!(await link.isVisible())) {
        await page.locator('[data-lumen-toggle]').click();
        link = page.locator('#lumen-mobile-nav a').filter({ hasText: locale.label }).first();
      }
      await expect(link).toBeVisible();
      await link.click();
      await expect(page).toHaveURL(new RegExp(`${locale.prefix}/(?:%e4%bd%bf%e7%94%a8%e6%8c%87%e5%8d%97|使用指南)/?$`, 'i'));
      await expect(page.locator('.guides-page')).toHaveCount(1);
      await expect(page.locator('main h1')).toBeVisible();

      const pageState = await page.evaluate(() => ({
        lang: document.documentElement.lang,
        literalHtml: document.body.innerText.includes('<p>'),
        externalFixtureImages: [...document.images].filter((image) => image.currentSrc.includes('images.unsplash.com')).length,
        essentialImagesLoaded: [...document.querySelectorAll('.guides-hero img, .lumen-header__logo-image')].every((image) => image.complete && image.naturalWidth > 0),
      }));
      expect(pageState.lang).toBe(locale.lang);
      expect(pageState.literalHtml).toBe(false);
      expect(pageState.externalFixtureImages).toBe(0);
      expect(pageState.essentialImagesLoaded).toBe(true);
    });
  }
});
