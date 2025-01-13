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
                short_name = :short_name,
                description = :description,
                aims = :aims,
                assessment = :assessment,
                required = :required,
                communication = :communication,
                policies = :policies,
                rules = :rules,
                updated_at = NOW()
                WHERE id = :id";
                
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'short_name' => $data['short_name'],
            'description' => $data['description'] ?? '',
            'aims' => $data['aims'] ?? '',
            'assessment' => $data['assessment'] ?? '',
            'required' => $data['required'] ?? '',
            'communication' => $data['communication'] ?? '',
            'policies' => $data['policies'] ?? '',
            'rules' => $data['rules'] ?? ''
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