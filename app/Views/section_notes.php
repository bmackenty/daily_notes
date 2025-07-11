<?php
function human_timing($timestamp) {
    $time = time() - $timestamp;
    $tokens = array (
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '') . ' ago';
    }
    
    return 'today';
}
?>

<?php require ROOT_PATH . '/app/Views/partials/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/courses">Courses</a></li>
                    <li class="breadcrumb-item"><a href="/courses/<?= $course['id'] ?>"><?= htmlspecialchars($course['name']) ?></a></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($section['name']) ?> Daily Notes</li>
                </ol>
            </nav>

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
                        
                        <a href="/courses/<?= $course['id'] ?>/yearly-plans" class="btn btn-outline-info">
                            <i class="bi bi-calendar-week"></i> Yearly Plan
                        </a>
                        
                        <?php if (!empty($course['library_link'])): ?>
                            <a href="<?= htmlspecialchars($course['library_link']) ?>" class="btn btn-outline-danger" target="_blank">
                                <i class="bi bi-book"></i> Class Library
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Search -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-search text-primary me-2"></i>
                        <h3 class="h5 mb-0">Search Notes</h3>
                    </div>
                </div>
                <div class="card-body">
                    <form action="/courses/<?= $course['id'] ?>/sections/<?= $section['id'] ?>/search" method="GET" class="d-flex gap-2">
                        <input type="text" name="q" class="form-control" placeholder="Search notes..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </form>
                </div>
            </div>

            <!-- Academic Year Info -->
            <?php 
            $isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
            $academicYearModel = new \App\Models\AcademicYear($GLOBALS['pdo']);
            $activeYear = $academicYearModel->getActive();
            ?>
            <div class="alert alert-info mb-4">
                <div class="d-flex align-items-center">
                    <i class="bi bi-calendar-event text-info me-2"></i>
                    <div>
                        <strong>Academic Year:</strong> 
                        <?php if ($activeYear): ?>
                            <?= htmlspecialchars($activeYear['name']) ?>
                            (<?= date('M d, Y', strtotime($activeYear['start_date'])) ?> - <?= date('M d, Y', strtotime($activeYear['end_date'])) ?>)
                        <?php else: ?>
                            <span class="text-warning">No active academic year set</span>
                        <?php endif; ?>
                        
                        <?php if ($isAdmin): ?>
                            <br><small class="text-muted">
                                <i class="bi bi-shield-check"></i> Admin view: Showing ALL notes (including previous academic years)
                            </small>
                        <?php else: ?>
                            <br><small class="text-muted">
                                <i class="bi bi-person"></i> Student view: Showing only notes from current academic year
                            </small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="h5 mb-0">
                            <i class="bi bi-journal-text text-primary me-2"></i>
                            Daily Notes for <?= htmlspecialchars($section['name']) ?>
                            <span class="badge bg-secondary ms-2">
                                <?= count($notes) ?> note<?= count($notes) !== 1 ? 's' : '' ?>
                            </span>
                        </h3>
                    </div>
                </div>
                <?php if (!empty($notes)): ?>
                    <div class="card-body mb-4">
                        <h4 class="h6 text-muted mb-3">
                            Most Recent Note 
                            <span class="text-muted">
                                (<?= human_timing(strtotime($notes[0]['date'])) ?>)
                            </span>
                        </h4>
                        <div class="most-recent-note">
                            <h5><?= date('l, F j, Y', strtotime($notes[0]['date'])) ?></h5>
                            <div class="note-content">
                                <?= $notes[0]['content'] ?>
                            </div>
                            <div class="mt-3">
                                <a href="/courses/<?= $course['id'] ?>/sections/<?= $section['id'] ?>/notes/<?= $notes[0]['id'] ?>" 
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i> View Full Note
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                </div> <!-- close card -->

                <div class="card mb-4">
                    <div class="card-body">
                        <?php if (empty($notes)): ?>
                            <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            No daily notes have been added for this section yet.
                        </div>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($notes as $note): ?>
                                <a href="/courses/<?= $course['id'] ?>/sections/<?= $section['id'] ?>/notes/<?= $note['id'] ?>" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-journal-text text-primary me-2"></i>
                                            <?= date('l, F j, Y', strtotime($note['date'])) ?>
                                            <span class="text-muted ms-2">
                                                (<?= human_timing(strtotime($note['date'])) ?>)
                                            </span>
                                        </div>
                                        <?php if ($isAdmin && isset($note['academic_year_id'])): ?>
                                            <?php 
                                            $noteYear = $academicYearModel->get($note['academic_year_id']);
                                            $isCurrentYear = $activeYear && $note['academic_year_id'] == $activeYear['id'];
                                            ?>
                                            <span class="badge <?= $isCurrentYear ? 'bg-success' : 'bg-warning' ?>">
                                                <?= $noteYear ? htmlspecialchars($noteYear['name']) : 'Unknown Year' ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
});
</script>

<?php require ROOT_PATH . '/app/Views/partials/footer.php'; ?> 