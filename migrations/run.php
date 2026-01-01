<?php
/**
 * Migration Runner
 * Executes SQL migration files in order and tracks executed migrations
 */

require_once __DIR__ . '/../includes/database.php';

try {
    $db = getDatabaseConnection();
} catch (Exception $e) {
    die("Fatal error: " . $e->getMessage() . "\n");
}

// Create schema_migrations table if it doesn't exist
try {
    $db->exec("CREATE TABLE IF NOT EXISTS schema_migrations (
        migration VARCHAR(255) PRIMARY KEY,
        executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
} catch (PDOException $e) {
    die("Error creating schema_migrations table: " . $e->getMessage() . "\n");
}

// Get list of migration files
$migrationDir = __DIR__;
$files = glob($migrationDir . '/[0-9][0-9][0-9]_*.sql');
sort($files);

// Get already executed migrations
$stmt = $db->query("SELECT migration FROM schema_migrations");
$executed = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "Running migrations...\n\n";

foreach ($files as $file) {
    $filename = basename($file);
    
    // Skip schema_migrations table creation if already executed
    if (in_array($filename, $executed)) {
        echo "Skipping $filename (already executed)\n";
        continue;
    }
    
    echo "Executing $filename...\n";
    
    $sql = file_get_contents($file);
    
    try {
        // DDL statements (CREATE TABLE, etc.) auto-commit in MySQL/MariaDB
        // So we execute the migration SQL first, then record it
        $db->exec($sql);
        
        // Record migration in a separate operation
        $stmt = $db->prepare("INSERT INTO schema_migrations (migration) VALUES (?)");
        $stmt->execute([$filename]);
        
        echo "✓ $filename completed successfully\n\n";
    } catch (PDOException $e) {
        die("Error executing $filename: " . $e->getMessage() . "\n");
    }
}

echo "All migrations completed successfully!\n";

