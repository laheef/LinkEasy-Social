<?php
namespace App;
/** Central boot-time checks. Secrets remain outside source; only setting names appear in failures. */
final class RuntimeConfig
{
    public static function validate(): void {
        foreach(['APP_ENV'=>['production','local','development','test'],'PAYPAL_MODE'=>['sandbox','live'],'MAIL_DRIVER'=>['log','smtp'],'SMTP_SECURE'=>['ssl','tls']] as $key=>$allowed){
            $default=['APP_ENV'=>'production','PAYPAL_MODE'=>'sandbox','MAIL_DRIVER'=>'log','SMTP_SECURE'=>'tls'][$key];
            if(!in_array(env($key,$default),$allowed,true))throw new \RuntimeException('Invalid configuration: '.$key);
        }
        $url=(string)config('brand.app_url');$parts=parse_url($url);
        if(!filter_var($url,FILTER_VALIDATE_URL)||!in_array($parts['scheme']??'',['http','https'],true)||isset($parts['user'])||isset($parts['pass'])||isset($parts['query'])||isset($parts['fragment']))throw new \RuntimeException('Invalid configuration: APP_URL');
        if(env('APP_ENV','production')==='production' && ($parts['scheme']??'')!=='https')throw new \RuntimeException('Production APP_URL must use HTTPS.');
        if(!filter_var(config('brand.support_email'),FILTER_VALIDATE_EMAIL))throw new \RuntimeException('Invalid configuration: SUPPORT_EMAIL');
        $port=filter_var(env('SMTP_PORT',587),FILTER_VALIDATE_INT);if(!$port||$port<1||$port>65535)throw new \RuntimeException('Invalid configuration: SMTP_PORT');
        foreach(['APP_DEBUG','APP_FORCE_HTTPS','PAYPAL_ENABLED','MAIL_LOG_CONTENT'] as $key)if(getenv($key)!==false&&getenv($key)!==''&&!is_bool(env($key)))throw new \RuntimeException('Invalid boolean configuration: '.$key);
        foreach(array_filter(array_map('trim',explode(',',(string)env('TRUSTED_PROXY_IPS','')))) as $ip)if(!filter_var($ip,FILTER_VALIDATE_IP))throw new \RuntimeException('TRUSTED_PROXY_IPS requires explicit IP addresses.');
        date_default_timezone_set((string)env('APP_TIMEZONE','UTC'));
    }
}
