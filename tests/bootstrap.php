<?php
// Define root path
define('ROOT_PATH', dirname(__DIR__));

// Load required classes
require_once ROOT_PATH . '/app/Utils/Config.php';
require_once ROOT_PATH . '/app/Utils/Logger.php';
require_once ROOT_PATH . '/app/Utils/Security.php';
require_once ROOT_PATH . '/app/Utils/SessionManager.php';

// Load test helpers
require_once __DIR__ . '/helpers/TestHelper.php'; 