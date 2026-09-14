<?php

namespace App\Services;

/**
 * Transactional email.
 *
 * Drivers (MAIL_DRIVER):
 *   - log   : append to storage/logs/mail.log (default; safe for dev)
 *   - mail  : PHP's mail() (works on some hosts, poor deliverability)
 *   - smtp  : dependency-free raw-socket SMTP with AUTH LOGIN and TLS,
 *             configured via SMTP_HOST/PORT/USER/PASS (use this on
 *             Hostinger: smtp.hostinger.com, port 465 SSL or 587 STARTTLS).
 */
final class Mailer
{
    public static function send(string $to, string $subject, string $htmlBody, string $textBody = ''): bool
    {
        $driver = strtolower((string) (getenv('MAIL_DRIVER') ?: 'log'));
        $from = getenv('MAIL_FROM_ADDRESS') ?: config('brand.support_email');
        $fromName = getenv('MAIL_FROM_NAME') ?: config('brand.name');

        if (!filter_var($to, FILTER_VALIDATE_EMAIL) || !filter_var($from, FILTER_VALIDATE_EMAIL)
            || preg_match('/[\r\n]/', $to . $from . $subject . $fromName)) {
            \App\Logger::event('error','smtp_configuration_or_transport_failure');
            return false;
        }
        return match ($driver) {
            'smtp' => self::sendSmtp($to, $subject, $htmlBody, $textBody, $from, $fromName),
            'mail' => false, // Use SMTP: native mail() has no bounded transport deadline.
            'log' => self::sendLog($to, $subject, $htmlBody, $textBody),
            default => false,
        };
    }

    private static function sendMail(string $to, string $subject, string $htmlBody, string $from, string $fromName): bool
    {
        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . self::encodeName($fromName) . ' <' . $from . '>',
        ];
        return @mail($to, self::encodeHeader($subject), $htmlBody, implode("\r\n", $headers));
    }

    private static function sendLog(string $to, string $subject, string $htmlBody, string $textBody): bool
    {
        // Development payload logging is explicitly opt-in and prohibited in production.
        if (env('APP_ENV') === 'production' || !env('MAIL_LOG_CONTENT',false)) {
            \App\Logger::event('warning','mail_log_driver_no_delivery');
            return false; // Never report an unsent production message as sent.
        }
        $line = '[' . gmdate('c') . '] ' . $subject . "\n" . ($textBody ?: strip_tags($htmlBody)) . "\n---\n";
        $file=LES_BASE_PATH.'/storage/logs/mail.log';
        $fp=@fopen($file,'ab');if(!$fp)return false;
        if(!flock($fp,LOCK_EX)){fclose($fp);return false;}
        if(fstat($fp)['size']>5*1024*1024)ftruncate($fp,0);
        $ok=fwrite($fp,$line)!==false;flock($fp,LOCK_UN);fclose($fp);return $ok;
    }

    /**
     * Minimal SMTP client (no Composer deps). SMTP_SECURE=tls|ssl; port 465
     * implies implicit SSL, 587 uses STARTTLS.
     */
    private static function sendSmtp(
        string $to, string $subject, string $htmlBody, string $textBody,
        string $from, string $fromName
    ): bool {
        $host = (string) getenv('SMTP_HOST');
        $port = (int) (getenv('SMTP_PORT') ?: 587);
        $user = (string) getenv('SMTP_USER');
        $pass = (string) getenv('SMTP_PASS');
        $secure = strtolower((string) (getenv('SMTP_SECURE') ?: ($port === 465 ? 'ssl' : 'tls')));
        if (!in_array($secure, ['ssl', 'tls'], true)) {
            \App\Logger::event('error','smtp_configuration_or_transport_failure');
            return false;
        }
        if (!filter_var($user, FILTER_VALIDATE_EMAIL) || preg_match('/[\r\n]/', $user)) {
            \App\Logger::event('error','smtp_configuration_or_transport_failure');
            return false;
        }
        if ($host === '' || $user === '') {
            \App\Logger::event('error','smtp_configuration_or_transport_failure');
            return false;
        }

        $remote = ($secure === 'ssl' ? 'ssl://' : 'tcp://') . $host;
        $errno = 0; $errstr = '';
        $context=stream_context_create(['ssl'=>['verify_peer'=>true,'verify_peer_name'=>true,'allow_self_signed'=>false,'peer_name'=>$host]]);
        $fp = @stream_socket_client($remote.':'.$port, $errno, $errstr, 5, STREAM_CLIENT_CONNECT, $context);
        if ($fp === false) {
            \App\Logger::event('error','smtp_connect_failed');
            return false;
        }
        self::$deadline=microtime(true)+30;
        stream_set_timeout($fp, 5);

        try {
            self::expect($fp, [220]);
            $ehlo = parse_url((string) config('brand.app_url'), PHP_URL_HOST) ?: 'localhost';
            self::cmd($fp, 'EHLO ' . $ehlo, [250]);

            if ($secure === 'tls') {
                self::cmd($fp, 'STARTTLS', [220]);
                if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT |
                    STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT)) {
                    \App\Logger::event('error','smtp_configuration_or_transport_failure');
                    return false;
                }
                self::cmd($fp, 'EHLO ' . $ehlo, [250]);
            }

            self::cmd($fp, 'AUTH LOGIN', [334]);
            self::cmd($fp, base64_encode($user), [334]);
            self::cmd($fp, base64_encode($pass), [235]);

            self::cmd($fp, 'MAIL FROM:<' . $user . '>', [250]);
            self::cmd($fp, 'RCPT TO:<' . $to . '>', [250, 251]);
            self::cmd($fp, 'DATA', [354]);

            $boundary = 'les_' . bin2hex(random_bytes(8));
            $textBody = $textBody !== '' ? $textBody : trim(strip_tags($htmlBody));
            $message = implode("\r\n", [
                'Date: ' . date('r'),
                'From: ' . self::encodeName($fromName) . ' <' . $from . '>',
                'To: <' . $to . '>',
                'Subject: ' . self::encodeHeader($subject),
                'MIME-Version: 1.0',
                'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
                '',
                '--' . $boundary,
                'Content-Type: text/plain; charset=UTF-8',
                'Content-Transfer-Encoding: 8bit',
                '',
                self::dotStuff($textBody),
                '--' . $boundary,
                'Content-Type: text/html; charset=UTF-8',
                'Content-Transfer-Encoding: 8bit',
                '',
                self::dotStuff($htmlBody),
                '--' . $boundary . '--',
                '.',
            ]);
            fwrite($fp, $message . "\r\n");
            self::expect($fp, [250]);
            self::cmd($fp, 'QUIT', [221]);
        } catch (\RuntimeException $e) {
            \App\Logger::exception($e,'smtp_failure');
            return false;
        } finally {
            fclose($fp);
        }
        return true;
    }

    private static function cmd($fp, string $command, array $okCodes): string
    {
        fwrite($fp, $command . "\r\n");
        return self::expect($fp, $okCodes);
    }

    private static float $deadline = 0;

    private static function expect($fp, array $okCodes): string
    {
        $response = '';
        $bytes=0;
        while (true) {
            $remaining=self::$deadline-microtime(true);if($remaining<=0)throw new \RuntimeException('SMTP deadline exceeded.');
            stream_set_timeout($fp,max(1,(int)min(5,$remaining)));
            $line=fgets($fp,515);if($line===false)break;
            $bytes+=strlen($line);if($bytes>16384)throw new \RuntimeException('SMTP response too large.');
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break; // final line of an SMTP reply
            }
        }
        $code = (int) substr($response, 0, 3);
        if (!in_array($code, $okCodes, true)) {
            throw new \RuntimeException(trim($response));
        }
        return $response;
    }

    private static function dotStuff(string $body): string
    {
        return str_replace("\n", "\r\n", preg_replace('/^\./m', '..', str_replace(["\r\n", "\r"], "\n", $body)));
    }

    private static function encodeName(string $name): string
    {
        return '=?UTF-8?B?' . base64_encode($name) . '?=';
    }

    private static function encodeHeader(string $value): string
    {
        return self::encodeName($value);
    }
}
