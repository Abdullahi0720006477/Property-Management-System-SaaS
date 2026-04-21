<?php
/**
 * Calendar Controller
 * Displays lease, payment, and maintenance events on a FullCalendar view
 */
requireAuth();

$action = $action ?? 'index';

switch ($action) {
    case 'events':
        // Return JSON events for FullCalendar
        header('Content-Type: application/json');
        $db = Database::getInstance();
        $cid = companyId();
        $events = [];

        // Lease events (blue)
        $stmt = $db->prepare("
            SELECT l.id, CONCAT('Lease: ', t.first_name, ' ', t.last_name) AS title,
                   l.start_date AS start, l.end_date AS end
            FROM leases l
            JOIN tenants t ON l.tenant_id = t.id
            WHERE l.company_id = ?
        ");
        $stmt->execute([$cid]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $events[] = [
                'title' => $row['title'],
                'start' => $row['start'],
                'end'   => $row['end'],
                'color' => '#3B82F6',
                'url'   => '?page=leases&action=show&id=' . $row['id'],
            ];
        }

        // Payment events (green=paid, red=overdue, amber=pending)
        $stmt = $db->prepare("
            SELECT p.id, CONCAT('Payment: KES ', FORMAT(p.amount, 0)) AS title,
                   p.due_date AS start, p.status
            FROM payments p
            WHERE p.company_id = ?
        ");
        $stmt->execute([$cid]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $color = match ($row['status']) {
                'paid'    => '#10B981',
                'overdue' => '#EF4444',
                default   => '#F59E0B',
            };
            $events[] = [
                'title' => $row['title'],
                'start' => $row['start'],
                'color' => $color,
                'url'   => '?page=payments&action=show&id=' . $row['id'],
            ];
        }

        // Maintenance request events (orange, open/in_progress only)
        $stmt = $db->prepare("
            SELECT m.id, CONCAT('Maintenance: ', m.title) AS title,
                   DATE(m.created_at) AS start
            FROM maintenance_requests m
            WHERE m.company_id = ? AND m.status IN ('open', 'in_progress')
        ");
        $stmt->execute([$cid]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $events[] = [
                'title' => $row['title'],
                'start' => $row['start'],
                'color' => '#F59E0B',
                'url'   => '?page=maintenance&action=show&id=' . $row['id'],
            ];
        }

        echo json_encode($events);
        exit;

    case 'index':
    default:
        $pageTitle = 'Calendar';
        require_once VIEWS_PATH . '/calendar/index.php';
        break;
}
