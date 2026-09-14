#!/usr/bin/env python3
"""Finite subprocess stress checks, not a production load/capacity benchmark.
Creates/deletes its own temporary SQLite DB. Optional dedicated MySQL DSN in
LES_CONCURRENCY_MYSQL_DSN must contain dbname=les_test and be an empty database.
"""
import os, subprocess, tempfile, json, pathlib, concurrent.futures, shutil
root=pathlib.Path(__file__).resolve().parent.parent
temp=pathlib.Path(tempfile.mkdtemp(prefix='les-concurrency-'))
env=os.environ.copy();mysql=env.get('LES_CONCURRENCY_MYSQL_DSN','')
if mysql: assert 'dbname=les_test' in mysql, 'Dedicated test DB required'
env.update(DATABASE_DSN=mysql or 'sqlite:'+str(temp/'test.sqlite'),APP_ENV='test')
boot='require '+json.dumps(str(root/'src/bootstrap.php'))+'; '
def php(code):
    p=subprocess.run(['php','-r',boot+code],env=env,capture_output=True,text=True,timeout=30)
    assert p.returncode==0,p.stderr+p.stdout
    return p.stdout.strip()
def parallel(code,n=20):
    with concurrent.futures.ThreadPoolExecutor(max_workers=n) as pool:return list(pool.map(lambda _:php(code),range(n)))
try:
    php('App\\Migrations::run(App\\Database::pdo());')
    result=parallel("echo App\\RateLimiter::check('race',5,60,'same')?'1':'0';")
    assert result.count('1')==5,result
    print('PASS 20 concurrent rate checks admit exactly 5')
    data="['name'=>'Concurrency QA','email'=>'race@example.test','subject'=>'General question','message'=>'Concurrent submission should only be saved once.']"
    parallel(f"App\\Services\\ContactService::submit({data},false,str_repeat('a',32));")
    assert php('echo App\\Database::pdo()->query("SELECT COUNT(*) FROM les_contact_messages")->fetchColumn();')=='1'
    assert php('echo App\\Database::pdo()->query("SELECT COUNT(*) FROM les_mail_jobs")->fetchColumn();')=='1'
    print('PASS 20 concurrent duplicate submissions create one contact + one job')
    php("App\\Database::pdo()->exec('DELETE FROM les_mail_jobs');for($i=0;$i<10;$i++)App\\Services\\MailQueue::enqueue(App\\Database::pdo(),'race-'.$i,'qa@example.test','QA','QA','QA');")
    result=parallel("$j=App\\Services\\MailQueue::claim();echo $j?$j['id']:'none';")
    ids=[x for x in result if x!='none'];assert len(ids)==10 and len(set(ids))==10,result
    print('PASS 20 competing workers claim 10 jobs once each')
    php("App\\Database::pdo()->exec('UPDATE les_mail_jobs SET locked_until=0');while(App\\Services\\MailQueue::processOne(fn()=>true)){}")
    assert php('echo App\\Database::pdo()->query("SELECT COUNT(*) FROM les_mail_jobs WHERE status=\'sent\'")->fetchColumn();')=='10'
    print('PASS abandoned worker leases recover and complete')
    php("App\\Auth::register('Reset QA','reset@example.test','Original password 123');App\\Services\\PasswordReset::request('reset@example.test');")
    payload=json.loads(php("echo App\\Database::pdo()->query(\"SELECT payload FROM les_mail_jobs WHERE dedupe_key LIKE 'reset:%' ORDER BY id DESC LIMIT 1\")->fetchColumn();"))
    import re
    token=re.search('token=([a-f0-9]{64})',payload['text']).group(1)
    result=parallel("echo App\\Services\\PasswordReset::reset('"+token+"','reset@example.test','Replacement password 456')?'1':'0';",10)
    assert result.count('1')==1,result
    print('PASS 10 simultaneous password resets consume token exactly once')
    print('Concurrency checks passed on '+('MySQL/MariaDB' if mysql else 'SQLite')+'. No production throughput claim.')
finally:shutil.rmtree(temp)
