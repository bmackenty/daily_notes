<?php
namespace App\Models;

class Section {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getAllByCourse($courseId) {
        $stmt = $this->db->prepare("SELECT * FROM sections WHERE course_id = ? ORDER BY position");
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }
    
    public function get($id) {
        $stmt = $this->db->prepare("SELECT * FROM sections WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO sections (
                course_id, name, description, position, meeting_time, meeting_place
            ) VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['course_id'],
            $data['name'],
            $data['description'],
            $data['position'],
            $data['meeting_time'],
            $data['meeting_place']
        ]);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE sections 
            SET name = ?, description = ?, position = ?, meeting_time = ?, meeting_place = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['name'],
            $data['description'],
            $data['position'],
            $data['meeting_time'],
            $data['meeting_place'],
            $id
        ]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM sections WHERE id = ?");
        return $stmt->execute([$id]);
    }
} 