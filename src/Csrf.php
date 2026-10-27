<?php

namespace App;

class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf'];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="csrf" value="' . self::token() . '">';
    }

    public static function verify(): void
    {
        $sent = $_POST["csrf"] ?? '';

        if (!hash_equals($_SESSION['csrf'] ?? '', $sent)) {
            self::fail();
        }
    }

    public static function fail(): never
    {
        http_response_code(403);
        $code = 403;
        $title = 'Formularz wygasł';
        $message = 'Nieprawidłowy token formularza. Odśwież stronę i próbuj ponownie.';
        require __DIR__ . '/../views/error.php';
        exit;
    }

}