import { chromium } from 'playwright';
import fs from 'node:fs/promises';
import path from 'node:path';

const baseURL = process.env.PLAYWRIGHT_BASE_URL || 'http://svicloud10p.svic.local';
const routes = [['home','/'],['shop','/shop/'],['compare','/compare/'],['product-15p','/product/svicloud-15p/'],['product-10p','/product/svicloud-10p-plus/'],['product-9p','/product/svicloud-9p/'],['cart','/cart/'],['checkout','/checkout/']];
const browser = await chromium.launch();
const report = { baseURL, auditedAt: new Date().toISOString(), pages: [] };
for (const [name, route] of routes) {
  const context = await browser.newContext({ viewport: { width: 390, height: 844 } });
  const page = await context.newPage();
  if (name === 'cart' || name === 'checkout') await page.goto(`${baseURL}/?add-to-cart=12`, { waitUntil: 'domcontentloaded' });
  await page.goto(`${baseURL}${route}`, { waitUntil: 'domcontentloaded' });
  await page.waitForTimeout(250);
  const failures = await page.evaluate(() => {
    const parse = (value) => {
      const match = value.match(/rgba?\(([^)]+)\)/);
      if (!match) return [0,0,0,0];
      const parts = match[1].split(/[ ,/]+/).map(Number);
      return [parts[0] || 0, parts[1] || 0, parts[2] || 0, Number.isFinite(parts[3]) ? parts[3] : 1];
    };
    const blend = (front, back) => {
      const alpha = front[3] + back[3] * (1 - front[3]);
      if (!alpha) return [0,0,0,0];
      return [0,1,2].map((index) => (front[index] * front[3] + back[index] * back[3] * (1 - front[3])) / alpha).concat(alpha);
    };
    const luminance = (color) => {
      const channels = color.slice(0,3).map((value) => { const channel=value/255; return channel<=0.03928?channel/12.92:Math.pow((channel+0.055)/1.055,2.4); });
      return 0.2126*channels[0]+0.7152*channels[1]+0.0722*channels[2];
    };
    const ratio = (a,b) => { const l1=luminance(a),l2=luminance(b); return (Math.max(l1,l2)+0.05)/(Math.min(l1,l2)+0.05); };
    const selector = (element) => element.id ? `#${CSS.escape(element.id)}` : `${element.tagName.toLowerCase()}${typeof element.className==='string' ? element.className.trim().split(/\s+/).slice(0,3).map((item)=>`.${CSS.escape(item)}`).join('') : ''}`;
    const visible = (element) => { const style=getComputedStyle(element),rect=element.getBoundingClientRect();return style.display!=='none'&&style.visibility!=='hidden'&&rect.width>0&&rect.height>0; };
    const background = (element) => {
      const chain=[]; for(let current=element;current;current=current.parentElement) chain.unshift(current);
      return {
        color: chain.reduce((color,current)=>blend(parse(getComputedStyle(current).backgroundColor),color),[3,10,26,1]),
        complex: chain.some((current)=>getComputedStyle(current).backgroundImage !== 'none'),
      };
    };
    const rows=[];
    for (const element of document.querySelectorAll('body *')) {
      if (!visible(element)) continue;
      const direct=[...element.childNodes].filter((node)=>node.nodeType===Node.TEXT_NODE).map((node)=>node.textContent.trim()).filter(Boolean).join(' ');
      if (!direct) continue;
      const style=getComputedStyle(element),backgroundResult=background(element),fontSize=parseFloat(style.fontSize),fontWeight=parseInt(style.fontWeight,10)||400;
      const parsedColor=parse(style.color); if (backgroundResult.complex || parsedColor[3] === 0) continue;
      const bg=backgroundResult.color,fg=blend(parsedColor,bg);
      const large=fontSize>=24||(fontSize>=18.66&&fontWeight>=700),required=large?3:4.5,contrast=ratio(fg,bg);
      if (contrast+0.05<required) rows.push({selector:selector(element),text:direct.slice(0,100),fontSize:Math.round(fontSize*10)/10,fontWeight,contrast:Math.round(contrast*100)/100,required});
    }
    return rows.sort((a,b)=>a.contrast-b.contrast).slice(0,80);
  });
  report.pages.push({ name, route, failures });
  await context.close();
}
await browser.close();
const output=path.resolve('docs/frontend-audit/contrast-audit.json');
await fs.writeFile(output,`${JSON.stringify(report,null,2)}\n`);
console.log(`Contrast audit wrote ${output}`);
