<?php
class SupportTicket {
    public static function findById(int $id): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT st.*, c.name as company_name, u.full_name as user_name, sa.full_name as assigned_name FROM support_tickets st JOIN companies c ON st.company_id = c.id LEFT JOIN users u ON st.user_id = u.id LEFT JOIN super_admins sa ON st.assigned_to = sa.id WHERE st.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function getByCompany(int $companyId): array {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT st.*, u.full_name as user_name FROM support_tickets st LEFT JOIN users u ON st.user_id = u.id WHERE st.company_id = ? ORDER BY st.updated_at DESC");
        $stmt->execute([$companyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAll(array $filters = []): array {
        $db = Database::getInstance();
        $sql = "SELECT st.*, c.name as company_name, u.full_name as user_name, sa.full_name as assigned_name FROM support_tickets st JOIN companies c ON st.company_id = c.id LEFT JOIN users u ON st.user_id = u.id LEFT JOIN super_admins sa ON st.assigned_to = sa.id WHERE 1=1";
        $params = [];
        if (!empty($filters['status'])) { $sql .= " AND st.status = ?"; $params[] = $filters['status']; }
        if (!empty($filters['priority'])) { $sql .= " AND st.priority = ?"; $params[] = $filters['priority']; }
        $sql .= " ORDER BY FIELD(st.priority,'urgent','high','medium','low'), st.updated_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create(array $data): int {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO support_tickets (company_id, user_id, subject, message, priority) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$data['company_id'], $data['user_id'], $data['subject'], $data['message'], $data['priority'] ?? 'medium']);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $db = Database::getInstance();
        $fields = []; $params = [];
        foreach (['status','priority','assigned_to'] as $f) {
            if (array_key_exists($f, $data)) { $fields[] = "$f = ?"; $params[] = $data[$f]; }
        }
        if (empty($fields)) return false;
        $params[] = $id;
        return $db->prepare("UPDATE support_tickets SET " . implode(', ', $fields) . " WHERE id = ?")->execute($params);
    }

    public static function getReplies(int $ticketId): array {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT tr.*, CASE WHEN tr.user_type = 'staff' THEN (SELECT full_name FROM users WHERE id = tr.user_id) ELSE (SELECT full_name FROM super_admins WHERE id = tr.user_id) END as author_name FROM ticket_replies tr WHERE tr.ticket_id = ? ORDER BY tr.created_at ASC");
        $stmt->execute([$ticketId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function addReply(int $ticketId, string $userType, int $userId, string $message): int {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO ticket_replies (ticket_id, user_type, user_id, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$ticketId, $userType, $userId, $message]);
        return (int)$db->lastInsertId();
    }

    public static function countOpen(): int {
        $db = Database::getInstance();
        return (int)$db->query("SELECT COUNT(*) FROM support_tickets WHERE status IN ('open','in_progress','waiting')")->fetchColumn();
    }
}
