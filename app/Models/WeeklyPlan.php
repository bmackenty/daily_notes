<?php
namespace App\Models;

class WeeklyPlan {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function create($data) {
        $sql = "INSERT INTO course_weekly_plans (course_id, academic_week_id, topic, objectives, resources, notes) 
                VALUES (:course_id, :academic_week_id, :topic, :objectives, :resources, :notes)";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'course_id' => $data['course_id'],
                'academic_week_id' => $data['academic_week_id'],
                'topic' => $data['topic'],
                'objectives' => $data['objectives'] ?? null,
                'resources' => $data['resources'] ?? null,
                'notes' => $data['notes'] ?? null
            ]);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function getByCourse($courseId) {
        $stmt = $this->db->prepare("SELECT * FROM course_weekly_plans WHERE course_id = ?");
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }

    public function update($id, $data) {
        $sql = "UPDATE course_weekly_plans 
                SET topic = :topic, objectives = :objectives, 
                    resources = :resources, notes = :notes 
                WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'id' => $id,
                'topic' => $data['topic'],
                'objectives' => $data['objectives'] ?? null,
                'resources' => $data['resources'] ?? null,
                'notes' => $data['notes'] ?? null
            ]);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function getByWeek($courseId, $weekId) {
        $sql = "SELECT * FROM course_weekly_plans 
                WHERE course_id = :course_id AND academic_week_id = :week_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'course_id' => $courseId,
            'week_id' => $weekId
        ]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getAllByCourse($courseId, $academicYearId) {
        $sql = "SELECT cwp.*, aw.week_number, aw.start_date, aw.end_date 
                FROM course_weekly_plans cwp
                JOIN academic_weeks aw ON cwp.academic_week_id = aw.id
                WHERE cwp.course_id = :course_id
                AND aw.academic_year_id = :academic_year_id
                ORDER BY aw.week_number ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'course_id' => $courseId,
            'academic_year_id' => $academicYearId
        ]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
} 