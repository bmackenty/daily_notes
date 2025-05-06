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
        $sql = "INSERT INTO courses (name, description, code) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['name'],
            $data['description'] ?? null,
            $data['code']
        ]);
        return $this->db->lastInsertId();
    }
    
    public function findByCode($code) {
        $sql = "SELECT * FROM courses WHERE code = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$code]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE courses SET name = ?, description = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['name'],
            $data['description'] ?? null,
            $id
        ]);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM courses WHERE id = ?";
        $stmt = $this->db->prepare($sql);
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