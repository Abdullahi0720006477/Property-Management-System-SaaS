<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Settings', 'url' => '?page=settings'], ['label' => 'Profile', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Settings</h4>
</div>

<!-- Tab Navigation -->
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link" href="?page=settings&action=company">
            <i class="bi bi-building me-1"></i> Company
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="?page=settings&action=preferences">
            <i class="bi bi-sliders me-1"></i> Preferences
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="?page=settings&action=profile">
            <i class="bi bi-person me-1"></i> Profile
        </a>
    </li>
</ul>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="bi bi-person-circle me-2"></i>My Profile</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="?page=settings&action=profile" enctype="multipart/form-data">
            <?php echo csrfField(); ?>

            <!-- Avatar Section -->
            <div class="text-center mb-4">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="<?php echo uploadUrl($user['avatar']); ?>" alt="Avatar" class="rounded-circle mb-2" width="100" height="100" style="object-fit: cover;">
                <?php else: ?>
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width:100px;height:100px;background:var(--color-primary);color:white;font-size:2rem;">
                        <?php echo strtoupper(substr($user['full_name'] ?? 'U', 0, 1)); ?>
                    </div>
                <?php endif; ?>
                <div>
                    <label for="avatar" class="form-label mb-1">Change Avatar</label>
                    <input type="file" class="form-control form-control-sm mx-auto" id="avatar" name="avatar" accept="image/jpeg,image/png,image/gif,image/webp" style="max-width: 300px;">
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo e($user['full_name'] ?? ''); ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo e($user['email'] ?? ''); ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo e($user['phone'] ?? ''); ?>">
                </div>

                <div class="col-12">
                    <hr>
                    <h6 class="text-muted mb-3"><i class="bi bi-shield-lock me-1"></i>Change Password</h6>
                    <p class="form-text mb-3">Leave blank to keep your current password.</p>
                </div>

                <div class="col-md-6">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" minlength="8" autocomplete="new-password">
                    <div class="form-text">Minimum 8 characters.</div>
                </div>

                <div class="col-md-6">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="8" autocomplete="new-password">
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i> Save Profile</button>
            </div>
        </form>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
