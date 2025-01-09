<form method="POST">
    <div class="mb-3">
        <label>Section Name</label>
        <input type="text" name="name" class="form-control" required 
               value="<?= isset($section) ? htmlspecialchars($section['name']) : '' ?>">
    </div>

    <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control" rows="3"><?= isset($section) ? htmlspecialchars($section['description'] ?? '') : '' ?></textarea>
    </div>

    <div class="mb-3">
        <label>Position</label>
        <input type="number" name="position" class="form-control" required 
               value="<?= isset($section) ? htmlspecialchars($section['position']) : (isset($sections) ? count($sections) + 1 : 1) ?>">
        <small class="text-muted">The order in which this section appears in the course</small>
    </div>

    <div class="mb-3">
        <label>Meeting Time</label>
        <input type="text" name="meeting_time" class="form-control"
               value="<?= isset($section) ? htmlspecialchars($section['meeting_time'] ?? '') : '' ?>">
    </div>

    <div class="mb-3">
        <label>Meeting Place</label>
        <input type="text" name="meeting_place" class="form-control"
               value="<?= isset($section) ? htmlspecialchars($section['meeting_place'] ?? '') : '' ?>">
    </div>

    <button type="submit" class="btn btn-primary">Save Section</button>
    <a href="/admin/courses/<?= $course['id'] ?>/sections" class="btn btn-secondary">Cancel</a>
</form> 