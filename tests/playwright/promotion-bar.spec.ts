import { test, expect } from '@playwright/test';

test.describe('Google 10P promotion bar', () => {
  test('shows the active GOOGLE5 offer on the public homepage', async ({ page, baseURL }) => {
    const url = new URL('/', baseURL);
    url.searchParams.set('cb', Date.now().toString());

    const resp = await page.goto(url.toString(), { waitUntil: 'domcontentloaded' });
    expect(resp?.ok()).toBeTruthy();

    const bar = page.locator('.svic-promo-bar');
    await expect(bar).toBeVisible();
    await expect(bar).toContainText('GOOGLE5');
    await expect(bar).toContainText('SVICLOUD 10P+ saves 5%');
    await expect(bar).toContainText('10P+ only code GOOGLE5');
    await expect(bar.locator('.svic-promo-bar__cta')).toHaveAttribute('href', /\/product\/svicloud-10p-plus\/?$/);
  });
});
