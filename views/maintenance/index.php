<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Maintenance', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Maintenance Requests</h4>
    <?php if (isTenant()): ?>
        <a href="?page=maintenance&action=create" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>New Request
        </a>
    <?php elseif (isAdmin() || isManager()): ?>
        <a href="?page=maintenance&action=create" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Add Request
        </a>
    <?php endif; ?>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <input type="hidden" name="page" value="maintenance">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by title, tenant, unit..." value="<?php echo e($search); ?>">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="open" <?php echo $status === 'open' ? 'selected' : ''; ?>>Open</option>
                    <option value="in_progress" <?php echo $status === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="resolved" <?php echo $status === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                    <option value="closed" <?php echo $status === 'closed' ? 'selected' : ''; ?>>Closed</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="priority" class="form-select">
                    <option value="">All Priorities</option>
                    <option value="low" <?php echo $priority === 'low' ? 'selected' : ''; ?>>Low</option>
                    <option value="medium" <?php echo $priority === 'medium' ? 'selected' : ''; ?>>Medium</option>
                    <option value="high" <?php echo $priority === 'high' ? 'selected' : ''; ?>>High</option>
                    <option value="emergency" <?php echo $priority === 'emergency' ? 'selected' : ''; ?>>Emergency</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-search me-1"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Requests Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Unit</th>
                        <?php if (!isTenant()): ?>
                            <th>Tenant</th>
                        <?php endif; ?>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($requests)): ?>
                        <?php foreach ($requests as $req): ?>
                            <tr>
                                <td>
                                    <a href="?page=maintenance&action=show&id=<?php echo $req['id']; ?>">
                                        <?php echo e($req['title']); ?>
                                    </a>
                                </td>
                                <td><?php echo e($req['property_name']); ?> - <?php echo e($req['unit_number']); ?></td>
                                <?php if (!isTenant()): ?>
                                    <td><?php echo e($req['tenant_name']); ?></td>
                                <?php endif; ?>
                                <td><?php echo priorityBadge($req['priority']); ?></td>
                                <td><?php echo statusBadge($req['status']); ?></td>
                                <td><?php echo formatDate($req['created_at']); ?></td>
                                <td>
                                    <a href="?page=maintenance&action=show&id=<?php echo $req['id']; ?>" class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if (isAdmin() || isManager()): ?>
                                        <a href="?page=maintenance&action=edit&id=<?php echo $req['id']; ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="?page=maintenance&action=delete&id=<?php echo $req['id']; ?>" class="d-inline delete-form">
                                            <?php echo csrfField(); ?>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                    data-item-name="<?php echo e($req['title']); ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?php echo isTenant() ? '6' : '7'; ?>" class="text-center text-muted py-4">
                                No maintenance requests found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<?php
$baseUrl = '?page=maintenance&search=' . urlencode($search) . '&status=' . urlencode($status) . '&priority=' . urlencode($priority);
echo paginate($totalRecords, $currentPage, RECORDS_PER_PAGE, $baseUrl);
?>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
