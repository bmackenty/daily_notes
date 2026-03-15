<?php
namespace App\Controllers;

use App\Models\PredictedGradeStudent;
use App\Models\PredictedGradeEntry;
use App\Models\PredictedGradeConfig;
use App\Utils\PredictedGradeCalculator;

/**
 * PredictedGradeController
 * Public (no login). Students use a unique code to access their predicted grade data.
 */
class PredictedGradeController {
    /** @var \PDO */
    private $db;

    const SESSION_KEY = 'predicted_grade_student_id';
    const SESSION_CODE_KEY = 'predicted_grade_code';

    public function __construct($db) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = $db;
    }

    /**
     * GET /predicted-grade — show landing or dashboard. Admin can use ?code=XXX to view as student.
     */
    public function index() {
        $studentId = $_SESSION[self::SESSION_KEY] ?? null;
        if (!$studentId && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin' && !empty($_GET['code'])) {
            $studentModel = new PredictedGradeStudent($this->db);
            $student = $studentModel->findByCode(trim($_GET['code']));
            if ($student) {
                $_SESSION[self::SESSION_KEY] = (int) $student['id'];
                $_SESSION[self::SESSION_CODE_KEY] = $student['code'];
                header('Location: /predicted-grade');
                exit;
            }
        }
        if ($studentId) {
            $this->dashboard($studentId);
            return;
        }
        $this->landing();
    }

    private function landing() {
        require ROOT_PATH . '/app/Views/predicted_grade/landing.php';
    }

    private function dashboard($studentId) {
        $studentModel = new PredictedGradeStudent($this->db);
        $student = $studentModel->get($studentId);
        if (!$student) {
            unset($_SESSION[self::SESSION_KEY], $_SESSION[self::SESSION_CODE_KEY]);
            header('Location: /predicted-grade');
            exit;
        }
        $entryModel = new PredictedGradeEntry($this->db);
        $entriesByCategory = $entryModel->getEntriesByStudent($studentId);
        $calculator = new PredictedGradeCalculator($this->db);
        $result = $calculator->calculate($studentId, $student);
        $showCodeAlert = !empty($_SESSION['predicted_grade_show_code']);
        if ($showCodeAlert) {
            unset($_SESSION['predicted_grade_show_code']);
        }
        require ROOT_PATH . '/app/Views/predicted_grade/dashboard.php';
    }

    /**
     * POST /predicted-grade/access — validate code and set session.
     */
    public function access() {
        $code = isset($_POST['code']) ? trim($_POST['code']) : '';
        if ($code === '') {
            $_SESSION['error'] = 'Please enter your code.';
            header('Location: /predicted-grade');
            exit;
        }
        $studentModel = new PredictedGradeStudent($this->db);
        $student = $studentModel->findByCode($code);
        if (!$student) {
            $_SESSION['error'] = 'Code not found. Please check and try again.';
            header('Location: /predicted-grade');
            exit;
        }
        $_SESSION[self::SESSION_KEY] = (int) $student['id'];
        $_SESSION[self::SESSION_CODE_KEY] = $student['code'];
        unset($_SESSION['error']);
        header('Location: /predicted-grade');
        exit;
    }

    /**
     * POST /predicted-grade/start — create new student and show code.
     */
    public function start() {
        $studentModel = new PredictedGradeStudent($this->db);
        $created = $studentModel->create();
        if (!$created) {
            $_SESSION['error'] = 'Could not create your record. Please try again.';
            header('Location: /predicted-grade');
            exit;
        }
        $_SESSION[self::SESSION_KEY] = $created['id'];
        $_SESSION[self::SESSION_CODE_KEY] = $created['code'];
        $_SESSION['predicted_grade_show_code'] = true;
        unset($_SESSION['error']);
        header('Location: /predicted-grade');
        exit;
    }

    /**
     * POST /predicted-grade/entry — add a score entry.
     */
    public function addEntry() {
        $studentId = $_SESSION[self::SESSION_KEY] ?? null;
        if (!$studentId) {
            header('Location: /predicted-grade');
            exit;
        }
        $category = isset($_POST['category']) ? trim($_POST['category']) : '';
        $score = isset($_POST['score']) ? (float) $_POST['score'] : 0;
        $weight = 1.0;
        $label = isset($_POST['label']) ? trim($_POST['label']) : null;
        if ($category === '' || $score < 1 || $score > 7) {
            $_SESSION['error'] = 'Please enter a grade from 1 to 7.';
            header('Location: /predicted-grade');
            exit;
        }
        $entryModel = new PredictedGradeEntry($this->db);
        if ($entryModel->add($studentId, $category, $score, $weight, $label ?: null) === false) {
            $_SESSION['error'] = 'Could not save that score.';
        }
        header('Location: /predicted-grade');
        exit;
    }

    /**
     * POST /predicted-grade/entry/delete — delete an entry.
     */
    public function deleteEntry() {
        $studentId = $_SESSION[self::SESSION_KEY] ?? null;
        if (!$studentId) {
            header('Location: /predicted-grade');
            exit;
        }
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id > 0) {
            $entryModel = new PredictedGradeEntry($this->db);
            $entryModel->delete($id, $studentId);
        }
        header('Location: /predicted-grade');
        exit;
    }

    /**
     * POST /predicted-grade/save-weight — save student's homework & habits weight override.
     */
    public function saveWeight() {
        $studentId = $_SESSION[self::SESSION_KEY] ?? null;
        if (!$studentId) {
            header('Location: /predicted-grade');
            exit;
        }
        $pct = isset($_POST['weight_soft']) ? trim($_POST['weight_soft']) : '';
        $studentModel = new PredictedGradeStudent($this->db);
        if ($pct === '' || $pct === null) {
            $studentModel->setWeightSoftOverride($studentId, null);
        } else {
            $value = (float) $pct / 100;
            if ($value >= 0 && $value <= 0.30) {
                $studentModel->setWeightSoftOverride($studentId, $value);
            }
        }
        header('Location: /predicted-grade');
        exit;
    }

    /**
     * GET /predicted-grade/logout — clear session (stay on tool, show landing).
     */
    public function logout() {
        unset($_SESSION[self::SESSION_KEY], $_SESSION[self::SESSION_CODE_KEY], $_SESSION['predicted_grade_show_code']);
        header('Location: /predicted-grade');
        exit;
    }
}
