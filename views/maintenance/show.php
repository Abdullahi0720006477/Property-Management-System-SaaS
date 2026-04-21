<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Maintenance', 'url' => '?page=maintenance'], ['label' => 'Request #' . $request['id'], 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Maintenance Request #<?php echo $request['id']; ?></h4>
    <div class="d-flex gap-2">
        <?php if (isAdmin() || isManager()): ?>
            <a href="?page=maintenance&action=edit&id=<?php echo $request['id']; ?>" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
        <?php endif; ?>
        <a href="?page=maintenance" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to List
        </a>
    </div>
</div>

<div class="row">
    <!-- Main Details -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-wrench me-1"></i>Request Details
            </div>
            <div class="card-body">
                <h5><?php echo e($request['title']); ?></h5>
                <hr>
                <div class="mb-3">
                    <strong>Description:</strong>
                    <p class="mt-1 text-muted"><?php echo nl2br(e($request['description'])); ?></p>
                </div>

                <?php if (!empty($request['images'])): ?>
                    <div class="mb-3">
                        <strong>Photo:</strong>
                        <div class="mt-2">
                            <a href="<?php echo uploadUrl($request['images']); ?>" target="_blank">
                                <img src="<?php echo uploadUrl($request['images']); ?>" alt="Maintenance photo" class="img-thumbnail" style="max-height: 300px;">
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-info-circle me-1"></i>Information
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Status</small>
                    <?php echo statusBadge($request['status']); ?>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">Priority</small>
                    <?php echo priorityBadge($request['priority']); ?>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">Unit</small>
                    <strong><?php echo e($request['property_name']); ?> - <?php echo e($request['unit_number']); ?></strong>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">Tenant</small>
                    <strong><?php echo e($request['tenant_name']); ?></strong>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">Assigned To</small>
                    <strong><?php echo $request['assigned_name'] ? e($request['assigned_name']) : '<span class="text-muted">Unassigned</span>'; ?></strong>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">Cost</small>
                    <strong><?php echo $request['cost'] ? formatCurrency((float) $request['cost']) : '<span class="text-muted">N/A</span>'; ?></strong>
                </div>

                <hr>

                <div class="mb-3">
                    <small class="text-muted d-block">Submitted</small>
                    <strong><?php echo formatDateTime($request['created_at']); ?></strong>
                </div>

                <?php if (!empty($request['resolved_at'])): ?>
                    <div class="mb-3">
                        <small class="text-muted d-block">Resolved</small>
                        <strong><?php echo formatDateTime($request['resolved_at']); ?></strong>
                    </div>
                <?php endif; ?>

                <div class="mb-0">
                    <small class="text-muted d-block">Last Updated</small>
                    <strong><?php echo formatDateTime($request['updated_at'] ?? $request['created_at']); ?></strong>
                </div>
            </div>
        </div>

        <?php if (isAdmin() || isManager()): ?>
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="?page=maintenance&action=delete&id=<?php echo $request['id']; ?>" onsubmit="return confirm('Are you sure you want to delete this maintenance request? This action cannot be undone.');">
                        <?php echo csrfField(); ?>
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-trash me-1"></i>Delete Request
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
