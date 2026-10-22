<?php

namespace App\Models;

use PDO;
use Config\Database;
use PDOException;

class Plan
{
    private const CHARS = 'ABCDEFGHIJKMNPQRSTUVWXYZ23456789';

    private static function generateCode(PDO $pdo, int $length = 6): string
    {
        do {
            $code = '';
            for ($i = 0; $i < $length; ++$i) {
                $code .= self::CHARS[mt_rand(0, strlen(self::CHARS) - 1)];
            }
            $stmt = $pdo->prepare("SELECT 1 FROM plans WHERE invite_code = :code");
            $stmt->execute(['code' => $code]);
        } while ($stmt->fetch());
        return $code;
    }

    public static function create(string $name, int $userId): int
    {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare("INSERT INTO plans (name, invite_code) VALUES (:name, :code)");
            $stmt->execute(['name' => $name, 'code' => self::generateCode($pdo)]);
            $planId = (int) $pdo->lastInsertId();

            $stmt = $pdo->prepare("INSERT INTO plan_members (plan_id, user_id, role) VALUES (:plan, :user, 'owner')");
            $stmt->execute(['plan' => $planId, 'user' => $userId]);
            $pdo->commit();
            return $planId;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function findById(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM plans WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public static function findByCode(string $code): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM plans WHERE invite_code = :code LIMIT 1");
        $stmt->execute(['code' => strtoupper(trim($code))]);

        return $stmt->fetch() ?: null;
    }

    public static function addMember(int $planId, int $userId): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO plan_members (plan_id, user_id) VALUES (:plan, :user)");
        $stmt->execute(['plan' => $planId, 'user' => $userId]);
    }

    public static function forUser(int $userId): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT p.*, pm.role FROM plans p JOIN plan_members pm ON pm.plan_id = p.id WHERE pm.user_id = :user ORDER BY p.name");
        $stmt->execute(['user' => $userId]);

        return $stmt->fetchAll();
    }

    public static function isOwner(int $planId, int $userId): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT 1 FROM plan_members WHERE plan_id = :plan AND user_id = :user AND role = 'owner'");
        $stmt->execute(['plan' => $planId, 'user' => $userId]);

        return (bool) $stmt->fetch();
    }

    public static function members(int $planId): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT u.id, u.name, pm.role
                                     FROM plan_members pm
                                     JOIN users u ON u.id = pm.user_id
                                     WHERE pm.plan_id = :plan
                                     ORDER BY pm.role = 'owner' DESC, u.name");
        $stmt->execute(['plan' => $planId]);

        return $stmt->fetchAll();
    }
}