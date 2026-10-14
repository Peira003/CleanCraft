<?php

namespace App\Controllers;


use Models\User;

class AuthController
{
    public function showLoginForm(): void
    {
        require_once __DIR__ . '/../../views/login.php';
    }

    public function showDashboard(): void
    {
        require_once __DIR__ . '/../../views/dashboard.php';
    }

    public function login(): void
    {
        if(isset($_POST['email']))
        {
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = $_POST['password'] ?? '';

            if (!$email || empty($password)) {
                $_SESSION['bad_attempt'] = true;
                header('Location: ' . BASE_URL . '/login');
                exit();
            }

            $user = User::findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {

                session_regenerate_id(true);

                $_SESSION['userId'] = $user['id'];
                $_SESSION['userRole'] = $user['role'];

                unset($_SESSION['bad_attempt']);

                if($_SESSION['userRole'] == 'admin')
                {
                    header('Location: ' . BASE_URL . '/adminPanel');
                    exit();
                } else {
                    header('Location: ' . BASE_URL. '/dashboard');
                }
                exit();

            } else {
                $_SESSION['bad_attempt'] = true;
                header('Location: ' . BASE_URL . '/login');
                exit();
            }

        }


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
}