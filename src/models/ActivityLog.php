<?php
class ActivityLog
{
    public static function log(string $action, string $entityType, ?int $entityId = null, string $description = ''): void
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO activity_logs (user_id, company_id, action, entity_type, entity_id, description, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['user_id'] ?? null,
            $_SESSION['company_id'] ?? null,
            $action,
            $entityType,
            $entityId,
            $description,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
    }

    public static function getRecent(int $limit = 100, ?string $actionFilter = null, ?int $userFilter = null, ?string $dateFrom = null, ?string $dateTo = null, ?int $companyId = null): array
    {
        $db = Database::getInstance();
        $sql = "SELECT al.*, u.full_name as user_name FROM activity_logs al LEFT JOIN users u ON al.user_id = u.id WHERE 1=1";
        $params = [];

        if ($companyId) {
            $sql .= " AND al.company_id = ?";
            $params[] = $companyId;
        }
        if ($actionFilter) {
            $sql .= " AND al.action = ?";
            $params[] = $actionFilter;
        }
        if ($userFilter) {
            $sql .= " AND al.user_id = ?";
            $params[] = $userFilter;
        }
        if ($dateFrom) {
            $sql .= " AND DATE(al.created_at) >= ?";
            $params[] = $dateFrom;
        }
        if ($dateTo) {
            $sql .= " AND DATE(al.created_at) <= ?";
            $params[] = $dateTo;
        }

        $sql .= " ORDER BY al.created_at DESC LIMIT ?";
        $params[] = $limit;

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getActionTypes(?int $companyId = null): array
    {
        $db = Database::getInstance();
        if ($companyId) {
            $stmt = $db->prepare("SELECT DISTINCT action FROM activity_logs WHERE company_id = ? ORDER BY action");
            $stmt->execute([$companyId]);
        } else {
            $stmt = $db->query("SELECT DISTINCT action FROM activity_logs ORDER BY action");
        }
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
