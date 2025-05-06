<?php
namespace App\Utils;

class DatabaseAdapter {
    private $db;
    private $driver;

    public function __construct($db) {
        $this->db = $db;
        $this->driver = $this->db->getAttribute(\PDO::ATTR_DRIVER_NAME);
    }

    public function tableExists($tableName) {
        if ($this->driver === 'sqlite') {
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
            $stmt = $this->db->query("SHOW TABLES LIKE ?");
            $stmt->execute([$tableName]);
            return $stmt->rowCount() > 0;
        }
    }

    public function columnExists($tableName, $columnName) {
        if ($this->driver === 'sqlite') {
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
            $stmt = $this->db->query("SHOW COLUMNS FROM $tableName LIKE ?");
            $stmt->execute([$columnName]);
            return $stmt->rowCount() > 0;
        }
    }

    public function getAutoIncrement() {
        return $this->driver === 'sqlite' ? 'AUTOINCREMENT' : 'AUTO_INCREMENT';
    }

    public function getTimestamp() {
        return $this->driver === 'sqlite' ? 'TIMESTAMP' : 'DATETIME';
    }

    public function getInsertIgnore() {
        return $this->driver === 'sqlite' ? 'INSERT OR IGNORE' : 'INSERT IGNORE';
    }
} 