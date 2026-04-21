<?php
class Property
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id, ?int $companyId = null): ?array
    {
        $sql = "SELECT p.*, u.full_name as manager_name
             FROM properties p
             LEFT JOIN users u ON p.manager_id = u.id
             WHERE p.id = ?";
        $params = [$id];
        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() ?: null;
    }

    public function getAll(string $search = '', string $city = '', string $type = '', ?int $managerId = null, int $limit = RECORDS_PER_PAGE, int $offset = 0, ?int $companyId = null): array
    {
        $sql = "SELECT p.*, u.full_name as manager_name,
                (SELECT COUNT(*) FROM units WHERE property_id = p.id) as unit_count,
                (SELECT COUNT(*) FROM units WHERE property_id = p.id AND status = 'occupied') as occupied_count
                FROM properties p
                LEFT JOIN users u ON p.manager_id = u.id
                WHERE p.is_active = 1";
        $params = [];

        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        if ($search) {
            $sql .= " AND (p.name LIKE ? OR p.address LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($city) {
            $sql .= " AND p.city = ?";
            $params[] = $city;
        }
        if ($type) {
            $sql .= " AND p.property_type = ?";
            $params[] = $type;
        }
        if ($managerId) {
            $sql .= " AND p.manager_id = ?";
            $params[] = $managerId;
        }

        $sql .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function count(string $search = '', string $city = '', string $type = '', ?int $managerId = null, ?int $companyId = null): int
    {
        $sql = "SELECT COUNT(*) FROM properties p WHERE p.is_active = 1";
        $params = [];

        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        if ($search) {
            $sql .= " AND (p.name LIKE ? OR p.address LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($city) {
            $sql .= " AND p.city = ?";
            $params[] = $city;
        }
        if ($type) {
            $sql .= " AND p.property_type = ?";
            $params[] = $type;
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
            "INSERT INTO properties (company_id, name, address, city, property_type, total_units, description, image, manager_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['company_id'], $data['name'], $data['address'], $data['city'] ?? null,
            $data['property_type'], $data['total_units'] ?? 1,
            $data['description'] ?? null, $data['image'] ?? null,
            $data['manager_id'] ?: null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data, ?int $companyId = null): bool
    {
        $fields = [];
        $params = [];
        foreach (['name', 'address', 'city', 'property_type', 'total_units', 'description', 'image', 'manager_id', 'is_active'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = ?";
                $params[] = $data[$field] ?: null;
            }
        }
        if (empty($fields)) return false;
        $params[] = $id;
        $sql = "UPDATE properties SET " . implode(', ', $fields) . " WHERE id = ?";
        if ($companyId) {
            $sql .= " AND company_id = ?";
            $params[] = $companyId;
        }
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id, ?int $companyId = null): bool
    {
        $sql = "UPDATE properties SET is_active = 0 WHERE id = ?";
        $params = [$id];
        if ($companyId) {
            $sql .= " AND company_id = ?";
            $params[] = $companyId;
        }
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function getCities(?int $companyId = null): array
    {
        $sql = "SELECT DISTINCT city FROM properties WHERE city IS NOT NULL AND is_active = 1";
        $params = [];
        if ($companyId) {
            $sql .= " AND company_id = ?";
            $params[] = $companyId;
        }
        $sql .= " ORDER BY city";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getTotalCount(?int $companyId = null): int
    {
        $sql = "SELECT COUNT(*) FROM properties WHERE is_active = 1";
        $params = [];
        if ($companyId) {
            $sql .= " AND company_id = ?";
            $params[] = $companyId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }
}
