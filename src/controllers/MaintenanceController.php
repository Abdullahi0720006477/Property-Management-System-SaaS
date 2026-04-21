<?php
/**
 * Maintenance Controller
 */
require_once SRC_PATH . '/models/MaintenanceRequest.php';
require_once SRC_PATH . '/models/Unit.php';
require_once SRC_PATH . '/models/Lease.php';
require_once SRC_PATH . '/models/User.php';
require_once SRC_PATH . '/models/Notification.php';
require_once SRC_PATH . '/models/ActivityLog.php';

requireRole('company_admin', 'manager', 'staff', 'maintenance_tech');

$maintenanceModel = new MaintenanceRequest();
$unitModel = new Unit();
$leaseModel = new Lease();
$userModel = new User();
$notifModel = new Notification();
$cid = companyId();

$action = $action ?? 'index';

switch ($action) {
    case 'create':
        if (isPost()) {
            if (!validateCsrfToken()) {
                setFlashMessage('error', 'Invalid security token. Please try again.');
                redirect('?page=maintenance&action=create');
            }

            $data = [
                'title'       => postData('title'),
                'description' => postData('description'),
                'priority'    => postData('priority'),
                'unit_id'     => (int) postData('unit_id'),
            ];

            // Look up the tenant from the active lease on the selected unit
            $unitLease = $leaseModel->getActiveLeaseByUnit($data['unit_id']);
            $data['tenant_id'] = $unitLease ? $unitLease['tenant_id'] : null;

            $validator = new Validator($data);
            $validator->required('title', 'Title')
                      ->maxLength('title', 255, 'Title')
                      ->required('description', 'Description')
                      ->required('priority', 'Priority')
                      ->in('priority', ['low', 'medium', 'high', 'emergency'], 'Priority')
                      ->required('unit_id', 'Unit');

            if ($validator->fails()) {
                setFlashMessage('error', $validator->firstError());
                redirect('?page=maintenance&action=create');
            }

            // Verify unit belongs to this company
            $unit = $unitModel->findById($data['unit_id'], $cid);
            if (!$unit) {
                setFlashMessage('error', 'Invalid unit selected.');
                redirect('?page=maintenance&action=create');
            }

            // Handle image upload
            if (!empty($_FILES['images']['name'])) {
                $uploader = new FileUpload();
                $imagePath = $uploader->upload($_FILES['images'], 'maintenance', ALLOWED_IMAGE_TYPES);
                if ($imagePath === false) {
                    setFlashMessage('error', $uploader->firstError());
                    redirect('?page=maintenance&action=create');
                }
                $data['images'] = $imagePath;
            }

            $id = $maintenanceModel->create($data);
            if ($id) {
                ActivityLog::log('create', 'maintenance', $id, 'Created maintenance request: ' . $data['title']);
                setFlashMessage('success', 'Maintenance request submitted successfully.');
                redirect('?page=maintenance&action=show&id=' . $id);
            } else {
                setFlashMessage('error', 'Failed to submit maintenance request.');
                redirect('?page=maintenance&action=create');
            }
        }

        // Load units for dropdown (scoped to company)
        $units = $unitModel->getAll('', null, '', 1000, 0, $cid);

        $pageTitle = 'New Maintenance Request';
        require_once VIEWS_PATH . '/maintenance/create.php';
        break;

    case 'edit':
        $id = (int) ($id ?? 0);
        $request = $maintenanceModel->findById($id, $cid);

        if (!$request) {
            setFlashMessage('error', 'Maintenance request not found.');
            redirect('?page=maintenance');
        }

        if (isPost()) {
            if (!validateCsrfToken()) {
                setFlashMessage('error', 'Invalid security token. Please try again.');
                redirect('?page=maintenance&action=edit&id=' . $id);
            }

            $newStatus = postData('status');

            $data = [
                'status'      => $newStatus,
                'priority'    => postData('priority'),
                'assigned_to' => postData('assigned_to') ?: null,
                'cost'        => postData('cost') !== '' ? postData('cost') : null,
            ];

            $validator = new Validator($data);
            $validator->required('status', 'Status')
                      ->in('status', ['open', 'in_progress', 'resolved', 'closed'], 'Status')
                      ->required('priority', 'Priority')
                      ->in('priority', ['low', 'medium', 'high', 'emergency'], 'Priority');

            if ($validator->fails()) {
                setFlashMessage('error', $validator->firstError());
                redirect('?page=maintenance&action=edit&id=' . $id);
            }

            // Set resolved_at when status changes to resolved
            if ($newStatus === 'resolved' && $request['status'] !== 'resolved') {
                $data['resolved_at'] = date('Y-m-d H:i:s');
            }

            // Handle image upload
            if (!empty($_FILES['images']['name'])) {
                $uploader = new FileUpload();
                $imagePath = $uploader->upload($_FILES['images'], 'maintenance', ALLOWED_IMAGE_TYPES);
                if ($imagePath === false) {
                    setFlashMessage('error', $uploader->firstError());
                    redirect('?page=maintenance&action=edit&id=' . $id);
                }
                $data['images'] = $imagePath;
            }

            if ($maintenanceModel->update($id, $data, $cid)) {
                ActivityLog::log('update', 'maintenance', $id, 'Updated maintenance request #' . $id . ' status: ' . $newStatus);

                setFlashMessage('success', 'Maintenance request updated successfully.');
                redirect('?page=maintenance&action=show&id=' . $id);
            } else {
                setFlashMessage('error', 'Failed to update maintenance request.');
                redirect('?page=maintenance&action=edit&id=' . $id);
            }
        }

        // Load managers and admins for assign dropdown
        $managers = $userModel->getAll('manager', '', 1000, 0);
        $admins = $userModel->getAll('admin', '', 1000, 0);
        $assignableUsers = array_merge($admins, $managers);

        $pageTitle = 'Edit Maintenance Request';
        require_once VIEWS_PATH . '/maintenance/edit.php';
        break;

    case 'show':
        $id = (int) ($id ?? 0);
        $request = $maintenanceModel->findById($id, $cid);

        if (!$request) {
            setFlashMessage('error', 'Maintenance request not found.');
            redirect('?page=maintenance');
        }

        $pageTitle = 'Maintenance Request #' . $id;
        require_once VIEWS_PATH . '/maintenance/show.php';
        break;

    case 'delete':
        if (!isPost()) {
            redirect('?page=maintenance');
        }

        if (!validateCsrfToken()) {
            setFlashMessage('error', 'Invalid security token. Please try again.');
            redirect('?page=maintenance');
        }

        $id = (int) ($id ?? 0);
        $request = $maintenanceModel->findById($id, $cid);

        if (!$request) {
            setFlashMessage('error', 'Maintenance request not found.');
            redirect('?page=maintenance');
        }

        if ($maintenanceModel->delete($id, $cid)) {
            setFlashMessage('success', 'Maintenance request deleted successfully.');
        } else {
            setFlashMessage('error', 'Failed to delete maintenance request.');
        }
        redirect('?page=maintenance');
        break;

    case 'index':
    default:
        $search = getData('search');
        $status = getData('status');
        $priority = getData('priority');
        $currentPage = max(1, (int) getData('pg', '1'));
        $offset = ($currentPage - 1) * RECORDS_PER_PAGE;

        $requests = $maintenanceModel->getAll($search, $status, $priority, null, RECORDS_PER_PAGE, $offset, $cid);
        $totalRecords = $maintenanceModel->count($search, $status, $priority, null, $cid);

        $pageTitle = 'Maintenance Requests';
        require_once VIEWS_PATH . '/maintenance/index.php';
        break;
}
