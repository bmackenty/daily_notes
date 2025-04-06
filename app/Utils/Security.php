<?php
namespace App\Utils;

class Security {
    private $db;
    private $maxAttempts = 5;
    private $lockoutTime = 15; // minutes
    private $rateLimitWindow = 60; // seconds
    private $maxRateLimit = 10; // attempts per window

    public function __construct($db) {
        $this->db = $db;
        $this->checkAndCreateTables();
        $this->initializeSettings();
    }

    private function checkAndCreateTables() {
        try {
            // Check if login_attempts table exists
            $stmt = $this->db->query("SHOW TABLES LIKE 'login_attempts'");
            if ($stmt->rowCount() === 0) {
                $this->createLoginAttemptsTable();
            }

            // Check if security_settings table exists
            $stmt = $this->db->query("SHOW TABLES LIKE 'security_settings'");
            if ($stmt->rowCount() === 0) {
                $this->createSecuritySettingsTable();
            }

            // Check if users table has security columns
            $stmt = $this->db->query("SHOW COLUMNS FROM users LIKE 'failed_login_attempts'");
            if ($stmt->rowCount() === 0) {
                $this->addSecurityColumnsToUsers();
            }
        } catch (\PDOException $e) {
            error_log("Failed to check/create security tables: " . $e->getMessage());
        }
    }

    private function createLoginAttemptsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS login_attempts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(45) NOT NULL,
            email VARCHAR(255) NOT NULL,
            attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            success BOOLEAN DEFAULT FALSE,
            INDEX idx_ip_email (ip_address, email),
            INDEX idx_time (attempt_time)
        )";
        $this->db->exec($sql);
    }

    private function createSecuritySettingsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS security_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(50) NOT NULL UNIQUE,
            setting_value TEXT,
            description VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        $this->db->exec($sql);

        // Insert default settings
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

        $stmt = $this->db->prepare("
            INSERT IGNORE INTO security_settings (setting_key, setting_value, description)
            VALUES (?, ?, ?)
        ");

        foreach ($defaultSettings as $setting) {
            $stmt->execute($setting);
        }
    }

    private function addSecurityColumnsToUsers() {
        $sql = "ALTER TABLE users 
            ADD COLUMN failed_login_attempts INT DEFAULT 0,
            ADD COLUMN last_failed_login TIMESTAMP NULL,
            ADD COLUMN account_locked_until TIMESTAMP NULL,
            ADD COLUMN password_reset_token VARCHAR(255) NULL,
            ADD COLUMN password_reset_expires TIMESTAMP NULL,
            ADD COLUMN last_password_change TIMESTAMP NULL";
        $this->db->exec($sql);
    }

    private function initializeSettings() {
        try {
            // Check if security_settings table exists
            $stmt = $this->db->query("SHOW TABLES LIKE 'security_settings'");
            if ($stmt->rowCount() > 0) {
                $stmt = $this->db->query("SELECT setting_key, setting_value FROM security_settings");
                $settings = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
                
                $this->maxAttempts = $settings['max_login_attempts'] ?? $this->maxAttempts;
                $this->lockoutTime = $settings['lockout_duration'] ?? $this->lockoutTime;
                $this->rateLimitWindow = $settings['rate_limit_window'] ?? $this->rateLimitWindow;
                $this->maxRateLimit = $settings['max_rate_limit'] ?? $this->maxRateLimit;
            }
        } catch (\PDOException $e) {
            // If tables don't exist yet, use default values
            error_log("Security settings not initialized: " . $e->getMessage());
        }
    }

    public function checkRateLimit($ip, $email) {
        try {
            $windowStart = date('Y-m-d H:i:s', time() - $this->rateLimitWindow);
            
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as attempts 
                FROM login_attempts 
                WHERE ip_address = ? 
                AND attempt_time > ?
            ");
            $stmt->execute([$ip, $windowStart]);
            $result = $stmt->fetch();
            
            return $result['attempts'] < $this->maxRateLimit;
        } catch (\PDOException $e) {
            // If table doesn't exist yet, allow the attempt
            error_log("Rate limit check failed: " . $e->getMessage());
            return true;
        }
    }

    public function recordLoginAttempt($ip, $email, $success) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO login_attempts (ip_address, email, success)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$ip, $email, $success ? 1 : 0]);
        } catch (\PDOException $e) {
            error_log("Failed to record login attempt: " . $e->getMessage());
        }
    }

    public function checkAccountLockout($email) {
        try {
            $stmt = $this->db->prepare("
                SELECT failed_login_attempts, account_locked_until 
                FROM users 
                WHERE email = ?
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user) return false;

            if ($user['account_locked_until'] && strtotime($user['account_locked_until']) > time()) {
                return $user['account_locked_until'];
            }

            return false;
        } catch (\PDOException $e) {
            error_log("Account lockout check failed: " . $e->getMessage());
            return false;
        }
    }

    public function incrementFailedAttempts($email) {
        try {
            $stmt = $this->db->prepare("
                UPDATE users 
                SET failed_login_attempts = failed_login_attempts + 1,
                    last_failed_login = CURRENT_TIMESTAMP
                WHERE email = ?
            ");
            $stmt->execute([$email]);

            // Check if we should lock the account
            $stmt = $this->db->prepare("
                SELECT failed_login_attempts 
                FROM users 
                WHERE email = ?
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user['failed_login_attempts'] >= $this->maxAttempts) {
                $this->lockAccount($email);
            }
        } catch (\PDOException $e) {
            error_log("Failed to increment login attempts: " . $e->getMessage());
        }
    }

    public function lockAccount($email) {
        try {
            $lockoutUntil = date('Y-m-d H:i:s', time() + ($this->lockoutTime * 60));
            
            $stmt = $this->db->prepare("
                UPDATE users 
                SET account_locked_until = ?
                WHERE email = ?
            ");
            $stmt->execute([$lockoutUntil, $email]);
        } catch (\PDOException $e) {
            error_log("Failed to lock account: " . $e->getMessage());
        }
    }

    public function resetFailedAttempts($email) {
        try {
            $stmt = $this->db->prepare("
                UPDATE users 
                SET failed_login_attempts = 0,
                    account_locked_until = NULL,
                    last_failed_login = NULL
                WHERE email = ?
            ");
            $stmt->execute([$email]);
        } catch (\PDOException $e) {
            error_log("Failed to reset login attempts: " . $e->getMessage());
        }
    }

    public function validatePassword($password) {
        // Minimum 8 characters
        if (strlen($password) < 8) {
            return "Password must be at least 8 characters long";
        }

        // At least one uppercase letter
        if (!preg_match('/[A-Z]/', $password)) {
            return "Password must contain at least one uppercase letter";
        }

        // At least one lowercase letter
        if (!preg_match('/[a-z]/', $password)) {
            return "Password must contain at least one lowercase letter";
        }

        // At least one number
        if (!preg_match('/[0-9]/', $password)) {
            return "Password must contain at least one number";
        }

        // At least one special character
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            return "Password must contain at least one special character";
        }

        return true;
    }
} 