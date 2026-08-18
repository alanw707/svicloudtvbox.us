#!/usr/bin/env node
/** Verify every locally imported product permalink returns HTTP 200. */
import { execFileSync } from 'node:child_process';
import process from 'node:process';
import { chromium } from 'playwright';

const baseUrl = (process.env.PLAYWRIGHT_BASE_URL || 'http://svicloud10p.svic.local').replace(/\/$/, '');
const products = JSON.parse(execFileSync('docker', [
  'exec', 'svicloud10p-wp', 'wp', 'eval',
  'echo wp_json_encode(array_map(static fn($post) => array("slug" => $post->post_name, "url" => get_permalink($post)), get_posts(array("post_type" => "product", "post_status" => "publish", "numberposts" => -1))));',
  '--allow-root',
], { encoding: 'utf8' }));

const browser = await chromium.launch({ headless: true });
const page = await browser.newPage();
let failed = false;
for (const product of products) {
  const slug = String(product.slug || '');
  const localPath = new URL(String(product.url || ''), baseUrl).pathname;
  const response = await page.goto(`${baseUrl}${localPath}`, { waitUntil: 'domcontentloaded' });
  const status = response?.status() ?? 0;
  console.log(`${slug}: ${status}`);
  if (status !== 200) {
    failed = true;
  }
}
await browser.close();
process.exitCode = failed ? 1 : 0;
