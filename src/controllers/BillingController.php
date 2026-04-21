<?php
/**
 * Billing Controller
 * Manage subscription billing, plans, and invoices.
 * Restricted to company_admin role.
 */
require_once SRC_PATH . '/models/Company.php';
require_once SRC_PATH . '/models/Invoice.php';
require_once SRC_PATH . '/helpers/PlanLimits.php';

requireAuth();
requireRole('company_admin');

$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        $company = Company::findById(companyId());
        $usage = PlanLimits::getUsage(companyId());
        $invoices = Invoice::getByCompany(companyId());

        $currentPlanKey = $company['subscription_plan'] ?? 'trial';
        $plans = SUBSCRIPTION_PLANS;
        $currentPlan = $plans[$currentPlanKey] ?? $plans['trial'];

        $pageTitle = 'Billing';
        require_once VIEWS_PATH . '/billing/index.php';
        break;

    case 'plans':
        $company = Company::findById(companyId());
        $currentPlanKey = $company['subscription_plan'] ?? 'trial';
        $plans = SUBSCRIPTION_PLANS;

        $pageTitle = 'Subscription Plans';
        require_once VIEWS_PATH . '/billing/plans.php';
        break;

    case 'invoice_detail':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$id) {
            setFlashMessage('error', 'Invalid invoice.');
            redirect('?page=billing');
        }

        $invoice = Invoice::findById($id);
        if (!$invoice || (int)$invoice['company_id'] !== companyId()) {
            setFlashMessage('error', 'Invoice not found.');
            redirect('?page=billing');
        }

        $company = Company::findById(companyId());
        $plans = SUBSCRIPTION_PLANS;
        $currentPlanKey = $company['subscription_plan'] ?? 'trial';
        $currentPlan = $plans[$currentPlanKey] ?? $plans['trial'];

        $pageTitle = 'Invoice #' . e($invoice['invoice_number']);
        require_once VIEWS_PATH . '/billing/invoice_detail.php';
        break;

    default:
        redirect('?page=billing');
}
