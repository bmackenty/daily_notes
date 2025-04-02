<?php
namespace App\Models;

class Note {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function create($data) {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("
                INSERT INTO notes (user_id, section_id, title, content, date)
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['user_id'],
                $data['section_id'],
                $data['title'],
                $data['content'],
                $data['date']
            ]);
            
            $noteId = $this->db->lastInsertId();
            
            // Handle tag
            if (!empty($data['title'])) {
                $tagModel = new Tag($this->db);
                $tagId = $tagModel->findOrCreate($data['title']);
                
                $stmt = $this->db->prepare("
                    INSERT INTO note_tags (note_id, tag_id)
                    VALUES (?, ?)
                ");
                $stmt->execute([$noteId, $tagId]);
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }
    
    public function getAllBySection($sectionId) {
        $sql = "SELECT * FROM notes WHERE section_id = ? ORDER BY date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sectionId]);
        return $stmt->fetchAll();
    }
    
    public function update($id, $data) {
        $this->db->beginTransaction();
        try {
            $sql = "UPDATE notes 
                    SET title = ?, content = ?, date = ?, 
                        updated_at = CURRENT_TIMESTAMP 
                    WHERE id = ?";
                    
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['title'],
                $data['content'],
                $data['date'],
                $id
            ]);

            // Update tag
            if (!empty($data['title'])) {
                // Remove old tags
                $stmt = $this->db->prepare("DELETE FROM note_tags WHERE note_id = ?");
                $stmt->execute([$id]);
                
                // Add new tag
                $tagModel = new Tag($this->db);
                $tagId = $tagModel->findOrCreate($data['title']);
                
                $stmt = $this->db->prepare("
                    INSERT INTO note_tags (note_id, tag_id)
                    VALUES (?, ?)
                ");
                $stmt->execute([$id, $tagId]);
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }
    
    public function get($id) {
        $stmt = $this->db->prepare("SELECT * FROM notes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getLastBySection($sectionId) {
        $sql = "SELECT * FROM notes WHERE section_id = :section_id ORDER BY date DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['section_id' => $sectionId]);
        return $stmt->fetch();
    }

    public function search($query, $sectionId = null) {
        $sql = "SELECT n.*, s.name as section_name, c.name as course_name 
                FROM notes n 
                JOIN sections s ON n.section_id = s.id 
                JOIN courses c ON s.course_id = c.id 
                WHERE (n.title LIKE ? OR n.content LIKE ?)";
        
        $params = ["%$query%", "%$query%"];
        
        if ($sectionId) {
            $sql .= " AND n.section_id = ?";
            $params[] = $sectionId;
        }
        
        $sql .= " ORDER BY n.date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
} 