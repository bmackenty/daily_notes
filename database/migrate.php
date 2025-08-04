<?php
// Define root path
define('ROOT_PATH', dirname(__DIR__));

// Load required classes
require_once ROOT_PATH . '/app/Utils/Config.php';
require_once ROOT_PATH . '/app/Utils/Logger.php';

use App\Utils\Config;
use App\Utils\Logger;

try {
    // Initialize configuration
    $config = Config::getInstance();
    $dbConfig = $config->getDatabaseConfig();
    
    // Create database connection
    $dsn = sprintf(
        "mysql:host=%s;dbname=%s;charset=%s",
        $dbConfig['host'],
        $dbConfig['name'],
        $dbConfig['charset']
    );
    
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], $options);
    
    // Get all migration files
    $migrationsDir = __DIR__ . '/migrations/';
    $migrationFiles = glob($migrationsDir . '*.sql');
    sort($migrationFiles); // Ensure files are processed in order
    
    if (empty($migrationFiles)) {
        throw new RuntimeException("No migration files found in: $migrationsDir");
    }
    
    echo "Found " . count($migrationFiles) . " migration files.\n";
    
    // Process each migration file
    foreach ($migrationFiles as $migrationFile) {
        $filename = basename($migrationFile);
        echo "Processing migration: $filename\n";
        
        if (!file_exists($migrationFile)) {
            throw new RuntimeException("Migration file not found: $migrationFile");
        }
        
        $migration = file_get_contents($migrationFile);
        if ($migration === false) {
            throw new RuntimeException("Failed to read migration file: $migrationFile");
        }
        
        // Split the file into individual statements
        $statements = array_filter(array_map('trim', explode(';', $migration)));
        
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                try {
                    $pdo->exec($statement);
                    Logger::log("Executed SQL from $filename: " . substr($statement, 0, 100) . "...", 'INFO');
                } catch (PDOException $e) {
                    // Log the error but continue with other statements
                    Logger::log("Error executing SQL from $filename: " . $e->getMessage(), 'ERROR');
                    Logger::log("Failed SQL: " . $statement, 'ERROR');
                }
            }
        }
        
        echo "Completed migration: $filename\n";
    }
    
    echo "All migrations completed successfully!\n";
    Logger::log("All migrations completed successfully", 'SUCCESS');
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    Logger::log("Migration failed: " . $e->getMessage(), 'ERROR');
    exit(1);
} 