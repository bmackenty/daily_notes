<?php require ROOT_PATH . '/app/Views/partials/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="h5 mb-0"><i class="bi bi-calculator me-2"></i>IB CS Predicted Grade</h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
                    <?php endif; ?>

                    <p class="text-muted">Enter your unique code to see your predicted grade, or create a new record to get started.</p>

                    <form method="post" action="/predicted-grade/access" class="mb-4">
                        <label for="code" class="form-label">Your code</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="code" name="code" placeholder="e.g. AB12CD34EF" autocomplete="off" required>
                            <button type="submit" class="btn btn-primary">Go</button>
                        </div>
                    </form>

                    <hr>

                    <p class="mb-2 small text-muted">Don't have a code yet?</p>
                    <form method="post" action="/predicted-grade/start">
                        <button type="submit" class="btn btn-outline-success w-100">Create my record</button>
                    </form>
                    <p class="small text-muted mt-2 mb-0">You'll get a unique code to save. Use it next time to access your grades.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/app/Views/partials/footer.php'; ?>
