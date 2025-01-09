<?php require ROOT_PATH . '/app/Views/partials/header.php'; ?>

<div class="container mt-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($course['name']) ?> Syllabus</li>
        </ol>
    </nav>

    <div class="card">
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
                            <!-- Course Description -->
                            <div class="mb-4 border border-info rounded p-3">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-info-circle-fill text-info me-2" style="font-size: 1.2rem;"></i>
                                    <h3 class="h5 mb-0">Course Description</h3>
                                </div>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($course['description'])) ?></p>
                            </div>

                            <!-- Course Aims -->
                            <div class="mb-4 border border-success rounded p-3">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-bullseye text-success me-2" style="font-size: 1.2rem;"></i>
                                    <h3 class="h5 mb-0">Course Aims</h3>
                                </div>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($course['aims'])) ?></p>
                            </div>

                            <!-- Assessment Methods -->
                            <div class="mb-4 border border-warning rounded p-3">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-clipboard-check text-warning me-2" style="font-size: 1.2rem;"></i>
                                    <h3 class="h5 mb-0">Assessment Methods</h3>
                                </div>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($course['assessment'])) ?></p>
                            </div>

                            <!-- Required Materials -->
                            <div class="mb-4 border border-primary rounded p-3">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-journal-bookmark text-primary me-2" style="font-size: 1.2rem;"></i>
                                    <h3 class="h5 mb-0">Required Materials</h3>
                                </div>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($course['required'])) ?></p>
                            </div>

                            <!-- Communication -->
                            <div class="mb-4 border border-secondary rounded p-3">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-chat-dots text-secondary me-2" style="font-size: 1.2rem;"></i>
                                    <h3 class="h5 mb-0">Communication</h3>
                                </div>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($course['communication'])) ?></p>
                            </div>

                            <!-- Policies -->
                            <div class="mb-4 border border-danger rounded p-3">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-shield-check text-danger me-2" style="font-size: 1.2rem;"></i>
                                    <h3 class="h5 mb-0">Policies</h3>
                                </div>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($course['policies'])) ?></p>
                            </div>

                            <!-- Rules -->
                            <div class="mb-4 border border-dark rounded p-3">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-list-check text-dark me-2" style="font-size: 1.2rem;"></i>
                                    <h3 class="h5 mb-0">Rules</h3>
                                </div>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($course['rules'])) ?></p>
                            </div>
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
                                    <strong>Teacher</strong>
                                </div>
                                <p class="ms-4 mb-0"><?= htmlspecialchars($course['teacher']) ?></p>
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
                                <p class="ms-4 mb-0"><?= count($sections) ?> sections</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/app/Views/partials/footer.php'; ?> 