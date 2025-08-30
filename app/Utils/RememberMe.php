<?php
namespace App\Utils;

/**
 * RememberMe
 * 
 * Handles "Remember Me" functionality for persistent user sessions.
 * This class implements:
 * - Secure token generation and validation
 * - Cookie management for remember me tokens
 * - Token expiration handling
 * - Security measures against token theft
 * 
 * Security Features:
 * - Cryptographically secure random tokens
 * - Token hashing for database storage
 * - Configurable token expiration
 * - Automatic cleanup of expired tokens
 * - Secure cookie settings
 */
class RememberMe {
    /** @var \PDO Database connection instance */
    private $db;
    
    /** @var User Model for user operations */
    private $userModel;
    
    /** @var int Token expiration time in seconds (30 days) */
    private const TOKEN_EXPIRY = 2592000;
    
    /** @var string Cookie name for remember me token */
    private const COOKIE_NAME = 'remember_token';
    
    /**
     * Constructor - initializes database connection and user model
     * 
     * @param \PDO $db Database connection instance
     */
    public function __construct($db) {
        $this->db = $db;
        $this->userModel = new \App\Models\User($db);
    }
    
    /**
     * Creates a remember me token for a user
     * Generates a cryptographically secure token and stores it in the database
     * Sets a secure HTTP-only cookie with the token
     * 
     * @param int $userId User ID to create token for
     * @return bool True if token was created successfully, false otherwise
     */
    public function createToken($userId) {
        try {
            // Generate a cryptographically secure random token
            $token = bin2hex(random_bytes(32));
            
            // Hash the token for secure storage
            $tokenHash = hash('sha256', $token);
            
            // Calculate expiration time
            $expiresAt = date('Y-m-d H:i:s', time() + self::TOKEN_EXPIRY);
            
            // Store the hashed token in the database
            if ($this->userModel->createRememberToken($userId, $tokenHash, $expiresAt)) {
                // Set secure HTTP-only cookie
                $this->setRememberCookie($token, $expiresAt);
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            // Log error and return false
            error_log("Error creating remember me token: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Validates a remember me token from cookies
     * Checks if the token exists and hasn't expired
     * Returns user data if token is valid
     * 
     * @return array|false User data if token is valid, false otherwise
     */
    public function validateToken() {
        if (!isset($_COOKIE[self::COOKIE_NAME])) {
            return false;
        }
        
        $token = $_COOKIE[self::COOKIE_NAME];
        
        // Hash the token for database lookup
        $tokenHash = hash('sha256', $token);
        
        // Find user by token
        $user = $this->userModel->findUserByRememberToken($tokenHash);
        
        if ($user) {
            // Token is valid - refresh it for security
            $this->refreshToken($user['id'], $token);
            return $user;
        }
        
        // Invalid or expired token - remove cookie
        $this->removeRememberCookie();
        return false;
    }
    
    /**
     * Refreshes a remember me token
     * Creates a new token and updates the cookie
     * 
     * @param int $userId User ID
     * @param string $oldToken Old token to replace
     * @return bool True if token was refreshed successfully
     */
    public function refreshToken($userId, $oldToken) {
        // Remove old token
        $oldTokenHash = hash('sha256', $oldToken);
        $this->userModel->deleteRememberToken($oldTokenHash);
        
        // Create new token
        return $this->createToken($userId);
    }
    
    /**
     * Removes a remember me token
     * Deletes the token from database and removes the cookie
     * 
     * @param string $token Token to remove
     * @return bool True if token was removed successfully
     */
    public function removeToken($token) {
        if ($token) {
            $tokenHash = hash('sha256', $token);
            $this->userModel->deleteRememberToken($tokenHash);
        }
        
        $this->removeRememberCookie();
        return true;
    }
    
    /**
     * Removes all remember me tokens for a user
     * Useful when user changes password or logs out
     * 
     * @param int $userId User ID
     * @return bool True if tokens were removed successfully
     */
    public function removeAllTokens($userId) {
        $this->removeRememberCookie();
        return $this->userModel->deleteAllRememberTokens($userId);
    }
    
    /**
     * Sets a secure remember me cookie
     * Configures cookie with security best practices
     * 
     * @param string $token Token value to store
     * @param string $expiresAt Expiration time
     */
    private function setRememberCookie($token, $expiresAt) {
        $expires = strtotime($expiresAt);
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
        $httponly = true;
        $samesite = 'Lax';
        
        setcookie(
            self::COOKIE_NAME,
            $token,
            [
                'expires' => $expires,
                'path' => '/',
                'domain' => '',
                'secure' => $secure,
                'httponly' => $httponly,
                'samesite' => $samesite
            ]
        );
    }
    
    /**
     * Removes the remember me cookie
     * Sets cookie to expire in the past
     */
    private function removeRememberCookie() {
        setcookie(
            self::COOKIE_NAME,
            '',
            [
                'expires' => time() - 3600,
                'path' => '/',
                'domain' => '',
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );
    }
    
    /**
     * Cleans up expired remember me tokens
     * Removes tokens that have passed their expiration date
     * 
     * @return bool True if cleanup was successful
     */
    public function cleanupExpiredTokens() {
        return $this->userModel->cleanupExpiredRememberTokens();
    }
}
