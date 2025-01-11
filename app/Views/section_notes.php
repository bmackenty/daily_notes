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
                        <div class="list-group">
                            <?php foreach ($notes as $note): ?>
                                <a href="/courses/<?= $course['id'] ?>/sections/<?= $section['id'] ?>/notes/<?= $note['id'] ?>" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-journal-text text-primary me-2"></i>
                                        <?= date('l, F j, Y', strtotime($note['date'])) ?>
                                        <span class="text-muted ms-2">
                                            (<?= human_timing(strtotime($note['date'])) ?>)
                                        </span>
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