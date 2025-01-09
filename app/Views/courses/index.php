<?php require ROOT_PATH . '/app/Views/partials/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">All Courses</h2>
            
            <div class="row g-4">
                <?php foreach ($courses as $course): ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm d-flex flex-column">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-book-half text-primary me-2" style="font-size: 1.5rem;"></i>
                                <h5 class="card-title mb-0">
                                    <a href="/syllabus/<?= $course['id'] ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($course['name']) ?>
                                    </a>
                                </h5>
                            </div>
                            <p class="card-text flex-grow-1"><?= htmlspecialchars($course['description']) ?></p>
                            <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-person-circle text-secondary me-2"></i>
                                    <span><?= htmlspecialchars($course['teacher']) ?></span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-collection text-info me-2"></i>
                                    <span class="text-muted"><?= count($sections[$course['id']]) ?> sections</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/app/Views/partials/footer.php'; ?>