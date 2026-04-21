<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Staff', 'url' => '?page=staff'], ['label' => 'Add Staff', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Add Staff</h4>
    <a href="?page=staff" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Staff
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="?page=staff&action=create">
            <?php echo csrfField(); ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo e(postData('full_name')); ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo e(postData('email')); ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo e(postData('phone')); ?>">
                </div>

                <div class="col-md-6">
                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="">Select Role</option>
                        <option value="manager" <?php echo postData('role') === 'manager' ? 'selected' : ''; ?>>Manager</option>
                        <option value="staff" <?php echo postData('role') === 'staff' ? 'selected' : ''; ?>>Staff</option>
                        <option value="accountant" <?php echo postData('role') === 'accountant' ? 'selected' : ''; ?>>Accountant</option>
                        <option value="maintenance_tech" <?php echo postData('role') === 'maintenance_tech' ? 'selected' : ''; ?>>Maintenance Tech</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password" name="password" minlength="8" required>
                    <div class="form-text">Minimum 8 characters.</div>
                </div>

                <div class="col-md-6">
                    <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="8" required>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i> Create Staff Member</button>
                <a href="?page=staff" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
