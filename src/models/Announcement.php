<?php
class Announcement {
    public static function getActive(?string $plan = null): array {
        $db = Database::getInstance();
        $sql = "SELECT * FROM announcements WHERE is_active = 1 AND (starts_at IS NULL OR starts_at <= NOW()) AND (ends_at IS NULL OR ends_at >= NOW())";
        $params = [];
        if ($plan) {
            $sql .= " AND (target = 'all' OR target = ?)";
            $params[] = $plan;
        }
        $sql .= " ORDER BY created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAll(): array {
        $db = Database::getInstance();
        return $db->query("SELECT a.*, sa.full_name as author FROM announcements a LEFT JOIN super_admins sa ON a.created_by = sa.id ORDER BY a.created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create(array $data): int {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO announcements (title, message, type, target, is_active, starts_at, ends_at, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$data['title'], $data['message'], $data['type'] ?? 'info', $data['target'] ?? 'all', $data['is_active'] ?? 1, $data['starts_at'] ?? null, $data['ends_at'] ?? null, $data['created_by'] ?? null]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $db = Database::getInstance();
        $fields = []; $params = [];
        foreach (['title','message','type','target','is_active','starts_at','ends_at'] as $f) {
            if (array_key_exists($f, $data)) { $fields[] = "$f = ?"; $params[] = $data[$f]; }
        }
        if (empty($fields)) return false;
        $params[] = $id;
        return $db->prepare("UPDATE announcements SET " . implode(', ', $fields) . " WHERE id = ?")->execute($params);
    }

    public static function delete(int $id): bool {
        $db = Database::getInstance();
        return $db->prepare("DELETE FROM announcements WHERE id = ?")->execute([$id]);
    }
}
