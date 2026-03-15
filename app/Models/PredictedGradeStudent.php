<?php
namespace App\Models;

/**
 * PredictedGradeStudent
 * Students access their data via a unique code (no username/password).
 */
class PredictedGradeStudent {
    /** @var \PDO */
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Find student by code.
     * @param string $code
     * @return array|false
     */
    public function findByCode($code) {
        $code = trim($code);
        if ($code === '') return false;
        $stmt = $this->db->prepare("SELECT * FROM predicted_grade_students WHERE code = ?");
        $stmt->execute([$code]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Create a new student and return with generated code.
     * @return array|false ['id' => int, 'code' => string]
     */
    public function create() {
        $code = $this->generateUniqueCode();
        $stmt = $this->db->prepare("INSERT INTO predicted_grade_students (code) VALUES (?)");
        if (!$stmt->execute([$code])) return false;
        return ['id' => (int) $this->db->lastInsertId(), 'code' => $code];
    }

    /**
     * Generate a 10-character alphanumeric code (no ambiguous chars).
     */
    private function generateUniqueCode() {
        $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $max = strlen($chars) - 1;
        for ($attempt = 0; $attempt < 20; $attempt++) {
            $code = '';
            for ($i = 0; $i < 10; $i++) {
                $code .= $chars[random_int(0, $max)];
            }
            $stmt = $this->db->prepare("SELECT id FROM predicted_grade_students WHERE code = ?");
            $stmt->execute([$code]);
            if (!$stmt->fetch()) return $code;
        }
        return bin2hex(random_bytes(5));
    }

    public function get($id) {
        $stmt = $this->db->prepare("SELECT * FROM predicted_grade_students WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Get all students (for admin list).
     * @return array
     */
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM predicted_grade_students ORDER BY created_at DESC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
