<?php

namespace App\Models;

use App\Core\Model;

class SessionModel extends Model
{
    public function createSession($userId, $token, $deviceInfo, $ipAddress, $expiresAt)
    {
        $stmt = $this->db->prepare("
            INSERT INTO user_sessions (user_id, session_token, device_info, ip_address, expires_at)
            VALUES (:user_id, :token, :device_info, :ip_address, :expires_at)
        ");

        return $stmt->execute([
            'user_id' => $userId,
            'token' => $token,
            'device_info' => $deviceInfo,
            'ip_address' => $ipAddress,
            'expires_at' => $expiresAt
        ]);
    }

    public function getActiveSession($token)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM user_sessions
            WHERE session_token = :token AND expires_at > CURRENT_TIMESTAMP
            LIMIT 1
        ");
        $stmt->execute(['token' => $token]);
        return $stmt->fetch();
    }

    public function updateActivity($token, $expiresAt)
    {
        $stmt = $this->db->prepare("
            UPDATE user_sessions
            SET last_activity_at = CURRENT_TIMESTAMP, expires_at = :expires_at
            WHERE session_token = :token
        ");
        return $stmt->execute([
            'expires_at' => $expiresAt,
            'token' => $token
        ]);
    }

    public function invalidateSession($token)
    {
        $stmt = $this->db->prepare("UPDATE user_sessions SET expires_at = CURRENT_TIMESTAMP WHERE session_token = :token");
        return $stmt->execute(['token' => $token]);
    }

    public function invalidateUserSessions($userId)
    {
        $stmt = $this->db->prepare("UPDATE user_sessions SET expires_at = CURRENT_TIMESTAMP WHERE user_id = :user_id");
        return $stmt->execute(['user_id' => $userId]);
    }
}
