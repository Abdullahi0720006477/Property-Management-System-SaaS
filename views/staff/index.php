<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Staff', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Staff Members</h4>
    <a href="?page=staff&action=create" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Add Staff
    </a>
</div>

<!-- Usage Meter -->
<?php if (isset($usage)): ?>
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <span class="fw-semibold">User Usage:</span>
                <span class="ms-2">
                    <?php echo (int)$usage['users']['used']; ?> of <?php echo $usage['users']['max'] === -1 ? 'Unlimited' : (int)$usage['users']['max']; ?> users used
                </span>
                <small class="text-muted ms-2">(<?php echo e($usage['plan_name']); ?> Plan)</small>
            </div>
            <?php if ($usage['users']['max'] !== -1): ?>
            <div style="width: 200px;">
                <?php
                    $usagePercent = $usage['users']['max'] > 0 ? round(($usage['users']['used'] / $usage['users']['max']) * 100) : 0;
                    $barClass = $usagePercent >= 90 ? 'bg-danger' : ($usagePercent >= 70 ? 'bg-warning' : 'bg-success');
                ?>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar <?php echo $barClass; ?>" style="width: <?php echo $usagePercent; ?>%"></div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Staff Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Last Login</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($staffMembers)): ?>
                        <?php foreach ($staffMembers as $member): ?>
                            <tr>
                                <td>
                                    <a href="?page=staff&action=show&id=<?php echo $member['id']; ?>" class="text-decoration-none fw-semibold">
                                        <?php echo e($member['full_name']); ?>
                                    </a>
                                </td>
                                <td><?php echo e($member['email']); ?></td>
                                <td>
                                    <?php
                                        $roleBadges = [
                                            'company_admin' => 'bg-danger',
                                            'manager'       => 'bg-primary',
                                            'staff'         => 'bg-info',
                                            'accountant'    => 'bg-warning text-dark',
                                            'maintenance_tech' => 'bg-secondary',
                                        ];
                                        $badgeClass = $roleBadges[$member['role']] ?? 'bg-secondary';
                                        $roleLabel = ucwords(str_replace('_', ' ', $member['role']));
                                    ?>
                                    <span class="badge <?php echo $badgeClass; ?>"><?php echo e($roleLabel); ?></span>
                                </td>
                                <td><?php echo formatDateTime($member['last_login'] ?? null); ?></td>
                                <td>
                                    <?php if (!empty($member['is_active'])): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="?page=staff&action=show&id=<?php echo $member['id']; ?>" class="btn btn-outline-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="?page=staff&action=edit&id=<?php echo $member['id']; ?>" class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if (!empty($member['is_active']) && $member['id'] !== currentUserId()): ?>
                                            <form method="POST" action="?page=staff&action=deactivate&id=<?php echo $member['id']; ?>" class="d-inline" onsubmit="return confirm('Are you sure you want to deactivate this staff member?');">
                                                <?php echo csrfField(); ?>
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Deactivate">
                                                    <i class="bi bi-person-x"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No staff members found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
