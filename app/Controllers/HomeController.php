<?php
namespace App\Controllers;

use App\Models\Course;
use App\Models\Section;
use App\Models\Note;

class HomeController {
    private $courseModel;
    private $sectionModel;
    private $noteModel;

    public function __construct() {
        global $pdo;
        $this->courseModel = new Course($pdo);
        $this->sectionModel = new Section($pdo);
        $this->noteModel = new Note($pdo);
    }

    public function index() {
        $courses = $this->courseModel->getAll();
        $sections = [];
        $notes = [];
        
        // Get sections for each course
        foreach ($courses as $course) {
            $sections[$course['id']] = $this->sectionModel->getAllByCourse($course['id']);
            
            // Get notes for each section
            foreach ($sections[$course['id']] as $section) {
                $notes[$section['id']] = $this->noteModel->getAllBySection($section['id']);
            }
        }
        
        require ROOT_PATH . '/app/Views/home.php';
    }

    public function syllabus($courseId) {
        $course = $this->courseModel->get($courseId);
        if (!$course) {
            header('Location: /');
            exit;
        }
        
        $sections = $this->sectionModel->getAllByCourse($courseId);
        require ROOT_PATH . '/app/Views/syllabus.php';
    }
} 