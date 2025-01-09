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

            <div class="card">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="h5 mb-0">
                            <i class="bi bi-journal-text text-primary me-2"></i>
                            Daily Notes for <?= htmlspecialchars($section['name']) ?>
                        </h3>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($notes)): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            No daily notes have been added for this section yet.
                        </div>
                    <?php else: ?>
                        <div class="accordion" id="notesAccordion">
                            <?php foreach ($notes as $note): ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#note<?= $note['id'] ?>">
                                            <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                                <span>
                                                    <i class="bi bi-journal-text text-primary me-2"></i>
                                                    <?= date('F j, Y', strtotime($note['date'])) ?>
                                                    <span class="text-muted">
                                                        (<?= human_timing(strtotime($note['date'])) ?>)
                                                    </span>
                                                </span>
                                                <span class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-tag"></i> Topic: <?= htmlspecialchars($note['title']) ?>
                                                </span>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="note<?= $note['id'] ?>" class="accordion-collapse collapse" 
                                         data-bs-parent="#notesAccordion">
                                        <div class="accordion-body">
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <h5 class="h6">Our notes for today:</h5>
                                                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin'): ?>
                                                        <a href="/admin/notes/<?= $note['id'] ?>/edit" class="btn btn-sm btn-primary">
                                                            <i class="bi bi-pencil"></i> Edit Note
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="mb-3"><?= $note['content'] ?></div>
                                            </div>
                                            
                                            <?php if (!empty($note['homework'])): ?>
                                                <div class="mb-3">
                                                    <h5 class="h6">Homework</h5>
                                                    <div class="mb-3"><?= $note['homework'] ?></div>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (!empty($note['resources'])): ?>
                                                <div>
                                                    <h5 class="h6">Resources</h5>
                                                    <div><?= $note['resources'] ?></div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
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