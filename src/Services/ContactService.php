<?php
namespace App\Services;
use App\Database;
use App\Auth;
use App\ContactInquiry;
final class ContactService
{
    public static function submit(array $data,bool $quote,string $requestKey): void {
        if(!preg_match('/^[a-f0-9]{32}$/',$requestKey)) throw new \InvalidArgumentException('Invalid submission key.');
        $key='contact:'.$requestKey;
        try {
            Database::transaction(function($pdo)use($data,$quote,$key){
                $stmt=$pdo->prepare('SELECT id FROM les_mail_jobs WHERE dedupe_key=?');$stmt->execute([$key]);if($stmt->fetchColumn())return;
                $message=ContactInquiry::message($data,$quote);
                $pdo->prepare('INSERT INTO les_contact_messages(name,email,subject,message,created_at) VALUES (?,?,?,?,?)')->execute([$data['name'],Auth::normalizeEmail($data['email']),$data['subject'],$message,gmdate('Y-m-d H:i:s')]);
                MailQueue::enqueue($pdo,$key,config('brand.support_email'),'Contact form: '.$data['subject'],'<p><b>'.e($data['name']).' ('.e($data['email']).')</b></p><p>'.nl2br(e($message)).'</p>',$data['name'].' <'.$data['email'].">\nSubject: ".$data['subject']."\n\n".$message);
            });
        } catch(\PDOException $e) {if(!Database::duplicate($e))throw $e;}
    }
}
