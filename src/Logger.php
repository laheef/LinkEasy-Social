<?php
namespace App;
/** Structured telemetry with allowlisted context only. Never logs bodies/URLs/tokens/messages. */
final class Logger
{
    private static string $id='';
    public static function requestId(): string { return self::$id ?: (self::$id=bin2hex(random_bytes(12))); }
    public static function exception(\Throwable $e,string $event): void {
        self::event('error',$event,['exception'=>get_class($e),'file'=>basename($e->getFile()),'line'=>$e->getLine()]);
    }
    public static function event(string $level,string $event,array $context=[]): void {
        $allowed=['user_id','job_id','provider','status','attempt','duration_ms','exception','file','line','count','outcome'];
        $safe=[];
        foreach($allowed as $key) if(isset($context[$key]) && is_scalar($context[$key])) $safe[$key]=is_string($context[$key])?substr($context[$key],0,150):$context[$key];
        $row=['time'=>gmdate('c'),'level'=>$level,'event'=>preg_replace('/[^a-z0-9_.-]/i','_',substr($event,0,80)),'request_id'=>self::requestId()];
        if(!empty($_SESSION['user_id'])) $row['user_id']=(int)$_SESSION['user_id'];
        $line=json_encode($row+$safe,JSON_UNESCAPED_SLASHES|JSON_INVALID_UTF8_SUBSTITUTE)."\n";
        $file=LES_BASE_PATH.'/storage/logs/app.jsonl';
        $fp=@fopen($file,'ab');
        if($fp && flock($fp,LOCK_EX)) {
            // Bounded single-file telemetry; use an external log collector for durable history.
            if(fstat($fp)['size']>5*1024*1024) ftruncate($fp,0);
            fwrite($fp,$line);flock($fp,LOCK_UN);fclose($fp);
        } else { if(is_resource($fp))fclose($fp);error_log($line); }
    }
}
