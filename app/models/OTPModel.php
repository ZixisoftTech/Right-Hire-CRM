<?php

namespace App\Models;

use App\Core\Model;

class OTPModel extends Model
{
    public function createOTP($email, $hashedOtp, $expiresAt)
    {
        // First, invalidate any previous active OTPs for this email
        $this->invalidateOTPs($email);

        $stmt = $this->db->prepare("
            INSERT INTO email_otps (email, otp_code, expires_at)
            VALUES (:email, :otp_code, :expires_at)
        ");

        return $stmt->execute([
            'email' => $email,
            'otp_code' => $hashedOtp,
            'expires_at' => $expiresAt
        ]);
    }

    public function getActiveOTP($email)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM email_otps
            WHERE email = :email AND is_used = 0
            ORDER BY generated_at DESC LIMIT 1
        ");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function incrementAttempt($id)
    {
        $stmt = $this->db->prepare("UPDATE email_otps SET attempt_count = attempt_count + 1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function incrementResend($id)
    {
        $stmt = $this->db->prepare("UPDATE email_otps SET resend_count = resend_count + 1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function markAsUsed($id)
    {
        $stmt = $this->db->prepare("UPDATE email_otps SET is_used = 1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function invalidateOTPs($email)
    {
        $stmt = $this->db->prepare("UPDATE email_otps SET is_used = 1 WHERE email = :email AND is_used = 0");
        return $stmt->execute(['email' => $email]);
    }
}
