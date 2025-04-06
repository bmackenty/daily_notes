<?php
namespace App\Utils;

class Config {
    private static $instance = null;
    private $config = [];
    private $db = null;

    private function __construct() {
        if (!defined('ROOT_PATH')) {
            throw new \RuntimeException('ROOT_PATH constant is not defined');
        }
        $this->loadEnv();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadEnv() {
        $envFile = ROOT_PATH . '/.env';
        
        if (!file_exists($envFile)) {
            throw new \RuntimeException('.env file not found at: ' . $envFile);
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                list($key, $value) = explode('=', $line, 2);
                $this->config[trim($key)] = trim($value);
            }
        }
    }

    public function get($key, $default = null) {
        return $this->config[$key] ?? $default;
    }

    public function getDatabase() {
        if ($this->db === null) {
            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;charset=%s",
                $this->get('DB_HOST'),
                $this->get('DB_NAME'),
                $this->get('DB_CHARSET', 'utf8mb4')
            );
            
            $options = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->db = new \PDO($dsn, $this->get('DB_USER'), $this->get('DB_PASS'), $options);
        }
        
        return $this->db;
    }

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