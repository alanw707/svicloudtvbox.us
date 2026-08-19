import { test, expect } from '@playwright/test';

const locales = [
  { prefix: '', copy: 'Everything in 10P+, plus support for downloading mobile apps.', mobileApps: 'Supports downloading mobile apps', bestFor: 'Family rooms, sports fans, and mobile app users' },
  { prefix: '/zh', copy: '具備 10P+ 的全部功能，並支援下載手機 App。', mobileApps: '支援下載手機 App', bestFor: '家庭客廳、體育迷與手機 App 使用者' },
  { prefix: '/zh-cn', copy: '具备 10P+ 的全部功能，并支持下载手机 App。', mobileApps: '支持下载手机 App', bestFor: '家庭客厅、体育迷与手机 App 用户' },
];

test.describe('Shop 15P localized description', () => {
  for (const locale of locales) {
    test(`shows the approved 15P description ${locale.prefix || '/'} locale`, async ({ page }) => {
      await page.goto(`${locale.prefix}/shop/`, { waitUntil: 'domcontentloaded' });
      const card = page.locator('.shop-product-card').filter({ has: page.locator('.shop-product-card__title') }).first();
      await expect(card.locator('.shop-product-card__lead')).toHaveText(locale.copy);
      await expect(card.locator('.shop-product-card__features')).toContainText(locale.mobileApps);
      await expect(card.locator('.shop-product-card__best-for')).toContainText(locale.bestFor);
    });
  }
});
