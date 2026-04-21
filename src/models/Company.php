<?php
class Company {
    public static function findById(int $id): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM companies WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function findBySlug(string $slug): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM companies WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function getAll(array $filters = []): array {
        $db = Database::getInstance();
        $sql = "SELECT c.*, (SELECT COUNT(*) FROM users WHERE company_id = c.id AND is_active = 1) as user_count, (SELECT COUNT(*) FROM properties WHERE company_id = c.id AND is_active = 1) as property_count FROM companies c WHERE 1=1";
        $params = [];
        if (!empty($filters['status'])) { $sql .= " AND c.subscription_status = ?"; $params[] = $filters['status']; }
        if (!empty($filters['plan'])) { $sql .= " AND c.subscription_plan = ?"; $params[] = $filters['plan']; }
        if (!empty($filters['search'])) { $sql .= " AND (c.name LIKE ? OR c.email LIKE ?)"; $params[] = "%{$filters['search']}%"; $params[] = "%{$filters['search']}%"; }
        $sql .= " ORDER BY c.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create(array $data): int {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO companies (name, slug, email, phone, address, city, country, subscription_plan, subscription_status, subscription_start, subscription_end, max_properties, max_users) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['name'], $data['slug'], $data['email'], $data['phone'] ?? null,
            $data['address'] ?? null, $data['city'] ?? null, $data['country'] ?? 'Kenya',
            $data['subscription_plan'] ?? 'trial', $data['subscription_status'] ?? 'active',
            $data['subscription_start'] ?? date('Y-m-d'),
            $data['subscription_end'] ?? date('Y-m-d', strtotime('+14 days')),
            $data['max_properties'] ?? 2, $data['max_users'] ?? 2
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $db = Database::getInstance();
        $fields = [];
        $params = [];
        foreach (['name','slug','email','phone','address','city','country','logo','subscription_plan','subscription_status','subscription_start','subscription_end','max_properties','max_users','billing_cycle','is_active','settings'] as $f) {
            if (array_key_exists($f, $data)) { $fields[] = "$f = ?"; $params[] = $data[$f]; }
        }
        if (empty($fields)) return false;
        $params[] = $id;
        $stmt = $db->prepare("UPDATE companies SET " . implode(', ', $fields) . " WHERE id = ?");
        return $stmt->execute($params);
    }

    public static function countByStatus(): array {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT subscription_status, COUNT(*) as cnt FROM companies GROUP BY subscription_status");
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public static function countByPlan(): array {
        $db = Database::getInstance();
        $stmt = $db->query("SELECT subscription_plan, COUNT(*) as cnt FROM companies GROUP BY subscription_plan");
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public static function generateSlug(string $name): string {
        $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $name), '-'));
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM companies WHERE slug = ?");
        $stmt->execute([$slug]);
        if ($stmt->fetchColumn() > 0) {
            $slug .= '-' . substr(uniqid(), -4);
        }
        return $slug;
    }

    public static function getTotalCount(): int {
        $db = Database::getInstance();
        return (int)$db->query("SELECT COUNT(*) FROM companies")->fetchColumn();
    }
}
