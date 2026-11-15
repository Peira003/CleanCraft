<?php

namespace App;

class Router
{
    private array $routes = [];

    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


        if (!empty(BASE_URL) && str_starts_with($path, BASE_URL)) {
            $path = substr($path, strlen(BASE_URL));
        }

        $path = rtrim($path ?: '/', '/') ?: '/';


        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            $pattern = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $route) . '$#';

            if (preg_match($pattern, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                if ($method === 'POST') {
                    Csrf::verify();
                }

                [$controllerClass, $action] = $handler;
                try {
                    (new $controllerClass())->$action(...array_values($params));
                } catch (\Exception $e) {
                    error_log($e);
                    ErrorPage::show(500, 'Coś poszło nie tak', 'Spróbuj ponownie za chwilę.');
                }
                return;
            }
        }

        ErrorPage::show(404, 'Nie znaleziono strony', 'Taka strona nie istnieje albo została przeniesiona');
    }
}