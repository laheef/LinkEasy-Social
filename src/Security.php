<?php
namespace App;
final class Security
{
    private static string $nonce='';
    public static function nonce(): string { return self::$nonce ?: (self::$nonce=base64_encode(random_bytes(24))); }
    public static function https(): bool {
        if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off') || (string)($_SERVER['SERVER_PORT']??'')==='443') return true;
        $trusted=array_filter(array_map('trim',explode(',',(string)env('TRUSTED_PROXY_IPS',''))));
        return in_array($_SERVER['REMOTE_ADDR']??'', $trusted,true) && ($_SERVER['HTTP_X_FORWARDED_PROTO']??'')==='https';
    }
    public static function initialize(): void {
        ini_set('display_errors', env('APP_DEBUG',false) && env('APP_ENV')!=='production' ? '1':'0');
        ini_set('log_errors','1');
        umask(0077);
        set_exception_handler(function(\Throwable $e): void {
            Logger::exception($e,'uncaught_exception');
            if (PHP_SAPI==='cli') { fwrite(STDERR,"Operation failed. Check the private structured log.\n");exit(1); }
            if (!headers_sent()) { http_response_code(500);header('Content-Type: text/html; charset=utf-8');header('Cache-Control: no-store'); }
            echo '<!doctype html><title>Request could not be completed</title><h1>Something went wrong.</h1><p>Please try again later. Reference: '.e(Logger::requestId()).'</p>';
        });
        register_shutdown_function(function(): void {
            $error=error_get_last();
            if ($error && in_array($error['type'],[E_ERROR,E_PARSE,E_CORE_ERROR,E_COMPILE_ERROR],true)) Logger::event('error','fatal_error',['file'=>basename($error['file']),'line'=>$error['line']]);
        });
        RuntimeConfig::validate();
        if (PHP_SAPI==='cli') return;
        header_remove('X-Powered-By');
        header('X-Request-ID: '.Logger::requestId());
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: no-referrer');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
        header('Cache-Control: private, no-store');
        $csp="default-src 'self'; base-uri 'none'; object-src 'none'; script-src 'self' 'nonce-".self::nonce()."'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self'; connect-src 'self'; form-action 'self' https://www.paypal.com https://www.sandbox.paypal.com";
        // Arena embeds the dev preview. Real web deployments prohibit framing.
        if (PHP_SAPI!=='cli-server') { $csp.="; frame-ancestors 'none'";header('X-Frame-Options: DENY'); }
        header('Content-Security-Policy: '.$csp);
        if (self::https()) header('Strict-Transport-Security: max-age=31536000');
        if (PHP_SAPI!=='cli-server' && env('APP_FORCE_HTTPS',true) && !self::https()) {
            $origin=rtrim((string)config('brand.app_url'),'/');
            if (!filter_var($origin,FILTER_VALIDATE_URL)||parse_url($origin,PHP_URL_SCHEME)!=='https') Request::reject(503,'HTTPS configuration is required.');
            $path=$_SERVER['REQUEST_URI']??'/';
            header('Location: '.$origin.(str_starts_with($path,'/')?$path:'/'),true,308);exit;
        }
        Request::guard();
    }
}
