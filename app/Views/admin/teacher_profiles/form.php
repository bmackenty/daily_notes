<form method="POST" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label>Full Name</label>
                <input type="text" name="full_name" class="form-control" required 
                       value="<?= isset($profile) ? htmlspecialchars($profile['full_name']) : '' ?>">
            </div>

            <div class="mb-3">
                <label>Title/Position</label>
                <input type="text" name="title" class="form-control" required
                       value="<?= isset($profile) ? htmlspecialchars($profile['title']) : '' ?>">
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required
                       value="<?= isset($profile) ? htmlspecialchars($profile['email']) : '' ?>">
            </div>

            <div class="mb-3">
                <label>Profile Picture</label>
                <?php if (isset($profile) && $profile['profile_picture']): ?>
                    <div class="mb-2">
                        <img src="<?= htmlspecialchars($profile['profile_picture']) ?>" 
                             alt="Current profile picture" class="img-thumbnail" style="max-width: 150px">
                    </div>
                <?php endif; ?>
                <input type="file" name="profile_picture" class="form-control" accept="image/*">
            </div>

            <div class="mb-3">
                <label>Office Hours/Availability</label>
                <textarea name="office_hours" class="form-control" rows="3"><?= isset($profile) ? htmlspecialchars($profile['office_hours']) : '' ?></textarea>
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label>Biography</label>
                <textarea name="biography" class="form-control" rows="4"><?= isset($profile) ? htmlspecialchars($profile['biography']) : '' ?></textarea>
            </div>

            <div class="mb-3">
                <label>Education/Credentials</label>
                <textarea name="education" class="form-control" rows="3"><?= isset($profile) ? htmlspecialchars($profile['education']) : '' ?></textarea>
            </div>

            <div class="mb-3">
                <label>Areas of Expertise</label>
                <textarea name="expertise" class="form-control" rows="3"><?= isset($profile) ? htmlspecialchars($profile['expertise']) : '' ?></textarea>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="mb-3">
                <label>Teaching Philosophy</label>
                <textarea name="teaching_philosophy" class="form-control rich-editor" rows="4"><?= isset($profile) ? htmlspecialchars($profile['teaching_philosophy']) : '' ?></textarea>
            </div>

            <div class="mb-3">
                <label>Vision for Students</label>
                <textarea name="vision_for_students" class="form-control rich-editor" rows="4"><?= isset($profile) ? htmlspecialchars($profile['vision_for_students']) : '' ?></textarea>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label>Personal Interests</label>
                <textarea name="personal_interests" class="form-control" rows="3"><?= isset($profile) ? htmlspecialchars($profile['personal_interests']) : '' ?></textarea>
            </div>

            <div class="mb-3">
                <label>Fun Facts</label>
                <textarea name="fun_facts" class="form-control" rows="3"><?= isset($profile) ? htmlspecialchars($profile['fun_facts']) : '' ?></textarea>
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label>Contact Preferences</label>
                <textarea name="contact_preferences" class="form-control" rows="3"><?= isset($profile) ? htmlspecialchars($profile['contact_preferences']) : '' ?></textarea>
            </div>

            <div class="mb-3">
                <label>Social Media Links</label>
                <textarea name="social_media_links" class="form-control" rows="3"><?= isset($profile) ? htmlspecialchars($profile['social_media_links']) : '' ?></textarea>
                <small class="text-muted">Enter one link per line</small>
            </div>

            <div class="mb-3">
                <label>GitHub Link</label>
                <input type="url" name="github_link" class="form-control"
                       value="<?= isset($profile) ? htmlspecialchars($profile['github_link']) : '' ?>">
            </div>

            <div class="mb-3">
                <label>Personal Webpage</label>
                <input type="url" name="personal_webpage" class="form-control"
                       value="<?= isset($profile) ? htmlspecialchars($profile['personal_webpage']) : '' ?>">
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">Save Profile</button>
        <a href="/admin/dashboard#teacher-profiles" class="btn btn-secondary">Cancel</a>
    </div>
</form>
