<?php
namespace App\Models;

class Tag {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function findOrCreate($name) {
        // First try to find the tag
        $stmt = $this->db->prepare("SELECT id FROM tags WHERE name = ?");
        $stmt->execute([$name]);
        $tag = $stmt->fetch();

        if ($tag) {
            return $tag['id'];
        }

        // If not found, create it
        $stmt = $this->db->prepare("INSERT INTO tags (name) VALUES (?)");
        $stmt->execute([$name]);
        return $this->db->lastInsertId();
    }

    public function getTagCloud($courseId, $academicYearId = null) {
        $sql = "SELECT t.name, COUNT(*) as count 
                FROM tags t 
                JOIN note_tags nt ON t.id = nt.tag_id 
                JOIN notes n ON nt.note_id = n.id 
                JOIN sections s ON n.section_id = s.id 
                WHERE s.course_id = ?";
        
        $params = [$courseId];
        
        if ($academicYearId !== null) {
            $sql .= " AND n.academic_year_id = ?";
            $params[] = $academicYearId;
        }
        
        $sql .= " GROUP BY t.id, t.name ORDER BY count DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getNotesByTag($tagName, $courseId, $academicYearId = null) {
        $sql = "SELECT n.* 
                FROM notes n 
                JOIN note_tags nt ON n.id = nt.note_id 
                JOIN tags t ON nt.tag_id = t.id 
                JOIN sections s ON n.section_id = s.id 
                WHERE t.name = ? AND s.course_id = ?";
        
        $params = [$tagName, $courseId];
        
        if ($academicYearId !== null) {
            $sql .= " AND n.academic_year_id = ?";
            $params[] = $academicYearId;
        }
        
        $sql .= " ORDER BY n.date DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
} 