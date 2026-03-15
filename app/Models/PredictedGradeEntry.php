<?php
namespace App\Models;

/**
 * PredictedGradeEntry
 * One row per exam/assessment. Multiple rows per category = weighted average.
 */
class PredictedGradeEntry {
    /** @var \PDO */
    private $db;

    /** Topic keys by component for validation */
    const PAPER1_TOPICS = ['computer_fundamentals', 'networks', 'databases', 'machine_learning', 'case_study', 'mock_exam'];
    const IA_TOPICS = ['ia'];
    const PAPER2_TOPICS = ['programming', 'comp_thinking', 'oop', 'adts'];
    /** Soft factors that can lift or lower the predicted grade */
    const SOFT_FACTOR_TOPICS = ['homework', 'study_habits', 'independent_coding'];
    const ALL_CATEGORIES = ['computer_fundamentals', 'networks', 'databases', 'machine_learning', 'case_study', 'mock_exam', 'ia', 'programming', 'comp_thinking', 'oop', 'adts', 'homework', 'study_habits', 'independent_coding'];

    public function __construct($db) {
        $this->db = $db;
    }

    public static function getAllCategories() {
        return self::ALL_CATEGORIES;
    }

    public static function getCategoryLabel($key) {
        $labels = [
            'computer_fundamentals' => 'Computer fundamentals',
            'networks' => 'Networks',
            'databases' => 'Databases',
            'machine_learning' => 'Machine learning',
            'case_study' => 'Case study',
            'mock_exam' => 'Mock exam',
            'ia' => 'IA',
            'programming' => 'Programming',
            'comp_thinking' => 'Comp Thinking',
            'oop' => 'OOP',
            'adts' => "ADT's",
            'homework' => 'Homework',
            'study_habits' => 'Study habits',
            'independent_coding' => 'Independent coding',
        ];
        return $labels[$key] ?? $key;
    }

    /**
     * @param int $studentId
     * @return array [category => [entries], ...]
     */
    public function getEntriesByStudent($studentId) {
        $stmt = $this->db->prepare("
            SELECT id, category, score, weight, label, created_at
            FROM predicted_grade_entries
            WHERE student_id = ?
            ORDER BY category, created_at
        ");
        $stmt->execute([$studentId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $byCategory = [];
        foreach (self::ALL_CATEGORIES as $c) {
            $byCategory[$c] = [];
        }
        foreach ($rows as $row) {
            if (isset($byCategory[$row['category']])) {
                $byCategory[$row['category']][] = $row;
            }
        }
        return $byCategory;
    }

    /**
     * Add an entry. Score is IB grade 1-7. Returns new id or false.
     */
    public function add($studentId, $category, $score, $weight = 1.0, $label = null) {
        if (!in_array($category, self::ALL_CATEGORIES, true)) return false;
        $score = (float) $score;
        $weight = (float) $weight;
        if ($weight <= 0 || $score < 1 || $score > 7) return false;
        $stmt = $this->db->prepare("
            INSERT INTO predicted_grade_entries (student_id, category, score, weight, label)
            VALUES (?, ?, ?, ?, ?)
        ");
        if (!$stmt->execute([$studentId, $category, $score, $weight, $label ?: null])) return false;
        return (int) $this->db->lastInsertId();
    }

    /**
     * Delete an entry. Verify student_id to avoid cross-student deletes.
     */
    public function delete($id, $studentId) {
        $stmt = $this->db->prepare("DELETE FROM predicted_grade_entries WHERE id = ? AND student_id = ?");
        return $stmt->execute([$id, $studentId]);
    }

    /**
     * Weighted average for one category. Returns null if no entries.
     */
    public function getCategoryAverage($studentId, $category) {
        $stmt = $this->db->prepare("
            SELECT SUM(score * weight) / SUM(weight) AS avg_score, SUM(weight) AS total_weight
            FROM predicted_grade_entries
            WHERE student_id = ? AND category = ?
        ");
        $stmt->execute([$studentId, $category]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row || $row['total_weight'] == 0) return null;
        return (float) $row['avg_score'];
    }
}
