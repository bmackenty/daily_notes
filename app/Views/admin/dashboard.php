<?php require ROOT_PATH . '/app/Views/partials/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">Dashboard</h2>

            <ul class="nav nav-tabs mb-4" id="dashboardTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="overview-tab" data-bs-toggle="tab" href="#overview">Overview</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="courses-tab" data-bs-toggle="tab" href="#courses">Courses</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="academic-tab" data-bs-toggle="tab" href="#academic">Academic Years</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="settings-tab" data-bs-toggle="tab" href="#settings">Settings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="sections-tab" data-bs-toggle="tab" href="#sections">Course Sections</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="teacher-profiles-tab" data-bs-toggle="tab" href="#teacher-profiles">Teacher Profiles</a>
                </li>
            </ul>

            <div class="tab-content" id="dashboardContent">
                <!-- Overview Tab -->
                <div class="tab-pane fade show active" id="overview">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Active Academic Year</h5>
                                    <?php if ($activeYear): ?>
                                        <p class="card-text"><?= htmlspecialchars($activeYear['name']) ?></p>
                                    <?php else: ?>
                                        <p class="text-warning">No active academic year set</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Total Courses</h5>
                                    <p class="card-text"><?= count($courses) ?> courses</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Weekly Plans Count -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Weekly Topics per Course</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Course</th>
                                                    <th>Weekly Topics</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($courses as $course): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($course['name']) ?></td>
                                                        <td>
                                                            <span class="badge bg-primary">
                                                                <?= $weeklyPlanCounts[$course['id']] ?? 0 ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Daily Notes Count -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Daily Notes per Section</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Course</th>
                                                    <th>Section</th>
                                                    <th>Notes</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($courses as $course): 
                                                    $sections = $sectionModel->getAllByCourse($course['id']);
                                                    foreach ($sections as $section):
                                                ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($course['name']) ?></td>
                                                        <td><?= htmlspecialchars($section['name']) ?></td>
                                                        <td>
                                                            <span class="badge bg-info">
                                                                <?= $noteCounts[$section['id']] ?? 0 ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php 
                                                    endforeach;
                                                endforeach; 
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

               <!-- Courses Tab -->
<div class="tab-pane fade" id="courses">
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
                                        <a href="/admin/courses/edit/<?= $course['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                        <a href="/admin/courses/<?= $course['id'] ?>/sections" class="btn btn-sm btn-info">Sections</a>
                                        <a href="/admin/courses/<?= $course['id'] ?>/weekly-plans" class="btn btn-sm btn-secondary">
                                            <i class="bi bi-calendar-week"></i> Weekly Plans
                                        </a>
                                        <?php if ($settings['show_delete_buttons'] === 'true'): ?>
                                            <a href="/admin/courses/<?= $course['id'] ?>/delete" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Are you sure you want to delete this course?')">Delete</a>
                                        <?php endif; ?>
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
                                                    <td>
                                                        <a href="/courses/<?= $course['id'] ?>/sections/<?= $section['id'] ?>/notes">
                                                            <?= htmlspecialchars($section['name']) ?>
                                                        </a>
                                                    </td>
                                                    <td><?= htmlspecialchars($section['meeting_time'] ?? '') ?></td>
                                                    <td><?= htmlspecialchars($section['meeting_place'] ?? '') ?></td>
                                                    <td>
                                                        <a href="/admin/sections/<?= $section['id'] ?>/notes/create" 
                                                            class="btn btn-sm btn-success">New Daily Note</a>
                                                        <a href="/admin/sections/<?= $section['id'] ?>/edit" 
                                                           class="btn btn-sm btn-primary">Edit</a>
                                                        <?php if ($settings['show_delete_buttons'] === 'true'): ?>
                                                            <a href="/admin/sections/<?= $section['id'] ?>/delete" 
                                                               class="btn btn-sm btn-danger"
                                                               onclick="return confirm('Are you sure you want to delete this section?')">Delete</a>
                                                        <?php endif; ?>
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

                <!-- Academic Years Tab -->
                <div class="tab-pane fade" id="academic">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Academic Years</h2>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAcademicYearModal">
                            Add New Academic Year
                        </button>
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Year</th>
                                            <th>Status</th>
                                            <th>Period</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($academicYears as $year): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($year['name']) ?></td>
                                                <td>
                                                    <?php if ($year['is_active']): ?>
                                                        <span class="badge bg-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?= date('M d, Y', strtotime($year['start_date'])) ?> - 
                                                    <?= date('M d, Y', strtotime($year['end_date'])) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Academic Year Modal -->
                <div class="modal fade" id="addAcademicYearModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add New Academic Year</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="/admin/settings/academic-years/create">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label>Academic Year Name</label>
                                        <input type="text" name="name" class="form-control" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label>Start Date</label>
                                                <input type="date" name="start_date" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label>End Date</label>
                                                <input type="date" name="end_date" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Create Academic Year</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Settings Tab -->
                <div class="tab-pane fade" id="settings">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="/admin/settings">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="registration" 
                                               name="registration_enabled" <?= $settings['registration_enabled'] === 'true' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="registration">Enable Registration</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label>Max Notes Per User</label>
                                    <input type="number" class="form-control" name="max_notes_per_user" 
                                           value="<?= htmlspecialchars($settings['max_notes_per_user']) ?>">
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="deleteButtons" 
                                               name="show_delete_buttons" <?= $settings['show_delete_buttons'] === 'true' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="deleteButtons">Show Delete Buttons</label>
                                        <small class="form-text text-muted d-block">
                                            Enable/disable delete buttons for courses and sections
                                        </small>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Save Settings</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Course Sections Tab -->
                <div class="tab-pane fade" id="sections">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Course Name</th>
                                            <th>Sections</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($courses as $course): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($course['name']) ?></td>
                                                <td>
                                                    <?php 
                                                    $sections = $sectionModel->getAllByCourse($course['id']);
                                                    if (!empty($sections)): 
                                                    ?>
                                                    <div class="mb-2">
                                                        <table class="table table-sm table-bordered mb-0">
                                                            <thead>
                                                                <tr>
                                                                  
                                                                    <th>Name</th>
                                                                    <th>Meeting Time</th>
                                                                    <th>Meeting Place</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($sections as $section): ?>
                                                                <tr>
                                                                    <td>
                                                                        <a href="/courses/<?= $course['id'] ?>/sections/<?= $section['id'] ?>/notes">
                                                                            <?= htmlspecialchars($section['name']) ?>
                                                                        </a>
                                                                    </td>
                                                                    <td><?= htmlspecialchars($section['meeting_time'] ?? '') ?></td>
                                                                    <td><?= htmlspecialchars($section['meeting_place'] ?? '') ?></td>
                                                                </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <?php else: ?>
                                                        <em class="text-muted">No sections</em>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Teacher Profiles Tab -->
                <div class="tab-pane fade" id="teacher-profiles">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Teacher Profiles</h2>
                        <a href="/admin/teacher-profiles/create" class="btn btn-primary">Add New Profile</a>
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
                                            <th>Title</th>
                                            <th>Email</th>
                                            <th>Linked Courses</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($teacherProfiles as $profile): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($profile['full_name']) ?></td>
                                                <td><?= htmlspecialchars($profile['title']) ?></td>
                                                <td><?= htmlspecialchars($profile['email']) ?></td>
                                                <td><?= count($profileCourses[$profile['id']] ?? []) ?> courses</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="/admin/teacher-profiles/<?= $profile['id'] ?>/edit" 
                                                           class="btn btn-sm btn-primary">Edit</a>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-info"
                                                                onclick="viewTeacherProfile(<?= $profile['id'] ?>)">View</button>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-danger"
                                                                onclick="deleteTeacherProfile(<?= $profile['id'] ?>)">Delete</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require ROOT_PATH . '/app/Views/partials/footer.php'; ?>
