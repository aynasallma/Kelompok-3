<?php

require_once __DIR__ . '/../config/config.php';

class Database
{
    private static $pdo = null;

    public static function connect()
    {
        if (self::$pdo === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST .
                       ";port=" . DB_PORT .
                       ";dbname=" . DB_NAME .
                       ";charset=utf8mb4";

                self::$pdo = new PDO(
                    $dsn,
                    DB_USER,
                    DB_PASS,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            } catch (PDOException $e) {
                error_log('Database connection failed: ' . $e->getMessage());
                http_response_code(500);
                die('Layanan sedang tidak tersedia. Silakan coba kembali.');
            }
        }

        return self::$pdo;
    }
}