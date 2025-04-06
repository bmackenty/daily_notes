<?php
namespace App\Middleware;

use App\Utils\Config;

class SecurityHeaders {
    private $config;

    public function __construct() {
        $this->config = Config::getInstance();
    }

    public function handle() {
        // Force HTTPS in production
        if ($this->shouldForceHttps()) {
            $this->forceHttps();
        }

        // Set security headers
        $this->setSecurityHeaders();
    }

    private function shouldForceHttps() {
        return $this->config->get('APP_ENV') === 'production' && 
               !isset($_SERVER['HTTPS']) && 
               $_SERVER['HTTP_X_FORWARDED_PROTO'] !== 'https';
    }

    private function forceHttps() {
        if (!headers_sent()) {
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            exit;
        }
    }

    private function setSecurityHeaders() {
        // Prevent clickjacking
        header('X-Frame-Options: SAMEORIGIN');
        
        // Enable XSS protection
        header('X-XSS-Protection: 1; mode=block');
        
        // Prevent MIME type sniffing
        header('X-Content-Type-Options: nosniff');
        
        // Referrer policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Content Security Policy
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net; " .
               "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; " .
               "img-src 'self' data: https:; " .
               "font-src 'self' https://cdn.jsdelivr.net; " .
               "connect-src 'self'; " .
               "frame-ancestors 'none';";
        header("Content-Security-Policy: " . $csp);
        
        // Permissions Policy
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
        
        // HSTS (HTTP Strict Transport Security)
        if ($this->config->get('APP_ENV') === 'production') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        }
    }
} 