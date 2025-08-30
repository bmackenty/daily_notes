<?php
/**
 * Cleanup script for expired remember me tokens
 * 
 * This script should be run periodically (e.g., daily via cron) to clean up
 * expired remember me tokens from the database.
 * 
 * Usage:
 * php scripts/cleanup_expired_tokens.php
 * 
 * Or add to crontab:
 * 0 2 * * * /usr/bin/php /path/to/your/app/scripts/cleanup_expired_tokens.php
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define paths
define('ROOT_PATH', dirname(__DIR__));

// Load required files
require_once ROOT_PATH . '/app/Utils/Config.php';
require_once ROOT_PATH . '/app/Utils/RememberMe.php';
require_once ROOT_PATH . '/app/Utils/Logger.php';

try {
    // Get database connection
    $config = \App\Utils\Config::getInstance();
    $db = $config->getDatabase();
    
    if (!$db) {
        throw new Exception("Could not establish database connection");
    }
    
    // Initialize RememberMe utility
    $rememberMe = new \App\Utils\RememberMe($db);
    
    // Clean up expired tokens
    $result = $rememberMe->cleanupExpiredTokens();
    
    if ($result) {
        echo "Successfully cleaned up expired remember me tokens.\n";
        \App\Utils\Logger::log("Expired remember me tokens cleanup completed successfully", 'INFO');
    } else {
        echo "No expired tokens found or cleanup failed.\n";
        \App\Utils\Logger::log("Remember me tokens cleanup: no expired tokens found", 'INFO');
    }
    
} catch (Exception $e) {
    echo "Error during cleanup: " . $e->getMessage() . "\n";
    \App\Utils\Logger::log("Error during remember me tokens cleanup: " . $e->getMessage(), 'ERROR');
    exit(1);
}

echo "Cleanup script completed.\n";
