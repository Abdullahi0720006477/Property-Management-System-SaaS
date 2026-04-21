<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Overdue Payments</h4>
        <small class="text-muted"><?php echo count($overduePayments); ?> overdue payment<?php echo count($overduePayments) !== 1 ? 's' : ''; ?></small>
    </div>
    <div>
        <a href="?page=reports&action=export_csv&type=overdue" class="btn btn-outline-success me-2">
            <i class="bi bi-download me-1"></i> Export CSV
        </a>
        <a href="?page=reports" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<!-- Summary Card -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-danger">
            <div class="card-body text-center">
                <div class="stat-value text-danger"><?php echo formatCurrency($totalOverdue); ?></div>
                <div class="stat-label text-muted">Total Overdue Amount</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="stat-value"><?php echo count($overduePayments); ?></div>
                <div class="stat-label text-muted">Overdue Payments</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <?php
                    $avgOverdue = count($overduePayments) > 0
                        ? round(array_sum(array_map(function($p) {
                            return ceil((time() - strtotime($p['due_date'])) / 86400);
                          }, $overduePayments)) / count($overduePayments))
                        : 0;
                ?>
                <div class="stat-value text-warning"><?php echo $avgOverdue; ?></div>
                <div class="stat-label text-muted">Avg. Days Overdue</div>
            </div>
        </div>
    </div>
</div>

<!-- Overdue Payments Table -->
<div class="card">
    <div class="card-header">Overdue Payment Details</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Tenant</th>
                        <th>Property</th>
                        <th>Unit</th>
                        <th class="text-end">Amount</th>
                        <th>Due Date</th>
                        <th class="text-center">Days Overdue</th>
                        <th>Phone</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($overduePayments)): ?>
                        <?php foreach ($overduePayments as $p):
                            $daysOverdue = ceil((time() - strtotime($p['due_date'])) / 86400);
                        ?>
                            <tr>
                                <td class="fw-semibold"><?php echo e($p['tenant_name']); ?></td>
                                <td><?php echo e($p['property_name']); ?></td>
                                <td><?php echo e($p['unit_number']); ?></td>
                                <td class="text-end"><?php echo formatCurrency((float) $p['amount']); ?></td>
                                <td><?php echo formatDate($p['due_date']); ?></td>
                                <td class="text-center">
                                    <span class="badge <?php echo $daysOverdue > 30 ? 'bg-danger' : ($daysOverdue > 14 ? 'bg-warning text-dark' : 'bg-info'); ?>">
                                        <?php echo $daysOverdue; ?> day<?php echo $daysOverdue !== 1 ? 's' : ''; ?>
                                    </span>
                                </td>
                                <td><?php echo e($p['tenant_phone'] ?? 'N/A'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No overdue payments found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($overduePayments)): ?>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="3">Total</th>
                        <th class="text-end"><?php echo formatCurrency($totalOverdue); ?></th>
                        <th colspan="3"></th>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
