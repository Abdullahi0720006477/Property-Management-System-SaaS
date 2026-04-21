<?php
$old = $_SESSION['old_input'] ?? $unit;
unset($_SESSION['old_input']);
require_once VIEWS_PATH . '/layouts/header.php';
?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Units', 'url' => '?page=units'], ['label' => 'Edit Unit', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Unit <?php echo e($unit['unit_number']); ?></h4>
    <a href="?page=units&action=show&id=<?php echo $unit['id']; ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Unit
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="?page=units&action=edit&id=<?php echo $unit['id']; ?>">
            <?php echo csrfField(); ?>

            <div class="row g-3">
                <!-- Property -->
                <div class="col-md-6">
                    <label for="property_id" class="form-label">Property <span class="text-danger">*</span></label>
                    <select class="form-select" id="property_id" name="property_id" required>
                        <option value="">Select Property</option>
                        <?php foreach ($properties as $property): ?>
                            <option value="<?php echo $property['id']; ?>" <?php echo ($old['property_id'] ?? '') == $property['id'] ? 'selected' : ''; ?>>
                                <?php echo e($property['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Unit Number -->
                <div class="col-md-6">
                    <label for="unit_number" class="form-label">Unit Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="unit_number" name="unit_number" value="<?php echo e($old['unit_number'] ?? ''); ?>" required placeholder="e.g. A101">
                </div>

                <!-- Floor Number -->
                <div class="col-md-4">
                    <label for="floor_number" class="form-label">Floor Number</label>
                    <input type="number" class="form-control" id="floor_number" name="floor_number" value="<?php echo e($old['floor_number'] ?? ''); ?>" min="0">
                </div>

                <!-- Bedrooms -->
                <div class="col-md-4">
                    <label for="bedrooms" class="form-label">Bedrooms</label>
                    <input type="number" class="form-control" id="bedrooms" name="bedrooms" value="<?php echo e($old['bedrooms'] ?? '1'); ?>" min="0">
                </div>

                <!-- Bathrooms -->
                <div class="col-md-4">
                    <label for="bathrooms" class="form-label">Bathrooms</label>
                    <input type="number" class="form-control" id="bathrooms" name="bathrooms" value="<?php echo e($old['bathrooms'] ?? '1'); ?>" min="0">
                </div>

                <!-- Area -->
                <div class="col-md-4">
                    <label for="area_sqft" class="form-label">Area (sq ft)</label>
                    <input type="number" class="form-control" id="area_sqft" name="area_sqft" value="<?php echo e($old['area_sqft'] ?? ''); ?>" min="0" step="0.01">
                </div>

                <!-- Rent Amount -->
                <div class="col-md-4">
                    <label for="rent_amount" class="form-label">Rent Amount (<?php echo CURRENCY_SYMBOL; ?>) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="rent_amount" name="rent_amount" value="<?php echo e($old['rent_amount'] ?? ''); ?>" min="0" step="0.01" required>
                </div>

                <!-- Status -->
                <div class="col-md-4">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="vacant" <?php echo ($old['status'] ?? '') === 'vacant' ? 'selected' : ''; ?>>Vacant</option>
                        <option value="occupied" <?php echo ($old['status'] ?? '') === 'occupied' ? 'selected' : ''; ?>>Occupied</option>
                        <option value="maintenance" <?php echo ($old['status'] ?? '') === 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                        <option value="reserved" <?php echo ($old['status'] ?? '') === 'reserved' ? 'selected' : ''; ?>>Reserved</option>
                    </select>
                </div>

                <!-- Description -->
                <div class="col-12">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Optional unit description..."><?php echo e($old['description'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Update Unit
                </button>
                <a href="?page=units&action=show&id=<?php echo $unit['id']; ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
