<?php
namespace App\Services;
use App\Database;
use App\Logger;
use PDO;

/** Transactional outbox. Workers deliver at least once, never claim exactly-once SMTP. */
final class MailQueue
{
    // Caller owns the transaction when enqueueing alongside business data.
    public static function enqueue(PDO $pdo,string $key,string $to,string $subject,string $html,string $text,?int $expires=null): void {
        if(strlen($key)>128 || !filter_var($to,FILTER_VALIDATE_EMAIL) || preg_match('/[\r\n]/',$to.$subject)) throw new \InvalidArgumentException('Invalid mail job.');
        $payload=json_encode(compact('to','subject','html','text'),JSON_THROW_ON_ERROR);
        if(strlen($payload)>100000) throw new \InvalidArgumentException('Mail job too large.');
        $pdo->prepare("INSERT INTO les_mail_jobs(dedupe_key,payload,status,attempts,available_at,created_at,expires_at) VALUES (?,?,'queued',0,?,?,?)")->execute([$key,$payload,time(),time(),$expires]);
    }
    public static function claim(): ?array {
        return Database::transaction(function($pdo){
            $now=time();
            // Expired password-reset jobs and exhausted abandoned leases never send.
            $pdo->prepare("UPDATE les_mail_jobs SET status='dead',payload='',finished_at=?,last_error='expired_or_exhausted' WHERE (status='queued' OR (status='processing' AND locked_until<=?)) AND ((expires_at IS NOT NULL AND expires_at<=?) OR attempts>=5)")->execute([$now,$now,$now]);
            $stmt=$pdo->prepare("SELECT * FROM les_mail_jobs WHERE (status='queued' AND available_at<=?) OR (status='processing' AND locked_until<=?) ORDER BY available_at,id LIMIT 1".Database::forUpdate());
            $stmt->execute([$now,$now]);$job=$stmt->fetch();if(!$job)return null;
            $claim=bin2hex(random_bytes(16));
            $pdo->prepare("UPDATE les_mail_jobs SET status='processing',attempts=attempts+1,claim_token=?,locked_until=? WHERE id=?")->execute([$claim,$now+180,$job['id']]);
            $job['claim_token']=$claim;$job['attempts']++;return $job;
        });
    }
    public static function processOne(?callable $deliver=null): bool {
        $job=self::claim();if(!$job)return false;
        $ok=false;
        try {
            $data=json_decode($job['payload'],true,16,JSON_THROW_ON_ERROR);
            foreach(['to','subject','html','text'] as $key)if(!is_string($data[$key]??null))throw new \RuntimeException('Invalid payload.');
            $ok=$deliver ? (bool)$deliver($data) : Mailer::send($data['to'],$data['subject'],$data['html'],$data['text']);
        } catch(\Throwable $e) {Logger::exception($e,'mail_delivery_exception');}
        $dead=!$ok && (int)$job['attempts']>=5;
        $status=$ok?'sent':($dead?'dead':'queued');
        $delay=min(3600,30*(2**min(7,(int)$job['attempts']-1)))+random_int(0,15);
        $stmt=Database::pdo()->prepare('UPDATE les_mail_jobs SET status=?,payload=?,available_at=?,finished_at=?,locked_until=NULL,claim_token=NULL,last_error=? WHERE id=? AND claim_token=?');
        $stmt->execute([$status,($ok||$dead)?'':$job['payload'],time()+$delay,($ok||$dead)?time():null,$ok?null:'delivery_failed',$job['id'],$job['claim_token']]);
        Logger::event($ok?'info':'error','mail_job_result',['job_id'=>(int)$job['id'],'attempt'=>(int)$job['attempts'],'outcome'=>$status]);
        return true;
    }
}
