<?php require ROOT_PATH . '/app/Views/partials/header.php'; ?>

<div class="container mt-4 mb-5">
    <h2 class="mb-4">Predicted Grade tool</h2>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <h3 class="h6 mb-0">Weights</h3>
        </div>
        <div class="card-body">
            <form method="post" action="/admin/predicted-grade/save-config">
                <p class="small text-muted mb-3">Exam component: Paper 1 + IA + Paper 2 must equal 100%. Homework & habits are a separate &quot;outside&quot; weight that adjusts the final (e.g. 10% = 90% exam + 10% soft average).</p>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Paper 1 (%)</label>
                        <input type="number" name="weight_paper1" class="form-control" min="0" max="100" step="0.5" value="<?= (int)round(($weights['paper1'] ?? 0.4) * 100) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">IA (%)</label>
                        <input type="number" name="weight_ia" class="form-control" min="0" max="100" step="0.5" value="<?= (int)round(($weights['ia'] ?? 0.2) * 100) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Paper 2 (%)</label>
                        <input type="number" name="weight_paper2" class="form-control" min="0" max="100" step="0.5" value="<?= (int)round(($weights['paper2'] ?? 0.4) * 100) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Homework & habits weight (%)</label>
                        <input type="number" name="weight_soft" class="form-control" min="0" max="30" step="0.5" value="<?= (int)round(($weights['weight_soft'] ?? 0) * 100) ?>">
                        <small class="text-muted">0–30%. Final = (100−this)% exam + this% soft average.</small>
                    </div>
                </div>
                <p class="small text-muted mt-2 mb-0">Paper 1 + IA + Paper 2 must equal 100%.</p>
                <button type="submit" class="btn btn-primary mt-3">Save weights</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="h6 mb-0">Students (by code)</h3>
        </div>
        <div class="card-body">
            <?php if (empty($students)): ?>
                <p class="text-muted mb-0">No students yet. Students create a record from the <a href="/predicted-grade">Predicted Grade</a> page.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $s): ?>
                                <tr>
                                    <td><code><?= htmlspecialchars($s['code']) ?></code></td>
                                    <td><?= date('M j, Y', strtotime($s['created_at'])) ?></td>
                                    <td>
                                        <a href="/predicted-grade?code=<?= urlencode($s['code']) ?>" class="btn btn-sm btn-outline-primary" target="_blank">View as student</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/app/Views/partials/footer.php'; ?>
