<?php require ROOT_PATH . '/app/Views/partials/header.php'; ?>

<!-- Add TinyMCE -->
<script src="https://cdn.tiny.cloud/1/0ej5pnow0o4gxdyaqdyz2zgdu0f4nulp55y17gr52byvbd35/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '.rich-editor',
        height: 300,
        plugins: 'lists link table code help wordcount',
        toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | link | code',
        menubar: false,
        setup: function(editor) {
            editor.on('change', function() {
                editor.save();
            });
        }
    });
</script>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/courses">Courses</a></li>
                    <li class="breadcrumb-item"><a href="/admin/courses/<?= $course['id'] ?>/weekly-plans"><?= htmlspecialchars($course['name']) ?> Weekly Plans</a></li>
                    <li class="breadcrumb-item active">Create Weekly Plan</li>
                </ol>
            </nav>

            <div class="card">
                <div class="card-header">
                    <h2 class="h4 mb-0">
                        Create Weekly Plan
                        <small class="text-muted">
                            Week <?= $week['week_number'] ?>
                            (<?= date('M d', strtotime($week['start_date'])) ?> - 
                            <?= date('M d, Y', strtotime($week['end_date'])) ?>)
                        </small>
                    </h2>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="/admin/courses/<?= $course['id'] ?>/weekly-plans/<?= $week['id'] ?>/create" onsubmit="return validateForm()">
                        <div class="mb-4">
                            <label class="form-label">Topic</label>
                            <input type="text" name="topic" class="form-control" required>
                            <small class="text-muted">The main topic or theme for this week</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Learning Objectives</label>
                            <textarea name="objectives" id="objectives" class="form-control rich-editor"></textarea>
                            <small class="text-muted">What students should be able to do by the end of the week</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Resources</label>
                            <textarea name="resources" id="resources" class="form-control rich-editor"></textarea>
                            <small class="text-muted">Reading materials, links, and other resources for the week</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Additional Notes</label>
                            <textarea name="notes" id="notes" class="form-control rich-editor"></textarea>
                            <small class="text-muted">Any additional information or instructions for the week</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="/admin/courses/<?= $course['id'] ?>/weekly-plans" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Weekly Plan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function validateForm() {
    var editors = ['objectives', 'resources', 'notes'];
    for (var i = 0; i < editors.length; i++) {
        var content = tinymce.get(editors[i]).getContent();
        document.getElementById(editors[i]).value = content;
    }
    return true;
}
</script>

<?php require ROOT_PATH . '/app/Views/partials/footer.php'; ?> 