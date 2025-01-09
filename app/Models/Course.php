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
        $stmt = $this->db->prepare("
            INSERT INTO courses (
                name, short_name, description, aims, assessment,
                required, communication, policies, rules,
                academic_integrity, prerequisites, teacher,
                google_classroom_link, meeting_notes, default_tags
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['name'],
            $data['short_name'],
            $data['description'],
            $data['aims'],
            $data['assessment'],
            $data['required'],
            $data['communication'],
            $data['policies'],
            $data['rules'],
            $data['academic_integrity'],
            $data['prerequisites'],
            $data['teacher'],
            $data['google_classroom_link'],
            $data['meeting_notes'],
            $data['default_tags']
        ]);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE courses SET 
                name = ?, short_name = ?, description = ?,
                aims = ?, assessment = ?, required = ?,
                communication = ?, policies = ?, rules = ?,
                academic_integrity = ?, prerequisites = ?,
                teacher = ?, google_classroom_link = ?,
                meeting_notes = ?, default_tags = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['name'],
            $data['short_name'],
            $data['description'],
            $data['aims'],
            $data['assessment'],
            $data['required'],
            $data['communication'],
            $data['policies'],
            $data['rules'],
            $data['academic_integrity'],
            $data['prerequisites'],
            $data['teacher'],
            $data['google_classroom_link'],
            $data['meeting_notes'],
            $data['default_tags'],
            $id
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
} 