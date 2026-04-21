<?php
class Subscription {
    public static function logAction(int $companyId, string $action, ?string $oldPlan, ?string $newPlan, ?float $amount = null, ?string $reference = null, ?string $notes = null, ?int $performedBy = null): int {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO subscription_history (company_id, action, old_plan, new_plan, amount, payment_reference, notes, performed_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$companyId, $action, $oldPlan, $newPlan, $amount, $reference, $notes, $performedBy]);
        return (int)$db->lastInsertId();
    }

    public static function getByCompany(int $companyId): array {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT sh.*, sa.full_name as performed_by_name FROM subscription_history sh LEFT JOIN super_admins sa ON sh.performed_by = sa.id WHERE sh.company_id = ? ORDER BY sh.created_at DESC");
        $stmt->execute([$companyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
