<?php
// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define root path
define('ROOT_PATH', __DIR__);

// Load database connection
require_once ROOT_PATH . '/config/database.php';

// Load required classes
require_once ROOT_PATH . '/app/Utils/Logger.php';
require_once ROOT_PATH . '/app/Models/User.php';
require_once ROOT_PATH . '/app/Models/Setting.php';
require_once ROOT_PATH . '/app/Models/Course.php';
require_once ROOT_PATH . '/app/Models/Section.php';
require_once ROOT_PATH . '/app/Models/Note.php';
require_once ROOT_PATH . '/app/Models/AcademicYear.php';
require_once ROOT_PATH . '/app/Models/WeeklyPlan.php';
require_once ROOT_PATH . '/app/Controllers/AuthController.php';
require_once ROOT_PATH . '/app/Controllers/HomeController.php';
require_once ROOT_PATH . '/app/Controllers/AdminController.php';
require_once ROOT_PATH . '/app/Controllers/CourseController.php';

// Load router
require_once ROOT_PATH . '/routes/web.php';