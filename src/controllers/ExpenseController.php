<?php
/**
 * Expense Controller
 */
require_once SRC_PATH . '/models/Expense.php';
require_once SRC_PATH . '/models/Property.php';
require_once SRC_PATH . '/models/ActivityLog.php';

requireRole('company_admin', 'manager', 'accountant');

$expenseModel = new Expense();
$propertyModel = new Property();
$cid = companyId();

$action = $action ?? 'index';

switch ($action) {
    case 'create':
        if (isPost()) {
            if (!validateCsrfToken()) {
                setFlashMessage('error', 'Invalid security token. Please try again.');
                redirect('?page=expenses&action=create');
            }

            $data = [
                'property_id'  => postData('property_id'),
                'category'     => postData('category'),
                'description'  => postData('description'),
                'amount'       => postData('amount'),
                'expense_date' => postData('expense_date'),
                'recorded_by'  => currentUserId(),
            ];

            $validator = new Validator($data);
            $validator->required('property_id', 'Property')
                      ->numeric('property_id', 'Property')
                      ->required('category', 'Category')
                      ->in('category', ['maintenance', 'utilities', 'insurance', 'taxes', 'management', 'other'], 'Category')
                      ->required('description', 'Description')
                      ->maxLength('description', 500, 'Description')
                      ->required('amount', 'Amount')
                      ->numeric('amount', 'Amount')
                      ->positive('amount', 'Amount')
                      ->required('expense_date', 'Expense Date');

            if ($validator->fails()) {
                setFlashMessage('error', $validator->firstError());
                redirect('?page=expenses&action=create');
            }

            // Verify property belongs to this company
            $property = $propertyModel->findById((int) $data['property_id'], $cid);
            if (!$property) {
                setFlashMessage('error', 'Invalid property selected.');
                redirect('?page=expenses&action=create');
            }

            // Handle receipt upload
            if (!empty($_FILES['receipt']['name'])) {
                $uploader = new FileUpload();
                $allowedTypes = array_merge(ALLOWED_IMAGE_TYPES, ALLOWED_DOC_TYPES);
                $receiptPath = $uploader->upload($_FILES['receipt'], 'expenses', $allowedTypes);
                if ($receiptPath === false) {
                    setFlashMessage('error', $uploader->firstError());
                    redirect('?page=expenses&action=create');
                }
                $data['receipt_path'] = $receiptPath;
            }

            $id = $expenseModel->create($data);
            if ($id) {
                ActivityLog::log('create', 'expense', $id, 'Created expense: ' . $data['description'] . ' (' . $data['amount'] . ')');
                setFlashMessage('success', 'Expense recorded successfully.');
                redirect('?page=expenses');
            } else {
                setFlashMessage('error', 'Failed to record expense.');
                redirect('?page=expenses&action=create');
            }
        }

        $properties = $propertyModel->getAll('', '', '', null, 1000, 0, $cid);
        $pageTitle = 'Add Expense';
        require_once VIEWS_PATH . '/expenses/create.php';
        break;

    case 'delete':
        if (!isPost()) {
            redirect('?page=expenses');
        }

        if (!validateCsrfToken()) {
            setFlashMessage('error', 'Invalid security token. Please try again.');
            redirect('?page=expenses');
        }

        $id = (int) ($id ?? 0);
        $expense = $expenseModel->findById($id, $cid);

        if (!$expense) {
            setFlashMessage('error', 'Expense not found.');
            redirect('?page=expenses');
        }

        if ($expenseModel->delete($id, $cid)) {
            ActivityLog::log('delete', 'expense', $id, 'Deleted expense #' . $id);
            setFlashMessage('success', 'Expense deleted successfully.');
        } else {
            setFlashMessage('error', 'Failed to delete expense.');
        }
        redirect('?page=expenses');
        break;

    case 'index':
    default:
        $propertyId = getData('property_id') ? (int) getData('property_id') : null;
        $category = getData('category');
        $startDate = getData('start_date');
        $endDate = getData('end_date');
        $currentPage = max(1, (int) getData('pg', '1'));
        $offset = ($currentPage - 1) * RECORDS_PER_PAGE;

        $expenses = $expenseModel->getAll('', $propertyId, $category, $startDate, $endDate, RECORDS_PER_PAGE, $offset, $cid);
        $totalRecords = $expenseModel->count('', $propertyId, $category, $startDate, $endDate, $cid);
        $properties = $propertyModel->getAll('', '', '', null, 1000, 0, $cid);

        $pageTitle = 'Expenses';
        require_once VIEWS_PATH . '/expenses/index.php';
        break;
}
