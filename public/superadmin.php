<?php
/**
 * BizConnect Super Admin Panel Entry Point
 */
session_name('bizconnect_sa');
session_start();

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/plans.php';
require_once __DIR__ . '/../src/helpers/functions.php';

$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// SA Auth check
if (!isset($_SESSION['sa_id']) && $page !== 'auth') {
    header('Location: superadmin.php?page=auth&action=login');
    exit;
}

try {
    switch ($page) {
        case 'auth':
            require_once __DIR__ . '/../src/controllers/superadmin/SAAuthController.php';
            break;
        case 'dashboard':
            require_once __DIR__ . '/../src/controllers/superadmin/SADashboardController.php';
            break;
        case 'companies':
            require_once __DIR__ . '/../src/controllers/superadmin/SACompanyController.php';
            break;
        case 'subscriptions':
            require_once __DIR__ . '/../src/controllers/superadmin/SASubscriptionController.php';
            break;
        case 'invoices':
            require_once __DIR__ . '/../src/controllers/superadmin/SAInvoiceController.php';
            break;
        case 'support':
            require_once __DIR__ . '/../src/controllers/superadmin/SASupportController.php';
            break;
        case 'announcements':
            require_once __DIR__ . '/../src/controllers/superadmin/SAAnnouncementController.php';
            break;
        case 'reports':
            require_once __DIR__ . '/../src/controllers/superadmin/SAReportController.php';
            break;
        default:
            http_response_code(404);
            echo '<h1>404 - Page Not Found</h1>';
            break;
    }
} catch (\Throwable $e) {
    error_log('SA Error: ' . $e->getMessage());
    echo '<h3>Error</h3><pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
}
