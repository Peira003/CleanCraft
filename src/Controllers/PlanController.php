<?php

namespace App\Controllers;

use App\Models\Plan;

class PlanController
{
    private function requireLogin(): int
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
        return (int) $_SESSION['user_id'];
    }

    public function chooser(): void
    {
        $userId = $this->requireLogin();
        $plany = Plan::forUser($userId);
        require __DIR__ . '/../../views/plans/chooser.php';
    }

    public function create(): void
    {
        $userId = $this->requireLogin();
        $name = trim($_POST['name'] ?? '');

        if ($name === '' || mb_strlen($name) > 120) {
            header('Location: ' . BASE_URL . '/plans/create');
            exit();
        }

        $planId = Plan::create($name, $userId);
        header('Location: ' . BASE_URL . '/plans/' . $planId);
        exit();
    }

    public function join(): void
    {
        $userId = $this->requireLogin();
        $this->dolacz($userId, $_POST['kod'] ?? '');
    }

    public function joinByLink(string $kod): void
    {
        if (empty($_SESSION['user_id'])) {
            $_SESSION['pending_join_code'] = $kod;
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
        $this->dolacz((int) $_SESSION['user_id'], $kod);
    }

    private function dolacz(int $userId, string $kod): void
    {
        $plan = Plan::findByCode($kod);

        if (!$plan) {
            header('Location: ' . BASE_URL . '/plans/join');
            exit();
        }

        Plan::addMember((int) $plan['id'], $userId);
        header('Location: ' . BASE_URL . '/plans/' . $plan['id']);
        exit();
    }

    public function show(string $id): void
    {
        $userId = $this->requireLogin();
        $planId = (int) $id;

        $members = Plan::members($planId);
        if(!in_array($userId, array_column($members, 'id'), true)){
            http_response_code(403);
            echo "Brak dostępu do tego planu.";
            return;
        }

        $users = $members;
        $isOwner = Plan::isOwner($planId, $userId);
        require __DIR__ . '/../../views/dashboard.php';
    }

}