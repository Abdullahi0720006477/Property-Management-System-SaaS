<?php
requireAuth();
requireRole('company_admin');

$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        $pageTitle = 'Welcome to BizConnect';
        // Get current step from session or default to 1
        $step = $_SESSION['onboarding_step'] ?? 1;
        require_once VIEWS_PATH . '/onboarding/wizard.php';
        break;

    case 'save_company':
        // Step 1: Update company profile
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once SRC_PATH . '/models/Company.php';
            $companyId = companyId();
            Company::update($companyId, [
                'name' => trim($_POST['company_name'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'city' => trim($_POST['city'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
            ]);
            // Update session company name
            $_SESSION['company_name'] = trim($_POST['company_name'] ?? $_SESSION['company_name']);
            $_SESSION['onboarding_step'] = 2;
            redirect('?page=onboarding');
        }
        break;

    case 'save_property':
        // Step 2: Create first property
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::getInstance();
            $stmt = $db->prepare("INSERT INTO properties (company_id, name, address, city, type, manager_id, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)");
            $stmt->execute([
                companyId(),
                trim($_POST['property_name'] ?? ''),
                trim($_POST['property_address'] ?? ''),
                trim($_POST['property_city'] ?? ''),
                $_POST['property_type'] ?? 'apartment',
                currentUserId()
            ]);
            $_SESSION['onboarding_property_id'] = $db->lastInsertId();
            $_SESSION['onboarding_step'] = 3;
            redirect('?page=onboarding');
        }
        break;

    case 'save_units':
        // Step 3: Add units to the property
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = Database::getInstance();
            $propertyId = $_SESSION['onboarding_property_id'] ?? null;
            if ($propertyId) {
                $unitNumbers = $_POST['unit_numbers'] ?? [];
                $rents = $_POST['rents'] ?? [];
                for ($i = 0; $i < count($unitNumbers); $i++) {
                    if (!empty(trim($unitNumbers[$i]))) {
                        $stmt = $db->prepare("INSERT INTO units (company_id, property_id, unit_number, rent_amount, status) VALUES (?, ?, ?, ?, 'vacant')");
                        $stmt->execute([companyId(), $propertyId, trim($unitNumbers[$i]), (float)($rents[$i] ?? 0)]);
                    }
                }
            }
            $_SESSION['onboarding_step'] = 4;
            redirect('?page=onboarding');
        }
        break;

    case 'complete':
        // Step 4: Complete onboarding
        unset($_SESSION['onboarding_step'], $_SESSION['onboarding_property_id']);
        setFlashMessage('success', 'Welcome to BizConnect! Your workspace is ready.');
        redirect('?page=dashboard');
        break;

    default:
        redirect('?page=onboarding');
}
