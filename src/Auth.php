<?php
namespace App;
/** Account adapter: keep existing-app integration at this boundary. */
final class Auth
{
    public static function user(): ?array {
        if(empty($_SESSION['user_id']))return null;
        static $cache=[];$id=(int)$_SESSION['user_id'];
        if(!array_key_exists($id,$cache)){$s=Database::pdo()->prepare('SELECT id,name,email,plan,email_verified,auth_version,created_at FROM les_users WHERE id=?');$s->execute([$id]);$cache[$id]=$s->fetch()?:null;}
        $user=$cache[$id];
        if(!$user || (int)($_SESSION['_auth_version']??0)!==(int)$user['auth_version'] || time()-(int)($_SESSION['_authenticated_at']??0)>86400){les_logout(true);return null;}
        return $user;
    }
    public static function check(): bool {return self::user()!==null;}
    public static function validPassword(string $password): bool {return strlen($password)>=8 && strlen($password)<=72 && !str_contains($password,"\0");}
    private static function algorithm(): string|int {return defined('PASSWORD_ARGON2ID')?PASSWORD_ARGON2ID:PASSWORD_BCRYPT;}
    public static function hashPassword(string $password): string {
        if(!self::validPassword($password))throw new \InvalidArgumentException('Password must be 8–72 bytes.');
        return password_hash($password,self::algorithm());
    }
    public static function attempt(string $email,string $password,bool $remember=false): bool {
        $email=self::normalizeEmail($email);
        if(strlen($email)>190 || strlen($password)>72 || str_contains($password,"\0"))return false;
        if(!RateLimiter::check('login-account',10,900,$email))return false;
        $s=Database::pdo()->prepare('SELECT * FROM les_users WHERE email=?');$s->execute([$email]);$user=$s->fetch();
        // Always perform password verification, including absent/provider-only accounts.
        $hash=$user['password_hash']??'$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';
        $valid=password_verify($password,$hash);
        if(!$user || empty($user['password_hash']) || !$valid)return false;
        if(password_needs_rehash($hash,self::algorithm()))Database::pdo()->prepare('UPDATE les_users SET password_hash=? WHERE id=? AND password_hash=?')->execute([password_hash($password,self::algorithm()),$user['id'],$hash]);
        self::loginUser($user);return true;
    }
    public static function register(string $name,string $email,string $password): array {
        $email=self::normalizeEmail($email);
        if(mb_strlen($name)<2 || mb_strlen($name)>100 || strlen($email)>190 || !filter_var($email,FILTER_VALIDATE_EMAIL))throw new \InvalidArgumentException('Invalid registration.');
        $hash=self::hashPassword($password);
        $user=Database::transaction(function($pdo)use($name,$email,$hash){
            $now=gmdate('Y-m-d H:i:s');
            $pdo->prepare("INSERT INTO les_users(name,email,password_hash,plan,created_at) VALUES (?,?,?,'free',?)")->execute([$name,$email,$hash,$now]);
            $user=['id'=>(int)$pdo->lastInsertId(),'name'=>$name,'email'=>$email,'plan'=>'free','auth_version'=>1];
            $pdo->prepare("INSERT INTO les_subscriptions(user_id,plan,status,created_at) VALUES (?,'free','active',?)")->execute([$user['id'],$now]);return $user;
        });self::loginUser($user);return $user;
    }
    public static function emailExists(string $email): bool {$s=Database::pdo()->prepare('SELECT 1 FROM les_users WHERE email=?');$s->execute([self::normalizeEmail($email)]);return (bool)$s->fetchColumn();}
    public static function loginOrCreateFromProvider(string $provider,string $providerId,string $email,string $name): array {
        $email=self::normalizeEmail($email);
        if($provider!=='google'||$providerId===''||strlen($providerId)>120||strlen($email)>190||!filter_var($email,FILTER_VALIDATE_EMAIL))throw new \InvalidArgumentException('Invalid provider identity.');
        $user=Database::transaction(function($pdo)use($provider,$providerId,$email,$name){
            $s=$pdo->prepare('SELECT u.* FROM les_users u JOIN les_oauth_accounts o ON o.user_id=u.id WHERE o.provider=? AND o.provider_user_id=?');$s->execute([$provider,$providerId]);if($u=$s->fetch())return $u;
            $s=$pdo->prepare('SELECT id FROM les_users WHERE email=?');$s->execute([$email]);
            if($s->fetchColumn())throw new \RuntimeException('Sign in with the existing account method. Automatic email linking is disabled.');
            $now=gmdate('Y-m-d H:i:s');$name=mb_substr(trim($name)?:$email,0,100);
            $pdo->prepare("INSERT INTO les_users(name,email,plan,email_verified,created_at) VALUES (?,?,'free',1,?)")->execute([$name,$email,$now]);
            $u=['id'=>(int)$pdo->lastInsertId(),'name'=>$name,'email'=>$email,'plan'=>'free','auth_version'=>1];
            $pdo->prepare("INSERT INTO les_subscriptions(user_id,plan,status,created_at) VALUES (?,'free','active',?)")->execute([$u['id'],$now]);
            $pdo->prepare('INSERT INTO les_oauth_accounts(user_id,provider,provider_user_id,created_at) VALUES (?,?,?,?)')->execute([$u['id'],$provider,$providerId,$now]);return $u;
        });self::loginUser($user);return $user;
    }
    public static function loginUser(array $user): void {
        if(session_status()===PHP_SESSION_ACTIVE)session_regenerate_id(true);
        $_SESSION['user_id']=(int)$user['id'];$_SESSION['_auth_version']=(int)$user['auth_version'];
        $_SESSION['_last_activity']=time();$_SESSION['_authenticated_at']=time();$_SESSION['_csrf']=bin2hex(random_bytes(32));
    }
    public static function logout(): void {les_logout();}
    public static function requireLogin(): array {
        if($u=self::user())return $u;
        if(is_ajax_request()){header('Content-Type: application/json');http_response_code(401);echo json_encode(['error'=>'unauthenticated','login'=>url('login')]);exit;}
        flash('warning','Please log in to continue.');redirect(url('login',['next'=>Request::next($_SERVER['REQUEST_URI']??'/dashboard')]));
    }
    public static function normalizeEmail(string $email): string{return mb_strtolower(trim($email));}
}
