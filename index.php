<?php
/**
 * Daily Notes Application - Main Entry Point
 * 
 * This file serves as the main entry point for the application. It handles:
 * 1. Error reporting configuration
 * 2. Essential constant definitions
 * 3. Class autoloading
 * 4. Security header application
 * 5. Database connection initialization
 * 6. Route handling
 */

// Error reporting configuration
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');

// Define essential paths
define('ROOT_PATH', __DIR__);
define('ERROR_LOG_PATH', ROOT_PATH . '/logs/error.log');

// Create logs directory if it doesn't exist
if (!file_exists(ROOT_PATH . '/logs')) {
    mkdir(ROOT_PATH . '/logs', 0777, true);
}

/**
 * Load all required classes
 * 
 * The application follows a modular structure with:
 * - Utils: Helper classes and utilities
 * - Models: Database interaction classes
 * - Controllers: Request handling classes
 * - Middleware: Request/response processing classes
 */

// Load utility classes
require_once ROOT_PATH . '/app/Utils/Config.php';           // Configuration management
require_once ROOT_PATH . '/app/Utils/Logger.php';           // Logging functionality
require_once ROOT_PATH . '/app/Utils/DatabaseAdapter.php';  // Database connection handling
require_once ROOT_PATH . '/app/Utils/Security.php';         // Security utilities
require_once ROOT_PATH . '/app/Utils/SecurityHelper.php';   // Additional security helpers
require_once ROOT_PATH . '/app/Utils/SessionManager.php';   // Session management
require_once ROOT_PATH . '/app/Utils/RememberMe.php';      // Remember me functionality

// Load middleware
require_once ROOT_PATH . '/app/Middleware/SecurityHeaders.php';  // Security headers management

// Load model classes
require_once ROOT_PATH . '/app/Models/User.php';            // User management
require_once ROOT_PATH . '/app/Models/Setting.php';         // Application settings
require_once ROOT_PATH . '/app/Models/Course.php';          // Course management
require_once ROOT_PATH . '/app/Models/Section.php';         // Section management
require_once ROOT_PATH . '/app/Models/Note.php';            // Note management
require_once ROOT_PATH . '/app/Models/AcademicYear.php';    // Academic year management
require_once ROOT_PATH . '/app/Models/WeeklyPlan.php';      // Weekly planning
require_once ROOT_PATH . '/app/Models/TeacherProfile.php';  // Teacher profiles
require_once ROOT_PATH . '/app/Models/Tag.php';             // Tag management
require_once ROOT_PATH . '/app/Models/LearningStatement.php'; // Learning statements

// Load controller classes
require_once ROOT_PATH . '/app/Controllers/AuthController.php';    // Authentication handling
require_once ROOT_PATH . '/app/Controllers/HomeController.php';    // Home page handling
require_once ROOT_PATH . '/app/Controllers/AdminController.php';   // Admin functionality
require_once ROOT_PATH . '/app/Controllers/CourseController.php';  // Course management

// Load database configuration and establish connection
require_once ROOT_PATH . '/config/database.php';

// Apply security headers to all responses
// This helps protect against common web vulnerabilities
$securityHeaders = new \App\Middleware\SecurityHeaders();
$securityHeaders->handle();

// Load and execute the router
// This will handle all incoming requests and route them to appropriate controllers
require_once ROOT_PATH . '/routes/web.php';