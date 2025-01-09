<?php
namespace App\Models;

class Note {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO notes (user_id, section_id, title, content, date)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['user_id'],
            $data['section_id'],
            $data['title'],
            $data['content'],
            $data['date']
        ]);
    }
    
    public function getAllBySection($sectionId) {
        $sql = "SELECT * FROM notes WHERE section_id = ? ORDER BY date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sectionId]);
        return $stmt->fetchAll();
    }
    
    public function update($id, $data) {
        $sql = "UPDATE notes 
                SET title = ?, content = ?, date = ?, 
                    updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?";
                
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['title'],
            $data['content'],
            $data['date'],
            $id
        ]);
    }
    
    public function get($id) {
        $stmt = $this->db->prepare("SELECT * FROM notes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
} 