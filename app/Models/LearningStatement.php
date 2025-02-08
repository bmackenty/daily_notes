<?php
namespace App\Models;

class LearningStatement {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getAll() {
        return $this->db->query("SELECT * FROM learning_statement ORDER BY identifier")->fetchAll();
    }
    
    public function get($id) {
        $stmt = $this->db->prepare("SELECT * FROM learning_statement WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO learning_statement (identifier, learning_statement) VALUES (?, ?)");
        return $stmt->execute([$data['identifier'], $data['learning_statement']]);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE learning_statement SET identifier = ?, learning_statement = ? WHERE id = ?");
        return $stmt->execute([$data['identifier'], $data['learning_statement'], $id]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM learning_statement WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function updatePosition($id, $position) {
        $stmt = $this->db->prepare("UPDATE learning_statement SET position = ? WHERE id = ?");
        return $stmt->execute([$position, $id]);
    }
} 