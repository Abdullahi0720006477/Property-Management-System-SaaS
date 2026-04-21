<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Payments', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Payments</h4>
    <a href="?page=payments&action=create" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Record Payment
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="page" value="payments">
            <div class="col-md-5">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" value="<?php echo e($search); ?>" placeholder="Search by tenant, reference or unit...">
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Statuses</option>
                    <option value="paid" <?php echo $status === 'paid' ? 'selected' : ''; ?>>Paid</option>
                    <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="overdue" <?php echo $status === 'overdue' ? 'selected' : ''; ?>>Overdue</option>
                    <option value="partial" <?php echo $status === 'partial' ? 'selected' : ''; ?>>Partial</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-search me-1"></i> Filter</button>
            </div>
            <div class="col-md-2">
                <a href="?page=payments" class="btn btn-outline-secondary w-100"><i class="bi bi-x-circle me-1"></i> Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- Payments Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Tenant</th>
                        <th>Unit</th>
                        <th>Amount</th>
                        <th>Due Date</th>
                        <th>Payment Date</th>
                        <th>Method</th>
                        <th>Ref #</th>
                        <th>Status</th>
                        <th>Auto</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($payments)): ?>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td>
                                    <a href="?page=payments&action=tenant_history&id=<?php echo $payment['tenant_id']; ?>" class="text-decoration-none">
                                        <?php echo e($payment['tenant_name']); ?>
                                    </a>
                                </td>
                                <td><?php echo e($payment['unit_number']); ?> <small class="text-muted">(<?php echo e($payment['property_name']); ?>)</small></td>
                                <td class="fw-semibold"><?php echo formatCurrency($payment['amount']); ?></td>
                                <td><?php echo formatDate($payment['due_date']); ?></td>
                                <td><?php echo formatDate($payment['payment_date']); ?></td>
                                <td><?php echo e(ucwords(str_replace('_', ' ', $payment['payment_method']))); ?></td>
                                <td><?php echo e($payment['reference_number'] ?? '-'); ?></td>
                                <td><?php echo statusBadge($payment['status']); ?></td>
                                <td>
                                    <?php if (!empty($payment['is_auto_paid']) && $payment['is_auto_paid']): ?>
                                        <span class="badge bg-primary" title="Auto-processed on <?php echo e($payment['auto_paid_at'] ?? ''); ?>">
                                            <i class="bi bi-lightning-fill me-1"></i>Auto-Paid
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-secondary border">Manual</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="?page=payments&action=show&id=<?php echo $payment['id']; ?>" class="btn btn-outline-info" title="View Receipt">
                                            <i class="bi bi-receipt"></i>
                                        </a>
                                        <?php if (in_array($payment['status'], ['pending','overdue'])): ?>
                                        <form method="POST" action="?page=payments&action=mark_auto_paid&id=<?php echo $payment['id']; ?>" class="d-inline">
                                            <?php echo csrfField(); ?>
                                            <button type="submit" class="btn btn-outline-primary btn-sm" title="Mark as Auto-Paid"
                                                    onclick="return confirm('Mark Payment #<?php echo $payment['id']; ?> as automatically paid?')">
                                                <i class="bi bi-lightning-fill"></i>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                        <form method="POST" action="?page=payments&action=delete&id=<?php echo $payment['id']; ?>" class="d-inline delete-form">
                                            <?php echo csrfField(); ?>
                                            <button type="button" class="btn btn-outline-danger btn-sm" title="Delete"
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                    data-item-name="<?php echo e('Payment #' . $payment['id']); ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No payments found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<?php
    $baseUrl = '?page=payments&search=' . urlencode($search) . '&status=' . urlencode($status);
    echo paginate($totalRecords, $currentPage, RECORDS_PER_PAGE, $baseUrl);
?>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
