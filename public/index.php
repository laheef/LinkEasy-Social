<?php
/**
 * LinkEasy Social — single entry point (front controller).
 * Apache: .htaccess rewrites everything here. Nginx: try_files $uri /index.php.
 */

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

require LES_BASE_PATH . '/routes/web.php';

$router->dispatch(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'
);
