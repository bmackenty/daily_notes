<?php 
// Function to check if note is from the past
function isPastNote($noteDate) {
    $noteTimestamp = strtotime($noteDate);
    $today = strtotime('today');
    return $noteTimestamp < $today;
}

// Function to get human-readable time difference
function human_timing($timestamp) {
    $time = time() - $timestamp;
    
    if ($time < 0) {
        $time = abs($time);
        $tokens = array (
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day'
        );
        foreach ($tokens as $unit => $text) {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            return 'in ' . $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '');
        }
        return 'today';
    }
    
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

$isPastNote = isPastNote($note['date']);
$timeAgo = human_timing(strtotime($note['date']));
?>

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

            <!-- Past Note Indicator -->
            <?php if ($isPastNote): ?>
                <div class="alert alert-info mb-4 past-note-indicator">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-clock-history text-info me-3 fs-4"></i>
                        <div>
                            <h5 class="mb-1">Past Lesson Note</h5>
                            <p class="mb-0 text-muted">
                                This note is from <?= $timeAgo ?>. You're viewing content from a previous lesson.
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Daily Note Content -->
            <div class="card mb-5 <?= $isPastNote ? 'past-note-card' : '' ?>">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="h5 mb-0">
                            <i class="bi bi-journal-text text-primary me-2"></i>
                            Daily Note for <?= date('F j, Y', strtotime($note['date'])) ?>
                            <?php if ($isPastNote): ?>
                                <span class="badge bg-secondary ms-2">Past Lesson</span>
                            <?php endif; ?>
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

<style>
.past-note-indicator {
    border-left: 4px solid #17a2b8;
    background-color: #f8f9fa;
}

.past-note-indicator .bi-clock-history {
    font-size: 1.5rem;
}

.past-note-card {
    border-left: 4px solid #6c757d;
    opacity: 0.95;
}

.past-note-card .card-header {
    background-color: #f8f9fa !important;
}

.past-note-card .badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}
</style>

<?php require ROOT_PATH . '/app/Views/partials/footer.php'; ?> 