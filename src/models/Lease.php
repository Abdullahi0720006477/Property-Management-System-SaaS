<?php
class Lease
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id, ?int $companyId = null): ?array
    {
        $sql = "SELECT l.*, u.unit_number, u.property_id, p.name as property_name,
                       t.first_name as tenant_first_name, t.last_name as tenant_last_name,
                       CONCAT(t.first_name, ' ', t.last_name) as tenant_name,
                       t.email as tenant_email, t.phone as tenant_phone
             FROM leases l
             JOIN units u ON l.unit_id = u.id
             JOIN properties p ON u.property_id = p.id
             JOIN tenants t ON l.tenant_id = t.id
             WHERE l.id = ?";
        $params = [$id];
        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() ?: null;
    }

    public function getAll(string $search = '', string $status = '', ?int $propertyId = null, int $limit = RECORDS_PER_PAGE, int $offset = 0, ?int $managerId = null, ?int $companyId = null): array
    {
        $sql = "SELECT l.*, u.unit_number, p.name as property_name,
                       CONCAT(t.first_name, ' ', t.last_name) as tenant_name
                FROM leases l
                JOIN units u ON l.unit_id = u.id
                JOIN properties p ON u.property_id = p.id
                JOIN tenants t ON l.tenant_id = t.id
                WHERE 1=1";
        $params = [];

        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        if ($search) {
            $sql .= " AND (CONCAT(t.first_name, ' ', t.last_name) LIKE ? OR u.unit_number LIKE ? OR p.name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($status) {
            $sql .= " AND l.lease_status = ?";
            $params[] = $status;
        }
        if ($propertyId) {
            $sql .= " AND u.property_id = ?";
            $params[] = $propertyId;
        }
        if ($managerId) {
            $sql .= " AND p.manager_id = ?";
            $params[] = $managerId;
        }

        $sql .= " ORDER BY l.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function count(string $search = '', string $status = '', ?int $propertyId = null, ?int $managerId = null, ?int $companyId = null): int
    {
        $sql = "SELECT COUNT(*) FROM leases l
                JOIN units u ON l.unit_id = u.id
                JOIN properties p ON u.property_id = p.id
                JOIN tenants t ON l.tenant_id = t.id
                WHERE 1=1";
        $params = [];
        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        if ($search) {
            $sql .= " AND (CONCAT(t.first_name, ' ', t.last_name) LIKE ? OR u.unit_number LIKE ? OR p.name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($status) {
            $sql .= " AND l.lease_status = ?";
            $params[] = $status;
        }
        if ($propertyId) {
            $sql .= " AND u.property_id = ?";
            $params[] = $propertyId;
        }
        if ($managerId) {
            $sql .= " AND p.manager_id = ?";
            $params[] = $managerId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO leases (unit_id, tenant_id, start_date, end_date, monthly_rent, security_deposit, lease_status, document_path, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['unit_id'], $data['tenant_id'], $data['start_date'], $data['end_date'],
            $data['monthly_rent'], $data['security_deposit'] ?? 0,
            $data['lease_status'] ?? 'active', $data['document_path'] ?? null, $data['notes'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data, ?int $companyId = null): bool
    {
        $fields = [];
        $params = [];
        foreach (['unit_id', 'tenant_id', 'start_date', 'end_date', 'monthly_rent', 'security_deposit', 'lease_status', 'document_path', 'notes'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }
        if (empty($fields)) return false;
        $params[] = $id;
        $sql = "UPDATE leases SET " . implode(', ', $fields) . " WHERE id = ?";
        if ($companyId) {
            $sql .= " AND unit_id IN (SELECT u.id FROM units u JOIN properties p ON u.property_id = p.id WHERE p.company_id = ?)";
            $params[] = $companyId;
        }
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id, ?int $companyId = null): bool
    {
        $sql = "DELETE FROM leases WHERE id = ?";
        $params = [$id];
        if ($companyId) {
            $sql .= " AND unit_id IN (SELECT u.id FROM units u JOIN properties p ON u.property_id = p.id WHERE p.company_id = ?)";
            $params[] = $companyId;
        }
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function getActiveLeaseByUnit(int $unitId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM leases WHERE unit_id = ? AND lease_status = 'active' LIMIT 1");
        $stmt->execute([$unitId]);
        return $stmt->fetch() ?: null;
    }

    public function getActiveLeaseByTenant(int $tenantId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT l.*, u.unit_number, p.name as property_name, p.address as property_address
             FROM leases l
             JOIN units u ON l.unit_id = u.id
             JOIN properties p ON u.property_id = p.id
             WHERE l.tenant_id = ? AND l.lease_status = 'active'
             LIMIT 1"
        );
        $stmt->execute([$tenantId]);
        return $stmt->fetch() ?: null;
    }

    public function expireLeases(): int
    {
        $stmt = $this->db->prepare(
            "UPDATE leases SET lease_status = 'expired' WHERE lease_status = 'active' AND end_date < CURDATE()"
        );
        $stmt->execute();
        $count = $stmt->rowCount();

        // Also update unit statuses
        if ($count > 0) {
            $this->db->exec(
                "UPDATE units SET status = 'vacant'
                 WHERE id IN (SELECT unit_id FROM leases WHERE lease_status = 'expired')
                 AND status = 'occupied'
                 AND id NOT IN (SELECT unit_id FROM leases WHERE lease_status = 'active')"
            );
        }
        return $count;
    }

    public function getExpiringLeases(int $days = 30, ?int $companyId = null): array
    {
        $sql = "SELECT l.*, u.unit_number, p.name as property_name,
                       CONCAT(t.first_name, ' ', t.last_name) as tenant_name
             FROM leases l
             JOIN units u ON l.unit_id = u.id
             JOIN properties p ON u.property_id = p.id
             JOIN tenants t ON l.tenant_id = t.id
             WHERE l.lease_status = 'active'
             AND l.end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)";
        $params = [$days];
        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        $sql .= " ORDER BY l.end_date";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getActiveCount(?int $companyId = null): int
    {
        $sql = "SELECT COUNT(*) FROM leases l";
        $params = [];
        if ($companyId) {
            $sql .= " JOIN units u ON l.unit_id = u.id JOIN properties p ON u.property_id = p.id WHERE l.lease_status = 'active' AND p.company_id = ?";
            $params[] = $companyId;
        } else {
            $sql .= " WHERE l.lease_status = 'active'";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }
}
