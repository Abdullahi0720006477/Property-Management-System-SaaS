<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Units', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-door-open me-2"></i>Units</h4>
    <a href="?page=units&action=create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add Unit
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="page" value="units">
            <input type="hidden" name="action" value="index">

            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" value="<?php echo e($search); ?>" placeholder="Unit number or property name...">
            </div>

            <div class="col-md-3">
                <label for="property_id" class="form-label">Property</label>
                <select class="form-select" id="property_id" name="property_id">
                    <option value="">All Properties</option>
                    <?php foreach ($properties as $property): ?>
                        <option value="<?php echo $property['id']; ?>" <?php echo $filterProperty == $property['id'] ? 'selected' : ''; ?>>
                            <?php echo e($property['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Statuses</option>
                    <option value="vacant" <?php echo $filterStatus === 'vacant' ? 'selected' : ''; ?>>Vacant</option>
                    <option value="occupied" <?php echo $filterStatus === 'occupied' ? 'selected' : ''; ?>>Occupied</option>
                    <option value="maintenance" <?php echo $filterStatus === 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                    <option value="reserved" <?php echo $filterStatus === 'reserved' ? 'selected' : ''; ?>>Reserved</option>
                </select>
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-search me-1"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Units Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($units)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-door-open fs-1 d-block mb-3"></i>
                <p>No units found.</p>
                <a href="?page=units&action=create" class="btn btn-primary btn-sm">Add Your First Unit</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover data-table">
                    <thead>
                        <tr>
                            <th>Unit #</th>
                            <th>Property</th>
                            <th>Floor</th>
                            <th>Beds/Baths</th>
                            <th>Rent</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($units as $unit): ?>
                            <tr>
                                <td>
                                    <a href="?page=units&action=show&id=<?php echo $unit['id']; ?>" class="fw-semibold text-decoration-none">
                                        <?php echo e($unit['unit_number']); ?>
                                    </a>
                                </td>
                                <td><?php echo e($unit['property_name']); ?></td>
                                <td><?php echo $unit['floor_number'] !== null ? e($unit['floor_number']) : '-'; ?></td>
                                <td><?php echo (int) $unit['bedrooms']; ?> / <?php echo (int) $unit['bathrooms']; ?></td>
                                <td><?php echo formatCurrency($unit['rent_amount']); ?></td>
                                <td><?php echo statusBadge($unit['status']); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="?page=units&action=show&id=<?php echo $unit['id']; ?>" class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="?page=units&action=edit&id=<?php echo $unit['id']; ?>" class="btn btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="?page=units&action=delete&id=<?php echo $unit['id']; ?>" class="d-inline delete-form">
                                            <?php echo csrfField(); ?>
                                            <button type="button" class="btn btn-outline-danger btn-sm" title="Delete"
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                    data-item-name="<?php echo e($unit['unit_number']); ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php echo paginate($totalRecords, $page, $perPage, '?page=units&action=index&search=' . urlencode($search) . '&property_id=' . ($filterProperty ?? '') . '&status=' . urlencode($filterStatus)); ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
