<?php require ROOT_PATH . '/app/Views/partials/header.php'; ?>

<!-- Add TinyMCE -->
<script src="https://cdn.tiny.cloud/1/0ej5pnow0o4gxdyaqdyz2zgdu0f4nulp55y17gr52byvbd35/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Initializing TinyMCE...');
        tinymce.init({
            selector: '#content',
            height: 500,
            plugins: 'lists link image table code help wordcount',
            toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | link image | code',
            menubar: 'file edit view insert format tools table help',
            setup: function(editor) {
                console.log('TinyMCE editor setup complete');
                editor.on('change', function() {
                    editor.save(); // This saves content back to textarea
                });
            }
        }).then(function(editors) {
            console.log('TinyMCE editors initialized:', editors);
        }).catch(function(error) {
            console.error('TinyMCE initialization error:', error);
        });
    });
</script>

<style>
    .tox-tinymce {
        min-height: 500px !important;
        border: 1px solid #ced4da !important;
    }
    #content {
        min-height: 500px;
    }
</style>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/courses">Courses</a></li>
                    <li class="breadcrumb-item"><a href="/admin/courses/<?= $course['id'] ?>/sections"><?= htmlspecialchars($course['name']) ?></a></li>
                    <li class="breadcrumb-item"><?= htmlspecialchars($section['name']) ?></li>
                    <li class="breadcrumb-item active">New Daily Note</li>
                </ol>
            </nav>

            <div class="card">
                <div class="card-header">
                    <h2>New Daily Note for <?= htmlspecialchars($section['name']) ?></h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="/admin/sections/<?= $section['id'] ?>/notes/create" onsubmit="return validateForm()">
                        <div class="mb-3">
                            <label>Date</label>
                            <input type="date" name="date" class="form-control" required 
                                   value="<?= date('Y-m-d') ?>">
                        </div>

                        <div class="mb-3">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label>Content</label>
                            <textarea id="content" name="content" class="form-control" required><?= (isset($lastNote) && is_array($lastNote)) ? $lastNote['content'] : '' ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Note</button>
                        <a href="/admin/courses/<?= $course['id'] ?>/sections" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function validateForm() {
    // Get TinyMCE content
    var content = tinymce.get('content').getContent();
    if (!content) {
        alert('Please enter some content');
        return false;
    }
    // Update textarea with TinyMCE content
    document.getElementById('content').value = content;
    return true;
}
</script>


<?php require ROOT_PATH . '/app/Views/partials/footer.php'; ?> 