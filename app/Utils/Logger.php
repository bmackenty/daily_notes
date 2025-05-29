<?php
namespace App\Utils;

/**
 * Logger
 * 
 * A static utility class that provides application-wide logging functionality.
 * This class handles:
 * - Log file initialization and management
 * - Timestamp-based log entries
 * - IP address tracking
 * - Different log types (INFO, ERROR, etc.)
 * - Automatic log directory and file creation
 * 
 * Security features:
 * - Proper file permissions (0755 for directory, 0644 for file)
 * - IP address logging for security tracking
 * - Timestamp-based entries for audit trails
 * 
 * Usage:
 * Logger::init();  // Initialize logger (optional, called automatically)
 * Logger::log('User logged in', 'INFO');
 * Logger::log('Database connection failed', 'ERROR');
 * 
 * Log format:
 * [TIMESTAMP] [TYPE] [IP: xxx.xxx.xxx.xxx] Message
 * Example: [2024-03-14 15:30:45] [INFO] [IP: 192.168.1.1] User logged in
 */
class Logger {
    /** @var string|null Path to the log file */
    private static $logFile;
    
    /**
     * Initializes the logger by setting up the log file and directory
     * Creates the logs directory and log file if they don't exist
     * Sets appropriate file permissions for security
     * 
     * Directory permissions (0755):
     * - Owner: read, write, execute
     * - Group: read, execute
     * - Others: read, execute
     * 
     * File permissions (0644):
     * - Owner: read, write
     * - Group: read
     * - Others: read
     * 
     * @throws \RuntimeException If ROOT_PATH constant is not defined
     */
    public static function init() {
        // Define log file path
        self::$logFile = ROOT_PATH . '/logs/app.log';
        
        // Create logs directory with secure permissions if it doesn't exist
        if (!file_exists(ROOT_PATH . '/logs')) {
            mkdir(ROOT_PATH . '/logs', 0755, true);
        }
        
        // Create log file with secure permissions if it doesn't exist
        if (!file_exists(self::$logFile)) {
            touch(self::$logFile);
            chmod(self::$logFile, 0644);
        }
    }
    
    /**
     * Writes a log entry to the log file
     * Automatically initializes the logger if not already initialized
     * Includes timestamp, log type, and IP address in each entry
     * 
     * Log types:
     * - INFO: General information
     * - ERROR: Error messages
     * - WARNING: Warning messages
     * - DEBUG: Debug information
     * - CUSTOM: Any other type specified
     * 
     * @param string $message The message to log
     * @param string $type The type of log entry (default: 'INFO')
     * @return void
     */
    public static function log($message, $type = 'INFO') {
        // Ensure logger is initialized
        if (!self::$logFile) {
            self::init();
        }
        
        // Generate timestamp and get client IP
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        // Format log message with timestamp, type, IP, and message
        $logMessage = "[$timestamp] [$type] [IP: $ip] $message" . PHP_EOL;
        
        // Write to log file using error_log for atomic writes
        error_log($logMessage, 3, self::$logFile);
    }
} 