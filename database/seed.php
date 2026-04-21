<?php
/**
 * Database Seed Script
 * Run: php database/seed.php
 */

echo "=== Property Management System - Database Seeder ===\n\n";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=property_management_db;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // --- Users ---
    echo "Seeding users...\n";
    $adminHash = password_hash('Admin@123', PASSWORD_BCRYPT);
    $managerHash = password_hash('Manager@123', PASSWORD_BCRYPT);
    $tenantHash = password_hash('Tenant@123', PASSWORD_BCRYPT);

    $pdo->exec("DELETE FROM notifications");
    $pdo->exec("DELETE FROM expenses");
    $pdo->exec("DELETE FROM maintenance_requests");
    $pdo->exec("DELETE FROM payments");
    $pdo->exec("DELETE FROM leases");
    $pdo->exec("DELETE FROM units");
    $pdo->exec("DELETE FROM properties");
    $pdo->exec("DELETE FROM users");

    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password_hash, role, is_active) VALUES (?, ?, ?, ?, ?, 1)");

    $users = [
        ['System Admin', 'admin@propertyms.com', '+254700000001', $adminHash, 'admin'],
        ['John Manager', 'manager@propertyms.com', '+254700000002', $managerHash, 'manager'],
        ['Jane Manager', 'jane.manager@propertyms.com', '+254700000003', $managerHash, 'manager'],
        ['Alice Tenant', 'alice@tenant.com', '+254711111001', $tenantHash, 'tenant'],
        ['Bob Tenant', 'bob@tenant.com', '+254711111002', $tenantHash, 'tenant'],
        ['Carol Tenant', 'carol@tenant.com', '+254711111003', $tenantHash, 'tenant'],
        ['David Tenant', 'david@tenant.com', '+254711111004', $tenantHash, 'tenant'],
        ['Eve Tenant', 'eve@tenant.com', '+254711111005', $tenantHash, 'tenant'],
    ];

    foreach ($users as $u) {
        $stmt->execute($u);
    }
    echo "  Created " . count($users) . " users.\n";

    // Get user IDs
    $adminId = $pdo->query("SELECT id FROM users WHERE email='admin@propertyms.com'")->fetchColumn();
    $manager1Id = $pdo->query("SELECT id FROM users WHERE email='manager@propertyms.com'")->fetchColumn();
    $manager2Id = $pdo->query("SELECT id FROM users WHERE email='jane.manager@propertyms.com'")->fetchColumn();
    $tenantIds = $pdo->query("SELECT id FROM users WHERE role='tenant' ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);

    // --- Properties ---
    echo "Seeding properties...\n";
    $stmt = $pdo->prepare("INSERT INTO properties (name, address, city, property_type, total_units, description, manager_id) VALUES (?, ?, ?, ?, ?, ?, ?)");

    $properties = [
        ['Sunrise Apartments', '123 Moi Avenue', 'Nairobi', 'apartment_building', 10, 'Modern apartment complex in the heart of Nairobi.', $manager1Id],
        ['Greenview Residences', '456 Kenyatta Road', 'Mombasa', 'apartment_building', 8, 'Beachside apartments with ocean views.', $manager1Id],
        ['Downtown Office Plaza', '789 Uhuru Highway', 'Nairobi', 'commercial', 5, 'Premium commercial office spaces.', $manager2Id],
        ['Hillside Villa', '321 Ngong Road', 'Nairobi', 'single_house', 1, 'Luxury standalone villa with garden.', $manager2Id],
    ];

    foreach ($properties as $p) {
        $stmt->execute($p);
    }
    echo "  Created " . count($properties) . " properties.\n";

    $propertyIds = $pdo->query("SELECT id FROM properties ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);

    // --- Units ---
    echo "Seeding units...\n";
    $stmt = $pdo->prepare("INSERT INTO units (property_id, unit_number, floor_number, bedrooms, bathrooms, area_sqft, rent_amount, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    $units = [
        // Sunrise Apartments
        [$propertyIds[0], 'A101', 1, 2, 1, 850.00, 25000.00, 'occupied'],
        [$propertyIds[0], 'A102', 1, 1, 1, 550.00, 18000.00, 'occupied'],
        [$propertyIds[0], 'A201', 2, 3, 2, 1200.00, 35000.00, 'occupied'],
        [$propertyIds[0], 'A202', 2, 2, 1, 850.00, 25000.00, 'vacant'],
        [$propertyIds[0], 'A301', 3, 2, 2, 900.00, 28000.00, 'maintenance'],
        // Greenview Residences
        [$propertyIds[1], 'B101', 1, 2, 1, 800.00, 30000.00, 'occupied'],
        [$propertyIds[1], 'B102', 1, 1, 1, 500.00, 20000.00, 'vacant'],
        [$propertyIds[1], 'B201', 2, 3, 2, 1100.00, 40000.00, 'occupied'],
        // Commercial
        [$propertyIds[2], 'OFF-1', 1, 0, 1, 2000.00, 80000.00, 'occupied'],
        [$propertyIds[2], 'OFF-2', 1, 0, 1, 1500.00, 60000.00, 'vacant'],
        // Villa
        [$propertyIds[3], 'VILLA-1', 1, 4, 3, 3000.00, 120000.00, 'vacant'],
    ];

    foreach ($units as $unit) {
        $stmt->execute($unit);
    }
    echo "  Created " . count($units) . " units.\n";

    $unitIds = $pdo->query("SELECT id FROM units ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);

    // --- Leases ---
    echo "Seeding leases...\n";
    $stmt = $pdo->prepare("INSERT INTO leases (unit_id, tenant_id, start_date, end_date, monthly_rent, security_deposit, lease_status) VALUES (?, ?, ?, ?, ?, ?, ?)");

    $leases = [
        [$unitIds[0], $tenantIds[0], '2025-01-01', '2026-12-31', 25000.00, 50000.00, 'active'],
        [$unitIds[1], $tenantIds[1], '2025-03-01', '2026-02-28', 18000.00, 36000.00, 'active'],
        [$unitIds[2], $tenantIds[2], '2025-06-01', '2026-05-31', 35000.00, 70000.00, 'active'],
        [$unitIds[5], $tenantIds[3], '2025-04-01', '2026-03-31', 30000.00, 60000.00, 'active'],
        [$unitIds[7], $tenantIds[4], '2025-02-01', '2026-01-31', 40000.00, 80000.00, 'active'],
    ];

    foreach ($leases as $l) {
        $stmt->execute($l);
    }
    echo "  Created " . count($leases) . " leases.\n";

    $leaseIds = $pdo->query("SELECT id FROM leases ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);

    // --- Payments ---
    echo "Seeding payments...\n";
    $stmt = $pdo->prepare("INSERT INTO payments (lease_id, tenant_id, amount, payment_date, due_date, payment_method, reference_number, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    $payments = [
        // Alice's payments
        [$leaseIds[0], $tenantIds[0], 25000.00, '2026-01-05', '2026-01-01', 'mpesa', 'MPESA001', 'paid'],
        [$leaseIds[0], $tenantIds[0], 25000.00, '2026-02-03', '2026-02-01', 'mpesa', 'MPESA002', 'paid'],
        [$leaseIds[0], $tenantIds[0], 25000.00, '2026-03-02', '2026-03-01', 'bank_transfer', 'BT003', 'paid'],
        // Bob's payments
        [$leaseIds[1], $tenantIds[1], 18000.00, '2026-01-01', '2026-01-01', 'mpesa', 'MPESA004', 'paid'],
        [$leaseIds[1], $tenantIds[1], 18000.00, '2026-02-01', '2026-02-01', 'cash', null, 'paid'],
        [$leaseIds[1], $tenantIds[1], 18000.00, '2026-03-15', '2026-03-01', 'mpesa', null, 'overdue'],
        // Carol's payments
        [$leaseIds[2], $tenantIds[2], 35000.00, '2026-01-02', '2026-01-01', 'bank_transfer', 'BT005', 'paid'],
        [$leaseIds[2], $tenantIds[2], 35000.00, '2026-02-01', '2026-02-01', 'mpesa', 'MPESA006', 'paid'],
        // David - overdue
        [$leaseIds[3], $tenantIds[3], 30000.00, '2026-01-05', '2026-01-01', 'mpesa', 'MPESA007', 'paid'],
        [$leaseIds[3], $tenantIds[3], 15000.00, '2026-02-10', '2026-02-01', 'cash', null, 'partial'],
    ];

    foreach ($payments as $p) {
        $stmt->execute($p);
    }
    echo "  Created " . count($payments) . " payments.\n";

    // --- Maintenance Requests ---
    echo "Seeding maintenance requests...\n";
    $stmt = $pdo->prepare("INSERT INTO maintenance_requests (unit_id, tenant_id, title, description, priority, status, assigned_to, cost) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    $requests = [
        [$unitIds[0], $tenantIds[0], 'Leaking Kitchen Faucet', 'The kitchen faucet has been dripping constantly for the past week.', 'medium', 'open', $manager1Id, 0],
        [$unitIds[2], $tenantIds[2], 'AC Not Cooling', 'The air conditioning unit is running but not producing cold air.', 'high', 'in_progress', $manager1Id, 5000.00],
        [$unitIds[5], $tenantIds[3], 'Broken Window Lock', 'The lock on the bedroom window is broken and cannot be secured.', 'high', 'resolved', $manager1Id, 2500.00],
        [$unitIds[1], $tenantIds[1], 'Paint Peeling in Bathroom', 'Paint on the bathroom ceiling is peeling due to moisture.', 'low', 'open', null, 0],
    ];

    foreach ($requests as $r) {
        $stmt->execute($r);
    }
    echo "  Created " . count($requests) . " maintenance requests.\n";

    // --- Expenses ---
    echo "Seeding expenses...\n";
    $stmt = $pdo->prepare("INSERT INTO expenses (property_id, category, description, amount, expense_date, recorded_by) VALUES (?, ?, ?, ?, ?, ?)");

    $expenses = [
        [$propertyIds[0], 'maintenance', 'Plumbing repair - Building A', 15000.00, '2026-01-15', $adminId],
        [$propertyIds[0], 'utilities', 'Water bill - January', 8000.00, '2026-01-31', $adminId],
        [$propertyIds[0], 'insurance', 'Annual building insurance', 120000.00, '2026-01-01', $adminId],
        [$propertyIds[1], 'maintenance', 'Elevator servicing', 25000.00, '2026-02-10', $adminId],
        [$propertyIds[1], 'utilities', 'Electricity - Common areas', 12000.00, '2026-02-28', $adminId],
        [$propertyIds[2], 'taxes', 'Property tax Q1', 50000.00, '2026-03-01', $adminId],
        [$propertyIds[2], 'management', 'Security services', 30000.00, '2026-01-31', $adminId],
    ];

    foreach ($expenses as $exp) {
        $stmt->execute($exp);
    }
    echo "  Created " . count($expenses) . " expenses.\n";

    // --- Notifications ---
    echo "Seeding notifications...\n";
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message, type, is_read) VALUES (?, ?, ?, ?, ?)");

    $notifications = [
        [$tenantIds[1], 'Payment Overdue', 'Your rent payment for March 2026 is overdue. Please make the payment as soon as possible.', 'payment_due', 0],
        [$tenantIds[3], 'Partial Payment Received', 'We received a partial payment of KSh 15,000. Remaining balance: KSh 15,000.', 'payment_due', 0],
        [$tenantIds[0], 'Maintenance Update', 'Your maintenance request "Leaking Kitchen Faucet" has been received and assigned.', 'maintenance', 0],
        [$adminId, 'Lease Expiring Soon', 'The lease for Bob Tenant (Unit A102) expires on 2026-02-28.', 'lease_expiry', 0],
        [$adminId, 'New Maintenance Request', 'A new maintenance request has been submitted for Unit A101.', 'maintenance', 1],
    ];

    foreach ($notifications as $n) {
        $stmt->execute($n);
    }
    echo "  Created " . count($notifications) . " notifications.\n";

    echo "\n=== Seeding completed successfully! ===\n";
    echo "\nLogin credentials:\n";
    echo "  Admin:   admin@propertyms.com / Admin@123\n";
    echo "  Manager: manager@propertyms.com / Manager@123\n";
    echo "  Tenant:  alice@tenant.com / Tenant@123\n";

} catch (PDOException $e) {
    die("Seeding failed: " . $e->getMessage() . "\n");
}
