<?php
/**
 * Global Search Controller - returns JSON
 */
requireAuth();

$query = trim($_GET['q'] ?? '');

header('Content-Type: application/json');

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

$db = Database::getInstance();
$results = [];
$searchTerm = "%{$query}%";
$cid = companyId();

// Search properties
$sql = "SELECT id, name, 'property' AS type, 'bi-building' AS icon FROM properties WHERE company_id = ? AND name LIKE ? AND is_active = 1";
$params = [$cid, $searchTerm];
$sql .= " LIMIT 5";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$results = array_merge($results, $stmt->fetchAll(PDO::FETCH_ASSOC));

// Search units
$sql = "SELECT u.id, CONCAT(p.name, ' - Unit ', u.unit_number) AS name, 'unit' AS type, 'bi-door-open' AS icon
        FROM units u JOIN properties p ON u.property_id = p.id
        WHERE p.company_id = ? AND (u.unit_number LIKE ? OR p.name LIKE ?) AND p.is_active = 1";
$params = [$cid, $searchTerm, $searchTerm];
$sql .= " LIMIT 5";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$results = array_merge($results, $stmt->fetchAll(PDO::FETCH_ASSOC));

// Search tenants (from tenants table)
$sql = "SELECT id, CONCAT(first_name, ' ', last_name) AS name, 'tenant' AS type, 'bi-person' AS icon
        FROM tenants WHERE company_id = ? AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ?) AND is_active = 1 LIMIT 5";
$stmt = $db->prepare($sql);
$stmt->execute([$cid, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
$results = array_merge($results, $stmt->fetchAll(PDO::FETCH_ASSOC));

// Search leases
$sql = "SELECT l.id, CONCAT('Lease #', l.id, ' - ', t.first_name, ' ', t.last_name) AS name, 'lease' AS type, 'bi-file-earmark-text' AS icon
        FROM leases l
        JOIN tenants t ON l.tenant_id = t.id
        JOIN units un ON l.unit_id = un.id
        JOIN properties p ON un.property_id = p.id
        WHERE p.company_id = ? AND (CONCAT(t.first_name, ' ', t.last_name) LIKE ? OR un.unit_number LIKE ?)";
$params = [$cid, $searchTerm, $searchTerm];
$sql .= " LIMIT 5";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$results = array_merge($results, $stmt->fetchAll(PDO::FETCH_ASSOC));

echo json_encode($results);
exit;
