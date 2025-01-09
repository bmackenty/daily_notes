<?php require ROOT_PATH . '/app/Views/partials/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <h1>Site Maintenance</h1>
            <div class="alert alert-warning">
                <p>We're currently performing maintenance. Please check back later.</p>
                <p>We apologize for any inconvenience.</p>
            </div>
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <div class="mt-3">
                    <a href="/admin/dashboard" class="btn btn-primary">Admin Dashboard</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div> 