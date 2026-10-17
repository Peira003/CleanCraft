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

        $path = rtrim($path ?: '/', '/') ?: '/';


        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            $pattern = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $route) . '$#';

            if (preg_match($pattern, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                [$controllerClass, $action] = $handler;
                (new $controllerClass())->$action(...array_values($params));
                return;
            }
        }

        http_response_code(404);
        echo "404 - Strona nie została znaleziona";
    }
}