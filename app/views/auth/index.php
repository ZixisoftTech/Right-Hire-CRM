<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="col-md-5 col-lg-4">
        <div class="card glass-card shadow-lg border-0 rounded-4 p-4">
            <div class="card-body">
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-primary">Right Hire CRM</h3>
                    <p class="text-muted">Super Admin Portal</p>
                </div>

                <form id="loginForm" action="/api/auth/login" method="POST">
                    <?= \App\Helpers\CSRF::getTokenField() ?>

                    <div class="form-floating mb-3">
                        <input type="email" class="form-control bg-light border-0" id="email" name="email" placeholder="name@example.com" required>
                        <label for="email"><i class="bi bi-envelope me-2"></i>Email address</label>
                    </div>

                    <div class="form-floating mb-3 position-relative">
                        <input type="password" class="form-control bg-light border-0" id="password" name="password" placeholder="Password" required>
                        <label for="password"><i class="bi bi-lock me-2"></i>Password</label>
                        <i class="bi bi-eye-slash position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer text-muted" id="togglePassword"></i>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="rememberMe">
                            <label class="form-check-label text-muted small" for="rememberMe">
                                Remember me
                            </label>
                        </div>
                        <a href="/auth/recover-password" class="text-decoration-none small fw-semibold text-primary">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-bold shadow-sm d-flex justify-content-center align-items-center btn-loading">
                        <span class="btn-text">Sign In</span>
                        <span class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
