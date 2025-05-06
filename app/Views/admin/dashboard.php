<?php require ROOT_PATH . '/app/Views/partials/header.php'; ?>

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

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">Dashboard</h2>

            <ul class="nav nav-tabs mb-4" id="dashboardTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link" id="overview-tab" data-bs-toggle="tab" href="#overview">Overview</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" id="courses-tab" data-bs-toggle="tab" href="#courses">Courses</a>
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
                <li class="nav-item">
                    <a class="nav-link" id="learning-statements-tab" data-bs-toggle="tab" href="#learning-statements">Learning Statements</a>
                </li>
            </ul>

            <div class="tab-content mb-5" id="dashboardContent">
                <!-- Overview Tab -->
                <div class="tab-pane fade" id="overview">
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
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Total Sections</h5>
                                    <p class="card-text">
                                        <?php
                                        $totalSections = 0;
                                        foreach ($courses as $course) {
                                            $sections = $sectionModel->getAllByCourse($course['id']);
                                            $totalSections += count($sections);
                                        }
                                        echo $totalSections . ' sections';
                                        ?>
                                    </p>
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
                                                        <td>
                                                            <a href="/admin/sections/<?= $section['id'] ?>/edit" class="text-decoration-none">
                                                                <?= htmlspecialchars($section['name']) ?>
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <?php 
                                                            if (isset($notes[$section['id']]) && !empty($notes[$section['id']])) {
                                                                $latestNote = reset($notes[$section['id']]);
                                                                if (isset($latestNote['date']) && $latestNote['date']) {
                                                                    $noteUrl = '/courses/' . $course['id'] . '/sections/' . $section['id'] . '/notes';
                                                                    echo '<span class="badge bg-light text-dark border">';
                                                                    echo '<i class="bi bi-journal-text text-success me-1"></i>';
                                                                    echo '<a href="' . $noteUrl . '" class="text-dark text-decoration-none" data-bs-toggle="tooltip" title="View all notes for this section">';
                                                                    echo htmlspecialchars(date('M j, Y', strtotime($latestNote['date'])));
                                                                    echo '</a>';
                                                                    echo '</span>';
                                                                } else {
                                                                    echo '<span class="badge bg-light text-muted border"><i class="bi bi-journal-x me-1"></i>No notes yet</span>';
                                                                }
                                                            } else {
                                                                echo '<span class="badge bg-light text-muted border"><i class="bi bi-journal-x me-1"></i>No notes yet</span>';
                                                            }
                                                            ?>
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
                <div class="tab-pane fade show active" id="courses">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h2 class="h4 mb-0">Sections</h2>
                        <a href="/admin/courses/create" class="btn btn-sm btn-primary">Add New Course</a>
                    </div>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success py-1 px-2 mb-2"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-body p-2">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Course</th>
                                            <th>Section</th>
                                            <th>Last Note</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($courses as $course): 
                                            $sections = $sectionModel->getAllByCourse($course['id']);
                                            foreach ($sections as $section): ?>
                                            <tr>
                                                <td>
                                                    <a href="/admin/courses/edit/<?= $course['id'] ?>" class="text-decoration-none">
                                                        <?= htmlspecialchars($course['name']) ?>
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="/admin/sections/<?= $section['id'] ?>/edit" class="text-decoration-none">
                                                        <?= htmlspecialchars($section['name']) ?>
                                                    </a>
                                                </td>
                                                <td>
                                                    <?php if (isset($notes[$section['id']]) && !empty($notes[$section['id']])): 
                                                        $latestNote = reset($notes[$section['id']]);
                                                        if (isset($latestNote['date']) && $latestNote['date']): ?>
                                                            <a href="/courses/<?= $course['id'] ?>/sections/<?= $section['id'] ?>/notes" class="text-decoration-none d-inline-block">
                                                                <span class="badge bg-light text-dark border">
                                                                    <?= date('M j', strtotime($latestNote['date'])) ?>
                                                                    <small class="text-muted">(<?= human_timing(strtotime($latestNote['date'])) ?>)</small>
                                                                </span>
                                                            </a>
                                                        <?php else: ?>
                                                            <span class="badge bg-light text-muted border">No notes</span>
                                                        <?php endif;
                                                    else: ?>
                                                        <span class="badge bg-light text-muted border">No notes</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-end">
                                                    <a href="/admin/sections/<?= $section['id'] ?>/notes/create" class="btn btn-sm btn-outline-success">New Note</a>
                                                    <a href="/admin/sections/<?= $section['id'] ?>/edit" class="btn btn-sm btn-outline-primary">Edit</a>
                                                    <?php if ($settings['show_delete_buttons'] === 'true'): ?>
                                                        <a href="/admin/sections/<?= $section['id'] ?>/delete" class="btn btn-sm btn-outline-danger"
                                                           onclick="return confirm('Delete this section?')">×</a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach;
                                        endforeach; ?>
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
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Year</th>
                                            <th>Status</th>
                                            <th>Period</th>
                                            <th>Actions</th>
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
                                                <td>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-primary" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editAcademicYearModal<?= $year['id'] ?>">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </button>
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

                <!-- Edit Academic Year Modals -->
                <?php foreach ($academicYears as $year): ?>
                <div class="modal fade" id="editAcademicYearModal<?= $year['id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Academic Year</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="/admin/settings/academic-years/edit/<?= $year['id'] ?>">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label>Academic Year Name</label>
                                        <input type="text" name="name" class="form-control" required 
                                               value="<?= htmlspecialchars($year['name']) ?>">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label>Start Date</label>
                                                <input type="date" name="start_date" class="form-control" required
                                                       value="<?= date('Y-m-d', strtotime($year['start_date'])) ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label>End Date</label>
                                                <input type="date" name="end_date" class="form-control" required
                                                       value="<?= date('Y-m-d', strtotime($year['end_date'])) ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Update Academic Year</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

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
                                        <small class="form-text text-muted d-block">
                                           This will enable the registration feature on the navigation bar. In general, this system is designed to be used by one or two teachers. 
                                        </small>
                                    </div>
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
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Course Name</th>
                                            <th>Sections</th>
                                            <th>Actions</th>
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
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="/admin/courses/edit/<?= $course['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                                        <a href="/admin/courses/<?= $course['id'] ?>/sections" class="btn btn-sm btn-info">Sections</a>
                                                        <a href="/admin/courses/<?= $course['id'] ?>/weekly-plans" class="btn btn-sm btn-secondary">
                                                            <i class="bi bi-calendar-week"></i> Weekly Plans
                                                        </a>
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
                                <table class="table table-sm">
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

                                                        <a class="btn btn-sm btn-info" href="/teacher-profile/<?= $course['teacher_profile_id'] ?>">View</a>                                                              



                                                        <?php if ($settings['show_delete_buttons'] === 'true'): ?>
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-danger"
                                                                    onclick="deleteTeacherProfile(<?= $profile['id'] ?>)">Delete</button>
                                                        <?php endif; ?>
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

                <!-- Learning Statements Tab -->
                <div class="tab-pane fade" id="learning-statements">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Learning Statements</h2>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLearningStatementModal">
                            Add New Statement
                        </button>
                    </div>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="learningStatementsTable" class="table table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th width="100">Identifier</th>
                                            <th>Learning Statement</th>
                                            <th width="100" class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($learningStatements as $statement): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($statement['identifier'] ?? '') ?></td>
                                            <td class="w-100"><?= htmlspecialchars($statement['learning_statement'] ?? '') ?></td>
                                            <td class="text-end">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-primary" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editLearningStatementModal<?= $statement['id'] ?>">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <?php if ($settings['show_delete_buttons'] === 'true'): ?>
                                                        <button onclick="deleteLearningStatement(<?= $statement['id'] ?>)" 
                                                                class="btn btn-sm btn-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
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

                <!-- Add Learning Statement Modal -->
                <div class="modal fade" id="addLearningStatementModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add New Learning Statement</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="/admin/learning-statements/create">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label>Identifier</label>
                                        <input type="text" name="identifier" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label>Learning Statement</label>
                                        <textarea name="learning_statement" class="form-control" rows="3" required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Create Statement</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Edit Learning Statement Modals -->
                <?php foreach ($learningStatements as $statement): ?>
                <div class="modal fade" id="editLearningStatementModal<?= $statement['id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Learning Statement</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="/admin/learning-statements/edit/<?= $statement['id'] ?>">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label>Identifier</label>
                                        <input type="text" name="identifier" class="form-control" 
                                               value="<?= htmlspecialchars($statement['identifier']) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label>Learning Statement</label>
                                        <textarea name="learning_statement" class="form-control" rows="3" required><?= htmlspecialchars($statement['learning_statement']) ?></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Update Statement</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
.handle {
    cursor: move;
}
.handle-header {
    cursor: default;
}
.table, .table th, .table td {
    padding: 0.25rem !important;
    font-size: 0.92rem;
}
.card-body, .card-header {
    padding: 0.5rem 0.75rem !important;
}
.btn-sm {
    padding: 0.15rem 0.5rem !important;
    font-size: 0.85rem !important;
}
.badge {
    display: inline-block;
    position: relative;
    z-index: 1;
}
.badge a {
    color: inherit;
    text-decoration: none;
    display: block;
    width: 100%;
    height: 100%;
}
</style>

<script>
$(document).ready(function() {
    let table = $('#learningStatementsTable').DataTable({
        dom: '<"d-flex justify-content-between align-items-center mb-3"lf>rtip',
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        order: [[0, 'asc']],
        columns: [
            { orderable: true, className: 'reorder' },  // Identifier
            { orderable: false },  // Learning Statement
            { orderable: false }  // Actions
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search statements...",
            lengthMenu: "_MENU_ statements per page"
        }
    });

    // Keep the original delete function
    window.deleteLearningStatement = function(id) {
        if (confirm('Are you sure you want to delete this learning statement?')) {
            fetch(`/admin/learning-statements/delete/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    table.row($(`button[onclick*="deleteLearningStatement(${id})"]`).closest('tr'))
                        .remove()
                        .draw();
                } else {
                    alert('Failed to delete learning statement');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the learning statement');
            });
        }
    };
});
</script>

<?php require ROOT_PATH . '/app/Views/partials/footer.php'; ?>
