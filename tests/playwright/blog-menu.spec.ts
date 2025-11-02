import { test, expect } from '@playwright/test';

const scenarios = [
  { name: 'english homepage', path: '/' },
  { name: 'chinese homepage', path: '/?lang=zh' },
];

test.describe('Blog navigation submenu', () => {
  for (const scenario of scenarios) {
    test(`renders latest posts for ${scenario.name}`, async ({ page, baseURL }) => {
      const url = new URL(scenario.path, baseURL);
      url.searchParams.set('cb', Date.now().toString());

      await page.goto(url.toString(), { waitUntil: 'domcontentloaded' });
      await expect(page.locator('header.lumen-header')).toBeVisible();

      const desktopLinks = page.locator('nav.lumen-nav li.menu-item-svic-dynamic a');
      const desktopCount = await desktopLinks.count();
      expect(desktopCount).toBeGreaterThan(0);

      for (let i = 0; i < desktopCount; i++) {
        const text = (await desktopLinks.nth(i).innerText()).trim();
        expect(text.length, `Desktop blog submenu item ${i + 1} should have text`).toBeGreaterThan(0);
      }

      const toggle = page.locator('[data-lumen-toggle]');
      if (await toggle.isVisible()) {
        await toggle.click();

        const mobileNav = page.locator('.lumen-mobile-nav');
        await expect(mobileNav).toBeVisible();

        const mobileLinks = mobileNav.locator('li.menu-item-svic-dynamic a');
        const mobileCount = await mobileLinks.count();
        expect(mobileCount).toBeGreaterThan(0);

        for (let i = 0; i < mobileCount; i++) {
          const text = (await mobileLinks.nth(i).innerText()).trim();
          expect(text.length, `Mobile blog submenu item ${i + 1} should have text`).toBeGreaterThan(0);
        }
      } else {
        const mobileLinks = page.locator('.lumen-mobile-nav li.menu-item-svic-dynamic a');
        const mobileCount = await mobileLinks.count();
        expect(mobileCount).toBeGreaterThan(0);
      }
    });
  }
});
