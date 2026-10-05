<?php


namespace Models;

use PDO;
use Config\Database;
class User
{
    public function findByEmail(string $email): ?array
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);

        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function verifyPassword(string $password, string $hashPassword): bool {
        return password_verify($password, $hashPassword);
    }
}
