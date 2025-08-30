<?php require ROOT_PATH . '/app/Views/partials/header.php'; ?>

<div class="container mt-3">
    <h1>Test Page</h1>
    
    <p>PHP is working if you see this text.</p>
    
    <?php if (isset($courses) && is_array($courses)): ?>
        <h2>Courses Found: <?= count($courses) ?></h2>
        <ul>
        <?php foreach ($courses as $course): ?>
            <li><?= htmlspecialchars($course['name'] ?? 'Unknown') ?></li>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No courses data available.</p>
        <p>Debug info: <?= var_export($courses ?? 'undefined', true) ?></p>
    <?php endif; ?>
</div>

<?php require ROOT_PATH . '/app/Views/partials/footer.php'; ?>
