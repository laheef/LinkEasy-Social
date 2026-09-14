<?php
/** Private CLI operational views; never exposes contact bodies or mail payloads. */
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require dirname(__DIR__).'/src/bootstrap.php';
$cmd=$argv[1]??'status';$pdo=App\Database::pdo();
if($cmd==='status'){
    $after=max(0,(int)($argv[2]??0));$limit=max(1,min(100,(int)($argv[3]??25)));
    $s=$pdo->prepare('SELECT id,status,attempts,available_at,created_at,finished_at,last_error FROM les_mail_jobs WHERE id>? ORDER BY id LIMIT '.$limit);$s->execute([$after]);$rows=$s->fetchAll();
    echo json_encode(['jobs'=>$rows,'next_after_id'=>$rows?end($rows)['id']:null],JSON_PRETTY_PRINT).PHP_EOL;
    exit;
}
if($cmd==='resend-contact'){
    $id=(int)($argv[2]??0);
    if($id<1||($argv[3]??'')!=='--confirm'){fwrite(STDERR,"Confirm with: php tools/ops.php resend-contact ID --confirm\n");exit(1);}
    App\Database::transaction(function($pdo)use($id){
        $q=$pdo->prepare('SELECT name,email,subject,message FROM les_contact_messages WHERE id=?');$q->execute([$id]);$data=$q->fetch();if(!$data)throw new RuntimeException('Contact not found.');
        $text=$data['name'].' <'.$data['email'].">\nSubject: ".$data['subject']."\n\n".$data['message'];
        App\Services\MailQueue::enqueue($pdo,'contact-resend:'.$id.':'.bin2hex(random_bytes(8)),config('brand.support_email'),'Contact form: '.$data['subject'],'<p>'.nl2br(e($text)).'</p>',$text);
    });echo "Contact notification queued again. Run the worker with SMTP configured.\n";exit;
}
if($cmd==='prune'){
    // Explicit opt-in. Never deletes customers, subscriptions, payment events or contact records.
    $days=max(7,(int)($argv[2]??30));$cut=time()-$days*86400;$apply=($argv[3]??'')==='--apply';
    $conditions=[
        'les_rate_limits'=>['reset_at<?',[time()-86400]],
        'les_password_resets'=>['expires_at<?',[gmdate('Y-m-d H:i:s',$cut)]],
        'les_mail_jobs'=>["status IN ('sent','dead') AND finished_at<?",[$cut]]
    ];
    foreach($conditions as $table=>[$where,$values]){
        $s=$pdo->prepare("SELECT COUNT(*) FROM $table WHERE $where");$s->execute($values);$n=(int)$s->fetchColumn();
        echo ($apply?'Deleting':'Would delete')." $n from $table\n";
        if($apply){$s=$pdo->prepare("DELETE FROM $table WHERE $where");$s->execute($values);}
    }
    exit;
}
fwrite(STDERR,"Usage: php tools/ops.php status [after-id] [limit<=100]\n       php tools/ops.php prune [days>=7] [--apply]\n");exit(1);
