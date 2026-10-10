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

        $basePath = BASE_URL;

        if (!empty($basePath) && str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath));
        }

        if($path === '' || $path === false) {
            $path = '/';
        }

        $handler = $this->routes[$method][$path] ?? null;

        if ($handler) {
            [$controllerClass, $action] = $handler;
            $controller = new $controllerClass();
            $controller->$action();
            return;
        }

        http_response_code(404);
        echo "404 - Strona nie została znaleziona";
    }
}