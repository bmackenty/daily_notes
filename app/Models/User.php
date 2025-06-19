<?php
namespace App\Models;

class User {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['name'],
            $data['email'],
            $data['password'],
            $data['role'] ?? 'user'
        ]);
        return $this->db->lastInsertId();
    }

    public function validatePassword($password) {
        // Minimum 8 characters
        if (strlen($password) < 8) {
            return "Password must be at least 8 characters long";
        }

        // At least one uppercase letter
        if (!preg_match('/[A-Z]/', $password)) {
            return "Password must contain at least one uppercase letter";
        }

        // At least one lowercase letter
        if (!preg_match('/[a-z]/', $password)) {
            return "Password must contain at least one lowercase letter";
        }

        // At least one number
        if (!preg_match('/[0-9]/', $password)) {
            return "Password must contain at least one number";
        }

        // At least one special character
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            return "Password must contain at least one special character";
        }

        return true;
    }
} 