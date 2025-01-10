<?php
namespace App\Controllers;

use App\Models\Setting;
use App\Models\Course;
use App\Models\Section;
use App\Models\Note;
use App\Utils\Logger;
use App\Models\AcademicYear;
use App\Models\WeeklyPlan;
use DateTime;

class AdminController {
    private $settingModel;
    private $courseModel;
    private $sectionModel;
    private $noteModel;
    private $db;
    
    public function __construct($db) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if user is logged in and is admin
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            $_SESSION['error'] = 'Access denied. Admin privileges required.';
            header('Location: /');
            exit;
        }
        
        $this->db = $db;
        $this->settingModel = new Setting($db);
        $this->courseModel = new Course($db);
        $this->sectionModel = new Section($db);
        $this->noteModel = new Note($db);
    }
    
    public function dashboard() {
        $settings = $this->settingModel->getAll();
        $courses = $this->courseModel->getAll();
        $sectionModel = $this->sectionModel;
        $academicYearModel = new AcademicYear($this->db);
        $academicYears = $academicYearModel->getAll();
        $activeYear = $academicYearModel->getActive();
        
        // Get weekly plans count per course
        $weeklyPlanModel = new WeeklyPlan($this->db);
        $weeklyPlanCounts = [];
        foreach ($courses as $course) {
            $plans = $weeklyPlanModel->getByCourse($course['id']);
            $weeklyPlanCounts[$course['id']] = count($plans);
        }
        
        // Get daily notes count per section
        $noteCounts = [];
        foreach ($courses as $course) {
            $sections = $sectionModel->getAllByCourse($course['id']);
            foreach ($sections as $section) {
                $notes = $this->noteModel->getAllBySection($section['id']);
                $noteCounts[$section['id']] = count($notes);
            }
        }
        
        require ROOT_PATH . '/app/Views/admin/dashboard.php';
    }

    public function courses() {
        $courses = $this->courseModel->getAll();
        $sectionModel = $this->sectionModel;
        require ROOT_PATH . '/app/Views/admin/courses/index.php';
    }

    public function createCourse() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            require ROOT_PATH . '/app/Views/admin/courses/create.php';
        } else {
            if ($this->courseModel->create($_POST)) {
                Logger::log("Course created: {$_POST['name']} (Short name: {$_POST['short_name']}) by Teacher: {$_POST['teacher']}", 'INFO');
                $_SESSION['success'] = 'Course created successfully';
            } else {
                Logger::log("Failed to create course: {$_POST['name']}", 'ERROR');
                $_SESSION['error'] = 'Failed to create course';
            }
            header('Location: /admin/courses');
            exit;
        }
    }

    public function editCourse($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $course = $this->courseModel->get($id);
            require ROOT_PATH . '/app/Views/admin/courses/edit.php';
        } else {
            if ($this->courseModel->update($id, $_POST)) {
                Logger::log("Course updated: {$_POST['name']} (ID: $id) - Teacher: {$_POST['teacher']}", 'INFO');
                $_SESSION['success'] = 'Course updated successfully';
            } else {
                Logger::log("Failed to update course ID: $id", 'ERROR');
                $_SESSION['error'] = 'Failed to update course';
            }
            header('Location: /admin/courses');
            exit;
        }
    }

    public function deleteCourse($id) {
        $course = $this->courseModel->get($id);
        if ($this->courseModel->delete($id)) {
            Logger::log("Course deleted: {$course['name']} (ID: $id) - Teacher: {$course['teacher']}", 'INFO');
            $_SESSION['success'] = 'Course deleted successfully';
        } else {
            Logger::log("Failed to delete course ID: $id", 'ERROR');
            $_SESSION['error'] = 'Failed to delete course';
        }
        header('Location: /admin/courses');
        exit;
    }

    public function updateSettings() {
        // Registration setting
        $registration = isset($_POST['registration_enabled']) ? 'true' : 'false';
        $maxNotes = $_POST['max_notes_per_user'] ?? '100';
        $maintenance = isset($_POST['maintenance_mode']) ? 'true' : 'false';
        
        // Update settings
        $this->settingModel->set('registration_enabled', $registration);
        $this->settingModel->set('max_notes_per_user', $maxNotes);
        $this->settingModel->set('maintenance_mode', $maintenance);
        
        $_SESSION['success'] = 'Settings updated successfully';
        header('Location: /admin/dashboard');
        exit;
    }

    public function sections($courseId) {
        $course = $this->courseModel->get($courseId);
        $sections = $this->sectionModel->getAllByCourse($courseId);
        require ROOT_PATH . '/app/Views/admin/sections/index.php';
    }

    public function createSection($courseId) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $course = $this->courseModel->get($courseId);
            require ROOT_PATH . '/app/Views/admin/sections/create.php';
        } else {
            $_POST['course_id'] = $courseId;
            if ($this->sectionModel->create($_POST)) {
                Logger::log("Section created: {$_POST['name']} for course ID: $courseId", 'INFO');
                $_SESSION['success'] = 'Section created successfully';
            } else {
                Logger::log("Failed to create section for course ID: $courseId", 'ERROR');
                $_SESSION['error'] = 'Failed to create section';
            }
            header("Location: /admin/courses/$courseId/sections");
            exit;
        }
    }

    public function editSection($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $section = $this->sectionModel->get($id);
            $course = $this->courseModel->get($section['course_id']);
            require ROOT_PATH . '/app/Views/admin/sections/edit.php';
        } else {
            if ($this->sectionModel->update($id, $_POST)) {
                Logger::log("Section updated: {$_POST['name']} (ID: $id)", 'INFO');
                $_SESSION['success'] = 'Section updated successfully';
            } else {
                Logger::log("Failed to update section ID: $id", 'ERROR');
                $_SESSION['error'] = 'Failed to update section';
            }
            $section = $this->sectionModel->get($id);
            header("Location: /admin/courses/{$section['course_id']}/sections");
            exit;
        }
    }

    public function deleteSection($id) {
        $section = $this->sectionModel->get($id);
        $courseId = $section['course_id'];
        if ($this->sectionModel->delete($id)) {
            Logger::log("Section deleted: {$section['name']} (ID: $id)", 'INFO');
            $_SESSION['success'] = 'Section deleted successfully';
        } else {
            Logger::log("Failed to delete section ID: $id", 'ERROR');
            $_SESSION['error'] = 'Failed to delete section';
        }
        header("Location: /admin/courses/$courseId/sections");
        exit;
    }

    public function createNote($sectionId) {
        error_log("Method hit: createNote with sectionId: $sectionId");
        error_log("Request method: " . $_SERVER['REQUEST_METHOD']);
        
        $section = $this->sectionModel->get($sectionId);
        $course = $this->courseModel->get($section['course_id']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            require ROOT_PATH . '/app/Views/admin/notes/create.php';
        } else {
            error_log('POST data: ' . print_r($_POST, true));
            $_POST['section_id'] = $sectionId;
            $_POST['user_id'] = $_SESSION['user_id'];
            
            if ($this->noteModel->create($_POST)) {
                Logger::log("Daily note created for section ID: $sectionId", 'INFO');
                $_SESSION['success'] = 'Daily note created successfully';
            } else {
                Logger::log("Failed to create daily note for section ID: $sectionId", 'ERROR');
                $_SESSION['error'] = 'Failed to create daily note';
            }
            header("Location: /admin/courses/{$course['id']}/sections");
            exit;
        }
    }

    public function academicYears() {
        $academicYearModel = new AcademicYear($this->db);
        $academicYears = $academicYearModel->getAll();
        require ROOT_PATH . '/app/Views/admin/settings/academic_years.php';
    }

    public function createAcademicYear() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $start = new DateTime($_POST['start_date']);
            $end = new DateTime($_POST['end_date']);
            $interval = $start->diff($end);
            $_POST['num_weeks'] = ceil($interval->days / 7);
            
            $academicYearModel = new AcademicYear($this->db);
            if ($academicYearModel->create($_POST)) {
                $_SESSION['success'] = 'Academic year created successfully';
            } else {
                $_SESSION['error'] = 'Failed to create academic year';
            }
        }
        header('Location: /admin/settings/academic-years');
        exit;
    }

    public function setActiveAcademicYear() {
        $academicYearModel = new AcademicYear($this->db);
        if ($academicYearModel->setActive($_POST['id'])) {
            $_SESSION['success'] = 'Academic year set as active';
        }
        header('Location: /admin/settings/academic-years');
        exit;
    }

    public function editAcademicYear($id) {
        $academicYearModel = new AcademicYear($this->db);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($academicYearModel->update($id, $_POST)) {
                $_SESSION['success'] = 'Academic year updated successfully';
            } else {
                $_SESSION['error'] = 'Failed to update academic year';
            }
            header('Location: /admin/dashboard');
            exit;
        }
    }
    public function editNote($id) {
        $note = $this->noteModel->get($id);
        if (!$note) {
            $_SESSION['error'] = 'Note not found';
            header('Location: /admin/courses');
            exit;
        }
    
        $section = $this->sectionModel->get($note['section_id']);
        $course = $this->courseModel->get($section['course_id']);
    
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            require ROOT_PATH . '/app/Views/admin/notes/edit.php';
        } else {
            $data = [
                'date' => $_POST['date'],
                'title' => $_POST['title'],
                'content' => $_POST['content']
            ];
    
            if ($this->noteModel->update($id, $data)) {
                $_SESSION['success'] = 'Note updated successfully';
                Logger::log("Note updated: ID $id", 'INFO');
            } else {
                $_SESSION['error'] = 'Failed to update note';
                Logger::log("Failed to update note ID: $id", 'ERROR');
            }
    
            header('Location: /courses/' . $course['id'] . '/sections/' . $section['id'] . '/notes');
            exit;
        }
    }
    public function weeklyPlans($courseId) {
        $course = $this->courseModel->get($courseId);
        if (!$course) {
            $_SESSION['error'] = 'Course not found';
            header('Location: /admin/courses');
            exit;
        }

        $academicYearModel = new AcademicYear($this->db);
        $activeYear = $academicYearModel->getActive();
        
        // Debug output without exit
        error_log('Active Year: ' . print_r($activeYear, true));
        error_log('All Years: ' . print_r($academicYearModel->getAll(), true));
        
        if (!$activeYear) {
            $_SESSION['error'] = 'No active academic year found';
            header('Location: /admin/settings/academic-years');
            exit;
        }

        $weeks = $academicYearModel->getWeeks($activeYear['id']);
        if (empty($weeks)) {
            $_SESSION['error'] = 'Please define weeks for the active academic year';
            header('Location: /admin/settings/academic-years');
            exit;
        }

        $weeklyPlanModel = new WeeklyPlan($this->db);
        $plans = $weeklyPlanModel->getByCourse($courseId);

        require ROOT_PATH . '/app/Views/admin/courses/weekly_plans.php';
    }

    public function updateWeeklyPlan($courseId, $weekId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/courses/' . $courseId . '/weekly-plans');
            exit;
        }

        $weeklyPlanModel = new WeeklyPlan($this->db);
        $existingPlan = $weeklyPlanModel->getByWeek($courseId, $weekId);

        $data = [
            'course_id' => $courseId,
            'academic_week_id' => $weekId,
            'topic' => $_POST['topic'],
            'objectives' => $_POST['objectives'],
            'resources' => $_POST['resources'],
            'notes' => $_POST['notes']
        ];

        if ($existingPlan) {
            $success = $weeklyPlanModel->update($existingPlan['id'], $data);
        } else {
            $success = $weeklyPlanModel->create($data);
        }

        if ($success) {
            $_SESSION['success'] = 'Weekly plan updated successfully';
            Logger::log("Weekly plan updated for course ID: $courseId, week ID: $weekId", 'INFO');
        } else {
            $_SESSION['error'] = 'Failed to update weekly plan';
            Logger::log("Failed to update weekly plan for course ID: $courseId, week ID: $weekId", 'ERROR');
        }

        header('Location: /admin/courses/' . $courseId . '/weekly-plans');
        exit;
    }

    public function editWeeklyPlan($courseId, $weekId) {
        $course = $this->courseModel->get($courseId);
        if (!$course) {
            $_SESSION['error'] = 'Course not found';
            header('Location: /admin/courses');
            exit;
        }

        $academicYearModel = new AcademicYear($this->db);
        $activeYear = $academicYearModel->getActive();
        $weeks = $academicYearModel->getWeeks($activeYear['id']);
        
        $week = array_filter($weeks, function($w) use ($weekId) {
            return $w['id'] == $weekId;
        });
        $week = reset($week);
        
        if (!$week) {
            $_SESSION['error'] = 'Week not found';
            header('Location: /admin/courses/' . $courseId . '/weekly-plans');
            exit;
        }

        $weeklyPlanModel = new WeeklyPlan($this->db);
        $plan = $weeklyPlanModel->getByWeek($courseId, $weekId);
        
        require ROOT_PATH . '/app/Views/admin/courses/edit_weekly_plan.php';
    }

    public function createWeeklyPlan($courseId, $weekId) {
        $course = $this->courseModel->get($courseId);
        if (!$course) {
            $_SESSION['error'] = 'Course not found';
            header('Location: /admin/courses');
            exit;
        }

        $academicYearModel = new AcademicYear($this->db);
        $activeYear = $academicYearModel->getActive();
        $weeks = $academicYearModel->getWeeks($activeYear['id']);

        $week = array_filter($weeks, function($w) use ($weekId) {
            return $w['id'] == $weekId;
        });
        $week = reset($week);
        
        require ROOT_PATH . '/app/Views/admin/courses/create_weekly_plan.php';
    }

    public function storeWeeklyPlan($courseId, $weekId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/courses/' . $courseId . '/weekly-plans');
            exit;
        }

        $data = [
            'course_id' => $courseId,
            'academic_week_id' => $weekId,
            'topic' => $_POST['topic'],
            'objectives' => $_POST['objectives'],
            'resources' => $_POST['resources'],
            'notes' => $_POST['notes']
        ];

        $weeklyPlanModel = new WeeklyPlan($this->db);
        $success = $weeklyPlanModel->create($data);

        if ($success) {
            $_SESSION['success'] = 'Weekly plan created successfully';
            Logger::log("Weekly plan created for course ID: $courseId, week ID: $weekId", 'INFO');
        } else {
            $_SESSION['error'] = 'Failed to create weekly plan';
            Logger::log("Failed to create weekly plan for course ID: $courseId, week ID: $weekId", 'ERROR');
        }

        header('Location: /admin/courses/' . $courseId . '/weekly-plans');
        exit;
    }

    public function updateNote($id) {
        $note = $this->noteModel->get($id);
        $section = $this->sectionModel->get($note['section_id']);
        $course = $this->courseModel->get($section['course_id']);

        if ($this->noteModel->update($id, $_POST)) {
            $_SESSION['success'] = 'Note updated successfully';
            Logger::log("Note updated: ID $id", 'INFO');
        } else {
            $_SESSION['error'] = 'Failed to update note';
            Logger::log("Failed to update note ID: $id", 'ERROR');
        }

        header('Location: /courses/' . $course['id'] . '/sections/' . $section['id'] . '/notes');
        exit;
    }
} 