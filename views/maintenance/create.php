<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Maintenance', 'url' => '?page=maintenance'], ['label' => 'New Request', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">New Maintenance Request</h4>
    <a href="?page=maintenance" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to List
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="?page=maintenance&action=create" enctype="multipart/form-data">
            <?php echo csrfField(); ?>

            <?php if (isTenant() && isset($lease)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-1"></i>
                    Submitting request for: <strong><?php echo e($lease['property_name']); ?> - Unit <?php echo e($lease['unit_number']); ?></strong>
                </div>
            <?php endif; ?>

            <?php if (!isTenant()): ?>
                <div class="mb-3">
                    <label for="unit_id" class="form-label">Unit <span class="text-danger">*</span></label>
                    <select name="unit_id" id="unit_id" class="form-select" required>
                        <option value="">Select Unit</option>
                        <?php foreach ($units as $unit): ?>
                            <option value="<?php echo $unit['id']; ?>">
                                <?php echo e($unit['property_name']); ?> - <?php echo e($unit['unit_number']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" id="title" class="form-control" maxlength="255" required placeholder="Brief description of the issue">
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                <textarea name="description" id="description" class="form-control" rows="5" required placeholder="Provide detailed information about the maintenance issue..."></textarea>
            </div>

            <div class="mb-3">
                <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                <select name="priority" id="priority" class="form-select" required>
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                    <option value="emergency">Emergency</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="images" class="form-label">Photo</label>
                <input type="file" name="images" id="images" class="form-control" accept="image/*">
                <div class="form-text">Upload a photo of the issue (JPEG, PNG, GIF, WebP).</div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send me-1"></i>Submit Request
                </button>
                <a href="?page=maintenance" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
