<?php
declare(strict_types=1);

/** Read environment variables, falling back to the private project-root .env file. */
function app_env(string $key, ?string $default = null): string
{
    static $fileValues = null;
    $value = getenv($key);
    if ($value !== false && $value !== '') {
        return (string) $value;
    }

    if ($fileValues === null) {
        $file = dirname(__DIR__, 2) . '/.env';
        $fileValues = is_readable($file) ? (parse_ini_file($file, false, INI_SCANNER_RAW) ?: []) : [];
    }

    if (array_key_exists($key, $fileValues) && $fileValues[$key] !== '') {
        return trim((string) $fileValues[$key], " \t\n\r\0\x0B\"'");
    }
    return $default ?? '';
}
