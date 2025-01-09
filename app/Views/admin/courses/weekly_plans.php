<?php require ROOT_PATH . '/app/Views/partials/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/courses">Courses</a></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($course['name']) ?> Weekly Plans</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Weekly Plans for <?= htmlspecialchars($course['name']) ?></h2>
         
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <?php if (empty($weeks)): ?>
                        <div class="alert alert-warning">
                            No weeks defined in the active academic year. 
                            <a href="/admin/settings/academic-years" class="alert-link">Click here to set up the academic year calendar</a>.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Week</th>
                                        <th>Dates</th>
                                        <th>Topic</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($weeks as $week): ?>
                                        <?php 
                                        $plan = array_filter($plans, function($p) use ($week) {
                                            return $p['academic_week_id'] == $week['id'];
                                        });
                                        $plan = !empty($plan) ? reset($plan) : null;
                                        ?>
                                        <tr>
                                            <td>Week <?= htmlspecialchars($week['week_number']) ?></td>
                                            <td>
                                                <?= date('M d', strtotime($week['start_date'])) ?> - 
                                                <?= date('M d, Y', strtotime($week['end_date'])) ?>
                                            </td>
                                            <td><?= $plan ? htmlspecialchars($plan['topic']) : '<em>Not set</em>' ?></td>
                                            <td>
                                                <a href="/admin/courses/<?= $course['id'] ?>/weekly-plans/<?= $week['id'] ?>/<?= $plan ? 'edit' : 'create' ?>" 
                                                   class="btn btn-primary btn-sm">
                                                    <?= $plan ? 'Edit' : 'Create' ?> Plan
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Weekly Plan Modals -->
                        <?php foreach ($weeks as $week): ?>
                            <div class="modal fade" id="weekPlanModal<?= $week['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                Week <?= $week['week_number'] ?> Plan 
                                                (<?= date('M d', strtotime($week['start_date'])) ?> - 
                                                <?= date('M d, Y', strtotime($week['end_date'])) ?>)
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST" action="/admin/courses/<?= $course['id'] ?>/weekly-plans/<?= $week['id'] ?>">
                                            <div class="modal-body">
                                                <?php 
                                                $plan = array_filter($plans, function($p) use ($week) {
                                                    return $p['academic_week_id'] == $week['id'];
                                                });
                                                $plan = !empty($plan) ? reset($plan) : null;
                                                ?>
                                                <div class="mb-3">
                                                    <label>Topic</label>
                                                    <input type="text" name="topic" class="form-control" required
                                                           value="<?= $plan ? htmlspecialchars($plan['topic']) : '' ?>">
                                                </div>
                                                <div class="mb-3">
                                                    <label>Learning Objectives</label>
                                                    <textarea name="objectives" class="form-control" rows="3"><?= $plan ? htmlspecialchars($plan['objectives']) : '' ?></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Resources</label>
                                                    <textarea name="resources" class="form-control" rows="3"><?= $plan ? htmlspecialchars($plan['resources']) : '' ?></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label>Additional Notes</label>
                                                    <textarea name="notes" class="form-control" rows="3"><?= $plan ? htmlspecialchars($plan['notes']) : '' ?></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Save Plan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/app/Views/partials/footer.php'; ?> 