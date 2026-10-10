<?php

session_start();

require_once __DIR__.'/../vendor/autoload.php';

$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl = ($scriptDir === '/' || $scriptDir === '\\') ? '' : $scriptDir;

define('BASE_URL', $baseUrl);

require_once __DIR__ . '/../src/Router.php';
require_once __DIR__ . '/../src/Controllers/HomeController.php';
require_once __DIR__ . '/../src/Controllers/AuthController.php';

use App\Router;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
$router = new Router();

$router->get('/', [HomeController::class, 'index']);

$router->get('/login', [AuthController::class, 'showLoginForm']);
$router->post('/login', [AuthController::class, 'login']);

$router->post('/logout', [AuthController::class, 'logout']);

$router->dispatch();