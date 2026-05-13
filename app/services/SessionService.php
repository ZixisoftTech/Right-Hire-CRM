<?php

namespace App\Services;

use App\Models\SessionModel;
use App\Helpers\Session;
use App\Services\SecurityService;

class SessionService
{
    private $sessionModel;
    private $securityService;

    public function __construct()
    {
        $this->sessionModel = new SessionModel();
        $this->securityService = new SecurityService();
    }

    public function createSession($userId)
    {
        // Invalidate old sessions for this user to ensure single active session
        $this->sessionModel->invalidateUserSessions($userId);

        $sessionToken = bin2hex(random_bytes(32));
        $deviceInfo = $this->securityService->getDeviceInfo();
        $ipAddress = $this->securityService->getIpAddress();

        // 30 minutes expiry
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 minutes'));

        $this->sessionModel->createSession($userId, $sessionToken, $deviceInfo, $ipAddress, $expiresAt);

        Session::regenerate();
        Session::set('user_id', $userId);
        Session::set('session_token', $sessionToken);
        Session::set('last_activity', time());
    }

    public function destroySession()
    {
        $sessionToken = Session::get('session_token');
        if ($sessionToken) {
            $this->sessionModel->invalidateSession($sessionToken);
        }
        Session::destroy();
    }
}
