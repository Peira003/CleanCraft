<?php

namespace App\Models;

use Config\Database;

class Schedule
{
    public static function forPlan(int $planId): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT week_start, user_id FROM week_assignments WHERE plan_id = :plan");
        $stmt->execute(["plan" => $planId]);

        return array_map('intval', $stmt->fetchAll(\PDO::FETCH_KEY_PAIR));
    }

    public static function set(int $planId, string $weekStart, int $userId): void
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare(
            "INSERT INTO week_assignments (plan_id, week_start, user_id) VALUES (:plan, :week, :user)
                   ON DUPLICATE KEY UPDATE user_id = VALUES(user_id)");
        $stmt->execute(["plan" => $planId, "week" => $weekStart, "user" => $userId]);
    }

    public static function clear(int $planId, string $weekStart): void
    {
        $pdo = Database::getConnection();
        $pdo->prepare("DELETE FROM week_assignments WHERE plan_id = :plan AND week_start = :week")
                    ->execute(["plan" => $planId, "week" => $weekStart]);
    }
}