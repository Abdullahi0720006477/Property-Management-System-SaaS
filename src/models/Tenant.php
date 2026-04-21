<?php
class Tenant {
    public static function findById(int $id, int $companyId): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM tenants WHERE id = ? AND company_id = ?");
        $stmt->execute([$id, $companyId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function getAll(int $companyId, array $filters = []): array {
        $db = Database::getInstance();
        $sql = "SELECT t.*, (SELECT COUNT(*) FROM leases WHERE tenant_id = t.id AND lease_status = 'active') as active_leases FROM tenants t WHERE t.company_id = ?";
        $params = [$companyId];
        if (!empty($filters['search'])) {
            $sql .= " AND (t.first_name LIKE ? OR t.last_name LIKE ? OR t.phone LIKE ? OR t.email LIKE ?)";
            $s = "%{$filters['search']}%";
            $params = array_merge($params, [$s, $s, $s, $s]);
        }
        if (isset($filters['is_active'])) { $sql .= " AND t.is_active = ?"; $params[] = $filters['is_active']; }
        $sql .= " ORDER BY t.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create(array $data): int {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO tenants (company_id, first_name, last_name, email, phone, id_number, emergency_contact_name, emergency_contact_phone, date_of_birth, occupation, employer, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['company_id'], $data['first_name'], $data['last_name'],
            $data['email'] ?? null, $data['phone'], $data['id_number'] ?? null,
            $data['emergency_contact_name'] ?? null, $data['emergency_contact_phone'] ?? null,
            $data['date_of_birth'] ?? null, $data['occupation'] ?? null,
            $data['employer'] ?? null, $data['notes'] ?? null
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, int $companyId, array $data): bool {
        $db = Database::getInstance();
        $fields = []; $params = [];
        foreach (['first_name','last_name','email','phone','id_number','emergency_contact_name','emergency_contact_phone','date_of_birth','occupation','employer','notes','avatar','is_active'] as $f) {
            if (array_key_exists($f, $data)) { $fields[] = "$f = ?"; $params[] = $data[$f]; }
        }
        if (empty($fields)) return false;
        $params[] = $id;
        $params[] = $companyId;
        return $db->prepare("UPDATE tenants SET " . implode(', ', $fields) . " WHERE id = ? AND company_id = ?")->execute($params);
    }

    public static function delete(int $id, int $companyId): bool {
        $db = Database::getInstance();
        return $db->prepare("UPDATE tenants SET is_active = 0 WHERE id = ? AND company_id = ?")->execute([$id, $companyId]);
    }

    public static function countByCompany(int $companyId): int {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM tenants WHERE company_id = ? AND is_active = 1");
        $stmt->execute([$companyId]);
        return (int)$stmt->fetchColumn();
    }

    public static function getFullName(array $tenant): string {
        return trim($tenant['first_name'] . ' ' . $tenant['last_name']);
    }
}
