<?php require_once VIEWS_PATH . '/superadmin/layout.php'; ?>

<?= breadcrumbs([
    ['label' => 'Companies', 'url' => 'superadmin.php?page=companies'],
    ['label' => $company['name'], 'url' => 'superadmin.php?page=companies&action=show&id=' . $company['id']],
    ['label' => 'Edit', 'url' => '']
]) ?>

<h4 class="fw-bold mb-4">Edit <?= e($company['name']) ?></h4>

<?php if (!empty($success)): ?>
    <div class="alert alert-success py-2" style="font-size:0.85rem;"><?= e($success) ?></div>
<?php endif; ?>

<div class="card" style="max-width:700px;">
    <div class="card-body">
        <form method="POST" action="superadmin.php?page=companies&action=edit&id=<?= $company['id'] ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">Company Name</label>
                    <input type="text" name="name" class="form-control" value="<?= e($company['name']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= e($company['email']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">Subscription Plan</label>
                    <select name="subscription_plan" class="form-select">
                        <?php foreach (SUBSCRIPTION_PLANS as $key => $plan): ?>
                            <option value="<?= $key ?>" <?= $company['subscription_plan'] === $key ? 'selected' : '' ?>><?= e($plan['name']) ?> (<?= formatCurrency($plan['price_monthly']) ?>/mo)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">Status</label>
                    <select name="subscription_status" class="form-select">
                        <option value="active" <?= ($company['subscription_status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="trial" <?= ($company['subscription_status'] ?? '') === 'trial' ? 'selected' : '' ?>>Trial</option>
                        <option value="cancelled" <?= ($company['subscription_status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        <option value="expired" <?= ($company['subscription_status'] ?? '') === 'expired' ? 'selected' : '' ?>>Expired</option>
                        <option value="suspended" <?= ($company['subscription_status'] ?? '') === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">Max Properties</label>
                    <input type="number" name="max_properties" class="form-control" value="<?= (int)$company['max_properties'] ?>" min="-1">
                    <div class="form-text">-1 = unlimited</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">Max Users</label>
                    <input type="number" name="max_users" class="form-control" value="<?= (int)$company['max_users'] ?>" min="-1">
                    <div class="form-text">-1 = unlimited</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold" style="font-size:0.85rem;">Subscription End</label>
                    <input type="date" name="subscription_end" class="form-control" value="<?= e($company['subscription_end'] ?? '') ?>">
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="superadmin.php?page=companies&action=show&id=<?= $company['id'] ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
