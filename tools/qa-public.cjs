/* Run with Playwright installed: node tools/qa-public.cjs [base URL]
   Non-destructive public-page checks; does not call live billing APIs. */
const { chromium } = require('playwright');
const assert = require('node:assert/strict');
const base = process.argv[2] || 'http://127.0.0.1:8080';
(async () => {
 const browser = await chromium.launch({headless:true,args:['--no-sandbox']});
 const routes = ['/about','/contact','/help','/legal','/privacy','/terms','/cookies','/data-deletion','/copyright','/refunds','/security','/disclosures','/acceptable-use'];
 for (const width of [360,390,768,1024,1440]) {
  const page = await browser.newPage({viewport:{width,height:900}});
  const errors=[];page.on('pageerror',e=>errors.push(e.message));
  await page.goto(base+'/');
  for(const selector of ['#features','.creation-flow','.publish-diagram','[data-timeline]','.ba-grid','#pricing']) {
   await page.locator(selector).scrollIntoViewIfNeeded();await page.waitForTimeout(500);
  }
  assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth-innerWidth),0,`overflow ${width}`);
  assert.deepEqual(await page.evaluate(()=>[...document.querySelectorAll('use')].filter(u=>!document.querySelector(u.getAttribute('href'))).map(u=>u.getAttribute('href'))),[]);
  assert.deepEqual(await page.evaluate(()=>[...document.images].filter(i=>i.complete&&!i.naturalWidth).map(i=>i.src)),[]);
  await page.locator('[data-billing="yearly"]').click();
  assert.equal(await page.locator('[data-billing="yearly"]').getAttribute('aria-pressed'),'true');
  assert.match(await page.locator('.js-managed-cta').getAttribute('href'),/cycle=yearly/);
  assert.equal(await page.locator('[data-yearly-note]').evaluate(e=>getComputedStyle(e).visibility),'visible');
  await page.locator('[data-billing="monthly"]').click();
  assert.match(await page.locator('.js-managed-cta').getAttribute('href'),/cycle=monthly/);
  assert.equal(await page.locator('[data-yearly-note]').evaluate(e=>getComputedStyle(e).visibility),'hidden');
  const byok=await page.locator('[data-plan="opensource"] .price-cta').getAttribute('href');
  await page.goto(base+byok);
  assert.equal(await page.locator('#subject').inputValue(),'Bring Your Own API');
  if(width<=960){
   await page.locator('.nav-toggle').click();assert.equal(await page.locator('#mobileNav').isVisible(),true);
   await page.keyboard.press('Escape');assert.equal(await page.locator('#mobileNav').isVisible(),false);
   await page.locator('.nav-toggle').click();await page.locator('#mobileNav a[href="/about"]').click();assert.equal(new URL(page.url()).pathname,'/about');
  }
  if(width===360||width===1440) for(const route of routes) {
   const res=await page.goto(base+route);assert.equal(res.status(),200,route);
   assert.equal(await page.locator('#siteHeader').count(),1,route);
   assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth-innerWidth),0,route);
   assert.deepEqual(await page.evaluate(()=>[...document.querySelectorAll('use')].filter(u=>!document.querySelector(u.getAttribute('href'))).map(u=>u.getAttribute('href'))),[],route);
  }
  assert.deepEqual(errors,[],`console ${width}`);
  console.log(`PASS ${width}px: layouts, assets, symbols, navigation and pricing`);
  await page.close();
 }
 const page=await browser.newPage();await page.goto(base+'/');
 const links=await page.locator('a[href]').evaluateAll(els=>els.map(e=>e.getAttribute('href')).filter(h=>h.startsWith('/#')||h.startsWith('#')));
 for(const link of links){const id=link.split('#')[1];assert(id&&await page.locator(`[id="${id}"]`).count(),`missing anchor ${link}`);}
 const sitemap=await page.request.get(base+'/sitemap.xml');assert.equal(sitemap.status(),200);assert((await sitemap.text()).includes('/about'));
 for(const route of ['/.env','/src/bootstrap.php','/config/app.php','/not-a-real-page']) assert.equal((await page.request.get(base+route)).status(),404);
 for(const [route,end] of [['/dmca','/copyright'],['/privacy-policy','/privacy'],['/terms-of-service','/terms']]) {await page.goto(base+route);assert.equal(new URL(page.url()).pathname,end);}
 console.log('PASS internal anchors, sitemap, aliases and private-path checks');
 await browser.close();
})().catch(e=>{console.error(e);process.exit(1)});
