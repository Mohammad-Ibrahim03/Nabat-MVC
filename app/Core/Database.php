<?php
declare(strict_types=1);

require_once __DIR__ . '/Env.php';

/** One PDO connection shared by all controllers and models. */
class Database
{
    private static ?PDO $shared = null;
    private PDO $conn;

    public function __construct()
    {
        $this->conn = self::connection();
    }

    public static function connection(): PDO
    {
        if (self::$shared instanceof PDO) {
            return self::$shared;
        }

        $config = require dirname(__DIR__, 2) . '/config/database.php';
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $config['host'],
            $config['dbname'],
            $config['charset'] ?? 'utf8mb4'
        );

        self::$shared = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ]);
        return self::$shared;
    }

    public function connect(): PDO
    {
        return $this->conn;
    }
}
