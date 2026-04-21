<?php
/**
 * Database Migration Script
 * Run: php database/migrate.php
 */

echo "=== Property Management System - Database Migration ===\n\n";

try {
    // Connect without database first to create it
    $pdo = new PDO('mysql:host=localhost;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    echo "Connected to MySQL server.\n";

    // Read and execute schema
    $schemaFile = __DIR__ . '/schema.sql';
    if (!file_exists($schemaFile)) {
        die("Error: schema.sql not found.\n");
    }

    $sql = file_get_contents($schemaFile);
    $pdo->exec($sql);

    echo "Database 'property_management_db' created/verified.\n";
    echo "All tables created successfully.\n\n";

    // Verify tables
    $pdo->exec('USE property_management_db');
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "Tables in database:\n";
    foreach ($tables as $table) {
        echo "  - $table\n";
    }

    echo "\nMigration completed successfully!\n";
    echo "Run 'php database/seed.php' to add sample data.\n";

} catch (PDOException $e) {
    die("Migration failed: " . $e->getMessage() . "\n");
}
