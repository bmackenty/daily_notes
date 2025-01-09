<form method="POST" action="<?= isset($course) ? "/admin/courses/edit/{$course['id']}" : "/admin/courses/create" ?>">
    <div class="mb-3">
        <label>Course Name</label>
        <input type="text" name="name" class="form-control" required 
               value="<?= isset($course) ? htmlspecialchars($course['name']) : '' ?>">
    </div>

    <div class="mb-3">
        <label>Short Name</label>
        <input type="text" name="short_name" class="form-control" required 
               value="<?= isset($course) ? htmlspecialchars($course['short_name']) : '' ?>">
    </div>

    <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control" rows="3"><?= isset($course) ? htmlspecialchars($course['description']) : '' ?></textarea>
    </div>

    <div class="mb-3">
        <label>Aims</label>
        <textarea name="aims" class="form-control" rows="3"><?= isset($course) ? htmlspecialchars($course['aims']) : '' ?></textarea>
    </div>

    <div class="mb-3">
        <label>Assessment</label>
        <textarea name="assessment" class="form-control" rows="3"><?= isset($course) ? htmlspecialchars($course['assessment']) : '' ?></textarea>
    </div>

    <div class="mb-3">
        <label>Required Materials</label>
        <textarea name="required" class="form-control" rows="3"><?= isset($course) ? htmlspecialchars($course['required']) : '' ?></textarea>
    </div>

    <div class="mb-3">
        <label>Communication</label>
        <textarea name="communication" class="form-control" rows="3"><?= isset($course) ? htmlspecialchars($course['communication']) : '' ?></textarea>
    </div>

    <div class="mb-3">
        <label>Policies</label>
        <textarea name="policies" class="form-control" rows="3"><?= isset($course) ? htmlspecialchars($course['policies']) : '' ?></textarea>
    </div>

    <div class="mb-3">
        <label>Rules</label>
        <textarea name="rules" class="form-control" rows="3"><?= isset($course) ? htmlspecialchars($course['rules']) : '' ?></textarea>
    </div>

    <div class="mb-3">
        <label>Academic Integrity</label>
        <textarea name="academic_integrity" class="form-control" rows="3"><?= isset($course) ? htmlspecialchars($course['academic_integrity']) : '' ?></textarea>
    </div>

    <div class="mb-3">
        <label>Prerequisites</label>
        <textarea name="prerequisites" class="form-control" rows="3"><?= isset($course) ? htmlspecialchars($course['prerequisites']) : '' ?></textarea>
    </div>

    <div class="mb-3">
        <label>Teacher</label>
        <input type="text" name="teacher" class="form-control" required 
               value="<?= isset($course) ? htmlspecialchars($course['teacher']) : '' ?>">
    </div>

    <div class="mb-3">
        <label>Google Classroom Link</label>
        <input type="url" name="google_classroom_link" class="form-control" 
               value="<?= isset($course) ? htmlspecialchars($course['google_classroom_link']) : '' ?>">
    </div>

    <div class="mb-3">
        <label>Default Tags (comma-separated)</label>
        <input type="text" name="default_tags" class="form-control" 
               value="<?= isset($course) ? htmlspecialchars($course['default_tags']) : '' ?>">
    </div>

    <button type="submit" class="btn btn-primary">Save Course</button>
    <a href="/admin/courses" class="btn btn-secondary">Cancel</a>
</form>
