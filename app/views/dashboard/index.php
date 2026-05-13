<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#">Right Hire CRM</a>
        <div class="d-flex">
            <a href="/api/auth/logout" class="btn btn-outline-light btn-sm"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-5 text-center">
                    <div class="icon-circle bg-success-subtle text-success mx-auto mb-4" style="width: 80px; height: 80px; line-height: 80px; border-radius: 50%; font-size: 2rem;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <h2 class="fw-bold mb-3">Welcome to Dashboard</h2>
                    <p class="text-muted lead">You have successfully authenticated via Super Admin.</p>
                    <p class="small text-muted mt-4">User ID: <?= htmlspecialchars($userId ?? '') ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
