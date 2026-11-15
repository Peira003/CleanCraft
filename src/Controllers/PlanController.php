<?php

namespace App\Controllers;

use App\ErrorPage;
use App\Models\LoginAttempt;
use App\Models\Plan;
use App\Csrf;
use App\Models\Schedule;

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
        $userId = $this->requireLogin();
        $this->joinWithCode($userId, $_POST['code'] ?? '');
    }

    public function joinByLink(string $code): void
    {
        if (empty($_SESSION['userId'])) {
            $_SESSION['pending_join_code'] = $code;
            $this->redirect('/login');
        }

        $plan = Plan::findByCode($code);
        if (!$plan) {
            ErrorPage::show(404, 'Nieprawidłowy link', 'Ten kod zaproszenia nie istnieje albo został unieważniony.');
        }

        if (Plan::isMember((int) $plan['id'], (int) $_SESSION['userId'])) {
            $this->redirect('/plan/' . $plan['id']);
        }

        $inviteCode = strtoupper(trim($code));
        require __DIR__ . '/../../views/plans/confirm_join.php';
    }

    public function confirmJoin(string $code): void
    {
        $userId = $this->requireLogin();
        $this->joinWithCode($userId, $code);
    }

    public function regenerateCode(string $id): void
    {
        $userId = $this->requireLogin();
        $planId = (int) $id;

        if (!Plan::isOwner($planId, $userId)) {
            ErrorPage::show(403, 'Brak uprawnień', 'Tylko właściciel może zmienić kod zaproszenia.');
        }

        Plan::regenerateCode($planId);
        $this->redirect('/plan/' . $planId);
    }

    private function joinWithCode(int $userId, string $code): never
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';

        if (LoginAttempt::isBlocked('join', $ip)) {
            $_SESSION['form_error'] = 'Zbyt wiele błędnych kodów. Spróbuj ponownie za 15 minut.';
            $this->redirect('/plans/join');
        }

        $plan = Plan::findByCode($code);

        if (!$plan) {
            LoginAttempt::record('join', $ip);
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

        $plan = Plan::findById($planId);
        $members = Plan::members($planId);

        if(!$plan || !in_array($userId, array_column($members, 'id'), true)) {
            ErrorPage::show(404, 'Nie znaleziono planu','Ten plan nie istnieje albo nie masz do niego dostępu.');
        }

        $users = $members;
        $isOwner = Plan::isOwner($planId, $userId);
        $schedule = Schedule::forPlan($planId);

        require __DIR__ . '/../../views/dashboard.php';
    }

    public function removeMember(string $id, string $memberId): void
    {
        $userId = $this->requireLogin();
        $planId = (int) $id;
        $targetId = (int) $memberId;

        if (!Plan::isOwner($planId, $userId) || $targetId === $userId) {
            ErrorPage::show(403, 'Brak uprawnień', 'Nie posiadasz uprawnień do tej akcji.');
        }

        Plan::removeMember($planId, $targetId);
        $this->redirect('/plan/' . $planId);
    }

    public function leavePlan(string $id): void
    {
        $userId = $this->requireLogin();
        Plan::removeMember((int) $id, $userId);
        $this->redirect('/plans');
    }

    public function setWeek(string $id): void
    {
        $userId = $this->requireLogin();
        $planId = (int) $id;

        if (!Plan::isOwner($planId, $userId)) {
            $this->json(['ok' => false], 403);
        }

        $week = $_POST['week'] ?? '';
        $target = (int) ($_POST['user_id'] ?? 0);

        $date = \DateTime::createFromFormat('Y-m-d', $week);
        if (!$date || $date->format('Y-m-d') !== $week || $date->format('N') !== '1') {
            $this->json(['ok' => false], 422);
        }

        if ($target === 0) {
            Schedule::clear($planId,$week);
        } else {
            $memberIds = array_column(Plan::members($planId), 'id');
            if (!in_array($target, array_map('intval', $memberIds), true)) {
                $this->json(['ok' => false], 422);
            }
            Schedule::set($planId, $week, $target);
        }

        $this->json(['ok' => true]);
    }

    public function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

}