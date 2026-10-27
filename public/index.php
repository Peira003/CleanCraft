<?php

session_set_cookie_params([
    'httponly' => true,
    'secure' => !empty($_SERVER['HTTPS']),
    'samesite' => 'Lax',
]);
ini_set('session.use_strict_mode', '1');
session_start();

require_once __DIR__.'/../vendor/autoload.php';

$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
define('BASE_URL', ($scriptDir === '/' || $scriptDir === '\\') ? '' : $scriptDir);


use App\Router;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\PlanController;

$router = new Router();

$router->get('/', [HomeController::class, 'index']);

$router->get('/login', [AuthController::class, 'showLoginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegisterForm']);
$router->post('/register', [AuthController::class, 'register']);
$router->post('/logout', [AuthController::class, 'logout']);

$router->get('/plans', [PlanController::class, 'chooser']);
$router->get('/plans/create', [PlanController::class, 'showCreate']);
$router->post('/plans/create', [PlanController::class, 'create']);
$router->get('/plans/join', [PlanController::class, 'showJoin']);
$router->post('/plans/join', [PlanController::class, 'join']);
$router->get('/dolacz/{kod}', [PlanController::class, 'joinByLink']);
$router->get('/plan/{id}', [PlanController::class, 'show']);

$router->post('/plan/{id}/remove/{memberId}', [PlanController::class, 'removeMember']);
$router->post('/plan/{id}/leave', [PlanController::class, 'leavePlan']);


//$router->get('/dashboard', [AuthController::class, 'showDashboard']);


$router->dispatch();