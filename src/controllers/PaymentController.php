<?php
/**
 * Payment Controller
 */
require_once SRC_PATH . '/models/Payment.php';
require_once SRC_PATH . '/models/Lease.php';
require_once SRC_PATH . '/models/Unit.php';
require_once SRC_PATH . '/models/Tenant.php';
require_once SRC_PATH . '/models/ActivityLog.php';
require_once SRC_PATH . '/helpers/Mailer.php';

requireAuth();
requireRole('company_admin', 'manager', 'accountant', 'staff');

$paymentModel = new Payment();
$leaseModel = new Lease();
$unitModel = new Unit();
$cid = companyId();

// Auto-mark overdue payments
$paymentModel->markOverduePayments();

switch ($action) {
    case 'index':
        $search = getData('search');
        $status = getData('status');
        $currentPage = max(1, (int) getData('pg', '1'));
        $offset = ($currentPage - 1) * RECORDS_PER_PAGE;

        $payments = $paymentModel->getAll($search, $status, null, null, RECORDS_PER_PAGE, $offset, $cid);
        $totalRecords = $paymentModel->count($search, $status, null, null, $cid);

        $pageTitle = 'Payments';
        require_once VIEWS_PATH . '/payments/index.php';
        break;

    case 'create':
        if (isPost()) {
            if (!validateCsrfToken()) {
                setFlashMessage('error', 'Invalid request. Please try again.');
                redirect('?page=payments&action=create');
            }

            $validator = new Validator($_POST);
            $validator->required('lease_id')
                      ->required('amount')
                      ->required('payment_date')
                      ->required('due_date')
                      ->required('payment_method')
                      ->required('status');
            $validator->positive('amount', 'Amount');

            if ($validator->fails()) {
                setFlashMessage('error', $validator->firstError());
                $_SESSION['old_input'] = $_POST;
                redirect('?page=payments&action=create');
            }

            $leaseId = (int) postData('lease_id');
            $lease = $leaseModel->findById($leaseId, $cid);

            if (!$lease) {
                setFlashMessage('error', 'Selected lease not found.');
                redirect('?page=payments&action=create');
            }

            $paymentId = $paymentModel->create([
                'lease_id'         => $leaseId,
                'tenant_id'        => $lease['tenant_id'],
                'amount'           => (float) postData('amount'),
                'payment_date'     => postData('payment_date'),
                'due_date'         => postData('due_date'),
                'payment_method'   => postData('payment_method'),
                'reference_number' => postData('reference_number') ?: null,
                'status'           => postData('status'),
                'notes'            => postData('notes') ?: null,
            ]);

            ActivityLog::log('payment', 'payment', $paymentId, 'Recorded payment of ' . postData('amount') . ' for lease #' . $leaseId);

            // Send receipt email
            $payment = $paymentModel->findById($paymentId, $cid);
            if ($payment) {
                Mailer::sendPaymentReceipt($payment);
            }

            setFlashMessage('success', 'Payment recorded successfully. Receipt email sent.');
            redirect('?page=payments&action=show&id=' . $paymentId);
        }

        // Get active leases for the form select (scoped to company)
        $leases = $leaseModel->getAll('', 'active', null, 1000, 0, null, $cid);
        $old = $_SESSION['old_input'] ?? [];
        unset($_SESSION['old_input']);

        $pageTitle = 'Record Payment';
        require_once VIEWS_PATH . '/payments/create.php';
        break;

    case 'show':
        $paymentId = $id;
        if (!$paymentId) {
            setFlashMessage('error', 'Payment not found.');
            redirect('?page=payments');
        }

        $payment = $paymentModel->findById($paymentId, $cid);
        if (!$payment) {
            setFlashMessage('error', 'Payment not found.');
            redirect('?page=payments');
        }

        $pageTitle = 'Payment Receipt #' . $payment['id'];
        require_once VIEWS_PATH . '/payments/receipt.php';
        break;

    case 'tenant_history':
        $tenantId = $id;

        if (!$tenantId) {
            setFlashMessage('error', 'Tenant not specified.');
            redirect('?page=payments');
        }

        $tenant = Tenant::findById($tenantId, $cid);
        if (!$tenant) {
            setFlashMessage('error', 'Tenant not found.');
            redirect('?page=payments');
        }

        $payments = $paymentModel->getAll('', '', null, $tenantId, 1000, 0, $cid);

        // Calculate summary stats
        $totalPaid = 0;
        $totalPending = 0;
        $totalOverdue = 0;
        foreach ($payments as $p) {
            if ($p['status'] === 'paid') {
                $totalPaid += $p['amount'];
            } elseif ($p['status'] === 'pending') {
                $totalPending += $p['amount'];
            } elseif ($p['status'] === 'overdue') {
                $totalOverdue += $p['amount'];
            }
        }

        $pageTitle = 'Payment History - ' . Tenant::getFullName($tenant);
        require_once VIEWS_PATH . '/payments/tenant_history.php';
        break;

    case 'send_receipt':
        $paymentId = $id;
        $payment   = $paymentModel->findById($paymentId, $cid);
        if (!$payment) {
            setFlashMessage('error', 'Payment not found.');
            redirect('?page=payments');
        }
        if (Mailer::sendPaymentReceipt($payment)) {
            setFlashMessage('success', 'Receipt emailed to ' . $payment['tenant_email'] . '.');
        } else {
            setFlashMessage('error', 'Could not send email. Check mail settings.');
        }
        redirect('?page=payments&action=show&id=' . $paymentId);
        break;

    case 'mark_auto_paid':
        if (!isPost() || !validateCsrfToken()) {
            setFlashMessage('error', 'Invalid request.');
            redirect('?page=payments');
        }

        $paymentId = $id;
        $payment   = $paymentModel->findById($paymentId, $cid);
        if (!$payment) {
            setFlashMessage('error', 'Payment not found.');
            redirect('?page=payments');
        }

        if ($paymentModel->markAsAutoPaid($paymentId)) {
            ActivityLog::log('payment', 'payment', $paymentId, 'Marked payment #' . $paymentId . ' as auto-paid');
            $updated = $paymentModel->findById($paymentId, $cid);
            if ($updated) {
                Mailer::sendPaymentReceipt($updated);
            }
            setFlashMessage('success', 'Payment marked as automatically paid and receipt emailed.');
        } else {
            setFlashMessage('error', 'Could not mark payment as auto-paid (already paid or not found).');
        }
        redirect('?page=payments&action=show&id=' . $paymentId);
        break;

    case 'delete':
        if (!isPost()) {
            redirect('?page=payments');
        }

        if (!validateCsrfToken()) {
            setFlashMessage('error', 'Invalid request. Please try again.');
            redirect('?page=payments');
        }

        $paymentId = $id;
        if (!$paymentId) {
            setFlashMessage('error', 'Payment not found.');
            redirect('?page=payments');
        }

        $payment = $paymentModel->findById($paymentId, $cid);
        if (!$payment) {
            setFlashMessage('error', 'Payment not found.');
            redirect('?page=payments');
        }

        $paymentModel->delete($paymentId, $cid);
        ActivityLog::log('delete', 'payment', $paymentId, 'Deleted payment #' . $paymentId);
        setFlashMessage('success', 'Payment deleted successfully.');
        redirect('?page=payments');
        break;

    default:
        redirect('?page=payments');
        break;
}
