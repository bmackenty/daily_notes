<?php
/**
 * Bootstrap: define ROOT_PATH and load all application classes.
 * Used by public/index.php so that routes can use any controller.
 */
define('ROOT_PATH', dirname(__FILE__));

// Load utility classes
require_once ROOT_PATH . '/app/Utils/Config.php';
require_once ROOT_PATH . '/app/Utils/Logger.php';
require_once ROOT_PATH . '/app/Utils/DatabaseAdapter.php';
require_once ROOT_PATH . '/app/Utils/Security.php';
require_once ROOT_PATH . '/app/Utils/SecurityHelper.php';
require_once ROOT_PATH . '/app/Utils/SessionManager.php';
require_once ROOT_PATH . '/app/Utils/RememberMe.php';
require_once ROOT_PATH . '/app/Utils/PredictedGradeCalculator.php';

// Load middleware
require_once ROOT_PATH . '/app/Middleware/SecurityHeaders.php';

// Load model classes
require_once ROOT_PATH . '/app/Models/User.php';
require_once ROOT_PATH . '/app/Models/Setting.php';
require_once ROOT_PATH . '/app/Models/Course.php';
require_once ROOT_PATH . '/app/Models/Section.php';
require_once ROOT_PATH . '/app/Models/Note.php';
require_once ROOT_PATH . '/app/Models/AcademicYear.php';
require_once ROOT_PATH . '/app/Models/WeeklyPlan.php';
require_once ROOT_PATH . '/app/Models/TeacherProfile.php';
require_once ROOT_PATH . '/app/Models/Tag.php';
require_once ROOT_PATH . '/app/Models/LearningStatement.php';
require_once ROOT_PATH . '/app/Models/PredictedGradeStudent.php';
require_once ROOT_PATH . '/app/Models/PredictedGradeEntry.php';
require_once ROOT_PATH . '/app/Models/PredictedGradeConfig.php';

// Load controller classes
require_once ROOT_PATH . '/app/Controllers/AuthController.php';
require_once ROOT_PATH . '/app/Controllers/HomeController.php';
require_once ROOT_PATH . '/app/Controllers/AdminController.php';
require_once ROOT_PATH . '/app/Controllers/CourseController.php';
require_once ROOT_PATH . '/app/Controllers/PredictedGradeController.php';
