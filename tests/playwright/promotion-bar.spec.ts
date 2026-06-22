import { test, expect } from '@playwright/test';

test.describe('Father\'s Day promotion bar', () => {
  test('is no longer shown on the public homepage', async ({ page, baseURL }) => {
    const url = new URL('/', baseURL);
    url.searchParams.set('cb', Date.now().toString());

    const resp = await page.goto(url.toString(), { waitUntil: 'domcontentloaded' });
    expect(resp?.ok()).toBeTruthy();

    const bar = page.locator('.svic-promo-bar');
    await expect(bar).toHaveCount(0);
    await expect(page.locator('body')).not.toContainText('DAD2026');
  });
});
