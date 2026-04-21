<?php
/**
 * Unit Controller
 * Full CRUD for managing property units
 */
require_once SRC_PATH . '/models/Unit.php';
require_once SRC_PATH . '/models/Property.php';
require_once SRC_PATH . '/models/Lease.php';
require_once SRC_PATH . '/models/User.php';
require_once SRC_PATH . '/models/ActivityLog.php';
require_once SRC_PATH . '/helpers/PlanLimits.php';

requireAuth();
requireRole('company_admin', 'manager', 'staff');

$unitModel = new Unit();
$propertyModel = new Property();
$cid = companyId();

switch ($action) {
    case 'index':
    default:
        $search = getData('search');
        $filterProperty = getData('property_id') !== '' ? (int) getData('property_id') : null;
        $filterStatus = getData('status');
        $page = max(1, (int) getData('pg', '1'));
        $perPage = RECORDS_PER_PAGE;
        $offset = ($page - 1) * $perPage;

        $units = $unitModel->getAll($search, $filterProperty, $filterStatus, $perPage, $offset, $cid);
        $totalRecords = $unitModel->count($search, $filterProperty, $filterStatus, $cid);

        // Load properties for filter dropdown
        $properties = $propertyModel->getAll('', '', '', null, 1000, 0, $cid);

        $pageTitle = 'Units';
        require_once VIEWS_PATH . '/units/index.php';
        break;

    case 'create':
        if (isPost()) {
            if (!validateCsrfToken()) {
                setFlashMessage('error', 'Invalid request. Please try again.');
                redirect('?page=units&action=create');
            }

            // Plan limit check
            if (!PlanLimits::canAddUnit($cid)) {
                setFlashMessage('error', 'You have reached the maximum number of units for your plan. Please upgrade.');
                redirect('?page=billing');
            }

            $validator = new Validator($_POST);
            $validator->required('property_id')
                      ->required('unit_number')
                      ->required('rent_amount')
                      ->numeric('rent_amount', 'Rent amount');

            if ($validator->fails()) {
                setFlashMessage('error', $validator->firstError());
                $_SESSION['old_input'] = $_POST;
                redirect('?page=units&action=create');
            }

            // Verify property belongs to this company
            $property = $propertyModel->findById((int) postData('property_id'), $cid);
            if (!$property) {
                setFlashMessage('error', 'Invalid property selected.');
                redirect('?page=units&action=create');
            }

            $data = [
                'property_id'  => (int) postData('property_id'),
                'unit_number'  => postData('unit_number'),
                'floor_number' => postData('floor_number') !== '' ? (int) postData('floor_number') : null,
                'bedrooms'     => postData('bedrooms') !== '' ? (int) postData('bedrooms') : 1,
                'bathrooms'    => postData('bathrooms') !== '' ? (int) postData('bathrooms') : 1,
                'area_sqft'    => postData('area_sqft') !== '' ? (float) postData('area_sqft') : null,
                'rent_amount'  => (float) postData('rent_amount'),
                'status'       => postData('status') ?: 'vacant',
                'description'  => postData('description') ?: null,
            ];

            $unitId = $unitModel->create($data);

            if ($unitId) {
                ActivityLog::log('create', 'unit', $unitId, 'Created unit: ' . $data['unit_number']);
                setFlashMessage('success', 'Unit created successfully.');
                redirect('?page=units&action=show&id=' . $unitId);
            } else {
                setFlashMessage('error', 'Failed to create unit. Please try again.');
                $_SESSION['old_input'] = $_POST;
                redirect('?page=units&action=create');
            }
        }

        // Load properties for select dropdown
        $properties = $propertyModel->getAll('', '', '', null, 1000, 0, $cid);
        $pageTitle = 'Add Unit';
        require_once VIEWS_PATH . '/units/create.php';
        break;

    case 'edit':
        $id = (int) ($id ?? 0);
        $unit = $unitModel->findById($id, $cid);

        if (!$unit) {
            setFlashMessage('error', 'Unit not found.');
            redirect('?page=units');
        }

        if (isPost()) {
            if (!validateCsrfToken()) {
                setFlashMessage('error', 'Invalid request. Please try again.');
                redirect('?page=units&action=edit&id=' . $id);
            }

            $validator = new Validator($_POST);
            $validator->required('property_id')
                      ->required('unit_number')
                      ->required('rent_amount')
                      ->numeric('rent_amount', 'Rent amount');

            if ($validator->fails()) {
                setFlashMessage('error', $validator->firstError());
                $_SESSION['old_input'] = $_POST;
                redirect('?page=units&action=edit&id=' . $id);
            }

            $data = [
                'property_id'  => (int) postData('property_id'),
                'unit_number'  => postData('unit_number'),
                'floor_number' => postData('floor_number') !== '' ? (int) postData('floor_number') : null,
                'bedrooms'     => postData('bedrooms') !== '' ? (int) postData('bedrooms') : 1,
                'bathrooms'    => postData('bathrooms') !== '' ? (int) postData('bathrooms') : 1,
                'area_sqft'    => postData('area_sqft') !== '' ? (float) postData('area_sqft') : null,
                'rent_amount'  => (float) postData('rent_amount'),
                'status'       => postData('status') ?: 'vacant',
                'description'  => postData('description') ?: null,
            ];

            $updated = $unitModel->update($id, $data, $cid);

            if ($updated) {
                ActivityLog::log('update', 'unit', $id, 'Updated unit: ' . $data['unit_number']);
                setFlashMessage('success', 'Unit updated successfully.');
                redirect('?page=units&action=show&id=' . $id);
            } else {
                setFlashMessage('error', 'Failed to update unit.');
                redirect('?page=units&action=edit&id=' . $id);
            }
        }

        // Load properties for select dropdown
        $properties = $propertyModel->getAll('', '', '', null, 1000, 0, $cid);
        $pageTitle = 'Edit Unit';
        require_once VIEWS_PATH . '/units/edit.php';
        break;

    case 'show':
        $id = (int) ($id ?? 0);
        $unit = $unitModel->findById($id, $cid);

        if (!$unit) {
            setFlashMessage('error', 'Unit not found.');
            redirect('?page=units');
        }

        // Get unit with active lease info (tenant, lease dates, etc.)
        $unitWithLease = $unitModel->getUnitWithActiveLease($id, $cid);

        $pageTitle = 'Unit ' . $unit['unit_number'];
        require_once VIEWS_PATH . '/units/show.php';
        break;

    case 'delete':
        $id = (int) ($id ?? 0);

        if (!isPost()) {
            setFlashMessage('error', 'Invalid request method.');
            redirect('?page=units');
        }

        if (!validateCsrfToken()) {
            setFlashMessage('error', 'Invalid request. Please try again.');
            redirect('?page=units');
        }

        $unit = $unitModel->findById($id, $cid);

        if (!$unit) {
            setFlashMessage('error', 'Unit not found.');
            redirect('?page=units');
        }

        // Check if unit has an active lease before deleting
        $unitWithLease = $unitModel->getUnitWithActiveLease($id, $cid);
        if (!empty($unitWithLease['lease_id'])) {
            setFlashMessage('error', 'Cannot delete a unit with an active lease. Terminate the lease first.');
            redirect('?page=units&action=show&id=' . $id);
        }

        $deleted = $unitModel->delete($id, $cid);

        if ($deleted) {
            ActivityLog::log('delete', 'unit', $id, 'Deleted unit: ' . $unit['unit_number']);
            setFlashMessage('success', 'Unit deleted successfully.');
        } else {
            setFlashMessage('error', 'Failed to delete unit.');
        }

        redirect('?page=units');
        break;
}
