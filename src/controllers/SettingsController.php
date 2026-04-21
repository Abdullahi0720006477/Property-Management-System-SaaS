<?php
/**
 * Settings Controller
 */
requireAuth();

$action = $_GET['action'] ?? 'company';

switch ($action) {
    case 'company':
        // Company profile - company_admin only
        requireRole('company_admin');
        require_once SRC_PATH . '/models/Company.php';
        $company = Company::findById(companyId());

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken()) {
                setFlashMessage('error', 'Invalid security token. Please try again.');
                redirect('?page=settings&action=company');
            }

            Company::update(companyId(), [
                'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'city' => trim($_POST['city'] ?? ''),
                'country' => trim($_POST['country'] ?? 'Kenya'),
            ]);

            // Handle logo upload if provided
            if (!empty($_FILES['logo']['name'])) {
                require_once SRC_PATH . '/helpers/FileUpload.php';
                $upload = new FileUpload();
                $logoPath = $upload->upload($_FILES['logo'], 'logos');
                if ($logoPath) {
                    Company::update(companyId(), ['logo' => $logoPath]);
                }
            }

            $_SESSION['company_name'] = trim($_POST['name'] ?? $_SESSION['company_name']);
            setFlashMessage('success', 'Company profile updated.');
            redirect('?page=settings&action=company');
        }

        $pageTitle = 'Company Settings';
        require_once VIEWS_PATH . '/settings/company.php';
        break;

    case 'preferences':
        // App preferences - company_admin only
        requireRole('company_admin');
        require_once SRC_PATH . '/models/Company.php';
        $company = Company::findById(companyId());
        $settings = json_decode($company['settings'] ?? '{}', true) ?: [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken()) {
                setFlashMessage('error', 'Invalid security token. Please try again.');
                redirect('?page=settings&action=preferences');
            }

            $newSettings = [
                'currency' => $_POST['currency'] ?? 'KES',
                'date_format' => $_POST['date_format'] ?? 'Y-m-d',
                'timezone' => $_POST['timezone'] ?? 'Africa/Nairobi',
                'late_fee_percentage' => (float)($_POST['late_fee_percentage'] ?? 0),
                'late_fee_grace_days' => (int)($_POST['late_fee_grace_days'] ?? 0),
                'auto_rent_invoices' => isset($_POST['auto_rent_invoices']),
                'sms_reminders' => isset($_POST['sms_reminders']),
            ];
            Company::update(companyId(), ['settings' => json_encode($newSettings)]);
            setFlashMessage('success', 'Preferences updated.');
            redirect('?page=settings&action=preferences');
        }

        $pageTitle = 'Preferences';
        require_once VIEWS_PATH . '/settings/preferences.php';
        break;

    case 'profile':
        // User's own profile - any authenticated user
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND company_id = ?");
        $stmt->execute([currentUserId(), companyId()]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken()) {
                setFlashMessage('error', 'Invalid security token. Please try again.');
                redirect('?page=settings&action=profile');
            }

            $updates = [
                'full_name' => trim($_POST['full_name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
            ];

            // Handle password change
            if (!empty($_POST['new_password'])) {
                if (strlen($_POST['new_password']) < 8) {
                    setFlashMessage('error', 'Password must be at least 8 characters.');
                    redirect('?page=settings&action=profile');
                }
                if ($_POST['new_password'] !== $_POST['confirm_password']) {
                    setFlashMessage('error', 'Passwords do not match.');
                    redirect('?page=settings&action=profile');
                }
                $updates['password_hash'] = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
            }

            // Handle avatar upload
            if (!empty($_FILES['avatar']['name'])) {
                require_once SRC_PATH . '/helpers/FileUpload.php';
                $upload = new FileUpload();
                $avatarPath = $upload->upload($_FILES['avatar'], 'avatars');
                if ($avatarPath) {
                    $updates['avatar'] = $avatarPath;
                    $_SESSION['user_avatar'] = $avatarPath;
                }
            }

            $setClauses = [];
            $params = [];
            foreach ($updates as $k => $v) {
                $setClauses[] = "$k = ?";
                $params[] = $v;
            }
            $params[] = currentUserId();
            $params[] = companyId();
            $db->prepare("UPDATE users SET " . implode(', ', $setClauses) . " WHERE id = ? AND company_id = ?")->execute($params);

            $_SESSION['user_name'] = $updates['full_name'];
            $_SESSION['user_email'] = $updates['email'];
            setFlashMessage('success', 'Profile updated.');
            redirect('?page=settings&action=profile');
        }

        $pageTitle = 'My Profile';
        require_once VIEWS_PATH . '/settings/profile.php';
        break;

    default:
        redirect('?page=settings&action=company');
}
