import { test, expect } from '@playwright/test';
import fs from 'node:fs';
import path from 'node:path';

test.use({ hasTouch: true });

// Test proposed CSS against actual public markup before release; leave unset
// for independent production verification. No cart/order writes are performed.
test.beforeEach(async ({ page }) => {
  await page.route(/(?:google-analytics\.com|googleadservices\.com|doubleclick\.net)\//, route => route.abort());
  if (process.env.SVIC_MENU_LOCAL_CSS === '1') {
    await page.route(/\/themes\/svicloudtvbox-lumen\/assets\/css\/style\.css(?:\?|$)/, route =>
      route.fulfill({ contentType: 'text/css', body: fs.readFileSync(path.resolve('theme/svicloudtvbox-lumen/assets/css/style.css'), 'utf8') }));
  }
});

for (const locale of ['/', '/zh/', '/zh-cn/']) {
  for (const viewport of [{ width: 360, height: 740 }, { width: 393, height: 852 }, { width: 820, height: 430 }]) {
    test(`mobile menu stays tappable with promo: ${locale} ${viewport.width}`, async ({ page, baseURL }, testInfo) => {
      await page.setViewportSize(viewport);
      await page.goto(new URL(locale, baseURL).toString(), { waitUntil: 'domcontentloaded' });
      const banner = page.locator('.svic-promo-bar');
      const toggle = page.locator('[data-lumen-toggle]');
      const nav = page.locator('#lumen-mobile-nav');
      await expect(banner).toBeVisible();
      const bannerHeight = await banner.evaluate(e => e.getBoundingClientRect().height);
      await toggle.tap();
      await expect(nav).toBeVisible();
      await expect(banner).toBeHidden();
      expect(await banner.evaluate(e => e.getBoundingClientRect().height)).toBe(bannerHeight);
      expect(await nav.evaluate(e => !!e.closest('[inert]'))).toBe(false);

      // Actual pointer actions (never force/synthetic clicks) on the previously
      // covered first menu row and its expandable submenu.
      const submenuToggle = nav.locator('.lumen-mobile-nav__submenu-toggle').first();
      await submenuToggle.tap();
      await expect(submenuToggle).toHaveAttribute('aria-expanded', 'true');
      await expect(submenuToggle.locator('..').locator('.lumen-mobile-nav__submenu')).toBeVisible();
      await submenuToggle.tap();
      await expect(submenuToggle).toHaveAttribute('aria-expanded', 'false');
      const topLink = nav.locator('.lumen-mobile-nav__list > li > a').first();
      await topLink.scrollIntoViewIfNeeded();
      expect(await topLink.evaluate(e => {
        const r = e.getBoundingClientRect();
        const hit = document.elementFromPoint(r.x + r.width / 2, r.y + r.height / 2);
        return !!hit && (hit === e || e.contains(hit));
      })).toBe(true);

      // Keyboard close and pointer close restore banner visibility and remove
      // only the navigation's background inertness.
      await page.keyboard.press('Escape');
      await expect(nav).toBeHidden();
      await expect(banner).toBeVisible();
      await expect(toggle).toBeFocused();
      await toggle.tap();
      await toggle.tap();
      await expect(nav).toBeHidden();
      await expect(banner).toBeVisible();
      expect(await page.locator('[data-svic-nav-inert]').count()).toBe(0);

      await toggle.tap();
      const cart = nav.locator('.lumen-cart-link');
      await cart.scrollIntoViewIfNeeded();
      await cart.click({ trial: true });
      // Restore menu to top for the visual evidence, then follow a real link.
      await topLink.scrollIntoViewIfNeeded();
      if (locale === '/' && viewport.width === 393) {
        await page.screenshot({ path: testInfo.outputPath('mobile-menu-clear.png') });
      }
      const destination = await topLink.getAttribute('href');
      await topLink.tap();
      await expect(page).toHaveURL(destination!);
      await expect(page.locator('#lumen-mobile-nav')).toBeHidden();
    });
  }
}

test('desktop promotion remains visible and clickable', async ({ page, baseURL }) => {
  await page.setViewportSize({ width: 1280, height: 800 });
  await page.goto(new URL('/', baseURL).toString(), { waitUntil: 'domcontentloaded' });
  await expect(page.locator('[data-lumen-toggle]')).toBeHidden();
  await expect(page.locator('.svic-promo-bar')).toBeVisible();
  await page.locator('.svic-promo-bar__cta').click({ trial: true });
});
