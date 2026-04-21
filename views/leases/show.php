<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Leases', 'url' => '?page=leases'], ['label' => 'Lease Details', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Lease Details</h4>
    <div>
        <a href="?page=leases" class="btn btn-outline-secondary me-1">
            <i class="bi bi-arrow-left me-1"></i> Back to Leases
        </a>
        <a href="?page=leases&action=edit&id=<?php echo $lease['id']; ?>" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
    </div>
</div>

<div class="row">
    <!-- Lease Information -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Lease Information</h5>
                <?php echo statusBadge($lease['lease_status']); ?>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted mb-0">Monthly Rent</label>
                        <p class="fw-semibold fs-5"><?php echo formatCurrency((float) $lease['monthly_rent']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted mb-0">Security Deposit</label>
                        <p class="fw-semibold fs-5"><?php echo formatCurrency((float) $lease['security_deposit']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted mb-0">Start Date</label>
                        <p><?php echo formatDate($lease['start_date']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted mb-0">End Date</label>
                        <p><?php echo formatDate($lease['end_date']); ?></p>
                    </div>
                    <?php if (!empty($lease['document_path'])): ?>
                        <div class="col-md-6">
                            <label class="form-label text-muted mb-0">Document</label>
                            <p>
                                <a href="<?php echo uploadUrl($lease['document_path']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-file-earmark-pdf me-1"></i> View Document
                                </a>
                                <a href="<?php echo uploadUrl($lease['document_path']); ?>" download class="btn btn-sm btn-outline-secondary ms-1">
                                    <i class="bi bi-download me-1"></i> Download
                                </a>
                            </p>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($lease['notes'])): ?>
                        <div class="col-12">
                            <label class="form-label text-muted mb-0">Notes</label>
                            <p><?php echo nl2br(e($lease['notes'])); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar: Tenant & Unit Info -->
    <div class="col-lg-4">
        <!-- Tenant Info -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person me-2"></i>Tenant</h5>
            </div>
            <div class="card-body">
                <p class="fw-semibold mb-1"><?php echo e($lease['tenant_name']); ?></p>
                <p class="mb-1"><i class="bi bi-envelope me-1 text-muted"></i> <?php echo e($lease['tenant_email']); ?></p>
                <?php if (!empty($lease['tenant_phone'])): ?>
                    <p class="mb-0"><i class="bi bi-telephone me-1 text-muted"></i> <?php echo e($lease['tenant_phone']); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Unit Info -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-building me-2"></i>Unit</h5>
            </div>
            <div class="card-body">
                <p class="fw-semibold mb-1"><?php echo e($lease['property_name']); ?></p>
                <p class="mb-0">Unit <?php echo e($lease['unit_number']); ?></p>
            </div>
        </div>

        <!-- Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Actions</h5>
            </div>
            <div class="card-body d-grid gap-2">
                <a href="?page=leases&action=edit&id=<?php echo $lease['id']; ?>" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i> Edit Lease
                </a>
                <?php if (in_array($lease['lease_status'], ['active', 'expired'])): ?>
                    <a href="?page=leases&action=renew&id=<?php echo $lease['id']; ?>" class="btn btn-outline-success">
                        <i class="bi bi-arrow-repeat me-1"></i> Renew Lease
                    </a>
                <?php endif; ?>
                <?php if ($lease['lease_status'] === 'active'): ?>
                    <form method="POST" action="?page=leases&action=terminate&id=<?php echo $lease['id']; ?>" onsubmit="return confirm('Are you sure you want to terminate this lease? The unit will be marked as vacant.');">
                        <?php echo csrfField(); ?>
                        <button type="submit" class="btn btn-outline-warning w-100">
                            <i class="bi bi-x-circle me-1"></i> Terminate Lease
                        </button>
                    </form>
                <?php endif; ?>
                <form method="POST" action="?page=leases&action=delete&id=<?php echo $lease['id']; ?>" onsubmit="return confirm('Are you sure you want to delete this lease? This action cannot be undone.');">
                    <?php echo csrfField(); ?>
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="bi bi-trash me-1"></i> Delete Lease
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
