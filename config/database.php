<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/Env.php';

return [
    'host' => app_env('DB_HOST', 'localhost'),
    'name' => app_env('DB_NAME', app_env('DB_DATABASE', 'nabta_store')),
    'dbname' => app_env('DB_NAME', app_env('DB_DATABASE', 'nabta_store')),
    'username' => app_env('DB_USER', 'root'),
    'password' => app_env('DB_PASSWORD', ''),
    'charset' => 'utf8mb4',
];
