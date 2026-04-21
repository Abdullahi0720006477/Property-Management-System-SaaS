<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Payments', 'url' => '?page=payments'], ['label' => 'Payment #' . $payment['id'], 'url' => '']]) ?>

<style>
@media print {
    .sidebar, .top-navbar, .no-print, footer {
        display: none !important;
    }
    .main-content {
        margin-left: 0 !important;
        padding: 0 !important;
    }
    .receipt-card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <h4 class="mb-0">Payment Receipt</h4>
    <div class="d-flex gap-2 flex-wrap">
        <button onclick="window.print()" class="btn btn-outline-primary">
            <i class="bi bi-printer me-1"></i> Print
        </button>
        <?php if (in_array($payment['status'], ['pending','overdue'])): ?>
        <form method="POST" action="?page=payments&action=mark_auto_paid&id=<?php echo $payment['id']; ?>" class="d-inline">
            <?php echo csrfField(); ?>
            <button type="submit" class="btn btn-primary"
                    onclick="return confirm('Mark this payment as automatically paid and email receipt?')">
                <i class="bi bi-lightning-fill me-1"></i> Mark as Auto-Paid
            </button>
        </form>
        <?php endif; ?>
        <a href="?page=payments&action=send_receipt&id=<?php echo $payment['id']; ?>" class="btn btn-outline-success">
            <i class="bi bi-envelope me-1"></i> Email Receipt
        </a>
        <a href="?page=payments" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="card receipt-card mx-auto" style="max-width: 700px;">
    <div class="card-body p-4">
        <!-- Header -->
        <div class="text-center border-bottom pb-3 mb-4">
            <h3 class="fw-bold mb-1"><?php echo APP_NAME; ?></h3>
            <p class="text-muted mb-1">Payment Receipt</p>
            <h5 class="text-primary">Receipt #<?php echo str_pad($payment['id'], 6, '0', STR_PAD_LEFT); ?></h5>
        </div>

        <!-- Payment Details -->
        <div class="row mb-4">
            <div class="col-sm-6">
                <h6 class="text-muted text-uppercase small mb-2">Tenant Details</h6>
                <p class="mb-1 fw-semibold"><?php echo e($payment['tenant_name']); ?></p>
                <?php if (!empty($payment['tenant_email'])): ?>
                    <p class="mb-1 small"><?php echo e($payment['tenant_email']); ?></p>
                <?php endif; ?>
                <?php if (!empty($payment['tenant_phone'])): ?>
                    <p class="mb-0 small"><?php echo e($payment['tenant_phone']); ?></p>
                <?php endif; ?>
            </div>
            <div class="col-sm-6 text-sm-end">
                <h6 class="text-muted text-uppercase small mb-2">Property Details</h6>
                <p class="mb-1 fw-semibold"><?php echo e($payment['property_name']); ?></p>
                <p class="mb-0 small">Unit: <?php echo e($payment['unit_number']); ?></p>
            </div>
        </div>

        <!-- Amount Box -->
        <div class="bg-light rounded p-3 text-center mb-4">
            <p class="text-muted small mb-1">Amount Paid</p>
            <h2 class="fw-bold text-success mb-0"><?php echo formatCurrency($payment['amount']); ?></h2>
            <div class="mt-2">
                <?php echo statusBadge($payment['status']); ?>
                <?php if (!empty($payment['is_auto_paid']) && $payment['is_auto_paid']): ?>
                    <span class="badge bg-primary ms-1"><i class="bi bi-lightning-fill me-1"></i>Paid Automatically</span>
                <?php endif; ?>
            </div>
            <?php if (!empty($payment['is_auto_paid']) && $payment['is_auto_paid'] && !empty($payment['auto_paid_at'])): ?>
                <p class="text-muted small mt-1 mb-0">Auto-processed: <?php echo formatDateTime($payment['auto_paid_at']); ?></p>
            <?php endif; ?>
        </div>

        <!-- Details Table -->
        <table class="table table-borderless">
            <tbody>
                <tr>
                    <td class="text-muted" style="width: 40%;">Payment Date</td>
                    <td class="fw-semibold"><?php echo formatDate($payment['payment_date']); ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Due Date</td>
                    <td class="fw-semibold"><?php echo formatDate($payment['due_date']); ?></td>
                </tr>
                <tr>
                    <td class="text-muted">Payment Method</td>
                    <td class="fw-semibold"><?php echo e(ucwords(str_replace('_', ' ', $payment['payment_method']))); ?></td>
                </tr>
                <?php if (!empty($payment['reference_number'])): ?>
                <tr>
                    <td class="text-muted"><?php echo $payment['payment_method'] === 'mpesa' ? 'M-Pesa Transaction ID' : 'Reference Number'; ?></td>
                    <td class="fw-semibold">
                        <?php echo e($payment['reference_number']); ?>
                        <?php if ($payment['payment_method'] === 'mpesa'): ?>
                            <span class="badge bg-success ms-2"><i class="bi bi-phone me-1"></i>M-Pesa</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td class="text-muted">Monthly Rent</td>
                    <td class="fw-semibold"><?php echo formatCurrency($payment['monthly_rent']); ?></td>
                </tr>
                <?php if (!empty($payment['notes'])): ?>
                <tr>
                    <td class="text-muted">Notes</td>
                    <td><?php echo e($payment['notes']); ?></td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Footer -->
        <div class="border-top pt-3 mt-3 text-center">
            <p class="text-muted small mb-1">This is a computer-generated receipt and does not require a signature.</p>
            <p class="text-muted small mb-0">Generated on <?php echo date(DISPLAY_DATETIME_FORMAT); ?></p>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
