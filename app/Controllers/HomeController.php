<?php
namespace App\Controllers;

use App\Models\Course;
use App\Models\Section;
use App\Models\Note;

/**
 * HomeController
 * 
 * Handles the main public-facing pages of the application including:
 * - Home page with course overview
 * - Course syllabus display
 * - Teacher profile pages
 * 
 * This controller serves as the entry point for users to view course information
 * and teacher profiles. It provides a hierarchical view of courses, their sections,
 * and associated notes.
 */
class HomeController {
    /** @var \PDO Database connection instance */
    private $pdo;
    
    /** @var Course Model for course operations */
    private $courseModel;
    
    /** @var Section Model for section operations */
    private $sectionModel;
    
    /** @var Note Model for note operations */
    private $noteModel;

    /**
     * Constructor - initializes models and database connection
     * 
     * @param \PDO $pdo Database connection instance
     */
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->courseModel = new Course($pdo);
        $this->sectionModel = new Section($pdo);
        $this->noteModel = new Note($pdo);
    }

    /**
     * Displays the home page with a complete overview of all courses
     * Shows hierarchical structure of:
     * - All courses
     * - Sections within each course
     * - Notes within each section (filtered by academic year for students)
     * 
     * This method builds a nested data structure that represents the entire
     * course hierarchy for display on the home page.
     */
    public function index() {
        // Get all courses
        $courses = $this->courseModel->getAll();
        $sections = [];
        $notes = [];
        
        // Determine if user is admin
        $isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
        
        // Get active academic year for filtering
        $academicYearModel = new \App\Models\AcademicYear($this->pdo);
        $activeYear = $academicYearModel->getActive();
        
        // Build hierarchical data structure
        foreach ($courses as $course) {
            // Get all sections for each course
            $sections[$course['id']] = $this->sectionModel->getAllByCourse($course['id']);
            
            // Get all notes for each section
            foreach ($sections[$course['id']] as $section) {
                if ($isAdmin) {
                    // Admins see all notes
                    $notes[$section['id']] = $this->noteModel->getAllBySection($section['id']);
                } else {
                    // Students only see notes from active academic year
                    $notes[$section['id']] = $this->noteModel->getAllBySection($section['id'], $activeYear ? $activeYear['id'] : null);
                }
            }
        }
        
        require ROOT_PATH . '/app/Views/home.php';
    }

    /**
     * Displays the syllabus for a specific course
     * Shows course details and its sections
     * 
     * @param int $courseId ID of the course to display syllabus for
     * @throws \Exception If course is not found, redirects to home page
     */
    public function syllabus($courseId) {
        // Verify course exists
        $course = $this->courseModel->get($courseId);
        if (!$course) {
            header('Location: /');
            exit;
        }
        
        // Get all sections for the course
        $sections = $this->sectionModel->getAllByCourse($courseId);
        require ROOT_PATH . '/app/Views/syllabus.php';
    }

    /**
     * Displays a teacher's profile page
     * Shows teacher information and their associated courses
     * 
     * @param int $id ID of the teacher profile to display
     * @throws \Exception If profile is not found, redirects to home page
     */
    public function teacherProfile($id) {
        // Get teacher profile
        $teacherProfileModel = new \App\Models\TeacherProfile($this->pdo);
        $profile = $teacherProfileModel->get($id);
        
        // Verify profile exists
        if (!$profile) {
            header('Location: /');
            exit;
        }
        
        // Get all courses taught by this teacher
        $courses = $this->courseModel->getCoursesByTeacherProfileId($id);
        
        require ROOT_PATH . '/app/Views/teacher_profile.php';
    }
} 