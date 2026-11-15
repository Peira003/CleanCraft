<?php

namespace App;

class ErrorPage
{
    public static function show(int $code, string $title, string $message): never
    {
        http_response_code($code);
        require __DIR__ .'/../views/error.php';
        exit;
    }
}