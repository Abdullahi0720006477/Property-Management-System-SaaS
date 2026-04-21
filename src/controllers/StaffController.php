<?php
/**
 * Staff Controller
 * Manages company staff members (CRUD). Restricted to company_admin role.
 */
requireAuth();
requireRole('company_admin');

require_once SRC_PATH . '/models/ActivityLog.php';
require_once SRC_PATH . '/helpers/PlanLimits.php';

$action = $action ?? 'index';
$id = isset($id) ? (int)$id : null;
$db = Database::getInstance();
$companyId = companyId();

switch ($action) {
    case 'create':
        if (isPost()) {
            if (!validateCsrfToken()) {
                setFlashMessage('error', 'Invalid security token. Please try again.');
                redirect('?page=staff&action=create');
            }

            $data = [
                'full_name'  => postData('full_name'),
                'email'      => postData('email'),
                'phone'      => postData('phone'),
                'role'       => postData('role'),
                'password'   => postData('password'),
                'confirm_password' => postData('confirm_password'),
            ];

            $validator = new Validator($data);
            $validator->required('full_name', 'Full Name')
                      ->maxLength('full_name', 255, 'Full Name')
                      ->required('email', 'Email')
                      ->email('email', 'Email')
                      ->unique('email', 'users', 'email', null, 'Email')
                      ->required('role', 'Role')
                      ->in('role', ['manager', 'staff', 'accountant', 'maintenance_tech'], 'Role')
                      ->required('password', 'Password')
                      ->minLength('password', 8, 'Password')
                      ->match('password', 'confirm_password', 'Password');

            if (!empty($data['phone'])) {
                $validator->phone('phone', 'Phone');
            }

            if ($validator->fails()) {
                setFlashMessage('error', $validator->firstError());
                redirect('?page=staff&action=create');
            }

            // Check plan limits
            if (!PlanLimits::canAddUser($companyId)) {
                setFlashMessage('warning', 'You have reached the maximum number of users for your plan. Please upgrade to add more staff.');
                redirect('?page=billing');
            }

            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            $stmt = $db->prepare("INSERT INTO users (full_name, email, phone, role, password, company_id, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, 1, NOW())");
            $stmt->execute([
                $data['full_name'],
                $data['email'],
                $data['phone'] ?: null,
                $data['role'],
                $hashedPassword,
                $companyId,
            ]);

            $newId = $db->lastInsertId();
            if ($newId) {
                ActivityLog::log('create', 'user', (int)$newId, 'Created staff member: ' . $data['full_name']);
                setFlashMessage('success', 'Staff member created successfully.');
                redirect('?page=staff');
            } else {
                setFlashMessage('error', 'Failed to create staff member.');
                redirect('?page=staff&action=create');
            }
        }

        // GET - show create form
        // Check plan limits before showing form
        if (!PlanLimits::canAddUser($companyId)) {
            setFlashMessage('warning', 'You have reached the maximum number of users for your plan. Please upgrade to add more staff.');
            redirect('?page=billing');
        }

        $usage = PlanLimits::getUsage($companyId);
        $pageTitle = 'Add Staff';
        require_once VIEWS_PATH . '/staff/create.php';
        break;

    case 'edit':
        $id = (int)($id ?? 0);

        // Get user scoped by company_id
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND company_id = ?");
        $stmt->execute([$id, $companyId]);
        $staff = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$staff) {
            setFlashMessage('error', 'Staff member not found.');
            redirect('?page=staff');
        }

        if (isPost()) {
            if (!validateCsrfToken()) {
                setFlashMessage('error', 'Invalid security token. Please try again.');
                redirect('?page=staff&action=edit&id=' . $id);
            }

            $data = [
                'full_name' => postData('full_name'),
                'email'     => postData('email'),
                'phone'     => postData('phone'),
                'role'      => postData('role'),
            ];

            $validator = new Validator($data);
            $validator->required('full_name', 'Full Name')
                      ->maxLength('full_name', 255, 'Full Name')
                      ->required('email', 'Email')
                      ->email('email', 'Email')
                      ->unique('email', 'users', 'email', $id, 'Email')
                      ->required('role', 'Role')
                      ->in('role', ['manager', 'staff', 'accountant', 'maintenance_tech'], 'Role');

            if (!empty(postData('phone'))) {
                $validator->phone('phone', 'Phone');
            }

            if ($validator->fails()) {
                setFlashMessage('error', $validator->firstError());
                redirect('?page=staff&action=edit&id=' . $id);
            }

            // Update basic fields
            $sql = "UPDATE users SET full_name = ?, email = ?, phone = ?, role = ?, updated_at = NOW() WHERE id = ? AND company_id = ?";
            $params = [
                $data['full_name'],
                $data['email'],
                $data['phone'] ?: null,
                $data['role'],
                $id,
                $companyId,
            ];

            $stmt = $db->prepare($sql);
            $stmt->execute($params);

            // Update password if provided
            $password = postData('password');
            if (!empty($password)) {
                if (strlen($password) < 8) {
                    setFlashMessage('error', 'Password must be at least 8 characters.');
                    redirect('?page=staff&action=edit&id=' . $id);
                }
                $confirmPassword = postData('confirm_password');
                if ($password !== $confirmPassword) {
                    setFlashMessage('error', 'Password fields do not match.');
                    redirect('?page=staff&action=edit&id=' . $id);
                }
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmtPw = $db->prepare("UPDATE users SET password = ? WHERE id = ? AND company_id = ?");
                $stmtPw->execute([$hashedPassword, $id, $companyId]);
            }

            ActivityLog::log('update', 'user', $id, 'Updated staff member: ' . $data['full_name']);
            setFlashMessage('success', 'Staff member updated successfully.');
            redirect('?page=staff');
        }

        // GET - show edit form
        $pageTitle = 'Edit Staff';
        require_once VIEWS_PATH . '/staff/edit.php';
        break;

    case 'show':
        $id = (int)($id ?? 0);

        $stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND company_id = ?");
        $stmt->execute([$id, $companyId]);
        $staff = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$staff) {
            setFlashMessage('error', 'Staff member not found.');
            redirect('?page=staff');
        }

        // Get activity log entries for this user
        $activityLogs = ActivityLog::getRecent(20, null, $id);

        $pageTitle = $staff['full_name'];
        require_once VIEWS_PATH . '/staff/show.php';
        break;

    case 'deactivate':
        if (!isPost()) {
            redirect('?page=staff');
        }

        if (!validateCsrfToken()) {
            setFlashMessage('error', 'Invalid security token. Please try again.');
            redirect('?page=staff');
        }

        $id = (int)($id ?? 0);

        // Cannot deactivate yourself
        if ($id === currentUserId()) {
            setFlashMessage('error', 'You cannot deactivate your own account.');
            redirect('?page=staff');
        }

        $stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND company_id = ?");
        $stmt->execute([$id, $companyId]);
        $staff = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$staff) {
            setFlashMessage('error', 'Staff member not found.');
            redirect('?page=staff');
        }

        $stmtDeactivate = $db->prepare("UPDATE users SET is_active = 0, updated_at = NOW() WHERE id = ? AND company_id = ?");
        $stmtDeactivate->execute([$id, $companyId]);

        ActivityLog::log('update', 'user', $id, 'Deactivated staff member: ' . $staff['full_name']);
        setFlashMessage('success', 'Staff member deactivated successfully.');
        redirect('?page=staff');
        break;

    case 'index':
    default:
        $usage = PlanLimits::getUsage($companyId);

        $stmt = $db->prepare("SELECT * FROM users WHERE company_id = ? ORDER BY created_at DESC");
        $stmt->execute([$companyId]);
        $staffMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $pageTitle = 'Staff Members';
        require_once VIEWS_PATH . '/staff/index.php';
        break;
}
