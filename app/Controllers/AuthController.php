<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Setting;
use App\Utils\Logger;

class AuthController {
    private $userModel;
    private $settingModel;
    private $db;
    
    public function __construct($db) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = $db;
        $this->userModel = new User($db);
        $this->settingModel = new Setting($db);
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
            $email = $_POST['email'];
            Logger::log("Login attempt for email: $email");
            
            $user = $this->userModel->findByEmail($email);
            
            if ($user && password_verify($_POST['password'], $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                Logger::log("Successful login for user: $email", 'SUCCESS');
                header('Location: /dashboard');
                exit;
            }
            
            Logger::log("Failed login attempt for email: $email", 'WARNING');
            $_SESSION['error'] = 'Invalid credentials';
            header('Location: /login');
            exit;
        }
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->settingModel->get('registration_enabled') !== 'true') {
                Logger::log("Registration attempt when disabled", 'WARNING');
                $_SESSION['error'] = 'Registration is currently disabled';
                header('Location: /login');
                exit;
            }
            
            $email = $_POST['email'];
            Logger::log("Registration attempt for email: $email");
            
            if ($_POST['password'] !== $_POST['confirm_password']) {
                Logger::log("Registration failed - passwords don't match for email: $email", 'WARNING');
                $_SESSION['error'] = 'Passwords do not match';
                header('Location: /register');
                exit;
            }

            if ($this->userModel->findByEmail($email)) {
                Logger::log("Registration failed - email already exists: $email", 'WARNING');
                $_SESSION['error'] = 'Email already exists';
                header('Location: /register');
                exit;
            }
            
            if ($this->userModel->create($_POST)) {
                Logger::log("Successful registration for email: $email", 'SUCCESS');
                $_SESSION['success'] = 'Registration successful. Please login.';
                header('Location: /login');
                exit;
            }
            
            Logger::log("Registration failed - database error for email: $email", 'ERROR');
            $_SESSION['error'] = 'Registration failed';
            header('Location: /register');
            exit;
        }
    }
    
    public function logout() {
        Logger::log("User logged out: " . ($_SESSION['user_id'] ?? 'unknown'));
        session_destroy();
        header('Location: /login');
        exit;
    }
} 