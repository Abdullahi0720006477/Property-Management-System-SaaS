<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Payments', 'url' => '?page=payments'], ['label' => 'Record Payment', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Record Payment</h4>
    <a href="?page=payments" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Payments
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="?page=payments&action=create">
            <?php echo csrfField(); ?>

            <div class="row g-3">
                <!-- Lease Select -->
                <div class="col-md-12">
                    <label for="lease_id" class="form-label">Lease <span class="text-danger">*</span></label>
                    <select class="form-select" id="lease_id" name="lease_id" required>
                        <option value="">-- Select Lease --</option>
                        <?php foreach ($leases as $lease): ?>
                            <option value="<?php echo $lease['id']; ?>" <?php echo (($old['lease_id'] ?? '') == $lease['id']) ? 'selected' : ''; ?>>
                                <?php echo e($lease['tenant_name']); ?> - <?php echo e($lease['unit_number']); ?> - <?php echo e($lease['property_name']); ?> (<?php echo formatCurrency($lease['monthly_rent']); ?>/mo)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Amount -->
                <div class="col-md-4">
                    <label for="amount" class="form-label">Amount (<?php echo CURRENCY_SYMBOL; ?>) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0" value="<?php echo e($old['amount'] ?? ''); ?>" required>
                </div>

                <!-- Payment Date -->
                <div class="col-md-4">
                    <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="payment_date" name="payment_date" value="<?php echo e($old['payment_date'] ?? date('Y-m-d')); ?>" required>
                </div>

                <!-- Due Date -->
                <div class="col-md-4">
                    <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="due_date" name="due_date" value="<?php echo e($old['due_date'] ?? ''); ?>" required>
                </div>

                <!-- Payment Method -->
                <div class="col-md-4">
                    <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                    <select class="form-select" id="payment_method" name="payment_method" required>
                        <option value="">-- Select Method --</option>
                        <?php
                        $methods = ['cash' => 'Cash', 'bank_transfer' => 'Bank Transfer', 'mpesa' => 'M-Pesa', 'cheque' => 'Cheque', 'online' => 'Online'];
                        foreach ($methods as $val => $label):
                        ?>
                            <option value="<?php echo $val; ?>" <?php echo (($old['payment_method'] ?? '') === $val) ? 'selected' : ''; ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Reference Number -->
                <div class="col-md-4">
                    <label for="reference_number" class="form-label">Reference Number</label>
                    <input type="text" class="form-control" id="reference_number" name="reference_number" value="<?php echo e($old['reference_number'] ?? ''); ?>" placeholder="e.g. Transaction ID">
                </div>

                <!-- Status -->
                <div class="col-md-4">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select" id="status" name="status" required>
                        <?php
                        $statuses = ['paid' => 'Paid', 'pending' => 'Pending', 'overdue' => 'Overdue', 'partial' => 'Partial'];
                        foreach ($statuses as $val => $label):
                        ?>
                            <option value="<?php echo $val; ?>" <?php echo (($old['status'] ?? 'paid') === $val) ? 'selected' : ''; ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Notes -->
                <div class="col-md-12">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Optional notes..."><?php echo e($old['notes'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i> Record Payment
                </button>
                <a href="?page=payments" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
