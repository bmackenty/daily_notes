<?php require ROOT_PATH . '/app/Views/partials/header.php'; ?>

<div class="container mt-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($course['name']) ?> Syllabus</li>
        </ol>
    </nav>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <div class="d-flex align-items-center">
                <i class="bi bi-book-half me-2" style="font-size: 1.5rem;"></i>
                <h1 class="h3 mb-0">Syllabus for <?= htmlspecialchars($course['name']) ?></h1>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <!-- Definition of Syllabus -->
                            <div class="mb-4 border border-info rounded p-3">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-info-circle-fill text-info me-2" style="font-size: 1.2rem;"></i>
                                    <h3 class="h5 mb-0">What is a Syllabus?</h3>
                                </div>
                                <p class="mb-0">A syllabus is a document that outlines the key components of a course, including the topics to be covered, the schedule of classes, assessment methods, required materials, and other important information. It serves as a guide for both instructors and students to understand the expectations and structure of the course.</p>
                            </div>

                            <!-- Course Description -->
                            <div class="mb-4 border border-info rounded p-3">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-info-circle-fill text-info me-2" style="font-size: 1.2rem;"></i>
                                    <h3 class="h5 mb-0">Course Description</h3>
                                </div>
                                <div class="mb-0"><?= $course['description'] ?></div>
                            </div>

                            <!-- Course Aims -->
                            <div class="mb-4 border border-success rounded p-3">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-bullseye text-success me-2" style="font-size: 1.2rem;"></i>
                                    <h3 class="h5 mb-0">Course Aims</h3>
                                </div>
                                <div class="mb-0"><?= $course['aims'] ?></div>
                            </div>

                            <!-- Assessment Methods -->
                            <div class="mb-4 border border-warning rounded p-3">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-clipboard-check text-warning me-2" style="font-size: 1.2rem;"></i>
                                    <h3 class="h5 mb-0">Assessment Methods</h3>
                                </div>
                                <div class="mb-0"><?= $course['assessment'] ?></div>
                            </div>

                            <!-- Required Materials -->
                            <div class="mb-4 border border-primary rounded p-3">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-journal-bookmark text-primary me-2" style="font-size: 1.2rem;"></i>
                                    <h3 class="h5 mb-0">Required Materials</h3>
                                </div>
                                <div class="mb-0"><?= $course['required'] ?></div>
                            </div>



                            <!-- Communication -->
                            <div class="mb-4 border border-secondary rounded p-3">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-chat-dots text-secondary me-2" style="font-size: 1.2rem;"></i>
                                    <h3 class="h5 mb-0">Communication</h3>
                                </div>
                                <div class="mb-0"><?= $course['communication'] ?></div>
                            </div>

                            <!-- Policies -->
                            <div class="mb-4 border border-danger rounded p-3">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-shield-check text-danger me-2" style="font-size: 1.2rem;"></i>
                                    <h3 class="h5 mb-0">Policies</h3>
                                </div>
                                <div class="mb-0"><?= $course['policies'] ?></div>
                            </div>

                            <!-- Rules -->
                            <div class="mb-4 border border-dark rounded p-3">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-list-check text-dark me-2" style="font-size: 1.2rem;"></i>
                                    <h3 class="h5 mb-0">Rules</h3>
                                </div>
                                <div class="mb-0"><?= $course['rules'] ?></div>
                            </div>

                            <!-- Academic Integrity -->
                            <?php if (!empty($course['academic_integrity'])): ?>
                            <div class="mb-4 border border-danger rounded p-3">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-shield-exclamation text-danger me-2" style="font-size: 1.2rem;"></i>
                                    <h3 class="h5 mb-0">Academic Integrity</h3>
                                </div>
                                <div class="mb-0"><?= $course['academic_integrity'] ?></div>
                            </div>
                            <?php endif; ?>

                            <!-- Weekly Plan -->
                            <?php if (!empty($course['weekly_plan'])): ?>
                            <div class="mb-4 border border-info rounded p-3">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-calendar-week text-info me-2" style="font-size: 1.2rem;"></i>
                                    <h3 class="h5 mb-0">Weekly Plan</h3>
                                </div>
                                <div class="mb-0"><?= $course['weekly_plan'] ?></div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h3 class="h5 mb-3">Quick Information</h3>
                            
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-person-circle text-primary me-2"></i>
                                    <strong>Instructor</strong>
                                </div>
                                <p class="ms-4 mb-0">
                                    <?php if (!empty($course['teacher_profile_id'])): ?>
                                        <a href="/teacher-profile/<?= $course['teacher_profile_id'] ?>">
                                            <?= htmlspecialchars($course['teacher']) ?>
                                        </a>
                                    <?php else: ?>
                                        <?= htmlspecialchars($course['teacher']) ?>
                                    <?php endif; ?>
                                </p>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-collection text-primary me-2"></i>
                                    <strong>Course Code</strong>
                                </div>
                                <p class="ms-4 mb-0"><?= htmlspecialchars($course['short_name']) ?></p>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-people text-primary me-2"></i>
                                    <strong>Available Sections</strong>
                                </div>
                                <ul class="ms-4 mb-0">
                                    <?php foreach ($sections as $section): ?>
                                        <li>
                                            <a href="/">
                                                <?= htmlspecialchars($section['name']) ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/app/Views/partials/footer.php'; ?> 