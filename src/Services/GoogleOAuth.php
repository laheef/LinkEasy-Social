<?php
namespace App\Services;
use App\Request;
final class GoogleOAuth
{
    private HttpClient $http;
    public function __construct(?HttpClient $http=null){$this->http=$http??new HttpClient();}
    private function redirectUri(): string {return (string)(getenv('GOOGLE_REDIRECT_URI')?:config('brand.app_url').url('google_callback'));}
    public function configured(): bool {return (bool)getenv('GOOGLE_CLIENT_ID')&&(bool)getenv('GOOGLE_CLIENT_SECRET');}
    public function authUrl(): string {
        $_SESSION['google_oauth_state']=bin2hex(random_bytes(24));$_SESSION['google_oauth_started']=time();
        $_SESSION['google_oauth_verifier']=bin2hex(random_bytes(32));
        $_SESSION['google_oauth_next']=Request::next($_GET['next']??null);
        return 'https://accounts.google.com/o/oauth2/v2/auth?'.http_build_query([
            'client_id'=>getenv('GOOGLE_CLIENT_ID'),'redirect_uri'=>$this->redirectUri(),'response_type'=>'code','scope'=>'openid email profile','state'=>$_SESSION['google_oauth_state'],'code_challenge'=>rtrim(strtr(base64_encode(hash('sha256',$_SESSION['google_oauth_verifier'],true)),'+/','-_'),'='),'code_challenge_method'=>'S256','prompt'=>'select_account'
        ]);
    }
    public function handleCallback(string $code,string $state): array {
        $stored=$_SESSION['google_oauth_state']??'';$started=$_SESSION['google_oauth_started']??0;$verifier=$_SESSION['google_oauth_verifier']??'';$next=Request::next($_SESSION['google_oauth_next']??null);
        unset($_SESSION['google_oauth_state'],$_SESSION['google_oauth_started'],$_SESSION['google_oauth_verifier'],$_SESSION['google_oauth_next']);
        if(!$stored || !hash_equals($stored,$state) || time()-(int)$started>600 || !$verifier || !$code || strlen($code)>4096)throw new \RuntimeException('Invalid or expired OAuth callback.');
        $token=$this->http->request('POST','https://oauth2.googleapis.com/token',http_build_query(['code'=>$code,'code_verifier'=>$verifier,'client_id'=>getenv('GOOGLE_CLIENT_ID'),'client_secret'=>getenv('GOOGLE_CLIENT_SECRET'),'redirect_uri'=>$this->redirectUri(),'grant_type'=>'authorization_code']),['Content-Type: application/x-www-form-urlencoded']);
        if(!is_string($token['access_token']??null)||strlen($token['access_token'])>4096||preg_match('/[\r\n]/',$token['access_token']))throw new \RuntimeException('Invalid access token.');
        $profile=$this->http->request('GET','https://www.googleapis.com/oauth2/v3/userinfo',null,['Authorization: Bearer '.$token['access_token']]);
        if(($profile['email_verified']??false)!==true || !is_string($profile['sub']??null) || !preg_match('/^[0-9]{1,120}$/',$profile['sub']) || !is_string($profile['email']??null) || strlen($profile['email'])>190 || !filter_var($profile['email'],FILTER_VALIDATE_EMAIL))throw new \RuntimeException('A verified provider identity is required.');
        return ['provider_id'=>$profile['sub'],'email'=>$profile['email'],'name'=>is_string($profile['name']??null)?mb_substr($profile['name'],0,100):'','next'=>$next];
    }
}
