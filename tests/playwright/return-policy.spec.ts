import { test, expect } from '@playwright/test';
import { execFileSync } from 'node:child_process';

for (const [locale, prefix, title, exclusion] of [
  ['en_US', '', 'Hardware warranty & customer-caused damage', 'not eligible for a refund'],
  ['zh_TW', '/zh', '硬體保固與人為損壞', '恕不退款'],
  ['zh_CN', '/zh-cn', '硬件保修与人为损坏', '恕不退款'],
]) {
  test(`damage exclusion ${locale}`, async ({ page }, testInfo) => {
    await page.route(/translate\.google\.com|translate\.googleapis\.com|www\.google\.com|google-analytics\.com|googletagmanager\.com|doubleclick\.net/, r => r.abort());
    if (process.env.SVIC_LOCAL_POLICY === '1') {
      const main = execFileSync('php', ['tests/fixtures/render-return-policy.php', locale], { encoding: 'utf8' });
      await page.route(`**${prefix}/return-policy/`, async route => {
        const response = await route.fetch();
        const body = (await response.text()).replace(/<main\b[\s\S]*?<\/main>/, main);
        await route.fulfill({ response, body });
      });
    }
    await page.goto(`${prefix}/return-policy/`, { waitUntil: 'domcontentloaded' });
    const section = page.locator('#policy-section-damage');
    await expect(section.locator('h2')).toHaveText(title);
    await expect(section.locator('li')).toHaveCount(3);
    await expect(section).toContainText(exclusion);
    await expect(page.locator('#policy-section-eligibility')).toContainText('14');
    await expect(page.locator('#policy-section-shipping')).toContainText('10%');
    await expect(page.locator('main')).not.toContainText('return_policy.sections');
    if (locale === 'en_US') {
      await expect(section).toContainText('manufacturing defects under normal use');
      await expect(section).toContainText('not eligible for a refund, free repair, or replacement');
      await expect(section).toContainText('except where required by law');
      await expect(section).not.toContainText('flaps');
    }
    await section.scrollIntoViewIfNeeded();
    const bounds = await section.evaluate(el => { const b = el.getBoundingClientRect(); return {left:b.left,right:b.right,width:innerWidth}; });
    expect(bounds.left).toBeGreaterThanOrEqual(0);
    expect(bounds.right).toBeLessThanOrEqual(bounds.width + 1);
    await page.screenshot({ path: testInfo.outputPath('policy.png'), fullPage: true });
  });
}
