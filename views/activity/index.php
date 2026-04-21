<?php require_once VIEWS_PATH . '/layouts/header.php'; ?>

<?= breadcrumbs([['label' => 'Dashboard', 'url' => '?page=dashboard'], ['label' => 'Activity Log', 'url' => '']]) ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-clock-history me-2"></i>Activity Log</h4>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="page" value="activity">
            <div class="col-md-3">
                <label class="form-label">Action Type</label>
                <select name="action_filter" class="form-select">
                    <option value="">All Actions</option>
                    <?php foreach ($actionTypes as $type): ?>
                        <option value="<?= e($type) ?>" <?= $actionFilter === $type ? 'selected' : '' ?>><?= e(ucfirst($type)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">User</label>
                <select name="user_filter" class="form-select">
                    <option value="">All Users</option>
                    <?php foreach ($allUsers as $user): ?>
                        <option value="<?= $user['id'] ?>" <?= $userFilter == $user['id'] ? 'selected' : '' ?>><?= e($user['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Date From</label>
                <input type="date" name="date_from" class="form-control" value="<?= e($dateFrom ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Date To</label>
                <input type="date" name="date_to" class="form-control" value="<?= e($dateTo ?? '') ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
            </div>
            <div class="col-12">
                <a href="?page=activity" class="text-decoration-none">
                    <i class="bi bi-x-circle me-1"></i> Reset Filters
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Activity Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($logs)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-clock-history" style="font-size: 3rem;"></i>
                <p class="mt-3 mb-0">No activity logs found.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Date/Time</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Entity Type</th>
                            <th>Description</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <?php
                            $badgeColors = [
                                'create' => 'bg-success',
                                'update' => 'bg-primary',
                                'delete' => 'bg-danger',
                                'login'  => 'bg-purple',
                                'logout' => 'bg-secondary',
                                'payment' => 'bg-warning text-dark',
                            ];
                            $badgeClass = $badgeColors[$log['action']] ?? 'bg-secondary';
                            ?>
                            <tr>
                                <td class="text-nowrap"><?= formatDateTime($log['created_at']) ?></td>
                                <td><?= e($log['user_name'] ?? 'System') ?></td>
                                <td><span class="badge <?= $badgeClass ?>"><?= e(ucfirst($log['action'])) ?></span></td>
                                <td><?= e(ucfirst(str_replace('_', ' ', $log['entity_type'] ?? ''))) ?></td>
                                <td><?= e($log['description']) ?></td>
                                <td><small class="text-muted"><?= e($log['ip_address'] ?? '') ?></small></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
