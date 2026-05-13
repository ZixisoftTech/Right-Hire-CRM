<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);

        try {
            $this->mailer->isSMTP();
            $this->mailer->Host       = $_ENV['SMTP_HOST'];
            $this->mailer->SMTPAuth   = true;
            $this->mailer->Username   = $_ENV['SMTP_USERNAME'];
            $this->mailer->Password   = $_ENV['SMTP_PASSWORD'];
            $this->mailer->Port       = $_ENV['SMTP_PORT'];

            // For Mailtrap local testing, TLS is often not strictly required or we use generic settings
            // $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

            $this->mailer->setFrom($_ENV['SMTP_FROM_EMAIL'], $_ENV['SMTP_FROM_NAME']);
            $this->mailer->isHTML(true);
        } catch (Exception $e) {
            error_log("Mailer Initialization Error: {$this->mailer->ErrorInfo}");
        }
    }

    public function sendOTP($toEmail, $otpCode)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($toEmail);
            $this->mailer->Subject = 'Your Login OTP - Right Hire CRM';

            // Basic professional template
            $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px;'>
                <h2 style='color: #333;'>Login Verification</h2>
                <p>Hello,</p>
                <p>Your One-Time Password (OTP) for login is:</p>
                <h1 style='color: #0d6efd; letter-spacing: 5px; text-align: center; padding: 10px; background: #f8f9fa; border-radius: 5px;'>{$otpCode}</h1>
                <p>This OTP is valid for 10 minutes. Do not share it with anyone.</p>
                <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
                <p style='font-size: 12px; color: #777; text-align: center;'>Right Hire CRM &copy; " . date('Y') . "</p>
            </div>
            ";

            $this->mailer->Body = $body;
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Mail Error: {$this->mailer->ErrorInfo}");
            return false;
        }
    }

    public function sendPasswordReset($toEmail, $resetToken)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($toEmail);
            $this->mailer->Subject = 'Password Reset Request - Right Hire CRM';

            $resetLink = $_ENV['APP_URL'] . "/auth/reset-password?token=" . urlencode($resetToken) . "&email=" . urlencode($toEmail);

            $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px;'>
                <h2 style='color: #333;'>Password Reset Request</h2>
                <p>Hello,</p>
                <p>We received a request to reset your password. Click the button below to set a new password:</p>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$resetLink}' style='background-color: #0d6efd; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 4px; font-weight: bold;'>Reset Password</a>
                </div>
                <p>This link will expire in 30 minutes.</p>
                <p>If you did not request this, please ignore this email.</p>
                <hr style='border: none; border-top: 1px solid #eee; margin: 20px 0;'>
                <p style='font-size: 12px; color: #777; text-align: center;'>Right Hire CRM &copy; " . date('Y') . "</p>
            </div>
            ";

            $this->mailer->Body = $body;
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Mail Error: {$this->mailer->ErrorInfo}");
            return false;
        }
    }
}
