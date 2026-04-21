<?php
require_once __DIR__ . '/../../models/Company.php';
require_once __DIR__ . '/../../models/Subscription.php';

$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        $planDistribution = Company::countByPlan();
        $db = Database::getInstance();
        // All active subscriptions
        $stmt = $db->query("SELECT c.id, c.name, c.email, c.subscription_plan, c.subscription_status, c.subscription_start, c.subscription_end, c.billing_cycle, (SELECT COUNT(*) FROM users WHERE company_id = c.id AND is_active = 1) as user_count FROM companies c WHERE c.subscription_status IN ('active','trial') ORDER BY c.subscription_plan, c.name");
        $subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $pageTitle = 'Subscriptions';
        require_once VIEWS_PATH . '/superadmin/subscriptions/index.php';
        break;

    case 'manage':
        $id = (int)($_GET['id'] ?? 0);
        $company = Company::findById($id);
        if (!$company) {
            echo '<h3>Company not found</h3>';
            exit;
        }
        if (isPost()) {
            $newPlan = postData('new_plan');
            $notes = postData('notes');
            $oldPlan = $company['subscription_plan'];
            if ($newPlan && $newPlan !== $oldPlan) {
                $planConfig = SUBSCRIPTION_PLANS[$newPlan] ?? null;
                if ($planConfig) {
                    Company::update($id, [
                        'subscription_plan' => $newPlan,
                        'max_properties' => $planConfig['max_properties'],
                        'max_users' => $planConfig['max_users'],
                        'subscription_status' => 'active',
                    ]);
                    Subscription::logAction($id, 'plan_change', $oldPlan, $newPlan, $planConfig['price_monthly'], null, $notes, $_SESSION['sa_id']);
                    $success = 'Subscription plan updated successfully.';
                    $company = Company::findById($id);
                }
            }
        }
        $pageTitle = 'Manage Subscription - ' . $company['name'];
        require_once VIEWS_PATH . '/superadmin/subscriptions/manage.php';
        break;

    default:
        header('Location: superadmin.php?page=subscriptions');
        exit;
}
