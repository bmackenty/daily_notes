<?php require ROOT_PATH . '/app/Views/partials/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Topics Tag Cloud</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($tagCloud as $tag): ?>
                        <a href="/courses/<?= $course['id'] ?>/tags/<?= urlencode($tag['name']) ?>" 
                           class="btn btn-outline-primary btn-sm m-1" 
                           style="font-size: <?= min(max(100 + ($tag['count'] * 20), 100), 200) ?>%">
                            <?= htmlspecialchars($tag['name']) ?>
                            <span class="badge bg-secondary"><?= $tag['count'] ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <h2>Notes tagged with "<?= htmlspecialchars($tagName) ?>"</h2>
            <?php foreach ($notes as $note): ?>
                <!-- Use your existing note display code here -->
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/app/Views/partials/footer.php'; ?> 