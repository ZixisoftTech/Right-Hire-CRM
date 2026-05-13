<?php

namespace App\Services;

class OTPService
{
    public function generateOTP()
    {
        // For testing purposes as requested
        return '001234';
    }

    public function hashOTP($otp)
    {
        return password_hash($otp, PASSWORD_BCRYPT);
    }

    public function verifyOTP($inputOtp, $hashedOtp)
    {
        return password_verify($inputOtp, $hashedOtp);
    }
}
