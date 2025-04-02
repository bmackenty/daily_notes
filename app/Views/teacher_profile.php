<?php require ROOT_PATH . '/app/Views/partials/header.php'; ?>

<div class="container mt-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item active">Teacher Profile</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-4">
            <div class="text-center mb-4">
                <img src="<?= !empty($profile['profile_picture']) ? htmlspecialchars($profile['profile_picture']) : '/public/assets/images/default-avatar.svg' ?>" 
                     class="img-fluid rounded-circle mb-3" 
                     style="max-width: 200px; height: 200px; object-fit: cover;" 
                     alt="<?= htmlspecialchars($profile['full_name']) ?>'s Profile Picture">
                <h2><?= htmlspecialchars($profile['full_name']) ?></h2>
                <p class="text-muted"><?= htmlspecialchars($profile['title']) ?></p>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h3 class="card-title">Contact Information</h3>
                    <p><i class="bi bi-envelope me-2"></i><?= htmlspecialchars($profile['email']) ?></p>
                    <?php if (!empty($profile['office_hours'])): ?>
                        <p><i class="bi bi-clock me-2"></i><?= nl2br(htmlspecialchars($profile['office_hours'])) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($profile['contact_preferences'])): ?>
                        <p><i class="bi bi-chat me-2"></i><?= nl2br(htmlspecialchars($profile['contact_preferences'])) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <?php if (!empty($profile['biography'])): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h3 class="card-title">Biography</h3>
                    <p><?= nl2br(htmlspecialchars($profile['biography'])) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($profile['education'])): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h3 class="card-title">Education & Credentials</h3>
                    <p><?= nl2br(htmlspecialchars($profile['education'])) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($profile['teaching_philosophy'])): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h3 class="card-title">Teaching Philosophy</h3>
                    <p><?= nl2br(htmlspecialchars($profile['teaching_philosophy'])) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($profile['vision_for_students'])): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h3 class="card-title">Vision for Students</h3>
                    <p><?= nl2br(htmlspecialchars($profile['vision_for_students'])) ?></p>
                </div>
            </div>
            <?php endif; ?>

                    <h3 class="card-title">Courses</h3>
                    <div class="list-group mb-4">
                        <?php foreach ($courses as $course): ?>
                            <a href="/syllabus/<?= $course['id'] ?>" class="list-group-item list-group-item-action">
                                <i class="bi bi-book me-2"></i><?= htmlspecialchars($course['name']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/app/Views/partials/footer.php'; ?> 