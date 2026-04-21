<?php
class Expense
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id, ?int $companyId = null): ?array
    {
        $sql = "SELECT e.*, p.name as property_name, u.full_name as recorded_by_name
             FROM expenses e
             JOIN properties p ON e.property_id = p.id
             LEFT JOIN users u ON e.recorded_by = u.id
             WHERE e.id = ?";
        $params = [$id];
        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() ?: null;
    }

    public function getAll(string $search = '', ?int $propertyId = null, string $category = '', string $startDate = '', string $endDate = '', int $limit = RECORDS_PER_PAGE, int $offset = 0, ?int $companyId = null): array
    {
        $sql = "SELECT e.*, p.name as property_name, u.full_name as recorded_by_name
                FROM expenses e
                JOIN properties p ON e.property_id = p.id
                LEFT JOIN users u ON e.recorded_by = u.id
                WHERE 1=1";
        $params = [];

        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        if ($search) { $sql .= " AND e.description LIKE ?"; $params[] = "%$search%"; }
        if ($propertyId) { $sql .= " AND e.property_id = ?"; $params[] = $propertyId; }
        if ($category) { $sql .= " AND e.category = ?"; $params[] = $category; }
        if ($startDate) { $sql .= " AND e.expense_date >= ?"; $params[] = $startDate; }
        if ($endDate) { $sql .= " AND e.expense_date <= ?"; $params[] = $endDate; }

        $sql .= " ORDER BY e.expense_date DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function count(string $search = '', ?int $propertyId = null, string $category = '', string $startDate = '', string $endDate = '', ?int $companyId = null): int
    {
        $sql = "SELECT COUNT(*) FROM expenses e JOIN properties p ON e.property_id = p.id WHERE 1=1";
        $params = [];
        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        if ($search) { $sql .= " AND e.description LIKE ?"; $params[] = "%$search%"; }
        if ($propertyId) { $sql .= " AND e.property_id = ?"; $params[] = $propertyId; }
        if ($category) { $sql .= " AND e.category = ?"; $params[] = $category; }
        if ($startDate) { $sql .= " AND e.expense_date >= ?"; $params[] = $startDate; }
        if ($endDate) { $sql .= " AND e.expense_date <= ?"; $params[] = $endDate; }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO expenses (property_id, category, description, amount, expense_date, receipt_path, recorded_by)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['property_id'], $data['category'], $data['description'],
            $data['amount'], $data['expense_date'],
            $data['receipt_path'] ?? null, $data['recorded_by'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function delete(int $id, ?int $companyId = null): bool
    {
        if ($companyId) {
            $stmt = $this->db->prepare(
                "DELETE e FROM expenses e
                 JOIN properties p ON e.property_id = p.id
                 WHERE e.id = ? AND p.company_id = ?"
            );
            return $stmt->execute([$id, $companyId]);
        }
        $stmt = $this->db->prepare("DELETE FROM expenses WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getTotalByProperty(string $startDate = '', string $endDate = '', ?int $companyId = null): array
    {
        $sql = "SELECT p.id, p.name as property_name, COALESCE(SUM(e.amount), 0) as total_expense
                FROM properties p
                LEFT JOIN expenses e ON p.id = e.property_id";
        $params = [];
        $conditions = [];
        if ($startDate) { $conditions[] = "e.expense_date >= ?"; $params[] = $startDate; }
        if ($endDate) { $conditions[] = "e.expense_date <= ?"; $params[] = $endDate; }
        if ($conditions) $sql .= " AND " . implode(' AND ', $conditions);
        $sql .= " WHERE p.is_active = 1";
        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        $sql .= " GROUP BY p.id, p.name ORDER BY total_expense DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getTotalByCategory(string $startDate = '', string $endDate = '', ?int $companyId = null): array
    {
        $sql = "SELECT e.category, SUM(e.amount) as total FROM expenses e
                JOIN properties p ON e.property_id = p.id WHERE 1=1";
        $params = [];
        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        if ($startDate) { $sql .= " AND e.expense_date >= ?"; $params[] = $startDate; }
        if ($endDate) { $sql .= " AND e.expense_date <= ?"; $params[] = $endDate; }
        $sql .= " GROUP BY e.category ORDER BY total DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getMonthlyExpenses(int $months = 12, ?int $companyId = null): array
    {
        $sql = "SELECT DATE_FORMAT(e.expense_date, '%Y-%m') as month, SUM(e.amount) as total
             FROM expenses e
             JOIN properties p ON e.property_id = p.id
             WHERE e.expense_date >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)";
        $params = [$months];
        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        $sql .= " GROUP BY month ORDER BY month";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
