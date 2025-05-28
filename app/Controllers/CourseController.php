<?php
namespace App\Controllers;

use App\Models\Course;
use App\Models\AcademicYear;
use App\Models\WeeklyPlan;
use App\Models\Section;
use App\Models\Note;
use App\Models\Tag;

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

    public function notesByTag($courseId, $tagName) {
        $course = $this->courseModel->get($courseId);
        if (!$course) {
            $_SESSION['error'] = 'Course not found';
            header('Location: /courses');
            exit;
        }

        $tagModel = new Tag($this->db);
        $notes = $tagModel->getNotesByTag($tagName, $courseId);
        $tagCloud = $tagModel->getTagCloud($courseId);

        require ROOT_PATH . '/app/Views/notes_by_tag.php';
    }

    public function singleNote($courseId, $sectionId, $noteId) {
        $course = $this->courseModel->get($courseId);
        $section = $this->sectionModel->get($sectionId);
        $note = $this->noteModel->get($noteId);
        
        if (!$course || !$section || !$note) {
            $_SESSION['error'] = 'Note not found';
            header('Location: /courses/' . $courseId . '/sections/' . $sectionId . '/notes');
            exit;
        }

        // Get settings for delete button visibility
        $settingModel = new \App\Models\Setting($this->db);
        $settings = $settingModel->getAll();
        
        require ROOT_PATH . '/app/Views/single_note.php';
    }

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

    public function viewNote($courseId, $sectionId, $noteId) {
        $course = $this->courseModel->get($courseId);
        $section = $this->sectionModel->get($sectionId);
        $note = $this->noteModel->get($noteId);
        
        if (!$course || !$section || !$note) {
            $_SESSION['error'] = 'Course, section, or note not found';
            header('Location: /');
            exit;
        }
        
        // Get all notes for this section
        $allNotes = $this->noteModel->getAllBySection($sectionId);
        
        // Sort notes by date in descending order (newest first)
        usort($allNotes, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        // Initialize variables
        $previousNote = null;
        $nextNote = null;
        $isCurrentNote = false;
        
        // Find current note position
        foreach ($allNotes as $index => $currentNote) {
            if ($currentNote['id'] == $noteId) {
                // If this is the first note (most current)
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

    public function search($courseId, $sectionId = null) {
        $course = $this->courseModel->get($courseId);
        if (!$course) {
            $_SESSION['error'] = 'Course not found';
            header('Location: /courses');
            exit;
        }

        $section = null;
        if ($sectionId) {
            $section = $this->sectionModel->get($sectionId);
            if (!$section) {
                $_SESSION['error'] = 'Section not found';
                header('Location: /courses/' . $courseId);
                exit;
            }
        }

        $query = $_GET['q'] ?? '';
        $notes = [];
        
        if (!empty($query)) {
            $notes = $this->noteModel->search($query, $sectionId);
            
            // Process content for each note
            foreach ($notes as &$note) {
                // Strip HTML tags for preview but preserve line breaks
                $content = strip_tags($note['content'], '<br><p>');
                $content = str_replace(['<br>', '<br/>', '<br />', '</p>'], "\n", $content);
                $content = str_replace('<p>', '', $content);
                $content = preg_replace('/\n\s+/', "\n", $content); // Remove extra whitespace
                $content = trim($content);
                
                // Get first 200 characters and ensure we don't cut words
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

