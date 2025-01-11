<?php require ROOT_PATH . '/app/Views/partials/header.php'; ?>

<div class="container mt-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/courses">Courses</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($course['name']) ?></li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header">
            <h2><?= htmlspecialchars($course['name']) ?></h2>
        </div>
        <div class="card-body">
            <h3>Sections</h3>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Section</th>
                            <th>Meeting Time</th>
                            <th>Meeting Place</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sections as $section): ?>
                            <tr>
                                <td><?= htmlspecialchars($section['name']) ?></td>
                                <td><?= htmlspecialchars($section['meeting_time'] ?? '') ?></td>
                                <td><?= htmlspecialchars($section['meeting_place'] ?? '') ?></td>
                                <td>
                                    <a href="/courses/<?= $course['id'] ?>/sections/<?= $section['id'] ?>/notes" 
                                       class="btn btn-sm btn-outline-success me-2">
                                        <i class="bi bi-journal-text"></i> Daily Notes
                                    </a>
                                    <a href="/syllabus/<?= $course['id'] ?>" 
                                       class="btn btn-sm btn-outline-primary me-2">
                                        <i class="bi bi-file-text"></i> Syllabus
                                    </a>
                                    <a href="/courses/<?= $course['id'] ?>/yearly-plans" 
                                       class="btn btn-sm btn-outline-info">
                                        <i class="bi bi-calendar-week"></i> Yearly Plan
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/app/Views/partials/footer.php'; ?> 