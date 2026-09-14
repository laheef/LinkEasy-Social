<?php
namespace App\Services;
use App\Logger;
/** Fixed-provider outbound adapter. Never fetches contact-form/public-user URLs. */
final class HttpClient
{
    private const HOSTS=['oauth2.googleapis.com','www.googleapis.com','api-m.paypal.com','api-m.sandbox.paypal.com'];
    public function __construct(private mixed $transport=null) {}
    public static function validateUrl(string $url): string {
        $parts=parse_url($url);
        if(!$parts || ($parts['scheme']??'')!=='https' || !in_array($parts['host']??'',self::HOSTS,true) || isset($parts['user']) || isset($parts['pass']) || (isset($parts['port'])&&$parts['port']!==443))throw new \InvalidArgumentException('Outbound URL is not allowed.');
        return $parts['host'];
    }
    public function request(string $method,string $url,?string $body=null,array $headers=[],?string $basic=null,bool $safeRetry=false): array {
        $provider=self::validateUrl($url);CircuitBreaker::enter($provider);
        $tries=($method==='GET'||$safeRetry)?2:1;
        for($attempt=1;$attempt<=$tries;$attempt++) {
            try {[$status,$raw]=$this->transport?($this->transport)($method,$url,$body,$headers,$basic):$this->curl($method,$url,$body,$headers,$basic);}
            catch(\Throwable $e) {$status=0;$raw='';}
            $transient=$status===0||$status===429||$status>=500;
            if($transient && $attempt<$tries){usleep(random_int(100000,250000));continue;}
            Logger::event($status>=200&&$status<300?'info':'warning','provider_response',['provider'=>$provider,'status'=>$status,'attempt'=>$attempt]);
            if($transient){CircuitBreaker::failure($provider);throw new \RuntimeException('Provider unavailable.');}
            if($status<200||$status>=300){CircuitBreaker::success($provider);throw new \RuntimeException('Provider rejected request.');}
            try {$data=json_decode($raw,true,32,JSON_THROW_ON_ERROR);if(!is_array($data)||array_is_list($data))throw new \RuntimeException('Expected an object.');}
            catch(\Throwable $e){CircuitBreaker::failure($provider);throw new \RuntimeException('Invalid provider response.');}
            CircuitBreaker::success($provider);return $data;
        }
        throw new \RuntimeException('Provider unavailable.');
    }
    private function curl(string $method,string $url,?string $body,array $headers,?string $basic): array {
        $raw='';$ch=curl_init($url);
        curl_setopt_array($ch,[CURLOPT_CUSTOMREQUEST=>$method,CURLOPT_HTTPHEADER=>$headers,CURLOPT_CONNECTTIMEOUT=>3,CURLOPT_TIMEOUT=>10,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_MAXREDIRS=>0,CURLOPT_PROTOCOLS=>CURLPROTO_HTTPS,CURLOPT_SSL_VERIFYPEER=>true,CURLOPT_SSL_VERIFYHOST=>2,CURLOPT_WRITEFUNCTION=>function($ch,$part)use(&$raw){if(strlen($raw)+strlen($part)>1048576)return 0;$raw.=$part;return strlen($part);}]);
        if($body!==null)curl_setopt($ch,CURLOPT_POSTFIELDS,$body);
        if($basic!==null)curl_setopt($ch,CURLOPT_USERPWD,$basic);
        $ok=curl_exec($ch);$status=(int)curl_getinfo($ch,CURLINFO_RESPONSE_CODE);$error=curl_errno($ch);curl_close($ch);
        return [$ok===false||$error?0:$status,$raw];
    }
}
