<?php
namespace App\Utils;

class SecurityHelper {
    public static function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    public static function sanitizeOutput($output) {
        if (is_array($output)) {
            return array_map([self::class, 'sanitizeOutput'], $output);
        }
        return htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
    }

    public static function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateCsrfToken($token) {
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

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

        // Check file type
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        
        if (!empty($allowedTypes) && !in_array($mimeType, $allowedTypes)) {
            return false;
        }

        return true;
    }

    public static function getSafeFilename($filename) {
        // Remove any path information
        $filename = basename($filename);
        
        // Replace spaces and special characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        // Ensure the filename is unique
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $basename = pathinfo($filename, PATHINFO_FILENAME);
        
        return $basename . '_' . uniqid() . '.' . $extension;
    }

    public static function validateUrl($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

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

    public static function validateString($value, $minLength = 1, $maxLength = null) {
        if (!is_string($value)) {
            return false;
        }
        
        $length = mb_strlen($value, 'UTF-8');
        
        if ($length < $minLength) {
            return false;
        }
        
        if ($maxLength !== null && $length > $maxLength) {
            return false;
        }
        
        return true;
    }
} 