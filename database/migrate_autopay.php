<?php
/**
 * Migration: Add auto-payment columns to payments table
 */
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance();

$queries = [
    "ALTER TABLE payments ADD COLUMN is_auto_paid TINYINT(1) NOT NULL DEFAULT 0 AFTER receipt_path",
    "ALTER TABLE payments ADD COLUMN auto_paid_at DATETIME NULL AFTER is_auto_paid",
    "ALTER TABLE payments MODIFY COLUMN payment_method ENUM('cash','bank_transfer','mpesa','cheque','online','auto') DEFAULT 'cash'",
];

foreach ($queries as $sql) {
    try {
        $db->exec($sql);
        echo "OK: " . substr($sql, 0, 60) . "...\n";
    } catch (PDOException $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
}

echo "\nMigration complete.\n";
