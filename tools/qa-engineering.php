<?php
/** Isolated engineering unit/integration tests. No real provider traffic or mail delivery. */
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
$testFile=sys_get_temp_dir().'/les-engineering-'.bin2hex(random_bytes(6)).'.sqlite';
$mysql=getenv('LES_TEST_MYSQL_DSN');
if($mysql && (!str_contains($mysql,'dbname=les_test') || getenv('LES_TEST_ALLOW_MYSQL')!=='1'))die("Refusing non-test MySQL.\n");
putenv('DATABASE_DSN='.($mysql?:'sqlite:'.$testFile));putenv('APP_ENV=test');putenv('MAIL_LOG_CONTENT=false');
require dirname(__DIR__).'/src/bootstrap.php';
use App\Database as DB;use App\Auth;use App\Request;use App\RateLimiter;use App\Services\MailQueue;use App\Services\PasswordReset;use App\Services\ContactService;use App\Services\HttpClient;use App\Services\CircuitBreaker;use App\Services\GoogleOAuth;use App\Services\PayPal;
$count=0;
function check(bool $ok,string $label): void {global $count;if(!$ok)throw new RuntimeException('FAILED: '.$label);$count++;echo "PASS $label\n";}
function throws(callable $fn,string $label): void {try{$fn();}catch(Throwable $e){check(true,$label);return;}check(false,$label);}
try{
 $pdo=DB::pdo();App\Migrations::run($pdo);
 check(count(App\Migrations::run($pdo))===0,'migrations repeat without changes');
 foreach(['//evil.test','/\\evil.test','/%5cevil.test','/%2f/evil.test',"/\r\nInjected:1",['/dashboard']] as $next)check(Request::next($next)==='/dashboard','unsafe redirect rejected');
 check(Request::next('/dashboard?tab=posts')==='/dashboard?tab=posts','same-site destination retained');
 $_SERVER['REMOTE_ADDR']='198.51.100.9';$_SERVER['HTTP_X_FORWARDED_PROTO']='https';putenv('TRUSTED_PROXY_IPS=');check(!App\Security::https(),'spoofed proxy TLS header ignored');
 putenv('TRUSTED_PROXY_IPS=198.51.100.9');check(App\Security::https(),'explicit trusted TLS proxy supported');$_SERVER['HTTP_X_FORWARDED_FOR']='1.2.3.4, 203.0.113.9';check(Request::clientIp()==='203.0.113.9','trusted proxy peels chain from the right');putenv('TRUSTED_PROXY_IPS=');check(Request::clientIp()==='198.51.100.9','untrusted forwarded client IP ignored');
 $u=Auth::register('QA Person','qa@example.test','Correct horse 123');
 check($u['plan']==='free','new account is Free');
 check((int)$pdo->query('SELECT COUNT(*) FROM les_subscriptions')->fetchColumn()===1,'registration includes subscription');
 check(Auth::attempt('qa@example.test','Correct horse 123'),'correct password accepted');check(!Auth::attempt('qa@example.test','incorrect'),'incorrect password denied');
 $hash=$pdo->query('SELECT password_hash FROM les_users LIMIT 1')->fetchColumn();check($hash!=='Correct horse 123' && password_verify('Correct horse 123',$hash),'salted adaptive password hash');
 check(!Auth::validPassword(str_repeat('x',73)),'password length bounded');
 throws(fn()=>Auth::register('QA Duplicate','qa@example.test','Password 123'),'duplicate registration rejected');
 check((int)$pdo->query('SELECT COUNT(*) FROM les_users')->fetchColumn()===1,'duplicate transaction leaves no partial account');
 check(!App\PlanGate::allows($u,'misspelled_gate',0),'unknown plan permission fails closed');
 check(!App\PlanGate::owns($u,['user_id'=>$u['id']+1]),'cross-user ownership denied');
 check(App\PlanGate::allows($u,'max_social_accounts',2)&&!App\PlanGate::allows($u,'max_social_accounts',3),'Free account limit enforced');
 $oldVersion=$_SESSION['_auth_version'];PasswordReset::request('qa@example.test');
 $payload=json_decode($pdo->query("SELECT payload FROM les_mail_jobs WHERE dedupe_key LIKE 'reset:%' ORDER BY id DESC LIMIT 1")->fetchColumn(),true);
 preg_match('/token=([a-f0-9]{64})/',$payload['text'],$matches);$token=$matches[1];
 check(PasswordReset::valid($token,'qa@example.test'),'reset token resolves by stored hash');
 check(PasswordReset::reset($token,'qa@example.test','New password 456'),'reset commits token use and password change');
 check(!PasswordReset::reset($token,'qa@example.test','Another password 789'),'reset cannot replay');
 check((int)$pdo->query('SELECT auth_version FROM les_users LIMIT 1')->fetchColumn()===$oldVersion+1,'reset invalidates prior sessions by auth version');
 check(!Auth::check(),'old session rejected after password reset');
 throws(fn()=>Auth::loginOrCreateFromProvider('google','123','qa@example.test','QA Person'),'OAuth cannot auto-link existing email');
 $provider=Auth::loginOrCreateFromProvider('google','456','provider@example.test','Provider Test');
 check($provider['id']===Auth::loginOrCreateFromProvider('google','456','provider@example.test','Provider Test')['id'],'provider identity links uniquely');
 $data=['name'=>'Quote QA','email'=>'quote@example.test','subject'=>'One-Time Setup','message'=>'Please help set up the following accounts.','use_case'=>'Clients / agency','account_count'=>'4','api_status'=>'I need help with all of them','platforms'=>['Instagram','LinkedIn'],'services'=>['Hosted workspace onboarding'],'organization'=>'Quote Brand','website_url'=>'https://example.test','current_tool'=>'Sheets','timeline'=>'Within a month','budget'=>'Not sure yet','timezone'=>'Asia/Karachi','phone'=>''];
 [$valid,$errors,$quote]=App\ContactInquiry::validate($data);check(!$errors,'setup brief validates');
 $before=(int)$pdo->query('SELECT COUNT(*) FROM les_contact_messages')->fetchColumn();
 throws(function()use($pdo){DB::transaction(function($p){$p->prepare('INSERT INTO les_contact_messages(name,email,subject,message,created_at) VALUES (?,?,?,?,?)')->execute(['Rollback QA','rollback@example.test','Test','Test rollback',gmdate('Y-m-d H:i:s')]);throw new RuntimeException('Injected failure');});},'injected multi-write failure surfaces');
 check((int)$pdo->query('SELECT COUNT(*) FROM les_contact_messages')->fetchColumn()===$before,'failed transaction rolls back its earlier write');
 $key=bin2hex(random_bytes(16));ContactService::submit($valid,$quote,$key);ContactService::submit($valid,$quote,$key);
 check((int)$pdo->query('SELECT COUNT(*) FROM les_contact_messages')->fetchColumn()===1,'duplicate contact submit creates one message');
 check((int)$pdo->query("SELECT COUNT(*) FROM les_mail_jobs WHERE dedupe_key LIKE 'contact:%'")->fetchColumn()===1,'contact and mail outbox deduplicate together');
 $delivered=[];while(MailQueue::processOne(function($data)use(&$delivered){$delivered[]=$data;return true;})){}
 check(count($delivered)===2,'worker consumes queued reset and contact notifications');
 check(str_contains($delivered[1]['text'],'Quote Brand')&&str_contains($delivered[1]['text'],'Instagram, LinkedIn'),'quote fields reach worker intact');
 check((int)$pdo->query("SELECT COUNT(*) FROM les_mail_jobs WHERE status='sent' AND payload='' AND claim_token IS NULL")->fetchColumn()===2,'delivered mail payload scrubbed, claim released');
 DB::transaction(fn($p)=>MailQueue::enqueue($p,'fail-job','fail@example.test','Test','Test','Test'));
 MailQueue::processOne(fn()=>false);
 $job=$pdo->query("SELECT * FROM les_mail_jobs WHERE dedupe_key='fail-job'")->fetch();check($job['status']==='queued'&&(int)$job['attempts']===1&&(int)$job['available_at']>time(),'failure rescheduled with backoff');
 for($i=0;$i<4;$i++){$pdo->exec("UPDATE les_mail_jobs SET available_at=0 WHERE dedupe_key='fail-job'");MailQueue::processOne(fn()=>false);}
 $job=$pdo->query("SELECT * FROM les_mail_jobs WHERE dedupe_key='fail-job'")->fetch();check($job['status']==='dead'&&$job['payload']===''&&(int)$job['attempts']===5,'bounded retries end in scrubbed dead letter');
 DB::transaction(fn($p)=>MailQueue::enqueue($p,'expired-job','fail@example.test','Test','Test','Test',time()-1));check(!MailQueue::processOne(fn()=>true),'expired reset job not delivered');
 for($i=0;$i<3;$i++)check(RateLimiter::check('test',3,60,'same'),'rate budget admits allowed request');check(!RateLimiter::check('test',3,60,'same'),'rate budget blocks next request');
 check(RateLimiter::check('test',3,60,'other'),'independent caller gets own budget');
 foreach(['http://www.googleapis.com/x','https://127.0.0.1','https://169.254.169.254/latest/meta-data','https://evil.test','https://www.googleapis.com.evil.test','https://user@www.googleapis.com','https://www.googleapis.com:444/'] as $url)throws(fn()=>HttpClient::validateUrl($url),'unsafe outbound destination rejected');
 $calls=0;$http=new HttpClient(function()use(&$calls){$calls++;return $calls===1?[503,'{}']:[200,'{"ok":true}'];});check($http->request('GET','https://www.googleapis.com/test')['ok']&&$calls===2,'safe GET transient retry bounded');
 $calls=0;$http=new HttpClient(function()use(&$calls){$calls++;return [503,'{}'];});throws(fn()=>$http->request('POST','https://www.googleapis.com/test','x'),'unsafe POST fails without replay');check($calls===1,'one unsafe POST attempt');
 $http=new HttpClient(fn()=>[200,'not json']);throws(fn()=>$http->request('GET','https://www.googleapis.com/test'),'malformed provider JSON rejected');
 $pdo->exec('DELETE FROM les_circuits');$http=new HttpClient(fn()=>[503,'{}']);for($i=0;$i<5;$i++){try{$http->request('POST','https://www.googleapis.com/test');}catch(Throwable){}}
 throws(fn()=>CircuitBreaker::enter('www.googleapis.com'),'circuit opens after repeated failures');
 $pdo->exec('UPDATE les_circuits SET open_until=0');CircuitBreaker::enter('www.googleapis.com');throws(fn()=>CircuitBreaker::enter('www.googleapis.com'),'one half-open recovery probe');CircuitBreaker::success('www.googleapis.com');
 $google=new GoogleOAuth(new HttpClient(fn($m,$url)=>str_contains($url,'/token')?[200,'{"access_token":"test-access-token"}']:[200,'{"sub":"123456","email":"verified@example.test","email_verified":false}']));$google->authUrl();$state=$_SESSION['google_oauth_state'];throws(fn()=>$google->handleCallback('test-code',$state),'Google rejects unverified email');
 $google=new GoogleOAuth(new HttpClient(fn($m,$url)=>str_contains($url,'/token')?[200,'{"access_token":"test-access-token"}']:[200,'{"sub":"123456","email":"verified@example.test","email_verified":true,"name":"Verified Test"}']));$url=$google->authUrl();$state=$_SESSION['google_oauth_state'];check(str_contains($url,'code_challenge_method=S256'),'PKCE enabled');check($google->handleCallback('test-code',$state)['email']==='verified@example.test','verified Google profile accepted');throws(fn()=>$google->handleCallback('test-code',$state),'OAuth state single use');
 putenv('PAYPAL_ENABLED=true');putenv('PAYPAL_CLIENT_ID=test-id');putenv('PAYPAL_CLIENT_SECRET=test-secret');putenv('PAYPAL_WEBHOOK_ID=test-webhook');putenv('PAYPAL_PLAN_MONTHLY_ID=P-TEST');
 $snapshot=['id'=>'I-TEST123','plan_id'=>'P-TEST','custom_id'=>(string)$u['id'],'status'=>'ACTIVE','update_time'=>'2026-09-14T01:00:00Z'];
 $http=new HttpClient(function($method,$url)use(&$snapshot){
  if(str_ends_with($url,'/v1/oauth2/token'))return [200,'{"access_token":"test-paypal-token"}'];
  if(str_contains($url,'verify-webhook'))return [200,'{"verification_status":"SUCCESS"}'];
  if($method==='POST')return [200,'{"id":"I-TEST123","links":[{"rel":"approve","href":"https://www.sandbox.paypal.com/approve"}]}'];
  return [200,json_encode($snapshot)];
 });$paypal=new PayPal($http);
 $paypal->createSubscription($u['id'],'monthly',str_repeat('a',32));$paypal->createSubscription($u['id'],'monthly',str_repeat('a',32));
 check((int)$pdo->query("SELECT COUNT(*) FROM les_subscriptions WHERE paypal_subscription_id='I-TEST123'")->fetchColumn()===1,'checkout response retry does not reassign or duplicate subscription');
 $headers=['paypal-auth-algo'=>'test','paypal-cert-url'=>'https://api.paypal.com/cert','paypal-transmission-id'=>'test','paypal-transmission-sig'=>'test','paypal-transmission-time'=>'2026-09-14T01:00:00Z'];
 $event=['id'=>'EVT-1','event_type'=>'BILLING.SUBSCRIPTION.ACTIVATED','create_time'=>'2026-09-14T01:00:00Z','resource'=>['id'=>'I-TEST123']];
 $paypal->processWebhook(json_encode($event),$headers);$paypal->processWebhook(json_encode($event),$headers);
 check((int)$pdo->query('SELECT COUNT(*) FROM les_subscription_events')->fetchColumn()===1,'verified webhook replay idempotent');
 check($pdo->query('SELECT plan FROM les_users WHERE id='.$u['id'])->fetchColumn()==='managed','matched canonical active subscription grants plan');
 $snapshot['plan_id']='P-WRONG';$event['id']='EVT-2';throws(fn()=>$paypal->processWebhook(json_encode($event),$headers),'wrong provider plan binding rejected');check((int)$pdo->query('SELECT COUNT(*) FROM les_subscription_events')->fetchColumn()===1,'failed entitlement transaction rolls back event');
 $snapshot['plan_id']='P-TEST';$snapshot['status']='CANCELLED';$snapshot['update_time']='2026-09-14T02:00:00Z';$event['event_type']='BILLING.SUBSCRIPTION.CANCELLED';$paypal->processWebhook(json_encode($event),$headers);
 check($pdo->query('SELECT plan FROM les_users WHERE id='.$u['id'])->fetchColumn()==='free','canonical cancellation revokes paid plan');
 $snapshot['status']='ACTIVE';$snapshot['update_time']='2026-09-14T01:00:00Z';$event['id']='EVT-3';$paypal->processWebhook(json_encode($event),$headers);
 check($pdo->query('SELECT plan FROM les_users WHERE id='.$u['id'])->fetchColumn()==='free','out-of-order older event cannot restore access');
 $event['id']='EVT-4';$event['event_type']='PAYMENT.SALE.COMPLETED';$paypal->processWebhook(json_encode($event),$headers);check($pdo->query('SELECT plan FROM les_users WHERE id='.$u['id'])->fetchColumn()==='free','sale event cannot grant subscription privileges');
 putenv('APP_ENV=production');putenv('MAIL_DRIVER=log');putenv('MAIL_LOG_CONTENT=true');check(!App\Services\Mailer::send('test@example.test','Reset','secret-reset-link','secret-reset-link'),'production log driver never sends or exposes mail contents');
 App\Logger::event('error','redaction_test',['password'=>'TOP-SECRET-SENTINEL','token'=>'TOKEN-SENTINEL','body'=>'BODY-SENTINEL','status'=>503]);$log=file_get_contents(LES_BASE_PATH.'/storage/logs/app.jsonl');check(!str_contains($log,'TOP-SECRET-SENTINEL')&&!str_contains($log,'TOKEN-SENTINEL')&&!str_contains($log,'BODY-SENTINEL'),'structured logger drops sensitive/unapproved fields');
 echo "\n$count engineering checks passed on ".DB::driver().". Provider responses were mocked; no live service certification.\n";
}finally{if(!$mysql){@unlink($testFile);@unlink($testFile.'-wal');@unlink($testFile.'-shm');}}
