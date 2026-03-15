<?php
$code = $_SESSION[\App\Controllers\PredictedGradeController::SESSION_CODE_KEY] ?? $student['code'] ?? '';
$steps = $result['steps'] ?? [];
$w = $steps['weights'] ?? ['paper1' => 0.40, 'ia' => 0.20, 'paper2' => 0.40, 'weight_soft' => 0];
$weightSoft = $w['weight_soft'] ?? 0;
$paper1Topics = \App\Models\PredictedGradeEntry::PAPER1_TOPICS;
$paper2Topics = \App\Models\PredictedGradeEntry::PAPER2_TOPICS;
$boundaries = $steps['boundaries'] ?? [];
$categoryAvg = $result['category_avg'] ?? [];
require ROOT_PATH . '/app/Views/partials/header.php';
?>

<style>
/* Color-code Paper 1, IA, Paper 2 */
.pg-paper1 { border-left: 4px solid #0d6efd; background-color: rgba(13, 110, 253, 0.08); }
.pg-ia      { border-left: 4px solid #198754; background-color: rgba(25, 135, 84, 0.08); }
.pg-paper2  { border-left: 4px solid #fd7e14; background-color: rgba(253, 126, 20, 0.08); }
.pg-soft    { border-left: 4px solid #6f42c1; background-color: rgba(111, 66, 193, 0.08); }
[data-bs-theme="dark"] .pg-paper1 { background-color: rgba(13, 110, 253, 0.15); }
[data-bs-theme="dark"] .pg-ia      { background-color: rgba(25, 135, 84, 0.15); }
[data-bs-theme="dark"] .pg-paper2  { background-color: rgba(253, 126, 20, 0.15); }
[data-bs-theme="dark"] .pg-soft    { background-color: rgba(111, 66, 193, 0.15); }
.pg-math-block { font-family: ui-monospace, monospace; font-size: 0.95rem; }
</style>

<div class="container mt-3 mb-5">
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <?php if (!empty($showCodeAlert) && $code): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Your code:</strong> <code id="student-code"><?= htmlspecialchars($code) ?></code>
            <button type="button" class="btn btn-sm btn-outline-success ms-2" id="copy-code-btn" title="Copy">Copy</button>
            <p class="mb-0 mt-2 small">Save this code to return later. You won't need a password.</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <span class="text-muted">Your code:</span> <code><?= htmlspecialchars($code) ?></code>
            <a href="/predicted-grade/logout" class="btn btn-sm btn-outline-secondary ms-2">Use a different code</a>
        </div>
    </div>

    <!-- Predicted grade result -->
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h2 class="h5 mb-0">Your predicted grade</h2>
        </div>
        <div class="card-body text-center">
            <?php if ($result['ib_grade'] !== null): ?>
                <p class="display-4 mb-0 text-primary"><?= (int) $result['ib_grade'] ?></p>
                <p class="text-muted small mb-0">Predicted grade (1–7) · Final average <?= number_format($result['final_avg'] ?? $result['final_percent'], 1) ?> (1–7 scale)</p>
            <?php else: ?>
                <p class="text-muted mb-0">Add grades (1–7) in the sections below to see your predicted grade.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- How the grade is calculated (visible math) -->
    <div class="card mb-4">
        <div class="card-header">
            <h2 class="h6 mb-0">How your grade is calculated</h2>
        </div>
        <div class="card-body pg-math-block">
            <p class="mb-2">Enter a <strong>grade from 1 to 7</strong> for each assessment. If you have more than one assessment per topic, we average them (all on the 1–7 scale).</p>
            <ul class="mb-2">
                <li><strong class="text-primary">Paper 1</strong> (<?= (int)round($w['paper1']*100) ?>% of exam) = average of your grades in: <?= implode(', ', array_map([\App\Models\PredictedGradeEntry::class, 'getCategoryLabel'], $paper1Topics)) ?>.</li>
                <li><strong class="text-success">IA</strong> (<?= (int)round($w['ia']*100) ?>% of exam) = your IA grade(s) average.</li>
                <li><strong class="text-warning">Paper 2</strong> (<?= (int)round($w['paper2']*100) ?>% of exam) = average of your grades in: <?= implode(', ', array_map([\App\Models\PredictedGradeEntry::class, 'getCategoryLabel'], $paper2Topics)) ?>.</li>
                <li><strong>Exam average</strong> (100%) = (<?= (int)round($w['paper1']*100) ?>% × P1) + (<?= (int)round($w['ia']*100) ?>% × IA) + (<?= (int)round($w['paper2']*100) ?>% × P2).</li>
                <li><strong class="text-secondary">Homework & habits</strong> (outside the 100%): average of your Homework, Study habits, and Independent coding grades. If your teacher sets a weight (e.g. 10%), then Final = (100−10)% × Exam avg + 10% × Soft avg.</li>
            </ul>
            <p class="mb-1">Formula (all on 1–7 scale):</p>
            <p class="mb-1"><code>Exam avg = (<?= (int)round($w['paper1']*100) ?>% × P1) + (<?= (int)round($w['ia']*100) ?>% × IA) + (<?= (int)round($w['paper2']*100) ?>% × P2)</code></p>
            <?php if ($weightSoft > 0): ?>
            <p class="mb-1"><code>Final = (<?= (int)round((1-$weightSoft)*100) ?>% × Exam avg) + (<?= (int)round($weightSoft*100) ?>% × Homework & habits avg)</code></p>
            <?php else: ?>
            <p class="mb-1"><code>Final = Exam avg</code> (homework & habits weight is 0)</p>
            <?php endif; ?>
            <p class="mb-1"><code>Predicted grade = round(Final) → 1 to 7</code></p>
            <?php if (($result['final_avg'] ?? $result['final_percent']) !== null): ?>
                <p class="mb-1 mt-2">Your numbers:</p>
                <p class="mb-1">
                    <span class="text-primary">Paper 1</span> = <?= $result['paper1_avg'] !== null ? number_format($result['paper1_avg'], 1) : '—' ?> &nbsp;
                    <span class="text-success">IA</span> = <?= $result['ia_avg'] !== null ? number_format($result['ia_avg'], 1) : '—' ?> &nbsp;
                    <span class="text-warning">Paper 2</span> = <?= $result['paper2_avg'] !== null ? number_format($result['paper2_avg'], 1) : '—' ?> &nbsp;
                    → <strong>Exam avg</strong> = <?= ($result['exam_avg'] ?? null) !== null ? number_format($result['exam_avg'], 1) : '—' ?>
                    <?php if ($weightSoft > 0): ?>
                    &nbsp; &nbsp; <span class="text-secondary">Homework & habits avg</span> = <?= ($result['soft_avg'] ?? null) !== null ? number_format($result['soft_avg'], 1) : '—' ?>
                    <?php endif; ?>
                </p>
                <p class="mb-0">
                    <?php if ($weightSoft > 0 && ($result['soft_avg'] ?? null) !== null): ?>
                    <code>Final = (<?= (int)round((1-$weightSoft)*100) ?>%×<?= number_format($result['exam_avg'] ?? 0, 1) ?>) + (<?= (int)round($weightSoft*100) ?>%×<?= number_format($result['soft_avg'], 1) ?>) = <?= number_format($result['final_avg'] ?? $result['final_percent'], 1) ?> → predicted <strong><?= (int)($result['ib_grade']) ?></strong></code>
                    <?php else: ?>
                    <code>Final = Exam avg = <?= number_format($result['exam_avg'] ?? $result['final_avg'] ?? $result['final_percent'], 1) ?> → predicted <strong><?= (int)($result['ib_grade']) ?></strong></code>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Paper 1 topics -->
    <div class="card mb-3 pg-paper1">
        <div class="card-header bg-primary text-white">
            <h3 class="h6 mb-0">Paper 1 (<?= (int)round($w['paper1']*100) ?>%)</h3>
        </div>
        <div class="card-body">
            <?php foreach ($paper1Topics as $cat): ?>
                <?php
                $entries = $entriesByCategory[$cat] ?? [];
                $avg = $categoryAvg[$cat] ?? null;
                $topicLabel = \App\Models\PredictedGradeEntry::getCategoryLabel($cat);
                ?>
                <div class="mb-4">
                    <strong><?= htmlspecialchars($topicLabel) ?></strong>
                    <?php if ($avg !== null): ?><span class="text-muted small">(avg <?= number_format($avg, 1) ?>)</span><?php endif; ?>
                    <div class="table-responsive mt-1">
                        <table class="table table-sm table-bordered mb-2" style="max-width: 24rem;">
                            <thead class="table-light">
                                <tr><th>Label</th><th>Grade (1–7)</th><th class="text-end">Actions</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($entries as $e): ?>
                                <tr>
                                    <td><?= $e['label'] ? htmlspecialchars($e['label']) : '—' ?></td>
                                    <td><?= (int)round($e['score']) ?></td>
                                    <td class="text-end">
                                        <form method="post" action="/predicted-grade/entry/delete" class="d-inline">
                                            <input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
                                            <button type="submit" class="btn btn-link btn-sm p-0 text-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($entries)): ?><tr><td colspan="3" class="text-muted small">No assessments yet.</td></tr><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <form method="post" action="/predicted-grade/entry" class="row g-2 align-items-end">
                        <input type="hidden" name="category" value="<?= htmlspecialchars($cat) ?>">
                        <div class="col-auto"><label class="form-label small mb-0">Grade (1–7)</label><input type="number" name="score" class="form-control form-control-sm" placeholder="1–7" min="1" max="7" step="1" required style="width:4rem"></div>
                        <div class="col-auto"><label class="form-label small mb-0">Label (optional)</label><input type="text" name="label" class="form-control form-control-sm" placeholder="e.g. Quiz 1" style="width:8rem"></div>
                        <div class="col-auto"><button type="submit" class="btn btn-primary btn-sm">Add</button></div>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- IA -->
    <div class="card mb-3 pg-ia">
        <div class="card-header bg-success text-white">
            <h3 class="h6 mb-0">IA (<?= (int)round($w['ia']*100) ?>%)</h3>
        </div>
        <div class="card-body">
            <?php $cat = 'ia'; $entries = $entriesByCategory[$cat] ?? []; $avg = $categoryAvg[$cat] ?? null; $topicLabel = \App\Models\PredictedGradeEntry::getCategoryLabel($cat); ?>
            <strong><?= htmlspecialchars($topicLabel) ?></strong>
            <?php if ($avg !== null): ?><span class="text-muted small">(avg <?= number_format($avg, 1) ?>)</span><?php endif; ?>
            <div class="table-responsive mt-1">
                <table class="table table-sm table-bordered mb-2" style="max-width: 24rem;">
                    <thead class="table-light"><tr><th>Label</th><th>Grade (1–7)</th><th class="text-end">Actions</th></tr></thead>
                    <tbody>
                        <?php foreach ($entries as $e): ?>
                        <tr>
                            <td><?= $e['label'] ? htmlspecialchars($e['label']) : '—' ?></td>
                            <td><?= (int)round($e['score']) ?></td>
                            <td class="text-end">
                                <form method="post" action="/predicted-grade/entry/delete" class="d-inline">
                                    <input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
                                    <button type="submit" class="btn btn-link btn-sm p-0 text-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($entries)): ?><tr><td colspan="3" class="text-muted small">No assessments yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <form method="post" action="/predicted-grade/entry" class="row g-2 align-items-end">
                <input type="hidden" name="category" value="ia">
                <div class="col-auto"><label class="form-label small mb-0">Grade (1–7)</label><input type="number" name="score" class="form-control form-control-sm" placeholder="1–7" min="1" max="7" step="1" required style="width:4rem"></div>
                <div class="col-auto"><label class="form-label small mb-0">Label (optional)</label><input type="text" name="label" class="form-control form-control-sm" placeholder="e.g. Draft 1" style="width:8rem"></div>
                <div class="col-auto"><button type="submit" class="btn btn-success btn-sm">Add</button></div>
            </form>
        </div>
    </div>

    <!-- Paper 2 topics -->
    <div class="card mb-3 pg-paper2">
        <div class="card-header text-white" style="background-color:#fd7e14">
            <h3 class="h6 mb-0">Paper 2 (<?= (int)round($w['paper2']*100) ?>%)</h3>
        </div>
        <div class="card-body">
            <?php foreach ($paper2Topics as $cat): ?>
                <?php $entries = $entriesByCategory[$cat] ?? []; $avg = $categoryAvg[$cat] ?? null; $topicLabel = \App\Models\PredictedGradeEntry::getCategoryLabel($cat); ?>
                <div class="mb-4">
                    <strong><?= htmlspecialchars($topicLabel) ?></strong>
                    <?php if ($avg !== null): ?><span class="text-muted small">(avg <?= number_format($avg, 1) ?>)</span><?php endif; ?>
                    <div class="table-responsive mt-1">
                        <table class="table table-sm table-bordered mb-2" style="max-width: 24rem;">
                            <thead class="table-light"><tr><th>Label</th><th>Grade (1–7)</th><th class="text-end">Actions</th></tr></thead>
                            <tbody>
                                <?php foreach ($entries as $e): ?>
                                <tr>
                                    <td><?= $e['label'] ? htmlspecialchars($e['label']) : '—' ?></td>
                                    <td><?= (int)round($e['score']) ?></td>
                                    <td class="text-end">
                                        <form method="post" action="/predicted-grade/entry/delete" class="d-inline">
                                            <input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
                                            <button type="submit" class="btn btn-link btn-sm p-0 text-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($entries)): ?><tr><td colspan="3" class="text-muted small">No assessments yet.</td></tr><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <form method="post" action="/predicted-grade/entry" class="row g-2 align-items-end">
                        <input type="hidden" name="category" value="<?= htmlspecialchars($cat) ?>">
                        <div class="col-auto"><label class="form-label small mb-0">Grade (1–7)</label><input type="number" name="score" class="form-control form-control-sm" placeholder="1–7" min="1" max="7" step="1" required style="width:4rem"></div>
                        <div class="col-auto"><label class="form-label small mb-0">Label (optional)</label><input type="text" name="label" class="form-control form-control-sm" placeholder="e.g. Quiz 1" style="width:8rem"></div>
                        <div class="col-auto"><button type="submit" class="btn btn-sm" style="background:#fd7e14;color:#fff">Add</button></div>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Homework & habits (soft factors) -->
    <?php $softTopics = \App\Models\PredictedGradeEntry::SOFT_FACTOR_TOPICS; ?>
    <div class="card mb-3 pg-soft">
        <div class="card-header text-white" style="background-color:#6f42c1">
            <h3 class="h6 mb-0">Homework & habits</h3>
        </div>
        <div class="card-body">
            <p class="small text-muted mb-2">These are separate from the exam 100%. Your teacher can give them a weight (e.g. 10%) so: Final = 90% × Exam avg + 10% × average of these.</p>
            <?php foreach ($softTopics as $cat): ?>
                <?php $entries = $entriesByCategory[$cat] ?? []; $avg = $categoryAvg[$cat] ?? null; $topicLabel = \App\Models\PredictedGradeEntry::getCategoryLabel($cat); ?>
                <div class="mb-4">
                    <strong><?= htmlspecialchars($topicLabel) ?></strong>
                    <?php if ($avg !== null): ?><span class="text-muted small">(avg <?= number_format($avg, 1) ?>)</span><?php endif; ?>
                    <div class="table-responsive mt-1">
                        <table class="table table-sm table-bordered mb-2" style="max-width: 24rem;">
                            <thead class="table-light"><tr><th>Label</th><th>Grade (1–7)</th><th class="text-end">Actions</th></tr></thead>
                            <tbody>
                                <?php foreach ($entries as $e): ?>
                                <tr>
                                    <td><?= $e['label'] ? htmlspecialchars($e['label']) : '—' ?></td>
                                    <td><?= (int)round($e['score']) ?></td>
                                    <td class="text-end">
                                        <form method="post" action="/predicted-grade/entry/delete" class="d-inline">
                                            <input type="hidden" name="id" value="<?= (int)$e['id'] ?>">
                                            <button type="submit" class="btn btn-link btn-sm p-0 text-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($entries)): ?><tr><td colspan="3" class="text-muted small">No entries yet.</td></tr><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <form method="post" action="/predicted-grade/entry" class="row g-2 align-items-end">
                        <input type="hidden" name="category" value="<?= htmlspecialchars($cat) ?>">
                        <div class="col-auto"><label class="form-label small mb-0">Grade (1–7)</label><input type="number" name="score" class="form-control form-control-sm" placeholder="1–7" min="1" max="7" step="1" required style="width:4rem"></div>
                        <div class="col-auto"><label class="form-label small mb-0">Label (optional)</label><input type="text" name="label" class="form-control form-control-sm" placeholder="e.g. Term 1" style="width:8rem"></div>
                        <div class="col-auto"><button type="submit" class="btn btn-secondary btn-sm">Add</button></div>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var btn = document.getElementById('copy-code-btn');
    if (btn) {
        btn.addEventListener('click', function() {
            var codeEl = document.getElementById('student-code');
            if (codeEl) {
                navigator.clipboard.writeText(codeEl.textContent).then(function() {
                    btn.textContent = 'Copied!';
                    setTimeout(function() { btn.textContent = 'Copy'; }, 2000);
                });
            }
        });
    }
});
</script>

<?php require ROOT_PATH . '/app/Views/partials/footer.php'; ?>
