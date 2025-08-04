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

            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <div class="d-flex justify-content-end mb-3">
                    <a href="/admin/notes/<?= $note['id'] ?>/edit" class="btn btn-primary me-2">
                        <i class="bi bi-pencil"></i> Edit Note
                    </a>
                    <?php if ($settings['show_delete_buttons'] === 'true'): ?>
                        <a href="/admin/notes/<?= $note['id'] ?>/delete" 
                           class="btn btn-danger"
                           onclick="return confirm('Are you sure you want to delete this note?')">
                            <i class="bi bi-trash"></i> Delete Note
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Quick Links -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-link-45deg text-primary me-2"></i>
                        <h3 class="h5 mb-0">Quick Links</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-3 justify-content-center">
                        <?php if (!empty($course['github_link'])): ?>
                            <a href="<?= htmlspecialchars($course['github_link']) ?>" class="btn btn-outline-secondary" target="_blank">
                                <i class="bi bi-github"></i> GitHub
                            </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($course['lms_link'])): ?>
                            <a href="<?= htmlspecialchars($course['lms_link']) ?>" class="btn btn-outline-primary" target="_blank">
                                <i class="bi bi-mortarboard"></i> LMS
                            </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($course['help_link'])): ?>
                            <a href="<?= htmlspecialchars($course['help_link']) ?>" class="btn btn-outline-info" target="_blank">
                                <i class="bi bi-question-circle"></i> Get Help
                            </a>
                        <?php endif; ?>
                        
                        <a href="/syllabus/<?= $course['id'] ?>" class="btn btn-outline-success">
                            <i class="bi bi-file-text"></i> Syllabus
                        </a>
                        
                        <a href="/courses/<?= $course['id'] ?>/sections/<?= $section['id'] ?>/notes" class="btn btn-outline-warning">
                            <i class="bi bi-journal-text"></i> All Notes
                        </a>


                        
                        <?php if (!empty($course['library_link'])): ?>
                            <a href="<?= htmlspecialchars($course['library_link']) ?>" class="btn btn-outline-danger" target="_blank">
                                <i class="bi bi-book"></i> Class Library
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Daily Note Content -->
            <div class="card mb-5">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="h5 mb-0">
                            <i class="bi bi-journal-text text-primary me-2"></i>
                            Daily Note for <?= date('F j, Y', strtotime($note['date'])) ?>
                        </h3>
                    </div>
         
                </div>
                <div class="card-body">
                    <?= $note['content'] ?>
                </div>
            </div>


        </div>
    </div>
</div>

<?php require ROOT_PATH . '/app/Views/partials/footer.php'; ?> 