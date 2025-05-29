<?php
namespace App\Middleware;

use App\Utils\Config;

/**
 * SecurityHeaders Middleware
 * 
 * This middleware class is responsible for implementing various security headers
 * and enforcing HTTPS in production environments. It helps protect the application
 * against common web vulnerabilities like clickjacking, XSS attacks, and MIME type sniffing.
 */
class SecurityHeaders {
    /** @var Config Configuration instance for accessing environment settings */
    private $config;

    /**
     * Constructor - initializes the configuration instance
     */
    public function __construct() {
        $this->config = Config::getInstance();
    }

    /**
     * Main entry point for the middleware
     * Handles both HTTPS redirection and security headers setup
     */
    public function handle() {
        // Force HTTPS in production
        if ($this->shouldForceHttps()) {
            $this->forceHttps();
        }

        // Set security headers
        $this->setSecurityHeaders();
    }

    /**
     * Determines if HTTPS should be forced based on environment and current request
     * 
     * @return bool True if HTTPS should be forced, false otherwise
     */
    private function shouldForceHttps() {
        return $this->config->get('APP_ENV') === 'production' && 
               !isset($_SERVER['HTTPS']) && 
               $_SERVER['HTTP_X_FORWARDED_PROTO'] !== 'https';
    }

    /**
     * Forces HTTPS by redirecting HTTP requests to HTTPS
     * Uses a 301 permanent redirect for better SEO
     */
    private function forceHttps() {
        if (!headers_sent()) {
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            exit;
        }
    }

    /**
     * Sets various security-related HTTP headers to protect against common web vulnerabilities
     * Includes headers for:
     * - Clickjacking protection (X-Frame-Options)
     * - XSS protection (X-XSS-Protection)
     * - MIME type sniffing prevention (X-Content-Type-Options)
     * - Referrer policy control
     * - Content Security Policy (CSP)
     * - Permissions Policy (formerly Feature-Policy)
     * - HTTP Strict Transport Security (HSTS) in production
     */
    private function setSecurityHeaders() {
        // Prevent clickjacking by only allowing same-origin frames
        header('X-Frame-Options: SAMEORIGIN');
        
        // Enable browser's built-in XSS protection with blocking mode
        header('X-XSS-Protection: 1; mode=block');
        
        // Prevent browsers from MIME-sniffing a response away from declared content-type
        header('X-Content-Type-Options: nosniff');
        
        // Control how much referrer information is included with requests
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Content Security Policy (CSP) to control resource loading
        // Defines allowed sources for scripts, styles, images, fonts, and connections
        $csp = "default-src 'self'; " . // Default fallback for all resource types
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdn.tiny.cloud https://cdn.datatables.net https://code.jquery.com; " . // Allowed script sources
               "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdn.datatables.net https://cdn.tiny.cloud/1/0ej5pnow0o4gxdyaqdyz2zgdu0f4nulp55y17gr52byvbd35/tinymce/6.8.5-39/skins/ui/oxide/skin.min.css https://cdn.tiny.cloud/1/0ej5pnow0o4gxdyaqdyz2zgdu0f4nulp55y17gr52byvbd35/tinymce/6.8.5-39/skins/ui/oxide/content.min.css https://cdn.tiny.cloud/1/0ej5pnow0o4gxdyaqdyz2zgdu0f4nulp55y17gr52byvbd35/tinymce/6.8.5-39/skins/content/default/content.min.css; " . // Allowed style sources
               "img-src 'self' data: https:; " . // Allowed image sources
               "font-src 'self' https://cdn.jsdelivr.net; " . // Allowed font sources
               "connect-src 'self' https://cdn.tiny.cloud; " . // Allowed connection endpoints
               "frame-ancestors 'none';"; // Prevents any site from embedding this site in frames
        header("Content-Security-Policy: " . $csp);
        
        // Permissions Policy to control browser features
        // Currently disables geolocation, microphone, and camera access
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
        
        // HTTP Strict Transport Security (HSTS)
        // Only enabled in production to force HTTPS connections
        // max-age=31536000 = 1 year
        if ($this->config->get('APP_ENV') === 'production') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        }
    }
} 