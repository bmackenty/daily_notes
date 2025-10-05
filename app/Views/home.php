<?php require ROOT_PATH . '/app/Views/partials/header.php'; ?>

<?php
/**
 * Helper function to format relative date
 * Returns human-readable relative time (e.g., "2 days ago", "in 3 days", "today")
 */
function formatRelativeDate($date) {
    $noteDate = new DateTime($date);
    $today = new DateTime();
    $today->setTime(0, 0, 0); // Reset time to start of day
    $noteDate->setTime(0, 0, 0); // Reset time to start of day
    
    $diff = $today->diff($noteDate);
    $daysDiff = (int)$diff->format('%r%a'); // %r gives sign, %a gives absolute days
    
    if ($daysDiff === 0) {
        return 'today';
    } elseif ($daysDiff === 1) {
        return 'tomorrow';
    } elseif ($daysDiff === -1) {
        return 'yesterday';
    } elseif ($daysDiff > 0) {
        return 'in ' . $daysDiff . ' day' . ($daysDiff > 1 ? 's' : '');
    } else {
        return abs($daysDiff) . ' day' . (abs($daysDiff) > 1 ? 's' : '') . ' ago';
    }
}
?>

<!-- Add Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  #courses-sections .card { border-radius: .5rem; }
  #courses-sections .card-header { padding: .5rem .75rem; }
  #courses-sections .card-body { padding: .5rem .75rem; }
  #courses-sections table.table td,
  #courses-sections table.table th { padding: .35rem .5rem; vertical-align: middle; }
  #courses-sections h3.h5 { margin: 0; }
  #courses-sections .btn { padding: .125rem .375rem; line-height: 1; }
  #courses-sections .badge-date { font-weight: 500; }
  #courses-sections .text-tight { line-height: 1.1; }
  #courses-sections .nowrap { white-space: nowrap; }
</style>
<div class="container mt-5">
    <!-- Server Time Display -->
    <section class="mb-4">
        <?php 
        // Set timezone to Warsaw, Poland
        date_default_timezone_set('Europe/Warsaw');
        ?>
        <div class="alert alert-info d-flex align-items-center" role="alert">
            <i class="bi bi-clock me-2"></i>
            <span class="me-2">Warsaw Time:</span>
            <strong><?= date('l, F j, Y \a\t g:i A T') ?></strong>
        </div>
    </section>

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
                                <p class="card-text">Daily Learning Notes give you a quick, clear summary of what we 
                                    covered in class each day. They're here to help you review important ideas, 
                                    remember what you learned, and stay on track — even if you missed a lesson. 
                                    Each note connects to our learning goals, key concepts, and extra resources so you can 
                                    study in a way that works best for you.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


<!-- Sections Section (Compact) -->


<section class="mb-5" id="courses-sections">
    <h2 class="mb-3">Courses and Sections</h2>
    <?php foreach ($courses as $course): ?>
    <div class="card mb-3">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="h5 mb-0 text-tight">
                    <i class="bi bi-book text-primary me-2"></i>
                    <?= htmlspecialchars($course['name']) ?>
                </h3>
            </div>
        </div>
        <div class="card-body">
            <?php if (!empty($sections[$course['id']])): ?>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="small">
                        <tr>
                            <th class="text-muted fw-semibold">Section</th>
                            <th class="text-muted fw-semibold">Room</th>
                            <th class="text-muted fw-semibold">Latest note</th>
                            <th class="text-muted fw-semibold text-end">Links</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        <?php foreach ($sections[$course['id']] as $section): ?>
                        <tr>
                            <td class="nowrap"><?= htmlspecialchars($section['name']) ?></td>
                            <td class="nowrap"><?= htmlspecialchars($section['meeting_place'] ?? '') ?></td>
                            <td class="nowrap">
                                <?php if (!empty($notes[$section['id']])): 
                                    $latestNote = reset($notes[$section['id']]); 
                                    $relativeDate = formatRelativeDate($latestNote['date']);
                                    $fullDate = date('M j, Y', strtotime($latestNote['date'])); ?>
                                    <i class="bi bi-calendar-event text-success me-1"></i>
                                    <a
                                        href="/courses/<?= $course['id'] ?>/sections/<?= $section['id'] ?>/notes"
                                        class="text-decoration-none"
                                        data-bs-toggle="tooltip"
                                        title="View all notes for this section"
                                    >
                                        <span class="badge bg-light text-secondary border badge-date">
                                            <?= htmlspecialchars($fullDate) ?> (<?= htmlspecialchars($relativeDate) ?>)
                                        </span>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">
                                        <i class="bi bi-journal-x me-1"></i>No notes
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a 
                                    href="/courses/<?= $course['id'] ?>/sections/<?= $section['id'] ?>/notes" 
                                    class="btn btn-outline-success btn-sm me-1"
                                    data-bs-toggle="tooltip" 
                                    title="Daily Notes"
                                >
                                    <i class="bi bi-journal-text me-1"></i> Daily Notes
                                </a>
                                <a 
                                    href="/syllabus/<?= $course['id'] ?>" 
                                    class="btn btn-outline-primary btn-sm"
                                    data-bs-toggle="tooltip" 
                                    title="Syllabus"
                                >
                                    <i class="bi bi-file-text me-1"></i> Syllabus
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p class="mb-0 small text-muted">No sections available for this course.</p>
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
                            <i class="bi bi-book-half text-primary me-2" style="font-size: 2rem;"></i>
                            <h5 class="card-title mb-0">
                                <a href="/syllabus/<?= $course['id'] ?>" class="text-decoration-none">
                                    <?= htmlspecialchars($course['name']) ?>
                                </a>
                            </h5>
                        </div>
                        <div class="card-text flex-grow-1"><?= $course['description'] ?? 'No description available' ?></div>
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
                                <?= count($sections[$course['id']]) ?> sections
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
