<?php

namespace App\Controllers;


use App\Models\LoginAttempt;
use App\Models\User;
use App\Csrf;

class AuthController
{
    public function showLoginForm(): void
    {
        require_once __DIR__ . '/../../views/login.php';
    }

    public function showRegisterForm(): void
    {
        require_once __DIR__ . '/../../views/register.php';
    }

    public function login(): void
    {

        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $emailKey = mb_substr(strtolower(trim($_POST['email'] ?? '')), 0, 255);

        if (LoginAttempt::isBlocked($ip, $emailKey)) {
            $_SESSION['form_error'] = 'Zbyt wiele nieudanych prób logowania. Spróbuj ponownie za 15 minut.';
            header('Location: ' . BASE_URL . '/login');
            exit();
        }

        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        $user = $email ? User::findByEmail($email) : null;

        if (!$email || $password === '' || !$user || !password_verify($password, $user['password'])) {
            LoginAttempt::recrod($ip, $emailKey);
            $_SESSION['form_error'] = 'Nieprawidłowy e-mail lub hasło.';
            header('Location: ' . BASE_URL . '/login');
            exit();
        }

        LoginAttempt::clear($ip, $emailKey);
        $this->startSession($user);
        $this->redirectAfterLogin();
    }

    public function register(): void
    {
        $name = trim($_POST['name'] ?? '');
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';

        $error = null;
        if ($name === '' || mb_strlen($name) > 100) {
            $error = 'Podaj imię (maks. 100 znaków)';
        } elseif (!$email) {
            $error = 'Podaj poprawny adres e-mail';
        } elseif (strlen($password) < 8) {
            $error = 'Hasło musi mieć co najmniej 8 znaków';
        } elseif ($password !== $confirm) {
            $error = 'Hasła nie są takie same.';
        } elseif (User::findByEmail($email)) {
            $error = 'Konto z tym adresem e-mail już istnieje.';
        }

        if ($error) {
            $_SESSION['form_error'] = $error;
            header('Location: ' . BASE_URL . '/register');
            exit();
        }

        $id = User::create($name, $email, $password);
        $this->startSession(User::findById($id));
        $this->redirectAfterLogin();
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();

        header('Location: ' . BASE_URL . '/');
        exit();
    }

    private function startSession(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['userId'] = (int) $user['id'];
        $_SESSION['userRole'] = $user['role'];
        unset($_SESSION['form_error']);
    }

    private function redirectAfterLogin(): void
    {
        if (!empty($_SESSION['pending_join_code'])) {
            $code = $_SESSION['pending_join_code'];
            unset($_SESSION['pending_join_code']);
            header('Location: ' . BASE_URL . '/dolacz/' . rawurldecode($code));
            exit();
        }

        header('Location: ' . BASE_URL . '/plans');
        exit();
    }
}