<?php
/**
 * BizConnect Application Entry Point / Router
 */

// Load configuration
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/plans.php';
require_once __DIR__ . '/../src/helpers/functions.php';
require_once __DIR__ . '/../src/helpers/Validator.php';
require_once __DIR__ . '/../src/helpers/FileUpload.php';

// Composer autoloader (PHPMailer etc.)
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Simple routing
$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

// Show landing page for unauthenticated users hitting the root
if (!isLoggedIn() && ($page === 'dashboard' || $page === 'home' || $page === 'landing')) {
    require_once VIEWS_PATH . '/landing.php';
    exit;
}

// Auth check: redirect to login if not logged in (except for auth pages)
if (!isLoggedIn() && $page !== 'auth') {
    redirect('?page=auth&action=login');
}

// If logged in and trying to access auth page, redirect to dashboard
if (isLoggedIn() && $page === 'auth' && $action !== 'logout') {
    redirect('?page=dashboard');
}

// Company middleware: check subscription status for logged-in users
if (isLoggedIn() && $page !== 'auth') {
    $cid = companyId();
    if ($cid) {
        $db = Database::getInstance();
        $stmtSub = $db->prepare("SELECT subscription_end, subscription_plan FROM companies WHERE id = ?");
        $stmtSub->execute([$cid]);
        $companySub = $stmtSub->fetch(PDO::FETCH_ASSOC);

        if ($companySub && !empty($companySub['subscription_end']) && strtotime($companySub['subscription_end']) < time()) {
            // Subscription expired - only allow billing page
            if ($page !== 'billing') {
                setFlashMessage('warning', 'Your subscription has expired. Please renew to continue using BizConnect.');
                redirect('?page=billing');
            }
        }
    }
}

// Route to controller
try {
    switch ($page) {
        case 'auth':
            require_once __DIR__ . '/../src/controllers/AuthController.php';
            break;
        case 'dashboard':
            require_once __DIR__ . '/../src/controllers/DashboardController.php';
            break;
        case 'properties':
            require_once __DIR__ . '/../src/controllers/PropertyController.php';
            break;
        case 'units':
            require_once __DIR__ . '/../src/controllers/UnitController.php';
            break;
        case 'tenants':
            require_once __DIR__ . '/../src/controllers/TenantController.php';
            break;
        case 'leases':
            require_once __DIR__ . '/../src/controllers/LeaseController.php';
            break;
        case 'payments':
            require_once __DIR__ . '/../src/controllers/PaymentController.php';
            break;
        case 'maintenance':
            require_once __DIR__ . '/../src/controllers/MaintenanceController.php';
            break;
        case 'expenses':
            require_once __DIR__ . '/../src/controllers/ExpenseController.php';
            break;
        case 'reports':
            require_once __DIR__ . '/../src/controllers/ReportController.php';
            break;
        case 'notifications':
            require_once __DIR__ . '/../src/controllers/NotificationController.php';
            break;
        case 'activity':
            require_once __DIR__ . '/../src/controllers/ActivityController.php';
            break;
        case 'search':
            require_once __DIR__ . '/../src/controllers/SearchController.php';
            break;
        case 'staff':
            require_once __DIR__ . '/../src/controllers/StaffController.php';
            break;
        case 'settings':
            require_once __DIR__ . '/../src/controllers/SettingsController.php';
            break;
        case 'billing':
            require_once __DIR__ . '/../src/controllers/BillingController.php';
            break;
        case 'support':
            require_once __DIR__ . '/../src/controllers/SupportController.php';
            break;
        case 'onboarding':
            require_once __DIR__ . '/../src/controllers/OnboardingController.php';
            break;
        case 'calendar':
            require_once __DIR__ . '/../src/controllers/CalendarController.php';
            break;
        case 'documents':
            require_once __DIR__ . '/../src/controllers/DocumentController.php';
            break;
        default:
            http_response_code(404);
            require_once __DIR__ . '/../views/errors/404.php';
            break;
    }
} catch (\PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    if (APP_ENV === 'development') {
        echo '<h3>Database Error</h3><pre>' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</pre>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8') . '</pre>';
    } else {
        http_response_code(500);
        require_once __DIR__ . '/../views/errors/500.php';
    }
} catch (\Throwable $e) {
    error_log('Application error: ' . $e->getMessage());
    if (APP_ENV === 'development') {
        echo '<h3>Application Error</h3><pre>' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</pre>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8') . '</pre>';
    } else {
        http_response_code(500);
        require_once __DIR__ . '/../views/errors/500.php';
    }
}
