<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Properties', 'url' => '?page=properties'], ['label' => 'Edit Property', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Edit Property</h4>
    <a href="?page=properties&action=show&id=<?php echo $property['id']; ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Property
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="?page=properties&action=edit&id=<?php echo $property['id']; ?>" enctype="multipart/form-data">
            <?php echo csrfField(); ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Property Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo e($property['name']); ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="property_type" class="form-label">Property Type <span class="text-danger">*</span></label>
                    <select class="form-select" id="property_type" name="property_type" required>
                        <option value="">Select Type</option>
                        <option value="apartment_building" <?php echo $property['property_type'] === 'apartment_building' ? 'selected' : ''; ?>>Apartment Building</option>
                        <option value="single_house" <?php echo $property['property_type'] === 'single_house' ? 'selected' : ''; ?>>Single House</option>
                        <option value="commercial" <?php echo $property['property_type'] === 'commercial' ? 'selected' : ''; ?>>Commercial</option>
                        <option value="mixed_use" <?php echo $property['property_type'] === 'mixed_use' ? 'selected' : ''; ?>>Mixed Use</option>
                    </select>
                </div>

                <div class="col-12">
                    <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="address" name="address" value="<?php echo e($property['address']); ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="city" class="form-label">City</label>
                    <input type="text" class="form-control" id="city" name="city" value="<?php echo e($property['city'] ?? ''); ?>">
                </div>

                <div class="col-md-6">
                    <label for="total_units" class="form-label">Total Units <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="total_units" name="total_units" value="<?php echo (int) $property['total_units']; ?>" min="1" required>
                </div>

                <div class="col-md-6">
                    <label for="manager_id" class="form-label">Manager</label>
                    <select class="form-select" id="manager_id" name="manager_id">
                        <option value="">No Manager Assigned</option>
                        <?php foreach ($managers as $manager): ?>
                            <option value="<?php echo $manager['id']; ?>" <?php echo $property['manager_id'] == $manager['id'] ? 'selected' : ''; ?>>
                                <?php echo e($manager['full_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="image" class="form-label">Property Image</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp">
                    <div class="form-text">Accepted formats: JPEG, PNG, GIF, WebP. Leave empty to keep current image.</div>
                    <?php if (!empty($property['image'])): ?>
                        <div class="mt-2">
                            <img src="<?php echo uploadUrl($property['image']); ?>" alt="Current Image" class="img-thumbnail" style="max-height: 100px;">
                            <small class="text-muted d-block">Current image</small>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-12">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4"><?php echo e($property['description'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i> Update Property</button>
                <a href="?page=properties&action=show&id=<?php echo $property['id']; ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
