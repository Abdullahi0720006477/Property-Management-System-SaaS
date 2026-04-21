<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Payment History - <?php echo e($tenant['first_name'] . ' ' . $tenant['last_name']); ?></h4>
    <?php if (!isTenant()): ?>
    <a href="?page=payments" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Payments
    </a>
    <?php endif; ?>
</div>

<?php
$totalAutoPaid = 0;
foreach ($payments as $p) {
    if (!empty($p['is_auto_paid']) && $p['is_auto_paid'] && $p['status'] === 'paid') {
        $totalAutoPaid += $p['amount'];
    }
}
?>
<!-- Summary Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 me-3">
                    <i class="bi bi-check-circle fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Paid</div>
                    <div class="fs-5 fw-bold text-success"><?php echo formatCurrency($totalPaid); ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-warning">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3 me-3">
                    <i class="bi bi-clock fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Pending</div>
                    <div class="fs-5 fw-bold text-warning"><?php echo formatCurrency($totalPending); ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-danger">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-danger bg-opacity-10 text-danger p-3 me-3">
                    <i class="bi bi-exclamation-triangle fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Overdue</div>
                    <div class="fs-5 fw-bold text-danger"><?php echo formatCurrency($totalOverdue); ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3 me-3">
                    <i class="bi bi-lightning-fill fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small">Auto-Paid</div>
                    <div class="fs-5 fw-bold text-primary"><?php echo formatCurrency($totalAutoPaid); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payments Table -->
<div class="card">
    <div class="card-header bg-white">
        <h6 class="mb-0">All Payments (<?php echo count($payments); ?>)</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Unit</th>
                        <th>Amount</th>
                        <th>Due Date</th>
                        <th>Payment Date</th>
                        <th>Method</th>
                        <th>Reference</th>
                        <th>Status</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($payments)): ?>
                        <?php foreach ($payments as $index => $payment): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo e($payment['unit_number']); ?> <small class="text-muted">(<?php echo e($payment['property_name']); ?>)</small></td>
                                <td class="fw-semibold"><?php echo formatCurrency($payment['amount']); ?></td>
                                <td><?php echo formatDate($payment['due_date']); ?></td>
                                <td><?php echo formatDate($payment['payment_date']); ?></td>
                                <td><?php echo e(ucwords(str_replace('_', ' ', $payment['payment_method']))); ?></td>
                                <td><?php echo e($payment['reference_number'] ?? '-'); ?></td>
                                <td><?php echo statusBadge($payment['status']); ?></td>
                                <td>
                                    <?php if (!empty($payment['is_auto_paid']) && $payment['is_auto_paid']): ?>
                                        <span class="badge bg-primary"><i class="bi bi-lightning-fill me-1"></i>Auto</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-secondary border">Manual</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?page=payments&action=show&id=<?php echo $payment['id']; ?>" class="btn btn-sm btn-outline-info" title="View Receipt">
                                        <i class="bi bi-receipt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No payments found for this tenant.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
