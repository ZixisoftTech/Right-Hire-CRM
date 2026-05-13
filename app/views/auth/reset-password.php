<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="col-md-5 col-lg-4">
        <div class="card glass-card shadow-lg border-0 rounded-4 p-4">
            <div class="card-body">
                <div class="mb-4">
                    <h4 class="fw-bold">Create New Password</h4>
                    <p class="text-muted small">Your new password must be different from previous used passwords.</p>
                </div>

                <form id="resetForm" action="/api/auth/reset-password" method="POST">
                    <?= \App\Helpers\CSRF::getTokenField() ?>
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($email ?? '') ?>">

                    <div class="form-floating mb-3 position-relative">
                        <input type="password" class="form-control bg-light border-0" id="password" name="password" placeholder="New Password" required minlength="8">
                        <label for="password"><i class="bi bi-lock me-2"></i>New Password</label>
                        <i class="bi bi-eye-slash position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer text-muted toggle-password" data-target="password"></i>
                    </div>

                    <div class="progress mb-3" style="height: 5px;">
                        <div id="passwordStrength" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>

                    <div class="form-floating mb-4 position-relative">
                        <input type="password" class="form-control bg-light border-0" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required minlength="8">
                        <label for="confirm_password"><i class="bi bi-shield-check me-2"></i>Confirm Password</label>
                        <i class="bi bi-eye-slash position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer text-muted toggle-password" data-target="confirm_password"></i>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-bold shadow-sm btn-loading">
                        <span class="btn-text">Reset Password</span>
                        <span class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
