<?php require ROOT_PATH . '/app/Views/partials/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/courses">Courses</a></li>
                    <li class="breadcrumb-item">
                        <a href="/courses/<?= $course['id'] ?>"><?= htmlspecialchars($course['name']) ?></a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="/courses/<?= $course['id'] ?>/sections/<?= $section['id'] ?>/notes">
                            <?= htmlspecialchars($section['name']) ?> Daily Notes
                        </a>
                    </li>
                    <li class="breadcrumb-item active">
                        <?= date('F j, Y', strtotime($note['date'])) ?>
                    </li>
                </ol>
            </nav>

            <div class="card">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="h5 mb-0">
                            <i class="bi bi-journal-text text-primary me-2"></i>
                            Daily Note for <?= date('l, F j, Y', strtotime($note['date'])) ?>
                        </h3>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin'): ?>
                            <a href="/admin/notes/<?= $note['id'] ?>/edit" class="btn btn-sm btn-primary">
                                <i class="bi bi-pencil"></i> Edit Note
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5 class="h6">Our note for today:</h5>
                        <div class="mb-3"><?= $note['content'] ?></div>
                    </div>
                    
                    <?php if (!empty($note['homework'])): ?>
                        <div class="mb-4">
                            <h5 class="h6">Homework</h5>
                            <div class="mb-3"><?= $note['homework'] ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($note['resources'])): ?>
                        <div class="mb-4">
                            <h5 class="h6">Resources</h5>
                            <div><?= $note['resources'] ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/app/Views/partials/footer.php'; ?> 