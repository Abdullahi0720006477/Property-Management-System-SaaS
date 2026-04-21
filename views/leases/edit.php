<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Leases', 'url' => '?page=leases'], ['label' => 'Edit Lease', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Edit Lease</h4>
    <a href="?page=leases&action=show&id=<?php echo $lease['id']; ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Lease
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="?page=leases&action=edit&id=<?php echo $lease['id']; ?>" enctype="multipart/form-data">
            <?php echo csrfField(); ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="tenant_id" class="form-label">Tenant <span class="text-danger">*</span></label>
                    <select class="form-select" id="tenant_id" name="tenant_id" required>
                        <option value="">Select Tenant</option>
                        <?php foreach ($tenants as $tenant): ?>
                            <option value="<?php echo $tenant['id']; ?>" <?php echo ($lease['tenant_id'] == $tenant['id']) ? 'selected' : ''; ?>>
                                <?= e($tenant['first_name'] . ' ' . $tenant['last_name']) ?> (<?php echo e($tenant['email']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="unit_id" class="form-label">Unit <span class="text-danger">*</span></label>
                    <select class="form-select" id="unit_id" name="unit_id" required>
                        <option value="">Select Unit</option>
                        <?php foreach ($vacantUnits as $unit): ?>
                            <option value="<?php echo $unit['id']; ?>" <?php echo ($lease['unit_id'] == $unit['id']) ? 'selected' : ''; ?>>
                                <?php echo e($unit['property_name']); ?> - Unit <?php echo e($unit['unit_number']); ?>
                            </option>
                        <?php endforeach; ?>
                        <?php
                            // Always include the currently assigned unit even if it is no longer vacant
                            $currentUnitInList = false;
                            foreach ($vacantUnits as $unit) {
                                if ($unit['id'] == $lease['unit_id']) {
                                    $currentUnitInList = true;
                                    break;
                                }
                            }
                            if (!$currentUnitInList):
                        ?>
                            <option value="<?php echo $lease['unit_id']; ?>" selected>
                                <?php echo e($lease['property_name']); ?> - Unit <?php echo e($lease['unit_number']); ?> (current)
                            </option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo e($lease['start_date']); ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo e($lease['end_date']); ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="monthly_rent" class="form-label">Monthly Rent <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><?php echo CURRENCY_SYMBOL; ?></span>
                        <input type="number" class="form-control" id="monthly_rent" name="monthly_rent" value="<?php echo e($lease['monthly_rent']); ?>" step="0.01" min="0" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="security_deposit" class="form-label">Security Deposit</label>
                    <div class="input-group">
                        <span class="input-group-text"><?php echo CURRENCY_SYMBOL; ?></span>
                        <input type="number" class="form-control" id="security_deposit" name="security_deposit" value="<?php echo e($lease['security_deposit']); ?>" step="0.01" min="0">
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="lease_status" class="form-label">Status</label>
                    <select class="form-select" id="lease_status" name="lease_status">
                        <option value="active" <?php echo $lease['lease_status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="pending" <?php echo $lease['lease_status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="expired" <?php echo $lease['lease_status'] === 'expired' ? 'selected' : ''; ?>>Expired</option>
                        <option value="terminated" <?php echo $lease['lease_status'] === 'terminated' ? 'selected' : ''; ?>>Terminated</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="document" class="form-label">Lease Document</label>
                    <input type="file" class="form-control" id="document" name="document" accept="application/pdf">
                    <div class="form-text">Accepted format: PDF</div>
                    <?php if (!empty($lease['document_path'])): ?>
                        <div class="mt-1">
                            <a href="<?php echo uploadUrl($lease['document_path']); ?>" target="_blank" class="small">
                                <i class="bi bi-file-earmark-pdf me-1"></i> View current document
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-12">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="4"><?php echo e($lease['notes']); ?></textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i> Update Lease</button>
                <a href="?page=leases&action=show&id=<?php echo $lease['id']; ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
