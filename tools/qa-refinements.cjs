/* Non-destructive UI checks. Install Playwright in a parent/tooling directory.
   node tools/qa-refinements.cjs [base URL] [--screenshots]
   Contact POST/persistence checks are separate: qa-contact-http.py on an isolated copy. */
const {chromium}=require('playwright');
const assert=require('node:assert/strict');
const path=require('node:path');
const fs=require('node:fs');
const base=process.argv[2]||'http://127.0.0.1:8080';
const shots=process.argv.includes('--screenshots');
(async()=>{
 const browser=await chromium.launch({args:['--no-sandbox']});
 for(const width of [360,390,768,900,1024,1440]){
  const page=await browser.newPage({viewport:{width,height:1000}});
  const errors=[];page.on('pageerror',e=>errors.push(e.message));
  await page.goto(base);await page.waitForFunction(()=>document.querySelector('.hero--command').classList.contains('in-view'));
  assert.match(await page.locator('h1').innerText(),/Make time for the ideas/);
  assert.equal(await page.locator('.hero-orbit-float').count(),6);
  for(let tick=0;tick<5;tick++){
   const overlap=await page.evaluate(()=>{
    const cards=[...document.querySelectorAll('.hero-orbit-float')].map(e=>e.getBoundingClientRect());
    const copy=[...document.querySelector('.command-intro').children].map(e=>e.getBoundingClientRect());
    const dashboard=document.querySelector('.command-stage').getBoundingClientRect();
    return cards.some(a=>a.bottom>=dashboard.top||a.left<0||a.right>innerWidth||copy.some(b=>a.left<b.right&&a.right>b.left&&a.top<b.bottom&&a.bottom>b.top));
   });assert.equal(overlap,false,`social icons overlap ${width}`);await page.waitForTimeout(180);
  }
  assert.equal(await page.locator('.hero-orbit-float').first().evaluate(e=>getComputedStyle(e).animationName),'hero-social-float');
  await page.locator('[data-motion-toggle]').first().click();
  assert.equal(await page.locator('.hero-orbit-float').first().evaluate(e=>getComputedStyle(e).animationName),'none');
  await page.locator('[data-motion-toggle]').first().click();
  await page.emulateMedia({reducedMotion:'reduce'});
  await page.waitForFunction(()=>document.querySelector('[data-motion-toggle]').disabled);
  assert.equal(await page.locator('.hero-orbit-float').first().evaluate(e=>getComputedStyle(e).animationName),'none');
  await page.emulateMedia({reducedMotion:'no-preference'});
  await page.evaluate(()=>scrollTo(0,0));
  if(shots&&[390,1440].includes(width)){await page.waitForTimeout(300);await page.screenshot({path:path.resolve(__dirname,`../qa/refined-hero-${width}.jpg`),quality:85});}
  for(const route of ['/contact','/contact?subject=One-Time%20Setup','/about']){
   assert.equal((await page.goto(base+route)).status(),200);
   assert.equal(await page.evaluate(()=>document.documentElement.scrollWidth-innerWidth),0,`${route} ${width}`);
   assert.deepEqual(await page.evaluate(()=>[...document.querySelectorAll('use')].filter(u=>!document.querySelector(u.getAttribute('href'))).map(u=>u.getAttribute('href'))),[]);
   if(route==='/contact'){
    assert.equal(await page.locator('#quote-details').isVisible(),false);
    await page.locator('[data-choose-quote]').click();
    assert.equal(await page.locator('#quote-details').isVisible(),true);
    await page.locator('#organization').fill('Retained brand');
    await page.locator('#subject').selectOption('General question');
    assert.equal(await page.locator('#quote-details').isVisible(),false);
    assert.equal(await page.locator('#quote-details').evaluate(e=>e.disabled),true);
    assert.equal(await page.locator('#account_count').evaluate(e=>e.required),false);
    await page.locator('#subject').selectOption('Request a quote');
    assert.equal(await page.locator('#organization').inputValue(),'Retained brand');
    assert.equal(await page.locator('#account_count').evaluate(e=>e.required),true);
   }
   if(route.includes('?')){
    assert.equal(await page.locator('#quote-details').isVisible(),true);
    assert.equal(await page.locator('[name="platforms[]"]').count(),13);
    assert.equal(await page.locator('[name="services[]"]').count(),6);
    assert.match(await page.locator('[data-contact-submit]').innerText(),/quote/);
   }
   if(route==='/about') assert.equal(await page.locator('.about-team-functions article').count(),4);
   if(shots&&[390,1440].includes(width)&&route!='/contact'){
    await page.screenshot({path:path.resolve(__dirname,`../qa/refined-${route==='/about'?'about':'quote'}-${width}.jpg`),quality:82,fullPage:true});
   }
  }
  assert.deepEqual(errors,[]);await page.close();
  console.log(`PASS ${width}px: copy, six unobstructed floating icons, pause/reduced motion, conditional quote form, About team, assets and overflow`);
 }
 const nojs=await browser.newPage({javaScriptEnabled:false});await nojs.goto(base+'/contact');
 assert.equal(await nojs.locator('#quote-details').isVisible(),true);assert.equal(await nojs.locator('#quote-details').evaluate(e=>e.disabled),false);
 console.log('PASS no-JavaScript quote fallback');await browser.close();
})().catch(e=>{console.error(e);process.exit(1)});
