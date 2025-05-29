<?php
namespace App\Utils;

/**
 * Config
 * 
 * A singleton class that manages application configuration and environment settings.
 * This class is responsible for:
 * - Loading and parsing environment variables from .env file
 * - Providing access to configuration values
 * - Managing database connection settings
 * - Implementing the Singleton pattern to ensure a single configuration instance
 * 
 * Usage:
 * $config = Config::getInstance();
 * $value = $config->get('CONFIG_KEY');
 * $db = $config->getDatabase();
 * 
 * The singleton pattern ensures that configuration is loaded only once
 * and provides a global point of access to the configuration throughout the application.
 */
class Config {
    /** @var Config|null The single instance of the Config class */
    private static $instance = null;
    
    /** @var array Storage for configuration values loaded from .env file */
    private $config = [];
    
    /** @var \PDO|null Database connection instance */
    private $db = null;

    /**
     * Private constructor to prevent direct instantiation
     * Initializes the configuration by loading environment variables
     * 
     * @throws \RuntimeException If ROOT_PATH constant is not defined
     * @throws \RuntimeException If .env file is not found
     */
    private function __construct() {
        // Ensure ROOT_PATH is defined for file operations
        if (!defined('ROOT_PATH')) {
            throw new \RuntimeException('ROOT_PATH constant is not defined');
        }
        $this->loadEnv();
    }

    /**
     * Gets the singleton instance of the Config class
     * Creates a new instance if one doesn't exist
     * 
     * @return Config The singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Loads and parses environment variables from .env file
     * Processes each line that contains a key-value pair
     * Ignores comments (lines starting with #) and empty lines
     * 
     * @throws \RuntimeException If .env file is not found
     */
    private function loadEnv() {
        $envFile = ROOT_PATH . '/.env';
        
        // Verify .env file exists
        if (!file_exists($envFile)) {
            throw new \RuntimeException('.env file not found at: ' . $envFile);
        }

        // Read and process .env file
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Process only non-comment lines containing key-value pairs
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                // Split on first '=' to handle values that might contain '='
                list($key, $value) = explode('=', $line, 2);
                // Store trimmed key-value pair in config array
                $this->config[trim($key)] = trim($value);
            }
        }
    }

    /**
     * Retrieves a configuration value by key
     * Returns the default value if the key doesn't exist
     * 
     * @param string $key The configuration key to retrieve
     * @param mixed $default Default value to return if key doesn't exist
     * @return mixed The configuration value or default value
     */
    public function get($key, $default = null) {
        return $this->config[$key] ?? $default;
    }

    /**
     * Creates and returns a PDO database connection
     * Implements lazy loading - connection is only created when first requested
     * Uses configuration values from .env file
     * 
     * Database connection settings:
     * - Host: DB_HOST
     * - Database name: DB_NAME
     * - Username: DB_USER
     * - Password: DB_PASS
     * - Character set: DB_CHARSET (defaults to utf8mb4)
     * 
     * PDO options:
     * - Error mode: Exception
     * - Default fetch mode: Associative array
     * - Prepared statements: Native (not emulated)
     * 
     * @return \PDO Database connection instance
     */
    public function getDatabase() {
        if ($this->db === null) {
            // Construct DSN (Data Source Name) for PDO connection
            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;charset=%s",
                $this->get('DB_HOST'),
                $this->get('DB_NAME'),
                $this->get('DB_CHARSET', 'utf8mb4')
            );
            
            // Configure PDO options for secure and efficient database operations
            $options = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,        // Throw exceptions for errors
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,   // Return rows as associative arrays
                \PDO::ATTR_EMULATE_PREPARES => false,                 // Use real prepared statements
            ];
            
            // Create PDO instance with configured options
            $this->db = new \PDO(
                $dsn,
                $this->get('DB_USER'),
                $this->get('DB_PASS'),
                $options
            );
        }
        
        return $this->db;
    }

    /**
     * Returns an array of database configuration values
     * Useful for creating database connections outside of this class
     * or for debugging purposes
     * 
     * @return array Database configuration values including:
     *               - host: Database server hostname
     *               - name: Database name
     *               - user: Database username
     *               - pass: Database password
     *               - charset: Database character set
     */
    public function getDatabaseConfig() {
        return [
            'host' => $this->get('DB_HOST'),
            'name' => $this->get('DB_NAME'),
            'user' => $this->get('DB_USER'),
            'pass' => $this->get('DB_PASS'),
            'charset' => $this->get('DB_CHARSET', 'utf8mb4')
        ];
    }
} 