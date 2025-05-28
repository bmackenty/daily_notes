<?php require ROOT_PATH . '/app/Views/partials/header.php'; ?>

<!-- Add TinyMCE -->
<script src="https://cdn.tiny.cloud/1/0ej5pnow0o4gxdyaqdyz2zgdu0f4nulp55y17gr52byvbd35/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        tinymce.init({
            selector: '#content',
            height: 600,
            plugins: 'lists link image table code help wordcount',
            toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | link image | code',
            menubar: 'file edit view insert format tools table help',
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            }
        });
    });
</script>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/courses">Courses</a></li>
                    <li class="breadcrumb-item"><a href="/admin/courses/<?= $course['id'] ?>/sections"><?= htmlspecialchars($course['name']) ?></a></li>
                    <li class="breadcrumb-item"><?= htmlspecialchars($section['name']) ?></li>
                    <li class="breadcrumb-item active">Edit Daily Note</li>
                </ol>
            </nav>

            <div class="card">
                <div class="card-header">
                    <h2>Edit Daily Notee</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="/admin/notes/<?= $note['id'] ?>/edit" onsubmit="return validateForm()">
                        <div class="mb-3">
                            <label>Date</label>
                            <input type="date" name="date" class="form-control" required 
                                   value="<?= htmlspecialchars($note['date']) ?>">
                        </div>

                        <div class="mb-3">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control" required
                                   value="<?= htmlspecialchars($note['title']) ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label>Content</label>
                            <textarea id="content" name="content" class="form-control" required><?= $note['content'] ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Note</button>
                        <a href="/courses/<?= $course['id'] ?>/sections/<?= $section['id'] ?>/notes" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function validateForm() {
    var content = tinymce.get('content').getContent();
    if (!content) {
        alert('Please enter some content');
        return false;
    }
    document.getElementById('content').value = content;
    return true;
}
</script>

<?php require ROOT_PATH . '/app/Views/partials/footer.php'; ?>