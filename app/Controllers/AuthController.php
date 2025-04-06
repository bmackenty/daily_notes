<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Setting;
use App\Utils\Logger;
use App\Utils\Security;
use App\Utils\SessionManager;

class AuthController {
    private $userModel;
    private $settingModel;
    private $security;
    private $db;
    private $session;
    
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
    
    public function showLogin() {
        require ROOT_PATH . '/app/Views/auth/login.php';
    }
    
    public function showRegister() {
        $registrationEnabled = $this->settingModel->get('registration_enabled') === 'true';
        require ROOT_PATH . '/app/Views/auth/register.php';
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $ip = $_SERVER['REMOTE_ADDR'];
            
            // Check rate limit
            if (!$this->security->checkRateLimit($ip, $email)) {
                $this->session->flash('error', 'Too many login attempts. Please try again later.');
                header('Location: /login');
                exit;
            }
            
            // Check account lockout
            if ($lockoutUntil = $this->security->checkAccountLockout($email)) {
                $this->session->flash('error', 'Account is locked until ' . date('Y-m-d H:i:s', strtotime($lockoutUntil)));
                header('Location: /login');
                exit;
            }
            
            $user = $this->userModel->findByEmail($email);
            
            if ($user && password_verify($password, $user['password'])) {
                // Record successful login
                $this->security->recordLoginAttempt($ip, $email, true);
                $this->security->resetFailedAttempts($email);
                
                // Set session variables
                $this->session->set('user_id', $user['id']);
                $this->session->set('user_email', $user['email']);
                $this->session->set('user_role', $user['role']);
                $this->session->set('last_activity', time());
                
                Logger::log("User {$user['email']} logged in successfully", 'INFO');
                
                if ($user['role'] === 'admin') {
                    header('Location: /admin/dashboard');
                } else {
                    header('Location: /dashboard');
                }
                exit;
            } else {
                // Record failed login
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
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            // Validate password
            $passwordValidation = $this->security->validatePassword($password);
            if ($passwordValidation !== true) {
                $this->session->flash('error', $passwordValidation);
                header('Location: /register');
                exit;
            }
            
            if ($password !== $confirmPassword) {
                $this->session->flash('error', 'Passwords do not match');
                header('Location: /register');
                exit;
            }
            
            // Check if registration is enabled
            $setting = $this->settingModel->findByKey('registration_enabled');
            if (!$setting || $setting['value'] !== '1') {
                $this->session->flash('error', 'Registration is currently disabled');
                header('Location: /register');
                exit;
            }
            
            // Check if email already exists
            if ($this->userModel->findByEmail($email)) {
                $this->session->flash('error', 'Email already registered');
                header('Location: /register');
                exit;
            }
            
            // Create user
            $userId = $this->userModel->create([
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
    
    public function logout() {
        $this->session->destroySession();
        header('Location: /login');
        exit;
    }
} 