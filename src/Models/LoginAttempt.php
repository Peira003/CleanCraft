<?php

namespace App\Models;

use Config\Database;

class LoginAttempt
{
    private const LIMITS = [
        'login'    => ['pair' => 5, 'ip' => 20, 'window' => 15],
        'register' => ['pair' => 0, 'ip' => 10, 'window' => 65],
        'join'     => ['pair' => 0, 'ip' => 10, 'window' => 15],
    ];

    public static function isBlocked(string $type, string $ip, string $key = ''): bool
    {
        $cfg = self::LIMITS[$type];
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT COUNT(*) AS ip_total,
                                            COALESCE(SUM(email = :email), 0) AS pair_total 
                                            FROM login_attempts 
                                            WHERE type = :type
                                              AND ip = :ip
                                              AND attempted_at > NOW() - INTERVAL " . (int) $cfg['window'] . " MINUTE");
        $stmt->execute(['email' => $key, 'type' => $type, 'ip' => $ip]);
        $row = $stmt->fetch();

        if ((int) $row['ip_total'] >= $cfg['ip']) {
            return true;
        }

        return $cfg['pair'] > 0 && (int) $row['pair_total'] >= $cfg['pair'];
    }

    public static function record(string $type, string $ip, string $key = ''): void
    {
        $pdo = Database::getConnection();

        $pdo->prepare("INSERT INTO login_attempts (type, ip, email) VALUES (:type, :ip, :email)")
            ->execute(['type' => $type, 'ip' => $ip, 'email' => $key]);

        if (random_int(1, 100) === 1) {
            $pdo->exec("DELETE FROM login_attempts WHERE attempted_at < NOW() - INTERVAL 1 DAY");
        }
    }

    public static function clear(string $type ,string $ip, string $key = ''): void
    {
        $pdo = Database::getConnection();
        $pdo->prepare("DELETE FROM login_attempts WHERE type = :type AND ip = :ip AND email = :email")
            ->execute(['type' => $type, 'ip' => $ip, 'email' => $key]);
    }
}