<?php
class Notification
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getByUser(int $userId, int $limit = 50): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?"
        );
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll();
    }

    public function getUnreadCount(int $userId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function create(int $userId, string $title, string $message, string $type = 'general'): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$userId, $title, $message, $type]);
        return (int) $this->db->lastInsertId();
    }

    public function markAsRead(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }

    public function markAllAsRead(int $userId): bool
    {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
        return $stmt->execute([$userId]);
    }

    public function delete(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM notifications WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }

    /**
     * Auto-generate payment due notifications (5 days before month end)
     * Generates one notification per tenant per month
     */
    public function generatePaymentDueNotifications(): void
    {
        // Only run if we're within 5 days of the end of the month
        $daysUntilMonthEnd = (int) date('t') - (int) date('j');
        if ($daysUntilMonthEnd > 5) return;

        $nextMonth = date('F Y', strtotime('first day of next month'));
        $dueDate = date('F 1, Y', strtotime('first day of next month'));

        $stmt = $this->db->prepare(
            "SELECT l.id AS lease_id, l.tenant_id, l.monthly_rent, u.unit_number, p.name AS property_name
             FROM leases l
             JOIN units u ON l.unit_id = u.id
             JOIN properties p ON u.property_id = p.id
             WHERE l.lease_status = 'active'
             AND l.tenant_id NOT IN (
                 SELECT n.user_id FROM notifications n
                 WHERE n.type = 'payment_due'
                 AND n.created_at >= DATE_SUB(CURDATE(), INTERVAL 25 DAY)
                 AND n.title LIKE ?
             )"
        );
        $stmt->execute(['%' . $nextMonth . '%']);
        $leases = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($leases as $lease) {
            $title = "Rent Payment Due - {$nextMonth}";
            $message = "Your rent of KES " . number_format($lease['monthly_rent'], 2)
                     . " for Unit {$lease['unit_number']} at {$lease['property_name']}"
                     . " is due on {$dueDate}. Please make your payment on time.";

            $this->create(
                (int) $lease['tenant_id'],
                $title,
                $message,
                'payment_due'
            );
        }
    }

    /**
     * Auto-generate lease expiry notifications (30 days before end date)
     * One notification per lease
     */
    public function generateLeaseExpiryNotifications(): void
    {
        $stmt = $this->db->prepare(
            "SELECT l.id, l.tenant_id, l.end_date, u.unit_number, p.name AS property_name
             FROM leases l
             JOIN units u ON l.unit_id = u.id
             JOIN properties p ON u.property_id = p.id
             WHERE l.lease_status = 'active'
             AND DATEDIFF(l.end_date, CURDATE()) BETWEEN 0 AND 30
             AND NOT EXISTS (
                 SELECT 1 FROM notifications n
                 WHERE n.user_id = l.tenant_id
                 AND n.type = 'lease_expiry'
                 AND n.title LIKE CONCAT('%', u.unit_number, '%')
                 AND n.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
             )"
        );
        $stmt->execute();
        $expiring = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($expiring as $lease) {
            $daysLeft = max(0, (int) ((strtotime($lease['end_date']) - time()) / 86400));
            $title = "Lease Expiring Soon - {$lease['property_name']} Unit {$lease['unit_number']}";
            $message = "Your lease for Unit {$lease['unit_number']} at {$lease['property_name']}"
                     . " expires on " . date('M d, Y', strtotime($lease['end_date']))
                     . " ({$daysLeft} days remaining). Please contact management about renewal.";

            $this->create(
                (int) $lease['tenant_id'],
                $title,
                $message,
                'lease_expiry'
            );
        }
    }
}
