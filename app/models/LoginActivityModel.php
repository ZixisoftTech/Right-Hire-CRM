<?php

namespace App\Models;

use App\Core\Model;

class LoginActivityModel extends Model
{
    public function logActivity($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO login_activities (user_id, email, ip_address, device_info, browser, login_status, failure_reason)
            VALUES (:user_id, :email, :ip_address, :device_info, :browser, :login_status, :failure_reason)
        ");

        return $stmt->execute([
            'user_id' => $data['user_id'] ?? null,
            'email' => $data['email'] ?? null,
            'ip_address' => $data['ip_address'] ?? null,
            'device_info' => $data['device_info'] ?? null,
            'browser' => $data['browser'] ?? null,
            'login_status' => $data['login_status'],
            'failure_reason' => $data['failure_reason'] ?? null
        ]);
    }

    public function logLogout($userId)
    {
        $stmt = $this->db->prepare("
            UPDATE login_activities
            SET logout_time = CURRENT_TIMESTAMP
            WHERE user_id = :user_id AND logout_time IS NULL
            ORDER BY login_time DESC LIMIT 1
        ");
        return $stmt->execute(['user_id' => $userId]);
    }
}
