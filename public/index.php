<?php
// Load bootstrap file
require_once dirname(__DIR__) . '/bootstrap.php';

// Define error log path
define('ERROR_LOG_PATH', ROOT_PATH . '/logs/error.log');

// Create logs directory if it doesn't exist
if (!file_exists(ROOT_PATH . '/logs')) {
    mkdir(ROOT_PATH . '/logs', 0755, true);
}

// Set error log location
ini_set('error_log', ERROR_LOG_PATH);
ini_set('log_errors', 1);

// Load database connection
require_once ROOT_PATH . '/config/database.php';

// Apply security headers
$securityHeaders = new \App\Middleware\SecurityHeaders();
$securityHeaders->handle();

// Load router
require_once ROOT_PATH . '/routes/web.php'; 