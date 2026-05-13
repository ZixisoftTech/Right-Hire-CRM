<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="col-md-5 col-lg-4">
        <div class="card glass-card shadow-lg border-0 rounded-4 p-4">
            <div class="card-body text-center">
                <div class="mb-4">
                    <div class="icon-circle bg-primary-subtle text-primary mb-3 mx-auto">
                        <i class="bi bi-shield-lock fs-2"></i>
                    </div>
                    <h4 class="fw-bold">Two-Step Verification</h4>
                    <p class="text-muted small">We sent a 6-digit code to <br><strong class="text-dark"><?= htmlspecialchars($email ?? '') ?></strong></p>
                </div>

                <form id="otpForm" action="/api/auth/verify-otp" method="POST">
                    <?= \App\Helpers\CSRF::getTokenField() ?>

                    <div class="d-flex justify-content-between mb-4 otp-container">
                        <input type="text" class="form-control otp-input text-center fw-bold fs-4 mx-1 rounded-3" name="otp[]" maxlength="1" required autofocus>
                        <input type="text" class="form-control otp-input text-center fw-bold fs-4 mx-1 rounded-3" name="otp[]" maxlength="1" required>
                        <input type="text" class="form-control otp-input text-center fw-bold fs-4 mx-1 rounded-3" name="otp[]" maxlength="1" required>
                        <input type="text" class="form-control otp-input text-center fw-bold fs-4 mx-1 rounded-3" name="otp[]" maxlength="1" required>
                        <input type="text" class="form-control otp-input text-center fw-bold fs-4 mx-1 rounded-3" name="otp[]" maxlength="1" required>
                        <input type="text" class="form-control otp-input text-center fw-bold fs-4 mx-1 rounded-3" name="otp[]" maxlength="1" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-bold shadow-sm mb-3 btn-loading">
                        <span class="btn-text">Verify Account</span>
                        <span class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
                    </button>
                </form>

                <div class="mt-3">
                    <p class="text-muted small mb-0">Didn't receive the code?</p>
                    <button id="resendOtpBtn" class="btn btn-link text-decoration-none fw-semibold p-0" disabled>
                        Resend Code <span id="timerText">(09:59)</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
