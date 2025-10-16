<?php
namespace App\Utils;

use App\Utils\DatabaseAdapter;

/**
 * Security
 * 
 * A comprehensive security management class that handles various security aspects of the application.
 * This class implements:
 * - Login attempt tracking and rate limiting
 * - Account lockout system
 * - Password validation and hashing
 * - CSRF protection
 * - Input sanitization
 * - Security settings management
 * 
 * Security Features:
 * - Rate limiting per IP and email
 * - Account lockout after failed attempts
 * - Configurable security settings
 * - Password complexity requirements
 * - Secure password hashing
 * - CSRF token generation
 * - Input sanitization
 * 
 * Database Tables:
 * - login_attempts: Tracks login attempts with IP and email
 * - security_settings: Stores configurable security parameters
 * - users: Extended with security-related columns
 * 
 * Usage:
 * $security = new Security($pdo);
 * if ($security->checkRateLimit($ip, $email)) {
 *     // Process login attempt
 * }
 */
class Security {
    /** @var \PDO Database connection instance */
    private $db;
    
    /** @var DatabaseAdapter Database adapter for driver-specific operations */
    private $dbAdapter;
    
    /** @var int Maximum number of failed login attempts before account lockout */
    private $maxAttempts = 5;
    
    /** @var int Account lockout duration in minutes */
    private $lockoutTime = 15;
    
    /** @var int Rate limit window in seconds */
    private $rateLimitWindow = 60;
    
    /** @var int Maximum number of login attempts per rate limit window */
    private $maxRateLimit = 10;

    /**
     * Constructor - initializes security system
     * Creates necessary database tables and columns
     * Loads security settings from database
     * 
     * @param \PDO $db Database connection instance
     */
    public function __construct($db) {
        $this->db = $db;
        $this->dbAdapter = new DatabaseAdapter($db);
        $this->checkAndCreateTables();
        $this->initializeSettings();
    }

    /**
     * Checks and creates necessary security-related database tables and columns
     * Creates tables if they don't exist:
     * - login_attempts: For tracking login attempts
     * - security_settings: For storing security configuration
     * Adds security columns to users table if they don't exist
     * 
     * @throws \PDOException If database operations fail
     */
    private function checkAndCreateTables() {
        try {
            // Create login_attempts table if it doesn't exist
            if (!$this->dbAdapter->tableExists('login_attempts')) {
                $this->createLoginAttemptsTable();
            }

            // Create security_settings table if it doesn't exist
            if (!$this->dbAdapter->tableExists('security_settings')) {
                $this->createSecuritySettingsTable();
            }

            // Add security columns to users table if they don't exist
            if (!$this->dbAdapter->columnExists('users', 'failed_login_attempts')) {
                $this->addSecurityColumnsToUsers();
            }
        } catch (\PDOException $e) {
            error_log("Failed to check/create security tables: " . $e->getMessage());
        }
    }

    /**
     * Creates the login_attempts table
     * Stores information about login attempts including:
     * - IP address
     * - Email address
     * - Attempt timestamp
     * - Success status
     * 
     * Uses a unique constraint on IP and email combination
     */
    private function createLoginAttemptsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS login_attempts (
            id INTEGER PRIMARY KEY " . $this->dbAdapter->getAutoIncrement() . ",
            ip_address TEXT NOT NULL,
            email TEXT NOT NULL,
            attempt_time " . $this->dbAdapter->getTimestamp() . " DEFAULT CURRENT_TIMESTAMP,
            success INTEGER DEFAULT 0,
            UNIQUE(ip_address, email)
        )";
        $this->db->exec($sql);
    }

    /**
     * Creates the security_settings table and populates it with default values
     * Stores configurable security parameters including:
     * - Maximum login attempts
     * - Lockout duration
     * - Rate limiting settings
     * - Password requirements
     */
    private function createSecuritySettingsTable() {
        // Create table structure
        $sql = "CREATE TABLE IF NOT EXISTS security_settings (
            id INTEGER PRIMARY KEY " . $this->dbAdapter->getAutoIncrement() . ",
            setting_key TEXT NOT NULL UNIQUE,
            setting_value TEXT,
            description TEXT,
            created_at " . $this->dbAdapter->getTimestamp() . " DEFAULT CURRENT_TIMESTAMP,
            updated_at " . $this->dbAdapter->getTimestamp() . " DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->exec($sql);

        // Default security settings
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

        // Insert default settings using INSERT IGNORE to prevent duplicates
        $stmt = $this->db->prepare("
            " . $this->dbAdapter->getInsertIgnore() . " INTO security_settings (setting_key, setting_value, description)
            VALUES (?, ?, ?)
        ");

        foreach ($defaultSettings as $setting) {
            $stmt->execute($setting);
        }
    }

    /**
     * Adds security-related columns to the users table
     * New columns include:
     * - failed_login_attempts: Counter for failed login attempts
     * - last_failed_login: Timestamp of last failed login
     * - account_locked_until: Account lockout expiration
     * - password_reset_token: Token for password reset
     * - password_reset_expires: Password reset token expiration
     * - last_password_change: Last password change timestamp
     */
    private function addSecurityColumnsToUsers() {
        $sql = "ALTER TABLE users 
            ADD COLUMN failed_login_attempts INTEGER DEFAULT 0,
            ADD COLUMN last_failed_login TIMESTAMP NULL,
            ADD COLUMN account_locked_until TIMESTAMP NULL,
            ADD COLUMN password_reset_token TEXT NULL,
            ADD COLUMN password_reset_expires TIMESTAMP NULL,
            ADD COLUMN last_password_change TIMESTAMP NULL";
        $this->db->exec($sql);
    }

    /**
     * Initializes security settings from the database
     * Loads configurable parameters:
     * - Maximum login attempts
     * - Lockout duration
     * - Rate limit window
     * - Maximum rate limit
     * Falls back to default values if settings are not found
     */
    private function initializeSettings() {
        try {
            // Load settings from database if table exists
            $stmt = $this->db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='security_settings'");
            if ($stmt->rowCount() > 0) {
                $stmt = $this->db->query("SELECT setting_key, setting_value FROM security_settings");
                $settings = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
                
                // Update class properties with database values or use defaults
                $this->maxAttempts = $settings['max_login_attempts'] ?? $this->maxAttempts;
                $this->lockoutTime = $settings['lockout_duration'] ?? $this->lockoutTime;
                $this->rateLimitWindow = $settings['rate_limit_window'] ?? $this->rateLimitWindow;
                $this->maxRateLimit = $settings['max_rate_limit'] ?? $this->maxRateLimit;
            }
        } catch (\PDOException $e) {
            // Use default values if settings can't be loaded
            error_log("Security settings not initialized: " . $e->getMessage());
        }
    }

    /**
     * Checks if a login attempt is within rate limits
     * Prevents brute force attacks by limiting attempts per IP
     * 
     * @param string $ip IP address of the attempt
     * @param string $email Email address of the attempt
     * @return bool True if within rate limits, false if rate limit exceeded
     */
    public function checkRateLimit($ip, $email) {
        try {
            $windowStart = date('Y-m-d H:i:s', time() - $this->rateLimitWindow);
            
            // Count attempts within the rate limit window
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
            // Allow attempt if rate limit check fails
            error_log("Rate limit check failed: " . $e->getMessage());
            return true;
        }
    }

    /**
     * Records a login attempt in the database
     * Tracks IP address, email, and success status
     * 
     * @param string $ip IP address of the attempt
     * @param string $email Email address of the attempt
     * @param bool $success Whether the login attempt was successful
     */
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

    /**
     * Checks if an account is currently locked
     * 
     * @param string $email Email address to check
     * @return string|false Returns lockout expiration time if locked, false if not locked
     */
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

            // Check if account is still locked
            if ($user['account_locked_until'] && strtotime($user['account_locked_until']) > time()) {
                return $user['account_locked_until'];
            }

            return false;
        } catch (\PDOException $e) {
            error_log("Account lockout check failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Increments the failed login attempts counter for a user
     * Locks the account if maximum attempts are exceeded
     * 
     * @param string $email Email address of the user
     */
    public function incrementFailedAttempts($email) {
        try {
            // Increment failed attempts counter
            $stmt = $this->db->prepare("
                UPDATE users 
                SET failed_login_attempts = failed_login_attempts + 1,
                    last_failed_login = CURRENT_TIMESTAMP
                WHERE email = ?
            ");
            $stmt->execute([$email]);

            // Check if account should be locked
            $stmt = $this->db->prepare("
                SELECT failed_login_attempts 
                FROM users 
                WHERE email = ?
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // Lock account if maximum attempts exceeded
            if ($user['failed_login_attempts'] >= $this->maxAttempts) {
                $this->lockAccount($email);
            }
        } catch (\PDOException $e) {
            error_log("Failed to increment login attempts: " . $e->getMessage());
        }
    }

    /**
     * Locks a user account for the configured lockout duration
     * 
     * @param string $email Email address of the user to lock
     */
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

    /**
     * Resets the failed login attempts counter for a user
     * Clears lockout status and last failed login timestamp
     * 
     * @param string $email Email address of the user
     */
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

    /**
     * Validates a password against security requirements
     * Checks for:
     * - Minimum length (8 characters)
     * - Uppercase letters
     * - Lowercase letters
     * - Numbers
     * - Special characters
     * 
     * @param string $password Password to validate
     * @return true|string Returns true if valid, error message if invalid
     */
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

    /**
     * Securely hashes a password using PHP's password_hash
     * Uses the default algorithm (currently bcrypt)
     * 
     * @param string $password Password to hash
     * @return string Hashed password
     */
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verifies a plaintext password against a hash
     *
     * @param string $password Plain text password
     * @param string $hash     Hashed password
     * @return bool True if password matches the hash, false otherwise
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Generates a CSRF token for form protection
     * Creates a new token if one doesn't exist in the session
     * Uses cryptographically secure random bytes
     * 
     * @return string CSRF token
     */
    public function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Sanitizes user input to prevent XSS attacks
     * Handles both string and array inputs
     * Uses htmlspecialchars with ENT_QUOTES for maximum security
     * 
     * @param string|array $input Input to sanitize
     * @return string|array Sanitized input
     */
    public function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([$this, 'sanitizeInput'], $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
} 