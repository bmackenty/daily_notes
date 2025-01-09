<?php
namespace App\Models;

class Setting {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function get($key) {
        $stmt = $this->db->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['setting_value'] : null;
    }
    
    public function set($key, $value) {
        $stmt = $this->db->prepare("
            UPDATE settings 
            SET setting_value = ?, updated_at = CURRENT_TIMESTAMP 
            WHERE setting_key = ?
        ");
        return $stmt->execute([$value, $key]);
    }
    
    public function getAll() {
        $stmt = $this->db->query("SELECT setting_key, setting_value FROM settings");
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }
} 