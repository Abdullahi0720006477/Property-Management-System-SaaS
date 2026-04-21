<?php
/**
 * Tenant Controller
 * Uses the new tenants table (not users table)
 */
require_once SRC_PATH . '/models/Tenant.php';
require_once SRC_PATH . '/models/Lease.php';
require_once SRC_PATH . '/models/Payment.php';
require_once SRC_PATH . '/helpers/FileUpload.php';
require_once SRC_PATH . '/models/ActivityLog.php';

requireRole('company_admin', 'manager', 'staff');

$leaseModel = new Lease();
$paymentModel = new Payment();
$cid = companyId();

$action = $action ?? 'index';

switch ($action) {
    case 'create':
        if (isPost()) {
            if (!validateCsrfToken()) {
                setFlashMessage('error', 'Invalid security token. Please try again.');
                redirect('?page=tenants&action=create');
            }

            $data = [
                'company_id'              => $cid,
                'first_name'              => postData('first_name'),
                'last_name'               => postData('last_name'),
                'email'                   => postData('email'),
                'phone'                   => postData('phone'),
                'id_number'               => postData('id_number'),
                'emergency_contact_name'  => postData('emergency_contact_name'),
                'emergency_contact_phone' => postData('emergency_contact_phone'),
                'date_of_birth'           => postData('date_of_birth') ?: null,
                'occupation'              => postData('occupation'),
                'employer'                => postData('employer'),
                'notes'                   => postData('notes'),
            ];

            $validator = new Validator($data);
            $validator->required('first_name', 'First Name')
                      ->maxLength('first_name', 100, 'First Name')
                      ->required('last_name', 'Last Name')
                      ->maxLength('last_name', 100, 'Last Name')
                      ->required('phone', 'Phone')
                      ->phone('phone');

            if (!empty($data['email'])) {
                $validator->email('email');
            }

            if ($validator->fails()) {
                setFlashMessage('error', $validator->firstError());
                $_SESSION['old_input'] = $_POST;
                redirect('?page=tenants&action=create');
            }

            $tenantId = Tenant::create($data);
            if ($tenantId) {
                ActivityLog::log('create', 'tenant', $tenantId, 'Created tenant: ' . $data['first_name'] . ' ' . $data['last_name']);
                setFlashMessage('success', 'Tenant created successfully.');
                redirect('?page=tenants&action=show&id=' . $tenantId);
            } else {
                setFlashMessage('error', 'Failed to create tenant.');
                redirect('?page=tenants&action=create');
            }
        }

        $pageTitle = 'Add Tenant';
        $old = $_SESSION['old_input'] ?? [];
        unset($_SESSION['old_input']);
        require_once VIEWS_PATH . '/tenants/create.php';
        break;

    case 'edit':
        $id = (int) ($id ?? 0);
        $tenant = Tenant::findById($id, $cid);

        if (!$tenant) {
            setFlashMessage('error', 'Tenant not found.');
            redirect('?page=tenants');
        }

        if (isPost()) {
            if (!validateCsrfToken()) {
                setFlashMessage('error', 'Invalid security token. Please try again.');
                redirect('?page=tenants&action=edit&id=' . $id);
            }

            $data = [
                'first_name'              => postData('first_name'),
                'last_name'               => postData('last_name'),
                'email'                   => postData('email'),
                'phone'                   => postData('phone'),
                'id_number'               => postData('id_number'),
                'emergency_contact_name'  => postData('emergency_contact_name'),
                'emergency_contact_phone' => postData('emergency_contact_phone'),
                'date_of_birth'           => postData('date_of_birth') ?: null,
                'occupation'              => postData('occupation'),
                'employer'                => postData('employer'),
                'notes'                   => postData('notes'),
            ];

            $validator = new Validator($data);
            $validator->required('first_name', 'First Name')
                      ->maxLength('first_name', 100, 'First Name')
                      ->required('last_name', 'Last Name')
                      ->maxLength('last_name', 100, 'Last Name')
                      ->required('phone', 'Phone')
                      ->phone('phone');

            if (!empty($data['email'])) {
                $validator->email('email');
            }

            if ($validator->fails()) {
                setFlashMessage('error', $validator->firstError());
                redirect('?page=tenants&action=edit&id=' . $id);
            }

            // Handle avatar upload
            if (!empty($_FILES['avatar']['name'])) {
                $upload = new FileUpload();
                $avatarPath = $upload->upload($_FILES['avatar'], 'avatars');
                if ($avatarPath) {
                    if (!empty($tenant['avatar'])) {
                        $upload->delete($tenant['avatar']);
                    }
                    $data['avatar'] = $avatarPath;
                }
            }

            if (Tenant::update($id, $cid, $data)) {
                ActivityLog::log('update', 'tenant', $id, 'Updated tenant: ' . $data['first_name'] . ' ' . $data['last_name']);
                setFlashMessage('success', 'Tenant updated successfully.');
                redirect('?page=tenants&action=show&id=' . $id);
            } else {
                setFlashMessage('error', 'Failed to update tenant.');
                redirect('?page=tenants&action=edit&id=' . $id);
            }
        }

        $pageTitle = 'Edit Tenant';
        require_once VIEWS_PATH . '/tenants/edit.php';
        break;

    case 'show':
        $id = (int) ($id ?? 0);
        $tenant = Tenant::findById($id, $cid);

        if (!$tenant) {
            setFlashMessage('error', 'Tenant not found.');
            redirect('?page=tenants');
        }

        $activeLease = $leaseModel->getActiveLeaseByTenant($id);
        $payments = $paymentModel->getTenantPayments($id, 20, $cid);

        $pageTitle = Tenant::getFullName($tenant);
        require_once VIEWS_PATH . '/tenants/show.php';
        break;

    case 'delete':
        if (!isPost()) {
            redirect('?page=tenants');
        }

        if (!validateCsrfToken()) {
            setFlashMessage('error', 'Invalid security token. Please try again.');
            redirect('?page=tenants');
        }

        $id = (int) ($id ?? 0);
        $tenant = Tenant::findById($id, $cid);

        if (!$tenant) {
            setFlashMessage('error', 'Tenant not found.');
            redirect('?page=tenants');
        }

        if (Tenant::delete($id, $cid)) {
            ActivityLog::log('delete', 'tenant', $id, 'Deactivated tenant: ' . Tenant::getFullName($tenant));
            setFlashMessage('success', 'Tenant deactivated successfully.');
        } else {
            setFlashMessage('error', 'Failed to deactivate tenant.');
        }
        redirect('?page=tenants');
        break;

    case 'index':
    default:
        $search = getData('search');
        $filters = [];
        if ($search) {
            $filters['search'] = $search;
        }

        $tenants = Tenant::getAll($cid, $filters);
        $totalRecords = count($tenants);

        $pageTitle = 'Tenants';
        require_once VIEWS_PATH . '/tenants/index.php';
        break;
}
