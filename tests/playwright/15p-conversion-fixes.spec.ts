import { execFileSync } from 'node:child_process';
import path from 'node:path';
import { expect, test } from '@playwright/test';

const theme = path.resolve('theme/svicloudtvbox-lumen');

for (const locale of ['en_US', 'zh_TW', 'zh_CN']) {
  test(`15P FAQ translation contains all seven real questions (${locale})`, () => {
    const code = `$data = include $argv[1]; echo json_encode($data['products']['svicloud-15p']['prelaunch']['faq'], JSON_UNESCAPED_UNICODE);`;
    const faq = JSON.parse(execFileSync('php', ['-r', code, path.join(theme, 'lang', `${locale}.php`)], { encoding: 'utf8' }));
    expect(Object.keys(faq)).toEqual(['specs', 'availability', 'policy', 'comparison', 'support', 'display_limits', 'marketplace']);
    for (const [key, entry] of Object.entries(faq) as [string, { q: string; a: string }][]) {
      expect(entry.q.trim().length, key).toBeGreaterThan(8);
      expect(entry.a.trim().length, key).toBeGreaterThan(12);
      expect(entry.q, key).not.toBe('q');
    }
  });
}

test('15P cart remove control remains accessible at mobile, tablet and desktop widths', async ({ page }) => {
  if (process.env.SVIC_LOCAL_CSS === '1') {
    await page.route(/\/assets\/css\/woocommerce\.css(?:\?|$)/, route =>
      route.fulfill({ path: path.join(theme, 'assets/css/woocommerce.css'), contentType: 'text/css' }),
    );
  }
  await page.goto('/product/svicloud-15p/', { waitUntil: 'domcontentloaded' });
  await page.locator('.single_add_to_cart_button').click();
  await page.goto('/cart/', { waitUntil: 'domcontentloaded' });
  for (const width of [390, 1100, 1280, 1440, 1600]) {
    await page.setViewportSize({ width, height: 900 });
    const item = page.locator('.lumen-cart__items');
    const summary = page.locator('.lumen-cart__summary');
    const remove = page.locator('.lumen-cart-remove');
    await expect(item).toBeVisible();
    await expect(summary).toBeVisible();
    await expect(remove).toBeVisible();
    const geometry = await page.locator('.lumen-cart__layout').evaluate(el => {
      const items = el.querySelector('.lumen-cart__items')!.getBoundingClientRect();
      const summary = el.querySelector('.lumen-cart__summary')!.getBoundingClientRect();
      const remove = el.querySelector('.lumen-cart-remove')!.getBoundingClientRect();
      const overlapping = items.left < summary.right && items.right > summary.left && items.top < summary.bottom && items.bottom > summary.top;
      const overflowing = Array.from(document.querySelectorAll('.lumen-cart__summary *'))
        .filter(node => node.getBoundingClientRect().right > innerWidth + 2)
        .slice(0, 12)
        .map(node => ({ selector: `${node.tagName.toLowerCase()}.${String(node.className).split(' ').join('.')}`, right: Math.round(node.getBoundingClientRect().right), width: Math.round(node.getBoundingClientRect().width) }));
      return { itemsBottom: items.bottom, summaryTop: summary.top, overlapping, removeRight: remove.right, viewport: innerWidth, pageWidth: document.documentElement.scrollWidth, overflowing };
    });
    if (geometry.pageWidth > geometry.viewport + 1) console.log(`${width}px overflow: ${JSON.stringify(geometry)}`);
    expect(geometry.overlapping, `${width}px cart columns overlap`).toBe(false);
    expect(geometry.pageWidth, `${width}px horizontal overflow`).toBeLessThanOrEqual(geometry.viewport + 1);
    await remove.click({ trial: true });
  }
});
