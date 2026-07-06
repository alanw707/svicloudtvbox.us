import { expect, test } from '@playwright/test';

function rgbToContrastColor(rgb: string): [number, number, number] {
  const match = rgb.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)/);
  if (!match) {
    throw new Error(`Unsupported color format: ${rgb}`);
  }
  return [Number(match[1]), Number(match[2]), Number(match[3])];
}

function luminance([r, g, b]: [number, number, number]): number {
  const values = [r, g, b].map((channel) => {
    const normalized = channel / 255;
    return normalized <= 0.03928
      ? normalized / 12.92
      : Math.pow((normalized + 0.055) / 1.055, 2.4);
  });

  return (0.2126 * values[0]) + (0.7152 * values[1]) + (0.0722 * values[2]);
}

function contrastRatio(foreground: string, background: string): number {
  const foregroundLuminance = luminance(rgbToContrastColor(foreground));
  const backgroundLuminance = luminance(rgbToContrastColor(background));
  const lighter = Math.max(foregroundLuminance, backgroundLuminance);
  const darker = Math.min(foregroundLuminance, backgroundLuminance);

  return (lighter + 0.05) / (darker + 0.05);
}

test.describe('guide detail readability', () => {
  test('troubleshooting answer hub has readable text on a real panel', async ({ page, baseURL }) => {
    await page.goto(new URL('/guides-troubleshooting/', baseURL).toString(), { waitUntil: 'domcontentloaded' });

    const answerHub = page.locator('.guides-answer-hub').first();
    await expect(answerHub).toBeVisible();

    const styles = await answerHub.evaluate((el) => {
      const hub = window.getComputedStyle(el);
      const heading = window.getComputedStyle(el.querySelector('h2') as HTMLElement);
      const paragraph = window.getComputedStyle(el.querySelector('p') as HTMLElement);
      const faq = window.getComputedStyle(el.querySelector('details') as HTMLElement);

      return {
        hubBackground: hub.backgroundColor,
        h2Color: heading.color,
        paragraphColor: paragraph.color,
        faqBackground: faq.backgroundColor,
      };
    });

    expect(styles.hubBackground).not.toBe('rgba(0, 0, 0, 0)');
    expect(contrastRatio(styles.h2Color, styles.hubBackground)).toBeGreaterThanOrEqual(4.5);
    expect(contrastRatio(styles.paragraphColor, styles.hubBackground)).toBeGreaterThanOrEqual(4.5);
    expect(styles.faqBackground).not.toBe('rgba(0, 0, 0, 0)');
  });

  test('decision page quick recommendation renders on a visible card surface', async ({ page, baseURL }) => {
    await page.goto(new URL('/best-svicloud-box-for-chinese-tv-usa/', baseURL).toString(), { waitUntil: 'domcontentloaded' });

    const answerHub = page.locator('.guides-answer-hub').first();
    await expect(answerHub).toBeVisible();

    const styles = await answerHub.evaluate((el) => {
      const hub = window.getComputedStyle(el);
      const heading = window.getComputedStyle(el.querySelector('h2') as HTMLElement);
      const listItem = window.getComputedStyle(el.querySelector('li') as HTMLElement);

      return {
        hubBackground: hub.backgroundColor,
        hubBackgroundImage: hub.backgroundImage,
        headingColor: heading.color,
        listItemColor: listItem.color,
      };
    });

    expect(styles.hubBackground !== 'rgba(0, 0, 0, 0)' || styles.hubBackgroundImage !== 'none').toBeTruthy();
    expect(contrastRatio(styles.headingColor, styles.hubBackground)).toBeGreaterThanOrEqual(4.5);
    expect(contrastRatio(styles.listItemColor, styles.hubBackground)).toBeGreaterThanOrEqual(4.5);
  });
});
