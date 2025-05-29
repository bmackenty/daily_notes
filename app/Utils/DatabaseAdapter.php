<?php
namespace App\Utils;

/**
 * DatabaseAdapter
 * 
 * A database abstraction layer that provides database-agnostic methods for common operations.
 * This class handles the differences between database drivers (MySQL and SQLite) by providing
 * unified methods that work consistently across different database systems.
 * 
 * Key features:
 * - Table existence checking
 * - Column existence verification
 * - Database-specific syntax handling
 * - Driver-specific SQL statement adaptation
 * 
 * Currently supports:
 * - MySQL/MariaDB
 * - SQLite
 * 
 * Usage:
 * $adapter = new DatabaseAdapter($pdo);
 * if ($adapter->tableExists('users')) {
 *     // Table exists
 * }
 */
class DatabaseAdapter {
    /** @var \PDO Database connection instance */
    private $db;
    
    /** @var string Database driver name (e.g., 'mysql', 'sqlite') */
    private $driver;

    /**
     * Constructor - initializes the database adapter
     * 
     * @param \PDO $db PDO database connection instance
     */
    public function __construct($db) {
        $this->db = $db;
        // Store the database driver name for driver-specific operations
        $this->driver = $this->db->getAttribute(\PDO::ATTR_DRIVER_NAME);
    }

    /**
     * Checks if a table exists in the database
     * Uses driver-specific queries to check table existence
     * 
     * For SQLite:
     * - Queries the sqlite_master table
     * - Uses a CASE statement for boolean result
     * 
     * For MySQL:
     * - Uses SHOW TABLES LIKE
     * - Checks row count for existence
     * 
     * @param string $tableName Name of the table to check
     * @return bool True if table exists, false otherwise
     */
    public function tableExists($tableName) {
        if ($this->driver === 'sqlite') {
            // SQLite-specific query using sqlite_master table
            $stmt = $this->db->query("
                SELECT CASE 
                    WHEN EXISTS (SELECT 1 FROM sqlite_master WHERE type='table' AND name=?)
                    THEN 1
                    ELSE 0
                END
            ");
            $stmt->execute([$tableName]);
            return $stmt->fetchColumn() === 1;
        } else {
            // MySQL-specific query using SHOW TABLES
            $stmt = $this->db->query("SHOW TABLES LIKE ?");
            $stmt->execute([$tableName]);
            return $stmt->rowCount() > 0;
        }
    }

    /**
     * Checks if a column exists in a specified table
     * Uses driver-specific queries to check column existence
     * 
     * For SQLite:
     * - Uses pragma_table_info to get column information
     * - Uses a CASE statement for boolean result
     * 
     * For MySQL:
     * - Uses SHOW COLUMNS FROM
     * - Checks row count for existence
     * 
     * @param string $tableName Name of the table to check
     * @param string $columnName Name of the column to check
     * @return bool True if column exists, false otherwise
     */
    public function columnExists($tableName, $columnName) {
        if ($this->driver === 'sqlite') {
            // SQLite-specific query using pragma_table_info
            $stmt = $this->db->query("
                SELECT CASE 
                    WHEN EXISTS (
                        SELECT 1 FROM pragma_table_info(?) 
                        WHERE name = ?
                    )
                    THEN 1
                    ELSE 0
                END
            ");
            $stmt->execute([$tableName, $columnName]);
            return $stmt->fetchColumn() === 1;
        } else {
            // MySQL-specific query using SHOW COLUMNS
            $stmt = $this->db->query("SHOW COLUMNS FROM $tableName LIKE ?");
            $stmt->execute([$columnName]);
            return $stmt->rowCount() > 0;
        }
    }

    /**
     * Returns the appropriate auto-increment syntax for the current database driver
     * 
     * @return string 'AUTOINCREMENT' for SQLite, 'AUTO_INCREMENT' for MySQL
     */
    public function getAutoIncrement() {
        return $this->driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT';
    }

    /**
     * Returns the appropriate timestamp data type for the current database driver
     * 
     * @return string 'TIMESTAMP' for SQLite, 'DATETIME' for MySQL
     */
    public function getTimestamp() {
        return $this->driver === 'sqlite' ? 'TIMESTAMP' : 'DATETIME';
    }

    /**
     * Returns the appropriate INSERT IGNORE syntax for the current database driver
     * Used for inserting records while ignoring duplicates
     * 
     * @return string 'INSERT OR IGNORE' for SQLite, 'INSERT IGNORE' for MySQL
     */
    public function getInsertIgnore() {
        return $this->driver === 'sqlite' ? 'INSERT OR IGNORE' : 'INSERT IGNORE';
    }
} 