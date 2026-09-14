<?php
namespace App\Services;
use App\Auth;
use App\Database;
final class PasswordReset
{
    public static function request(string $email): void {
        if(strlen($email)>190 || !filter_var($email,FILTER_VALIDATE_EMAIL))return;
        Database::transaction(function($pdo)use($email){
            $stmt=$pdo->prepare('SELECT id FROM les_users WHERE email=? AND password_hash IS NOT NULL'.Database::forUpdate());$stmt->execute([$email]);$id=$stmt->fetchColumn();if(!$id)return;
            $token=bin2hex(random_bytes(32));$hash=hash('sha256',$token);$now=gmdate('Y-m-d H:i:s');
            $pdo->prepare('UPDATE les_password_resets SET used=1 WHERE email=? AND used=0')->execute([$email]);
            $pdo->prepare('INSERT INTO les_password_resets(email,token_hash,expires_at,created_at) VALUES (?,?,?,?)')->execute([$email,$hash,gmdate('Y-m-d H:i:s',time()+3600),$now]);
            // Cancel earlier unsent reset emails for this account.
            $pdo->prepare("UPDATE les_mail_jobs SET status='dead',payload='',finished_at=?,last_error='superseded' WHERE dedupe_key LIKE ? AND status='queued'")->execute([time(),'reset:'.$id.':%']);
            $link=config('brand.app_url').url('reset_password').'?token='.$token.'&email='.urlencode($email);
            MailQueue::enqueue($pdo,'reset:'.$id.':'.$hash,$email,'Reset your '.config('brand.name').' password','<p>Reset your password (valid for 60 minutes): <a href="'.e($link).'">Choose a new password</a></p>',"Reset link (60 minutes):\n".$link,time()+3600);
        });
    }
    public static function valid(string $token,string $email): bool {
        if(!preg_match('/^[a-f0-9]{64}$/',$token)||strlen($email)>190||!filter_var($email,FILTER_VALIDATE_EMAIL))return false;
        $stmt=Database::pdo()->prepare('SELECT id FROM les_password_resets WHERE email=? AND token_hash=? AND used=0 AND expires_at>? LIMIT 1');$stmt->execute([$email,hash('sha256',$token),gmdate('Y-m-d H:i:s')]);return (bool)$stmt->fetchColumn();
    }
    public static function reset(string $token,string $email,string $password): bool {
        if(!Auth::validPassword($password) || !self::valid($token,$email))return false;
        $hash=Auth::hashPassword($password);
        return Database::transaction(function($pdo)use($token,$email,$hash){
            // Lock the account first, same ordering as reset requests, then recheck token atomically.
            $s=$pdo->prepare('SELECT id FROM les_users WHERE email=?'.Database::forUpdate());$s->execute([$email]);$id=$s->fetchColumn();if(!$id)return false;
            $stmt=$pdo->prepare('UPDATE les_password_resets SET used=1 WHERE email=? AND token_hash=? AND used=0 AND expires_at>?');$stmt->execute([$email,hash('sha256',$token),gmdate('Y-m-d H:i:s')]);if($stmt->rowCount()!==1)return false;
            $pdo->prepare('UPDATE les_users SET password_hash=?,auth_version=auth_version+1,updated_at=? WHERE id=?')->execute([$hash,gmdate('Y-m-d H:i:s'),$id]);
            $pdo->prepare('UPDATE les_password_resets SET used=1 WHERE email=?')->execute([$email]);return true;
        });
    }
}
