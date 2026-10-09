<?php

namespace App\Controllers;


class AuthController
{
    public function showLoginForm(): void
    {
        require_once __DIR__ . '/../../views/login.php';
    }

    public function login(): void
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = filter_input(INPUT_POST, 'password');
    }
}