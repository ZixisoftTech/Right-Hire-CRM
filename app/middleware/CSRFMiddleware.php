<?php

namespace App\Middleware;

use App\Helpers\CSRF;
use App\Helpers\Session;

class CSRFMiddleware
{
    public function handle()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Session::init();

            // Check headers for AJAX requests or POST payload
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

            if (!CSRF::validateToken($token)) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'CSRF token validation failed.']);
                exit;
            }
        }
    }
}
