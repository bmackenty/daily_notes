<?php
namespace App\Utils;

/**
 * SecurityHelper
 * 
 * A utility class providing static methods for common security operations.
 * This class implements various security helpers for:
 * - Input and output sanitization
 * - CSRF token management
 * - Data validation (email, URLs, integers, strings)
 * - File upload validation and sanitization
 * 
 * All methods are static for easy access throughout the application.
 * This class focuses on data sanitization and validation rather than
 * complex security operations (which are handled by the Security class).
 * 
 * Usage:
 * $sanitizedInput = SecurityHelper::sanitizeInput($_POST['user_input']);
 * if (SecurityHelper::validateEmail($email)) {
 *     // Process valid email
 * }
 */
class SecurityHelper {
    /**
     * Sanitizes user input to prevent XSS attacks
     * Handles both string and array inputs recursively
     * Uses htmlspecialchars with ENT_QUOTES for maximum security
     * 
     * @param string|array $input Input to sanitize
     * @return string|array Sanitized input
     */
    public static function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitizes output data before displaying
     * Similar to sanitizeInput but specifically for output
     * Handles both string and array outputs recursively
     * 
     * @param string|array $input Output to sanitize
     * @return string|array Sanitized output
     */
    public static function sanitizeOutput($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeOutput'], $input);
        }
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generates a CSRF token for form protection
     * Creates a new token if one doesn't exist in the session
     * Uses cryptographically secure random bytes
     * 
     * @return string CSRF token
     */
    public static function generateCsrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verifies a CSRF token against the session token
     * Uses hash_equals for timing attack prevention
     * 
     * @param string $token Token to verify
     * @return bool True if token is valid, false otherwise
     */
    public static function verifyCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Regenerates the CSRF token
     * Useful after successful form submission or for increased security
     * 
     * @return string New CSRF token
     */
    public static function regenerateCsrfToken() {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf_token'];
    }

    /**
     * Validates an email address
     * Uses PHP's filter_var with FILTER_VALIDATE_EMAIL
     * 
     * @param string $email Email address to validate
     * @return bool True if email is valid, false otherwise
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validates a file upload
     * Checks for:
     * - Upload errors
     * - File size limits
     * - Allowed MIME types
     * 
     * @param array $file $_FILES array element
     * @param array $allowedTypes Array of allowed MIME types
     * @param int $maxSize Maximum file size in bytes (default: 5MB)
     * @return bool True if file is valid, false otherwise
     */
    public static function validateFileUpload($file, $allowedTypes = [], $maxSize = 5242880) {
        // Check if file was uploaded
        if (!isset($file['error']) || is_array($file['error'])) {
            return false;
        }

        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        // Check file size
        if ($file['size'] > $maxSize) {
            return false;
        }

        // Check file type using finfo
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        
        // Validate against allowed types if specified
        if (!empty($allowedTypes) && !in_array($mimeType, $allowedTypes)) {
            return false;
        }

        return true;
    }

    /**
     * Generates a safe filename for uploaded files
     * - Removes path information
     * - Replaces special characters
     * - Adds unique identifier
     * 
     * @param string $filename Original filename
     * @return string Safe filename with unique identifier
     */
    public static function getSafeFilename($filename) {
        // Remove any path information
        $filename = basename($filename);
        
        // Replace spaces and special characters with underscores
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        // Extract extension and basename
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $basename = pathinfo($filename, PATHINFO_FILENAME);
        
        // Add unique identifier and return
        return $basename . '_' . uniqid() . '.' . $extension;
    }

    /**
     * Validates a URL
     * Uses PHP's filter_var with FILTER_VALIDATE_URL
     * 
     * @param string $url URL to validate
     * @return bool True if URL is valid, false otherwise
     */
    public static function validateUrl($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validates an integer value
     * Supports optional minimum and maximum range checks
     * 
     * @param mixed $value Value to validate
     * @param int|null $min Minimum allowed value
     * @param int|null $max Maximum allowed value
     * @return bool True if value is a valid integer within range, false otherwise
     */
    public static function validateInteger($value, $min = null, $max = null) {
        $options = [];
        if ($min !== null) {
            $options['min_range'] = $min;
        }
        if ($max !== null) {
            $options['max_range'] = $max;
        }
        return filter_var($value, FILTER_VALIDATE_INT, ['options' => $options]) !== false;
    }

    /**
     * Validates a string value
     * Checks for:
     * - String type
     * - Minimum length
     * - Maximum length (if specified)
     * Uses UTF-8 for proper character length calculation
     * 
     * @param mixed $value Value to validate
     * @param int $minLength Minimum allowed length
     * @param int|null $maxLength Maximum allowed length
     * @return bool True if value is a valid string within length constraints, false otherwise
     */
    public static function validateString($value, $minLength = 1, $maxLength = null) {
        if (!is_string($value)) {
            return false;
        }
        
        // Calculate length using UTF-8 encoding
        $length = mb_strlen($value, 'UTF-8');
        
        // Check minimum length
        if ($length < $minLength) {
            return false;
        }
        
        // Check maximum length if specified
        if ($maxLength !== null && $length > $maxLength) {
            return false;
        }
        
        return true;
    }
} 