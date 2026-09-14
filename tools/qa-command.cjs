const {chromium}=require('playwright');
const assert=require('node:assert/strict');
const base=process.argv[2]||'http://127.0.0.1:8080';
(async()=>{
 const browser=await chromium.launch({args:['--no-sandbox']});
 for(const width of [360,390,768,1024,1440]){
  const page=await browser.newPage({viewport:{width,height:900}});const errors=[];page.on('pageerror',e=>errors.push(e.message));
  await page.goto(base);await page.waitForTimeout(300);
  const initial=(await page.locator('#siteHeader').boundingBox()).width;
  await page.evaluate(()=>scrollTo(0,350));await page.waitForTimeout(500);
  assert((await page.locator('#siteHeader').boundingBox()).width<initial);
  const original=await page.locator('.command-chart-line').getAttribute('d');
  await page.locator('[data-command-metric=engagement]').click();
  assert.notEqual(await page.locator('.command-chart-line').getAttribute('d'),original);
  assert.equal(await page.locator('[data-command-series]').textContent(),'Engagement');
  await page.keyboard.press('ArrowRight');assert.equal(await page.locator('[data-command-metric=audience]').getAttribute('aria-selected'),'true');
  await page.keyboard.press('Home');assert.equal(await page.locator('.command-chart-line').getAttribute('d'),original);
  await page.locator('[data-motion-toggle]').first().click();await page.reload();assert(await page.locator('body').evaluate(e=>e.classList.contains('motion-paused')));
  await page.locator('[data-motion-toggle]').first().click();
  await page.locator('.flow-map').scrollIntoViewIfNeeded();await page.waitForTimeout(500);
  assert.equal(await page.locator('.flow-node').count(),6);
  const boxes=await page.locator('.flow-node,.flow-center').evaluateAll(es=>es.map(e=>{const r=e.getBoundingClientRect();return {left:r.left,right:r.right,top:r.top,bottom:r.bottom}}));
  for(let i=0;i<boxes.length;i++)for(let j=i+1;j<boxes.length;j++){const a=boxes[i],b=boxes[j];assert(!(a.left<b.right&&a.right>b.left&&a.top<b.bottom&&a.bottom>b.top),'Flow nodes overlap');}
  assert.equal(await page.locator('.hero--command .motion-glyph').first().evaluate(e=>getComputedStyle(e).animationPlayState),'paused');
  const sections=await page.locator('.motion-section').count();
  assert.equal(sections,await page.evaluate(()=>[...document.querySelectorAll('.motion-section')].filter(s=>s.querySelector('.motion-glyph,.section-cue')).length));
  assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth-innerWidth),0);
  await page.emulateMedia({reducedMotion:'reduce'});await page.waitForFunction(()=>document.querySelector('[data-motion-toggle]').disabled);
  assert.equal(await page.locator('.flow-center-halo').evaluate(e=>getComputedStyle(e).animationName),'none');
  assert.deepEqual(errors,[]);
  console.log(`PASS ${width}px: vertical hero, metric tabs, keyboard, non-overlapping flow, header contraction and motion preferences`);
  await page.close();
 }
 await browser.close();
})().catch(e=>{console.error(e);process.exit(1)});
