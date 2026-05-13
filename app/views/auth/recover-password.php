<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="col-md-5 col-lg-4">
        <div class="card glass-card shadow-lg border-0 rounded-4 p-4">
            <div class="card-body">
                <div class="mb-4">
                    <a href="/" class="text-decoration-none text-muted mb-3 d-inline-block"><i class="bi bi-arrow-left me-1"></i>Back to login</a>
                    <h4 class="fw-bold">Forgot Password</h4>
                    <p class="text-muted small">Enter your email address and we'll send you a link to reset your password.</p>
                </div>

                <form id="recoverForm" action="/api/auth/send-reset-link" method="POST">
                    <?= \App\Helpers\CSRF::getTokenField() ?>

                    <div class="form-floating mb-4">
                        <input type="email" class="form-control bg-light border-0" id="email" name="email" placeholder="name@example.com" required>
                        <label for="email"><i class="bi bi-envelope me-2"></i>Email address</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-bold shadow-sm btn-loading">
                        <span class="btn-text">Send Reset Link</span>
                        <span class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
