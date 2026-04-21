<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Staff', 'url' => '?page=staff'], ['label' => 'Edit Staff', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Edit Staff</h4>
    <a href="?page=staff" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Staff
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="?page=staff&action=edit&id=<?php echo $staff['id']; ?>">
            <?php echo csrfField(); ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo e($staff['full_name']); ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo e($staff['email']); ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo e($staff['phone'] ?? ''); ?>">
                </div>

                <div class="col-md-6">
                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="">Select Role</option>
                        <option value="manager" <?php echo $staff['role'] === 'manager' ? 'selected' : ''; ?>>Manager</option>
                        <option value="staff" <?php echo $staff['role'] === 'staff' ? 'selected' : ''; ?>>Staff</option>
                        <option value="accountant" <?php echo $staff['role'] === 'accountant' ? 'selected' : ''; ?>>Accountant</option>
                        <option value="maintenance_tech" <?php echo $staff['role'] === 'maintenance_tech' ? 'selected' : ''; ?>>Maintenance Tech</option>
                    </select>
                </div>

                <div class="col-12">
                    <hr>
                    <p class="text-muted mb-0">Leave password fields blank to keep the current password.</p>
                </div>

                <div class="col-md-6">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="password" name="password" minlength="8">
                    <div class="form-text">Minimum 8 characters.</div>
                </div>

                <div class="col-md-6">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="8">
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i> Update Staff Member</button>
                <a href="?page=staff" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>

<!-- Deactivate Section -->
<?php if (!empty($staff['is_active']) && $staff['id'] !== currentUserId()): ?>
<div class="card mt-4">
    <div class="card-body">
        <h6 class="text-danger mb-3">Danger Zone</h6>
        <p class="text-muted mb-3">Deactivating a staff member will revoke their access to the system. This action can be reversed by reactivating the user.</p>
        <form method="POST" action="?page=staff&action=deactivate&id=<?php echo $staff['id']; ?>" onsubmit="return confirm('Are you sure you want to deactivate this staff member?');">
            <?php echo csrfField(); ?>
            <button type="submit" class="btn btn-outline-danger">
                <i class="bi bi-person-x me-1"></i> Deactivate Staff Member
            </button>
        </form>
    </div>
</div>
<?php endif; ?>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
