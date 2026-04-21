<?php
require_once __DIR__ . '/../../models/Company.php';
require_once __DIR__ . '/../../models/Invoice.php';

$db = Database::getInstance();

// MRR calculation
$planDistribution = Company::countByPlan();
$mrr = 0;
foreach ($planDistribution as $plan => $count) {
    if ($plan !== 'trial' && isset(SUBSCRIPTION_PLANS[$plan])) {
        $mrr += SUBSCRIPTION_PLANS[$plan]['price_monthly'] * $count;
    }
}

// Total revenue from paid invoices
$stmt = $db->query("SELECT COALESCE(SUM(total_amount), 0) FROM invoices WHERE status = 'paid'");
$totalRevenue = (float)$stmt->fetchColumn();

// Status breakdown
$statusCounts = Company::countByStatus();

// Total companies
$totalCompanies = Company::getTotalCount();

// Revenue by month (last 6 months)
$stmt = $db->query("SELECT DATE_FORMAT(paid_date, '%Y-%m') as month, SUM(total_amount) as total FROM invoices WHERE status = 'paid' AND paid_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH) GROUP BY month ORDER BY month ASC");
$monthlyRevenue = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Reports';
require_once VIEWS_PATH . '/superadmin/reports/index.php';
