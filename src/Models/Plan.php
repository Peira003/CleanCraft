<?php

namespace App\Models;

use PDO;
use Config\Database;
use PDOException;

class Plan
{
    private const CHARS = 'ABCDEFGHIJKMNPQRSTUVWXYZ23456789';
    private const PALETTE = ['#89CFF0', '#90E0A0', '#F28BB4', '#C3A6FF', '#F6D860', '#6EDCD0', '#FF8A80'];

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
            $planId = (int)$pdo->lastInsertId();

            $stmt = $pdo->prepare("INSERT INTO plan_members (plan_id, user_id, role, color) VALUES (:plan, :user, 'owner', :color)");
            $stmt->execute(['plan' => $planId, 'user' => $userId, 'color' => self::PALETTE[0]]);
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
        $stmt = $pdo->prepare("INSERT INTO plan_members (plan_id, user_id, color) VALUES (:plan, :user, :color)");
        $stmt->execute(['plan' => $planId, 'user' => $userId, 'color' => self::nextColor($pdo, $planId)]);
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

        return (bool)$stmt->fetch();
    }

    public static function members(int $planId): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT u.id, u.name, pm.role, pm.color
                                     FROM plan_members pm
                                     JOIN users u ON u.id = pm.user_id
                                     WHERE pm.plan_id = :plan
                                     ORDER BY pm.role = 'owner' DESC, u.name");
        $stmt->execute(['plan' => $planId]);

        return $stmt->fetchAll();
    }

    public static function removeMember(int $planId, int $userId): void
    {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("DELETE FROM plan_members WHERE plan_id = :plan AND user_id = :user AND role != 'owner'");
            $stmt->execute(['plan' => $planId, 'user' => $userId]);

            if ($stmt->rowCount() > 0) {
                $pdo->prepare("DELETE FROM week_assignments WHERE plan_id = :plan AND user_id = :user")
                    ->execute(['plan' => $planId, 'user' => $userId]);
            }

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    private static function nextColor(PDO $pdo, int $planId): string
    {
        $stmt = $pdo->prepare("SELECT color FROM plan_members WHERE plan_id = :plan");
        $stmt->execute(['plan' => $planId]);
        $used = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach (self::PALETTE as $color) {
            if (!in_array($color, $used, true)) {
                return $color;
            }
        }
        return self::PALETTE[count($used) % count(self::PALETTE)];
    }
}
