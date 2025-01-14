<?php
namespace App\Models;

class Course {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM courses ORDER BY name");
        return $stmt->fetchAll();
    }
    
    public function get($id) {
        $stmt = $this->db->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $sql = "INSERT INTO courses (name, short_name, description, policies, rules, 
                academic_integrity, prerequisites, teacher, teacher_profile_id, google_classroom_link, 
                meeting_notes, default_tags) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        return $this->db->prepare($sql)->execute([
            $data['name'],
            $data['short_name'],
            $data['description'],
            $data['policies'],
            $data['rules'],
            $data['academic_integrity'],
            $data['prerequisites'],
            $data['teacher'],
            $data['teacher_profile_id'],
            $data['google_classroom_link'],
            $data['meeting_notes'],
            $data['default_tags']
        ]);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE courses SET 
                name = :name,
                description = :description,
                github_link = :github_link,
                lms_link = :lms_link,
                help_link = :help_link,
                library_link = :library_link
                WHERE id = :id";
                
        return $this->db->prepare($sql)->execute([
            'id' => $id,
            'name' => $data['name'],
            'description' => $data['description'],
            'github_link' => $data['github_link'] ?? null,
            'lms_link' => $data['lms_link'] ?? null,
            'help_link' => $data['help_link'] ?? null,
            'library_link' => $data['library_link'] ?? null
        ]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM courses WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM courses WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getCoursesByTeacherProfileId($profileId) {
        $sql = "SELECT * FROM courses WHERE teacher_profile_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$profileId]);
        return $stmt->fetchAll();
    }
} 