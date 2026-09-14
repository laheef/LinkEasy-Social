<?php
/** CLI-only configuration audit. No external calls and no secret values printed. */
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require dirname(__DIR__) . '/src/bootstrap.php';
$failures = 0;
function audit(bool $ok, string $message): void {
    global $failures;
    echo ($ok ? '[PASS] ' : '[ACTION] ') . $message . PHP_EOL;
    if (!$ok) $failures++;
}
audit(PHP_VERSION_ID >= 80200, 'PHP 8.2 or newer');
foreach (['pdo', 'curl', 'openssl', 'mbstring'] as $ext) audit(extension_loaded($ext), 'PHP extension: ' . $ext);
$dsn = getenv('DATABASE_DSN') ?: 'sqlite:' . LES_BASE_PATH . '/storage/linkeasy.sqlite';
audit(extension_loaded(str_starts_with($dsn, 'mysql:') ? 'pdo_mysql' : 'pdo_sqlite'), 'Configured PDO database driver');
audit(env('APP_ENV') === 'production' && env('APP_DEBUG') === false, 'Production mode with debug disabled');
audit(filter_var(config('brand.app_url'), FILTER_VALIDATE_URL) && str_starts_with(config('brand.app_url'), 'https://'), 'APP_URL is a valid HTTPS URL');
audit(is_writable(LES_BASE_PATH . '/storage') && is_writable(LES_BASE_PATH . '/storage/logs'), 'Runtime storage is writable');
foreach (['public/assets/css/landing.min.css', 'public/assets/css/platforms.min.css', 'public/assets/js/main.min.js', 'public/.htaccess'] as $file) audit(is_file(LES_BASE_PATH . '/' . $file), 'Packaged file: ' . $file);
$driver = getenv('MAIL_DRIVER') ?: 'log';
audit($driver === 'smtp', 'Email delivery enabled (log driver does not send email)');
if ($driver === 'smtp') {
    foreach (['SMTP_HOST','SMTP_USER','SMTP_PASS'] as $key) audit((bool) getenv($key), $key . ' configured');
    audit(in_array(getenv('SMTP_SECURE'), ['ssl','tls'], true), 'SMTP transport is encrypted');
}
foreach ([
    'Google login' => ['GOOGLE_CLIENT_ID','GOOGLE_CLIENT_SECRET'],
    'PayPal billing' => ['PAYPAL_CLIENT_ID','PAYPAL_CLIENT_SECRET','PAYPAL_WEBHOOK_ID','PAYPAL_PLAN_MONTHLY_ID','PAYPAL_PLAN_YEARLY_ID'],
] as $service => $keys) {
    if ($service === 'PayPal billing' && !env('PAYPAL_ENABLED',false)) echo '[DISABLED] PAYPAL_ENABLED is false: live checkout is deliberately gated.' . PHP_EOL;
    $count = count(array_filter($keys, fn ($key) => (bool) getenv($key)));
    if ($count === 0) echo '[DISABLED] ' . $service . ' has no credentials. Configure and test if offered at launch.' . PHP_EOL;
    else audit($count === count($keys), $service . ' configuration complete');
}
$drafts = array_filter(require LES_BASE_PATH . '/config/public-pages.php', fn ($page) => !empty($page['review']));
audit(count($drafts) === 0, 'Legal policy drafts reviewed and adopted by the operator');
audit(env('APP_FORCE_HTTPS',true) === true, 'Application HTTPS enforcement enabled (except local dev server)');
audit(env('MAIL_LOG_CONTENT',false) === false, 'Sensitive development mail-body logging disabled');
echo '[MANUAL] Run php tools/migrate.php after backup; configure cron for tools/worker.php and monitor queued/dead jobs. Configuration audit does not prove cron execution.' . PHP_EOL;
echo '[MANUAL] Connect and test the existing social-management backend. This dashboard is currently its integration scaffold.' . PHP_EOL;
echo '[MANUAL] Test TLS, email arrival/password reset, OAuth redirects, sandbox/live billing and webhook replay on your actual host.' . PHP_EOL;
echo '[MANUAL] Verify database access, backups, scheduled publishing worker and platform permissions in the existing app.' . PHP_EOL;
echo 'This audit checks configuration presence only. It is not a security or production certification.' . PHP_EOL;
exit($failures ? 1 : 0);
