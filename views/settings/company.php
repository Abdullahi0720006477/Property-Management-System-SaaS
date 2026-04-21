<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Settings', 'url' => '?page=settings'], ['label' => 'Company', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Settings</h4>
</div>

<!-- Tab Navigation -->
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link active" href="?page=settings&action=company">
            <i class="bi bi-building me-1"></i> Company
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="?page=settings&action=preferences">
            <i class="bi bi-sliders me-1"></i> Preferences
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="?page=settings&action=profile">
            <i class="bi bi-person me-1"></i> Profile
        </a>
    </li>
</ul>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="bi bi-building me-2"></i>Company Profile</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="?page=settings&action=company" enctype="multipart/form-data">
            <?php echo csrfField(); ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Company Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo e($company['name'] ?? ''); ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo e($company['email'] ?? ''); ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo e($company['phone'] ?? ''); ?>">
                </div>

                <div class="col-md-6">
                    <label for="country" class="form-label">Country</label>
                    <input type="text" class="form-control" id="country" name="country" value="<?php echo e($company['country'] ?? 'Kenya'); ?>">
                </div>

                <div class="col-md-6">
                    <label for="city" class="form-label">City</label>
                    <input type="text" class="form-control" id="city" name="city" value="<?php echo e($company['city'] ?? ''); ?>">
                </div>

                <div class="col-md-6">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="address" name="address" value="<?php echo e($company['address'] ?? ''); ?>">
                </div>

                <div class="col-md-6">
                    <label for="logo" class="form-label">Company Logo</label>
                    <input type="file" class="form-control" id="logo" name="logo" accept="image/jpeg,image/png,image/gif,image/webp">
                    <div class="form-text">Accepted formats: JPEG, PNG, GIF, WebP. Leave empty to keep current logo.</div>
                    <?php if (!empty($company['logo'])): ?>
                        <div class="mt-2">
                            <img src="<?php echo uploadUrl($company['logo']); ?>" alt="Company Logo" class="img-thumbnail" style="max-height: 100px;">
                            <small class="text-muted d-block">Current logo</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
