/* Browser auth/security integration. ISOLATED local copy only: writes test account/reset.
 node tools/qa-security-browser.cjs http://127.0.0.1:8081 /absolute/isolated/project
 Never point at production. No real external service is called. */
const {chromium}=require('playwright');const assert=require('node:assert/strict');const fs=require('node:fs');const path=require('node:path');const {execFileSync}=require('node:child_process');
const base=process.argv[2],copy=path.resolve(process.argv[3]||'');
assert(['127.0.0.1','localhost'].includes(new URL(base).hostname));assert(fs.existsSync(path.join(copy,'.contact-qa-isolated')));assert.notEqual(copy,path.resolve(__dirname,'..'));
function php(code){return execFileSync('php',['-r',`require ${JSON.stringify(path.join(copy,'src/bootstrap.php'))}; ${code}`],{encoding:'utf8',env:{...process.env,APP_ENV:'test',DATABASE_DSN:'sqlite:'+path.join(copy,'storage/linkeasy.sqlite')}}).trim();}
(async()=>{
 const browser=await chromium.launch({args:['--no-sandbox']});const context=await browser.newContext();const page=await context.newPage();const errors=[];
 page.on('pageerror',e=>errors.push(e.message));page.on('console',m=>{if(m.type()==='error'&&!m.text().includes('status of 4'))errors.push(m.text());});
 let res=await page.goto(base+'/');let headers=res.headers();assert.match(headers['content-security-policy'],/script-src 'self' 'nonce-/);assert.match(headers['cache-control'],/no-store/);assert.equal(headers['referrer-policy'],'no-referrer');assert.equal(headers['x-content-type-options'],'nosniff');assert(!headers['access-control-allow-origin']);assert(!headers['x-powered-by']);
 for(const route of ['/.env','/.user.ini','/../src/bootstrap.php','/%2e%2e/.env','/tools/worker.php','/storage/linkeasy.sqlite','/public/router.php'])assert.equal((await page.request.get(base+route)).status(),404,route);
 assert.equal((await page.request.get(base+'/login?next[]=x')).status(),400);
 assert.equal((await page.request.post(base+'/signup',{form:{_csrf:'',name:'Name'}})).status(),419);
 assert.equal((await page.request.post(base+'/signup',{form:{'name[]':'invalid'}})).status(),400);
 assert.equal((await page.request.post(base+'/contact',{data:'x'.repeat(33000)})).status(),413);
 assert.equal((await page.request.post(base+'/logout',{form:{_csrf:'bad'}})).status(),419);
 assert.equal((await page.request.post(base+'/billing/paypal/create',{form:{_csrf:'bad'}})).status(),419);
 // No side effects from unconfigured checkout GET (redirects to login for guests).
 await page.goto(base+'/billing/paypal/create');assert.equal(new URL(page.url()).pathname,'/login');
 const email=`browser-${Date.now()}@example.test`;const password='Browser test password 123';
 await page.goto(base+'/signup');await page.locator('#name').fill('Browser QA');await page.locator('#email').fill(email);await page.locator('#password').fill(password);await page.locator('#password_confirm').fill(password);
 await page.locator('button[type=submit]').click();await page.waitForLoadState();assert.match(await page.locator('body').innerText(),/Please read and accept the terms/);
 const before=(await context.cookies()).find(c=>c.name==='les_session').value;
 await page.locator('#password').fill(password);await page.locator('#password_confirm').fill(password);await page.locator('[name=terms]').check();await page.locator('button[type=submit]').click();await page.waitForURL('**/dashboard');
 const after=(await context.cookies()).find(c=>c.name==='les_session');assert.notEqual(before,after.value);assert(after.httpOnly);assert.equal(after.sameSite,'Lax');
 assert.match(await page.locator('body').innerText(),/free/i);
 // Separate logged-in session must be invalidated by the later password reset.
 const second=await browser.newContext();const p2=await second.newPage();await p2.goto(base+'/login');await p2.locator('#email').fill(email);await p2.locator('#password').fill(password);await p2.locator('button[type=submit]').click();await p2.waitForURL('**/dashboard');
 await page.goto(base+'/forgot-password');await page.locator('#email').fill(email);await page.locator('button[type=submit]').click();await page.waitForLoadState();
 const row=JSON.parse(php("echo App\\Database::pdo()->query(\"SELECT payload FROM les_mail_jobs WHERE dedupe_key LIKE 'reset:%' ORDER BY id DESC LIMIT 1\")->fetchColumn();"));
 const token=row.text.match(/token=([a-f0-9]{64})/)[1];
 const resetUrl=base+'/reset-password?token='+token+'&email='+encodeURIComponent(email);await page.goto(resetUrl);await page.locator('#password').fill('Changed browser password 456');await page.locator('#password_confirm').fill('Changed browser password 456');await page.locator('button[type=submit]').click();await page.waitForURL('**/login');
 await p2.goto(base+'/dashboard');assert.equal(new URL(p2.url()).pathname,'/login');
 await page.goto(resetUrl);assert.equal(new URL(page.url()).pathname,'/forgot-password');
 await page.goto(base+'/login?next=/%5cevil.example');await page.locator('#email').fill(email);await page.locator('#password').fill('Changed browser password 456');await page.locator('button[type=submit]').click();await page.waitForURL('**/dashboard');assert.equal(new URL(page.url()).origin,base);
 // Authenticated session check cannot keep idle sessions alive forever.
 const sessionCookie=(await context.cookies()).find(c=>c.name==='les_session').value;
 php(`session_id(${JSON.stringify(sessionCookie)});les_start_session();$_SESSION['_last_activity']=time()-3601;session_write_close();`);
 const expired=await page.request.get(base+'/auth/session-check');assert.equal((await expired.json()).authenticated,false);
 assert.deepEqual(errors,[]);
 console.log('PASS browser security/auth: CSP, no-store, private paths, malformed/oversized input, CSRF, signup terms, session rotation, password reset, second-session invalidation, redirect guards and idle expiry.');
 await browser.close();
})().catch(e=>{console.error(e);process.exit(1)});
