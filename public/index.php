<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Core\Router;

// Load Environment Variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Error Reporting
if ($_ENV['APP_ENV'] === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Initialize Router
$router = new Router();

// Load Routes
require_once __DIR__ . '/../routes/web.php';

// Dispatch
$url = $_SERVER['REQUEST_URI'];
$router->dispatch($url);
