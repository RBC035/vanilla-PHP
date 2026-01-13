<?php

namespace Config;

use PDO;
use PDOException;

class Database
{
    private static $connection = null;

    public static function getConnection()
    {
        if (self::$connection === null) {
            try {
                // Get vars from Env
                $host = getenv('DB_HOST');
                $db   = getenv('DB_NAME');
                $user = getenv('DB_USER');
                $pass = getenv('DB_PASS');
                $port = getenv('DB_PORT') ?: '3306';

                $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    // Useful for remote connections to prevent hanging
                    PDO::ATTR_TIMEOUT            => 5,
                ];

                self::$connection = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                header('Content-Type: application/json');
                http_response_code(500);

                echo json_encode([
                    "error" => "Database connection failed",
                    "details" => $e->getMessage()
                ]);
                exit;
            }
        }
        return self::$connection;
    }
}
