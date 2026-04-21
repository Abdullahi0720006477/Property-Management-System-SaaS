<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Units', 'url' => '?page=units'], ['label' => 'Unit ' . $unit['unit_number'], 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-door-open me-2"></i>Unit <?php echo e($unit['unit_number']); ?></h4>
    <div>
        <a href="?page=units" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Units
        </a>
        <a href="?page=units&action=edit&id=<?php echo $unit['id']; ?>" class="btn btn-primary ms-1">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Unit Details -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Unit Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th class="text-muted" style="width: 40%;">Unit Number</th>
                        <td><?php echo e($unit['unit_number']); ?></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Status</th>
                        <td><?php echo statusBadge($unit['status']); ?></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Floor</th>
                        <td><?php echo $unit['floor_number'] !== null ? e($unit['floor_number']) : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Bedrooms</th>
                        <td><?php echo (int) $unit['bedrooms']; ?></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Bathrooms</th>
                        <td><?php echo (int) $unit['bathrooms']; ?></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Area</th>
                        <td><?php echo $unit['area_sqft'] ? e($unit['area_sqft']) . ' sq ft' : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Rent Amount</th>
                        <td class="fw-semibold"><?php echo formatCurrency($unit['rent_amount']); ?></td>
                    </tr>
                    <?php if (!empty($unit['description'])): ?>
                    <tr>
                        <th class="text-muted">Description</th>
                        <td><?php echo e($unit['description']); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <!-- Property Info -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Property Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th class="text-muted" style="width: 40%;">Property Name</th>
                        <td>
                            <a href="?page=properties&action=show&id=<?php echo $unit['property_id']; ?>" class="text-decoration-none">
                                <?php echo e($unit['property_name']); ?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted">Address</th>
                        <td><?php echo e($unit['property_address'] ?? 'N/A'); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Current Tenant / Lease Info -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Lease & Tenant Information</h5>
                <?php if ($unit['status'] === 'vacant'): ?>
                    <a href="?page=leases&action=create&unit_id=<?php echo $unit['id']; ?>" class="btn btn-success btn-sm">
                        <i class="bi bi-plus-lg me-1"></i> Create Lease
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (!empty($unitWithLease['lease_id'])): ?>
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th class="text-muted" style="width: 25%;">Tenant</th>
                            <td>
                                <a href="?page=users&action=show&id=<?php echo $unitWithLease['tenant_id']; ?>" class="text-decoration-none">
                                    <?php echo e($unitWithLease['tenant_name']); ?>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Monthly Rent</th>
                            <td><?php echo formatCurrency($unitWithLease['monthly_rent']); ?></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Lease Period</th>
                            <td><?php echo formatDate($unitWithLease['start_date']); ?> &mdash; <?php echo formatDate($unitWithLease['end_date']); ?></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Lease</th>
                            <td>
                                <a href="?page=leases&action=show&id=<?php echo $unitWithLease['lease_id']; ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-file-earmark-text me-1"></i> View Lease
                                </a>
                            </td>
                        </tr>
                    </table>
                <?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                        <p class="mb-0">No active lease for this unit.</p>
                        <?php if ($unit['status'] === 'vacant'): ?>
                            <a href="?page=leases&action=create&unit_id=<?php echo $unit['id']; ?>" class="btn btn-success btn-sm mt-2">
                                <i class="bi bi-plus-lg me-1"></i> Create Lease
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Delete -->
<div class="mt-4">
    <form method="POST" action="?page=units&action=delete&id=<?php echo $unit['id']; ?>" onsubmit="return confirm('Are you sure you want to delete this unit? This action cannot be undone.');">
        <?php echo csrfField(); ?>
        <button type="submit" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-trash me-1"></i> Delete Unit
        </button>
    </form>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
