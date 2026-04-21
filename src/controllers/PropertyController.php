<?php
/**
 * Property Controller
 */
require_once SRC_PATH . '/models/Property.php';
require_once SRC_PATH . '/models/Unit.php';
require_once SRC_PATH . '/models/User.php';
require_once SRC_PATH . '/models/ActivityLog.php';
require_once SRC_PATH . '/helpers/PlanLimits.php';

requireRole('company_admin', 'manager', 'staff');

$propertyModel = new Property();
$unitModel = new Unit();
$userModel = new User();
$cid = companyId();

$action = $action ?? 'index';

switch ($action) {
    case 'create':
        if (isPost()) {
            if (!validateCsrfToken()) {
                setFlashMessage('error', 'Invalid security token. Please try again.');
                redirect('?page=properties&action=create');
            }

            // Plan limit check
            if (!PlanLimits::canAddProperty($cid)) {
                setFlashMessage('error', 'You have reached the maximum number of properties for your plan. Please upgrade.');
                redirect('?page=billing');
            }

            $data = [
                'company_id'    => $cid,
                'name'          => postData('name'),
                'address'       => postData('address'),
                'city'          => postData('city'),
                'property_type' => postData('property_type'),
                'total_units'   => postData('total_units'),
                'description'   => postData('description'),
                'manager_id'    => postData('manager_id'),
            ];

            $validator = new Validator($data);
            $validator->required('name', 'Property Name')
                      ->maxLength('name', 255, 'Property Name')
                      ->required('address', 'Address')
                      ->required('property_type', 'Property Type')
                      ->in('property_type', ['apartment_building', 'single_house', 'commercial', 'mixed_use'], 'Property Type')
                      ->required('total_units', 'Total Units')
                      ->numeric('total_units', 'Total Units');

            if ($validator->fails()) {
                setFlashMessage('error', $validator->firstError());
                redirect('?page=properties&action=create');
            }

            // Handle image upload
            if (!empty($_FILES['image']['name'])) {
                $uploader = new FileUpload();
                $imagePath = $uploader->upload($_FILES['image'], 'properties', ALLOWED_IMAGE_TYPES);
                if ($imagePath === false) {
                    setFlashMessage('error', $uploader->firstError());
                    redirect('?page=properties&action=create');
                }
                $data['image'] = $imagePath;
            }

            $id = $propertyModel->create($data);
            if ($id) {
                ActivityLog::log('create', 'property', $id, 'Created property: ' . $data['name']);
                setFlashMessage('success', 'Property created successfully.');
                redirect('?page=properties&action=show&id=' . $id);
            } else {
                setFlashMessage('error', 'Failed to create property.');
                redirect('?page=properties&action=create');
            }
        }

        $managers = $userModel->getManagers();
        $pageTitle = 'Add Property';
        require_once VIEWS_PATH . '/properties/create.php';
        break;

    case 'edit':
        $id = (int) ($id ?? 0);
        $property = $propertyModel->findById($id, $cid);

        if (!$property) {
            setFlashMessage('error', 'Property not found.');
            redirect('?page=properties');
        }

        // Managers can only edit their own properties
        if (isManager() && $property['manager_id'] != currentUserId()) {
            setFlashMessage('error', 'You do not have permission to edit this property.');
            redirect('?page=properties');
        }

        if (isPost()) {
            if (!validateCsrfToken()) {
                setFlashMessage('error', 'Invalid security token. Please try again.');
                redirect('?page=properties&action=edit&id=' . $id);
            }

            $data = [
                'name'          => postData('name'),
                'address'       => postData('address'),
                'city'          => postData('city'),
                'property_type' => postData('property_type'),
                'total_units'   => postData('total_units'),
                'description'   => postData('description'),
                'manager_id'    => postData('manager_id'),
            ];

            $validator = new Validator($data);
            $validator->required('name', 'Property Name')
                      ->maxLength('name', 255, 'Property Name')
                      ->required('address', 'Address')
                      ->required('property_type', 'Property Type')
                      ->in('property_type', ['apartment_building', 'single_house', 'commercial', 'mixed_use'], 'Property Type')
                      ->required('total_units', 'Total Units')
                      ->numeric('total_units', 'Total Units');

            if ($validator->fails()) {
                setFlashMessage('error', $validator->firstError());
                redirect('?page=properties&action=edit&id=' . $id);
            }

            // Handle image upload
            if (!empty($_FILES['image']['name'])) {
                $uploader = new FileUpload();
                $imagePath = $uploader->upload($_FILES['image'], 'properties', ALLOWED_IMAGE_TYPES);
                if ($imagePath === false) {
                    setFlashMessage('error', $uploader->firstError());
                    redirect('?page=properties&action=edit&id=' . $id);
                }
                // Delete old image if exists
                if (!empty($property['image'])) {
                    $uploader->delete($property['image']);
                }
                $data['image'] = $imagePath;
            }

            if ($propertyModel->update($id, $data, $cid)) {
                ActivityLog::log('update', 'property', $id, 'Updated property: ' . $data['name']);
                setFlashMessage('success', 'Property updated successfully.');
                redirect('?page=properties&action=show&id=' . $id);
            } else {
                setFlashMessage('error', 'Failed to update property.');
                redirect('?page=properties&action=edit&id=' . $id);
            }
        }

        $managers = $userModel->getManagers();
        $pageTitle = 'Edit Property';
        require_once VIEWS_PATH . '/properties/edit.php';
        break;

    case 'show':
        $id = (int) ($id ?? 0);
        $property = $propertyModel->findById($id, $cid);

        if (!$property) {
            setFlashMessage('error', 'Property not found.');
            redirect('?page=properties');
        }

        // Managers can only view their own properties
        if (isManager() && $property['manager_id'] != currentUserId()) {
            setFlashMessage('error', 'You do not have permission to view this property.');
            redirect('?page=properties');
        }

        $units = $unitModel->getByProperty($id);
        $pageTitle = $property['name'];
        require_once VIEWS_PATH . '/properties/show.php';
        break;

    case 'delete':
        if (!isPost()) {
            redirect('?page=properties');
        }

        if (!validateCsrfToken()) {
            setFlashMessage('error', 'Invalid security token. Please try again.');
            redirect('?page=properties');
        }

        $id = (int) ($id ?? 0);
        $property = $propertyModel->findById($id, $cid);

        if (!$property) {
            setFlashMessage('error', 'Property not found.');
            redirect('?page=properties');
        }

        // Managers can only delete their own properties
        if (isManager() && $property['manager_id'] != currentUserId()) {
            setFlashMessage('error', 'You do not have permission to delete this property.');
            redirect('?page=properties');
        }

        if ($propertyModel->delete($id, $cid)) {
            ActivityLog::log('delete', 'property', $id, 'Deleted property: ' . $property['name']);
            setFlashMessage('success', 'Property deleted successfully.');
        } else {
            setFlashMessage('error', 'Failed to delete property.');
        }
        redirect('?page=properties');
        break;

    case 'index':
    default:
        $search = getData('search');
        $city = getData('city');
        $type = getData('type');
        $currentPage = max(1, (int) getData('pg', '1'));
        $offset = ($currentPage - 1) * RECORDS_PER_PAGE;

        // Managers only see their own properties
        $managerId = isManager() ? currentUserId() : null;

        $properties = $propertyModel->getAll($search, $city, $type, $managerId, RECORDS_PER_PAGE, $offset, $cid);
        $totalRecords = $propertyModel->count($search, $city, $type, $managerId, $cid);
        $cities = $propertyModel->getCities($cid);

        $pageTitle = 'Properties';
        require_once VIEWS_PATH . '/properties/index.php';
        break;
}
