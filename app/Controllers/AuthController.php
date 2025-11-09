<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Setting;
use App\Utils\Logger;
use App\Utils\Security;
use App\Utils\SessionManager;
use App\Utils\SecurityHelper;
use App\Utils\RememberMe;

/**
 * AuthController
 * 
 * Handles all authentication-related operations including login, registration, and logout.
 * Implements security features such as:
 * - CSRF protection
 * - Rate limiting
 * - Account lockout
 * - Password validation
 * - Session management
 * - Input sanitization
 */
class AuthController {
    /** @var User Model for user operations */
    private $userModel;
    
    /** @var Setting Model for application settings */
    private $settingModel;
    
    /** @var Security Utility for security-related operations */
    private $security;
    
    /** @var \PDO Database connection instance */
    private $db;
    
    /** @var SessionManager Utility for session management */
    private $session;
    
    /**
     * Constructor - initializes models and utilities
     * 
     * @param \PDO $db Database connection instance
     */
    public function __construct($db) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = $db;
        $this->userModel = new User($db);
        $this->settingModel = new Setting($db);
        $this->security = new Security($db);
        $this->session = SessionManager::getInstance();
    }
    
    /**
     * Displays the login form
     */
    public function showLogin() {
        require ROOT_PATH . '/app/Views/auth/login.php';
    }
    
    /**
     * Displays the registration form
     * Checks if registration is enabled in settings
     */
    public function showRegister() {
        $registrationEnabled = $this->settingModel->get('registration_enabled') === 'true';
        require ROOT_PATH . '/app/Views/auth/register.php';
    }
    
    /**
     * Handles user login
     * Implements security measures:
     * - CSRF token validation
     * - Input sanitization
     * - Rate limiting
     * - Account lockout
     * - Password verification
     * - Session management
     * - Login attempt logging
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            if (!SecurityHelper::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                $this->session->flash('error', 'Invalid request. Please try again.');
                header('Location: /login');
                exit;
            }

            // Sanitize and validate input
            $email = SecurityHelper::sanitizeInput($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $rememberMe = isset($_POST['remember_me']) && $_POST['remember_me'] === 'on';
            $ip = $_SERVER['REMOTE_ADDR'];

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->session->flash('error', 'Invalid email format.');
                header('Location: /login');
                exit;
            }
            
            // Check rate limiting to prevent brute force attacks
            if (!$this->security->checkRateLimit($ip, $email)) {
                $this->session->flash('error', 'Too many login attempts. Please try again later.');
                header('Location: /login');
                exit;
            }
            
            // Check if account is locked due to too many failed attempts
            if ($lockoutUntil = $this->security->checkAccountLockout($email)) {
                $this->session->flash('error', 'Account is locked until ' . date('Y-m-d H:i:s', strtotime($lockoutUntil)));
                header('Location: /login');
                exit;
            }
            
            // Verify user credentials
            $user = $this->userModel->findByEmail($email);
            
            if ($user && password_verify($password, $user['password'])) {
                // Record successful login and reset failed attempts
                $this->security->recordLoginAttempt($ip, $email, true);
                $this->security->resetFailedAttempts($email);
                
                // Set session variables for authenticated user
                $this->session->set('user_id', $user['id']);
                $this->session->set('user_email', $user['email']);
                $this->session->set('user_role', $user['role']);
                $this->session->set('last_activity', time());
                
                // Handle remember me functionality
                if ($rememberMe) {
                    $rememberMeUtil = new RememberMe($this->db);
                    $rememberMeUtil->createToken($user['id']);
                    $this->session->set('remember_me', true);
                }
                
                Logger::log("User {$user['email']} logged in successfully" . ($rememberMe ? ' with remember me' : ''), 'INFO');
                
                // Redirect based on user role
                if ($user['role'] === 'admin') {
                    header('Location: /admin/dashboard');
                } else {
                    header('Location: /dashboard');
                }
                exit;
            } else {
                // Record failed login attempt and increment counter
                $this->security->recordLoginAttempt($ip, $email, false);
                $this->security->incrementFailedAttempts($email);
                
                Logger::log("Failed login attempt for email: $email from IP: $ip", 'WARNING');
                
                $this->session->flash('error', 'Invalid email or password');
                header('Location: /login');
                exit;
            }
        }
        
        require_once ROOT_PATH . '/app/Views/auth/login.php';
    }
    
    /**
     * Handles user registration
     * Implements security measures:
     * - CSRF token validation
     * - Input sanitization
     * - Password validation
     * - Registration status check
     * - Email uniqueness check
     * - Secure password hashing
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            if (!SecurityHelper::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                $this->session->flash('error', 'Invalid request. Please try again.');
                header('Location: /register');
                exit;
            }

            // Sanitize and validate input
            $name = SecurityHelper::sanitizeInput($_POST['name'] ?? '');
            $email = SecurityHelper::sanitizeInput($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->session->flash('error', 'Invalid email format.');
                header('Location: /register');
                exit;
            }
            
            // Validate password strength
            $passwordValidation = $this->security->validatePassword($password);
            if ($passwordValidation !== true) {
                $this->session->flash('error', $passwordValidation);
                header('Location: /register');
                exit;
            }
            
            // Verify password confirmation
            if ($password !== $confirmPassword) {
                $this->session->flash('error', 'Passwords do not match');
                header('Location: /register');
                exit;
            }
            
            // Check if registration is enabled in settings
            $registrationEnabled = $this->settingModel->get('registration_enabled');
            if (!$registrationEnabled || $registrationEnabled !== '1') {
                $this->session->flash('error', 'Registration is currently disabled');
                header('Location: /register');
                exit;
            }
            
            // Check for existing email
            if ($this->userModel->findByEmail($email)) {
                $this->session->flash('error', 'Email already registered');
                header('Location: /register');
                exit;
            }
            
            // Create new user with hashed password
            $userId = $this->userModel->create([
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => 'user'
            ]);
            
            if ($userId) {
                Logger::log("New user registered: $email", 'INFO');
                $this->session->flash('success', 'Registration successful. Please login.');
                header('Location: /login');
                exit;
            } else {
                $this->session->flash('error', 'Registration failed');
                header('Location: /register');
                exit;
            }
        }
        
        require_once ROOT_PATH . '/app/Views/auth/register.php';
    }
    
    /**
     * Handles user logout
     * Destroys the session and removes remember me tokens
     * Redirects to login page
     */
    public function logout() {
        // Remove remember me tokens if user was logged in
        if ($this->session->has('user_id')) {
            $rememberMeUtil = new RememberMe($this->db);
            $rememberMeUtil->removeAllTokens($this->session->get('user_id'));
        }
        
        $this->session->destroySession();
        header('Location: /login');
        exit;
    }
} 