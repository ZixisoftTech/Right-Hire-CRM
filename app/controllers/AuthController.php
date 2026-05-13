<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\UserModel;
use App\Models\OTPModel;
use App\Models\PasswordResetModel;
use App\Models\LoginActivityModel;
use App\Services\MailService;
use App\Services\OTPService;
use App\Services\SessionService;
use App\Services\SecurityService;
use App\Helpers\Session;
use App\Helpers\Validator;

class AuthController extends Controller
{
    private $userModel;
    private $otpModel;
    private $passwordResetModel;
    private $loginActivityModel;
    private $mailService;
    private $otpService;
    private $sessionService;
    private $securityService;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->otpModel = new OTPModel();
        $this->passwordResetModel = new PasswordResetModel();
        $this->loginActivityModel = new LoginActivityModel();
        $this->mailService = new MailService();
        $this->otpService = new OTPService();
        $this->sessionService = new SessionService();
        $this->securityService = new SecurityService();
        Session::init();
    }

    public function index()
    {
        $this->view('auth/index');
    }

    public function login()
    {
        $email = Validator::sanitizeEmail($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $ip = $this->securityService->getIpAddress();
        $device = $this->securityService->getDeviceInfo();

        if (!Validator::validateEmail($email)) {
            $this->json(['status' => 'error', 'message' => 'Invalid email format.']);
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || $user['status'] !== 'active') {
            $this->logActivity(null, $email, 'failed_password', 'Email not found or inactive');
            $this->json(['status' => 'error', 'message' => 'Invalid email or password']);
        }

        if (!password_verify($password, $user['password_hash'])) {
            $this->logActivity($user['id'], $email, 'failed_password', 'Incorrect password');
            $this->incrementRateLimit();
            $this->json(['status' => 'error', 'message' => 'Invalid email or password']);
        }

        // Generate OTP
        $otpCode = $this->otpService->generateOTP();
        $hashedOtp = $this->otpService->hashOTP($otpCode);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $this->otpModel->createOTP($email, $hashedOtp, $expiresAt);

        // Send OTP
        $this->mailService->sendOTP($email, $otpCode);

        // Store temp session for OTP verification
        Session::set('otp_email', $email);
        Session::set('otp_user_id', $user['id']);

        $this->json(['status' => 'success', 'redirect' => '/auth/email-otp']);
    }

    public function showOTP()
    {
        if (!Session::has('otp_email')) {
            $this->redirect('/');
        }
        $this->view('auth/email-otp', ['email' => Session::get('otp_email')]);
    }

    public function verifyOTP()
    {
        $email = Session::get('otp_email');
        $userId = Session::get('otp_user_id');
        $otpInput = implode('', $_POST['otp'] ?? []);

        if (!$email || strlen($otpInput) !== 6) {
            $this->json(['status' => 'error', 'message' => 'Invalid OTP format.']);
        }

        $activeOtp = $this->otpModel->getActiveOTP($email);

        if (!$activeOtp) {
            $this->json(['status' => 'error', 'message' => 'Email OTP expired. Please request a new one.']);
        }

        if (strtotime($activeOtp['expires_at']) < time()) {
            $this->json(['status' => 'error', 'message' => 'Email OTP expired. Please request a new one.']);
        }

        if ($activeOtp['attempt_count'] >= 5) {
            $this->otpModel->invalidateOTPs($email);
            $this->json(['status' => 'error', 'message' => 'Too many failed attempts. Please retry later.']);
        }

        if (!$this->otpService->verifyOTP($otpInput, $activeOtp['otp_code'])) {
            $this->otpModel->incrementAttempt($activeOtp['id']);
            $this->logActivity($userId, $email, 'failed_otp', 'Invalid OTP entered');
            $this->json(['status' => 'error', 'message' => 'Invalid Email OTP. Please try again.']);
        }

        // Success
        $this->otpModel->markAsUsed($activeOtp['id']);
        $this->userModel->updateLastLogin($userId);

        // Create actual session
        $this->sessionService->createSession($userId);

        $this->logActivity($userId, $email, 'success', null);

        Session::remove('otp_email');
        Session::remove('otp_user_id');

        $this->json(['status' => 'success', 'redirect' => '/dashboard']);
    }

    public function resendOTP()
    {
        $email = Session::get('otp_email');

        if (!$email) {
            $this->json(['status' => 'error', 'message' => 'Session expired.']);
        }

        $activeOtp = $this->otpModel->getActiveOTP($email);

        if ($activeOtp) {
            if ($activeOtp['resend_count'] >= 3) {
                $this->json(['status' => 'error', 'message' => 'Maximum resend limit reached.']);
            }

            // Check if 60 seconds have passed since generated
            if (time() - strtotime($activeOtp['generated_at']) < 60) {
                $this->json(['status' => 'error', 'message' => 'Please wait 60 seconds before resending.']);
            }
        }

        $otpCode = $this->otpService->generateOTP();
        $hashedOtp = $this->otpService->hashOTP($otpCode);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $this->otpModel->createOTP($email, $hashedOtp, $expiresAt);

        // Need to fetch new ID for resend count mapping though realistically it's a new row
        $newActiveOtp = $this->otpModel->getActiveOTP($email);
        // Copy resend count from previous and increment
        $prevResendCount = $activeOtp ? $activeOtp['resend_count'] : 0;

        for($i = 0; $i <= $prevResendCount; $i++){
             $this->otpModel->incrementResend($newActiveOtp['id']);
        }

        $this->mailService->sendOTP($email, $otpCode);

        $this->logActivity(Session::get('otp_user_id'), $email, 'otp_resend', 'Resent OTP');

        $this->json(['status' => 'success', 'message' => 'OTP sent successfully.']);
    }

    public function showRecoverPassword()
    {
        $this->view('auth/recover-password');
    }

    public function sendResetLink()
    {
        $email = Validator::sanitizeEmail($_POST['email'] ?? '');

        if (!Validator::validateEmail($email)) {
            $this->json(['status' => 'error', 'message' => 'Invalid email format.']);
        }

        $user = $this->userModel->findByEmail($email);

        // For security, always say success even if email not found to prevent user enumeration
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+30 minutes'));

            $this->passwordResetModel->createResetToken($email, $token, $expiresAt);
            $this->mailService->sendPasswordReset($email, $token);
        }

        $this->json(['status' => 'success', 'message' => 'If this email is registered, you will receive a reset link shortly.']);
    }

    public function showResetPassword()
    {
        $token = $_GET['token'] ?? '';
        $email = $_GET['email'] ?? '';

        if (empty($token) || empty($email)) {
            $this->redirect('/auth/recover-password');
        }

        $activeToken = $this->passwordResetModel->getActiveToken($email, $token);

        if (!$activeToken) {
            die("Reset link is invalid or expired.");
        }

        $this->view('auth/reset-password', ['token' => $token, 'email' => $email]);
    }

    public function resetPassword()
    {
        $token = $_POST['token'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($password !== $confirmPassword) {
            $this->json(['status' => 'error', 'message' => 'Passwords do not match.']);
        }

        // Ensure robust password
        if (strlen($password) < 8) {
             $this->json(['status' => 'error', 'message' => 'Password must be at least 8 characters.']);
        }

        $activeToken = $this->passwordResetModel->getActiveToken($email, $token);

        if (!$activeToken) {
            $this->json(['status' => 'error', 'message' => 'Reset link is invalid or expired.']);
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $this->userModel->updatePassword($email, $hashedPassword);
        $this->passwordResetModel->markAsUsed($activeToken['id']);

        $this->json(['status' => 'success', 'redirect' => '/', 'message' => 'Password reset successfully. Please login.']);
    }

    public function logout()
    {
        $userId = Session::get('user_id');
        if ($userId) {
            $this->loginActivityModel->logLogout($userId);
            $this->logActivity($userId, null, 'logout', 'User logged out manually');
            $this->sessionService->destroySession();
        }
        $this->redirect('/');
    }

    private function logActivity($userId, $email, $status, $reason)
    {
        $this->loginActivityModel->logActivity([
            'user_id' => $userId,
            'email' => $email,
            'ip_address' => $this->securityService->getIpAddress(),
            'device_info' => $this->securityService->getDeviceInfo(),
            'browser' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            'login_status' => $status,
            'failure_reason' => $reason
        ]);
    }

    private function incrementRateLimit()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $key = 'rate_limit_' . md5($ip);
        $attempts = Session::get($key, ['count' => 0, 'time' => time()]);
        $attempts['count']++;
        Session::set($key, $attempts);
    }
}
