<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Leases', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Leases</h4>
    <a href="?page=leases&action=create" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Create Lease
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="page" value="leases">
            <div class="col-md-5">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" value="<?php echo e($search); ?>" placeholder="Search by tenant, unit, or property...">
            </div>
            <div class="col-md-4">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Statuses</option>
                    <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="expired" <?php echo $status === 'expired' ? 'selected' : ''; ?>>Expired</option>
                    <option value="terminated" <?php echo $status === 'terminated' ? 'selected' : ''; ?>>Terminated</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-search me-1"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Leases Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Tenant</th>
                        <th>Property</th>
                        <th>Unit</th>
                        <th>Rent</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($leases)): ?>
                        <?php foreach ($leases as $lease): ?>
                            <tr>
                                <td><?php echo e($lease['tenant_name']); ?></td>
                                <td><?php echo e($lease['property_name']); ?></td>
                                <td><?php echo e($lease['unit_number']); ?></td>
                                <td><?php echo formatCurrency((float) $lease['monthly_rent']); ?></td>
                                <td><?php echo formatDate($lease['start_date']); ?></td>
                                <td><?php echo formatDate($lease['end_date']); ?></td>
                                <td><?php echo statusBadge($lease['lease_status']); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="?page=leases&action=show&id=<?php echo $lease['id']; ?>" class="btn btn-outline-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="?page=leases&action=edit&id=<?php echo $lease['id']; ?>" class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="?page=leases&action=delete&id=<?php echo $lease['id']; ?>" class="d-inline delete-form">
                                            <?php echo csrfField(); ?>
                                            <button type="button" class="btn btn-outline-danger btn-sm" title="Delete"
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                    data-item-name="<?php echo e($lease['tenant_name'] . ' - Unit ' . $lease['unit_number']); ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No leases found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<?php
    $baseUrl = '?page=leases&search=' . urlencode($search) . '&status=' . urlencode($status);
    echo paginate($totalRecords, $currentPage, RECORDS_PER_PAGE, $baseUrl);
?>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
