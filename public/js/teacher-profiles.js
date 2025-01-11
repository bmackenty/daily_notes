function viewTeacherProfile(profileId) {
    fetch(`/api/teacher-profiles/${profileId}`)
        .then(response => response.json())
        .then(profile => {
            document.getElementById('teacherName').textContent = profile.full_name;
            document.getElementById('teacherTitle').textContent = profile.title;
            document.getElementById('teacherEmail').textContent = profile.email;
            document.getElementById('teacherOfficeHours').textContent = profile.office_hours;
            document.getElementById('teacherBio').textContent = profile.biography;
            document.getElementById('teacherEducation').textContent = profile.education;
            
            const profilePic = document.getElementById('profilePicture');
            if (profile.profile_picture) {
                profilePic.src = profile.profile_picture;
                profilePic.style.display = 'block';
            } else {
                profilePic.style.display = 'none';
            }

            const modal = new bootstrap.Modal(document.getElementById('teacherProfileModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error fetching teacher profile:', error);
            alert('Failed to load teacher profile');
        });
}

function deleteTeacherProfile(profileId) {
    if (confirm('Are you sure you want to delete this teacher profile? This action cannot be undone.')) {
        fetch(`/admin/teacher-profiles/${profileId}/delete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Failed to delete teacher profile');
            }
        })
        .catch(error => {
            console.error('Error deleting teacher profile:', error);
            alert('Failed to delete teacher profile');
        });
    }
} 