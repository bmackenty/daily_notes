<?php
namespace App\Utils;

use App\Models\PredictedGradeEntry;
use App\Models\PredictedGradeConfig;

/**
 * Computes predicted grade and returns a breakdown for display (visible math).
 */
class PredictedGradeCalculator {
    /** @var \PDO */
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Full calculation for one student.
     * Returns: category_avg, paper1_avg, ia_avg, paper2_avg, final_percent, ib_grade, steps (for display).
     * Uses student's weight_soft_override if set; otherwise global config.
     */
    public function calculate($studentId, $student = null) {
        $entryModel = new PredictedGradeEntry($this->db);
        $configModel = new PredictedGradeConfig($this->db);
        $weights = $configModel->getWeights();
        if ($student === null) {
            $studentModel = new \App\Models\PredictedGradeStudent($this->db);
            $student = $studentModel->get($studentId);
        }
        if ($student && isset($student['weight_soft_override']) && $student['weight_soft_override'] !== null && $student['weight_soft_override'] !== '') {
            $weights['weight_soft'] = (float) $student['weight_soft_override'];
        }
        $boundaries = $configModel->getBoundaries();

        $categoryAvg = [];
        foreach (PredictedGradeEntry::ALL_CATEGORIES as $cat) {
            $avg = $entryModel->getCategoryAverage($studentId, $cat);
            $categoryAvg[$cat] = $avg;
        }

        // Paper 1: average of 6 topics (each 1/6)
        $paper1Topics = PredictedGradeEntry::PAPER1_TOPICS;
        $paper1Sum = 0;
        $paper1Count = 0;
        foreach ($paper1Topics as $t) {
            if ($categoryAvg[$t] !== null) {
                $paper1Sum += $categoryAvg[$t];
                $paper1Count++;
            }
        }
        $paper1_avg = $paper1Count > 0 ? $paper1Sum / $paper1Count : null;

        // IA: single topic
        $ia_avg = $categoryAvg['ia'];

        // Paper 2: average of 4 topics (each 0.25)
        $paper2Topics = PredictedGradeEntry::PAPER2_TOPICS;
        $paper2Sum = 0;
        $paper2Count = 0;
        foreach ($paper2Topics as $t) {
            if ($categoryAvg[$t] !== null) {
                $paper2Sum += $categoryAvg[$t];
                $paper2Count++;
            }
        }
        $paper2_avg = $paper2Count > 0 ? $paper2Sum / $paper2Count : null;

        // Exam component (100%): Paper 1 + IA + Paper 2 only
        $w1 = $weights['paper1'];
        $w2 = $weights['ia'];
        $w3 = $weights['paper2'];
        $exam_avg = null;
        if ($paper1_avg !== null || $ia_avg !== null || $paper2_avg !== null) {
            $exam_avg = ($w1 * ($paper1_avg ?? 0)) + ($w2 * ($ia_avg ?? 0)) + ($w3 * ($paper2_avg ?? 0));
        }

        // Soft factors (outside the 100%): homework, study habits, independent coding → one average
        $homework_avg = $categoryAvg['homework'] ?? null;
        $study_habits_avg = $categoryAvg['study_habits'] ?? null;
        $independent_coding_avg = $categoryAvg['independent_coding'] ?? null;
        $soft_avg = null;
        $softParts = array_filter([$homework_avg, $study_habits_avg, $independent_coding_avg], function ($v) { return $v !== null; });
        if (count($softParts) > 0) {
            $soft_avg = array_sum($softParts) / count($softParts);
        }

        $weight_soft = $weights['weight_soft'] ?? 0;
        $final_avg = null;
        if ($exam_avg !== null) {
            if ($soft_avg !== null && $weight_soft > 0) {
                $final_avg = (1 - $weight_soft) * $exam_avg + $weight_soft * $soft_avg;
            } else {
                $final_avg = $exam_avg;
            }
        }

        // Predicted grade = round final average to nearest 1-7
        $ib_grade = null;
        if ($final_avg !== null) {
            $ib_grade = (int) round($final_avg);
            if ($ib_grade < 1) $ib_grade = 1;
            if ($ib_grade > 7) $ib_grade = 7;
        }

        $steps = [
            'category_avg' => $categoryAvg,
            'paper1_avg' => $paper1_avg,
            'paper1_topics_used' => $paper1Topics,
            'ia_avg' => $ia_avg,
            'paper2_avg' => $paper2_avg,
            'paper2_topics_used' => $paper2Topics,
            'exam_avg' => $exam_avg,
            'homework_avg' => $homework_avg,
            'study_habits_avg' => $study_habits_avg,
            'independent_coding_avg' => $independent_coding_avg,
            'soft_avg' => $soft_avg,
            'weights' => $weights,
            'final_avg' => $final_avg,
            'ib_grade' => $ib_grade,
            'boundaries' => $boundaries,
        ];

        return [
            'category_avg' => $categoryAvg,
            'paper1_avg' => $paper1_avg,
            'ia_avg' => $ia_avg,
            'paper2_avg' => $paper2_avg,
            'exam_avg' => $exam_avg,
            'homework_avg' => $homework_avg,
            'study_habits_avg' => $study_habits_avg,
            'independent_coding_avg' => $independent_coding_avg,
            'soft_avg' => $soft_avg,
            'final_avg' => $final_avg,
            'final_percent' => $final_avg,
            'ib_grade' => $ib_grade,
            'steps' => $steps,
        ];
    }
}
