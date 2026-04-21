<?php
/**
 * BizConnect SaaS Seeder
 * Seeds super admin, demo companies, and migrates existing data
 */

require __DIR__ . '/../config/database.php';

$db = Database::getInstance();

echo "=== BizConnect SaaS Seeder ===\n\n";

// ──────────────────────────────────────
// 1. Super Admin
// ──────────────────────────────────────

echo "--- Creating super admin ---\n";
try {
    $hash = password_hash('BizAdmin@2026', PASSWORD_BCRYPT);
    $stmt = $db->prepare("INSERT INTO super_admins (full_name, email, password_hash, role, is_active) VALUES (?, ?, ?, 'owner', 1)");
    $stmt->execute(['BizConnect Admin', 'admin@bizconnect.co.ke', $hash]);
    $superAdminId = $db->lastInsertId();
    echo "[OK]    Super admin created (id: $superAdminId)\n";
} catch (PDOException $e) {
    echo "[WARN]  Super admin — {$e->getMessage()}\n";
    // Try to fetch existing
    $stmt = $db->prepare("SELECT id FROM super_admins WHERE email = ?");
    $stmt->execute(['admin@bizconnect.co.ke']);
    $superAdminId = $stmt->fetchColumn();
}

// ──────────────────────────────────────
// 2. Demo Company — Sunrise Properties Ltd
// ──────────────────────────────────────

echo "\n--- Creating demo company: Sunrise Properties Ltd ---\n";
try {
    $stmt = $db->prepare("
        INSERT INTO companies (name, slug, email, phone, address, city, country, subscription_plan, subscription_status, subscription_start, subscription_end, max_properties, max_users, billing_cycle, is_active, settings)
        VALUES (?, ?, ?, ?, ?, ?, 'Kenya', 'professional', 'active', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 YEAR), 50, 20, 'yearly', 1, ?)
    ");
    $stmt->execute([
        'Sunrise Properties Ltd',
        'sunrise-properties',
        'info@sunriseproperties.co.ke',
        '+254700000001',
        'Westlands, Nairobi',
        'Nairobi',
        json_encode(['currency' => 'KES', 'timezone' => 'Africa/Nairobi'])
    ]);
    $sunriseId = $db->lastInsertId();
    echo "[OK]    Sunrise Properties Ltd created (id: $sunriseId)\n";
} catch (PDOException $e) {
    echo "[WARN]  Sunrise company — {$e->getMessage()}\n";
    $stmt = $db->prepare("SELECT id FROM companies WHERE slug = ?");
    $stmt->execute(['sunrise-properties']);
    $sunriseId = $stmt->fetchColumn();
}

// ──────────────────────────────────────
// 3. Update existing data to belong to Sunrise Properties
// ──────────────────────────────────────

echo "\n--- Assigning existing data to Sunrise Properties (id: $sunriseId) ---\n";

$tables = ['users', 'properties', 'units', 'leases', 'payments', 'maintenance_requests', 'expenses', 'notifications', 'activity_logs'];

foreach ($tables as $table) {
    try {
        $count = $db->exec("UPDATE `$table` SET company_id = $sunriseId WHERE company_id IS NULL");
        echo "[OK]    $table — $count rows updated\n";
    } catch (PDOException $e) {
        echo "[WARN]  $table — {$e->getMessage()}\n";
    }
}

// ──────────────────────────────────────
// 4. Second Company — Greenfield Estates
// ──────────────────────────────────────

echo "\n--- Creating trial company: Greenfield Estates ---\n";
try {
    $stmt = $db->prepare("
        INSERT INTO companies (name, slug, email, phone, address, city, country, subscription_plan, subscription_status, subscription_start, subscription_end, max_properties, max_users, billing_cycle, is_active, settings)
        VALUES (?, ?, ?, ?, ?, ?, 'Kenya', 'trial', 'active', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY), 5, 3, 'monthly', 1, ?)
    ");
    $stmt->execute([
        'Greenfield Estates',
        'greenfield-estates',
        'admin@greenfield.co.ke',
        '+254700000002',
        'Kilimani, Nairobi',
        'Nairobi',
        json_encode(['currency' => 'KES', 'timezone' => 'Africa/Nairobi'])
    ]);
    $greenfieldId = $db->lastInsertId();
    echo "[OK]    Greenfield Estates created (id: $greenfieldId)\n";
} catch (PDOException $e) {
    echo "[WARN]  Greenfield company — {$e->getMessage()}\n";
    $stmt = $db->prepare("SELECT id FROM companies WHERE slug = ?");
    $stmt->execute(['greenfield-estates']);
    $greenfieldId = $stmt->fetchColumn();
}

// Create Greenfield admin user
echo "\n--- Creating Greenfield admin user ---\n";
try {
    $hash = password_hash('Green@2026', PASSWORD_BCRYPT);
    $stmt = $db->prepare("
        INSERT INTO users (company_id, full_name, email, phone, password_hash, role, is_active)
        VALUES (?, ?, ?, ?, ?, 'company_admin', 1)
    ");
    $stmt->execute([
        $greenfieldId,
        'Greenfield Admin',
        'admin@greenfield.co.ke',
        '+254700000002',
        $hash
    ]);
    echo "[OK]    Greenfield admin user created (id: {$db->lastInsertId()})\n";
} catch (PDOException $e) {
    echo "[WARN]  Greenfield admin — {$e->getMessage()}\n";
}

echo "\n=== Seeding complete ===\n";
