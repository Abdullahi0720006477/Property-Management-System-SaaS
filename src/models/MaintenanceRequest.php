<?php
class MaintenanceRequest
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id, ?int $companyId = null): ?array
    {
        $sql = "SELECT m.*, u.unit_number, p.name as property_name,
                       CONCAT(t.first_name, ' ', t.last_name) as tenant_name,
                       assigned.full_name as assigned_name
             FROM maintenance_requests m
             JOIN units u ON m.unit_id = u.id
             JOIN properties p ON u.property_id = p.id
             JOIN tenants t ON m.tenant_id = t.id
             LEFT JOIN users assigned ON m.assigned_to = assigned.id
             WHERE m.id = ?";
        $params = [$id];
        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() ?: null;
    }

    public function getAll(string $search = '', string $status = '', string $priority = '', ?int $tenantId = null, int $limit = RECORDS_PER_PAGE, int $offset = 0, ?int $companyId = null): array
    {
        $sql = "SELECT m.*, u.unit_number, p.name as property_name,
                       CONCAT(t.first_name, ' ', t.last_name) as tenant_name,
                       assigned.full_name as assigned_name
                FROM maintenance_requests m
                JOIN units u ON m.unit_id = u.id
                JOIN properties p ON u.property_id = p.id
                JOIN tenants t ON m.tenant_id = t.id
                LEFT JOIN users assigned ON m.assigned_to = assigned.id
                WHERE 1=1";
        $params = [];

        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        if ($search) {
            $sql .= " AND (m.title LIKE ? OR CONCAT(t.first_name, ' ', t.last_name) LIKE ? OR u.unit_number LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($status) { $sql .= " AND m.status = ?"; $params[] = $status; }
        if ($priority) { $sql .= " AND m.priority = ?"; $params[] = $priority; }
        if ($tenantId) { $sql .= " AND m.tenant_id = ?"; $params[] = $tenantId; }

        $sql .= " ORDER BY FIELD(m.priority, 'emergency', 'high', 'medium', 'low'), m.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function count(string $search = '', string $status = '', string $priority = '', ?int $tenantId = null, ?int $companyId = null): int
    {
        $sql = "SELECT COUNT(*) FROM maintenance_requests m
                JOIN units u ON m.unit_id = u.id
                JOIN properties p ON u.property_id = p.id
                JOIN tenants t ON m.tenant_id = t.id
                WHERE 1=1";
        $params = [];
        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        if ($search) {
            $sql .= " AND (m.title LIKE ? OR CONCAT(t.first_name, ' ', t.last_name) LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($status) { $sql .= " AND m.status = ?"; $params[] = $status; }
        if ($priority) { $sql .= " AND m.priority = ?"; $params[] = $priority; }
        if ($tenantId) { $sql .= " AND m.tenant_id = ?"; $params[] = $tenantId; }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO maintenance_requests (unit_id, tenant_id, title, description, priority, status, images)
             VALUES (?, ?, ?, ?, ?, 'open', ?)"
        );
        $stmt->execute([
            $data['unit_id'], $data['tenant_id'], $data['title'],
            $data['description'], $data['priority'] ?? 'medium', $data['images'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data, ?int $companyId = null): bool
    {
        $fields = [];
        $params = [];
        foreach (['title', 'description', 'priority', 'status', 'assigned_to', 'cost', 'resolved_at', 'images'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }
        if (empty($fields)) return false;
        $params[] = $id;
        $sql = "UPDATE maintenance_requests SET " . implode(', ', $fields) . " WHERE id = ?";
        if ($companyId) {
            $sql .= " AND unit_id IN (SELECT u.id FROM units u JOIN properties p ON u.property_id = p.id WHERE p.company_id = ?)";
            $params[] = $companyId;
        }
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id, ?int $companyId = null): bool
    {
        $sql = "DELETE FROM maintenance_requests WHERE id = ?";
        $params = [$id];
        if ($companyId) {
            $sql .= " AND unit_id IN (SELECT u.id FROM units u JOIN properties p ON u.property_id = p.id WHERE p.company_id = ?)";
            $params[] = $companyId;
        }
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function getOpenCount(?int $companyId = null): int
    {
        $sql = "SELECT COUNT(*) FROM maintenance_requests m";
        $params = [];
        if ($companyId) {
            $sql .= " JOIN units u ON m.unit_id = u.id JOIN properties p ON u.property_id = p.id";
        }
        $sql .= " WHERE m.status IN ('open', 'in_progress')";
        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function getRecent(int $limit = 5, ?int $companyId = null): array
    {
        $sql = "SELECT m.*, u.unit_number, p.name as property_name,
                       CONCAT(t.first_name, ' ', t.last_name) as tenant_name
             FROM maintenance_requests m
             JOIN units u ON m.unit_id = u.id
             JOIN properties p ON u.property_id = p.id
             JOIN tenants t ON m.tenant_id = t.id
             WHERE 1=1";
        $params = [];
        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        $sql .= " ORDER BY m.created_at DESC LIMIT ?";
        $params[] = $limit;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
