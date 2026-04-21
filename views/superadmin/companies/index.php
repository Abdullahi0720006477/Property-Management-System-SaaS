<?php require_once VIEWS_PATH . '/superadmin/layout.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Companies</h4>
    <span class="badge bg-secondary"><?= count($companies) ?> total</span>
</div>

<!-- Filters -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="page" value="companies">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by name or email..." value="<?= e($_GET['search'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <select name="plan" class="form-select form-select-sm">
                    <option value="">All Plans</option>
                    <?php foreach (SUBSCRIPTION_PLANS as $key => $plan): ?>
                        <option value="<?= $key ?>" <?= ($_GET['plan'] ?? '') === $key ? 'selected' : '' ?>><?= e($plan['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="trial" <?= ($_GET['status'] ?? '') === 'trial' ? 'selected' : '' ?>>Trial</option>
                    <option value="cancelled" <?= ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    <option value="expired" <?= ($_GET['status'] ?? '') === 'expired' ? 'selected' : '' ?>>Expired</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="superadmin.php?page=companies" class="btn btn-outline-secondary btn-sm w-100">Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- Companies Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="font-size:0.8rem;">Company</th>
                        <th style="font-size:0.8rem;">Plan</th>
                        <th style="font-size:0.8rem;">Status</th>
                        <th style="font-size:0.8rem;">Properties</th>
                        <th style="font-size:0.8rem;">Users</th>
                        <th style="font-size:0.8rem;">Signup Date</th>
                        <th style="font-size:0.8rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($companies as $c): ?>
                    <tr>
                        <td>
                            <a href="superadmin.php?page=companies&action=show&id=<?= $c['id'] ?>" class="fw-semibold text-decoration-none"><?= e($c['name']) ?></a>
                            <div style="font-size:0.75rem;color:var(--color-text-secondary);"><?= e($c['email']) ?></div>
                        </td>
                        <td>
                            <?php $p = SUBSCRIPTION_PLANS[$c['subscription_plan']] ?? null; ?>
                            <span class="badge" style="background:<?= $p['badge_color'] ?? '#94A3B8' ?>;"><?= e($p['name'] ?? $c['subscription_plan']) ?></span>
                        </td>
                        <td><?= statusBadge($c['subscription_status'] ?? 'active') ?></td>
                        <td><?= $c['property_count'] ?? 0 ?></td>
                        <td><?= $c['user_count'] ?? 0 ?></td>
                        <td style="font-size:0.8rem;"><?= formatDate($c['created_at']) ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="superadmin.php?page=companies&action=show&id=<?= $c['id'] ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye"></i></a>
                                <a href="superadmin.php?page=companies&action=edit&id=<?= $c['id'] ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil"></i></a>
                                <a href="superadmin.php?page=subscriptions&action=manage&id=<?= $c['id'] ?>" class="btn btn-outline-warning btn-sm"><i class="bi bi-credit-card"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($companies)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No companies found</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
