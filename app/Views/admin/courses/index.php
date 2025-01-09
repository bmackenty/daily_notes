<?php require ROOT_PATH . '/app/Views/partials/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Course Management</h2>
                <a href="/admin/courses/create" class="btn btn-primary">Add New Course</a>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Short Name</th>
                                    <th>Teacher</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($courses as $course): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($course['name']) ?></td>
                                        <td><?= htmlspecialchars($course['short_name']) ?></td>
                                        <td><?= htmlspecialchars($course['teacher']) ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="/admin/courses/<?= $course['id'] ?>/edit" class="btn btn-sm btn-primary">Edit</a>
                                                <a href="/admin/courses/<?= $course['id'] ?>/sections" class="btn btn-sm btn-info">Sections</a>
                                                <a href="/admin/courses/<?= $course['id'] ?>/weekly-plans" class="btn btn-sm btn-secondary">
                                                    <i class="bi bi-calendar-week"></i> Weekly Plans
                                                </a>
                                                <a href="/admin/courses/<?= $course['id'] ?>/delete" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Are you sure you want to delete this course?')">Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php 
                                    $sections = $sectionModel->getAllByCourse($course['id']);
                                    if (!empty($sections)): 
                                    ?>
                                    <tr>
                                        <td colspan="4" class="p-0">
                                            <div class="ms-4 my-2 bg-secondary bg-opacity-10 rounded p-3">
                                                <table class="table table-secondary table-sm mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Position</th>
                                                            <th>Name</th>
                                                            <th>Meeting Time</th>
                                                            <th>Meeting Place</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($sections as $section): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($section['position']) ?></td>
                                                            <td><?= htmlspecialchars($section['name']) ?></td>
                                                            <td><?= htmlspecialchars($section['meeting_time'] ?? '') ?></td>
                                                            <td><?= htmlspecialchars($section['meeting_place'] ?? '') ?></td>
                                                            <td>
                                                                <a href="/admin/sections/<?= $section['id'] ?>/edit" 
                                                                   class="btn btn-sm btn-primary">Edit</a>
                                                                <a href="/admin/sections/<?= $section['id'] ?>/delete" 
                                                                   class="btn btn-sm btn-danger"
                                                                   onclick="return confirm('Are you sure you want to delete this section?')">Delete</a>
                                                                <a href="/admin/sections/<?= $section['id'] ?>/notes/create" 
                                                                   class="btn btn-sm btn-success">New Daily Note</a>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/app/Views/partials/footer.php'; ?> 