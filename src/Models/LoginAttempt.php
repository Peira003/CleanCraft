<?php

namespace App\Models;

use Config\Database;

class LoginAttempt
{
    private const MAX_PER_EMAIL = 5;
    private const MAX_PER_IP = 20;
    private const WINDOW_MIN = 15;

    public static function isBlocked(string $ip, string $email): bool
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT COUNT(*) AS ip_total,
                                            COALESCE(SUM(email = :email), 0) AS pair_total 
                                            FROM login_attempts 
                                            WHERE ip = :ip 
                                              AND attempted_at > NOW() - INTERVAL " .self::WINDOW_MIN . " MINUTE");
        $stmt->execute(['email' => $email, 'ip' => $ip]);
        $row = $stmt->fetch();

        return (int) $row['pair_total'] >= self::MAX_PER_EMAIL
            || (int) $row['ip_total'] >= self::MAX_PER_IP;
    }

    public static function record(string $ip, string $email): void
    {
        $pdo = Database::getConnection();

        $pdo->prepare("INSERT INTO login_attempts (ip, email) VALUES (:ip, :email)")
            ->execute(['ip' => $ip, 'email' => $email]);

        if (random_int(1, 100) === 1) {
            $pdo->prepare("DELETE FROM login_attempts WHERE ip = :ip AND email = :email")
                ->execute(['ip' => $ip, 'email' => $email]);
        }
    }

    public static function clear(string $ip, string $email): void
    {
        $pdo = Database::getConnection();
        $pdo->prepare("DELETE FROM login_attempts WHERE ip = :ip AND email = :email")
            ->execute(['ip' => $ip, 'email' => $email]);
    }
}