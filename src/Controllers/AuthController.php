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

        if (LoginAttempt::isBlocked('login', $ip, $emailKey)) {
            $_SESSION['form_error'] = 'Zbyt wiele nieudanych prób logowania. Spróbuj ponownie za 15 minut.';
            header('Location: ' . BASE_URL . '/login');
            exit();
        }

        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $email = $email ? mb_strtolower($email) : false;
        $password = $_POST['password'] ?? '';
        if (strlen($password) > 72) {
            $password = '';
        }
        $user = $email ? User::findByEmail($email) : null;

        if (!$email || $password === '' || !$user || !password_verify($password, $user['password'])) {
            LoginAttempt::record('login', $ip, $emailKey);
            $_SESSION['form_error'] = 'Nieprawidłowy e-mail lub hasło.';
            header('Location: ' . BASE_URL . '/login');
            exit();
        }

        LoginAttempt::clear('login', $ip, $emailKey);
        $this->startSession($user);
        $this->redirectAfterLogin();
    }

    public function register(): void
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';

        if (LoginAttempt::isBlocked('register', $ip)) {
            $_SESSION['form_error'] = 'Zbyt wiele prób rejestracji. Spróbuj ponownie za godzinę.';
            header('Location: ' . BASE_URL . '/register');
            exit();
        }

        LoginAttempt::record('register', $ip);

        $name = trim($_POST['name'] ?? '');
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $email = $email ? mb_strtolower($email) : false;
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';

        $error = match (true) {
            $name === '' || mb_strlen($name) > 30 => 'Podaj imię (maks. 30 znaków).',
            !$email || strlen($email) > 255       => 'Podaj poprawny adres e-mail.',
            strlen($password) < 8                 => 'Hasło musi mieć co najmniej 8 znaków.',
            strlen($password) > 72                => 'Hasło może mieć maksymalnie 72 znaków.',
            $password != $confirm                 => 'Hasła nie są takie same.',
            User::findByEmail($email) !== null    => 'Konto z tym adresem e-mail już istnieje.',
            default                               => null,
        };



        if ($error) {
            $_SESSION['form_error'] = $error;
            header('Location: ' . BASE_URL . '/register');
            exit();
        }
        try {
            $id = User::create($name, $email, $password);
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000') {
                $_SESSION['form_error'] = 'Konto z tym adresem e-mail już istnieje.';
                header('Location: ' . BASE_URL . '/register');
                exit();
            }
            throw $e;
        }

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