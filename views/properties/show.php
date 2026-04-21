<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Properties', 'url' => '?page=properties'], ['label' => $property['name'], 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><?php echo e($property['name']); ?></h4>
    <div>
        <a href="?page=properties" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Properties
        </a>
        <a href="?page=properties&action=edit&id=<?php echo $property['id']; ?>" class="btn btn-primary ms-1">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Property Details -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Property Details</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($property['image'])): ?>
                    <div class="mb-4">
                        <img src="<?php echo uploadUrl($property['image']); ?>" alt="<?php echo e($property['name']); ?>" class="img-fluid rounded" style="max-height: 300px; width: 100%; object-fit: cover;">
                    </div>
                <?php endif; ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <strong class="text-muted d-block mb-1">Address</strong>
                        <span><?php echo e($property['address']); ?></span>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-muted d-block mb-1">City</strong>
                        <span><?php echo e($property['city'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-muted d-block mb-1">Property Type</strong>
                        <span><?php echo e(ucwords(str_replace('_', ' ', $property['property_type']))); ?></span>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-muted d-block mb-1">Total Units</strong>
                        <span><?php echo (int) $property['total_units']; ?></span>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-muted d-block mb-1">Manager</strong>
                        <span><?php echo e($property['manager_name'] ?? 'Unassigned'); ?></span>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-muted d-block mb-1">Created</strong>
                        <span><?php echo formatDate($property['created_at'] ?? ''); ?></span>
                    </div>
                    <?php if (!empty($property['description'])): ?>
                        <div class="col-12">
                            <strong class="text-muted d-block mb-1">Description</strong>
                            <p class="mb-0"><?php echo nl2br(e($property['description'])); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Sidebar -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Occupancy Summary</h5>
            </div>
            <div class="card-body">
                <?php
                    $totalUnitsCount = count($units);
                    $occupiedCount = 0;
                    $vacantCount = 0;
                    $maintenanceCount = 0;
                    foreach ($units as $u) {
                        if ($u['status'] === 'occupied') $occupiedCount++;
                        elseif ($u['status'] === 'vacant') $vacantCount++;
                        elseif ($u['status'] === 'maintenance') $maintenanceCount++;
                    }
                    $occRate = $totalUnitsCount > 0 ? round(($occupiedCount / $totalUnitsCount) * 100, 1) : 0;
                ?>
                <div class="text-center mb-3">
                    <h2 class="mb-0"><?php echo $occRate; ?>%</h2>
                    <small class="text-muted">Occupancy Rate</small>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Total Units</span>
                    <strong><?php echo $totalUnitsCount; ?></strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Occupied</span>
                    <strong class="text-success"><?php echo $occupiedCount; ?></strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Vacant</span>
                    <strong class="text-info"><?php echo $vacantCount; ?></strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Maintenance</span>
                    <strong class="text-warning"><?php echo $maintenanceCount; ?></strong>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="?page=properties&action=delete&id=<?php echo $property['id']; ?>" onsubmit="return confirm('Are you sure you want to delete this property? This action cannot be undone.');">
                    <?php echo csrfField(); ?>
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="bi bi-trash me-1"></i> Delete Property
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Units Table -->
<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Units</h5>
        <a href="?page=units&action=create&property_id=<?php echo $property['id']; ?>" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Add Unit
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Unit Number</th>
                        <th>Floor</th>
                        <th>Bedrooms</th>
                        <th>Bathrooms</th>
                        <th>Area (sqft)</th>
                        <th>Rent</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($units)): ?>
                        <?php foreach ($units as $unit): ?>
                            <tr>
                                <td class="fw-semibold"><?php echo e($unit['unit_number']); ?></td>
                                <td><?php echo e($unit['floor_number'] ?? 'N/A'); ?></td>
                                <td><?php echo (int) ($unit['bedrooms'] ?? 0); ?></td>
                                <td><?php echo (int) ($unit['bathrooms'] ?? 0); ?></td>
                                <td><?php echo $unit['area_sqft'] ? number_format($unit['area_sqft']) : 'N/A'; ?></td>
                                <td><?php echo formatCurrency($unit['rent_amount'] ?? 0); ?></td>
                                <td><?php echo statusBadge($unit['status']); ?></td>
                                <td>
                                    <a href="?page=units&action=show&id=<?php echo $unit['id']; ?>" class="btn btn-sm btn-outline-info" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="?page=units&action=edit&id=<?php echo $unit['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No units found for this property.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
