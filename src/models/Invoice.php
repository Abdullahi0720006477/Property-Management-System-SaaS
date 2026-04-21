<?php
class Invoice {
    public static function findById(int $id): ?array {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT i.*, c.name as company_name, c.email as company_email FROM invoices i JOIN companies c ON i.company_id = c.id WHERE i.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function getByCompany(int $companyId): array {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM invoices WHERE company_id = ? ORDER BY created_at DESC");
        $stmt->execute([$companyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAll(array $filters = []): array {
        $db = Database::getInstance();
        $sql = "SELECT i.*, c.name as company_name FROM invoices i JOIN companies c ON i.company_id = c.id WHERE 1=1";
        $params = [];
        if (!empty($filters['status'])) { $sql .= " AND i.status = ?"; $params[] = $filters['status']; }
        if (!empty($filters['company_id'])) { $sql .= " AND i.company_id = ?"; $params[] = $filters['company_id']; }
        $sql .= " ORDER BY i.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create(array $data): int {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO invoices (company_id, invoice_number, amount, tax_amount, total_amount, currency, status, due_date, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$data['company_id'], $data['invoice_number'], $data['amount'], $data['tax_amount'] ?? 0, $data['total_amount'], $data['currency'] ?? 'KES', $data['status'] ?? 'draft', $data['due_date'], $data['notes'] ?? null]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $db = Database::getInstance();
        $fields = []; $params = [];
        foreach (['status','paid_date','payment_method','payment_reference','notes'] as $f) {
            if (array_key_exists($f, $data)) { $fields[] = "$f = ?"; $params[] = $data[$f]; }
        }
        if (empty($fields)) return false;
        $params[] = $id;
        return $db->prepare("UPDATE invoices SET " . implode(', ', $fields) . " WHERE id = ?")->execute($params);
    }

    public static function generateNumber(): string {
        $db = Database::getInstance();
        $year = date('Y');
        $stmt = $db->prepare("SELECT COUNT(*) FROM invoices WHERE YEAR(created_at) = ?");
        $stmt->execute([$year]);
        $count = (int)$stmt->fetchColumn() + 1;
        return sprintf("BIZ-INV-%s-%04d", $year, $count);
    }
}
