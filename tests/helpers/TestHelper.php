<?php

namespace Tests\Helpers;

class TestHelper {
    /**
     * Get a mock PDO connection for testing
     */
    public static function getMockPDO() {
        $pdo = new \PDO('sqlite::memory:');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        
        // Create necessary tables for testing
        self::createTables($pdo);
        
        return $pdo;
    }

    /**
     * Create a test user array
     */
    public static function createTestUser($overrides = []) {
        return array_merge([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'Test123!',
            'role' => 'teacher'
        ], $overrides);
    }

    /**
     * Create a test course array
     */
    public static function createTestCourse($overrides = []) {
        return array_merge([
            'name' => 'Test Course',
            'description' => 'Test Course Description',
            'code' => 'TEST101'
        ], $overrides);
    }

    /**
     * Create necessary tables for testing
     */
    private static function createTables($pdo) {
        // Create login_attempts table
        $pdo->exec("CREATE TABLE IF NOT EXISTS login_attempts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ip_address TEXT NOT NULL,
            email TEXT NOT NULL,
            attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            success INTEGER DEFAULT 0,
            UNIQUE(ip_address, email)
        )");

        // Create security_settings table
        $pdo->exec("CREATE TABLE IF NOT EXISTS security_settings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            setting_key TEXT NOT NULL UNIQUE,
            setting_value TEXT,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // Create users table
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            role TEXT DEFAULT 'user',
            failed_login_attempts INTEGER DEFAULT 0,
            last_failed_login TIMESTAMP NULL,
            account_locked_until TIMESTAMP NULL,
            password_reset_token TEXT NULL,
            password_reset_expires TIMESTAMP NULL,
            last_password_change TIMESTAMP NULL
        )");

        // Insert default security settings
        $defaultSettings = [
            ['max_login_attempts', '5', 'Maximum number of failed login attempts before account lockout'],
            ['lockout_duration', '15', 'Account lockout duration in minutes'],
            ['rate_limit_window', '60', 'Rate limit window in seconds'],
            ['max_rate_limit', '10', 'Maximum number of login attempts per rate limit window'],
            ['password_min_length', '8', 'Minimum password length'],
            ['password_require_uppercase', '1', 'Password must contain uppercase letters'],
            ['password_require_lowercase', '1', 'Password must contain lowercase letters'],
            ['password_require_numbers', '1', 'Password must contain numbers'],
            ['password_require_special', '1', 'Password must contain special characters']
        ];

        $stmt = $pdo->prepare("
            INSERT OR IGNORE INTO security_settings (setting_key, setting_value, description)
            VALUES (?, ?, ?)
        ");

        foreach ($defaultSettings as $setting) {
            $stmt->execute($setting);
        }
    }
} 