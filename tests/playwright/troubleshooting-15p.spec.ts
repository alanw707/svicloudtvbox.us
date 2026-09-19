import { test, expect } from '@playwright/test';
import { execFileSync } from 'node:child_process';
import path from 'node:path';

for (const [locale, prefix, heading] of [
  ['en_US', '', 'SVICLOUD 15P troubleshooting'],
  ['zh_TW', '/zh', 'SVICLOUD 15P 疑難排解'],
  ['zh_CN', '/zh-cn', 'SVICLOUD 15P 疑难排解'],
]) {
  test(`15P troubleshooting ${locale}`, async ({ page }, testInfo) => {
    const errors: string[] = [];
    page.on('pageerror', e => errors.push(e.message));
    // Isolate guide behavior from the third-party Translate widget's WebKit frame errors.
    await page.route(/translate\.google\.com|translate\.googleapis\.com|www\.google\.com/, r => r.abort());
    await page.route(/google-analytics\.com|googletagmanager\.com|googleadservices\.com|doubleclick\.net/, r => r.abort());
    if (process.env.SVIC_LOCAL_GUIDE === '1') {
      const main = execFileSync('php', ['tests/fixtures/render-guide.php', locale], { encoding: 'utf8' });
      await page.route(`**${prefix}/guides-troubleshooting/`, async route => {
        const response = await route.fetch();
        const body = (await response.text()).replace(/<main\b[\s\S]*?<\/main>/, main);
        await route.fulfill({ response, body });
      });
      await page.route(/\/assets\/css\/guides\.css(?:\?|$)/, r => r.fulfill({ path: path.resolve('theme/svicloudtvbox-lumen/assets/css/guides.css'), contentType: 'text/css' }));
    }
    await page.goto(`${prefix}/guides-troubleshooting/`, { waitUntil: 'domcontentloaded' });
    await expect(page.locator('main h1')).toHaveText(heading);
    const hub = page.locator('.guides-troubleshooting');
    const details = hub.locator('details');
    await expect(details).toHaveCount(11);
    await expect(page.locator('main .guides-inline-cta, main .guides-support--detail-cta')).toHaveCount(0);
    await expect(hub).toContainText('10P+');
    await expect(hub.locator('a[href="mailto:support@svicloudtvbox.us"]')).toBeVisible();
    await expect(hub.locator(`a[href="https://svicloudtvbox.us${prefix}/contact/"]`)).toBeVisible();
    await details.first().locator('summary').focus();
    await page.keyboard.press('Enter');
    await expect(details.first()).toHaveAttribute('open', '');
    await expect(details.first().locator('li')).toHaveCount(3);
    await page.keyboard.press('Enter');
    await expect(details.first()).not.toHaveAttribute('open', '');
    for (let i = 0; i < 11; i++) {
      await details.nth(i).locator('summary').click();
      await expect(details.nth(i).locator('ol')).toBeVisible();
      await expect(details.nth(i).locator('li')).toHaveCount(3);
    }
    const layout = await hub.evaluate(el => {
      const rect = el.getBoundingClientRect();
      const steps = getComputedStyle(el.querySelector('.guides-troubleshooting__steps')!);
      return { left: rect.left, right: rect.right, width: innerWidth, overflow: document.documentElement.scrollWidth > innerWidth, color: steps.color, font: parseFloat(steps.fontSize) };
    });
    expect(layout.left).toBeGreaterThanOrEqual(0);
    expect(layout.right).toBeLessThanOrEqual(layout.width + 1);
    expect(layout.overflow).toBe(false);
    expect(layout.color).toBe('rgb(51, 65, 85)');
    expect(layout.font).toBeGreaterThanOrEqual(16);
    if (locale === 'en_US') {
      await expect(hub).not.toContainText('VOL-');
      await expect(hub).not.toContainText('firmware is already current');
      await expect(hub).toContainText('does not prove');
      await expect(hub).toContainText('Factory reset erases');
    }
    await page.screenshot({ path: testInfo.outputPath('expanded.png'), fullPage: true });
    await details.evaluateAll(nodes => nodes.forEach(el => el.removeAttribute('open')));
    await page.locator('main h1').scrollIntoViewIfNeeded();
    await page.screenshot({ path: testInfo.outputPath('collapsed.png'), fullPage: true });
    expect(errors).toEqual([]);
  });
}
