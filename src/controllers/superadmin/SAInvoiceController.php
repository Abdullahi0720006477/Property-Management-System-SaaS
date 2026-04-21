<?php
require_once __DIR__ . '/../../models/Invoice.php';
require_once __DIR__ . '/../../models/Company.php';

$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        $filters = [];
        if (!empty($_GET['status'])) $filters['status'] = $_GET['status'];
        if (!empty($_GET['company_id'])) $filters['company_id'] = (int)$_GET['company_id'];
        $invoices = Invoice::getAll($filters);
        $pageTitle = 'Invoices';
        require_once VIEWS_PATH . '/superadmin/invoices/index.php';
        break;

    case 'create':
        $companies = Company::getAll();
        if (isPost()) {
            $data = [
                'company_id' => (int)postData('company_id'),
                'invoice_number' => Invoice::generateNumber(),
                'amount' => (float)postData('amount'),
                'tax_amount' => 0,
                'total_amount' => (float)postData('amount'),
                'currency' => 'KES',
                'status' => 'pending',
                'due_date' => postData('due_date'),
                'notes' => postData('notes'),
            ];
            $invoiceId = Invoice::create($data);
            header('Location: superadmin.php?page=invoices&action=show&id=' . $invoiceId);
            exit;
        }
        $pageTitle = 'Create Invoice';
        require_once VIEWS_PATH . '/superadmin/invoices/create.php';
        break;

    case 'show':
        $id = (int)($_GET['id'] ?? 0);
        $invoice = Invoice::findById($id);
        if (!$invoice) {
            echo '<h3>Invoice not found</h3>';
            exit;
        }
        $pageTitle = 'Invoice ' . $invoice['invoice_number'];
        require_once VIEWS_PATH . '/superadmin/invoices/show.php';
        break;

    default:
        header('Location: superadmin.php?page=invoices');
        exit;
}
