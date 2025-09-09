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
    
    public function findById($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    public function createRememberToken($userId, $tokenHash, $expiresAt) {
        $sql = "INSERT INTO remember_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $tokenHash, $expiresAt]);
    }
    
    public function findUserByRememberToken($tokenHash) {
        $sql = "SELECT u.* FROM users u 
                INNER JOIN remember_tokens rt ON u.id = rt.user_id 
                WHERE rt.token_hash = ? AND rt.expires_at > NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$tokenHash]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    public function deleteRememberToken($tokenHash) {
        $sql = "DELETE FROM remember_tokens WHERE token_hash = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$tokenHash]);
    }
    
    public function deleteAllRememberTokens($userId) {
        $sql = "DELETE FROM remember_tokens WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId]);
    }
    
    public function cleanupExpiredRememberTokens() {
        $sql = "DELETE FROM remember_tokens WHERE expires_at < NOW()";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute();
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