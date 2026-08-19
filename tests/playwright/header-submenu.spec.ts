import { test, expect } from '@playwright/test';

test('keeps desktop submenu open while moving into its first item', async ({ page }) => {
  await page.setViewportSize({ width: 1512, height: 950 });
  await page.goto('/?submenu_verify=20260819', { waitUntil: 'domcontentloaded' });

  const parent = page.locator('.lumen-nav__list .menu-item-has-children').first();
  const submenu = parent.locator(':scope > .sub-menu');
  await parent.hover();
  await page.waitForTimeout(500);

  const boxes = await parent.evaluate((element) => {
    const link = element.querySelector(':scope > a')!.getBoundingClientRect();
    const menu = element.querySelector(':scope > .sub-menu')!;
    const menuRect = menu.getBoundingClientRect();
    const itemRect = menu.querySelector('a')!.getBoundingClientRect();
    return {
      start: { x: link.left + link.width / 2, y: link.bottom - 2 },
      target: { x: itemRect.left + itemRect.width / 2, y: itemRect.top + itemRect.height / 2 },
      gap: menuRect.top - link.bottom,
    };
  });

  await page.mouse.move(boxes.start.x, boxes.start.y);
  await page.mouse.move(boxes.target.x, boxes.target.y, { steps: 20 });
  await expect.poll(async () => submenu.evaluate((element) => {
    const style = getComputedStyle(element);
    return style.opacity !== '0' && style.pointerEvents !== 'none';
  })).toBe(true);
});
