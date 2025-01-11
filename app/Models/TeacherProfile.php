<?php
namespace App\Models;

class TeacherProfile {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($data) {
        $sql = "INSERT INTO teacher_profiles (user_id, full_name, title, email, office_hours, 
                biography, education, profile_picture, contact_preferences, expertise,
                teaching_philosophy, personal_interests, achievements, vision_for_students,
                fun_facts, social_media_links, github_link, personal_webpage) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['user_id'], $data['full_name'], $data['title'], $data['email'],
            $data['office_hours'], $data['biography'], $data['education'],
            $data['profile_picture'], $data['contact_preferences'], $data['expertise'],
            $data['teaching_philosophy'], $data['personal_interests'], $data['achievements'],
            $data['vision_for_students'], $data['fun_facts'], $data['social_media_links'],
            $data['github_link'], $data['personal_webpage']
        ]);
    }

    public function update($id, $data) {
        $sql = "UPDATE teacher_profiles SET 
                full_name = ?, title = ?, email = ?, office_hours = ?, biography = ?,
                education = ?, profile_picture = ?, contact_preferences = ?, expertise = ?,
                teaching_philosophy = ?, personal_interests = ?, achievements = ?,
                vision_for_students = ?, fun_facts = ?, social_media_links = ?,
                github_link = ?, personal_webpage = ? WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['full_name'], $data['title'], $data['email'], $data['office_hours'],
            $data['biography'], $data['education'], $data['profile_picture'],
            $data['contact_preferences'], $data['expertise'], $data['teaching_philosophy'],
            $data['personal_interests'], $data['achievements'], $data['vision_for_students'],
            $data['fun_facts'], $data['social_media_links'], $data['github_link'],
            $data['personal_webpage'], $id
        ]);
    }

    public function get($id) {
        $stmt = $this->db->prepare("SELECT * FROM teacher_profiles WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM teacher_profiles ORDER BY full_name");
        return $stmt->fetchAll();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM teacher_profiles WHERE id = ?");
        return $stmt->execute([$id]);
    }
}