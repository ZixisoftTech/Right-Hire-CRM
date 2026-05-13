<?php

// Load Native Autoloader
require_once __DIR__ . '/../app/core/Autoloader.php';
require_once __DIR__ . '/../app/core/EnvParser.php';

use App\Core\EnvParser;
use App\Core\Router;

// Parse .env file natively
EnvParser::load(__DIR__ . '/../.env');

// Error Reporting
if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
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
