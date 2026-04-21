<?php
require_once __DIR__ . '/../../models/Company.php';
require_once __DIR__ . '/../../models/Invoice.php';
require_once __DIR__ . '/../../models/Subscription.php';

$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        $filters = [];
        if (!empty($_GET['search'])) $filters['search'] = $_GET['search'];
        if (!empty($_GET['plan'])) $filters['plan'] = $_GET['plan'];
        if (!empty($_GET['status'])) $filters['status'] = $_GET['status'];
        $companies = Company::getAll($filters);
        $pageTitle = 'Companies';
        require_once VIEWS_PATH . '/superadmin/companies/index.php';
        break;

    case 'show':
        $id = (int)($_GET['id'] ?? 0);
        $company = Company::findById($id);
        if (!$company) {
            echo '<h3>Company not found</h3>';
            exit;
        }
        $db = Database::getInstance();
        // Usage stats
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE company_id = ? AND is_active = 1");
        $stmt->execute([$id]);
        $userCount = (int)$stmt->fetchColumn();
        $stmt = $db->prepare("SELECT COUNT(*) FROM properties WHERE company_id = ? AND is_active = 1");
        $stmt->execute([$id]);
        $propertyCount = (int)$stmt->fetchColumn();
        $stmt = $db->prepare("SELECT COUNT(*) FROM units WHERE property_id IN (SELECT id FROM properties WHERE company_id = ?)");
        $stmt->execute([$id]);
        $unitCount = (int)$stmt->fetchColumn();
        // Subscription history
        $subscriptionHistory = Subscription::getByCompany($id);
        // Recent invoices
        $invoices = Invoice::getByCompany($id);
        $pageTitle = $company['name'];
        require_once VIEWS_PATH . '/superadmin/companies/show.php';
        break;

    case 'edit':
        $id = (int)($_GET['id'] ?? 0);
        $company = Company::findById($id);
        if (!$company) {
            echo '<h3>Company not found</h3>';
            exit;
        }
        if (isPost()) {
            $data = [
                'name' => postData('name'),
                'email' => postData('email'),
                'subscription_plan' => postData('subscription_plan'),
                'subscription_status' => postData('subscription_status'),
                'max_properties' => (int)postData('max_properties'),
                'max_users' => (int)postData('max_users'),
                'subscription_end' => postData('subscription_end') ?: null,
            ];
            Company::update($id, $data);
            $success = 'Company updated successfully.';
            $company = Company::findById($id);
        }
        $pageTitle = 'Edit ' . $company['name'];
        require_once VIEWS_PATH . '/superadmin/companies/edit.php';
        break;

    default:
        header('Location: superadmin.php?page=companies');
        exit;
}
