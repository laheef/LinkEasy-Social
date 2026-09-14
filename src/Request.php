<?php
namespace App;
/** Reject malformed scalar boundaries before controllers/views can cast them. */
final class Request
{
    public static function guard(): void {
        $path=parse_url($_SERVER['REQUEST_URI'] ?? '/',PHP_URL_PATH) ?: '/';
        $max=$path==='/billing/paypal/webhook' ? 262144 : 32768;
        if ((int)($_SERVER['CONTENT_LENGTH'] ?? 0)>$max) self::reject(413,'This request is too large.');
        if (!empty($_FILES)) self::reject(400,'File uploads are not accepted by this endpoint.');
        foreach ([$_GET,$_POST] as $input) foreach ($input as $key=>$value) {
            $multi=$path==='/contact' && in_array($key,['platforms','services'],true);
            if ($multi && is_array($value)) {
                if (count($value)>60) self::reject(400,'Invalid selection.');
                foreach($value as $part) if (!is_string($part)||strlen($part)>150) self::reject(400,'Invalid selection.');
            } elseif (!is_string($value) || strlen($value)>8192 || str_contains($value,"\0")) self::reject(400,'Invalid form input.');
        }
    }
    public static function clientIp(): string {
        $remote=$_SERVER['REMOTE_ADDR']??'cli';
        $trusted=array_filter(array_map('trim',explode(',',(string)env('TRUSTED_PROXY_IPS',''))));
        if(!in_array($remote,$trusted,true))return $remote;
        $chain=array_map('trim',explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']??''));
        if(count($chain)>20)return $remote;
        foreach($chain as $ip)if(!filter_var($ip,FILTER_VALIDATE_IP))return $remote;
        $chain[]=$remote;
        while(count($chain)>1 && in_array(end($chain),$trusted,true))array_pop($chain);
        return end($chain);
    }
    public static function text(array $input,string $key,string $default=''): string { return is_string($input[$key]??null)?$input[$key]:$default; }
    public static function next(mixed $value): string {
        if (!is_string($value)||strlen($value)>2048) return url('dashboard');
        $decoded=rawurldecode($value);
        if (!str_starts_with($decoded,'/')||str_starts_with($decoded,'//')||preg_match('/[\\\\\x00-\x20\x7f]/',$decoded)) return url('dashboard');
        return $value;
    }
    public static function reject(int $status,string $message): never { les_render_error($status,$message);exit; }
}
