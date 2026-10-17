<?php


namespace App\Models;

use PDO;
use Config\Database;
class User
{
    public static function findByEmail(string $email): ?array
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);

        $user = $stmt->fetch();
        return $user ?: null;
    }
}
