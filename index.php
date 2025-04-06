<?php
// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define root path and error log path
define('ROOT_PATH', __DIR__);
define('ERROR_LOG_PATH', ROOT_PATH . '/logs/error.log');

// Create logs directory if it doesn't exist
if (!file_exists(ROOT_PATH . '/logs')) {
    mkdir(ROOT_PATH . '/logs', 0755, true);
}

// Set error log location
ini_set('error_log', ERROR_LOG_PATH);
ini_set('log_errors', 1);

// Load required classes
require_once ROOT_PATH . '/app/Utils/Config.php';
require_once ROOT_PATH . '/app/Utils/Logger.php';
require_once ROOT_PATH . '/app/Utils/Security.php';
require_once ROOT_PATH . '/app/Utils/SessionManager.php';
require_once ROOT_PATH . '/app/Middleware/SecurityHeaders.php';
require_once ROOT_PATH . '/app/Models/User.php';
require_once ROOT_PATH . '/app/Models/Setting.php';
require_once ROOT_PATH . '/app/Models/Course.php';
require_once ROOT_PATH . '/app/Models/Section.php';
require_once ROOT_PATH . '/app/Models/Note.php';
require_once ROOT_PATH . '/app/Models/AcademicYear.php';
require_once ROOT_PATH . '/app/Models/WeeklyPlan.php';
require_once ROOT_PATH . '/app/Models/TeacherProfile.php';
require_once ROOT_PATH . '/app/Controllers/AuthController.php';
require_once ROOT_PATH . '/app/Controllers/HomeController.php';
require_once ROOT_PATH . '/app/Controllers/AdminController.php';
require_once ROOT_PATH . '/app/Controllers/CourseController.php';
require_once ROOT_PATH . '/app/Models/Tag.php';
require_once ROOT_PATH . '/app/Models/LearningStatement.php';

// Load database connection
require_once ROOT_PATH . '/config/database.php';

// Apply security headers
$securityHeaders = new \App\Middleware\SecurityHeaders();
$securityHeaders->handle();

// Load router
require_once ROOT_PATH . '/routes/web.php';