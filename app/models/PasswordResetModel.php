<?php

namespace App\Models;

use App\Core\Model;

class PasswordResetModel extends Model
{
    public function createResetToken($email, $token, $expiresAt)
    {
        $this->invalidateTokens($email);

        $stmt = $this->db->prepare("
            INSERT INTO password_resets (email, reset_token, expires_at)
            VALUES (:email, :token, :expires_at)
        ");

        return $stmt->execute([
            'email' => $email,
            'token' => $token,
            'expires_at' => $expiresAt
        ]);
    }

    public function getActiveToken($email, $token)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM password_resets
            WHERE email = :email AND reset_token = :token AND is_used = 0 AND expires_at > CURRENT_TIMESTAMP
            LIMIT 1
        ");
        $stmt->execute([
            'email' => $email,
            'token' => $token
        ]);
        return $stmt->fetch();
    }

    public function markAsUsed($id)
    {
        $stmt = $this->db->prepare("UPDATE password_resets SET is_used = 1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function invalidateTokens($email)
    {
        $stmt = $this->db->prepare("UPDATE password_resets SET is_used = 1 WHERE email = :email AND is_used = 0");
        return $stmt->execute(['email' => $email]);
    }
}
