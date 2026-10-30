<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/Core/Env.php';

return [
    // This is a public OAuth client ID, not a client secret.
    'client_id' => app_env('GOOGLE_CLIENT_ID', ''),
];
