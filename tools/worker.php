<?php
/** Cron-friendly bounded worker, not a web endpoint. */
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require dirname(__DIR__).'/src/bootstrap.php';
$options=getopt('',['max-jobs::','max-seconds::']);
$limit=max(1,min(100,(int)($options['max-jobs']??20)));$seconds=max(1,min(300,(int)($options['max-seconds']??45)));
$start=microtime(true);$done=0;
while($done<$limit && microtime(true)-$start<$seconds && App\Services\MailQueue::processOne())$done++;
echo "Processed $done job(s).\n";
App\Logger::event('info','mail_worker_finished',['count'=>$done,'duration_ms'=>(int)((microtime(true)-$start)*1000)]);
