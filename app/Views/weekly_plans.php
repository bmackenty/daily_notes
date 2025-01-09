<?php require ROOT_PATH . '/app/Views/partials/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/courses">Courses</a></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($course['name']) ?> Weekly Plans</li>
                </ol>
            </nav>

            <div class="card">
                <div class="card-header bg-light">
                    <h3 class="h5 mb-0">
                        <i class="bi bi-calendar-week text-primary me-2"></i>
                        Weekly Plans for <?= htmlspecialchars($course['name']) ?>
                    </h3>
                </div>
                <div class="card-body">
                    <?php if (empty($plans)): ?>
                        <div class="alert alert-info">
                            No weekly plans have been set for this course yet.
                        </div>
                    <?php else: ?>
                        <div class="accordion" id="weeklyPlansAccordion">
    <?php foreach ($plans as $plan): ?>
        <div class="accordion-item">
            <h2 class="accordion-header" id="heading<?= $plan['academic_week_id'] ?>">
                <button class="accordion-button collapsed" type="button" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#collapse<?= $plan['academic_week_id'] ?>"
                        aria-expanded="false" 
                        aria-controls="collapse<?= $plan['academic_week_id'] ?>">
                    Week <?= htmlspecialchars($plan['week_number']) ?>: 
                    <?= htmlspecialchars($plan['topic']) ?>
                    <small class="text-muted ms-2">
                        (<?= date('M d', strtotime($plan['start_date'])) ?> - 
                        <?= date('M d', strtotime($plan['end_date'])) ?>)
                    </small>
                </button>
            </h2>
            <div id="collapse<?= $plan['academic_week_id'] ?>" 
                 class="accordion-collapse collapse" 
                 data-bs-parent="#weeklyPlansAccordion">
                <div class="accordion-body">
                    <?php if (!empty($plan['objectives'])): ?>
                        <div class="mb-3">
                            <h5 class="h6">Learning Objectives</h5>
                            <div class="mb-0"><?= $plan['objectives'] ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($plan['resources'])): ?>
                        <div class="mb-3">
                            <h5 class="h6">Resources</h5>
                            <div class="mb-0"><?= $plan['resources'] ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($plan['notes'])): ?>
                        <div>
                            <h5 class="h6">Additional Notes</h5>
                            <div class="mb-0"><?= $plan['notes'] ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/app/Views/partials/footer.php'; ?> 