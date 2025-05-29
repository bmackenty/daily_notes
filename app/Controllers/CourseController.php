<?php
namespace App\Controllers;

use App\Models\Course;
use App\Models\AcademicYear;
use App\Models\WeeklyPlan;
use App\Models\Section;
use App\Models\Note;
use App\Models\Tag;

/**
 * CourseController
 * 
 * Handles all course-related operations including:
 * - Course listing and details
 * - Section management
 * - Note management and viewing
 * - Weekly and yearly planning
 * - Note searching and tagging
 * 
 * This controller serves as the main interface for users to interact with courses,
 * their sections, and associated notes. It provides functionality for both viewing
 * and managing course content.
 */
class CourseController {
    /** @var \PDO Database connection instance */
    private $db;
    
    /** @var Course Model for course operations */
    private $courseModel;
    
    /** @var Section Model for section operations */
    private $sectionModel;
    
    /** @var Note Model for note operations */
    private $noteModel;
    
    /** @var WeeklyPlan Model for weekly plan operations */
    private $weeklyPlanModel;

    /**
     * Constructor - initializes models and database connection
     * 
     * @param \PDO $db Database connection instance
     */
    public function __construct($db) {
        $this->db = $db;
        $this->courseModel = new Course($db);
        $this->sectionModel = new Section($db);
        $this->noteModel = new Note($db);
        $this->weeklyPlanModel = new WeeklyPlan($db);
    }

    /**
     * Displays weekly plans for a specific course
     * Includes academic week information and plan details
     * 
     * @param int $courseId ID of the course to display plans for
     */
    public function weeklyPlans($courseId) {
        // Verify course exists
        $course = $this->courseModel->get($courseId);
        if (!$course) {
            $_SESSION['error'] = 'Course not found';
            header('Location: /');
            exit;
        }

        // Get active academic year and its weeks
        $academicYearModel = new AcademicYear($this->db);
        $activeYear = $academicYearModel->getActive();
        
        // Get weekly plans for the course
        $weeklyPlanModel = new WeeklyPlan($this->db);
        $plans = $weeklyPlanModel->getByCourse($courseId);

        // Enrich plan data with academic week information
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

    /**
     * Displays all notes for a specific section within a course
     * Notes are sorted by date in descending order (newest first)
     * 
     * @param int $courseId ID of the course
     * @param int $sectionId ID of the section
     */
    public function sectionNotes($courseId, $sectionId) {
        // Verify course and section exist
        $course = $this->courseModel->get($courseId);
        $section = $this->sectionModel->get($sectionId);
        
        if (!$course || !$section) {
            $_SESSION['error'] = 'Course or section not found';
            header('Location: /');
            exit;
        }

        // Get and sort notes
        $notes = $this->noteModel->getAllBySection($sectionId);
        usort($notes, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        require ROOT_PATH . '/app/Views/section_notes.php';
    }

    /**
     * Displays the main course listing page
     * Shows all courses and their associated sections
     */
    public function index() {
        $courses = $this->courseModel->getAll();
        $sections = [];
        
        // Get sections for each course
        foreach ($courses as $course) {
            $sections[$course['id']] = $this->sectionModel->getAllByCourse($course['id']);
        }
        
        require ROOT_PATH . '/app/Views/courses/index.php';
    }

    /**
     * Displays yearly plans for a course
     * Similar to weeklyPlans but organized by academic year
     * 
     * @param int $courseId ID of the course to display plans for
     */
    public function yearlyPlans($courseId) {
        // Verify course exists
        $course = $this->courseModel->get($courseId);
        if (!$course) {
            $_SESSION['error'] = 'Course not found';
            header('Location: /');
            exit;
        }

        // Get active academic year and plans
        $academicYearModel = new AcademicYear($this->db);
        $activeYear = $academicYearModel->getActive();
        $plans = [];
        
        if ($activeYear) {
            // Get weeks and plans for the active year
            $weeks = $academicYearModel->getWeeks($activeYear['id']);
            $plans = $this->weeklyPlanModel->getAllByCourse($courseId, $activeYear['id']);
            
            // Enrich plan data with week information
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

    /**
     * Displays notes filtered by tag for a specific course
     * Includes tag cloud for navigation
     * 
     * @param int $courseId ID of the course
     * @param string $tagName Name of the tag to filter by
     */
    public function notesByTag($courseId, $tagName) {
        // Verify course exists
        $course = $this->courseModel->get($courseId);
        if (!$course) {
            $_SESSION['error'] = 'Course not found';
            header('Location: /courses');
            exit;
        }

        // Get notes and tag cloud
        $tagModel = new Tag($this->db);
        $notes = $tagModel->getNotesByTag($tagName, $courseId);
        $tagCloud = $tagModel->getTagCloud($courseId);

        require ROOT_PATH . '/app/Views/notes_by_tag.php';
    }

    /**
     * Displays a single note with its details
     * Includes course and section context
     * 
     * @param int $courseId ID of the course
     * @param int $sectionId ID of the section
     * @param int $noteId ID of the note to display
     */
    public function singleNote($courseId, $sectionId, $noteId) {
        // Verify course, section, and note exist
        $course = $this->courseModel->get($courseId);
        $section = $this->sectionModel->get($sectionId);
        $note = $this->noteModel->get($noteId);
        
        if (!$course || !$section || !$note) {
            $_SESSION['error'] = 'Note not found';
            header('Location: /courses/' . $courseId . '/sections/' . $sectionId . '/notes');
            exit;
        }

        // Get settings for UI customization (e.g., delete button visibility)
        $settingModel = new \App\Models\Setting($this->db);
        $settings = $settingModel->getAll();
        
        require ROOT_PATH . '/app/Views/single_note.php';
    }

    /**
     * Displays detailed information about a specific course
     * Shows course details and its sections
     * 
     * @param int $courseId ID of the course to display
     */
    public function show($courseId) {
        $course = $this->courseModel->get($courseId);
        
        if (!$course) {
            $_SESSION['error'] = 'Course not found';
            header('Location: /courses');
            exit;
        }
        
        $sections = $this->sectionModel->getAllByCourse($courseId);
        require ROOT_PATH . '/app/Views/courses/show.php';
    }

    /**
     * Retrieves navigation information for a note
     * Returns previous and next notes in chronological order
     * 
     * @param int $courseId ID of the course
     * @param int $sectionId ID of the section
     * @param int $noteId ID of the current note
     * @return array Navigation data including previous and next notes
     */
    public function viewNote($courseId, $sectionId, $noteId) {
        // Verify course, section, and note exist
        $course = $this->courseModel->get($courseId);
        $section = $this->sectionModel->get($sectionId);
        $note = $this->noteModel->get($noteId);
        
        if (!$course || !$section || !$note) {
            $_SESSION['error'] = 'Course, section, or note not found';
            header('Location: /');
            exit;
        }
        
        // Get all notes for navigation
        $allNotes = $this->noteModel->getAllBySection($sectionId);
        
        // Sort notes by date (newest first)
        usort($allNotes, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        // Initialize navigation variables
        $previousNote = null;
        $nextNote = null;
        $isCurrentNote = false;
        
        // Find current note position and set navigation links
        foreach ($allNotes as $index => $currentNote) {
            if ($currentNote['id'] == $noteId) {
                if ($index == 0) {
                    $isCurrentNote = true;
                    if (isset($allNotes[$index + 1])) {
                        $previousNote = $allNotes[$index + 1];
                    }
                } else {
                    if (isset($allNotes[$index + 1])) {
                        $previousNote = $allNotes[$index + 1];
                    }
                    if (isset($allNotes[$index - 1])) {
                        $nextNote = $allNotes[$index - 1];
                    }
                }
                break;
            }
        }
        
        return [
            'previousNote' => $previousNote,
            'nextNote' => $nextNote,
            'isCurrentNote' => $isCurrentNote,
        ];
    }

    /**
     * Searches notes within a course or section
     * Provides content previews and handles HTML formatting
     * 
     * @param int $courseId ID of the course to search in
     * @param int|null $sectionId Optional section ID to limit search
     */
    public function search($courseId, $sectionId = null) {
        // Verify course exists
        $course = $this->courseModel->get($courseId);
        if (!$course) {
            $_SESSION['error'] = 'Course not found';
            header('Location: /courses');
            exit;
        }

        // Verify section if provided
        $section = null;
        if ($sectionId) {
            $section = $this->sectionModel->get($sectionId);
            if (!$section) {
                $_SESSION['error'] = 'Section not found';
                header('Location: /courses/' . $courseId);
                exit;
            }
        }

        // Get search query and perform search
        $query = $_GET['q'] ?? '';
        $notes = [];
        
        if (!empty($query)) {
            $notes = $this->noteModel->search($query, $sectionId);
            
            // Process and format note content for preview
            foreach ($notes as &$note) {
                // Clean HTML while preserving line breaks
                $content = strip_tags($note['content'], '<br><p>');
                $content = str_replace(['<br>', '<br/>', '<br />', '</p>'], "\n", $content);
                $content = str_replace('<p>', '', $content);
                $content = preg_replace('/\n\s+/', "\n", $content); // Remove extra whitespace
                $content = trim($content);
                
                // Create preview (first 200 characters)
                if (strlen($content) > 200) {
                    $content = substr($content, 0, 200);
                    $pos = strrpos($content, ' ');
                    if ($pos !== false) {
                        $content = substr($content, 0, $pos);
                    }
                    $content .= '...';
                }
                
                $note['content'] = $content;
            }
        }

        $sections = $this->sectionModel->getAllByCourse($courseId);
        require ROOT_PATH . '/app/Views/search_results.php';
    }
} 

