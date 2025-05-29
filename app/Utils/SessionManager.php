<?php
namespace App\Utils;

/**
 * SessionManager
 * 
 * A singleton class that manages PHP sessions with enhanced security features.
 * This class implements:
 * - Secure session configuration
 * - Session ID regeneration
 * - Session timeout handling
 * - Flash message support
 * - Session data management
 * 
 * Security Features:
 * - Secure cookie settings (HttpOnly, SameSite, Secure in production)
 * - Session ID regeneration every 30 minutes
 * - Session timeout after 1 hour of inactivity
 * - Protection against session fixation
 * - Configurable session parameters
 * 
 * Usage:
 * $session = SessionManager::getInstance();
 * $session->set('user_id', 123);
 * $session->flash('message', 'Login successful');
 * 
 * The singleton pattern ensures consistent session management
 * across the application and prevents multiple session initializations.
 */
class SessionManager {
    /** @var SessionManager|null The single instance of the SessionManager class */
    private static $instance = null;
    
    /** @var Config Configuration instance for session settings */
    private $config;
    
    /** @var Security Security instance for additional security features */
    private $security;
    
    /** @var bool Flag indicating if session has been initialized */
    private $initialized = false;

    /**
     * Private constructor to prevent direct instantiation
     * Initializes configuration and security instances
     * 
     * @throws \RuntimeException If required dependencies are not available
     */
    private function __construct() {
        $this->config = Config::getInstance();
        $this->security = new Security($this->config->getDatabase());
    }

    /**
     * Gets the singleton instance of the SessionManager
     * Creates a new instance if one doesn't exist
     * 
     * @return SessionManager The singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Initializes the session with secure settings
     * Configures session parameters and starts the session if not active
     * Implements security measures:
     * - Secure cookie settings
     * - Session ID regeneration
     * - Session timeout checking
     * 
     * Cookie Settings:
     * - lifetime: 0 (until browser closes)
     * - path: /
     * - domain: From config
     * - secure: true in production
     * - httponly: true
     * - samesite: Lax
     */
    private function initializeSession() {
        if ($this->initialized) {
            return;
        }

        // Only configure session if it's not already active
        if (session_status() === PHP_SESSION_NONE) {
            // Set custom session name for security
            session_name('DAILYNOTES_SESSION');

            // Configure secure session cookie parameters
            $secure = $this->config->get('APP_ENV') === 'production';
            $httponly = true;
            $samesite = 'Lax';

            // Set session cookie parameters with security in mind
            session_set_cookie_params([
                'lifetime' => 0, // Session cookie expires when browser closes
                'path' => '/',
                'domain' => $this->config->get('APP_DOMAIN'),
                'secure' => $secure, // Only send cookie over HTTPS in production
                'httponly' => $httponly, // Prevent JavaScript access to session cookie
                'samesite' => $samesite // Protect against CSRF
            ]);

            // Start the session
            session_start();
        }

        // Implement security measures
        $this->regenerateSessionId();
        $this->checkSessionTimeout();

        $this->initialized = true;
    }

    /**
     * Regenerates the session ID periodically
     * Helps prevent session fixation attacks
     * Regenerates every 30 minutes (1800 seconds)
     * Uses true parameter to delete old session file
     */
    private function regenerateSessionId() {
        // Check if regeneration is needed
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
        } else if (time() - $_SESSION['last_regeneration'] > 1800) {
            // Regenerate session ID and update timestamp
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
    }

    /**
     * Checks for session timeout
     * Destroys session after 1 hour of inactivity
     * Redirects to login page with session expired message
     * Updates last activity timestamp on each check
     */
    private function checkSessionTimeout() {
        $timeout = 3600; // 1 hour timeout in seconds
        
        // Check if session has expired
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
            // Session expired - destroy and redirect
            $this->destroySession();
            header('Location: /login?session=expired');
            exit;
        }
        
        // Update last activity timestamp
        $_SESSION['last_activity'] = time();
    }

    /**
     * Completely destroys the session
     * - Unsets all session variables
     * - Destroys the session cookie
     * - Destroys the session data
     * 
     * Used for logout and session expiration
     */
    public function destroySession() {
        // Clear all session variables
        $_SESSION = array();

        // Remove session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // Destroy the session
        session_destroy();
    }

    /**
     * Sets a session variable
     * Initializes session if not already initialized
     * 
     * @param string $key Session variable name
     * @param mixed $value Value to store
     */
    public function set($key, $value) {
        $this->initializeSession();
        $_SESSION[$key] = $value;
    }

    /**
     * Retrieves a session variable
     * Returns default value if key doesn't exist
     * 
     * @param string $key Session variable name
     * @param mixed $default Default value if key doesn't exist
     * @return mixed Session value or default
     */
    public function get($key, $default = null) {
        $this->initializeSession();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Removes a session variable
     * 
     * @param string $key Session variable name to remove
     */
    public function remove($key) {
        $this->initializeSession();
        unset($_SESSION[$key]);
    }

    /**
     * Checks if a session variable exists
     * 
     * @param string $key Session variable name to check
     * @return bool True if variable exists, false otherwise
     */
    public function has($key) {
        $this->initializeSession();
        return isset($_SESSION[$key]);
    }

    /**
     * Sets a flash message
     * Flash messages are temporary and are removed after first retrieval
     * Useful for one-time notifications (success messages, errors, etc.)
     * 
     * @param string $key Flash message key
     * @param mixed $value Flash message value
     */
    public function flash($key, $value) {
        $this->initializeSession();
        $_SESSION['flash'][$key] = $value;
    }

    /**
     * Retrieves and removes a flash message
     * Returns default value if flash message doesn't exist
     * 
     * @param string $key Flash message key
     * @param mixed $default Default value if flash message doesn't exist
     * @return mixed Flash message value or default
     */
    public function getFlash($key, $default = null) {
        $this->initializeSession();
        $value = $_SESSION['flash'][$key] ?? $default;
        unset($_SESSION['flash'][$key]);
        return $value;
    }
} 