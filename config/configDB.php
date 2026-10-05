<?php

namespace Config;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if(self::$instance == null) {
            $config = require __DIR__ . '/database.php';

            try {
                self::$instance = new PDO(
                    "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4",
                    $config['user'],
                    $config['password'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            } catch (PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                exit("Database connection failed. Please try again later.");
            }
        }
        return self::$instance;
    }
}

