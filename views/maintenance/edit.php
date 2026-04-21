<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Maintenance', 'url' => '?page=maintenance'], ['label' => 'Edit Request', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Edit Maintenance Request</h4>
    <a href="?page=maintenance&action=show&id=<?php echo $request['id']; ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Details
    </a>
</div>

<!-- Original Request Info -->
<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-info-circle me-1"></i>Original Request
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Title:</strong> <?php echo e($request['title']); ?></p>
                <p><strong>Unit:</strong> <?php echo e($request['property_name']); ?> - <?php echo e($request['unit_number']); ?></p>
                <p><strong>Tenant:</strong> <?php echo e($request['tenant_name']); ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Current Status:</strong> <?php echo statusBadge($request['status']); ?></p>
                <p><strong>Current Priority:</strong> <?php echo priorityBadge($request['priority']); ?></p>
                <p><strong>Submitted:</strong> <?php echo formatDateTime($request['created_at']); ?></p>
            </div>
        </div>
        <?php if (!empty($request['description'])): ?>
            <p><strong>Description:</strong></p>
            <p class="text-muted"><?php echo nl2br(e($request['description'])); ?></p>
        <?php endif; ?>
    </div>
</div>

<!-- Edit Form -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-pencil me-1"></i>Update Request
    </div>
    <div class="card-body">
        <form method="POST" action="?page=maintenance&action=edit&id=<?php echo $request['id']; ?>" enctype="multipart/form-data">
            <?php echo csrfField(); ?>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select" required>
                        <option value="open" <?php echo $request['status'] === 'open' ? 'selected' : ''; ?>>Open</option>
                        <option value="in_progress" <?php echo $request['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                        <option value="resolved" <?php echo $request['status'] === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                        <option value="closed" <?php echo $request['status'] === 'closed' ? 'selected' : ''; ?>>Closed</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                    <select name="priority" id="priority" class="form-select" required>
                        <option value="low" <?php echo $request['priority'] === 'low' ? 'selected' : ''; ?>>Low</option>
                        <option value="medium" <?php echo $request['priority'] === 'medium' ? 'selected' : ''; ?>>Medium</option>
                        <option value="high" <?php echo $request['priority'] === 'high' ? 'selected' : ''; ?>>High</option>
                        <option value="emergency" <?php echo $request['priority'] === 'emergency' ? 'selected' : ''; ?>>Emergency</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="assigned_to" class="form-label">Assign To</label>
                    <select name="assigned_to" id="assigned_to" class="form-select">
                        <option value="">Unassigned</option>
                        <?php foreach ($assignableUsers as $user): ?>
                            <option value="<?php echo $user['id']; ?>" <?php echo ($request['assigned_to'] ?? '') == $user['id'] ? 'selected' : ''; ?>>
                                <?php echo e($user['full_name']); ?> (<?php echo e($user['role']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="cost" class="form-label">Cost</label>
                    <div class="input-group">
                        <span class="input-group-text"><?php echo CURRENCY_SYMBOL; ?></span>
                        <input type="number" name="cost" id="cost" class="form-control" step="0.01" min="0" value="<?php echo e($request['cost'] ?? ''); ?>" placeholder="0.00">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="images" class="form-label">Update Photo</label>
                <input type="file" name="images" id="images" class="form-control" accept="image/*">
                <div class="form-text">Upload a new photo to replace the existing one (JPEG, PNG, GIF, WebP).</div>
            </div>

            <?php if (!empty($request['images'])): ?>
                <div class="mb-3">
                    <label class="form-label">Current Photo</label>
                    <div>
                        <img src="<?php echo uploadUrl($request['images']); ?>" alt="Maintenance photo" class="img-thumbnail" style="max-height: 200px;">
                    </div>
                </div>
            <?php endif; ?>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Update Request
                </button>
                <a href="?page=maintenance&action=show&id=<?php echo $request['id']; ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
