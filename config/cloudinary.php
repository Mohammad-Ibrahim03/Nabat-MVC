<?php
declare(strict_types=1);
require_once __DIR__ . '/../app/Core/Env.php';

return [
    'cloud_name' => app_env('CLOUDINARY_CLOUD_NAME', ''),
    'api_key' => app_env('CLOUDINARY_API_KEY', ''),
    'api_secret' => app_env('CLOUDINARY_API_SECRET', ''),
];
