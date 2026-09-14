<?php
/**
 * Router for PHP's built-in development server only:
 *   php -S 0.0.0.0:8080 -t public public/router.php
 * Static assets are served directly; everything else goes to index.php.
 */

declare(strict_types=1);

$uri = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
$root=realpath(__DIR__);
$file=realpath(__DIR__ . $uri);
if ($file && str_starts_with($file,$root.DIRECTORY_SEPARATOR) && !str_contains($uri,'..') && !preg_match('~(^|/)[.]~',$uri) && is_file($file) && preg_match('/\.(css|js|png|jpg|jpeg|webp|svg|ico|woff2|mp4|webm|webmanifest|txt)$/i',$file)) {
    return false;
}
require __DIR__ . '/index.php';
