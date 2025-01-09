<?php
namespace App\Utils;

class Logger {
    private static $logFile;
    
    public static function init() {
        self::$logFile = ROOT_PATH . '/logs/app.log';
        
        // Create logs directory if it doesn't exist
        if (!file_exists(ROOT_PATH . '/logs')) {
            mkdir(ROOT_PATH . '/logs', 0755, true);
        }
        
        // Create log file if it doesn't exist
        if (!file_exists(self::$logFile)) {
            touch(self::$logFile);
            chmod(self::$logFile, 0644);
        }
    }
    
    public static function log($message, $type = 'INFO') {
        if (!self::$logFile) {
            self::init();
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $logMessage = "[$timestamp] [$type] [IP: $ip] $message" . PHP_EOL;
        
        error_log($logMessage, 3, self::$logFile);
    }
} 