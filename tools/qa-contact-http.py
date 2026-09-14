#!/usr/bin/env python3
"""Destructive test of CONTACT DATA in an isolated copy ONLY.
Start that copy separately with PHP, MAIL_DRIVER=log and a separate sys_temp_dir.
python3 tools/qa-contact-http.py http://127.0.0.1:8081 /path/to/isolated-copy
Requires a clean SQLite schema and a file named .contact-qa-isolated in that copy.
Never run against production or a real mail driver.
"""
import sys, re, time, sqlite3, subprocess, os, urllib.request, urllib.parse, urllib.error, http.cookiejar
from pathlib import Path
base, directory = sys.argv[1:3]
root = Path(directory).resolve()
assert (root / '.contact-qa-isolated').is_file(), 'Isolated-copy marker required'
assert root != Path(__file__).resolve().parent.parent, 'Do not target the source project'
assert urllib.parse.urlparse(base).hostname in ['127.0.0.1', 'localhost'], 'Local test server only'
db = sqlite3.connect(root / 'storage/linkeasy.sqlite')
count = lambda: db.execute('SELECT COUNT(*) FROM les_contact_messages').fetchone()[0]
assert count() == 0, 'Use an empty test database'
opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))
def call(data=None):
    req = urllib.request.Request(base+'/contact', data=urllib.parse.urlencode(data, doseq=True).encode() if data is not None else None)
    try:
        with opener.open(req) as r: return r.status, r.read().decode()
    except urllib.error.HTTPError as e: return e.code, e.read().decode()
def form(**values):
    status, html = call()
    assert status == 200
    csrf = re.search(r'name="_csrf" value="([^"]+)"', html).group(1)
    request_key = re.search(r'name="request_key" value="([^"]+)"', html).group(1)
    return dict(_csrf=csrf, request_key=request_key, website='', form_started=str(int(time.time())-10), name='QA Example', email='qa@example.test', subject='General question', message='Please explain the setup options for my channels.', **values)
# CSRF blocks writes.
data=form();data['_csrf']='invalid';assert call(data)[0]==419;assert count()==0
# Honeypot silently absorbs bots without storing data.
data=form();data['website']='spam.example';assert call(data)[0]==200;assert count()==0
# General enquiries do not require setup fields.
status,html=call(form());assert status==200 and 'Your message has been saved.' in html;assert count()==1
# A fully populated quote survives HTTP, storage and the email-log driver.
data=form();data.update(subject='One-Time Setup',organization='QA Brand',website_url='https://example.test',use_case='Clients / agency',account_count='8',api_status='I have some, but need help',current_tool='Spreadsheets',timeline='Within a month',budget='Not sure yet',timezone='Asia/Karachi',phone='+92 000 0000000')
data['platforms[]']=['Instagram','LinkedIn'];data['services[]']=['Hosted workspace onboarding','Team walkthrough / training']
status,html=call(data);assert status==200 and 'Your quote request has been saved.' in html;assert count()==2
env=os.environ.copy();env.update(APP_ENV='test',MAIL_DRIVER='log',MAIL_LOG_CONTENT='true',DATABASE_DSN='sqlite:'+str(root/'storage/linkeasy.sqlite'))
subprocess.run(['php','tools/worker.php','--max-jobs=10'],cwd=root,env=env,check=True)
message=db.execute('SELECT message FROM les_contact_messages ORDER BY id DESC LIMIT 1').fetchone()[0]
log=(root/'storage/logs/mail.log').read_text()
for text in ['Instagram, LinkedIn','Hosted workspace onboarding, Team walkthrough / training','QA Brand','https://example.test','Clients / agency','8','I have some, but need help','Spreadsheets','Within a month','Not sure yet','Asia/Karachi','+92 000 0000000',data['message']]:
    assert text in message and text in log, text
# Missing quote fields produce visible errors and escaped retained input, no write.
data=form();data.update(subject='Request a quote',name='<script>alert(1)</script>',organization='Kept brand')
status,html=call(data);assert status==422 and 'data-error-summary' in html and 'Kept brand' in html
assert 'value="&lt;script&gt;alert(1)&lt;/script&gt;"' in html and '<script>alert(1)</script>' not in html;assert count()==2
# Minimum-time trap now gives a retry message instead of a false success.
data=form();data['form_started']=str(int(time.time()))
status,html=call(data);assert status==422 and 'Please wait a moment' in html;assert count()==2
# Unlisted topics and malformed scalar input are rejected safely.
data=form();data['subject']='Invented subject';assert call(data)[0]==422
# Sixth rate-limited attempt: bad email.
data=form();data['email']='invalid';assert call(data)[0]==422
# Subsequent attempt is throttled without writing.
status,html=call(form());assert status==429 and 'Too many requests' in html;assert count()==2
print('PASS isolated HTTP: CSRF, honeypot, general enquiry, complete quote persistence + outbox worker email log, retained escaped errors, minimum time, subject/email validation, 6/hour rate limit.')
print('Log-driver delivery only. No live SMTP or provider requests were made.')
db.close()
