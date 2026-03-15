<?php
namespace App\Models;

/**
 * PredictedGradeConfig
 * Key/value config for weights and IB boundaries.
 */
class PredictedGradeConfig {
    /** @var \PDO */
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function get($key) {
        $stmt = $this->db->prepare("SELECT config_value FROM predicted_grade_config WHERE config_key = ?");
        $stmt->execute([$key]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? $row['config_value'] : null;
    }

    /**
     * Get weights: exam components (paper1, ia, paper2) sum to 1.0.
     * weight_soft = separate "outside" weight (0–0.2): final = (1-weight_soft)*exam_avg + weight_soft*soft_avg.
     */
    public function getWeights() {
        return [
            'paper1' => (float) ($this->get('weight_paper1') ?: 0.40),
            'ia' => (float) ($this->get('weight_ia') ?: 0.20),
            'paper2' => (float) ($this->get('weight_paper2') ?: 0.40),
            'weight_soft' => (float) ($this->get('weight_soft') ?: 0),
        ];
    }

    /**
     * Set a config value (insert or update). Used by admin.
     */
    public function set($key, $value) {
        $stmt = $this->db->prepare("
            INSERT INTO predicted_grade_config (config_key, config_value) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE config_value = VALUES(config_value)
        ");
        return $stmt->execute([$key, (string) $value]);
    }

    /**
     * Get IB boundary (min percentage) for each grade 1-7.
     */
    public function getBoundaries() {
        $b = [];
        for ($g = 1; $g <= 7; $g++) {
            $b[$g] = (float) ($this->get("boundary_$g") ?: [0, 56, 63, 70, 77, 84, 91][$g - 1]);
        }
        return $b;
    }
}
