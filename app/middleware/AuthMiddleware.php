<?php

namespace App\Middleware;

use App\Helpers\Session;
use App\Models\SessionModel;

class AuthMiddleware
{
    public function handle()
    {
        Session::init();

        $userId = Session::get('user_id');
        $sessionToken = Session::get('session_token');

        if (!$userId || !$sessionToken) {
            header("Location: /");
            exit;
        }

        $sessionModel = new SessionModel();
        $session = $sessionModel->getActiveSession($sessionToken);

        if (!$session || $session['user_id'] != $userId) {
            // Session is invalid or expired
            Session::destroy();
            header("Location: /?expired=1");
            exit;
        }

        // Update activity
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 minutes'));
        $sessionModel->updateActivity($sessionToken, $expiresAt);
        Session::set('last_activity', time());
    }
}
