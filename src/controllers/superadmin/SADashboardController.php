<?php
require_once __DIR__ . '/../../models/Company.php';
require_once __DIR__ . '/../../models/SupportTicket.php';

$db = Database::getInstance();

// Total companies
$totalCompanies = Company::getTotalCount();

// Company counts by status
$statusCounts = Company::countByStatus();
$activeCount = ($statusCounts['active'] ?? 0);
$trialCount = ($statusCounts['trial'] ?? 0);
$cancelledCount = ($statusCounts['cancelled'] ?? 0);

// MRR calculation: SUM of monthly prices for active non-trial companies
$mrr = 0;
$planDistribution = Company::countByPlan();
foreach ($planDistribution as $plan => $count) {
    if ($plan !== 'trial' && isset(SUBSCRIPTION_PLANS[$plan])) {
        $mrr += SUBSCRIPTION_PLANS[$plan]['price_monthly'] * $count;
    }
}

// New signups this month
$stmt = $db->prepare("SELECT COUNT(*) FROM companies WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())");
$stmt->execute();
$newThisMonth = (int)$stmt->fetchColumn();

// Open support tickets
$openTickets = SupportTicket::countOpen();

// Recent companies (last 10)
$stmt = $db->query("SELECT c.*, (SELECT COUNT(*) FROM users WHERE company_id = c.id AND is_active = 1) as user_count, (SELECT COUNT(*) FROM properties WHERE company_id = c.id AND is_active = 1) as property_count FROM companies c ORDER BY c.created_at DESC LIMIT 10");
$recentCompanies = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Dashboard';
require_once VIEWS_PATH . '/superadmin/dashboard.php';
