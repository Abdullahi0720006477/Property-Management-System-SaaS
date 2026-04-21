<?php
$old = $_SESSION['old_input'] ?? $renewal ?? [];
unset($_SESSION['old_input']);
?>
<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Leases', 'url' => '?page=leases'], ['label' => isset($isRenewal) ? 'Renew Lease' : 'Create Lease', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><?php echo isset($isRenewal) ? 'Renew Lease' : 'Create Lease'; ?></h4>
    <a href="?page=leases" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Leases
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="?page=leases&action=<?php echo isset($isRenewal) ? 'renew&id=' . $oldLeaseId : 'create'; ?>" enctype="multipart/form-data">
            <?php echo csrfField(); ?>

            <?php if (isset($isRenewal)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-arrow-repeat me-1"></i> Renewing lease #<?php echo $oldLeaseId; ?>. The previous lease will be marked as expired.
                </div>
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="tenant_id" class="form-label">Tenant <span class="text-danger">*</span></label>
                    <select class="form-select" id="tenant_id" name="tenant_id" required>
                        <option value="">Select Tenant</option>
                        <?php foreach ($tenants as $tenant): ?>
                            <option value="<?php echo $tenant['id']; ?>" <?php echo ($old['tenant_id'] ?? postData('tenant_id')) == $tenant['id'] ? 'selected' : ''; ?>>
                                <?= e($tenant['first_name'] . ' ' . $tenant['last_name']) ?> (<?php echo e($tenant['email']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="unit_id" class="form-label">Unit <span class="text-danger">*</span></label>
                    <select class="form-select" id="unit_id" name="unit_id" required>
                        <option value="">Select Vacant Unit</option>
                        <?php foreach ($vacantUnits as $unit): ?>
                            <option value="<?php echo $unit['id']; ?>" <?php echo ($old['unit_id'] ?? postData('unit_id')) == $unit['id'] ? 'selected' : ''; ?>>
                                <?php echo e($unit['property_name']); ?> - Unit <?php echo e($unit['unit_number']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo e($old['start_date'] ?? postData('start_date')); ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo e($old['end_date'] ?? postData('end_date')); ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="monthly_rent" class="form-label">Monthly Rent <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><?php echo CURRENCY_SYMBOL; ?></span>
                        <input type="number" class="form-control" id="monthly_rent" name="monthly_rent" value="<?php echo e($old['monthly_rent'] ?? postData('monthly_rent')); ?>" step="0.01" min="0" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="security_deposit" class="form-label">Security Deposit</label>
                    <div class="input-group">
                        <span class="input-group-text"><?php echo CURRENCY_SYMBOL; ?></span>
                        <input type="number" class="form-control" id="security_deposit" name="security_deposit" value="<?php echo e($old['security_deposit'] ?? postData('security_deposit')); ?>" step="0.01" min="0">
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="lease_status" class="form-label">Status</label>
                    <?php if (isset($isRenewal)): ?>
                        <input type="text" class="form-control" value="Active" disabled>
                        <input type="hidden" name="lease_status" value="active">
                    <?php else: ?>
                    <select class="form-select" id="lease_status" name="lease_status">
                        <option value="active" <?php echo postData('lease_status') === 'active' || postData('lease_status') === '' ? 'selected' : ''; ?>>Active</option>
                        <option value="pending" <?php echo postData('lease_status') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    </select>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label for="document" class="form-label">Lease Document</label>
                    <input type="file" class="form-control" id="document" name="document" accept="application/pdf">
                    <div class="form-text">Accepted format: PDF</div>
                </div>

                <div class="col-12">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="4"><?php echo e($old['notes'] ?? postData('notes')); ?></textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i> <?php echo isset($isRenewal) ? 'Renew Lease' : 'Create Lease'; ?></button>
                <a href="?page=leases" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
