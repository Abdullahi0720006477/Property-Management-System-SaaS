<?php
class Payment
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id, ?int $companyId = null): ?array
    {
        $sql = "SELECT pay.*, l.unit_id, l.monthly_rent, u.unit_number, p.name as property_name,
                       CONCAT(t.first_name, ' ', t.last_name) as tenant_name, t.email as tenant_email, t.phone as tenant_phone
             FROM payments pay
             JOIN leases l ON pay.lease_id = l.id
             JOIN units u ON l.unit_id = u.id
             JOIN properties p ON u.property_id = p.id
             JOIN tenants t ON pay.tenant_id = t.id
             WHERE pay.id = ?";
        $params = [$id];
        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() ?: null;
    }

    public function getAll(string $search = '', string $status = '', ?int $leaseId = null, ?int $tenantId = null, int $limit = RECORDS_PER_PAGE, int $offset = 0, ?int $companyId = null): array
    {
        $sql = "SELECT pay.*, u.unit_number, p.name as property_name,
                       CONCAT(t.first_name, ' ', t.last_name) as tenant_name
                FROM payments pay
                JOIN leases l ON pay.lease_id = l.id
                JOIN units u ON l.unit_id = u.id
                JOIN properties p ON u.property_id = p.id
                JOIN tenants t ON pay.tenant_id = t.id
                WHERE 1=1";
        $params = [];

        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        if ($search) {
            $sql .= " AND (CONCAT(t.first_name, ' ', t.last_name) LIKE ? OR pay.reference_number LIKE ? OR u.unit_number LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($status) {
            $sql .= " AND pay.status = ?";
            $params[] = $status;
        }
        if ($leaseId) {
            $sql .= " AND pay.lease_id = ?";
            $params[] = $leaseId;
        }
        if ($tenantId) {
            $sql .= " AND pay.tenant_id = ?";
            $params[] = $tenantId;
        }

        $sql .= " ORDER BY pay.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function count(string $search = '', string $status = '', ?int $leaseId = null, ?int $tenantId = null, ?int $companyId = null): int
    {
        $sql = "SELECT COUNT(*) FROM payments pay
                JOIN leases l ON pay.lease_id = l.id
                JOIN tenants t ON pay.tenant_id = t.id
                JOIN units u ON l.unit_id = u.id
                JOIN properties p ON u.property_id = p.id
                WHERE 1=1";
        $params = [];
        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        if ($search) {
            $sql .= " AND (CONCAT(t.first_name, ' ', t.last_name) LIKE ? OR pay.reference_number LIKE ? OR u.unit_number LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        if ($status) { $sql .= " AND pay.status = ?"; $params[] = $status; }
        if ($leaseId) { $sql .= " AND pay.lease_id = ?"; $params[] = $leaseId; }
        if ($tenantId) { $sql .= " AND pay.tenant_id = ?"; $params[] = $tenantId; }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO payments (lease_id, tenant_id, amount, payment_date, due_date, payment_method, reference_number, status, notes, receipt_path, is_auto_paid, auto_paid_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $isAuto = !empty($data['is_auto_paid']) ? 1 : 0;
        $stmt->execute([
            $data['lease_id'], $data['tenant_id'], $data['amount'],
            $data['payment_date'], $data['due_date'],
            $data['payment_method'] ?? 'cash', $data['reference_number'] ?? null,
            $data['status'] ?? 'paid', $data['notes'] ?? null, $data['receipt_path'] ?? null,
            $isAuto,
            $isAuto ? ($data['auto_paid_at'] ?? date('Y-m-d H:i:s')) : null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [];
        foreach (['amount', 'payment_date', 'due_date', 'payment_method', 'reference_number', 'status', 'notes', 'receipt_path', 'is_auto_paid', 'auto_paid_at'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }
        if (empty($fields)) return false;
        $params[] = $id;
        $stmt = $this->db->prepare("UPDATE payments SET " . implode(', ', $fields) . " WHERE id = ?");
        return $stmt->execute($params);
    }

    public function delete(int $id, ?int $companyId = null): bool
    {
        if ($companyId) {
            $stmt = $this->db->prepare(
                "DELETE pay FROM payments pay
                 JOIN leases l ON pay.lease_id = l.id
                 JOIN units u ON l.unit_id = u.id
                 JOIN properties p ON u.property_id = p.id
                 WHERE pay.id = ? AND p.company_id = ?"
            );
            return $stmt->execute([$id, $companyId]);
        }
        $stmt = $this->db->prepare("DELETE FROM payments WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getMonthlyRevenue(int $months = 12, ?int $companyId = null): array
    {
        $sql = "SELECT DATE_FORMAT(pay.payment_date, '%Y-%m') as month, SUM(pay.amount) as total
             FROM payments pay";
        $params = [];
        if ($companyId) {
            $sql .= " JOIN leases l ON pay.lease_id = l.id
                       JOIN units u ON l.unit_id = u.id
                       JOIN properties p ON u.property_id = p.id";
        }
        $sql .= " WHERE pay.status IN ('paid', 'partial')
             AND pay.payment_date >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)";
        $params[] = $months;
        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        $sql .= " GROUP BY month ORDER BY month";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getCurrentMonthRevenue(?int $companyId = null): float
    {
        $sql = "SELECT COALESCE(SUM(pay.amount), 0) FROM payments pay";
        $params = [];
        if ($companyId) {
            $sql .= " JOIN leases l ON pay.lease_id = l.id
                       JOIN units u ON l.unit_id = u.id
                       JOIN properties p ON u.property_id = p.id";
        }
        $sql .= " WHERE pay.status IN ('paid', 'partial')
             AND MONTH(pay.payment_date) = MONTH(CURDATE())
             AND YEAR(pay.payment_date) = YEAR(CURDATE())";
        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (float) $stmt->fetchColumn();
    }

    public function getOverduePayments(?int $companyId = null): array
    {
        $sql = "SELECT pay.*, u.unit_number, p.name as property_name,
                       CONCAT(t.first_name, ' ', t.last_name) as tenant_name, t.phone as tenant_phone
             FROM payments pay
             JOIN leases l ON pay.lease_id = l.id
             JOIN units u ON l.unit_id = u.id
             JOIN properties p ON u.property_id = p.id
             JOIN tenants t ON pay.tenant_id = t.id
             WHERE pay.status IN ('overdue', 'pending') AND pay.due_date < CURDATE()";
        $params = [];
        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        $sql .= " ORDER BY pay.due_date";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getOverdueCount(?int $companyId = null): int
    {
        $sql = "SELECT COUNT(*) FROM payments pay";
        $params = [];
        if ($companyId) {
            $sql .= " JOIN leases l ON pay.lease_id = l.id
                       JOIN units u ON l.unit_id = u.id
                       JOIN properties p ON u.property_id = p.id";
        }
        $sql .= " WHERE pay.status IN ('overdue', 'pending') AND pay.due_date < CURDATE()";
        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function getTenantPayments(int $tenantId, int $limit = 50, ?int $companyId = null): array
    {
        $sql = "SELECT pay.*, u.unit_number, p.name as property_name
             FROM payments pay
             JOIN leases l ON pay.lease_id = l.id
             JOIN units u ON l.unit_id = u.id
             JOIN properties p ON u.property_id = p.id
             WHERE pay.tenant_id = ?";
        $params = [$tenantId];
        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        $sql .= " ORDER BY pay.payment_date DESC LIMIT ?";
        $params[] = $limit;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getIncomeByProperty(string $startDate = '', string $endDate = '', ?int $companyId = null): array
    {
        $sql = "SELECT p.id, p.name as property_name, COALESCE(SUM(pay.amount), 0) as total_income
                FROM properties p
                LEFT JOIN units u ON p.id = u.property_id
                LEFT JOIN leases l ON u.id = l.unit_id
                LEFT JOIN payments pay ON l.id = pay.lease_id AND pay.status IN ('paid', 'partial')";
        $params = [];
        if ($startDate) { $sql .= " AND pay.payment_date >= ?"; $params[] = $startDate; }
        if ($endDate) { $sql .= " AND pay.payment_date <= ?"; $params[] = $endDate; }
        $sql .= " WHERE p.is_active = 1";
        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        $sql .= " GROUP BY p.id, p.name ORDER BY total_income DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Auto-mark pending payments as overdue if due_date has passed
     */
    public function markOverduePayments(): int
    {
        $stmt = $this->db->prepare(
            "UPDATE payments SET status = 'overdue' WHERE status = 'pending' AND due_date < CURDATE()"
        );
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Mark a specific payment as auto-paid
     */
    public function markAsAutoPaid(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE payments SET status = 'paid', is_auto_paid = 1, auto_paid_at = NOW(), payment_method = 'auto'
             WHERE id = ? AND status IN ('pending','overdue')"
        );
        return $stmt->execute([$id]) && $stmt->rowCount() > 0;
    }

    /**
     * Auto-generate pending rent payments for all active leases that don't
     * yet have a payment record for the current month.
     * Returns array of newly created payment IDs with their lease data.
     */
    public function autoGenerateMonthlyRents(): array
    {
        $thisMonth = date('Y-m');
        $dueDay    = (int)($_ENV['RENT_DUE_DAY'] ?? 5);
        $dueDate   = date('Y-m-') . str_pad($dueDay, 2, '0', STR_PAD_LEFT);

        $stmt = $this->db->prepare(
            "SELECT l.id as lease_id, l.tenant_id, l.monthly_rent,
                    u.unit_number, p.name as property_name, p.company_id,
                    CONCAT(t.first_name,' ',t.last_name) as tenant_name, t.email as tenant_email
             FROM leases l
             JOIN units u ON l.unit_id = u.id
             JOIN properties p ON u.property_id = p.id
             JOIN tenants t ON l.tenant_id = t.id
             WHERE l.lease_status = 'active'
               AND l.end_date >= CURDATE()
               AND l.id NOT IN (
                   SELECT lease_id FROM payments
                   WHERE DATE_FORMAT(due_date,'%Y-%m') = ?
               )"
        );
        $stmt->execute([$thisMonth]);
        $leases = $stmt->fetchAll();

        $created = [];
        foreach ($leases as $lease) {
            $refNumber = 'AUTO-' . strtoupper(date('Ym')) . '-' . $lease['lease_id'];
            $payId = $this->create([
                'lease_id'       => $lease['lease_id'],
                'tenant_id'      => $lease['tenant_id'],
                'amount'         => $lease['monthly_rent'],
                'payment_date'   => date('Y-m-d'),
                'due_date'       => $dueDate,
                'payment_method' => 'auto',
                'reference_number' => $refNumber,
                'status'         => 'paid',
                'notes'          => 'Automatically generated monthly rent — ' . date('F Y'),
                'is_auto_paid'   => 1,
                'auto_paid_at'   => date('Y-m-d H:i:s'),
            ]);
            $created[] = array_merge($lease, ['payment_id' => $payId, 'reference_number' => $refNumber]);
        }
        return $created;
    }

    /**
     * Get overdue payments with tenant email for notification
     */
    public function getOverdueWithEmail(?int $companyId = null): array
    {
        $sql = "SELECT pay.*, u.unit_number, p.name as property_name,
                       CONCAT(t.first_name, ' ', t.last_name) as tenant_name,
                       t.email as tenant_email, t.phone as tenant_phone
             FROM payments pay
             JOIN leases l ON pay.lease_id = l.id
             JOIN units u ON l.unit_id = u.id
             JOIN properties p ON u.property_id = p.id
             JOIN tenants t ON pay.tenant_id = t.id
             WHERE pay.status = 'overdue' AND t.email IS NOT NULL AND t.email != ''";
        $params = [];
        if ($companyId) {
            $sql .= " AND p.company_id = ?";
            $params[] = $companyId;
        }
        $sql .= " ORDER BY pay.due_date";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
