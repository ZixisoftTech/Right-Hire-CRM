<?php

namespace App\Middleware;

use App\Helpers\Session;

class RateLimitMiddleware
{
    private $maxAttempts = 5;
    private $decayMinutes = 15;

    public function handle()
    {
        Session::init();

        $ip = $_SERVER['REMOTE_ADDR'];
        $key = 'rate_limit_' . md5($ip);

        $attempts = Session::get($key, ['count' => 0, 'time' => time()]);

        if (time() - $attempts['time'] > ($this->decayMinutes * 60)) {
            // Reset after decay time
            $attempts = ['count' => 0, 'time' => time()];
        }

        if ($attempts['count'] >= $this->maxAttempts) {
            http_response_code(429);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Too many failed attempts. Please retry later.']);
            exit;
        }

        // We only increment on failure, handled in the controller.
        // We just ensure the current state is stored.
        Session::set($key, $attempts);
    }
}
