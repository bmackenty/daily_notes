<?php require ROOT_PATH . '/app/Views/partials/header.php'; ?>

<!-- Add Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<div class="container mt-5">
    <!-- Feature Grid Section -->
    <section class="mb-5">
        <h2 class="mb-4">Welcome</h2>
        <div class="row g-4">
            <div class="col-md-12">
                <div class="card h-100 border-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-journal-text text-primary me-3" style="font-size: 2rem;"></i>
                            <div>
                                <h5 class="card-title">Daily Learning Notes</h5>
                                <p class="card-text">This system is designed for teachers to post daily 
                                    lessons for their students, helping students with review, retention and learning. Daily notes have 
                                    connections to standards, concepts, differentiated instruction, and more.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
      
        </div>
    </section>
<!-- Sections Section -->
<section class="mb-5">
        <h2 class="mb-4">Course Sections</h2>
        <?php foreach ($courses as $course): ?>
        <div class="card mb-4">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="h5 mb-0">
                        <i class="bi bi-book text-primary me-2"></i>
                        <?= htmlspecialchars($course['name']) ?>
                    </h3>

                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($sections[$course['id']])): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Section Name</th>
                                <th>Meeting Place</th>
                                <th>Latest Note</th>
                                <th>Links</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sections[$course['id']] as $section): ?>
                            <tr>
                                <td><?= htmlspecialchars($section['name']) ?></td>
                                <td><?= htmlspecialchars($section['meeting_place'] ?? '') ?></td>
                                <td>
                                    <?php 
                                    if (!empty($notes[$section['id']])) {
                                        $latestNote = reset($notes[$section['id']]); // Get first element (latest note)
                                        echo '<div class="d-flex align-items-center">';
                                        echo '<i class="bi bi-journal-text text-success me-2"></i>';
                                        echo '<a href="/courses/' . $course['id'] . '/sections/' . $section['id'] . '/notes" ';
                                        echo 'class="text-decoration-none" data-bs-toggle="tooltip" ';
                                        echo 'title="View all notes for this section">';
                                        echo htmlspecialchars(date('M j, Y', strtotime($latestNote['date'])));
                                        echo '</a>';
                                        echo '</div>';
                                    } else {
                                        echo '<span class="text-muted"><i class="bi bi-journal-x me-2"></i>No notes yet</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="/courses/<?= $course['id'] ?>/sections/<?= $section['id'] ?>/notes" class="btn btn-sm btn-outline-success me-2">
                                        <i class="bi bi-journal-text"></i> Daily Notes
                                    </a>
                                    <a href="/syllabus/<?= $course['id'] ?>" class="btn btn-sm btn-outline-primary me-2">
                                        <i class="bi bi-file-text"></i> Syllabus
                                    </a>

                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="mb-0">No sections available for this course.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </section>

    
    <!-- Courses Section -->
    <section class="mb-5">
        <h2 class="mb-4">Available Courses</h2>
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
                        <div class="card-text flex-grow-1"><?= $course['description'] ?></div>
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-circle text-secondary me-2"></i>
                                <?php if (!empty($course['teacher_profile_id'])): ?>
                                    <a href="/teacher-profile/<?= $course['teacher_profile_id'] ?>">
                                        <?= htmlspecialchars($course['teacher']) ?>
                                    </a>
                                <?php else: ?>
                                    <?= htmlspecialchars($course['teacher']) ?>
                                <?php endif; ?>
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
    </section>

    
</div>

<?php require ROOT_PATH . '/app/Views/partials/footer.php'; ?>
