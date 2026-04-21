<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Staff', 'url' => '?page=staff'], ['label' => $staff['full_name'], 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><?php echo e($staff['full_name']); ?></h4>
    <div>
        <a href="?page=staff" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Staff
        </a>
        <a href="?page=staff&action=edit&id=<?php echo $staff['id']; ?>" class="btn btn-primary ms-1">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Profile Card -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                <?php if (!empty($staff['avatar'])): ?>
                    <img src="<?php echo uploadUrl($staff['avatar']); ?>" alt="<?php echo e($staff['full_name']); ?>" class="rounded-circle mb-3" width="96" height="96" style="object-fit: cover;">
                <?php else: ?>
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width:96px;height:96px;background:var(--color-primary);color:white;font-size:2rem;">
                        <?php echo strtoupper(substr($staff['full_name'], 0, 1)); ?>
                    </div>
                <?php endif; ?>

                <h5 class="mb-1"><?php echo e($staff['full_name']); ?></h5>
                <?php
                    $roleBadges = [
                        'company_admin' => 'bg-danger',
                        'manager'       => 'bg-primary',
                        'staff'         => 'bg-info',
                        'accountant'    => 'bg-warning text-dark',
                        'maintenance_tech' => 'bg-secondary',
                    ];
                    $badgeClass = $roleBadges[$staff['role']] ?? 'bg-secondary';
                    $roleLabel = ucwords(str_replace('_', ' ', $staff['role']));
                ?>
                <span class="badge <?php echo $badgeClass; ?> mb-3"><?php echo e($roleLabel); ?></span>

                <div class="mb-2">
                    <?php if (!empty($staff['is_active'])): ?>
                        <span class="badge bg-success">Active</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Inactive</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Details -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Staff Details</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <strong class="text-muted d-block mb-1">Full Name</strong>
                        <span><?php echo e($staff['full_name']); ?></span>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-muted d-block mb-1">Email</strong>
                        <span><?php echo e($staff['email']); ?></span>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-muted d-block mb-1">Phone</strong>
                        <span><?php echo e($staff['phone'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-muted d-block mb-1">Role</strong>
                        <span><?php echo e($roleLabel); ?></span>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-muted d-block mb-1">Last Login</strong>
                        <span><?php echo formatDateTime($staff['last_login'] ?? null); ?></span>
                    </div>
                    <div class="col-md-6">
                        <strong class="text-muted d-block mb-1">Member Since</strong>
                        <span><?php echo formatDate($staff['created_at'] ?? null); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Log -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Recent Activity</h5>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($activityLogs)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($activityLogs as $log): ?>
                                    <tr>
                                        <td><span class="badge bg-secondary"><?php echo e(ucwords($log['action'])); ?></span></td>
                                        <td><?php echo e($log['description']); ?></td>
                                        <td><?php echo formatDateTime($log['created_at']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted py-4">No activity recorded for this user.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
