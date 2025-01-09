<?php
namespace App\Controllers;

use App\Models\Course;
use App\Models\AcademicYear;
use App\Models\WeeklyPlan;
use App\Models\Section;
use App\Models\Note;

class CourseController {
    private $db;
    private $courseModel;
    private $sectionModel;
    private $noteModel;
    private $weeklyPlanModel;

    public function __construct($db) {
        $this->db = $db;
        $this->courseModel = new Course($db);
        $this->sectionModel = new Section($db);
        $this->noteModel = new Note($db);
        $this->weeklyPlanModel = new WeeklyPlan($db);
    }

    public function weeklyPlans($courseId) {
        $course = $this->courseModel->get($courseId);
        if (!$course) {
            $_SESSION['error'] = 'Course not found';
            header('Location: /');
            exit;
        }

        $academicYearModel = new AcademicYear($this->db);
        $activeYear = $academicYearModel->getActive();
        
        $weeklyPlanModel = new WeeklyPlan($this->db);
        $plans = $weeklyPlanModel->getByCourse($courseId);

        // Join with academic_weeks data
        if (!empty($plans)) {
            $weeks = $academicYearModel->getWeeks($activeYear['id']);
            foreach ($plans as &$plan) {
                foreach ($weeks as $week) {
                    if ($week['id'] == $plan['academic_week_id']) {
                        $plan['week_number'] = $week['week_number'];
                        $plan['start_date'] = $week['start_date'];
                        $plan['end_date'] = $week['end_date'];
                        break;
                    }
                }
            }
        }

        require ROOT_PATH . '/app/Views/weekly_plans.php';
    }

    public function sectionNotes($courseId, $sectionId) {
        $course = $this->courseModel->get($courseId);
        $section = $this->sectionModel->get($sectionId);
        
        if (!$course || !$section) {
            $_SESSION['error'] = 'Course or section not found';
            header('Location: /');
            exit;
        }

        $notes = $this->noteModel->getAllBySection($sectionId);
        
        // Sort notes by date in descending order (newest first)
        usort($notes, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        require ROOT_PATH . '/app/Views/section_notes.php';
    }

    public function index() {
        $courses = $this->courseModel->getAll();
        $sections = [];
        
        foreach ($courses as $course) {
            $sections[$course['id']] = $this->sectionModel->getAllByCourse($course['id']);
        }
        
        require ROOT_PATH . '/app/Views/courses/index.php';
    }

    public function yearlyPlans($courseId) {
        // This is the same implementation as weeklyPlans
        $course = $this->courseModel->get($courseId);
        if (!$course) {
            $_SESSION['error'] = 'Course not found';
            header('Location: /');
            exit;
        }

        $academicYearModel = new AcademicYear($this->db);
        $activeYear = $academicYearModel->getActive();
        $plans = [];
        
        if ($activeYear) {
            $weeks = $academicYearModel->getWeeks($activeYear['id']);
            $plans = $this->weeklyPlanModel->getAllByCourse($courseId, $activeYear['id']);
            
            foreach ($plans as &$plan) {
                foreach ($weeks as $week) {
                    if ($week['id'] == $plan['academic_week_id']) {
                        $plan['week_number'] = $week['week_number'];
                        $plan['start_date'] = $week['start_date'];
                        $plan['end_date'] = $week['end_date'];
                        break;
                    }
                }
            }
        }
        
        require ROOT_PATH . '/app/Views/yearly_plans.php';
    }
} 