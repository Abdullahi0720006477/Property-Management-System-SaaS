<?php
/**
 * BizConnect SaaS Migration
 * Transforms single-tenant Property Management into multi-tenant SaaS
 */

require __DIR__ . '/../config/database.php';

$db = Database::getInstance();

function run($db, string $label, string $sql): void
{
    try {
        $db->exec($sql);
        echo "[OK]    $label\n";
    } catch (PDOException $e) {
        echo "[WARN]  $label — {$e->getMessage()}\n";
    }
}

echo "=== BizConnect SaaS Migration ===\n\n";

// ──────────────────────────────────────
// 1. NEW TABLES
// ──────────────────────────────────────

echo "--- Creating new tables ---\n";

run($db, 'Create companies', "
    CREATE TABLE IF NOT EXISTS companies (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(200) NOT NULL,
        slug VARCHAR(200) NOT NULL,
        email VARCHAR(150),
        phone VARCHAR(20),
        address TEXT,
        city VARCHAR(100),
        country VARCHAR(100) DEFAULT 'Kenya',
        logo VARCHAR(255),
        subscription_plan ENUM('trial','starter','professional','enterprise') DEFAULT 'trial',
        subscription_status ENUM('active','past_due','cancelled','suspended') DEFAULT 'active',
        subscription_start DATE,
        subscription_end DATE,
        max_properties INT DEFAULT 5,
        max_users INT DEFAULT 3,
        billing_cycle ENUM('monthly','yearly') DEFAULT 'monthly',
        stripe_customer_id VARCHAR(255),
        settings JSON,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_slug (slug),
        INDEX idx_subscription_status (subscription_status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

run($db, 'Create super_admins', "
    CREATE TABLE IF NOT EXISTS super_admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(150) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('owner','support','billing') DEFAULT 'support',
        avatar VARCHAR(255),
        is_active TINYINT(1) DEFAULT 1,
        last_login DATETIME,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

run($db, 'Create subscription_history', "
    CREATE TABLE IF NOT EXISTS subscription_history (
        id INT AUTO_INCREMENT PRIMARY KEY,
        company_id INT NOT NULL,
        action ENUM('subscribe','upgrade','downgrade','renew','cancel','suspend','reactivate') NOT NULL,
        old_plan VARCHAR(50),
        new_plan VARCHAR(50),
        amount DECIMAL(10,2),
        currency VARCHAR(10) DEFAULT 'KES',
        payment_reference VARCHAR(255),
        notes TEXT,
        performed_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
        INDEX idx_company_id (company_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

run($db, 'Create invoices', "
    CREATE TABLE IF NOT EXISTS invoices (
        id INT AUTO_INCREMENT PRIMARY KEY,
        company_id INT NOT NULL,
        invoice_number VARCHAR(100) UNIQUE NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        tax_amount DECIMAL(10,2) DEFAULT 0,
        total_amount DECIMAL(10,2) NOT NULL,
        currency VARCHAR(10) DEFAULT 'KES',
        status ENUM('draft','sent','paid','overdue','cancelled') DEFAULT 'draft',
        due_date DATE,
        paid_date DATE,
        payment_method VARCHAR(50),
        payment_reference VARCHAR(255),
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
        INDEX idx_company_id (company_id),
        INDEX idx_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

run($db, 'Create support_tickets', "
    CREATE TABLE IF NOT EXISTS support_tickets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        company_id INT NOT NULL,
        user_id INT,
        subject VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        priority ENUM('low','medium','high','urgent') DEFAULT 'medium',
        status ENUM('open','in_progress','waiting','resolved','closed') DEFAULT 'open',
        assigned_to INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
        FOREIGN KEY (assigned_to) REFERENCES super_admins(id) ON DELETE SET NULL,
        INDEX idx_company_id (company_id),
        INDEX idx_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

run($db, 'Create ticket_replies', "
    CREATE TABLE IF NOT EXISTS ticket_replies (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ticket_id INT NOT NULL,
        user_type ENUM('staff','super_admin') NOT NULL,
        user_id INT,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (ticket_id) REFERENCES support_tickets(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

run($db, 'Create announcements', "
    CREATE TABLE IF NOT EXISTS announcements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        type ENUM('info','warning','feature','maintenance') DEFAULT 'info',
        target ENUM('all','starter','professional','enterprise') DEFAULT 'all',
        is_active TINYINT(1) DEFAULT 1,
        starts_at DATETIME,
        ends_at DATETIME,
        created_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (created_by) REFERENCES super_admins(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

run($db, 'Create tenants', "
    CREATE TABLE IF NOT EXISTS tenants (
        id INT AUTO_INCREMENT PRIMARY KEY,
        company_id INT NOT NULL,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        email VARCHAR(150),
        phone VARCHAR(20) NOT NULL,
        id_number VARCHAR(50),
        emergency_contact_name VARCHAR(100),
        emergency_contact_phone VARCHAR(20),
        date_of_birth DATE,
        occupation VARCHAR(100),
        employer VARCHAR(200),
        notes TEXT,
        avatar VARCHAR(255),
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
        INDEX idx_company_id (company_id),
        INDEX idx_phone (phone)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

// ──────────────────────────────────────
// 2. ALTER EXISTING TABLES — add company_id
// ──────────────────────────────────────

echo "\n--- Adding company_id to existing tables ---\n";

$cascadeTables = ['users', 'properties', 'units', 'leases', 'payments', 'maintenance_requests', 'expenses', 'notifications'];
$setNullTables = ['activity_logs'];

foreach ($cascadeTables as $table) {
    run($db, "Add company_id column to $table", "
        ALTER TABLE `$table` ADD COLUMN company_id INT NULL AFTER id
    ");
    run($db, "Add FK company_id on $table", "
        ALTER TABLE `$table`
            ADD CONSTRAINT fk_{$table}_company
            FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
    ");
    run($db, "Add index idx_company on $table", "
        ALTER TABLE `$table` ADD INDEX idx_company (company_id)
    ");
}

foreach ($setNullTables as $table) {
    run($db, "Add company_id column to $table", "
        ALTER TABLE `$table` ADD COLUMN company_id INT NULL AFTER id
    ");
    run($db, "Add FK company_id on $table (SET NULL)", "
        ALTER TABLE `$table`
            ADD CONSTRAINT fk_{$table}_company
            FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE SET NULL
    ");
    run($db, "Add index idx_company on $table", "
        ALTER TABLE `$table` ADD INDEX idx_company (company_id)
    ");
}

// ──────────────────────────────────────
// 3. MODIFY users.role ENUM
// ──────────────────────────────────────

echo "\n--- Updating users.role enum ---\n";

run($db, 'Modify users.role enum', "
    ALTER TABLE users MODIFY role ENUM('company_admin','manager','staff','accountant','maintenance_tech') DEFAULT 'staff'
");

echo "\n=== Migration complete ===\n";
