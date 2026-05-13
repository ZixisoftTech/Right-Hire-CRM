<?php

namespace App\Middleware;

use App\Helpers\Session;

class GuestMiddleware
{
    public function handle()
    {
        Session::init();

        $userId = Session::get('user_id');
        $sessionToken = Session::get('session_token');

        if ($userId && $sessionToken) {
            header("Location: /dashboard");
            exit;
        }
    }
}
