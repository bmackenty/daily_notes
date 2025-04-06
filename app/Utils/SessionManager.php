<?php
namespace App\Utils;

class SessionManager {
    private static $instance = null;
    private $config;
    private $security;
    private $initialized = false;

    private function __construct() {
        $this->config = Config::getInstance();
        $this->security = new Security($this->config->getDatabase());
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function initializeSession() {
        if ($this->initialized) {
            return;
        }

        // Only configure session if it's not already active
        if (session_status() === PHP_SESSION_NONE) {
            // Set session name
            session_name('DAILYNOTES_SESSION');

            // Set secure session cookie parameters
            $secure = $this->config->get('APP_ENV') === 'production';
            $httponly = true;
            $samesite = 'Lax';

            session_set_cookie_params([
                'lifetime' => 0, // Until browser closes
                'path' => '/',
                'domain' => $this->config->get('APP_DOMAIN'),
                'secure' => $secure,
                'httponly' => $httponly,
                'samesite' => $samesite
            ]);

            // Start session
            session_start();
        }

        // Regenerate session ID periodically to prevent session fixation
        $this->regenerateSessionId();

        // Set session timeout
        $this->checkSessionTimeout();

        $this->initialized = true;
    }

    private function regenerateSessionId() {
        // Regenerate session ID every 30 minutes
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
        } else if (time() - $_SESSION['last_regeneration'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
    }

    private function checkSessionTimeout() {
        $timeout = 3600; // 1 hour in seconds
        
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
            // Session has expired
            $this->destroySession();
            header('Location: /login?session=expired');
            exit;
        }
        
        $_SESSION['last_activity'] = time();
    }

    public function destroySession() {
        // Unset all session variables
        $_SESSION = array();

        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // Destroy the session
        session_destroy();
    }

    public function set($key, $value) {
        $this->initializeSession();
        $_SESSION[$key] = $value;
    }

    public function get($key, $default = null) {
        $this->initializeSession();
        return $_SESSION[$key] ?? $default;
    }

    public function remove($key) {
        $this->initializeSession();
        unset($_SESSION[$key]);
    }

    public function has($key) {
        $this->initializeSession();
        return isset($_SESSION[$key]);
    }

    public function flash($key, $value) {
        $this->initializeSession();
        $_SESSION['flash'][$key] = $value;
    }

    public function getFlash($key, $default = null) {
        $this->initializeSession();
        $value = $_SESSION['flash'][$key] ?? $default;
        unset($_SESSION['flash'][$key]);
        return $value;
    }
} 