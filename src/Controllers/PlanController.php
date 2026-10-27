<?php

namespace App\Controllers;

use App\Models\Plan;
use App\Csrf;

class PlanController
{
    private function requireLogin(): int
    {
        if (empty($_SESSION['userId'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
        return (int) $_SESSION['userId'];
    }

    private function redirect(string $path): never
    {
        header('Location: ' . BASE_URL . $path);
        exit;
    }

    public function chooser(): void
    {
        $userId = $this->requireLogin();
        $plany = Plan::forUser($userId);

        require __DIR__ . '/../../views/plans/chooser.php';
    }

    public function showCreate(): void
    {
        $this->requireLogin();
        require __DIR__ . '/../../views/plans/create.php';
    }

    public function create(): void
    {
        Csrf::verify();

        $userId = $this->requireLogin();
        $name = trim($_POST['name'] ?? '');

        if ($name === '' || mb_strlen($name) > 120) {
            $_SESSION['form_error'] = 'Podaj nazwę planu (maks. 120 znaków).';
            $this->redirect('/plans/create');
        }

        $planId = Plan::create($name, $userId);
        $this->redirect('/plan/' . $planId);
    }

    public function showJoin(): void
    {
        $this->requireLogin();
        require __DIR__ . '/../../views/plans/join.php';
    }

    public function join(): void
    {
        Csrf::verify();

        $userId = $this->requireLogin();
        $this->joinWithCode($userId, $_POST['code'] ?? '');
    }

    public function joinByLink(string $code): void
    {
        if (empty($_SESSION['userId'])) {
            $_SESSION['pending_join_code'] = $code;
            $this->redirect('/login');
        }
        $this->joinWithCode((int) $_SESSION['userId'], $code);
    }

    private function joinWithCode(int $userId, string $code): never
    {
        $plan = Plan::findByCode($code);

        if (!$plan) {
            $_SESSION['form_error'] = 'Nie znaleziono planu o takim kodzie';
            $this->redirect('/plans/join');
        }

        Plan::addMember((int) $plan['id'], $userId);
        $this->redirect('/plan/' . $plan['id']);
    }

    public function show(string $id): void
    {
        $userId = $this->requireLogin();
        $planId = (int) $id;

        $members = Plan::members($planId);
        if(!in_array($userId, array_column($members, 'id'), true)){
            $this->redirect('/plans');
        }

        $plan = Plan::findById($planId);
        $users = $members;
        $isOwner = Plan::isOwner($planId, $userId);

        require __DIR__ . '/../../views/dashboard.php';
    }

    public function removeMember(string $id, string $memberId): void
    {
        $userId = $this->requireLogin();
        Csrf::verify();
        $planId = (int) $id;
        $targetId = (int) $memberId;

        if (!Plan::isOwner($planId, $userId) || $targetId === $userId) {
            http_response_code(403);
            exit('Brak uprawnień');
        }

        Plan::removeMember($planId, $targetId);
        $this->redirect('/plan/' . $planId);
    }

    public function leavePlan(string $id): void
    {
        $userId = $this->requireLogin();
        Csrf::verify();

        Plan::removeMember((int) $id, $userId);
        $this->redirect('/plans');
    }

}