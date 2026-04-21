<?php
/**
 * Dashboard Controller
 */
require_once SRC_PATH . '/models/Property.php';
require_once SRC_PATH . '/models/Unit.php';
require_once SRC_PATH . '/models/Lease.php';
require_once SRC_PATH . '/models/Payment.php';
require_once SRC_PATH . '/models/MaintenanceRequest.php';
require_once SRC_PATH . '/models/Expense.php';
require_once SRC_PATH . '/models/Notification.php';

requireAuth();

$cid  = companyId();
$role = currentUserRole();
$pageTitle = 'Dashboard';

// ── Tenant Dashboard ───────────────────────────────────────────────────────
if (isTenant()) {
    $db = Database::getInstance();

    // Find tenant profile by matching login email → tenants table
    $userEmail = $_SESSION['user_email'] ?? '';
    $stmtT = $db->prepare(
        "SELECT t.* FROM tenants t
         WHERE t.email = ? AND t.company_id = ? AND t.is_active = 1
         LIMIT 1"
    );
    $stmtT->execute([$userEmail, $cid]);
    $tenantProfile = $stmtT->fetch() ?: null;

    $activeLease    = null;
    $recentPayments = [];
    $myRequests     = [];

    if ($tenantProfile) {
        $tid = $tenantProfile['id'];
        $_SESSION['tenant_profile_id'] = $tid; // cache for sidebar link

        // Active lease
        $stmtL = $db->prepare(
            "SELECT l.*, u.unit_number, u.id as unit_id,
                    p.name as property_name, p.address as property_address
             FROM leases l
             JOIN units u ON l.unit_id = u.id
             JOIN properties p ON u.property_id = p.id
             WHERE l.tenant_id = ? AND l.lease_status = 'active'
             ORDER BY l.start_date DESC LIMIT 1"
        );
        $stmtL->execute([$tid]);
        $activeLease = $stmtL->fetch() ?: null;

        // Recent payments (last 10)
        $stmtP = $db->prepare(
            "SELECT pay.*, u.unit_number, p.name as property_name
             FROM payments pay
             JOIN leases l ON pay.lease_id = l.id
             JOIN units u ON l.unit_id = u.id
             JOIN properties p ON u.property_id = p.id
             WHERE pay.tenant_id = ?
             ORDER BY pay.created_at DESC LIMIT 10"
        );
        $stmtP->execute([$tid]);
        $recentPayments = $stmtP->fetchAll();

        // Maintenance requests
        $stmtM = $db->prepare(
            "SELECT mr.*, u.unit_number
             FROM maintenance_requests mr
             JOIN units u ON mr.unit_id = u.id
             WHERE mr.tenant_id = ?
             ORDER BY mr.created_at DESC LIMIT 10"
        );
        $stmtM->execute([$tid]);
        $myRequests = $stmtM->fetchAll();
    }

    require_once VIEWS_PATH . '/dashboard/tenant.php';
    return;
}

// ── Staff / Admin Dashboard ────────────────────────────────────────────────

// Auto-expire leases
$leaseModel = new Lease();
$leaseModel->expireLeases();

// Auto-mark overdue payments
$paymentModel_auto = new Payment();
$paymentModel_auto->markOverduePayments();

// Auto-generate notifications
$notificationModel_auto = new Notification();
$notificationModel_auto->generatePaymentDueNotifications();
$notificationModel_auto->generateLeaseExpiryNotifications();

$propertyModel   = new Property();
$unitModel       = new Unit();
$paymentModel    = new Payment();
$maintenanceModel = new MaintenanceRequest();

$totalProperties = $propertyModel->getTotalCount($cid);
$totalUnits      = $unitModel->getTotalCount($cid);
$occupiedUnits   = $unitModel->getOccupiedCount($cid);
$vacantUnits     = $unitModel->getVacantCount($cid);
$occupancyRate   = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100, 1) : 0;
$monthlyRevenue  = $paymentModel->getCurrentMonthRevenue($cid);
$overdueCount    = $paymentModel->getOverdueCount($cid);
$maintenanceOpen = $maintenanceModel->getOpenCount($cid);
$recentMaintenance = $maintenanceModel->getRecent(5, $cid);
$revenueData     = $paymentModel->getMonthlyRevenue(12, $cid);
$expiringLeases  = $leaseModel->getExpiringLeases(30, $cid);

require_once VIEWS_PATH . '/dashboard/admin.php';
