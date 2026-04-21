<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Tenants', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Tenants</h1>
    <a href="?page=tenants&action=create" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add Tenant
    </a>
</div>

<!-- Search & Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <input type="hidden" name="page" value="tenants">
            <input type="hidden" name="action" value="index">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search by name, email, or phone..." value="<?php echo e($search); ?>">
                </div>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="?page=tenants" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Tenants Table -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($tenants)): ?>
            <div class="text-center py-5">
                <i class="bi bi-people fs-1 text-muted"></i>
                <p class="text-muted mt-2">No tenants found.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 data-table">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Active Leases</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tenants as $t): ?>
                            <tr>
                                <td>
                                    <a href="?page=tenants&action=show&id=<?php echo $t['id']; ?>" class="text-decoration-none fw-semibold">
                                        <?= e($t['first_name'] . ' ' . $t['last_name']) ?>
                                    </a>
                                </td>
                                <td><?php echo e($t['phone']); ?></td>
                                <td><?php echo e($t['email']); ?></td>
                                <td>
                                    <span class="badge bg-info"><?php echo (int)($t['active_leases'] ?? 0); ?></span>
                                </td>
                                <td>
                                    <?php echo $t['is_active'] ? statusBadge('active') : statusBadge('expired'); ?>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="?page=tenants&action=show&id=<?php echo $t['id']; ?>" class="btn btn-outline-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="?page=tenants&action=edit&id=<?php echo $t['id']; ?>" class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if ($t['is_active']): ?>
                                            <form method="POST" action="?page=tenants&action=delete&id=<?php echo $t['id']; ?>" class="d-inline delete-form">
                                                <?php echo csrfField(); ?>
                                                <button type="button" class="btn btn-outline-danger" title="Deactivate"
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                        data-item-name="<?= e($t['first_name'] . ' ' . $t['last_name']) ?>">
                                                    <i class="bi bi-person-x"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($totalRecords > RECORDS_PER_PAGE): ?>
        <div class="card-footer">
            <?php echo paginate($totalRecords, $currentPage, RECORDS_PER_PAGE, '?page=tenants&action=index&search=' . urlencode($search)); ?>
        </div>
    <?php endif; ?>
</div>

<p class="text-muted mt-3 small">Showing <?php echo count($tenants); ?> of <?php echo $totalRecords; ?> tenants</p>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
