<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">My Dashboard</h4>
    <span class="text-muted"><?php echo date(DISPLAY_DATE_FORMAT); ?></span>
</div>

<?php if ($activeLease): ?>
<!-- Lease Info -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-house-door me-2"></i>My Unit</div>
            <div class="card-body">
                <h5><?php echo e($activeLease['property_name']); ?> - Unit <?php echo e($activeLease['unit_number']); ?></h5>
                <p class="text-muted mb-1"><?php echo e($activeLease['property_address']); ?></p>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted">Monthly Rent</small>
                        <div class="fw-bold"><?php echo formatCurrency($activeLease['monthly_rent']); ?></div>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Lease Status</small>
                        <div><?php echo statusBadge($activeLease['lease_status']); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-calendar me-2"></i>Lease Details</div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <small class="text-muted">Start Date</small>
                        <div><?php echo formatDate($activeLease['start_date']); ?></div>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">End Date</small>
                        <div><?php echo formatDate($activeLease['end_date']); ?></div>
                    </div>
                </div>
                <div class="d-grid">
                    <a href="?page=maintenance&action=create" class="btn btn-warning">
                        <i class="bi bi-tools me-1"></i> Submit Maintenance Request
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>You don't have an active lease. Contact your property manager for assistance.
</div>
<?php endif; ?>

<!-- Payment History -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between">
        <span><i class="bi bi-cash-stack me-2"></i>Recent Payments</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Date</th><th>Due Date</th><th>Amount</th><th>Method</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($recentPayments as $pay): ?>
                        <tr>
                            <td><?php echo formatDate($pay['payment_date']); ?></td>
                            <td><?php echo formatDate($pay['due_date']); ?></td>
                            <td><?php echo formatCurrency($pay['amount']); ?></td>
                            <td><?php echo e(ucfirst(str_replace('_', ' ', $pay['payment_method']))); ?></td>
                            <td><?php echo statusBadge($pay['status']); ?></td>
                            <td>
                                <?php if (!empty($pay['is_auto_paid']) && $pay['is_auto_paid']): ?>
                                    <span class="badge bg-primary"><i class="bi bi-lightning-fill me-1"></i>Auto-Paid</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recentPayments)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-3">No payment records</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- My Maintenance Requests -->
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <span><i class="bi bi-tools me-2"></i>My Maintenance Requests</span>
        <?php if ($activeLease): ?>
            <a href="?page=maintenance&action=create" class="btn btn-sm btn-primary">New Request</a>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Title</th><th>Priority</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                    <?php foreach ($myRequests as $req): ?>
                        <tr>
                            <td><a href="?page=maintenance&action=show&id=<?php echo $req['id']; ?>"><?php echo e($req['title']); ?></a></td>
                            <td><?php echo priorityBadge($req['priority']); ?></td>
                            <td><?php echo statusBadge($req['status']); ?></td>
                            <td><?php echo formatDate($req['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($myRequests)): ?>
                        <tr><td colspan="4" class="text-center text-muted py-3">No maintenance requests</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
