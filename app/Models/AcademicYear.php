<?php
namespace App\Models;

use DateTime;

class AcademicYear {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM academic_years ORDER BY start_date DESC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getActive() {
        $stmt = $this->db->query("SELECT * FROM academic_years WHERE is_active = 1 LIMIT 1");
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("
                INSERT INTO academic_years (name, start_date, end_date) 
                VALUES (:name, :start_date, :end_date)
            ");
            
            $stmt->execute([
                'name' => $data['name'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date']
            ]);
            
            $academicYearId = $this->db->lastInsertId();
            
            // Generate weeks based on the number specified
            if (isset($data['num_weeks']) && is_numeric($data['num_weeks'])) {
                $this->generateWeeks($academicYearId, (int)$data['num_weeks']);
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    private function generateWeeks($academicYearId, $numWeeks) {
        $year = $this->get($academicYearId);
        $startDate = new \DateTime($year['start_date']);
        $endDate = new \DateTime($year['end_date']);
        
        $interval = $endDate->diff($startDate)->days / $numWeeks;
        
        for ($i = 1; $i <= $numWeeks; $i++) {
            $weekEndDate = clone $startDate;
            $weekEndDate->modify('+' . floor($interval) . ' days');
            
            if ($i == $numWeeks) {
                $weekEndDate = $endDate;
            }
            
            $stmt = $this->db->prepare("
                INSERT INTO academic_weeks (
                    academic_year_id, week_number, start_date, end_date
                ) VALUES (?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $academicYearId,
                $i,
                $startDate->format('Y-m-d'),
                $weekEndDate->format('Y-m-d')
            ]);
            
            $startDate = clone $weekEndDate;
            $startDate->modify('+1 day');
        }
    }

    public function setActive($id) {
        $this->db->beginTransaction();
        try {
            $this->db->exec("UPDATE academic_years SET is_active = 0");
            $stmt = $this->db->prepare("UPDATE academic_years SET is_active = 1 WHERE id = ?");
            $stmt->execute([$id]);
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function update($id, $data) {
        $this->db->beginTransaction();
        try {
            // Update the academic year
            $stmt = $this->db->prepare("
                UPDATE academic_years 
                SET name = :name, 
                    start_date = :start_date, 
                    end_date = :end_date
                WHERE id = :id
            ");
            
            $stmt->execute([
                'id' => $id,
                'name' => $data['name'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date']
            ]);

            // Regenerate weeks if num_weeks is provided
            if (isset($data['num_weeks'])) {
                // Delete existing weeks
                $stmt = $this->db->prepare("DELETE FROM academic_weeks WHERE academic_year_id = ?");
                $stmt->execute([$id]);
                
                // Generate new weeks
                $this->generateWeeks($id, $data['num_weeks']);
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    public function getWeeks($academicYearId) {
        $stmt = $this->db->prepare("SELECT * FROM academic_weeks WHERE academic_year_id = ? ORDER BY week_number");
        $stmt->execute([$academicYearId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function get($id) {
        $stmt = $this->db->prepare("SELECT * FROM academic_years WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function createWeek($data) {
        $sql = "INSERT INTO academic_weeks (academic_year_id, week_number, start_date, end_date) 
                VALUES (:academic_year_id, :week_number, :start_date, :end_date)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'academic_year_id' => $data['academic_year_id'],
            'week_number' => $data['week_number'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date']
        ]);
    }
} 