<?php
/**
 * Database Connection Helper
 */

require_once __DIR__ . '/../config/database.php';

$dbConfig = require __DIR__ . '/../config/database.php';

$dbConnection = null;

function getDatabaseConnection() {
    global $dbConnection, $dbConfig;
    
    if ($dbConnection === null) {
        try {
            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;charset=%s",
                $dbConfig['host'],
                $dbConfig['dbname'],
                $dbConfig['charset']
            );
            
            $dbConnection = new PDO(
                $dsn,
                $dbConfig['username'],
                $dbConfig['password'],
                $dbConfig['options']
            );
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    return $dbConnection;
}

