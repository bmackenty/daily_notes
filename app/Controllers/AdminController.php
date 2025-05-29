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
use App\Models\TeacherProfile;
use App\Models\LearningStatement;

/**
 * AdminController
 * 
 * This controller handles all administrative operations in the application.
 * It manages courses, sections, notes, academic years, weekly plans, teacher profiles,
 * and learning statements. All methods require admin privileges to access.
 */
class AdminController {
    /** @var Setting Model for managing application settings */
    private $settingModel;
    
    /** @var Course Model for managing courses */
    private $courseModel;
    
    /** @var Section Model for managing course sections */
    private $sectionModel;
    
    /** @var Note Model for managing daily notes */
    private $noteModel;
    
    /** @var \PDO Database connection instance */
    private $db;
    
    /** @var TeacherProfile Model for managing teacher profiles */
    private $teacherProfileModel;
    
    /** @var LearningStatement Model for managing learning statements */
    private $learningStatementModel;
    
    /**
     * Constructor - initializes models and checks admin privileges
     * 
     * @param \PDO $db Database connection instance
     * @throws \Exception If user is not logged in or not an admin
     */
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
        $this->teacherProfileModel = new TeacherProfile($db);
        $this->learningStatementModel = new LearningStatement($db);
    }
    
    /**
     * Displays the admin dashboard with overview of all system components
     * Shows courses, sections, notes, teacher profiles, and learning statements
     */
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
        $notes = [];
        foreach ($courses as $course) {
            $sections = $sectionModel->getAllByCourse($course['id']);
            foreach ($sections as $section) {
                $notes[$section['id']] = $this->noteModel->getAllBySection($section['id']);
            }
        }
        
        // Get teacher profiles and their associated courses
        $teacherProfiles = $this->teacherProfileModel->getAll();
        $profileCourses = [];
        foreach ($teacherProfiles as $profile) {
            $profileCourses[$profile['id']] = $this->courseModel->getCoursesByTeacherProfileId($profile['id']);
        }

        $learningStatements = $this->learningStatementModel->getAll();

        require ROOT_PATH . '/app/Views/admin/dashboard.php';
    }

    /**
     * Lists all courses and their associated sections
     * Used for course management interface
     */
    public function courses() {
        $courses = $this->courseModel->getAll();
        $sectionModel = $this->sectionModel;
        $sections = [];
        foreach ($courses as $course) {
            $sections[$course['id']] = $sectionModel->getAllByCourse($course['id']);
        }
        require ROOT_PATH . '/app/Views/admin/courses/index.php';
    }

    /**
     * Creates a new course
     * Handles both GET (display form) and POST (process creation) requests
     */
    public function createCourse() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $teacherProfiles = $this->teacherProfileModel->getAll();
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

    /**
     * Edits an existing course
     * Handles both GET (display form) and POST (process update) requests
     * 
     * @param int $id Course ID to edit
     */
    public function editCourse($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $course = $this->courseModel->get($id);
            $teacherProfiles = $this->teacherProfileModel->getAll();
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

    /**
     * Deletes a course and logs the action
     * 
     * @param int $id Course ID to delete
     */
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

    /**
     * Updates application-wide settings
     * Handles registration, max notes, maintenance mode, and delete button visibility
     */
    public function updateSettings() {
        // Registration setting
        $registration = isset($_POST['registration_enabled']) ? 'true' : 'false';
        $maxNotes = $_POST['max_notes_per_user'] ?? '100';
        $maintenance = isset($_POST['maintenance_mode']) ? 'true' : 'false';
        $showDeleteButtons = isset($_POST['show_delete_buttons']) ? 'true' : 'false';
        
        // Update settings
        $this->settingModel->set('registration_enabled', $registration);
        $this->settingModel->set('max_notes_per_user', $maxNotes);
        $this->settingModel->set('maintenance_mode', $maintenance);
        $this->settingModel->set('show_delete_buttons', $showDeleteButtons);
        
        $_SESSION['success'] = 'Settings updated successfully';
        header('Location: /admin/dashboard');
        exit;
    }

    /**
     * Lists all sections for a specific course
     * 
     * @param int $courseId ID of the course to list sections for
     */
    public function sections($courseId) {
        $course = $this->courseModel->get($courseId);
        $sections = $this->sectionModel->getAllByCourse($courseId);
        $settings = $this->settingModel->getAll();
        require ROOT_PATH . '/app/Views/admin/sections/index.php';
    }

    /**
     * Creates a new section for a course
     * Handles both GET (display form) and POST (process creation) requests
     * 
     * @param int $courseId ID of the course to create section for
     */
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

    /**
     * Edits an existing section
     * Handles both GET (display form) and POST (process update) requests
     * 
     * @param int $id Section ID to edit
     */
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

    /**
     * Deletes a section and logs the action
     * 
     * @param int $id Section ID to delete
     */
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

    /**
     * Creates a new daily note for a section
     * Handles both GET (display form) and POST (process creation) requests
     * 
     * @param int $sectionId ID of the section to create note for
     */
    public function createNote($sectionId) {
        error_log("Method hit: createNote with sectionId: $sectionId");
        error_log("Request method: " . $_SERVER['REQUEST_METHOD']);
        
        $section = $this->sectionModel->get($sectionId);
        $course = $this->courseModel->get($section['course_id']);
        
        // Fetch the last note for the section
        $lastNote = $this->noteModel->getLastBySection($sectionId);
        
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

    /**
     * Manages academic years
     * Lists all academic years and their status
     */
    public function academicYears() {
        $academicYearModel = new AcademicYear($this->db);
        $academicYears = $academicYearModel->getAll();
        require ROOT_PATH . '/app/Views/admin/settings/academic_years.php';
    }

    /**
     * Creates a new academic year
     * Calculates number of weeks based on start and end dates
     */
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

    /**
     * Sets an academic year as the active one
     * Only one academic year can be active at a time
     */
    public function setActiveAcademicYear() {
        $academicYearModel = new AcademicYear($this->db);
        if ($academicYearModel->setActive($_POST['id'])) {
            $_SESSION['success'] = 'Academic year set as active';
        }
        header('Location: /admin/settings/academic-years');
        exit;
    }

    /**
     * Edits an existing academic year
     * Recalculates number of weeks based on new dates
     * 
     * @param int $id Academic year ID to edit
     */
    public function editAcademicYear($id) {
        $academicYearModel = new AcademicYear($this->db);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $start = new DateTime($_POST['start_date']);
            $end = new DateTime($_POST['end_date']);
            $interval = $start->diff($end);
            
            $data = [
                'name' => $_POST['name'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'num_weeks' => ceil($interval->days / 7)
            ];
            
            if ($academicYearModel->update($id, $data)) {
                $_SESSION['success'] = 'Academic year updated successfully';
            } else {
                $_SESSION['error'] = 'Failed to update academic year';
            }
            header('Location: /admin/dashboard#academic');
            exit;
        }
    }

    /**
     * Edits an existing note
     * Handles both GET (display form) and POST (process update) requests
     * 
     * @param int $id Note ID to edit
     */
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

    /**
     * Lists weekly plans for a course
     * Requires an active academic year with defined weeks
     * 
     * @param int $courseId ID of the course to list plans for
     */
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

    /**
     * Updates or creates a weekly plan for a course and week
     * 
     * @param int $courseId ID of the course
     * @param int $weekId ID of the academic week
     */
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

    /**
     * Displays form to edit a weekly plan
     * 
     * @param int $courseId ID of the course
     * @param int $weekId ID of the academic week
     */
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

    /**
     * Displays form to create a new weekly plan
     * 
     * @param int $courseId ID of the course
     * @param int $weekId ID of the academic week
     */
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

    /**
     * Stores a new weekly plan
     * 
     * @param int $courseId ID of the course
     * @param int $weekId ID of the academic week
     */
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

    /**
     * Updates an existing note
     * 
     * @param int $id Note ID to update
     */
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

    /**
     * Creates a new teacher profile
     * Handles both GET (display form) and POST (process creation) requests
     * Includes profile picture upload functionality
     */
    public function createTeacherProfile() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            require ROOT_PATH . '/app/Views/admin/teacher_profiles/create.php';
        } else {
            $data = $_POST;
            
            // Handle profile picture upload
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
                $uploadDir = ROOT_PATH . '/public/uploads/profile_pictures/';
                $fileName = uniqid() . '_' . basename($_FILES['profile_picture']['name']);
                $uploadFile = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
                    $data['profile_picture'] = '/uploads/profile_pictures/' . $fileName;
                }
            }

            if ($this->teacherProfileModel->create($data)) {
                Logger::log("Teacher profile created: {$data['full_name']}", 'INFO');
                $_SESSION['success'] = 'Teacher profile created successfully';
            } else {
                Logger::log("Failed to create teacher profile: {$data['full_name']}", 'ERROR');
                $_SESSION['error'] = 'Failed to create teacher profile';
            }
            header('Location: /admin/dashboard#teacher-profiles');
            exit;
        }
    }

    /**
     * Edits an existing teacher profile
     * Handles both GET (display form) and POST (process update) requests
     * Includes profile picture upload functionality
     * 
     * @param int $id Teacher profile ID to edit
     */
    public function editTeacherProfile($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $profile = $this->teacherProfileModel->get($id);
            require ROOT_PATH . '/app/Views/admin/teacher_profiles/edit.php';
        } else {
            $data = $_POST;
            
            // Handle profile picture upload
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
                $uploadDir = ROOT_PATH . '/public/uploads/profile_pictures/';
                $fileName = uniqid() . '_' . basename($_FILES['profile_picture']['name']);
                $uploadFile = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
                    $data['profile_picture'] = '/uploads/profile_pictures/' . $fileName;
                }
            }

            if ($this->teacherProfileModel->update($id, $data)) {
                Logger::log("Teacher profile updated: {$data['full_name']} (ID: $id)", 'INFO');
                $_SESSION['success'] = 'Teacher profile updated successfully';
            } else {
                Logger::log("Failed to update teacher profile ID: $id", 'ERROR');
                $_SESSION['error'] = 'Failed to update teacher profile';
            }
            header('Location: /admin/dashboard#teacher-profiles');
            exit;
        }
    }

    /**
     * Deletes a teacher profile
     * Returns JSON response for AJAX requests
     * 
     * @param int $id Teacher profile ID to delete
     * @return string JSON response indicating success or failure
     */
    public function deleteTeacherProfile($id) {
        $profile = $this->teacherProfileModel->get($id);
        if ($this->teacherProfileModel->delete($id)) {
            Logger::log("Teacher profile deleted: {$profile['full_name']} (ID: $id)", 'INFO');
            return json_encode(['success' => true]);
        } else {
            Logger::log("Failed to delete teacher profile ID: $id", 'ERROR');
            return json_encode(['success' => false]);
        }
    }

    /**
     * Retrieves a teacher profile by ID
     * Returns JSON response for AJAX requests
     * 
     * @param int $id Teacher profile ID to retrieve
     */
    public function getTeacherProfile($id) {
        $profile = $this->teacherProfileModel->get($id);
        if ($profile) {
            header('Content-Type: application/json');
            echo json_encode($profile);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Profile not found']);
        }
        exit;
    }

    /**
     * Displays form to create a new note
     * 
     * @param int $sectionId ID of the section to create note for
     */
    public function createNoteForm($sectionId) {
        $section = $this->sectionModel->get($sectionId);
        $course = $this->courseModel->get($section['course_id']);
        $lastNote = $this->noteModel->getLastBySection($sectionId);

        require ROOT_PATH . '/app/Views/admin/notes/create.php';
    }

    /**
     * Creates a new learning statement
     */
    public function createLearningStatement() {
        if ($this->learningStatementModel->create($_POST)) {
            $_SESSION['success'] = 'Learning statement created successfully';
        } else {
            $_SESSION['error'] = 'Failed to create learning statement';
        }
        header('Location: /admin/dashboard#learning-statements');
        exit;
    }

    /**
     * Edits an existing learning statement
     * 
     * @param int $id Learning statement ID to edit
     */
    public function editLearningStatement($id) {
        if ($this->learningStatementModel->update($id, $_POST)) {
            $_SESSION['success'] = 'Learning statement updated successfully';
        } else {
            $_SESSION['error'] = 'Failed to update learning statement';
        }
        header('Location: /admin/dashboard#learning-statements');
        exit;
    }

    /**
     * Deletes a learning statement
     * Returns JSON response for AJAX requests
     * 
     * @param int $id Learning statement ID to delete
     * @return string JSON response indicating success or failure
     */
    public function deleteLearningStatement($id) {
        $success = $this->learningStatementModel->delete($id);
        return json_encode(['success' => $success]);
    }

    /**
     * Reorders learning statements
     * Handles AJAX requests to update statement positions
     * Returns JSON response indicating success or failure
     */
    public function reorderLearningStatements() {
        $data = json_decode(file_get_contents('php://input'), true);
        $success = true;
        
        if (isset($data['orders']) && is_array($data['orders'])) {
            foreach ($data['orders'] as $order) {
                if (!$this->learningStatementModel->updatePosition($order['id'], $order['position'])) {
                    $success = false;
                    break;
                }
            }
        } else {
            $success = false;
        }
        
        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
    }

    /**
     * Deletes a note
     * 
     * @param int $id Note ID to delete
     */
    public function deleteNote($id) {
        $note = $this->noteModel->get($id);
        if (!$note) {
            $_SESSION['error'] = 'Note not found';
            header('Location: /admin/dashboard');
            exit;
        }

        $section = $this->sectionModel->get($note['section_id']);
        $course = $this->courseModel->get($section['course_id']);

        if ($this->noteModel->delete($id)) {
            Logger::log("Note deleted: ID $id", 'INFO');
            $_SESSION['success'] = 'Note deleted successfully';
        } else {
            Logger::log("Failed to delete note ID: $id", 'ERROR');
            $_SESSION['error'] = 'Failed to delete note';
        }

        header('Location: /courses/' . $course['id'] . '/sections/' . $section['id'] . '/notes');
        exit;
    }
} 