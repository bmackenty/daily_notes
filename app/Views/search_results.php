<?php require ROOT_PATH . '/app/Views/partials/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/courses">Courses</a></li>
                    <li class="breadcrumb-item"><a href="/courses/<?= $course['id'] ?>"><?= htmlspecialchars($course['name']) ?></a></li>
                    <?php if ($sectionId): ?>
                        <li class="breadcrumb-item"><a href="/courses/<?= $course['id'] ?>/sections/<?= $sectionId ?>/notes"><?= htmlspecialchars($section['name']) ?></a></li>
                    <?php endif; ?>
                    <li class="breadcrumb-item active">Search Results</li>
                </ol>
            </nav>

            <div class="card mb-4">
                <div class="card-header bg-light">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-search text-primary me-2"></i>
                        <h3 class="h5 mb-0">Search Notes</h3>
                    </div>
                </div>
                <div class="card-body">
                    <form action="<?= $sectionId ? "/courses/$courseId/sections/$sectionId/search" : "/courses/$courseId/search" ?>" method="GET" class="d-flex gap-2">
                        <input type="text" name="q" value="<?= htmlspecialchars($query) ?>" 
                               class="form-control" placeholder="Search notes...">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </form>
                </div>
            </div>

            <?php if (empty($query)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Enter a search term to find notes.
                </div>
            <?php elseif (empty($notes)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    No notes found matching your search.
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-header bg-light">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-search text-primary me-2"></i>
                            <h3 class="h5 mb-0">Search Results</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <?php foreach ($notes as $note): ?>
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h4 class="h6 mb-0">
                                            <a href="/courses/<?= $courseId ?>/sections/<?= $note['section_id'] ?>/notes/<?= $note['id'] ?>" 
                                               class="text-decoration-none">
                                                <?= htmlspecialchars($note['title']) ?>
                                            </a>
                                        </h4>
                                        <small class="text-muted">
                                            <?= date('F j, Y', strtotime($note['date'])) ?>
                                        </small>
                                    </div>
                                    <div class="text-muted small mb-2">
                                        <?= htmlspecialchars($note['course_name']) ?> > <?= htmlspecialchars($note['section_name']) ?>
                                    </div>
                                    <div class="note-preview">
                                        <?= substr(strip_tags($note['content']), 0, 200) ?>...
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/app/Views/partials/footer.php'; ?> 