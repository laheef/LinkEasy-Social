<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require dirname(__DIR__) . '/src/bootstrap.php';
$applied=App\Migrations::run(App\Database::pdo());
echo $applied ? 'Applied: '.implode(', ',$applied).PHP_EOL : "Schema is current.\n";
