<?php
/**
 * Lease Controller
 */
require_once SRC_PATH . '/models/Lease.php';
require_once SRC_PATH . '/models/Unit.php';
require_once SRC_PATH . '/models/Property.php';
require_once SRC_PATH . '/models/Tenant.php';
require_once SRC_PATH . '/models/ActivityLog.php';

requireRole('company_admin', 'manager', 'staff');

$leaseModel = new Lease();
$unitModel = new Unit();
$propertyModel = new Property();
$cid = companyId();

$action = $action ?? 'index';

switch ($action) {
    case 'create':
        if (isPost()) {
            if (!validateCsrfToken()) {
                setFlashMessage('error', 'Invalid security token. Please try again.');
                redirect('?page=leases&action=create');
            }

            $data = [
                'unit_id'          => postData('unit_id'),
                'tenant_id'        => postData('tenant_id'),
                'start_date'       => postData('start_date'),
                'end_date'         => postData('end_date'),
                'monthly_rent'     => postData('monthly_rent'),
                'security_deposit' => postData('security_deposit'),
                'lease_status'     => postData('lease_status'),
                'notes'            => postData('notes'),
            ];

            $validator = new Validator($data);
            $validator->required('unit_id', 'Unit')
                      ->required('tenant_id', 'Tenant')
                      ->required('start_date', 'Start Date')
                      ->date('start_date', 'Start Date')
                      ->required('end_date', 'End Date')
                      ->date('end_date', 'End Date')
                      ->required('monthly_rent', 'Monthly Rent')
                      ->positive('monthly_rent', 'Monthly Rent')
                      ->numeric('security_deposit', 'Security Deposit')
                      ->in('lease_status', ['active', 'pending', 'expired', 'terminated'], 'Lease Status');

            if ($validator->fails()) {
                setFlashMessage('error', $validator->firstError());
                redirect('?page=leases&action=create');
            }

            // Validate end date is after start date
            if (strtotime($data['end_date']) <= strtotime($data['start_date'])) {
                setFlashMessage('error', 'End date must be after start date.');
                redirect('?page=leases&action=create');
            }

            // Verify unit belongs to this company
            $unit = $unitModel->findById((int) $data['unit_id'], $cid);
            if (!$unit) {
                setFlashMessage('error', 'Invalid unit selected.');
                redirect('?page=leases&action=create');
            }

            // Verify tenant belongs to this company
            $tenant = Tenant::findById((int) $data['tenant_id'], $cid);
            if (!$tenant) {
                setFlashMessage('error', 'Invalid tenant selected.');
                redirect('?page=leases&action=create');
            }

            // Handle document upload
            if (!empty($_FILES['document']['name'])) {
                $uploader = new FileUpload();
                $docPath = $uploader->upload($_FILES['document'], 'leases', ALLOWED_DOC_TYPES);
                if ($docPath === false) {
                    setFlashMessage('error', $uploader->firstError());
                    redirect('?page=leases&action=create');
                }
                $data['document_path'] = $docPath;
            }

            $leaseId = $leaseModel->create($data);
            if ($leaseId) {
                // If lease status is active, mark unit as occupied
                if ($data['lease_status'] === 'active') {
                    $unitModel->updateStatus((int) $data['unit_id'], 'occupied', $cid);
                }
                ActivityLog::log('create', 'lease', $leaseId, 'Created lease for unit #' . $data['unit_id']);
                setFlashMessage('success', 'Lease created successfully.');
                redirect('?page=leases&action=show&id=' . $leaseId);
            } else {
                setFlashMessage('error', 'Failed to create lease.');
                redirect('?page=leases&action=create');
            }
        }

        $vacantUnits = $unitModel->getVacantUnits($cid);
        $tenants = Tenant::getAll($cid);
        $pageTitle = 'Create Lease';
        require_once VIEWS_PATH . '/leases/create.php';
        break;

    case 'edit':
        $id = (int) ($id ?? 0);
        $lease = $leaseModel->findById($id, $cid);

        if (!$lease) {
            setFlashMessage('error', 'Lease not found.');
            redirect('?page=leases');
        }

        if (isPost()) {
            if (!validateCsrfToken()) {
                setFlashMessage('error', 'Invalid security token. Please try again.');
                redirect('?page=leases&action=edit&id=' . $id);
            }

            $data = [
                'unit_id'          => postData('unit_id'),
                'tenant_id'        => postData('tenant_id'),
                'start_date'       => postData('start_date'),
                'end_date'         => postData('end_date'),
                'monthly_rent'     => postData('monthly_rent'),
                'security_deposit' => postData('security_deposit'),
                'lease_status'     => postData('lease_status'),
                'notes'            => postData('notes'),
            ];

            $validator = new Validator($data);
            $validator->required('unit_id', 'Unit')
                      ->required('tenant_id', 'Tenant')
                      ->required('start_date', 'Start Date')
                      ->date('start_date', 'Start Date')
                      ->required('end_date', 'End Date')
                      ->date('end_date', 'End Date')
                      ->required('monthly_rent', 'Monthly Rent')
                      ->positive('monthly_rent', 'Monthly Rent')
                      ->numeric('security_deposit', 'Security Deposit')
                      ->in('lease_status', ['active', 'pending', 'expired', 'terminated'], 'Lease Status');

            if ($validator->fails()) {
                setFlashMessage('error', $validator->firstError());
                redirect('?page=leases&action=edit&id=' . $id);
            }

            // Validate end date is after start date
            if (strtotime($data['end_date']) <= strtotime($data['start_date'])) {
                setFlashMessage('error', 'End date must be after start date.');
                redirect('?page=leases&action=edit&id=' . $id);
            }

            // Handle document upload
            if (!empty($_FILES['document']['name'])) {
                $uploader = new FileUpload();
                $docPath = $uploader->upload($_FILES['document'], 'leases', ALLOWED_DOC_TYPES);
                if ($docPath === false) {
                    setFlashMessage('error', $uploader->firstError());
                    redirect('?page=leases&action=edit&id=' . $id);
                }
                // Delete old document if exists
                if (!empty($lease['document_path'])) {
                    $uploader->delete($lease['document_path']);
                }
                $data['document_path'] = $docPath;
            }

            if ($leaseModel->update($id, $data, $cid)) {
                ActivityLog::log('update', 'lease', $id, 'Updated lease #' . $id);
                setFlashMessage('success', 'Lease updated successfully.');
                redirect('?page=leases&action=show&id=' . $id);
            } else {
                setFlashMessage('error', 'Failed to update lease.');
                redirect('?page=leases&action=edit&id=' . $id);
            }
        }

        $vacantUnits = $unitModel->getVacantUnits($cid);
        $tenants = Tenant::getAll($cid);
        $pageTitle = 'Edit Lease';
        require_once VIEWS_PATH . '/leases/edit.php';
        break;

    case 'show':
        $id = (int) ($id ?? 0);
        $lease = $leaseModel->findById($id, $cid);

        if (!$lease) {
            setFlashMessage('error', 'Lease not found.');
            redirect('?page=leases');
        }

        $pageTitle = 'Lease Details';
        require_once VIEWS_PATH . '/leases/show.php';
        break;

    case 'terminate':
        if (!isPost()) {
            redirect('?page=leases');
        }

        if (!validateCsrfToken()) {
            setFlashMessage('error', 'Invalid security token. Please try again.');
            redirect('?page=leases');
        }

        $id = (int) ($id ?? 0);
        $lease = $leaseModel->findById($id, $cid);

        if (!$lease) {
            setFlashMessage('error', 'Lease not found.');
            redirect('?page=leases');
        }

        if ($lease['lease_status'] !== 'active') {
            setFlashMessage('error', 'Only active leases can be terminated.');
            redirect('?page=leases&action=show&id=' . $id);
        }

        if ($leaseModel->update($id, ['lease_status' => 'terminated'], $cid)) {
            $unitModel->updateStatus((int) $lease['unit_id'], 'vacant', $cid);
            ActivityLog::log('update', 'lease', $id, 'Terminated lease #' . $id);
            setFlashMessage('success', 'Lease terminated successfully. Unit has been marked as vacant.');
            redirect('?page=leases&action=show&id=' . $id);
        } else {
            setFlashMessage('error', 'Failed to terminate lease.');
            redirect('?page=leases&action=show&id=' . $id);
        }
        break;

    case 'delete':
        if (!isPost()) {
            redirect('?page=leases');
        }

        if (!validateCsrfToken()) {
            setFlashMessage('error', 'Invalid security token. Please try again.');
            redirect('?page=leases');
        }

        $id = (int) ($id ?? 0);
        $lease = $leaseModel->findById($id, $cid);

        if (!$lease) {
            setFlashMessage('error', 'Lease not found.');
            redirect('?page=leases');
        }

        // Delete associated document if exists
        if (!empty($lease['document_path'])) {
            $uploader = new FileUpload();
            $uploader->delete($lease['document_path']);
        }

        // If lease was active, set unit back to vacant
        if ($lease['lease_status'] === 'active') {
            $unitModel->updateStatus((int) $lease['unit_id'], 'vacant', $cid);
        }

        if ($leaseModel->delete($id, $cid)) {
            ActivityLog::log('delete', 'lease', $id, 'Deleted lease #' . $id);
            setFlashMessage('success', 'Lease deleted successfully.');
        } else {
            setFlashMessage('error', 'Failed to delete lease.');
        }
        redirect('?page=leases');
        break;

    case 'renew':
        $id = (int) ($id ?? 0);
        $oldLease = $leaseModel->findById($id, $cid);

        if (!$oldLease) {
            setFlashMessage('error', 'Lease not found.');
            redirect('?page=leases');
        }

        if (!in_array($oldLease['lease_status'], ['active', 'expired'])) {
            setFlashMessage('error', 'Only active or expired leases can be renewed.');
            redirect('?page=leases&action=show&id=' . $id);
        }

        if (isPost()) {
            if (!validateCsrfToken()) {
                setFlashMessage('error', 'Invalid security token.');
                redirect('?page=leases&action=renew&id=' . $id);
            }

            $data = [
                'unit_id'          => postData('unit_id'),
                'tenant_id'        => postData('tenant_id'),
                'start_date'       => postData('start_date'),
                'end_date'         => postData('end_date'),
                'monthly_rent'     => postData('monthly_rent'),
                'security_deposit' => postData('security_deposit'),
                'lease_status'     => 'active',
                'notes'            => postData('notes'),
            ];

            $validator = new Validator($data);
            $validator->required('unit_id', 'Unit')
                      ->required('tenant_id', 'Tenant')
                      ->required('start_date', 'Start Date')
                      ->date('start_date', 'Start Date')
                      ->required('end_date', 'End Date')
                      ->date('end_date', 'End Date')
                      ->required('monthly_rent', 'Monthly Rent')
                      ->positive('monthly_rent', 'Monthly Rent');

            if ($validator->fails()) {
                setFlashMessage('error', $validator->firstError());
                redirect('?page=leases&action=renew&id=' . $id);
            }

            if (strtotime($data['end_date']) <= strtotime($data['start_date'])) {
                setFlashMessage('error', 'End date must be after start date.');
                redirect('?page=leases&action=renew&id=' . $id);
            }

            // Handle document upload
            if (!empty($_FILES['document']['name'])) {
                $uploader = new FileUpload();
                $docPath = $uploader->upload($_FILES['document'], 'leases', ALLOWED_DOC_TYPES);
                if ($docPath === false) {
                    setFlashMessage('error', $uploader->firstError());
                    redirect('?page=leases&action=renew&id=' . $id);
                }
                $data['document_path'] = $docPath;
            }

            $newLeaseId = $leaseModel->create($data);
            if ($newLeaseId) {
                // Expire the old lease
                $leaseModel->update($id, ['lease_status' => 'expired'], $cid);
                // Ensure unit stays occupied
                $unitModel->updateStatus((int) $data['unit_id'], 'occupied', $cid);
                ActivityLog::log('create', 'lease', $newLeaseId, 'Renewed lease #' . $id . ' as lease #' . $newLeaseId);
                setFlashMessage('success', 'Lease renewed successfully.');
                redirect('?page=leases&action=show&id=' . $newLeaseId);
            } else {
                setFlashMessage('error', 'Failed to renew lease.');
                redirect('?page=leases&action=renew&id=' . $id);
            }
        }

        // Pre-fill renewal data
        $newStartDate = date('Y-m-d', strtotime($oldLease['end_date'] . ' +1 day'));
        $newEndDate = date('Y-m-d', strtotime($newStartDate . ' +1 year'));

        $renewal = [
            'unit_id'          => $oldLease['unit_id'],
            'tenant_id'        => $oldLease['tenant_id'],
            'start_date'       => $newStartDate,
            'end_date'         => $newEndDate,
            'monthly_rent'     => $oldLease['monthly_rent'],
            'security_deposit' => $oldLease['security_deposit'],
            'notes'            => 'Renewal of lease #' . $id,
        ];

        $vacantUnits = $unitModel->getVacantUnits($cid);
        $tenants = Tenant::getAll($cid);
        $pageTitle = 'Renew Lease';
        $isRenewal = true;
        $oldLeaseId = $id;
        require_once VIEWS_PATH . '/leases/create.php';
        break;

    case 'index':
    default:
        $search = getData('search');
        $status = getData('status');
        $currentPage = max(1, (int) getData('pg', '1'));
        $offset = ($currentPage - 1) * RECORDS_PER_PAGE;

        $managerId = isManager() ? currentUserId() : null;
        $leases = $leaseModel->getAll($search, $status, null, RECORDS_PER_PAGE, $offset, $managerId, $cid);
        $totalRecords = $leaseModel->count($search, $status, null, $managerId, $cid);

        $pageTitle = 'Leases';
        require_once VIEWS_PATH . '/leases/index.php';
        break;
}
