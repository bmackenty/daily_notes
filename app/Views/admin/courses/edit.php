<?php require ROOT_PATH . '/app/Views/partials/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Edit Course: <?= htmlspecialchars($course['name']) ?></h2>
                <a href="/admin/courses" class="btn btn-secondary">Back to Courses</a>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <?php require 'form.php'; ?>
        </div>
    </div>
</div> 