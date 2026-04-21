<?php
class Unit
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id, ?int $companyId = null): ?array
    {
        $sql = "SELECT u.*, p.name as property_name, p.address as property_address
             FROM units u
             JOIN properties p ON u.property_id = p.id
             WHERE u.id = ?";
        $params = [$id];
        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() ?: null;
    }

    public function getAll(string $search = '', ?int $propertyId = null, string $status = '', int $limit = RECORDS_PER_PAGE, int $offset = 0, ?int $companyId = null): array
    {
        $sql = "SELECT u.*, p.name as property_name
                FROM units u
                JOIN properties p ON u.property_id = p.id
                WHERE p.is_active = 1";
        $params = [];

        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        if ($search) {
            $sql .= " AND (u.unit_number LIKE ? OR p.name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($propertyId) {
            $sql .= " AND u.property_id = ?";
            $params[] = $propertyId;
        }
        if ($status) {
            $sql .= " AND u.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY p.name, u.unit_number LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function count(string $search = '', ?int $propertyId = null, string $status = '', ?int $companyId = null): int
    {
        $sql = "SELECT COUNT(*) FROM units u JOIN properties p ON u.property_id = p.id WHERE p.is_active = 1";
        $params = [];
        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        if ($search) {
            $sql .= " AND (u.unit_number LIKE ? OR p.name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($propertyId) {
            $sql .= " AND u.property_id = ?";
            $params[] = $propertyId;
        }
        if ($status) {
            $sql .= " AND u.status = ?";
            $params[] = $status;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function getByProperty(int $propertyId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM units WHERE property_id = ? ORDER BY unit_number");
        $stmt->execute([$propertyId]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO units (property_id, unit_number, floor_number, bedrooms, bathrooms, area_sqft, rent_amount, status, description)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['property_id'], $data['unit_number'], $data['floor_number'] ?? null,
            $data['bedrooms'] ?? 1, $data['bathrooms'] ?? 1, $data['area_sqft'] ?? null,
            $data['rent_amount'], $data['status'] ?? 'vacant', $data['description'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data, ?int $companyId = null): bool
    {
        $fields = [];
        $params = [];
        foreach (['property_id', 'unit_number', 'floor_number', 'bedrooms', 'bathrooms', 'area_sqft', 'rent_amount', 'status', 'description'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }
        if (empty($fields)) return false;
        $params[] = $id;
        $sql = "UPDATE units SET " . implode(', ', $fields) . " WHERE id = ?";
        if ($companyId) {
            $sql .= " AND property_id IN (SELECT id FROM properties WHERE company_id = ?)";
            $params[] = $companyId;
        }
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id, ?int $companyId = null): bool
    {
        $sql = "DELETE FROM units WHERE id = ?";
        $params = [$id];
        if ($companyId) {
            $sql .= " AND property_id IN (SELECT id FROM properties WHERE company_id = ?)";
            $params[] = $companyId;
        }
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function updateStatus(int $id, string $status, ?int $companyId = null): bool
    {
        $sql = "UPDATE units SET status = ? WHERE id = ?";
        $params = [$status, $id];
        if ($companyId) {
            $sql .= " AND property_id IN (SELECT id FROM properties WHERE company_id = ?)";
            $params[] = $companyId;
        }
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function getTotalCount(?int $companyId = null): int
    {
        $sql = "SELECT COUNT(*) FROM units u JOIN properties p ON u.property_id = p.id WHERE p.is_active = 1";
        $params = [];
        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function getOccupiedCount(?int $companyId = null): int
    {
        $sql = "SELECT COUNT(*) FROM units u JOIN properties p ON u.property_id = p.id WHERE p.is_active = 1 AND u.status = 'occupied'";
        $params = [];
        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function getVacantCount(?int $companyId = null): int
    {
        $sql = "SELECT COUNT(*) FROM units u JOIN properties p ON u.property_id = p.id WHERE p.is_active = 1 AND u.status = 'vacant'";
        $params = [];
        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function getVacantUnits(?int $companyId = null): array
    {
        $sql = "SELECT u.*, p.name as property_name
             FROM units u
             JOIN properties p ON u.property_id = p.id
             WHERE u.status = 'vacant' AND p.is_active = 1";
        $params = [];
        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        $sql .= " ORDER BY p.name, u.unit_number";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getUnitWithActiveLease(int $unitId, ?int $companyId = null): ?array
    {
        $sql = "SELECT u.*, l.id as lease_id, l.tenant_id, t.first_name, t.last_name,
                       CONCAT(t.first_name, ' ', t.last_name) as tenant_name,
                       l.monthly_rent, l.start_date, l.end_date
             FROM units u
             JOIN properties p ON u.property_id = p.id
             LEFT JOIN leases l ON u.id = l.unit_id AND l.lease_status = 'active'
             LEFT JOIN tenants t ON l.tenant_id = t.id
             WHERE u.id = ?";
        $params = [$unitId];
        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() ?: null;
    }
}
