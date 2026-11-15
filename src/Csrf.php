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
        $expected = $_SESSION['csrf'] ?? '';
        $sent = $_POST["csrf"] ?? '';

        if ($expected === '' || !is_string($sent) || !hash_equals($expected, $sent)) {
            self::fail();
        }
    }

    public static function fail(): never
    {
        ErrorPage::show(403, 'Formularz wygasł', 'Nieprawidłowy token formularza. Odśwież stronę i spróbuj ponownie.');
    }

}